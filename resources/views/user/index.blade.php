@extends('layouts.app')

@section('title', 'User List')

@section('content')
@if (!auth()->user()->isAdmin() && !auth()->user()->isViewer())
    <script>
        window.location.href = "{{ route('dashboard') }}";
    </script>
@endif
@include('components.loadingAction')

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

            <select name="approve" class="form-control me-2">
                <option value="">Approve Status</option>
                <option value="Approved">Approved</option>
                <option value="Pending">Pending to approve</option>
                <option value="Rejected">Rejected</option>
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
                    <th>Image</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach($users as $index => $user)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>
                            {{-- <div class="btn-group" role="group" aria-label="Actions">
                                <a href="{{ route('profile', ['id' => $user->id]) }}" class="btn btn-primary btn-sm">View Profile</a>
                            </div> --}}
                            <div class="btn-group" role="group" aria-label="Actions">
                                <form action="{{ route('profile') }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="id" value="{{ $user->id }}">
                                    <button type="submit" class="btn btn-primary btn-sm">View Profile</button>
                                </form>
                            </div>
                        </td>
                        <td>
                            @if(auth()->user()->role === 'viewer')
                                User ***
                            @else
                                {{ $user->name }}
                            @endif
                        </td>
                        <td>
                            @if(auth()->user()->role === 'viewer')
                                <div style="height: 150px; width: 150px; background-color: #f0f0f0; border: 1px solid #ccc; display: flex; align-items: center; justify-content: center; color: #666;">
                                    [Hidden]
                                </div>
                            @else
                                @if($user->images)
                                    <img src="{{ asset($user->images) }}" alt="{{ $user->name }}" class="profile-picture" style="height: 150px !important; width: 150px !important; object-fit: cover;" loading="lazy">
                                @else
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 50 50" class="profile-picture">
                                        <circle cx="25" cy="25" r="25" fill="#ccc" />
                                        <text x="25" y="30" font-size="18" text-anchor="middle" fill="#555">?</text>
                                    </svg>
                                @endif
                            @endif
                        </td>
                        <td>
                            @if($user->email)
                                {{ $user->email }}
                            @elseif(auth()->user()->role === 'viewer')
                                <span>****@****.***</span>
                            @else
                                <span>No email</span>
                            @endif
                        </td>
                        <td>{{ ucfirst($user->role) }}</td>
                        <td>
                            @if(auth()->user()->role === 'viewer')
                                ***
                            @else
                                @if ($user->approve == "Approved")
                                    <p style="color: green;">{{ $user->approve }}</p>
                                    @if ($user->students)
                                        <p>{{ 'S' . sprintf('%05d',$user->student_id) }}-{{ $user->students->name }}</p>
                                    @endif
                                @elseif ($user->approve == "Blocked")
                                    <p style="color: red; margin: 0;">{{ $user->approve }}</p>
                                    @if ($user->students)
                                        <p>{{ 'S' . sprintf('%05d',$user->student_id) }}-{{ $user->students->name }}</p>
                                    @endif            
                                @elseif ($user->approve == "Rejected")
                                    <div style="display: flex; align-items: center;">
                                        <p style="color: red; margin: 0;">{{ $user->approve }}</p>
                                        <div class="tooltips">
                                            <button style="color: green; border: none; background: none; cursor: pointer; margin-left: 10px;"  data-user-id="{{ $user->id }}" class="selectStudentBtn">
                                                &#10004;
                                            </button>
                                            <span class="tooltips-text">Approve</span>
                                        </div>
                                        <div data-user-id="{{ $user->id }}" class="studentSelectionForm" style="display: none;
                                        position: fixed;
                                        top: 0; left: 0;
                                        width: 100%; height: 100%;
                                        background-color: rgba(255, 255, 255, 0.8);
                                        z-index: 9999;
                                        text-align: center;
                                        justify-content: center;
                                        padding-top: 200px;
                                        font-size: 24px;
                                        color: #333;" >
                                            <form action="{{ route('approveUser', $user->id) }}" method="POST" style="display: inline;">
                                                @csrf

                                                <label for="studentName">Select student name:</label>
                                                <select name="studentName" id="studentName">
                                                    <option value="">Student not added yet</option>
                                                    @foreach ($students as $student)
                                                        <option value="{{ $student->id }}">{{ $student->name }}</option>
                                                    @endforeach
                                                </select>

                                                <br><br>
                                                <button type="submit" data-user-id="{{ $user->id }}" class="btn btn-primary btn-sm cancelSelectionBtn">Approve</button>
                                            </form>
                                            <button data-user-id="{{ $user->id }}" class="btn btn-secondary cancelSelectionBtn">Cancel</button>
                                        </div>
                                    </div>
                                @elseif ($user->approve == 'Pending')
                                    @if ($user->email_verified_at == null)
                                        <p style="color: red;">User not yet verify email</p>
                                    @endif
                                    <p>Do you know this guy?</p>
                                    <div class="tooltips">
                            @endif
                                    <button style="color: green; border: none; background: none; cursor: pointer;" data-user-id="{{ $user->id }}" class="selectStudentBtn">
                                        &#10004;
                                    </button>
                                    <span class="tooltips-text">Approve</span>
                                </div>
                                <div data-user-id="{{ $user->id }}" class="studentSelectionForm" style="display: none;
                                position: fixed;
                                top: 0; left: 0;
                                width: 100%; height: 100%;
                                background-color: rgba(255, 255, 255, 0.8);
                                z-index: 9999;
                                text-align: center;
                                justify-content: center;
                                padding-top: 200px;
                                font-size: 24px;
                                color: #333;" >
                                    <form action="{{ route('approveUser', $user->id) }}" method="POST" style="display: inline;">
                                        @csrf

                                        <label for="studentName">Select student name:</label>
                                        <select name="student_id" id="studentName">
                                            <option value="">Student not added yet</option>
                                            @foreach ($students as $student)
                                                <option value="{{ $student->student_id }}">{{ $student->name }}</option>
                                            @endforeach
                                        </select>

                                        <br><br>
                                        <button type="submit" data-user-id="{{ $user->id }}" class="btn btn-primary btn-sm cancelSelectionBtn">Approve</button>
                                    </form>
                                    <button data-user-id="{{ $user->id }}" class="btn btn-secondary cancelSelectionBtn">Cancel</button>
                                </div>
                                <form action="{{ route('rejectUser', $user->id) }}" method="POST" style="display: inline;">
                                    @csrf
                                    <div class="tooltips">
                                        <button style="color: red; border: none; background: none; cursor: pointer;">
                                            &#10008;
                                        </button>
                                        <span class="tooltips-text">Reject</span>
                                    </div>
                                </form>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    @if (Auth::User()->isAdmin() || Auth::User()->isViewer())
        <div class="circle-button">
            @if(auth()->user()->role === 'viewer')
                <button class="btn btn-primary rounded-circle" onclick="alert('Permission Denied: Demo account cannot perform this action')" 
                style="width: 60px; height: 60px; display: flex; justify-content: center; align-items: center; position: fixed; bottom: 20px; right: 20px; font-size: 24px; border-radius: 50%;">
                    +
                </button>
            @else
                <a href="{{route('register')}}" class="btn btn-primary rounded-circle" 
                style="width: 60px; height: 60px; display: flex; justify-content: center; align-items: center; position: fixed; bottom: 20px; right: 20px; font-size: 24px; border-radius: 50%;">
                    +
                </a>
            @endif
            <span class="tooltip-text">Add User</span>
        </div>
    @endif

    <br>
    <div class="pagination-wrapper">
        {{ $users->links() }}
    </div>
</div>

<script>
    document.querySelectorAll('.selectStudentBtn').forEach(button => {
        button.addEventListener('click', function () {
            const userId = this.dataset.userId;
            const modal = document.querySelector(`.studentSelectionForm[data-user-id="${userId}"]`);
            if (modal) modal.style.display = 'block';
        });
    });

    document.querySelectorAll('.cancelSelectionBtn').forEach(button => {
        button.addEventListener('click', function () {
            const userId = this.dataset.userId;
            const modal = document.querySelector(`.studentSelectionForm[data-user-id="${userId}"]`);
            if (modal) modal.style.display = 'none';
        });
    });

    document.querySelectorAll('.closeStudentSelectionBtn').forEach(button => {
        button.addEventListener('click', function () {
            const userId = this.dataset.userId;
            const modal = document.querySelector(`.studentSelectionForm[data-user-id="${userId}"]`);
            if (modal) modal.style.display = 'none';
        });
    });
</script>

@endsection
