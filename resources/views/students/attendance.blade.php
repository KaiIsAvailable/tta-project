@extends('layouts.app')
@section('title', 'Edit Attendance')
@section('content')

<div class="container">
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <h2>Attendance for {{ $selectedDay }} ({{ $date }})</h2>

    <!-- Filter form -->
    <div class="filter-container mb-3">
        <form action="{{ route('students.attendance.filter') }}" method="POST" class="form-inline">
            @csrf
            <input type="date" name="filter[date]" class="form-control mr-2" value="{{ old('filter.date', $date) }}">
            <select name="filter[centre_id]" class="form-control mr-2">
                <option value="">Select Centre</option>
                @foreach ($centres as $centre)
                    <option value="{{ $centre->centre_id }}" {{ request('filter.centre_id') == $centre->centre_id ? 'selected' : '' }}>
                        {{ $centre->centre_name }}
                    </option>
                @endforeach
            </select>
            <button type="submit" class="btn btn-primary">Filter</button>
        </form>
    </div>

    <!-- Attendance form -->
    <form action="{{ route('students.updateAttendance') }}" method="POST">
        @csrf
        <input type="hidden" name="date" value="{{ $date }}">
        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Picture</th>
                        <th>Name</th>
                        <th>Class</th>
                        <th>Venue</th>
                        <th>Status</th>
                        <th>Reason</th>
                    </tr>
                </thead>
                <tbody>
                    @if($students->isEmpty())
                        <tr>
                            <td colspan="6" class="text-center">No students found for the selected date and day.</td>
                        </tr>
                    @else
                        @foreach ($students as $index => $student)
                            @php
                                // Get the attendance record for the student based on the date
                                $attendanceRecord = $attendanceRecords->firstWhere('student_id', $student->student_id);
                            @endphp
                            
                            @foreach ($student->classes as $class)
                                @if (strtolower($class->class_day) === strtolower($selectedDay))
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>
                                            @if($student->profile_picture)
                                                <img src="{{ asset('storage/' . $student->profile_picture) }}" alt="{{ $student->name }}" class="profile-picture">
                                            @else
                                                <svg xmlns="http://www.w3.org/2000/svg" width="50" height="50" viewBox="0 0 50 50" style="border-radius: 50%;">
                                                    <circle cx="25" cy="25" r="25" fill="#ccc" />
                                                    <text x="25" y="30" font-size="18" text-anchor="middle" fill="#555">?</text>
                                                </svg>
                                            @endif
                                        </td>
                                        <td>{{ $student->name }}</td>
                                        <td>{{ $class->class_day }} ({{ $class->class_start_time }} to {{$class->class_end_time}})</td>
                                        <td>
                                        @if ($class->venue) 
                                            {{ $class->venue->cv_name }} - {{ $class->venue->cv_state}}
                                        @else
                                            <span>No venue information</span>
                                        @endif
                                        </td>
                                        <td>
                                            <label>
                                                <input type="checkbox" name="attendance[{{ $student->student_id }}][present]" value="1"
                                                {{ $attendanceRecords->firstWhere('student_id', $student->student_id)?->status === 'present' ? 'checked' : '' }}
                                                onchange="toggleReasonInput(this, '{{ $student->student_id }}')">
                                                Present
                                            </label>
                                        </td>
                                        <td>
                                        <input type="text" name="attendance[{{ $student->student_id }}][reason]" 
                                            class="reason-input" 
                                            value="{{ old('attendance.'.$student->student_id.'.reason', $attendanceRecord ? $attendanceRecord->reason : '') }}" 
                                            placeholder="Reason for absence" 
                                            {{ $attendanceRecords->firstWhere('student_id', $student->student_id)?->status === 'present' ? 'disabled' : '' }} />
                                        </td>
                                    </tr>
                                @endif
                            @endforeach
                        @endforeach
                    @endif
                </tbody>
            </table>
        </div>
        <button type="submit" class="btn btn-primary">Submit Attendance</button>
    </form>
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