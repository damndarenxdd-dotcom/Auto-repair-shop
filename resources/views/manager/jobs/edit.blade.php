@extends('layouts.app')
@section('title', 'Edit Job')
@section('content')
<div class="card" style="max-width: 700px;">
    <h2>Edit Job: {{ $job->vehicle_model }}</h2>
    <form method="POST" action="{{ route('manager.jobs.update', $job) }}">@csrf @method('PUT')
        <div class="form-group">
            <label>Mechanic</label>
            <select name="mechanic_id">
                <option value="">-- Unassigned --</option>
                @foreach($mechanics as $mechanic)
                <option value="{{ $mechanic->id }}" @selected($job->mechanic_id === $mechanic->id)>{{ $mechanic->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="form-group">
            <label>Status</label>
            <select name="status" required>
                <option value="pending" @selected($job->status === 'pending')>Pending</option>
                <option value="assigned" @selected($job->status === 'assigned')>Assigned</option>
                <option value="in-progress" @selected($job->status === 'in-progress')>In Progress</option>
                <option value="completed" @selected($job->status === 'completed')>Completed</option>
                <option value="cancelled" @selected($job->status === 'cancelled')>Cancelled</option>
            </select>
        </div>
        <div class="form-group">
            <label>Estimated Cost ($)</label>
            <input type="number" name="estimated_cost" step="0.01" value="{{ $job->estimated_cost }}">
        </div>
        <div class="form-group">
            <label>Notes</label>
            <textarea name="notes" rows="4">{{ $job->notes }}</textarea>
        </div>
        <button type="submit" class="btn btn-primary">Update Job</button>
        <a href="{{ route('manager.jobs.index') }}" class="btn btn-primary">Cancel</a>
    </form>
</div>
@endsection
