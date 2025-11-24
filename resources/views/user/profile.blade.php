@extends('layouts.app')
@section('title', 'User Profile')
@section('content')

@if (!auth()->user()->isAdmin() && auth()->user()->id !== $user->id && !auth()->user()->isViewer())
    <script>
        window.location.href = "{{ route('dashboard') }}";
    </script>
@endif



<div class="container">
    <h1 class="text-left mb-4" style="display: inline;">
        @if(auth()->user()->role === 'viewer')
            User ***'s Profile
        @else
            {{ $user->name }}'s Profile
        @endif
    </h1>
    @if (auth()->user()->isAdmin() && auth()->user()->id !== $user->id)
        <form method="POST" action="{{ route('profile.destroy', $user->id) }}" style="display: inline;">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete your account?');">Delete</button>
        </form>
    @endif
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
                    @if(auth()->user()->role === 'viewer')
                        <div style="width: 150px; height: 150px; background-color: #f0f0f0; border: 1px solid #ccc; display: flex; align-items: center; justify-content: center; color: #666; margin: 0 auto;">
                            [Hidden]
                        </div>
                        <h3>User ***</h3>
                    @else
                        @if($user->images)
                            <img src="{{ asset($user->images) }}" alt="{{ $user->name }}" class="profile-pictures img-fluid">
                        @else
                            <svg xmlns="http://www.w3.org/2000/svg" width="150" height="150" viewBox="0 0 50 50" class="profile-pictures img-fluid">
                                <circle cx="25" cy="25" r="25" fill="#ccc" />
                                <text x="25" y="30" font-size="18" text-anchor="middle" fill="#555">?</text>
                            </svg>
                        @endif
                        <h3>{{ $user->name }}</h3>
                    @endif
                    <p><strong>Role:</strong> {{ $user->role }}</p>
                </div>
            </div>
        </div>

        <!-- Right Column (User Info) -->
        <div class="profile-right">
            <div class="card mb-3">
                <div class="card-body">
                    <h5>Contact Information:</h5>
                    <p><strong>Email:</strong> 
                        @if(auth()->user()->role === 'viewer')
                            <span>****@****.***</span>
                        @else
                            {{ $user->email }}
                        @endif
                    </p>
                </div>
            </div>
            
            <!-- Edit Profile -->
            <div class="card mb-3">
                <div class="card-body">
                    <h5>Edit Profile:</h5>
                    <form action="{{ route('profile.updateProfile') }}" method="POST">
                        @csrf
                        <input type="hidden" id="userId" name="userId" value="{{ $user->id }}">
                        <div class="form-group mb-3">
                            <label for="name">Name:</label>
                            @if(auth()->user()->role === 'viewer')
                                <input type="text" id="name" name="name" class="form-control" value="User ***" readonly>
                            @else
                                <input type="text" id="name" name="name" class="form-control" value="{{ old('name', $user->name) }}" readonly>
                            @endif
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="form-group mb-3">
                            <label for="email">Email:</label>
                            @if(auth()->user()->role === 'viewer')
                                <input type="email" id="email" name="email" class="form-control" value="****@****.***" readonly>
                            @else
                                <input type="email" id="email" name="email" class="form-control" value="{{ old('email', $user->email) }}" readonly>
                            @endif
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        @if (Auth::user()->role === 'admin' && auth()->user()->id !== $user->id)
                            <div class="form-group mb-3">
                                <label for="role">Role:</label>
                                <select id="role" name="role" class="form-control" readonly>
                                    <option value="admin" {{ old('role', $user->role) == 'admin' ? 'selected' : '' }}>Admin</option>
                                    <option value="instructor" {{ old('role', $user->role) == 'instructor' ? 'selected' : '' }}>Instructor</option>
                                    <option value="student" {{ old('role', $user->role) == 'student' ? 'selected' : '' }}>Student</option>
                                </select>
                            </div>
                            @if ($user->approve == "Approved" || $user->approve == "Blocked")
                                <div>
                                    <label for="approve">Status:</label>
                                    <select name="approve" id="approve" class="form-control">
                                        <option value="Approved" {{ old('approve', $user->approve) == 'Approved' ? 'selected' : '' }}>Unblock</option>
                                        <option value="Blocked" {{ old('approve', $user->approve) == 'Blocked' ? 'selected' : '' }}>Blocked</option>
                                    </select>
                                </div>
                            @endif
                        @else
                            <input type="hidden" name="role" id="role" class="form-control" value="{{ old('role', $user->role) }}" readonly>
                            <input type="hidden" name="approve" id="approve" class="form-control" value="{{ old('approve', $user->approve) }}" readonly>
                        @endif
                        @if (Auth::User()->isAdmin() || Auth::User()->isViewer())
                            @if(auth()->user()->role === 'viewer')
                                <button type="button" class="btn btn-primary" onclick="alert('Permission Denied: Demo account cannot perform this action')">Update Profile</button>
                            @else
                                <button type="submit" class="btn btn-primary">Update Profile</button>
                            @endif
                        @endif
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
