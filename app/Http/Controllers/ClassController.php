<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ClassRoom;
use App\Models\ClassVenue;
use Carbon\Carbon;
use App\Models\User;
use App\Models\ClassUser;
use App\Models\ClassStudent;

class ClassController extends Controller
{
    public function index(Request $request)
    {
        $class = ClassRoom::all();

        return view('students.class.class_index', compact('class'));
    }

    public function create(Request $request){
        $instructors = User::whereIn('role', ['instructor', 'admin'])->get();
        $class = classRoom::all();
        $venues = ClassVenue::all();

        return view('students.class.class_create', compact('class', 'venues', 'instructors'));
    }

    public function store(Request $request)
    {
        // Validate the incoming request
        $validated = $request->validate([
            'class_day' => 'required|string|max:255',
            'class_start_time' => 'required|date_format:H:i',
            'class_end_time' => 'required|date_format:H:i|after:class_start_time',
            'class_price' => 'required|numeric|min:0',
            'cv_id' => 'required|exists:class_venue,cv_id',
            'instructor_ids' => 'required|array|min:1',
            'instructor_ids.*' => 'exists:users,id'
        ], [
            'class_end_time.after' => 'The class end time must be after the start time.',
            'class_price.min' => 'The class price must be at least 0.',
            'cv_id.exists' => 'The selected venue is invalid.',
            'instructor_ids.required' => 'At least one instructor must be selected.',
            'instructor_ids.*.exists' => 'One or more selected instructors are invalid.'
        ]);

        // Create a new class record
        $class = ClassRoom::create([
            'class_day' => $validated['class_day'],
            'class_start_time' => $validated['class_start_time'],
            'class_end_time' => $validated['class_end_time'],
            'class_price' => $validated['class_price'],
            'cv_id' => $validated['cv_id'],
        ]);

        // Insert instructors into class_user table
        foreach ($validated['instructor_ids'] as $instructor_id) {
            ClassUser::create([
                'class_id' => $class->class_id,
                'user_id' => $instructor_id
            ]);
        }
        
        // Redirect to the class index with a success message
        return redirect()->route('students.class.class_index')->with('success', 'Class created successfully!');
    }

    public function destroy($id)
    {
        try {
            $class = ClassRoom::findOrFail($id);

            // Detach related records using Eloquent relationships
            $class->instructors()->detach();
            $class->students()->detach();

            // Delete the class
            $class->delete();

            return redirect()->route('students.class.class_index')
                ->with('success', 'Class deleted successfully!');
        } catch (\Exception $e) {
            return redirect()->route('students.class.class_index')
                ->with('error', 'Error: ' . $e->getMessage());
        }
    }

    public function edit($id)
    {
        // Find the class by its ID
        $class = ClassRoom::findOrFail($id);
        $instructors = User::whereIn('role', ['instructor', 'admin'])->get();

        $assignedInstructors = $class->instructors()->pluck('users.id')->toArray();

         // Format the start and end times to match the input[type="time"] format (HH:mm)
        $class->class_start_time = Carbon::parse($class->class_start_time)->format('H:i');
        $class->class_end_time = Carbon::parse($class->class_end_time)->format('H:i');
        $venues = ClassVenue::all();

        // Return the edit view with the class data
        return view('students.class.class_edit', compact('class', 'instructors', 'venues', 'assignedInstructors'));
    }

    public function update(Request $request, $id)
    {
        // Validate the incoming request
        $validated = $request->validate([
            'class_day' => 'required|string|max:255',
            'class_start_time' => 'required|date_format:H:i',
            'class_end_time' => 'required|date_format:H:i|after:class_start_time',
            'class_price' => 'required|numeric|min:0',
            'cv_id' => 'required|exists:class_venue,cv_id', // Add venue validation
            'instructor_ids' => 'required|array|min:1',
            'instructor_ids.*' => 'exists:users,id'
        ], [
            'class_end_time.after' => 'The class end time must be after the start time.',
            'class_price.min' => 'The class price must be at least 0.',
            'cv_id.exists' => 'The selected venue is invalid.',
            'instructor_ids.required' => 'At least one instructor must be selected.',
            'instructor_ids.*.exists' => 'One or more selected instructors are invalid.'
        ]);

        // Find the class by its ID
        $class = ClassRoom::findOrFail($id);

        // Update the class record, including the venue (cv_id)
        $class->update([
            'class_day' => $validated['class_day'],
            'class_start_time' => $validated['class_start_time'],
            'class_end_time' => $validated['class_end_time'],
            'class_price' => $validated['class_price'],
            'cv_id' => $validated['cv_id'], // Update venue
        ]);

        if ($request->has('instructor_ids')) {
            $class->instructors()->sync($request->input('instructor_ids')); // Sync instructors
        } else {
            $class->instructors()->detach(); // Detach if no instructors are selected
        }

        // Redirect to the class index with a success message
        return redirect()->route('students.class.class_index')->with('success', 'Class updated successfully!');
    }
}
