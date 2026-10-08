@extends('layouts.app')
@section('title', 'Job Details')
@section('content')
<div class="card">
    <h2>Job Details: {{ $job->vehicle_model }}</h2>
    <div style="margin: 2rem 0;">
        <p><strong>License Plate:</strong> {{ $job->license_plate }}</p>
        <p><strong>Status:</strong> <span style="background: #17a2b8; color: white; padding: 0.25rem 0.5rem; border-radius: 4px;">{{ ucfirst($job->status) }}</span></p>
        <p><strong>Mechanic:</strong> {{ $job->mechanic?->name ?? 'Not assigned yet' }}</p>
        <p><strong>Manager:</strong> {{ $job->manager?->name ?? 'Not assigned yet' }}</p>
        <p><strong>Estimated Cost:</strong> ${{ number_format($job->estimated_cost ?? 0, 2) }}</p>
        <p><strong>Description:</strong></p>
        <p>{{ $job->description }}</p>
    </div>
    <a href="{{ route('customer.jobs.index') }}" class="btn btn-primary">Back to Jobs</a>
</div>
@endsection
