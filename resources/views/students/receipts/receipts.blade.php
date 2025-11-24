@extends('layouts.app')

@section('content')



<div class="container py-4">
    <div class="card border-secondary" style="width: 210mm; margin: 0 auto; position: relative; overflow: hidden;">
        <!-- Watermark overlay 
        <div style="
            position: absolute;
            top: 0;
            left: 0;
            opacity: 0.1;
            color: black;
            font-size: 20px;
            pointer-events: none;
            z-index: 1;
            text-align: center;
        " class="watermark">
            @for ($i = 0; $i < 18; $i++)
            ---Tham's Taekwon-Do Academy--- ---Tham's Taekwon-Do Academy--- ---Tham's Taekwon-Do Academy--- ---Tham's Taekwon-Do Academy---
            @endfor
        </div>-->
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
                    @if(auth()->user()->role === 'viewer')
                        <p class="mb-0">Address: *********************</p>
                        <p class="mb-0">*********************</p>
                        <p class="mb-0">*********************</p>
                        <p class="mb-0">Tel: ***-*** ****</p>
                    @else
                        <p class="mb-0">Address: No 14A, Kledang Permai 7,</p>
                        <p class="mb-0">Taman Kledang Permai, Menglembu,</p>
                        <p class="mb-0">31450 Ipoh, Perak</p>
                        <p class="mb-0">Tel: 016-560 6092</p>
                    @endif
                </div>
            </div>
            <h3 class="text-dark mt-3"><strong>OFFICIAL RECEIPT</strong></h3>
        </div>
        <div class="card-body" id="receipt">
            <div class="row" style="display: flex; justify-content: space-between;">
                <div class="col-md-6" style="flex: 1;">
                    <h5>Received From</h5>
                    @if(auth()->user()->role === 'viewer')
                        <p><strong>Name:</strong> Student ***</p>
                    @else
                        <p><strong>Name:</strong> {{ $payment->student->name }}</p>
                    @endif
                </div>
                <div class="col-md-6 text-right" style="flex: 1; text-align: right;">
                    <h5>Receipt Details</h5>
                    @if(auth()->user()->role === 'viewer')
                        <p><strong>Receipt ID:</strong> P*****</p>
                    @else
                        <p><strong>Receipt ID:</strong> {{ 'P' . sprintf('%05d', $payment->payment_id) }}</p>
                    @endif
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
                    @if(auth()->user()->role === 'viewer')
                        <tr>
                            <td>Monthly Fees</td>
                            <td class="text-right">RM***.**</td>
                        </tr>
                        <tr>
                            <th style="text-align: right;">Total</th>
                            <td>RM***.**</td>
                        </tr>
                    @else
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
                    @endif
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
                        @if(auth()->user()->role === 'viewer')
                            <div style="display: inline-block; height:100px; width: 100px; margin-top: 5px; background-color: #f0f0f0; border: 1px solid #ccc; text-align: center; line-height: 100px; color: #666;">
                                [Hidden]
                            </div>
                        @else
                            <img src="{{ route('signiture.show') }}" alt="sign" style="height:100px; width: 100px; margin-top: 5px;" loading="lazy">
                        @endif
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
        .watermark{
            display: none;
        }
    }
</style>
@endsection
