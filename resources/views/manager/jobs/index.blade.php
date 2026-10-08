@extends('layouts.app')
@section('title', 'Manager Jobs')
@section('content')
<div class="card">
    <h2>Repair Jobs</h2>
    <a href="{{ route('manager.jobs.create') }}" class="btn btn-primary">Create New Job</a>
    <table class="table" style="margin-top: 1rem;">
        <thead>
            <tr><th>Vehicle</th><th>Customer</th><th>Status</th><th>Cost</th><th>Actions</th></tr>
        </thead>
        <tbody>
            @forelse($jobs as $job)
            <tr>
                <td>{{ $job->vehicle_model }}</td>
                <td>{{ $job->customer->name }}</td>
                <td><span style="background: #17a2b8; color: white; padding: 0.25rem 0.5rem; border-radius: 4px;">{{ ucfirst($job->status) }}</span></td>
                <td>${{ number_format($job->estimated_cost ?? 0, 2) }}</td>
                <td>
                    <a href="{{ route('manager.jobs.edit', $job) }}" class="btn btn-primary">Edit</a>
                    <form action="{{ route('manager.jobs.destroy', $job) }}" method="POST" style="display: inline;" onsubmit="return confirm('Delete?')">@csrf @method('DELETE')<button type="submit" class="btn btn-danger">Delete</button></form>
                </td>
            </tr>
            @empty<tr><td colspan="5">No jobs found</td></tr>@endforelse
        </tbody>
    </table>
    {{ $jobs->links() }}
</div>
@endsection
