@extends('layouts.app')
@section('content')

<div class="form_container">
    <h2>Edit Class</h2>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('students.class.update', $class->class_id) }}" method="POST" class="forms">
        @csrf
        @method('PUT')

        <div>
            <label for="class_day">Class Day:</label>
            <select name="class_day" id="class_day" class="form-control @error('class_day') is-invalid @enderror" required>
                <option value="" disabled {{ old('class_day', $class->class_day) ? '' : 'selected' }}>-- Select a Day --</option>
                <option value="Monday" {{ old('class_day', $class->class_day) == 'Monday' ? 'selected' : '' }}>Monday</option>
                <option value="Tuesday" {{ old('class_day', $class->class_day) == 'Tuesday' ? 'selected' : '' }}>Tuesday</option>
                <option value="Wednesday" {{ old('class_day', $class->class_day) == 'Wednesday' ? 'selected' : '' }}>Wednesday</option>
                <option value="Thursday" {{ old('class_day', $class->class_day) == 'Thursday' ? 'selected' : '' }}>Thursday</option>
                <option value="Friday" {{ old('class_day', $class->class_day) == 'Friday' ? 'selected' : '' }}>Friday</option>
                <option value="Saturday" {{ old('class_day', $class->class_day) == 'Saturday' ? 'selected' : '' }}>Saturday</option>
                <option value="Sunday" {{ old('class_day', $class->class_day) == 'Sunday' ? 'selected' : '' }}>Sunday</option>
            </select>
            @error('class_day')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div>
            <label for="class_start_time">Start Time</label>
            <input type="time" name="class_start_time" id="class_start_time" class="form-control" value="{{ old('class_start_time', $class->class_start_time) }}" required>
            @error('class_start_time')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div>
            <label for="class_end_time">End Time</label>
            <input type="time" name="class_end_time" id="class_end_time" class="form-control" value="{{ old('class_end_time', $class->class_end_time) }}" required>
            @error('class_end_time')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div>
            <label for="class_price">Price</label>
            <input type="number" name="class_price" id="class_price" class="form-control" value="{{ old('class_price', $class->class_price) }}" step="0.01" required>
            @error('class_price')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div>
            <label for="class_venue">Venue</label>
            <select name="cv_id" id="class_venue" class="form-control @error('cv_id') is-invalid @enderror" required>
                <option value="" disabled>-- Select a Venue --</option>
                @foreach ($venues as $venue)
                    <option value="{{ $venue->cv_id }}" 
                        {{ old('cv_id', $class->cv_id) == $venue->cv_id ? 'selected' : '' }}>
                        {{ $venue->cv_name }} - {{ $venue->cv_state }}
                    </option>
                @endforeach
            </select>
            @error('cv_id')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <label for="instructors">Select Instructors:</label>
            <select class="form-control" name="instructor_ids[]" multiple required id="classSelect">
                @foreach ($instructors as $instructor)
                    <option value="{{ $instructor->id }}" 
                        {{ in_array($instructor->id, old('instructor_ids', $assignedInstructors)) ? 'selected' : '' }}>
                        {{ $instructor->name }}
                    </option>
                @endforeach
            </select>
            @error('instructors')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <button type="submit" class="btn btn-primary">Update Class</button>
        <a href="{{ route('students.class.class_index') }}" class="btn btn-secondary">Cancel</a>
    </form>
</div>
@endsection
