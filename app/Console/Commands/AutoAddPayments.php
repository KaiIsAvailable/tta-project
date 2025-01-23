<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Student;
use App\Models\Payment;
use Carbon\Carbon;

class AutoAddPayments extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'auto:add-payments';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Automatically add payments for all students at the beginning of each month.';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        // Get the first day of the current month
        $currentMonth = Carbon::now()->startOfMonth();

        // Retrieve all students
        $students = Student::all();

        foreach ($students as $student) {
            // Check if a payment already exists for the student for this month
            $paymentExists = Payment::where('student_id', $student->student_id)
                ->where('paid_for', $currentMonth)
                ->where('payment_status', '!=', 'Voided')
                ->exists();

            if (!$paymentExists) {
                // Create the payment record
                Payment::create([
                    'student_id' => $student->student_id,
                    'payment_amount' => $student->fee, // Default amount or any logic you want
                    'payment_method' => 'N/A', // Default method or logic
                    'paid_for' => $currentMonth,
                    'payment_date' => null, //After pay then only got date
                ]);
            }
        }

        $this->info('Payments added successfully for the current month.');
        return 0;
    }
}
