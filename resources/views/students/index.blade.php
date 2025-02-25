@extends('layouts.app')
@section('title', 'Student List')
@section('content')
<div class="container">
    <h2>Student List</h2>
    @if ($students->isEmpty())
        <p>You have no student(s) yet...</p>
    @else
        <div class="filter-container mb-3">
            <form method="GET" action="{{ route('students.index') }}" class="form-inline">
                <input type="text" name="name" id="name_filter" class="form-control mr-2" placeholder="Student Name" value="{{ request('name') }}">

                <select name="belt_id" id="belt_id" class="form-control mr-2">
                    <option value="">All Belts</option>
                    @foreach($belts as $belt)
                        <option value="{{ $belt->BeltID }}" {{ request('belt_id') == $belt->BeltID ? 'selected' : '' }}>
                            {{ $belt->BeltName }} ({{ $belt->BeltLevel }})
                        </option>
                    @endforeach
                </select>

                <select name="cv_id" id="cv_id" class="form-control mr-2">
                    <option value="">All Places</option>
                    @foreach($classVenue as $classVenues)
                        <option value="{{ $classVenues->cv_id }}" {{ request('cv_id') == $classVenues->cv_id? 'selected' : '' }}>
                            {{ $classVenues->cv_name }}
                        </option>
                    @endforeach
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
                        <th>Photo</th>
                        <th>Name</th>
                        <th>IC Number</th>
                        <th>HP Number</th>
                        <th>Fee</th>
                        <th>Grade</th>
                        <th>Centre</th>
                        <th>Class</th>
                        <th>Class Venue</th>
                        <th>Start Date</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($students as $index => $student)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>
                                <div class="btn-group" role="group" aria-label="Actions">
                                    <form action="{{ route('students.showProfile', $student->student_id)}}" method="GET">
                                        @csrf
                                        <button type="submit" class="btn btn-primary btn-sm">View Profile</button>
                                    </form>
                                </div>
                            </td>
                            <td>
                                @if($student->profile_picture)
                                    <img src="data:image/jpeg;base64,{{ base64_encode($student->profile_picture) }}" alt="{{ $student->name }}" class="profile-picture" style="height: 150px !important; width: 150px !important; object-fit: cover;">
                                @else
                                    <svg xmlns="http://www.w3.org/2000/svg" width="150" height="150" viewBox="0 0 50 50" class="profile-picture">
                                        <circle cx="25" cy="25" r="25" fill="#ccc" />
                                        <text x="25" y="30" font-size="18" text-anchor="middle" fill="#555">?</text>
                                    </svg>
                                @endif
                            </td>
                            <td>{{ $student->name }}</td>
                            <td>
                            @if ($student->ic_number)
                                {{ $student->ic_number }}
                            @else
                                <span>N/A</span>
                            @endif
                            </td>
                            <td>
                                @foreach ($student->phone as $phone)
                                    <!-- Make the phone number clickable -->
                                    <a href="javascript:void(0)" class="phone-number-link" data-phone="{{$phone->country_code}}{{ $phone->phone_number }}" data-person="{{ $phone->phone_person }}">
                                        {{ $phone->phone_person }}: {{ $phone->phone_number }}
                                    </a>
                                    <br>
                                @endforeach
                            </td>
                            <td>RM{{ $student->fee !== null ? $student->fee : 'Not Assigned' }}</td>
                            <td>
                                {{ $student->belt->BeltName }} ({{ $student->belt->BeltLevel }})
                                <img src="data:image/jpeg;base64,{{ base64_encode($student->belt->BeltImg) }}" alt="{{ $student->belt->BeltName }}" class="grade" style="height: 150px; width: 50px;">
                            </td>
                            <td>
                                @if($student->centre)
                                    {{ $student->centre->centre_name }} 
                                @else
                                    No Centre Assigned
                                @endif
                            </td>
                            <td>
                                <ul>
                                    @forelse ($student->classes as $class)
                                        <li>{{ $class->class_day }} - {{ $class->class_start_time }} to {{ $class->class_end_time }}</li>
                                    @empty
                                        <li>No classes found for this student.</li>
                                    @endforelse
                                </ul>
                            </td>
                            <td>
                                <ul>
                                    @forelse ($student->classes as $class)
                                        <li>{{ $class->venue ? $class->venue->cv_name : 'No Venue Assigned' }}</li>
                                    @empty
                                        <li>No class venue found for this student</li>
                                    @endforelse
                                </ul>
                            </td>
                            <td>
                                <?php 
                                if ($student->student_startDate) {
                                    echo $student->student_startDate->format('d-M-Y');
                                } else {
                                    echo 'No date provided';
                                }
                                ?>
                            </td>
                        </tr>
                    @endforeach 
                </tbody>
            </table>
        </div>
        @if (Auth::User()->isAdmin())
            <div class="circle-button">
                <a href="{{ route('students.create') }}" class="btn btn-primary rounded-circle" 
                style="width: 60px; height: 60px; display: flex; justify-content: center; align-items: center; position: fixed; bottom: 20px; right: 20px; font-size: 24px; border-radius: 50%;">
                    +
                </a>
                <span class="tooltip-text">Add User</span> <!-- Custom tooltip text -->
            </div>
        @endif
        <br>
        <div class="pagination-wrapper">
            {{ $students->links() }}
        </div>
    @endif
</div>

<!-- Modal or Dropdown for choosing an action -->
<div id="phoneActionModal" class="modal" style="display: none;">
    <div class="modal-content">
        <p><h3>Choose an action</h3><button class="close-btn">&times;</button></p>
        <button id="callBtn">Call</button>
        <button id="whatsappBtn">WhatsApp</button>
    </di>
</div>

@endsection