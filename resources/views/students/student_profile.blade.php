@extends('layouts.app')

@section('content')
<div class="container">
    <h1 class="text-left mb-4" style="display: inline;">{{ $students->name }}'s Profile</h1>
    <a href="{{ route('students.edit', $students->student_id) }}" class="btn btn-primary btn-sm">Edit</a>
    <form action="{{ route('students.destroy', $students->student_id) }}" method="POST" style="display: inline;">
        @csrf
        @method('DELETE')
        <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this student?');">Delete</button>
    </form>
    <div class="profile-container">
        <!-- Left Column (Profile Picture & Name) -->
        <div class="profile-left">
            <!-- Profile Picture -->
            <div class="card mb-3">
                <div class="card-body">
                    <p>Student ID:{{ 'S' . sprintf('%05d',$students->student_id)}}</p>
                    @if($students->profile_picture)
                        <img src="{{ asset('storage/' . $students->profile_picture) }}" alt="{{ $students->name }}" class="profile-pictures img-fluid rounded-circle">
                    @else
                        <svg xmlns="http://www.w3.org/2000/svg" width="150" height="150" viewBox="0 0 50 50" style="border-radius: 50%;">
                            <circle cx="25" cy="25" r="25" fill="#ccc" />
                            <text x="25" y="30" font-size="18" text-anchor="middle" fill="#555">?</text>
                        </svg>
                    @endif
                    <h3>{{ $students->name }}</h3>
                    <p>{{ $students->ic_number }}</p>
                </div>
            </div>
        </div>

        <!-- Right Column (Student Info) -->
        <div class="profile-right">
            <!-- Contact Information -->
            <div class="card mb-3">
                <div class="card-body">
                    <h5>Contact Information:</h5>
                    @if($students->phone->isEmpty())
                        <p>No phone numbers available.</p>
                    @else
                        @foreach ($students->phone as $phone)
                            <p>
                                <strong>{{ $phone->phone_person }}:</strong>
                                <a href="javascript:void(0)" class="phone-number-link" data-phone="{{$phone->country_code}}{{ $phone->phone_number }}" data-person="{{ $phone->phone_person }}">
                                    {{ $phone->phone_number }}
                                </a>
                            </p>
                        @endforeach
                    @endif
                </div>
            </div>

            <!-- Additional Profile Information -->
            <div class="card mb-3">
                <div class="card-body">
                    <h5>Additional Information:</h5>
                    
                    <p><strong>Fee:</strong> RM{{ $students->fee ?? 'Not Assigned' }}</p>
                    <p><strong>Belt:</strong> {{ $students->belt ? $students->belt->BeltName : 'No belt assigned' }}</p>
                    <p><strong>Centre:</strong> {{ $students->centre ? $students->centre->centre_name : 'No Centre Assigned' }}</p>
                    </br>
                    <h5>Class Schedule:</h5>
                    @if($students->classes->isEmpty())
                        <p>No classes found for this student.</p>
                    @else
                        <ul>
                            @foreach($students->classes as $class)
                                <li>{{ $class->class_day }} - {{ $class->class_start_time }} to {{ $class->class_end_time }}</li>
                            @endforeach
                        </ul>
                    @endif
                    <br>
                    <h5>Payment Setting:</h5>
                    @if(is_null($students->student_startDate))
                        <p>No date found for this student.</p>
                    @else
                        <p><strong>Start Date:</strong> {{ $students->student_startDate->format('d-M-Y') }}</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
    <div class="payment-container">
        <h1 class="text-left mb-4" style="display: inline;">{{ $students->name }}'s Payment</h1>
        <form action="{{ route('students.stillInProgress') }}" method="GET" style="display: inline;">
            <input type="hidden" name="student_id" value="{{ $students->student_id }}">
            <button type="submit" class="btn btn-danger btn-sm">Add Payment</button>
        </form>
        <div class="card mb-3">
        <div class="card-body">
            <h5>Payment History:</h5>
            @if($students->payments->isEmpty())
                <p>No payments found for this student.</p>
            @else
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Action</th>
                            <th>Status</th>
                            <th>Payment Number</th>
                            <th>Payment Made Date</th>
                            <th>Paid For</th>
                            <th>Amount</th>
                            <th>Method</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($students->payments as $payment)
                            <tr>
                                <td></td>
                                <td>{{ $payment->payment_status }}</td>
                                <td>{{ 'P' . sprintf('%05d', $payment->payment_id) }}</td>
                                <td>{{ $payment->payment_date->format('d-m-Y') }}</td>
                                <td>{{ $payment->paid_for->format('F Y') }}</td>
                                <td>RM{{ number_format($payment->payment_amount, 2) }}</td>
                                <td>{{ $payment->payment_method }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @endif
        </div>
    </div>
</div>
<div id="phoneActionModal" class="modal" style="display: none;">
    <div class="modal-content">
        <h3>Choose an action</h3>
        <button class="close-btn">&times;</button>
        <button id="callBtn" class="close">Call</button>
        <button id="whatsappBtn" class="close">WhatsApp</button>
    </div>
</div>
@endsection
