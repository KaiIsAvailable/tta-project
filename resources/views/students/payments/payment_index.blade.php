@extends('layouts.app')

@section('content')
<div class="container">
    <h2 style="display: inline;">Payments List</h2>
    <!--Filter form-->
    <form method="GET" action="{{ route('payments.index') }}" class="form-inline mb-4">
        <!-- Filter by Student Name -->
        <input type="text" name="name" id="name_filter" class="form-control mr-2" placeholder="Student Name" value="{{ request('name') }}">

        <!-- Filter by Payment Status -->
        <select name="payment_status" id="payment_status" class="form-control mr-2">
            <option value="">Payment Status</option>
            <option value="Paid" {{ request('payment_status') == 'Paid' ? 'selected' : '' }}>Paid</option>
            <option value="Unpaid" {{ request('payment_status') == 'Unpaid' ? 'selected' : '' }}>Unpaid</option>
            <option value="Voided" {{ request('payment_status') == 'Voided' ? 'selected' : '' }}>Voided</option>
        </select>

        <!-- Filter by Paid For Month -->
        <input type="month" name="paid_for" id="paid_for" class="form-control mr-2" value="{{ request('paid_for') }}">

        <!-- Submit Button -->
        <button type="submit" class="btn btn-primary">Filter</button>
    </form>

    <!--Payment Table-->
    <div class="table-responsive">
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Option</th>
                    <th>Status</th>
                    <th>Payment Number</th>
                    <th>Student</th>
                    <th>Paid For</th>
                    <th>Amount</th>
                    <th>Paid Amount</th>
                    <th>Outstanding</th>
                    <th>Pre Payment</th>
                    <th>Payment Made Date</th>
                    <th>Method</th>
                    
                </tr>
            </thead>
            <tbody>
                @foreach($payments as $payment)
                    <tr>
                        <td>
                            <div style="display: flex; gap: 10px;">
                                @if ($payment->payment_status == 'Unpaid')
                                    <form action="{{ route('payments.edit', ['payment' => $payment->payment_id]) }}" method="GET">
                                        @csrf
                                        <button type="submit" class="btn btn-primary">Pay</button>
                                    </form>
                                @endif
                                @if ($payment->payment_status == 'Paid')
                                    <a href="{{ route('receipt.show', ['paymentId' => $payment->payment_id]) }}" title="Print Receipt">
                                        <button type="submit" class="btn btn-primary btn-sm">Print Receipt</button>
                                    </a>
                                @endif
                                <a href="{{ route('invoice.show', ['paymentId' => $payment->payment_id]) }}" title="Print Receipt">
                                    <button type="submit" class="btn btn-primary btn-sm">Print Invoice</button>
                                </a>
                                <form action="{{ route('payments.void', ['payment' => $payment->payment_id]) }}" method="POST" style="display: inline;">
                                    @csrf
                                    <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to void this payment?');">Void</button>
                                </form>
                            </div>
                        </td>
                        <td>{{ $payment->payment_status }}</td>
                        <td>{{ 'P' . sprintf('%05d', $payment->payment_id) }}</td>
                        <td>{{ $payment->student?->name ?? 'N/A' }}</td> <!-- Assuming you have a relation between Payment and Student -->
                        <td>{{ $payment->paid_for->format('F Y') }}</td>
                        <td>RM{{ number_format($payment->payment_amount, 2) }}</td>
                        <td>RM{{ number_format($payment->payment_payAmt, 2) }}</td>
                        <td>RM{{ number_format($payment->payment_outstanding, 2) }}</td>
                        <td>RM{{ number_format($payment->payment_preAmt, 2) }}</td>
                        <td>
                            @if ($payment->payment_date)
                                {{ $payment->payment_date->format('d-M-Y') }}</td>
                            @else
                                <span>N/A</span>
                            @endif
                        </td>
                        <td>{{ $payment->payment_method }}</td>  
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="pagination-link">
        {{ $payments->links() }}
    </div>
</div>
@endsection
