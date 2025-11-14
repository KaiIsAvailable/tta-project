<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Student;
use App\Models\ClassVenue;
use App\Models\CurrentBelt;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class AttendanceController extends Controller
{ 
    // Show attendance view
    public function showAttendance(Request $request)
    {
        // Fetch centres from the database
        $classVenue = ClassVenue::all();

        // Get the selected date from the request or set a default
        $date = $request->input('filter.date', now()->format('Y-m-d')); // Default to today's date

        // Calculate the selected day based on the date
        $selectedDay = Carbon::parse($date)->format('l'); // Get the full name of the day

        $user = Auth::User();

        // Fetch students and attendance records as needed
        // Query students based on whether the user is an instructor
        if ($user && $user->role === 'instructor') {
            // Assuming an instructor has classes, and students are related via classes
            $students = Student::whereHas('classes', function ($query) use ($user) {
                $query->whereHas('instructors', function ($q) use ($user) {
                    $q->where('user_id', $user->id); // Filter students based on instructor ID
                });
            })->get();
        } else {
            // If not an instructor, fetch all students
            $students = Student::all();
        }
        $attendanceRecords = Attendance::where('attendance_date', $date)->get(); // Example query
        $venues = Student::with('classes.venue')->get();

        $beltId = $request->get('belt_id');
        $belts = CurrentBelt::all();

        // Return the view with the necessary data
        return view('students.attendance', compact('classVenue', 'students', 'attendanceRecords', 'selectedDay', 'date', 'venues', 'belts'));
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

        // Get authenticated user
        $user = Auth::user();

        // Start query for students with their filtered classes
        $studentsQuery = Student::whereHas('classes', function ($query) use ($selectedDay, $cvId, $user) {
            // Filter classes by day
            $query->whereRaw("LOWER(TRIM(class_day)) = ?", [strtolower($selectedDay)]);

            // ✅ Filter by Centre Venue if selected
            if ($cvId) {
                $query->where('cv_id', $cvId);
            }

            // ✅ Filter by Instructor if the user is an instructor
            if ($user && $user->role === 'instructor') {
                $query->whereHas('instructors', function ($subQuery) use ($user) {
                    $subQuery->where('user_id', $user->id);
                });
            }
        })->with([
            'classes' => function ($query) use ($selectedDay, $cvId, $user) {
                // Only load classes that match the criteria
                $query->whereRaw("LOWER(TRIM(class_day)) = ?", [strtolower($selectedDay)]);

                // ✅ Filter by Centre Venue if selected
                if ($cvId) {
                    $query->where('cv_id', $cvId);
                }

                // ✅ Filter classes by Instructor
                if ($user && $user->role === 'instructor') {
                    $query->whereHas('instructors', function ($subQuery) use ($user) {
                        $subQuery->where('user_id', $user->id);
                    });
                }

                // Load the venue relationship for the view
                $query->with('venue');
            }
        ]);

        // Retrieve filtered students
        $students = $studentsQuery->get();

        // Fetch attendance records for the selected date
        $attendanceRecords = Attendance::whereDate('attendance_date', $date)
                                        ->get()
                                        ->keyBy('student_id');

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