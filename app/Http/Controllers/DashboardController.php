<?php
namespace App\Http\Controllers;
use App\Models\Payment;
use Illuminate\Support\Facades\Auth;
use App\Models\Student;

class DashboardController extends Controller
{
    public function index()
    {
        // Check if the user is authenticated
        if (!Auth::check()) {
            return redirect()->route('login'); // Redirect to login if not authenticated
        }
        // Get the total number of students
        $studentCount = Student::count();
        $paidPaymentCount = Payment::where('payment_status', 'Paid')->count();
        $unpaidPaymentCount = Payment::where('payment_status', 'Unpaid')->count();
        $voidedPaymentCount = Payment::where('payment_status', 'Voided')->count();

        return view('dashboard', compact(
            'studentCount', 'voidedPaymentCount', 'paidPaymentCount', 'unpaidPaymentCount'
        ));
    }
}
