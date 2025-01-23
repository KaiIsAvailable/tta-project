@extends('layouts.app')

@section('content')
<div class="container py-4"> <!-- Added vertical padding to container -->
    <h1 class="mb-4"><strong>Dashboard</strong></h1>

    @auth
        <div class="mb-4">Welcome <strong>{{ Auth::user()->name }}</strong> to Tham's Taekwon-do Academy system!</div>
    @else
        <div class="mb-4">Welcome to Tham's Taekwon-do Academy!</div>
    @endauth

    <div class="row">
        <!-- Summary Cards -->
        <div class="col-md-4 mb-4">
            <div class="card border-secondary">
                <div class="card-header bg-light text-dark">Total Students</div>
                <div class="card-body py-4"> <!-- Added padding to card body -->
                    <h5 class="card-title">{{ $studentCount }}</h5>
                    <p class="card-text">All enrolled students in the system.</p>
                </div>
            </div>
        </div>

        <div class="col-md-4 mb-4">
            <div class="card border-secondary">
                <div class="card-header bg-light text-dark">Paid Payments</div>
                <div class="card-body py-4">
                    <h5 class="card-title">{{ $paidPaymentCount }}</h5>
                    <div class="d-flex justify-content-between align-items-center">
                        <p class="mb-0">Payments successfully completed.</p>
                        <a href="{{ route('payments.index', ['payment_status' => 'Paid']) }}" class="btn btn-primary">View</a>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4 mb-4">
            <div class="card border-secondary">
                <div class="card-header bg-light text-dark">Unpaid Payments</div>
                <div class="card-body py-4">
                    <h5 class="card-title">{{ $unpaidPaymentCount }}</h5>
                    <div class="d-flex justify-content-between align-items-center">
                        <p class="mb-0">Payments not completed.</p>
                        <a href="{{ route('payments.index', ['payment_status' => 'Unpaid']) }}" class="btn btn-primary">View</a>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4 mb-4">
            <div class="card border-secondary">
                <div class="card-header bg-light text-dark">Voided Payments</div>
                <div class="card-body py-4">
                    <h5 class="card-title">{{ $voidedPaymentCount }}</h5>
                    <div class="d-flex justify-content-between align-items-center">
                        <p class="mb-0">Payments that have been voided.</p>
                        <a href="{{ route('payments.index', ['payment_status' => 'Voided']) }}" class="btn btn-primary">View</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
