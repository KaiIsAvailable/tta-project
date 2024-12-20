@extends('layouts.app')

@section('content')
<!-- The form for selecting recipients and composing a message -->
<form id="messageForm" action="{{ route('message.send') }}" method="POST">
    @csrf <!-- CSRF token for protection -->

    <label for="recipients">Select Recipients:</label>
    <select id="recipients" name="recipients[]" multiple>
        <!-- Loop through the phones and create options -->
        @foreach($phones as $phone)
            <option value="{{ $phone->phone_id }}">
                {{ $phone->phone_person }} - {{ $phone->phone_number }}
            </option>
        @endforeach
    </select>

    <label for="message">Message:</label>
    <textarea id="message" name="message" rows="4"></textarea>

    <button type="submit">Send Message</button>
</form>

@endsection
