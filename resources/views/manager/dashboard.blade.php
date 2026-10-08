@extends('layouts.app')
@section('title', 'Manager Dashboard')
@section('content')
<div class="dashboard-grid">
    <div class="card stat-card">
        <h3>{{ $assignedJobs }}</h3>
        <p>Assigned Jobs</p>
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
        <h3>{{ $pendingJobs }}</h3>
        <p>Pending</p>
    </div>
</div>

<div class="card">
    <h2>Manager Dashboard</h2>
    <nav style="margin-top: 1rem; display: flex; gap: 1rem; flex-wrap: wrap;">
        <a href="{{ route('manager.jobs.index') }}" class="btn btn-primary">View Jobs</a>
        <a href="{{ route('manager.jobs.create') }}" class="btn btn-primary">Create New Job</a>
        <a href="{{ route('manager.invoices.index') }}" class="btn btn-primary">Manage Invoices</a>
    </nav>
</div>
@include('partials.weather_card')
@endsection
