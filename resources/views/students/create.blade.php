@extends('layouts.app')
@section('title', 'Student List')
@section('content')
<div class="form_container">
    <h2>Add Student</h2>

    <form action="{{ route('students.store') }}" method="POST" enctype="multipart/form-data" class="forms">
        @csrf
        <div>
            <label for="name">Name:</label>
            <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" required>
            @error('name')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div>
            <label for="ic_number">IC Number:</label>
            <input type="text" name="ic_number" class="form-control @error('ic_number') is-invalid @enderror" value="{{ old('ic_number') }}">
            @error('ic_number')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div id="phoneNumbers">
            <div class="phone-number-group">
                <label for="hp_numbers">Contact Numbers:</label>

                <!-- Country Code Dropdown -->
                <select name="country_codes[]" class="form-control @error('country_codes.*') is-invalid @enderror">
                    <option value="+60" selected>+60</option>
                </select>

                <input type="text" name="hp_numbers[]" class="form-control @error('hp_numbers.*') is-invalid @enderror" value="{{ old('hp_numbers.0') }}" required>
                @error('hp_numbers.*')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror

                <label for="hp_numbers">Contact Name:</label>
                <input type="text" name="phone_persons[]" class="form-control mt-2 @error('phone_persons.*') is-invalid @enderror" value="{{ old('phone_persons.0') }}" required>
                @error('phone_persons.*')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>
        <button type="button" class="btn btn-secondary add-phone-number">Add another phone number</button>

        <div>
            <label for="profile_picture">Profile Picture:</label>
            <input type="file" name="profile_picture" id="profile_picture" accept="image/*">
        </div>

        <div>
            <label for="belt_id">Belt:</label>
            <select name="belt_id" class="form-control @error('belt_id') is-invalid @enderror" required>
                <option value="" disabled selected></option>
                @foreach($belts as $belt)
                    <option value="{{ $belt->BeltID }}" {{ old('belt_id') == $belt->BeltID ? 'selected' : '' }}>
                        {{ $belt->BeltName }} ({{ $belt->BeltLevel }})
                    </option>
                @endforeach
            </select>
            @error('belt_id')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div>
            <label for="centre_id">Centre:</label>
            <select name="centre_id" class="form-control @error('centre_id') is-invalid @enderror" required>
                <option value="" disabled selected></option>
                @foreach($centres as $centre)
                    <option value="{{ $centre->centre_id }}" {{ old('centre_id') == $centre->centre_id ? 'selected' : '' }}>
                        {{ $centre->centre_name }}
                    </option>
                @endforeach
            </select>
            @error('centre_id')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <label for="class_id">Classes:</label>
            <select name="class_id[]" class="form-control" multiple required id="classSelect">
                @foreach ($classes as $class)
                    <option value="{{ $class->class_id }}" data-price="{{ $class->class_price }}" 
                        {{ in_array($class->class_id, old('class_id', [])) ? 'selected' : '' }}>
                        {{ $class->class_day }} ({{ $class->class_start_time }} - {{ $class->class_end_time }})
                    </option>
                @endforeach
            </select>
            @error('class_id')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div>
            <label for="fee">Fee:</label>
            <input type="text" name="total_fee" id="total_fee" class="form-control" min="0" step="0.01" value="0">
            @error('fee')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div>
            <label for="startDate">Payment Start Date:</label>
            <input type="date" name="startDate" id="startDate" class="form-control" value="{{ $firstDayOfMonth }}">
            @error('startDate')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
        
        <button type="submit" class="btn btn-primary">Add Student</button>
        <a href="{{ route('students.index') }}" class="btn btn-secondary">Cancel</a>
    </form>
</div>

<script>
    // Function to calculate total fee
    function calculateTotalFee() {
        let totalFee = 0;
        // Loop through each selected option
        $('#classSelect option:selected').each(function() {
            totalFee += parseFloat($(this).data('price'));
        });
        // Update the total fee input
        $('#total_fee').val(totalFee.toFixed(2));
    }

    // Attach change event to the select box
    $('#classSelect').change(function() {
        calculateTotalFee();
    });

    // Initial calculation (in case any classes are selected by default)
    $(document).ready(function() {
        calculateTotalFee();
    });
</script>
@endsection
