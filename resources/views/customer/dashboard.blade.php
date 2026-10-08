@extends('layouts.app')
@section('title', 'Customer Dashboard')
@section('content')
<div class="dashboard-grid">
    <div class="card stat-card">
        <h3>{{ $totalJobs }}</h3>
        <p>Total Jobs</p>
    </div>
    <div class="card stat-card">
        <h3>{{ $inProgressJobs }}</h3>
        <p>In Progress</p>
    </div>
    <div class="card stat-card">
        <h3>{{ $completedJobs }}</h3>
        <p>Completed</p>
    </div>
    <div class="card stat-card">
        <h3>{{ $unreadNotifications }}</h3>
        <p>Unread Notifications</p>
    </div>
</div>

<div class="card">
    <h2>Quick Actions</h2>
    <nav style="margin-top: 1rem; display: flex; gap: 1rem; flex-wrap: wrap;">
        <a href="{{ route('customer.jobs.request') }}" class="btn btn-primary">Request Repair Job</a>
        <a href="{{ route('customer.jobs.index') }}" class="btn btn-primary">My Jobs</a>
        <a href="{{ route('customer.notifications.index') }}" class="btn btn-primary">Notifications</a>
        <a href="{{ route('customer.invoices.index') }}" class="btn btn-primary">Invoices</a>
    </nav>
</div>
@include('partials.weather_card')
@endsection
