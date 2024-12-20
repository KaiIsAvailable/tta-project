<?php
namespace App\Http\Controllers;
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

        // Pass the data to the dashboard view
        return view('dashboard', compact('studentCount'));
    }
}
