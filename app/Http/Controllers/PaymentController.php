<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\Student;
use Illuminate\Http\Request;
use Carbon\Carbon;

class PaymentController extends Controller
{
    // Show the form to create a payment
    public function create(Request $request)
    {
        $studentId = $request->query('student_id'); // Retrieves 'student_id' from the query string
        $student = Student::findOrFail($studentId);

        return view('students.payments.payment_create', compact('student'));
    }

    public function store(Request $request)
    {
        // Validate the incoming payment data
        $validated = $request->validate([
            'student_id' => 'required|exists:students,student_id',
            'payment_amount' => 'required|numeric|min:0',
            'payment_method' => 'required|string',
            'paid_for' => [
                'required',
                'date_format:Y-m',
                // Ensure the combination of student_id and paid_for is unique
                function ($attribute, $value, $fail) use ($request) {
                    // Retrieve student_id from the query string
                    $exists = Payment::where('student_id', $request->student_id)
                        ->where('paid_for', $value . '-01') // Adjusted the date format
                        ->exists();
                        
                    // If a payment already exists for this student and month
                    if ($exists) {
                        $fail('The student already has a payment for ' . $value . '.');
                    }
                }
            ],
        ]);

        // Append "-01" to `paid_for` to make it a valid date format (YYYY-MM-DD)
        $validated['paid_for'] = $validated['paid_for'] . '-01';

        // Add the payment_date to the validated data
        $validated['payment_date'] = now();

        // Store the payment record
        Payment::create($validated);

        // Redirect back to the student's profile with a success message
        return redirect()->route('students.showProfile', $validated['student_id'])
            ->with('success', 'Payment added successfully.');
    }

    // Show a list of all payments
    public function index()
    {
        $payments = Payment::with('student')->get(); // Eager load the student relationship
        return view('students.payments.payment_index', compact('payments'));
    }

    // Show a specific payment (optional)
    public function show($id)
    {
        $payment = Payment::findOrFail($id);
        return view('payments.show', compact('payment'));
    }
}
