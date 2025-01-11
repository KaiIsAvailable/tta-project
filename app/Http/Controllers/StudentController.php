<?php

namespace App\Http\Controllers;

use App\Models\Student; // Assuming you have a Student model
use App\Models\Centre; // Import the Centre model
use App\Models\CurrentBelt;
use App\Models\ClassRoom;
use App\Models\ClassVenue;
use App\Models\Phone;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;


class StudentController extends Controller
{
    public function index(Request $request)
    {
        // Get filter inputs from the request
        $cvId = $request->get('cv_id');
        $name = $request->get('name');
        $beltId = $request->get('belt_id');

        // Retrieve students and apply filters
        $students = Student::when($cvId, function ($query) use ($cvId) {
                return $query->whereHas('classes', function ($query) use ($cvId) {
                    $query->where('cv_id', $cvId); // Filter by cv_id in the ClassRoom model
                });
            })
            ->when($name, function ($query) use ($name) {
                return $query->where('name', 'like', '%' . $name . '%');
            })
            ->when($beltId, function ($query) use ($beltId) {
                return $query->where('belt_id', $beltId);
            })
            ->with(['classes.venue'])
            ->paginate(10);

        // Get all centres and belts for dropdown filters
        $classVenue = ClassVenue::all();
        $belts = CurrentBelt::all();
        $class = ClassRoom::all();

        // Return the view with filtered students, centres, and belts
        return view('students.index', compact('students', 'classVenue', 'belts', 'class'));
    }


    public function create()
    {
        $belts = CurrentBelt::all();
        $centres = Centre::all();
        $classes = ClassRoom::all(); 
        $today = Carbon::today()->format('Y-m-d'); // Get today's date
        return view('students.create', compact('belts', 'centres', 'classes', 'today'));
    }

    public function store(Request $request)
    {
        // Validate the incoming request
        $validated = $this->validateStudent($request);

        // Create a new student instance with validated data
        $student = new Student($validated);
        
        $student->student_startDate = $request->startDate;

        $student->fee = $request->total_fee ?? 0;

        // Handle profile picture upload
        $this->handleProfilePictureUpload($request, $student);

        // Save the student record first to generate student_id
        if ($student->save()) {
            // Attach the classes to the student if class_id is provided
            if ($request->filled('class_id')) { // Use filled to check if the input exists and is not empty
                // Ensure the input is an array
                $classIds = is_array($request->class_id) ? $request->class_id : [$request->class_id];

                // Attach classes to the student
                $student->classes()->attach($classIds);
            }

            // Handle multiple phone numbers if they are provided
            if ($request->filled('hp_numbers')) {
                foreach ($request->hp_numbers as $index => $phoneNumber) {
                    $student->phone()->create([
                        'phone_number' => $phoneNumber,
                        'phone_person' => $request->phone_persons[$index] ?? null,
                        'country_code' => $request->country_code[$index] ?? null,
                    ]);
                }
            }

            return redirect()->route('students.index')->with('success', $student->name.' added successfully.');
        }

        return redirect()->route('students.index')->with('error', 'Failed to add student.');
    }

    public function edit($student_id)
    {
        $student = Student::with('belt', 'centre', 'phone')->findOrFail($student_id);
        $belts = CurrentBelt::all();
        $centres = Centre::all();
        $classes = ClassRoom::all();

        return view('students.edit', compact('student', 'belts', 'centres', 'classes'));
    }

