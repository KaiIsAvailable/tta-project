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
            <h3 class="text-dark mt-3"><strong>INVOICE</strong></h3>
        </div>
        <div class="card-body" id="receipt">
            <div class="row" style="display: flex; justify-content: space-between;">
                <div class="col-md-6" style="flex: 1;">
                    <h5>Issues To</h5>
                    <p><strong>Name:</strong> 
                        @if(auth()->user()->role === 'viewer')
                            Student ***
                        @else
                            {{ $payment->student->name }}
                        @endif
                    </p>
                </div>
                <div class="col-md-6 text-right" style="flex: 1; text-align: right;">
                    <h5>Invoice Details</h5>
                    <p><strong>Inovoice ID:</strong> 
                        @if(auth()->user()->role === 'viewer')
                            I*****
                        @else
                            {{ 'I' . sprintf('%05d', $payment->payment_id) }}
                        @endif
                    </p>
                    @if($payment->payment_date)
                        <p><strong>Date:</strong> {{ $payment->payment_date->format('d-M-Y') }}</p>
                    @endif
                    <p style="color: red;"><strong style="color: black;">Status:</strong> {{$payment->payment_status}}</p>
                </div>
            </div> 
            <hr>
            <br>
            <h5>Details</h5>
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th style="width: 70%;">Description</th>
                        <th class="text-right">Amount</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>
                            @if (strtotime($payment->paid_for))
                                {{ $payment->paid_for->format('F Y') }}'s Fees
                            @endif
                        </td>
                        <td class="text-right">
                            @if(auth()->user()->role === 'viewer')
                                RM***.**
                            @else
                                RM{{ number_format($payment->payment_amount, 2) }}
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td style="text-align: right;">Outstanding</td>
                        <td>
                            @if(auth()->user()->role === 'viewer')
                                RM***.**
                            @else
                                RM{{number_format($previousOutstanding, 2)}}
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td style="text-align: right;">Pre Payment</td>
                        <td>
                            @if(auth()->user()->role === 'viewer')
                                RM***.**
                            @else
                                RM{{number_format(abs($previousPrePayment), 2)}}
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <th style="text-align: right;">Total</th>
                        <td>
                            @if(auth()->user()->role === 'viewer')
                                RM***.**
                            @else
                                RM{{number_format($payment->payment_amount + $previousOutstanding + $previousPrePayment, 2)}}
                            @endif
                        </td>
                    </tr>
                </tbody>
            </table>
            <hr>
            <div class="text-right">
                <div style="text-align: right;">
                    <p style="margin-right: 30px;">Sign:</p>
                    @if(auth()->user()->role === 'viewer')
                        <div style="display: inline-block; height:100px; width: 100px; margin-top: 5px; text-align: right; background-color: #f0f0f0; border: 1px solid #ccc; text-align: center; line-height: 100px; color: #666;">
                            [Hidden]
                        </div>
                    @else
                        <img src="{{ route('signiture.show') }}" alt="sign" style="display: inline-block; height:100px; width: 100px; margin-top: 5px; text-align: right;" loading="lazy">
                    @endif
                </div>
            </div>
        </div>
    </div>
    <div class="card-footer text-center">
        <a href="javascript:history.back()" class="btn btn-secondary">
            Cancel
        </a>
        <button class="btn btn-primary" onclick="window.print()">Print Invoice</button>
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
