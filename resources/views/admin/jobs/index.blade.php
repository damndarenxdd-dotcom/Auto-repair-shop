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
                <td>
                    <form method="POST" action="{{ route('admin.jobs.update', $job) }}" style="display: flex; flex-wrap: wrap; gap: 0.5rem; align-items: center; min-width: 250px;">
                        @csrf
                        @method('PUT')
                        <select name="mechanic_id" style="min-width: 140px;">
                            <option value="">-- Unassigned --</option>
                            @foreach(App\Models\User::whereHas('role', fn ($query) => $query->where('name', 'mechanic'))->get() as $mechanic)
                                <option value="{{ $mechanic->id }}" @selected($job->mechanic_id === $mechanic->id)>{{ $mechanic->name }}</option>
                            @endforeach
                        </select>
                        <select name="status" style="min-width: 120px;">
                            <option value="pending" @selected($job->status === 'pending')>Pending</option>
                            <option value="assigned" @selected($job->status === 'assigned')>Assigned</option>
                            <option value="in-progress" @selected($job->status === 'in-progress')>In Progress</option>
                            <option value="completed" @selected($job->status === 'completed')>Completed</option>
                            <option value="cancelled" @selected($job->status === 'cancelled')>Cancelled</option>
                        </select>
                        <input type="number" name="actual_cost" step="0.01" value="{{ $job->actual_cost ?? $job->estimated_cost ?? 0 }}" style="width: 110px;">
                        <button type="submit" class="btn btn-sm btn-primary">Save</button>
                    </form>
                </td>
                <td style="display:flex; gap:0.5rem; align-items:center;">
                    <a href="{{ route('admin.jobs.edit', $job) }}" class="btn btn-primary">Edit</a>
                    <form method="POST" action="{{ route('admin.jobs.destroy', $job) }}" onsubmit="return confirm('Are you sure you want to delete this job?');" style="display:inline;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">Delete</button>
                    </form>
                </td>
            </tr>
            @empty<tr><td colspan="6">No jobs found</td></tr>@endforelse
        </tbody>
    </table>
    {{ $jobs->links() }}
</div>
@endsection
