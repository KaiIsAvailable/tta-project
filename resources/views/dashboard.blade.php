@extends('layouts.app')

@section('content')
    <div class="container">
        <h1>Dashboard</h1>
        <p>Total number of students: {{ $studentCount }}</p>
    </div>
@endsection
