@extends('layouts.app')
@section('title', 'Edit Student')
@section('content')
<div class="form_container">
    <h2>Edit Student</h2>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                  <li>{{ $error }}</li> <!-- Uncommented to display errors -->
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('students.update', $student->student_id) }}" method="POST" enctype="multipart/form-data" class="forms">
        @csrf
        @method('PUT')

        <div class="form-group">
            <label for="name">Name:</label>
            <input type="text" name="name" class="form-control" value="{{ old('name', $student->name) }}" required>
            @error('name')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <label for="ic_number">IC Number:</label>
            <input type="text" name="ic_number" class="form-control" value="{{ old('ic_number', $student->ic_number) }}" required>
            @error('ic_number')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div id="phoneNumbers">
            @foreach($student->phone as $index => $phone)
                <div class="phone-number-group">
                    <input type="hidden" name="phone_ids[]" value="{{ $phone->phone_id }}">
                    <label for="hp_numbers">Contact Number:</label>
                    <select name="country_codes[]" class="form-control @error('country_codes.*') is-invalid @enderror">
                        <option value="+60" selected>+60</option>
                    </select>
                    <input type="text" name="hp_numbers[]" class="form-control @error('hp_numbers.*') is-invalid @enderror" value="{{ old('hp_numbers.' . $index, $phone->phone_number) }}" required>
                    @error('hp_numbers.' . $index)
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror

                    <label for="phone_persons">Contact Name:</label>
                    <input type="text" name="phone_persons[]" class="form-control mt-2 @error('phone_persons.*') is-invalid @enderror" value="{{ old('phone_persons.' . $index, $phone->phone_person) }}" required>
                    @error('phone_persons.' . $index)
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror

                    <!-- Remove button with data-id for JavaScript -->
                    <button type="button" class="btn btn-danger mt-2 remove-phone-btn" data-url="{{ route('phones.destroy', $phone->phone_id) }}">Remove</button>
                </div>
            @endforeach
        </div>

        <button type="button" class="btn btn-secondary" data-bs-toggle="modal" data-bs-target="#addPhoneModal">
            Add another phone number
        </button>

        <div class="form-group">
            <label for="profile_picture">Profile Picture:</label>
            @if ($student->profile_picture)
                <div class="mb-2">
                    <img src="{{ asset('storage/' . $student->profile_picture) }}" alt="Current Profile Picture" style="width:100px; height:100px;">
                </div>
            @endif
            <input type="file" name="profile_picture" class="form-control">
        </div>

        <div class="form-group">
            <label for="belt_id">Belt:</label>
            <select name="belt_id" class="form-control" required>
                @foreach ($belts as $belt)
                    <option value="{{ $belt->BeltID }}" {{ (old('belt_id', $student->belt_id) == $belt->BeltID) ? 'selected' : '' }}>
                        {{ $belt->BeltName}} ({{ $belt->BeltLevel }})
                    </option>
                @endforeach
            </select>
            @error('belt_id')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <label for="centre_id">Centre:</label>
            <select name="centre_id" class="form-control" required>
                @foreach ($centres as $centre)
                    <option value="{{ $centre->centre_id }}" {{ (old('centre_id', $student->centre_id) == $centre->centre_id) ? 'selected' : '' }}>
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
                    <option value="{{ $class->class_id }}"
                        {{ (in_array($class->class_id, old('class_id', $student->classes->pluck('class_id')->toArray()))) ? 'selected' : '' }}>
                        {{ $class->class_day }} ({{ $class->class_start_time }} to {{ $class->class_end_time }}) 
                    </option>
                @endforeach
            </select>
            @error('class_id')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <label for="payment_amount">Amount</label>
            <input type="number" name="payment_amount" id="payment_amount" class="form-control" 
                value="{{ old('payment_amount', $student->fee) }}" required>
            @error('payment_amount')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <label for="startDate">Payment Start Date</label>
            <input type="date" name="startDate" id="startDate" class="form-control" 
                value="{{ old('startDate', $student->student_startDate->format('Y-m-d')) }}" required>
            @error('startDate')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <button type="submit" class="btn btn-primary">Update Student</button>
        <a href="{{ route('students.showProfile', $student->student_id) }}" class="btn btn-secondary">Cancel</a>
    </form>
</div>
<!-- Add Phone Modal -->
<div class="modal fade" id="addPhoneModal" tabindex="-1" aria-labelledby="addPhoneModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">&times;</button>
                <h5 class="modal-title" id="addPhoneModalLabel">Add Phone Number</h5>
            </div>
            <div class="modal-body">
                <!-- Add Phone Form -->
                <form action="{{ route('phones.store') }}" method="POST">
                    @csrf
                    <input type="hidden" name="students_id" value="{{ $student->student_id }}">

                    <label for="phone_number">Phone Number:</label>
                    <select name="country_codes" class="form-control @error('country_codes') is-invalid @enderror">
                        <option value="+60" selected>+60</option>
                    </select>
                    <br>

                    <input type="text" id="phone_number" name="phone_number" required>
                    <br>
                    
                    <label for="phone_person">Phone Person:</label>
                    <input type="text" id="phone_person" name="phone_person" required>
                    <br>
                    
                    <button type="submit" class="btn btn-primary">Add Phone</button>
                </form>
            </div>
        </div>
    </div>
</div>
<script>
    document.querySelectorAll('.remove-phone-btn').forEach(button => {
        button.addEventListener('click', function () {
            const deleteUrl = this.getAttribute('data-url'); // Get the URL for deletion

            if (confirm('Are you sure you want to remove this phone number?')) {
                // Send the DELETE request
                fetch(deleteUrl, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Content-Type': 'application/json',
                    }
                })
                .then(response => {
                    if (response.ok) {
                        // Remove the corresponding group div
                        this.closest('.phone-number-group').remove();
                        alert('Phone number removed successfully.');
                    } else {
                        console.error('Failed to remove phone number. Status:', response.status);
                        alert('Failed to remove phone number. Please try again.');
                    }
                })
                .catch(error => console.error('Error:', error));
            }
        });
    });
</script>
@endsection
