<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Student;
use App\Notifications\ApprovedUser;
use App\Notifications\RejectedUser;
use App\Notifications\UserApprovedAlert;
use App\Notifications\ResetPassword;
use App\Notifications\VerifyEmail;
use Illuminate\Support\Facades\Notification;
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
        $students = Student::whereNotIn('student_id', function ($query) {
            $query->select('student_id')->from('users')->whereNotNull('student_id');
        })->get();

        // Filter by name
        if ($request->filled('name')) {
            $query->where('name', 'like', '%' . $request->name . '%');
        }

        // Filter by role
        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        if ($request->filled('approve')){
            $query->where('approve', $request->approve);
        }

        // Paginate the results (10 per page)
        $users = $query->orderByRaw("
            CASE approve
                WHEN 'Pending' THEN 0
                WHEN 'Approved' THEN 1
                WHEN 'Blocked' THEN 2
                WHEN 'Rejected' THEN 3
                ELSE 4
            END
        ")->paginate(10);

        return view('user.index', compact('users', 'students'));
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
            'role' => 'required|in:student,instructor,admin,viewer'
        ]);

        // Create user
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'role' => $request->role,
            'password' => $request->role === 'student' ? Hash::make($request->password) : bcrypt(Str::random(10)), // Generate random password for non-students
            'approve' => 'Approved'
        ]);

        // ✅ If role is Instructor or Admin, send password reset email
        if (in_array($request->role, ['instructor', 'admin', 'student', 'viewer'])) {
            //Password::sendResetLink(['email' => $user->email]);
            $token = Password::createToken($user);
            $user->notify(new ResetPassword($token));
        }

        return redirect('/users')->with('success', 'User registered successfully! An Email have sent to ' . $user->name);
    }

    // Show user profile
    public function profile()
    {
        $user = Auth::user();

        // Only allow 'admin' and 'approvedUser' roles to view this page
        if ($user->role !== 'admin') {
            abort(403, 'Unauthorized action.');
        }

        return view('user.profile', compact('user'));
    }

    // Admin can view all users
    public function viewAllUsers()
    {
        $user = Auth::user();

        // Check if user is an admin or approved user
        if ($user->role !== 'admin') {
            abort(403, 'Unauthorized action.');
        }

        $users = User::all(); // Fetch all users
        return view('user.index', compact('users'));
    }

    // For user register itself
    public function showUserRegistrationForm()
    {
        return view('user.userRegister');
    }

    public function userRegister(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'images' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8'
        ]);

        // Create user
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'role' => "student",
            'password' => Hash::make($request->password)
        ]);

        // Handle profile picture upload after user is saved
        if ($user->save()) {
            $this->handleUserImageUpload($request, $user);
            $user->save();
        }

        $user->notify(new VerifyEmail());

        $admins = User::where('role', 'admin')->get();
        Notification::send($admins, new UserApprovedAlert($user));

        return redirect('login')->with('success', 'Congratulations! you have registered successfully! Please check your email to verify');
    }

    public function approveUser(Request $request, $id)
    {
        $request->validate([
            'student_id' => 'required|exists:students,student_id',
        ]);

        $user = User::findOrFail($id);

        $success = $user->update([
            'approve' => 'Approved',
            'student_id' => $request->student_id
        ]);    

        if ($success)
        {
            $user->notify(new ApprovedUser());
            return redirect()->route('users.index', $id)->with('success', 'You have APPROVED ' . $user->name . ' as a student');
        }else{
            return redirect()->route('users.index', $id)->with('error', 'Failed to APPROVE user');
        }
    }

    public function rejectUser($id)
    {
        $user = User::findOrFail($id);

        $user->update([
            'approve' => 'Rejected'
        ]);

        $user->notify(new RejectedUser());

        return redirect()->route('users.index', $id)->with('success', 'You have REJECTED ' . $user->name . ' as a student');
    }

    private function handleUserImageUpload(Request $request, User $user)
    {
        if ($request->hasFile('images')) {
            try {
                // Delete old image if exists
                if ($user->images && file_exists(public_path($user->images))) {
                    unlink(public_path($user->images));
                }
                
                $image = $request->file('images');
                
                // Generate unique filename
                $userId = $user->id ?? uniqid();
                $filename = 'user_' . $userId . '_' . time() . '.' . $image->getClientOriginalExtension();
                
                // Ensure directory exists
                $uploadPath = public_path('storage/student_photos');
                if (!file_exists($uploadPath)) {
                    mkdir($uploadPath, 0755, true);
                }
                
                // Move file to storage directory
                $image->move($uploadPath, $filename);
                
                // Save file path to database
                $user->images = 'storage/student_photos/' . $filename;
                
                return true;
            } catch (\Exception $e) {
                return false;
            }
        }
        return true;
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);
        
        $request->validate([
            'name' => 'required|string|max:255',
            'images' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'email' => 'required|string|email|max:255|unique:users,email,' . $id,
        ]);

        $user->update([
            'name' => $request->name,
            'email' => $request->email,
        ]);

        // Handle image upload
        $this->handleUserImageUpload($request, $user);
        $user->save();

        return redirect()->back()->with('success', 'User updated successfully.');
    }

    public function destroy($id)
    {
        $user = User::findOrFail($id);
        
        // Delete user image file if exists
        if ($user->images && file_exists(public_path($user->images))) {
            unlink(public_path($user->images));
        }
        
        $user->delete();
        
        return redirect()->route('users.index')->with('success', 'User deleted successfully.');
    }
}
