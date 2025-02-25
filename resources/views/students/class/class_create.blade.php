@extends('layouts.app')
@section('content')

<div class="form_container mt-4">
    <h2>Create a New Class</h2>

    <form action="{{ route('students.class.class_store') }}" method="POST" class="forms">
        @csrf <!-- Laravel CSRF protection -->

        <div>
            <label for="class_day">Class Day:</label>
            <select name="class_day" id="class_day" class="form-control @error('class_day') is-invalid @enderror" required>
                <option value="" disabled selected>-- Select a Day --</option>
                <option value="Monday" {{ old('class_day') == 'Monday' ? 'selected' : '' }}>Monday</option>
                <option value="Tuesday" {{ old('class_day') == 'Tuesday' ? 'selected' : '' }}>Tuesday</option>
                <option value="Wednesday" {{ old('class_day') == 'Wednesday' ? 'selected' : '' }}>Wednesday</option>
                <option value="Thursday" {{ old('class_day') == 'Thursday' ? 'selected' : '' }}>Thursday</option>
                <option value="Friday" {{ old('class_day') == 'Friday' ? 'selected' : '' }}>Friday</option>
                <option value="Saturday" {{ old('class_day') == 'Saturday' ? 'selected' : '' }}>Saturday</option>
                <option value="Sunday" {{ old('class_day') == 'Sunday' ? 'selected' : '' }}>Sunday</option>
            </select>
            @error('class_day')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div>
            <label for="class_start_time">Class Start Time:</label>
            <input type="time" name="class_start_time" id="class_start_time" class="form-control @error('class_time') is-invalid @enderror" value="{{ old('class_start_time') }}" required>
            @error('class_start_time')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div>
            <label for="class_end_time">Class End Time:</label>
            <input type="time" name="class_end_time" id="class_end_time" class="form-control @error('class_end_time') is-invalid @enderror" value="{{ old('class_end_time') }}" required>
            @error('class_end_time')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div>
            <label for="class_price">Class Price:</label>
            <input type="number" name="class_price" id="class_price" class="form-control @error('class_price') is-invalid @enderror" value="{{ old('class_price') }}" step="0.01" required>
            @error('class_price')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div>
            <label for="class_venue">Venue</label>
            <select name="cv_id" id="class_venue" class="form-control @error('class_venue') is-invalid @enderror" value="{{ old('class_venue') }}"  required>
                <option value="" disabled selected></option>
                @foreach ($venues as $venue)
                    <option value="{{ $venue->cv_id }}">
                        {{ $venue->cv_name }} - {{ $venue->cv_state }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="form-group">
            <label for="instructors">Select Instructors:</label>
            <select name="instructor_ids[]" class="form-control" multiple required id="classSelect">
                @foreach ($instructors as $instructor)
                    <option value="{{ $instructor->id }}" 
                        {{ in_array($instructor->id, old('instructor_ids', [])) ? 'selected' : '' }}>
                        {{ $instructor->name }}
                    </option>
                @endforeach
            </select>
            @error('instructor_ids')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <button type="submit" class="btn btn-primary">Create Class</button>
        <a href="{{ route('students.class.class_index') }}" class="btn btn-secondary">Cancel</a>
    </form>
</div>

@endsection
