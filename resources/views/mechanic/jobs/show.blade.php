@extends('layouts.app')
@section('title', 'Job Details')
@section('content')
<div class="card">
    <h2>Job Details: {{ $job->vehicle_model }}</h2>
    <div style="margin: 1rem 0;">
        <p><strong>License Plate:</strong> {{ $job->license_plate }}</p>
        <p><strong>Customer:</strong> {{ $job->customer->name }}</p>
        <p><strong>Status:</strong> <span style="background: #17a2b8; color: white; padding: 0.25rem 0.5rem; border-radius: 4px;">{{ ucfirst($job->status) }}</span></p>
        <p><strong>Description:</strong> {{ $job->description }}</p>
        <p><strong>Notes:</strong> {{ $job->notes ?? 'None' }}</p>
    </div>
    
    @if($job->status !== 'completed' && $job->status !== 'cancelled')
    <form method="POST" action="{{ route('mechanic.jobs.updateStatus', $job) }}" style="margin: 1rem 0;">@csrf @method('PUT')
        <div class="form-group">
            <label>Update Status</label>
            <select name="status" required>
                <option value="assigned" @selected($job->status === 'assigned')>Assigned</option>
                <option value="in-progress" @selected($job->status === 'in-progress')>In Progress</option>
                <option value="completed">Completed</option>
            </select>
        </div>
        <button type="submit" class="btn btn-primary">Update Status</button>
    </form>
    @endif
    
    <div>
        <h3>Add Notes</h3>
        <form method="POST" action="{{ route('mechanic.jobs.addNotes', $job) }}">@csrf
            <div class="form-group">
                <textarea name="notes" rows="3" required></textarea>
            </div>
            <button type="submit" class="btn btn-primary">Add Notes</button>
        </form>
    </div>
</div>
@endsection
