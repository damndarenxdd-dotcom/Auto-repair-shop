@extends('layouts.app')
@section('title', 'Edit Service')
@section('content')
<div class="card" style="max-width: 600px;">
    <h2>Edit Service</h2>
    <form method="POST" action="{{ route('admin.services.update', $service) }}">@csrf @method('PUT')
        <div class="form-group">
            <label>Service Name</label>
            <input type="text" name="name" value="{{ $service->name }}" required>
        </div>
        <div class="form-group">
            <label>Description</label>
            <textarea name="description" rows="3">{{ $service->description }}</textarea>
        </div>
        <div class="form-group">
            <label>Price ($)</label>
            <input type="number" name="price" step="0.01" value="{{ $service->price }}" required>
        </div>
        <div class="form-group">
            <label>Estimated Hours</label>
            <input type="number" name="estimated_hours" value="{{ $service->estimated_hours }}" required>
        </div>
        <div class="form-group">
            <label><input type="checkbox" name="is_active" value="1" @checked($service->is_active)> Active</label>
        </div>
        <button type="submit" class="btn btn-primary">Update Service</button>
    </form>
</div>
@endsection
