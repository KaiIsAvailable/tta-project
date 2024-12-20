<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
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
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'student', // Set default role as 'student'
        ]);

        Auth::login($user);

        return redirect('/')->with('success', 'Registration successful!');
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
