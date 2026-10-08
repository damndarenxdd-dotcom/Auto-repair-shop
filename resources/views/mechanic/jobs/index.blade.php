@extends('layouts.app')
@section('title', 'My Jobs')
@section('content')
<div class="card">
    <h2>My Assigned Jobs</h2>
    <table class="table">
        <thead>
            <tr><th>Vehicle</th><th>License Plate</th><th>Status</th><th>Actions</th></tr>
        </thead>
        <tbody>
            @forelse($jobs as $job)
            <tr>
                <td>{{ $job->vehicle_model }}</td>
                <td>{{ $job->license_plate }}</td>
                <td><span style="background: #17a2b8; color: white; padding: 0.25rem 0.5rem; border-radius: 4px;">{{ ucfirst($job->status) }}</span></td>
                <td><a href="{{ route('mechanic.jobs.show', $job) }}" class="btn btn-primary">View</a></td>
            </tr>
            @empty<tr><td colspan="4">No jobs assigned</td></tr>@endforelse
        </tbody>
    </table>
    {{ $jobs->links() }}
</div>
@endsection
