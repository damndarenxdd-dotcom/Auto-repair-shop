@extends('layouts.app')
@section('title', 'Mechanic Dashboard')
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
</div>

<div class="card">
    <h2>My Jobs</h2>
    <table class="table">
        <thead>
            <tr><th>Vehicle</th><th>Status</th><th>Action</th></tr>
        </thead>
        <tbody>
            @forelse($recentJobs as $job)
            <tr>
                <td>{{ $job->vehicle_model }} ({{ $job->license_plate }})</td>
                <td><span style="background: #007bff; color: white; padding: 0.25rem 0.5rem; border-radius: 4px;">{{ ucfirst($job->status) }}</span></td>
                <td><a href="{{ route('mechanic.jobs.show', $job) }}" class="btn btn-primary">View</a></td>
            </tr>
            @empty
            <tr><td colspan="3">No jobs assigned yet</td></tr>
            @endforelse
        </tbody>
    </table>
    <a href="{{ route('mechanic.jobs.index') }}" class="btn btn-primary" style="margin-top: 1rem;">View All Jobs</a>
</div>
@include('partials.weather_card')
@endsection
