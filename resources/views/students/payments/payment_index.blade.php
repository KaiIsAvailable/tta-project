@extends('layouts.app')

@section('content')
<div class="container">
    <h2 style="display: inline;">Payments List</h2>
    <!--<form action="{{ route('payments.create') }}" method="GET" style="display: inline;">
        @csrf
        @method('GET')
        <button type="submit" class="btn btn-danger btn-sm">Add Payment</button>
    </form>-->
    <div class="table-responsive">
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Action</th>
                    <th>Status</th>
                    <th>Payment Number</th>
                    <th>Student</th>
                    <th>Payment Made Date</th>
                    <th>Amount</th>
                    <th>Method</th>
                    <th>Paid For</th>
                </tr>
            </thead>
            <tbody>
                @foreach($payments as $payment)
                    <tr>
                        <td></td>
                        <td>{{ $payment->payment_status }}</td>
                        <td>{{ 'P' . sprintf('%05d', $payment->payment_id) }}</td>
                        <td>{{ $payment->student?->name ?? 'N/A' }}</td> <!-- Assuming you have a relation between Payment and Student -->
                        <td>{{ $payment->payment_date->format('d-M-Y') }}</td>
                        <td>{{ $payment->payment_amount }}</td>
                        <td>{{ $payment->payment_method }}</td>
                        <td>{{ $payment->paid_for->format('F Y') }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
