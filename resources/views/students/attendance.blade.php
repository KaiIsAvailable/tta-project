@extends('layouts.app')
@section('title', 'Edit Attendance')
@section('content')

<div class="container">
    <h2>Take Attendance For: <br> {{ $selectedDay }} ({{ $date }})</h2>
    <!-- Filter form -->
    <div class="filter-container mb-3">
        <form action="{{ route('students.attendance.filter') }}" method="POST" class="form-inline">
            @csrf
            <input type="date" name="filter[date]" class="form-control mr-2" value="{{ old('filter.date', $date) }}">
            <select name="filter[cv_id]" class="form-control mr-2">
            <option value="">Select Place</option>
                @foreach ($classVenue as $venue)
                    <option value="{{ $venue->cv_id }}" {{ request('filter.cv_id') == $venue->cv_id ? 'selected' : '' }}>
                        {{ $venue->cv_name }}
                    </option>
                @endforeach
            </select>
            <button type="submit" class="btn btn-primary">Filter</button>
        </form>
    </div>
    @if($students->isEmpty())
        <p>No student attending class for: {{ $selectedDay }} ({{ $date }})</p>
    @else

        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        <!-- Attendance form -->
        <form action="{{ route('students.updateAttendance') }}" method="POST">
            @csrf
            <input type="hidden" name="date" value="{{ $date }}">
            <div class="table-responsive">
                @foreach ($students as $index => $student)
                    @foreach ($student->classes as $class)
                        @if (strtolower($class->class_day) === strtolower($selectedDay))
                            <strong>{{ $class->class_day }} ({{ $class->class_start_time }} to {{ $class->class_end_time }})</strong><br>
                            @if ($class->venue)
                                {{ $class->venue->cv_name }} - {{ $class->venue->cv_state }}
                            @else
                                <span>No venue information</span>
                            @endif
                            
                            @php
                                $classTime = $class->class_day . '(' . $class->class_start_time . ' to ' . $class->class_end_time . ')';
                            @endphp
                            
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th style="width: 160px;">Student</th>
                                        <th>Status</th>
                                        <th>Reason</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $attendanceRecord = $attendanceRecords->firstWhere('student_id', $student->student_id);
                                    @endphp

                                    @foreach ($student->classes as $class)
                                        <!--@if ($classTime === ($class->class_day . '(' . $class->class_start_time . ' to ' . $class->class_end_time . ')'))-->
                                        <tr>
                                            <td>{{ $index + 1 }}</td>
                                            <td>
                                                {{ $student->name }}
                                                @if($student->profile_picture)
                                                    <img src="data:image/jpeg;base64,{{ base64_encode($student->profile_picture) }}" 
                                                        alt="{{ $student->name }}" height="150px" width="150px" 
                                                        class="profile-pictures img-fluid">
                                                @else
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="100" height="100" viewBox="0 0 50 50">
                                                        <circle cx="25" cy="25" r="25" fill="#ccc" />
                                                        <text x="25" y="30" font-size="18" text-anchor="middle" fill="#555">?</text>
                                                    </svg>
                                                @endif
                                            </td>
                                            <td>
                                                <label>
                                                    <input type="checkbox" name="attendance[{{ $student->student_id }}][present]" value="1"
                                                        {{ $attendanceRecord?->status === 'present' ? 'checked' : '' }}
                                                        onchange="toggleReasonInput(this, '{{ $student->student_id }}')">
                                                    Present
                                                </label>
                                            </td>
                                            <td>
                                                <input type="text" name="attendance[{{ $student->student_id }}][reason]" 
                                                    class="reason-input" 
                                                    value="{{ old('attendance.'.$student->student_id.'.reason', $attendanceRecord ? $attendanceRecord->reason : '') }}" 
                                                    placeholder="Reason for absence" 
                                                    {{ $attendanceRecord?->status === 'present' ? 'disabled' : '' }} />
                                            </td>
                                        </tr>
                                        <!--@endif-->
                                    @endforeach
                                </tbody>
                            </table>
                            <br>
                        @endif
                    @endforeach
                @endforeach
            </div>
            <button type="submit" class="btn btn-primary">Submit Attendance</button>
        </form>
    @endif
</div>
<script>
    function toggleReasonInput(checkbox, studentId) {
        // Find the reason input for the student
        const reasonInput = document.querySelector(`input[name="attendance[${studentId}][reason]"]`);
        // Enable/disable the reason input based on the checkbox state
        reasonInput.disabled = checkbox.checked;
    }
</script>
@endsection