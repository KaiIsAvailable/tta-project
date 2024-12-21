@extends('layouts.app')

@section('content')
    <div class="container">
        <h1><strong>Dashboard</strong></h1>
        @auth
            <div>Welcome {{ Auth::user()->name }} to Tham's Taekwon-do Academy system</div>
        @else
            <div>Welcome to Tham's Taekwon-do Academy</div>
        @endauth
        <br>
        <p>Total number of students: {{ $studentCount }}</p>
        
    </div>
@endsection
