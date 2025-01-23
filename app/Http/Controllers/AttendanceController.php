<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Student;
use App\Models\Centre;
use App\Models\ClassVenue;
use App\Models\ClassRoom;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class AttendanceController extends Controller
{
    public function retrieveAttendance(Request $request)
    {
        // Get the selected date and centre from the request
        $date = $request->input('filter.date');
        $centreId = $request->input('filter.centre_id');

        // Validate the date input
        if (!$date) {
            return redirect()->back()->with('error', 'Attendance date is required.');
        }

        // Convert the date to a day format (e.g., Monday, Tuesday)
        $selectedDay = Carbon::parse($date)->format('l');

        // Fetch all centres (for filtering)
        $centres = Centre::all();

        // Query to get students associated with the selected centre
        $studentsQuery = Student::with('classes');

        if ($centreId) {
            $studentsQuery->where('centre_id', $centreId);
        }

        $students = $studentsQuery->get();

        // Retrieve attendance records for the selected date
        $attendanceRecords = Attendance::where('attendance_date', $date)->get()->keyBy('student_id');

        // Pass the data to the view
        return view('students.attendance', compact('students', 'attendanceRecords', 'date', 'selectedDay', 'centres'));
    }

    // Show attendance view
    public function showAttendance(Request $request)
    {
        // Fetch centres from the database
        $classVenue = ClassVenue::all();

        // Get the selected date from the request or set a default
        $date = $request->input('filter.date', now()->format('Y-m-d')); // Default to today's date

        // Calculate the selected day based on the date
        $selectedDay = Carbon::parse($date)->format('l'); // Get the full name of the day

        // Fetch students and attendance records as needed
        $students = Student::all(); // Adjust this to your actual query
        $attendanceRecords = Attendance::where('attendance_date', $date)->get(); // Example query
        $venues = Student::with('classes.venue')->get();

        // Return the view with the necessary data
        return view('students.attendance', compact('classVenue', 'students', 'attendanceRecords', 'selectedDay', 'date', 'venues'));
    }

    // Handle the filtering of attendance
    public function filterAttendance(Request $request)
    {
        $date = $request->input('filter.date');
        $cvId = $request->input('filter.cv_id'); // Get Centre Venue ID

        // Determine the day of the week from the selected date
        $selectedDay = Carbon::parse($date)->format('l');

        // Fetch all class venues for the dropdown
        $classVenue = ClassVenue::all();

        // Query students with their classes and venues
        $studentsQuery = Student::with(['classes.venue']);

        // Apply Centre Venue filtering if a venue is selected
        if ($cvId) {
            $studentsQuery->whereHas('classes.venue', function ($query) use ($cvId) {
                $query->where('cv_id', $cvId);
            });
        }

        // Retrieve filtered students
        $students = $studentsQuery->get();

        // Fetch attendance records for the selected date
        $attendanceRecords = Attendance::whereDate('attendance_date', $date)
                                        ->get()
                                        ->keyBy('student_id');

        if ($classVenue->isEmpty()) {
            dd('No venues found in ClassVenue table');
        }

        // Pass variables to the view
        return view('students.attendance', compact('classVenue', 'students', 'attendanceRecords', 'selectedDay', 'date'));
    }

    // Handle attendance update
    public function updateAttendance(Request $request)
    {
        // Validate attendance data
        $request->validate([
            'date' => 'required|date',
            'attendance' => 'required|array',
            'attendance.*.present' => 'boolean',
            'attendance.*.reason' => 'nullable|string',
        ]);

        // Process the attendance data
        foreach ($request->attendance as $studentId => $attendanceData) {
            // Save the attendance record for each student
            $attendanceRecord = Attendance::updateOrCreate(
                ['student_id' => $studentId, 'attendance_date' => $request->date],
                [
                    'status' => isset($attendanceData['present']) ? 'Present' : 'Absent',
                    'reason' => $attendanceData['reason'] ?? null,
                ]
            );
        }

        // Redirect with a success message
        return redirect()->route('students.attendance')->with('success', 'Attendance updated successfully');
    }
}