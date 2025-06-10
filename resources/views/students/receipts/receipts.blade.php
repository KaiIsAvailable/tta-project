@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="card border-secondary" style="width: 210mm; margin: 0 auto;">
        <div class="card-header text-center bg-light">
            <div class="d-flex align-items-center mb-4" style="gap: 20px;">
                <!-- Logo Section -->
                <div>
                    <img id="logo" src="{{ asset('image/TTA logo.jpg') }}" alt="Logo" style="width: 150px; height: auto;">
                </div>
                
                <!-- Text Section -->
                <div style="text-align: left;">
                    <h3 class="mb-0" style="font-size: 30px;"><strong>Tham's Taekwon-Do Academy</strong></h3>
                    <!--<p class="mb-0">Reg No: 201601010552</p>-->
                    <p class="mb-0">Address: No 14A, Kledang Permai 7,</p>
                    <p class="mb-0">Taman Kledang Permai, Menglembu,</p>
                    <p class="mb-0">31450 Ipoh, Perak</p>
                    <p class="mb-0">Tel: 016-560 6092</p>
                </div>
            </div>
            <h3 class="text-dark mt-3"><strong>OFFICIAL RECEIPT</strong></h3>
        </div>
        <div class="card-body" id="receipt">
            <div class="row" style="display: flex; justify-content: space-between;">
                <div class="col-md-6" style="flex: 1;">
                    <h5>Received From</h5>
                    <p><strong>Name:</strong> {{ $payment->student->name }}</p>
                </div>
                <div class="col-md-6 text-right" style="flex: 1; text-align: right;">
                    <h5>Receipt Details</h5>
                    <p><strong>Receipt ID:</strong> {{ 'P' . sprintf('%05d', $payment->payment_id) }}</p>
                    <p><strong>Date:</strong> {{ $payment->payment_date->format('d-M-Y') }}</p>
                </div>
            </div> 
            <hr>
            <h5>Paid For</h5>
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th style="width: 70%;">Description</th>
                        <th class="text-right">Amount</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $remain = 0; ?>
                    @if($previousOutstanding != 0)
                        @if($payment->payment_payAmt >= $previousOutstanding)
                            <tr>
                                <td>{{$previousMonth->format('F Y')}}'s Outstanding</td>
                                <td>RM{{number_format($previousOutstanding, 2)}}</td>
                            </tr>
                        @else
                            <tr>
                                <td>{{$previousMonth->format('F Y')}}'s Outstanding</td>
                                <td>RM{{number_format($payment->payment_payAmt, 2)}}</td>
                            </tr>
                        @endif
                    @endif
                    <?php $remain = $payment->payment_payAmt - $previousOutstanding ?>
                    @if($remain > 0)
                        <td>
                            @if (strtotime($payment->paid_for))
                                {{ $payment->paid_for->format('F Y') }}'s Fees
                            @endif
                        </td>
                        <td class="text-right">RM{{ number_format($remain, 2) }}</td>
                    @elseif($previousOutstanding == 0)
                        <td>
                            @if (strtotime($payment->paid_for))
                                {{ $payment->paid_for->format('F Y') }}'s Fees
                            @endif
                        </td>
                        <td class="text-right">RM{{ number_format($payment->payment_payAmt, 2) }}</td>
                    @elseif($payment->payment_payAmt == 0 && $payment->payment_status == "Pre Payment")
                        <td>
                            @if (strtotime($payment->paid_for))
                                {{ $payment->paid_for->format('F Y') }}'s Fees
                            @endif
                        </td>
                        <td class="text-right">RM{{ number_format($payment->payment_payAmt, 2) }}</td>
                    @endif
                    <tr>
                        <th style="text-align: right;">Total</th>
                        <td>RM{{ number_format($payment->payment_payAmt, 2) }}</td>
                    </tr>
                </tbody>
            </table>
            <hr>
            <div class="text-right">
                <div style="display: flex; justify-content: space-between; align-items: flex-start;">
                    <!-- Left (Payment Method) -->
                    <p style="flex: 1; text-align: left;">Payment Method: {{$payment->payment_method}}</p>
                    
                    <!-- Right (Sign and Image) -->
                    <div style="text-align: right;">
                        <p style="margin-right: 30px;">Sign:</p>
                        <img src="data:image/jpeg;base64,{{ base64_encode($payment_setting->pSign) }}" alt="sign" style="height:100px; width: 100px; margin-top: 5px;" loading="lazy">
                    </div>
                </div>
            </div>
            <div class="text-center">  
                <p>Thank you for your payment!</p>
            </div>
        </div>
    </div>
    <div class="card-footer text-center">
        <a href="javascript:history.back()" class="btn btn-secondary">
            Cancel
        </a>
        <button class="btn btn-primary" onclick="window.print()">Print Receipt</button>
    </div>
</div>

<!-- CSS for printing -->
<style>
    @media print {
        /* Hide unnecessary parts of the page */
        .navigation, .card-footer{
            visibility: hidden;
        }
    }
</style>
@endsection
