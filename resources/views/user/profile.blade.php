@extends('layouts.app')
@section('title', 'User Profile')
@section('content')

<div class="container">
    <h1 class="text-left mb-4" style="display: inline;">{{ $user->name }}'s Profile</h1>
    <form method="POST" action="{{ route('profile.destroy') }}" style="display: inline;">
        @csrf
        @method('DELETE')
        <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete your account?');">Delete</button>
    </form>
    @if (session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif

    <div class="profile-container">
        <!-- Left Column (Profile Picture & Name) -->
        <div class="profile-left">
            <div class="card mb-3">
                <div class="card-body text-center">
                    @if($user->profile_picture)
                        <img src="data:image/jpeg;base64,{{ base64_encode($user->profile_picture) }}" alt="{{ $user->name }}" class="profile-pictures img-fluid">
                    @else
                        <svg xmlns="http://www.w3.org/2000/svg" width="150" height="150" viewBox="0 0 50 50" class="profile-pictures img-fluid">
                            <circle cx="25" cy="25" r="25" fill="#ccc" />
                            <text x="25" y="30" font-size="18" text-anchor="middle" fill="#555">?</text>
                        </svg>
                    @endif
                    <h3>{{ $user->name }}</h3>
                    <p><strong>Role:</strong> {{ $user->role }}</p>
                </div>
            </div>
        </div>

        <!-- Right Column (User Info) -->
        <div class="profile-right">
            <div class="card mb-3">
                <div class="card-body">
                    <h5>Contact Information:</h5>
                    <p><strong>Email:</strong> {{ $user->email }}</p>
                </div>
            </div>
            
            <!-- Edit Profile -->
            <div class="card mb-3">
                <div class="card-body">
                    <h5>Edit Profile:</h5>
                    <form action="{{ route('profile.updateProfile', $user->id) }}" method="POST">
                        @csrf
                        @method('patch')
                        <div class="form-group mb-3">
                            <label for="name">Name:</label>
                            <input type="text" id="name" name="name" class="form-control" value="{{ old('name', $user->name) }}" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="form-group mb-3">
                            <label for="email">Email:</label>
                            <input type="email" id="email" name="email" class="form-control" value="{{ old('email', $user->email) }}" required>
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        @if (Auth::user()->role === 'admin')
                            <div class="form-group mb-3">
                                <label for="role">Role:</label>
                                <select id="role" name="role" class="form-control" required>
                                    <option value="" disabled selected>Select a role</option>
                                    <option value="admin" {{ old('role', $user->role) == 'admin' ? 'selected' : '' }}>Admin</option>
                                    <option value="instructor" {{ old('role', $user->role) == 'instructor' ? 'selected' : '' }}>Instructor</option>
                                    <option value="student" {{ old('role', $user->role) == 'student' ? 'selected' : '' }}>Student</option>
                                </select>

                                <p>Role options: <strong>Admin</strong>, <strong>Instructor</strong>, and <strong>Student</strong></p>
                            </div>
                        @endif
                        <button type="submit" class="btn btn-primary">Update Profile</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
