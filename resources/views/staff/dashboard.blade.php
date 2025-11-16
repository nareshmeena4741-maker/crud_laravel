@extends('layouts.app')

@section('title', 'Staff Dashboard')

@section('content')
    <h3>Staff Dashboard</h3>
    <p>Welcome, {{ auth()->user()->name }}. staff dashboard.</p>
@endsection
