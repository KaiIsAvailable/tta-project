@extends('layouts.app')

@section('title', 'User List')

@section('content')
<div class="container">
    <h2>User List</h2>

    <div class="filter-container mb-3">
        <form method="GET" action="{{ route('users.index') }}" class="form-inline">
            <input type="text" name="name" class="form-control mr-2" placeholder="User Name" value="{{ request('name') }}">

            <select name="role" class="form-control mr-2">
                <option value="">All Roles</option>
                <option value="admin" {{ request('role') == 'admin' ? 'selected' : '' }}>Admin</option>
                <option value="instructor" {{ request('role') == 'instructor' ? 'selected' : '' }}>Instructor</option>
                <option value="student" {{ request('role') == 'student' ? 'selected' : '' }}>Student</option>
            </select>

            <button type="submit" class="btn btn-primary">Filter</button>
        </form>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <div class="table-responsive">
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Actions</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Role</th>
                </tr>
            </thead>
            <tbody>
                @foreach($users as $index => $user)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>
                            <div class="btn-group" role="group" aria-label="Actions">
                                <a href="{{ route('profile', ['id' => $user->id]) }}" class="btn btn-primary btn-sm">View Profile</a>
                            </div>
                        </td>
                        <td>{{ $user->name }}</td>
                        <td>{{ $user->email }}</td>
                        <td>{{ ucfirst($user->role) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="circle-button">
        <a href="{{route('register')}}" class="btn btn-primary rounded-circle" 
        style="width: 60px; height: 60px; display: flex; justify-content: center; align-items: center; position: fixed; bottom: 20px; right: 20px; font-size: 24px; border-radius: 50%;">
            +
        </a>
        <span class="tooltip-text">Add User</span>
    </div>

    <br>
    <div class="pagination-wrapper">
        {{ $users->links() }}
    </div>
</div>
@endsection
