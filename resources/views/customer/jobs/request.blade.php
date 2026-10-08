@extends('layouts.app')
@section('title', 'Request Repair Job')
@section('content')
<div class="card" style="max-width: 700px;">
    <h2>Request Repair Job</h2>
    <form method="POST" action="{{ route('customer.jobs.submit') }}">@csrf
        <div class="form-group">
            <label>Vehicle Model (e.g., Honda Civic 2020)</label>
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
            <label>Describe the repair needed</label>
            <textarea name="description" rows="5" required placeholder="Describe any issues, symptoms, or work needed..."></textarea>
        </div>
        <button type="submit" class="btn btn-primary">Submit Request</button>
        <a href="{{ route('customer.dashboard') }}" class="btn btn-primary">Cancel</a>
    </form>
</div>
@endsection
