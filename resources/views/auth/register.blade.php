@extends('layouts.app')

@section('content')

<div class="form_container">   
    {{-- Display Validation Errors --}}
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Registration Form --}}
    <form method="POST" action="{{ route('register') }}" class="forms">
        @csrf

        {{-- Name Field --}}
        <div class="form-group">
            <label for="name">Name:</label>
            <input id="name" type="text" class="form-control @error('name') is-invalid @enderror" 
                name="name" value="{{ old('name') }}" required autocomplete="name" autofocus>

            @error('name')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>

        {{-- Email Field --}}
        <div class="form-group">
            <label for="email">Email Address:</label>
            <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" 
                name="email" value="{{ old('email') }}" required autocomplete="email">

            @error('email')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>

        {{-- Role Selection Field --}}
        <div class="form-group">
            <label for="role">Role:</label>
            <select id="role" name="role" class="form-control @error('role') is-invalid @enderror" required>
                <option value="">Select a role</option>
                <option value="student" {{ old('role') == 'student' ? 'selected' : '' }}>Student</option>
                <option value="instructor" {{ old('role') == 'instructor' ? 'selected' : '' }}>Instructor</option>
                <option value="admin" {{ old('role') == 'admin' ? 'selected' : '' }}>Admin</option>
            </select>

            @error('role')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>

        {{-- Password Field 
        <div class="form-group">
            <label for="password">Password:</label>
            <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" 
                name="password" required autocomplete="new-password">

            @error('password')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>--}}

        {{-- Confirm Password Field 
        <div class="form-group">
            <label for="password-confirm">Confirm Password:</label>
            <input id="password-confirm" type="password" class="form-control" 
                name="password_confirmation" required autocomplete="new-password">
        </div>--}}

        {{-- Submit & Cancel Buttons --}}
        <div class="form-group">
            <button type="submit" class="btn btn-primary">Register</button>
            <a href="{{ route('users.index') }}" class="btn btn-secondary">Cancel</a>
        </div>
    </form>
</div>

@endsection
