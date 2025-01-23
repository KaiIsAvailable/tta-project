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

    <form action="{{ route('payments.update', ['payment' => $payment->payment_id]) }}" method="POST" class="forms">
        @csrf
        @method('PUT') <!-- Since we're updating, we use PUT -->

        <!-- Hidden input to pass the student_id if needed -->
        <input type="hidden" name="student_id" value="{{ $payment->student_id }}">

        <div class="form-group">
            <label for="name">Name:</label>
            <p>{{ $student->name }}</p>
        </div>

        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Description</th>
                    <th>Amount (RM)</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Fee Amount</td>
                    <td>
                        {{ number_format($payment->payment_amount, 2) }}
                        <input type="hidden" name="payment_amount" id="payment_amount" value="{{ $payment->payment_amount }}" readonly>
                    </td>
                </tr>
                <tr>
                    <td>{{ $previousMonth->format('F Y') }} Outstanding</td>
                    <td>
                        {{ number_format($previousOutstanding, 2) }}
                        <input type="hidden" name="payment_outstanding" id="payment_outstanding" value="{{ $previousOutstanding }}" readonly>
                    </td>
                </tr>
                <tr>
                    <td>{{ $previousMonth->format('F Y') }} Pre Payment</td>
                    <td>
                        {{ number_format($previousPrePayment, 2) }}
                        <input type="hidden" name="payment_preAmt" id="payment_preAmt" value="{{ $previousPrePayment }}" readonly>
                    </td>
                </tr>
                <tr>
                    <th>Net Total</th>
                    <?php $total = 0; ?>
                    <td>
                        @if ($previousOutstanding > 0)
                            <?php $total = $payment->payment_amount + $previousOutstanding?>
                            {{ number_format($total, 2) }}
                            <input type="hidden" name="total" id="total" value="{{ $total }}">
                        @else
                            @if ($previousPrePayment > 0)
                                <?php $total = $payment->payment_amount - $previousPrePayment?>
                            @else
                                <?php $total = $payment->payment_amount + $previousPrePayment?>
                            @endif
                            {{ number_format($total, 2) }}
                            <input type="hidden" name="total" id="total" value="{{ $total }}">
                        @endif
                    </td>
                </tr>
            </tbody>
        </table>

        <div class="form-group">
            <label for="payAmt">Pay Amount:</label>
            <input type="number" name="payAmt" id="payAmt" class="form-control" value="{{ $payment->payment_payAmt }}" required step="0.01">
        </div>

        <div class="form-group">
            <label for="payment_method">Payment Method:</label>
            <select name="payment_method" id="payment_method" class="form-control" required>
                <option value="Cash" {{ $payment->payment_method == 'Cash' ? 'selected' : '' }}>Cash</option>
                <option value="Bank Transfer" {{ $payment->payment_method == 'Bank Transfer' ? 'selected' : '' }}>Bank Transfer</option>
                <option value="Online Payment" {{ $payment->payment_method == 'Online Payment' ? 'selected' : '' }}>Online Payment</option>
                <option value="Pre Payment" {{ $payment->payment_method == 'Pre Payment' ? 'selected' : '' }}>Pre Payment</option>
            </select>
        </div>

        <div class="form-group">
            <label for="paid_date">Payment Date:</label>
            <input type="date" name="paid_date" id="paid_date" class="form-control" value="{{ $payment->payment_date }}" required>
        </div>

        <div class="form-group">
            <label for="paid_for">Paid For (Month/Year):</label>
            <input type="month" name="paid_for" id="paid_for" class="form-control" value="{{ $payment->paid_for->format('Y-m') }}" required>
        </div>

        <button type="submit" class="btn btn-primary">Pay</button>
        <a href="javascript:history.back()" class="btn btn-secondary">
            Cancel
        </a>
    </form>
</div>

@endsection
