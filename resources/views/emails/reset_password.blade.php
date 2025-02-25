@component('mail::message')

{{-- Custom Logo --}}
<img src="{{ asset('images/TTA logo.jpg') }}" width="150" alt="App Logo">

# Hello, {{ $notifiable->name }}!

We received a request to reset your password. Click the button below to reset it.

@component('mail::button', ['url' => url('/password/reset', $token)])
Reset Password
@endcomponent

If you did not request this, you can ignore this email.

Thanks,  
{{ config('app.name') }}

@endcomponent
