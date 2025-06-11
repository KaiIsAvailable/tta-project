<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\Student;
use App\Models\PaymentSetting;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Intervention\Image\ImageManager;
use Illuminate\Support\Facades\Response;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\Encoders\PngEncoder;

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
        // Validate the incoming data
        $validated = $request->validate([
            'student_id' => 'required|exists:students,student_id',
            'student_price' => 'required|numeric|min:0',
            'student_startDate' => 'required|date'
        ]);

        $studentId = $validated['student_id'];
        $studentPrice = $validated['student_price'];
        $studentStartDate = $validated['student_startDate'];

        // Find the latest paid month for this student
        $lastPayment = Payment::where('student_id', $studentId)
            ->where('payment_status', '!=', 'Voided')
            ->orderBy('paid_for', 'desc')
            ->first();

        // Calculate the next month
        $nextMonth = $lastPayment
            ? Carbon::parse($lastPayment->paid_for)->addMonth()->format('Y-m')
            : Carbon::parse($studentStartDate)->format('Y-m'); // Default to the current month if no payments exist

        // Create the payment record for the next month
        Payment::create([
            'student_id' => $studentId,
            'payment_amount' => $studentPrice, // Default amount, adjust as needed
            'payment_method' => 'N/A', // Default method, adjust as needed
            'paid_for' => $nextMonth . '-01',
            'payment_date' => null,
        ]);

        return redirect()->back()->with('success', 'Payment for ' . $nextMonth . ' added successfully.');
    }

    // Show a list of all payments in payment_index.blade the profile is in studentController
    public function index(Request $request)
    {
        // Retrieve filter inputs from the request
        $name = $request->get('name');
        $paymentStatus = $request->get('payment_status');
        $paidFor = $request->get('paid_for');

        // Build the query with filters
        $query = Payment::with('student')->orderBy('payment_id', 'desc');

        // Apply filters conditionally
        if ($name) {
            $query->whereHas('student', function ($q) use ($name) {
                $q->where('name', 'LIKE', "%$name%");
            });
        }

        if ($paymentStatus) {
            $query->where('payment_status', $paymentStatus);
        }

        if ($paidFor) {
            $query->where('paid_for', 'LIKE', "$paidFor%");
        }

        // Paginate the filtered results
        $payments = $query->paginate(10);

        // Pass the results to the view
        return view('students.payments.payment_index', compact('payments'));
    }

    // Show a specific payment (optional)
    public function show($id)
    {
        // Fetch the student with payments ordered by 'paid_for'
        $student = Student::findOrFail($id);

        return view('students.show', compact('student', 'payments', ));
    }

    public function edit($paymentId)
    {
        // Find the payment by ID
        $payment = Payment::findOrFail($paymentId);

        // Find the student associated with the payment
        $student = $payment->student;  // Assuming you have a relationship set up

        // Get the previous month
        $previousMonth = Carbon::parse($payment->paid_for)->subMonth();

        // Fetch the previous month's payment record for the same student
        $previousPayment = Payment::where('student_id', $payment->student_id)
            ->where('paid_for', $previousMonth)
            ->first();
        
        if ($previousPayment && $previousPayment->payment_status === 'Unpaid') {
            // Redirect back with an error message
            return redirect()->back()->withErrors(['error' => 'Previous month\'s payment is still unpaid.']);
        }

        // Previous outstanding and pre-payment, default to 0 if no record exists
        $previousOutstanding = $previousPayment ? $previousPayment->payment_outstanding : 0;
        $previousPrePayment = $previousPayment ? $previousPayment->payment_preAmt : 0;

        // Pass both payment and student data to the view
        return view('students.payments.payment_update', compact('payment', 'student', 'previousOutstanding', 'previousPrePayment', 'previousMonth'));
    }

    public function update(Request $request, $paymentId)
    {
        // Validate the incoming data
        $validated = $request->validate([
            'payAmt' => 'required|numeric|min:0',
            'payment_method' => 'required|string|max:255',
            'paid_for' => 'required|date_format:Y-m',
            'paid_date' => 'required|date',
            'payment_amount' => 'required|numeric|min:0',
            'total' => 'required|numeric',
        ]);

        if ($validated['payAmt'] == 0 && $validated['payment_method'] !== 'Pre Payment') {
            return back()->withErrors(['payment_method' => 'If Pay Amount is 0, the payment method must be Pre Payment.'])
                ->withInput();
        }

        // Find the payment record by ID
        $payment = Payment::findOrFail($paymentId);

        $payAmt = $validated['payAmt'];
        $totalAmt = $validated['total'];
        $outstanding = 0;
        $preAmount = 0;

        // Determine outstanding and preAmt
        if ($payAmt < $totalAmt) {
            $outstanding = $totalAmt - $payAmt;
        }else{
            $preAmount = $totalAmt - $payAmt;
        }
        
        // Update the payment fields
        $payment->update([
            'payment_payAmt' => $validated['payAmt'],
            'payment_method' => $validated['payment_method'],
            'paid_for' => $validated['paid_for'],
            'payment_status' => 'Paid',
            'payment_date' => $validated['paid_date'],
            'payment_outstanding' => $outstanding,
            'payment_preAmt' => $preAmount,
        ]);

        //dd($request->all());

        // Redirect to the student's profile page with a success message
        return redirect()->route('students.showProfile', ['student_id' => $payment->student_id])
                 ->with('success', 'Payment updated successfully.');
    }

    public function void(Request $request, $paymentId)
    {
        // Find the payment record by ID
        $payment = Payment::findOrFail($paymentId);

        // Update the payment fields
        $payment->update([
            'payment_status' => 'Voided'
        ]);

        // Redirect to the student's profile page with a success message
        return redirect()->route('students.showProfile', ['student_id' => $payment->student_id])
                 ->with('success', 'Payment updated successfully.');
    }

    public function showReceipt($paymentId)
    {
        // Fetch payment details
        $payment = Payment::with(['student'])->findOrFail($paymentId);

        // Get the previous month
        $previousMonth = Carbon::parse($payment->paid_for)->subMonth();

        // Fetch the previous month's payment record for the same student
        $previousPayment = Payment::where('student_id', $payment->student_id)
            ->where('paid_for', $previousMonth)
            ->first();

        // Previous outstanding and pre-payment, default to 0 if no record exists
        $previousOutstanding = $previousPayment ? $previousPayment->payment_outstanding : 0;
        $previousPrePayment = $previousPayment ? $previousPayment->payment_preAmt : 0;

        return view('students.receipts.receipts', compact('payment', 'previousOutstanding', 'previousPrePayment', 'previousMonth'));
    }

    public function showInvoice($paymentId)
    {
        // Fetch payment details
        $payment = Payment::with(['student'])->findOrFail($paymentId);

        // Get the previous month
        $previousMonth = Carbon::parse($payment->paid_for)->subMonth();

        // Fetch the previous month's payment record for the same student
        $previousPayment = Payment::where('student_id', $payment->student_id)
            ->where('paid_for', $previousMonth)
            ->first();

        // Previous outstanding and pre-payment, default to 0 if no record exists
        $previousOutstanding = $previousPayment ? $previousPayment->payment_outstanding : 0;
        $previousPrePayment = $previousPayment ? $previousPayment->payment_preAmt : 0;

        return view('students.invoices.invoices', compact('payment', 'previousOutstanding', 'previousPrePayment', 'previousMonth'));
    }

    /*public function showSignature()
    {
        $paymentSetting = PaymentSetting::first();

        if (!$paymentSetting || !$paymentSetting->pSign) {
            abort(404, 'Signature not found.');
        }

        $signature = $paymentSetting->pSign;

        $finfo = new \finfo(FILEINFO_MIME_TYPE);
        $mime = $finfo->buffer($signature);

        return response($signature)->header('Content-Type', $mime);
    }*/
    public function showSignature()
    {
        $paymentSetting = PaymentSetting::first();

        if (!$paymentSetting || !$paymentSetting->pSign) {
            abort(404, 'Signature not found.');
        }

        $signatureData = $paymentSetting->pSign;

        // Load the image from binary blob
        $manager = new ImageManager(new Driver());
        $image = $manager->read($signatureData);

        $width = $image->width();
        $height = $image->height();

        // Loop through the image and add watermark text
        for ($y = 0; $y < $height; $y += 50) {
            for ($x = 0; $x < $width; $x += 150) {
                $image->text("Tham's Taekwon-Do Academy", $x, $y, function ($font) {
                    $font->size(24);
                    $font->color('rgba(103, 103, 103, 0.29)');
                    $font->align('center');
                    $font->angle(-45);
                });
            }
        }

        // Return image with proper content-type
        return response($image->toJpeg(80))->header('Content-Type', 'image/jpeg');
    }
}
