<?php
namespace App\Http\Controllers;
use App\Models\Payment;
use Illuminate\Support\Facades\Auth;
use App\Models\Student;
use App\Models\User;
use Illuminate\Validation\ValidationException;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $users = User::all();
        $hasPending = $users->contains('approve','Pending');
        $hasNewRegister = $users->contains('email_verified_at', null);

        // Check if the user is authenticated
        if (!Auth::check()) {
            return redirect()->route('login'); // Redirect to login if not authenticated
        }
        if ($user->email_verified_at == null){
            Auth::logout();
            throw ValidationException::withMessages([
                'email' => 'Plese verify your email first.'
            ]);
        }elseif ($user->approve == 'Pending'){
            Auth::logout();
            throw ValidationException::withMessages([
                'email' => 'Your account has not been approved yet.'
            ]);
        }elseif ($user->approve == 'Rejected'){
            Auth::logout();
            throw ValidationException::withMessages([
                'email' => 'Your account has been rejected. Please contact administrator for any argument.'
            ]);
        }elseif ($user->approve == "Blocked"){
            Auth::logout();
            throw ValidationException::withMessages([
                'email' => 'Your account has been blocked.'
            ]);
        }
        // Get the total number of students
        if ($user->role === 'admin') {
            $studentCount = Student::count();
        } elseif ($user->role === 'instructor') {
            $studentCount = 0;
        } else {
            $studentCount = 0;
        }
        $paidPaymentCount = Payment::where('payment_status', 'Paid')->count();
        $unpaidPaymentCount = Payment::where('payment_status', 'Unpaid')->count();
        $voidedPaymentCount = Payment::where('payment_status', 'Voided')->count();

        return view('dashboard', compact(
            'studentCount', 'voidedPaymentCount', 'paidPaymentCount', 'unpaidPaymentCount', 'hasPending', 'hasNewRegister'
        ));
    }
}
