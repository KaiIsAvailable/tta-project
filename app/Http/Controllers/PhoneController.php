<?php

namespace App\Http\Controllers;

use App\Models\Phone;
use Illuminate\Http\Request;

class PhoneController extends Controller
{
    /**
     * Store a new phone record.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        // Debug to check the submitted data
        if (!$request->has('students_id')) {
            return redirect()->back()->withErrors('Student ID is required.');
        }

        // Validation, note `students_id` instead of `student_id`
        $validated = $request->validate([
            'phone_number' => 'required|regex:/^\d{10,11}$/',
            'phone_person' => 'required|string|max:255',
            'country_codes' => 'required',
            'students_id' => 'required|exists:students,student_id',
        ]);

        // Insert using the validated data
        Phone::create([
            'phone_number' => $request->phone_number,
            'phone_person' => $request->phone_person,
            'country_codes' => $request->country_codes,
            'student_id' => $request->students_id,
        ]);

        return redirect()->back()->with('success', 'Phone number added successfully.');
    }

    public function destroy($id)
    {
        // Find and delete the phone record
        $phone = Phone::findOrFail($id);
        $phone->delete();

        // Redirect back with a success message
        //return redirect()->back()->with('success', 'Phone number removed successfully.');
        return response()->json(['message' => 'Phone number deleted successfully.']);
    }
}
