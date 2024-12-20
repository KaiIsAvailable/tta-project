@extends('layouts.app')

@section('content')

<div class="form_container">   
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    <form action="{{ route('payments.store') }}" method="POST" class="forms">
        @csrf
        <input type="hidden" name="student_id" value="{{ $student->student_id }}">

        <div class="form-group">
            <label for="payment_amount">Name:</label>
            <input type="text" name="name" id="name" class="form-control" value="{{ $student->name }}" required readonly>
        </div>

        <div class="form-group">
            <label for="payment_amount">Payment Amount:</label>
            <input type="number" name="payment_amount" id="payment_amount" class="form-control" value="{{ $student->fee }}" required>
        </div>

        <div class="form-group">
            <label for="payment_method">Payment Method:</label>
            <select name="payment_method" id="payment_method" class="form-control" required>
                <option value="Cash">Cash</option>
                <option value="Bank Transfer">Bank Transfer</option>
                <option value="Online Payment">Online Payment</option>
            </select>
        </div>

        <div class="form-group">
            <label for="paid_for">Paid For (Month/Year):</label>
            <input type="month" name="paid_for" id="paid_for" class="form-control" value="{{ now()->format('Y-m') }}" required>
        </div>

        <button type="submit" class="btn btn-primary">Submit Payment</button>
        <a href="{{ route('students.showProfile', $student->student_id) }}" class="btn btn-secondary">Cancel</a>
    </form>
</div>
@endsection