    public function update(Request $request, $id)
    {
        // Find the student by ID or fail if not found
        $student = Student::findOrFail($id);

        $this->handleProfilePictureUpload($request, $student);
        
        // Validate incoming request data
        $request->validate([
            'name' => 'required|string|max:255',
            'ic_number' => 'nullable|regex:/^\d{6}-\d{2}-\d{4}$/',
            'country_codes' => 'required',
            'hp_numbers' => 'array',  // Allow null values here; validate later if necessary
            'hp_numbers.*' => 'nullable|regex:/^\d{10,11}$/',
            'phone_persons' => 'array',
            'phone_persons.*' => 'nullable|string|max:255',
            'profile_picture' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'belt_id' => 'required|exists:current_belts,BeltID',
            'centre_id' => 'required|exists:student_centre,centre_id',
            'fee' => 'nullable|numeric|min:0',
            'startDate' => 'required|date',
        ]);

        // Update basic student information
        $student->update([
            'name' => $request->input('name'),
            'ic_number' => $request->input('ic_number'),
            'belt_id' => $request->input('belt_id'),
            'centre_id' => $request->input('centre_id'),
            'fee' => $request->input('payment_amount') ?? 0,
            'student_startDate' => $request->input('startDate'), 
        ]);

        // Handle phone numbers and associated persons
        $phoneNumbers = $request->input('hp_numbers', []);
        $phonePersons = $request->input('phone_persons', []);
        $countryCodes = $request->input('country_codes', []);
        $phoneIds = $request->input('phone_ids', []);  // Getting phone_ids from hidden field

        // Ensure that the number of phone numbers, persons, country codes, and phone_ids are the same
        if (count($phoneNumbers) === count($phonePersons) && count($phoneNumbers) === count($countryCodes) && count($phoneIds) === count($phoneNumbers)) {
            foreach ($phoneNumbers as $index => $phone) {
                // Check if phone number, country code, phone person, and phone_id are provided and not empty
                if (!empty($phone) && !empty($phonePersons[$index]) && !empty($countryCodes[$index]) && !empty($phoneIds[$index])) {
                    // Find the phone record by phone_id
                    $phoneRecord = Phone::where('phone_id', $phoneIds[$index])->first();

                    if ($phoneRecord) {
                        // If the phone record exists, update it
                        // Only update if the values are different
                        if ($phoneRecord->phone_number !== $phone || 
                            $phoneRecord->phone_person !== $phonePersons[$index] ||
                            $phoneRecord->country_code !== $countryCodes[$index]) {
                            
                            $phoneRecord->update([
                                'phone_number' => $phone,
                                'phone_person' => $phonePersons[$index],
                                'country_code' => $countryCodes[$index],
                            ]);
                        }
                    } else {
                        // If no matching phone record is found, return an error
                        return redirect()->back()->withErrors('Invalid phone record ID.');
                    }
                }
            }
        } else {
            return redirect()->back()->withErrors('Phone numbers, country codes, phone persons, and phone ids must match.');
        }

        $student->save();

        // If you are using classes, sync them with the request input
        if ($request->has('class_id')) {
            $student->classes()->sync($request->input('class_id'));
        }

        // Redirect back to the student index with a success message
        return redirect()->route('students.index')->with('success', $student->name.' updated successfully.');
    }


    public function destroy(Student $student)
    {
        // Delete related records in the pivot table (class_student)
        $student->classes()->detach();

        // Delete related attendance records
        $student->attendance()->delete();

        // Now delete the student
        $student->delete();

        return redirect()->route('students.index')->with('success', $student->name.' records deleted successfully.');
    }

    private function validateStudent(Request $request)
    {
        return $request->validate([
            'name' => 'required|string|max:255',
            'ic_number' => 'nullable|regex:/^\d{6}-\d{2}-\d{4}$/',
            'hp_numbers' => 'required|array',
            'hp_numbers.*' => 'required|regex:/^\d{10,11}$/',
            'phone_persons' => 'required|array', 
            'phone_persons.*' => 'required|string|max:255',
            'profile_picture' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'belt_id' => 'required|exists:current_belts,BeltID',
            'centre_id' => 'required|exists:student_centre,centre_id',
            'class_id' => 'required|array', 
            'class_id.*' => 'exists:classes,class_id', 
            'startDate' => 'required|date',
        ], [
            'name.required' => 'The name field is required.',
            'ic_number.required' => 'The IC number field is required.',
            'ic_number.regex' => 'The IC number must follow the format: 010912-11-1234.',
            'hp_numbers.required' => 'The HP number field is required.',
            'hp_number.*.regex' => 'The HP number must be 10 or 11 digits long without any dashes.',
            'phone_persons.required' => 'At least one contact name is required.',
            'phone_persons.*.required' => 'Each contact name is required.',
            'belt_id.required' => 'Please select a belt.',
            'centre_id.required' => 'Please select a center.',
            'class_id.required' => 'Please select at least one class.',
            'class_id.*.exists' => 'One or more of the selected classes is invalid.',
            'startDate.required' => 'Please select a date',
        ]);
    }

    public function handleProfilePictureUpload(Request $request, Student $student)
    {
        if ($request->hasFile('profile_picture')) {
            // Get the uploaded file
            $image = $request->file('profile_picture');

            // Read the contents of the file as binary data
            $imageData = file_get_contents($image->getRealPath());

            // Save the binary data to the database
            $student->profile_picture = $imageData;
            $student->save();
        }
    }

    public function showProfile($student_id)
    {
        // Retrieve all students and filter by ID
        $students = Student::with(['phone', 'belt', 'centre', 'classes'])->findOrFail($student_id);

        // Pass the students data to the view
        return view('students.student_profile', compact('students'));
    }

    public function stillInProgress()
    {
        return view('students.stillInProgress');
    }

}