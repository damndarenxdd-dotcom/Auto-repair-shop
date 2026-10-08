@extends('layouts.app')
@section('title', 'My Jobs')
@section('content')
<div class="card">
    <h2>My Repair Jobs</h2>
    <a href="{{ route('customer.jobs.request') }}" class="btn btn-primary">Request New Repair</a>
    <table class="table" style="margin-top: 1rem;">
        <thead>
            <tr><th>Vehicle</th><th>Status</th><th>Created</th><th>Actions</th></tr>
        </thead>
        <tbody>
            @forelse($jobs as $job)
            <tr>
                <td>{{ $job->vehicle_model }}</td>
                <td><span style="background: #17a2b8; color: white; padding: 0.25rem 0.5rem; border-radius: 4px;">{{ ucfirst($job->status) }}</span></td>
                <td>{{ $job->created_at->format('M d, Y') }}</td>
                <td><a href="{{ route('customer.jobs.show', $job) }}" class="btn btn-primary">View</a></td>
            </tr>
            @empty<tr><td colspan="4">No jobs found</td></tr>@endforelse
        </tbody>
    </table>
    {{ $jobs->links() }}
</div>
@endsection
