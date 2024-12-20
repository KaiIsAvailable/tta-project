<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\classRoom;
use App\Models\ClassVenue;
use Carbon\Carbon;

class ClassController extends Controller
{
    public function index(Request $request)
    {
        $class = ClassRoom::all();

        return view('students.class.class_index', compact('class'));
    }

    public function create(Request $request){
        $class = classRoom::all();
        $venues = ClassVenue::all();

        return view('students.class.class_create', compact('class', 'venues'));
    }

    public function store(Request $request)
    {
        // Validate the incoming request
        $validated = $request->validate([
            'class_day' => 'required|string|max:255',
            'class_start_time' => 'required|date_format:H:i',
            'class_end_time' => 'required|date_format:H:i|after:class_start_time',
            'class_price' => 'required|numeric|min:0',
            'cv_id' => 'required|exists:class_venue,cv_id'
        ], [
            'class_end_time.after' => 'The class end time must be after the start time.',
            'class_price.min' => 'The class price must be at least 0.',
            'cv_id.exists' => 'The selected venue is invalid.',
        ]);

        // Create a new class record
        classRoom::create([
            'class_day' => $validated['class_day'],
            'class_start_time' => $validated['class_start_time'],
            'class_end_time' => $validated['class_end_time'],
            'class_price' => $validated['class_price'],
            'cv_id' => $validated['cv_id'],
        ]);

        // Redirect to the class index with a success message
        return redirect()->route('students.class.class_index')->with('success', 'Class created successfully!');
    }

    public function destroy($id)
    {
        // Find the class by ID
        $class = classRoom::findOrFail($id); // This will throw a 404 if the class is not found

        // Delete the class
        $class->delete();

        // Redirect back to the class index with a success message
        return redirect()->route('students.class.class_index')->with('success', 'Class deleted successfully!');
    }

    public function edit($id)
    {
        // Find the class by its ID
        $class = ClassRoom::findOrFail($id);

         // Format the start and end times to match the input[type="time"] format (HH:mm)
        $class->class_start_time = Carbon::parse($class->class_start_time)->format('H:i');
        $class->class_end_time = Carbon::parse($class->class_end_time)->format('H:i');

        // Return the edit view with the class data
        return view('students.class.class_edit', compact('class'));
    }

    public function update(Request $request, $id)
    {
        // Validate the incoming request
        $validated = $request->validate([
            'class_day' => 'required|string|max:255',
            'class_start_time' => 'required|date_format:H:i',
            'class_end_time' => 'required|date_format:H:i|after:class_start_time',
            'class_price' => 'required|numeric|min:0',
        ], [
            'class_end_time.after' => 'The class end time must be after the start time.',
            'class_price.min' => 'The class price must be at least 0.',
        ]);

        // Find the class by its ID
        $class = ClassRoom::findOrFail($id);

        // Update the class record
        $class->update([
            'class_day' => $validated['class_day'],
            'class_start_time' => $validated['class_start_time'],
            'class_end_time' => $validated['class_end_time'],
            'class_price' => $validated['class_price'],
        ]);

        // Redirect to the class index with a success message
        return redirect()->route('students.class.class_index')->with('success', 'Class updated successfully!');
    }

}
