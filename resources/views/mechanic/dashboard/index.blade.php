@extends('layouts.app')
@section('title', 'Mechanic Dashboard')
@section('content')
<div class="dashboard-shell">
    <header class="page-header">
        <div>
            <span class="eyebrow">Technician workspace</span>
            <h1>My repair queue</h1>
            <p>Track assigned vehicles, current jobs, and completed repairs from one place.</p>
        </div>
        <span class="role-badge role-mechanic">Mechanic</span>
    </header>

    <section class="stat-grid">
        <div class="stat-card">
            <div>
                <p class="stat-number">{{ $assignedJobs }}</p>
                <p class="stat-label">Assigned</p>
            </div>
            <div class="stat-icon">🚗</div>
        </div>
        <div class="stat-card">
            <div>
                <p class="stat-number">{{ $inProgressJobs }}</p>
                <p class="stat-label">In progress</p>
            </div>
            <div class="stat-icon">⚙️</div>
        </div>
        <div class="stat-card">
            <div>
                <p class="stat-number">{{ $completedJobs }}</p>
                <p class="stat-label">Completed</p>
            </div>
            <div class="stat-icon">🏁</div>
        </div>
    </section>

    <section class="panel">
        <div class="panel-header">
            <h2 class="panel-title">Current jobs</h2>
        </div>
        <div class="table-wrap" style="padding: 0 22px 22px;">
            <table class="table">
                <thead>
                    <tr>
                        <th>Vehicle</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($recentJobs as $job)
                    <tr>
                        <td>{{ $job->vehicle_model }} ({{ $job->license_plate }})</td>
                        <td><span class="status-pill">{{ ucfirst($job->status) }}</span></td>
                        <td><a href="{{ route('mechanic.jobs.show', $job) }}" class="btn btn-primary">View</a></td>
                    </tr>
                    @empty
                    <tr><td colspan="3">No jobs assigned yet</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="action-grid" style="padding-top:0;">
            <a href="{{ route('mechanic.jobs.index') }}" class="action-card">
                <strong>View all jobs</strong>
                <span>Open the full technician schedule and job list.</span>
            </a>
        </div>
    </section>

    <section class="panel weather-panel">
        @include('partials.weather_card')
    </section>
</div>
@endsection
