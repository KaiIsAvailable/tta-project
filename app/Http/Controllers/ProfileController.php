<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\QueryException;
use Illuminate\Validation\ValidationException;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function index(Request $request)
    {
        // This could load the profile data for the logged-in user
        $id = $request->input('id');
        $user = User::findOrFail($id); // assuming you're using authentication

        return view('user.profile', compact('user')); // You can change the view name if necessary
    }

    public function edit(Request $request)
    {
        $id = $request->query('id');
        $user = User::findOrFail($id); // Fetch user or return 404 if not found
        return view('profile.edit', compact('user'));
    }

    /*
    Update the user's profile information.
     
    public function update(ProfileUpdateRequest $request, $id)
    {
        $user = User::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'email', 'max:255', Rule::unique('users')->ignore($id)],
            'role' => 'required|string|in:admin,instructor,student',
            'approve' => 'required|string|in:Approved, Blocked'
        ]);

        try {
            $user->update([
                'name' => $request->name,
                'email' => $request->email,
                'role' => $request->role,
                'approve' => $request->approve
            ]);

            return redirect()->route('user.profile', $id)->with('success', 'Profile updated successfully!');
        } catch (QueryException $e) {
            return back()->with('error', 'Database error: ' . $e->getMessage()); // Logs specific error
        } catch (ValidationException $e) {
            return back()->with('error', 'Validation error: ' . $e->getMessage());
        } catch (\Exception $e) {
            return back()->with('error', 'Unexpected error: ' . $e->getMessage());
        }
    }*/

    public function updateProfile(Request $request)
    {
        $id = $request->input('userId');
        $user = User::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'email', 'max:255', Rule::unique('users')->ignore($id)],
            'role' => 'required|string|in:admin,instructor,student',
            'approve' => 'required|string|in:Approved,Blocked'
        ]); 

        $user->update([
            'name' => $request->name,
            'email' => $request->email,
            'role' => $request->role,
            'approve' => $request->approve
        ]);

        if (Auth::user()->role === 'admin'){
            return redirect()->route('users.index')->with('success', 'Profile ' . $user->name . ' updated successfully!');
        }
        session()->flash('success', 'Profile ' . $user->name . ' updated successfully!');
        return view('user.profile', compact('user'));
    }

    /**
     * Delete the user's account.
     */
    public function destroy(User $user)
    {
        $user->classes()->detach();

        $user->delete();

        return redirect()->route('user.index')->with('success', $user->name.' records deleted successfully.');
    }
}
