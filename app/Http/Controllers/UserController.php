<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;


class UserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::query();

        // Filter by name
        if ($request->filled('name')) {
            $query->where('name', 'like', '%' . $request->name . '%');
        }

        // Filter by role
        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        // Paginate the results (10 per page)
        $users = $query->paginate(10);

        return view('user.index', compact('users'));
    }

    // Show registration form
    public function showRegistrationForm()
    {
        return view('auth.register');
    }

    // Handle user registration
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => $request->role === 'student' ? 'required|string|min:8|confirmed' : '', // Only required for students
            'role' => 'required|in:student,instructor,admin',
        ]);

        // Create user
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'role' => $request->role,
            'password' => $request->role === 'student' ? Hash::make($request->password) : bcrypt(Str::random(10)), // Generate random password for non-students
        ]);

        // ✅ If role is Instructor or Admin, send password reset email
        if (in_array($request->role, ['instructor', 'admin'])) {
            Password::sendResetLink(['email' => $user->email]);
        }

        return redirect('/users')->with('success', 'User registered successfully!');
    }

    // Show user profile
    public function profile()
    {
        $user = Auth::user();

        // Only allow 'admin' and 'approvedUser' roles to view this page
        if ($user->role !== 'admin' && $user->role !== 'approvedUser') {
            abort(403, 'Unauthorized action.');
        }

        return view('user.profile', compact('user'));
    }

    // Admin can view all users
    public function viewAllUsers()
    {
        $user = Auth::user();

        // Check if user is an admin or approved user
        if ($user->role !== 'admin' && $user->role !== 'approvedUser') {
            abort(403, 'Unauthorized action.');
        }

        $users = User::all(); // Fetch all users
        return view('user.index', compact('users'));
    }
}
