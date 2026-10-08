@extends('layouts.app')
@section('title', 'Jobs Management')
@section('content')
<div class="card">
    <h2>All Repair Jobs</h2>
    <table class="table">
        <thead>
            <tr><th>Vehicle</th><th>Customer</th><th>Mechanic</th><th>Status</th><th>Cost</th><th>Actions</th></tr>
        </thead>
        <tbody>
            @forelse($jobs as $job)
            <tr>
                <td>{{ $job->vehicle_model }}</td>
                <td>{{ $job->customer->name }}</td>
                <td>{{ $job->mechanic?->name ?? 'Unassigned' }}</td>
                <td><span style="background: #17a2b8; color: white; padding: 0.25rem 0.5rem; border-radius: 4px;">{{ ucfirst($job->status) }}</span></td>
                <td>${{ number_format($job->actual_cost ?? $job->estimated_cost ?? 0, 2) }}</td>
                <td><a href="{{ route('admin.jobs.edit', $job) }}" class="btn btn-primary">Edit</a></td>
            </tr>
            @empty<tr><td colspan="6">No jobs found</td></tr>@endforelse
        </tbody>
    </table>
    {{ $jobs->links() }}
</div>
@endsection
