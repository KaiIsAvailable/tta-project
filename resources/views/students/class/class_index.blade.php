@extends('layouts.app')
@section('content')
<div class="container">
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
                        <th>Action</th>
                        <th>Class Day</th>
                        <th>Class Time</th>
                        <th>Class Price</th>
                        <th>Class Venue</th>
                        <th>Class State</th>
                        <th>Instructor</th>
                        <th>Location Link</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($class as $index => $classes)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>
                                <div class="btn-group" role="group" aria-label="Actions">
                                    <form action="{{ route('students.class.class_edit', $classes->class_id) }}" method="GET" style="display: inline;">
                                        @csrf
                                        @method('GET')
                                        <button type="submit" class="btn btn-primary btn-sm">Edit</button>
                                    </form>
                                    <form action="{{ route('students.class.class_destroy', $classes->class_id) }}" method="POST" style="display: inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this student?');">Delete</button>
                                    </form>
                                </div>
                            </td>
                            <td>{{ $classes->class_day }}</td>
                            <td>{{ $classes->class_start_time }} to {{ $classes->class_end_time }}</td>
                            <td>{{ $classes->class_price}}</td>
                            <td>{{ $classes->venue->cv_name}}</td>
                            <td>{{ $classes->venue->cv_state}}</td>
                            <td>
                                @foreach($classes->instructors as $instructor)
                                    {{ $instructor->name }}<br>
                                @endforeach
                            </td>
                            <td><a href="{{ $classes->venue->cv_location_link}}" target="_blank">{{ $classes->venue->cv_location_link}}</a></td>
                        </tr>
                    @endforeach 
                </tbody>
            </table>
        </div>

        <div class="circle-button">
            <a href="{{ route('students.class.class_create') }}" class="btn btn-primary rounded-circle" 
            style="width: 60px; height: 60px; display: flex; justify-content: center; align-items: center; position: fixed; bottom: 20px; right: 20px; font-size: 24px; border-radius: 50%;">
                +
            </a>
            <span class="tooltip-text">Add Class</span> <!-- Custom tooltip text -->
        </div>
    </div>
</div>
@endsection