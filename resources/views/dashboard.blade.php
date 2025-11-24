@extends('layouts.app')

@section('content')
<div class="container py-4"> <!-- Added vertical padding to container -->
    <div class="d-flex align-items-center justify-content-between">
        <h1 class="mb-4"><strong>Dashboard</strong></h1>
        <button id="installButton" class="btn btn-primary" style="display: none;">Install App</button>
    </div>

    @if (Auth::user()->isAdmnin)
        @if ($hasPending || $hasNewRegister)
            <div class="alert alert-success">
                <p>You have new student registered. Please check who are they! <a href="{{ route('users.index') }}" style="color: red;">Click here to see who</a></p>
            </div>
        @endif
    @endif

    @auth
        <div class="mb-4">Welcome <strong>{{ Auth::user()->name }}</strong> to Tham's Taekwon-do Academy system!</div>
    @else
        <div class="mb-4">Welcome to Tham's Taekwon-do Academy!</div>
    @endauth

    @if(Auth::user()->isAdmin() || Auth::user()->isViewer())
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
                <div class="card-header bg-light text-dark">Payments</div>
                <div class="card-body py-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <p class="mb-0"><strong>{{$paidPaymentCount}}</strong> Paid.</p>
                        <a href="{{ route('payments.index', ['payment_status' => 'Paid']) }}" class="btn">View</a>
                    </div>
                    <div class="d-flex justify-content-between align-items-center">
                        <p class="mb-0"><strong>{{$unpaidPaymentCount}}</strong> Unpaid.</p>
                        <a href="{{ route('payments.index', ['payment_status' => 'Unpaid']) }}" class="btn">View</a>
                    </div>
                    <div class="d-flex justify-content-between align-items-center">
                        <p class="mb-0"><strong>{{$voidedPaymentCount}}</strong> Voided.</p>
                        <a href="{{ route('payments.index', ['payment_status' => 'Voided']) }}" class="btn">View</a>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4 mb-4">
            <div class="card border-secondary">
                <div class="card-header bg-light text-dark">Daily Fees Colected</div>
                <div class="card-body py-4">
                    
                </div>
            </div>
        </div>

        <div class="col-md-4 mb-4">
            <div class="card border-secondary">
                <div class="card-header bg-light text-dark">Montly Fee Collected</div>
                <div class="card-body py-4">
                   
                </div>
            </div>
        </div>
    </div>
    @elseif(Auth::user()->isInstructor())
        <div class="col-md-4 mb-4">
            <div class="card border-secondary">
                <div class="card-header bg-light text-dark">Total Students</div>
                <div class="card-body py-4"> <!-- Added padding to card body -->
                    <h5 class="card-title">{{ $studentCount }}</h5>
                    <p class="card-text">All enrolled students in the system.</p>
                </div>
            </div>
        </div>
    @elseif(Auth::user()->isStudent())
    
    @endif
</div>

<script>
    let deferredPrompt;

    // Listen for the `beforeinstallprompt` event
    window.addEventListener('beforeinstallprompt', (e) => {
        // Prevent the default mini-info bar from appearing
        e.preventDefault();
        // Save the event for later use
        deferredPrompt = e;

        // Enable the install button
        const installButton = document.getElementById('installButton');
        installButton.style.display = 'block';

        installButton.addEventListener('click', async () => {
            // Check if deferredPrompt is available
            if (!deferredPrompt) return;

            // Show the install prompt
            deferredPrompt.prompt();

            // Wait for the user's response
            const { outcome } = await deferredPrompt.userChoice;

            if (outcome === 'accepted') {
                console.log('User accepted the installation');
            } else {
                console.log('User dismissed the installation');
            }

            // Reset the deferred prompt
            deferredPrompt = null;
        });
    });

    // Optionally, hide the button if the app is already installed
    window.addEventListener('appinstalled', () => {
        console.log('App was installed');
        const installButton = document.getElementById('installButton');
        installButton.style.display = 'none';
    });
</script>
@endsection
