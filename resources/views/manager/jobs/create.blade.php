@extends('layouts.app')
@section('title', 'Create Job')
@section('content')
<div class="card" style="max-width: 700px;">
    <h2>Create New Repair Job</h2>
    <form method="POST" action="{{ route('manager.jobs.store') }}">@csrf
        <div class="form-group">
            <label>Customer</label>
            <select name="customer_id" required>
                <option value="">-- Select Customer --</option>
                @foreach($customers as $customer)
                <option value="{{ $customer->id }}">{{ $customer->name }} ({{ $customer->email }})</option>
                @endforeach
            </select>
        </div>
        <div class="form-group">
            <label>Mechanic (optional)</label>
            <select name="mechanic_id">
                <option value="">-- Unassigned --</option>
                @foreach($mechanics as $mechanic)
                <option value="{{ $mechanic->id }}">{{ $mechanic->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="form-group">
            <label>Vehicle Model</label>
            <input type="text" name="vehicle_model" required>
        </div>
        <div class="form-group">
            <label>License Plate</label>
            <input type="text" name="license_plate" required>
        </div>
        <div class="form-group">
            <label>Year (optional)</label>
            <input type="number" name="year" min="1900">
        </div>
        <div class="form-group">
            <label>Description</label>
            <textarea name="description" rows="4" required></textarea>
        </div>
        <div class="form-group">
            <label>Estimated Cost ($)</label>
            <input type="number" name="estimated_cost" step="0.01">
        </div>
        <button type="submit" class="btn btn-primary">Create Job</button>
    </form>
</div>
@endsection
