@extends('layouts.app')
@section('title', 'Create Service')
@section('content')
<div class="card" style="max-width: 600px;">
    <h2>Add Service</h2>
    <form method="POST" action="{{ route('admin.services.store') }}">@csrf
        <div class="form-group">
            <label>Service Name</label>
            <input type="text" name="name" required>
        </div>
        <div class="form-group">
            <label>Description</label>
            <textarea name="description" rows="3"></textarea>
        </div>
        <div class="form-group">
            <label>Price ($)</label>
            <input type="number" name="price" step="0.01" required>
        </div>
        <div class="form-group">
            <label>Estimated Hours</label>
            <input type="number" name="estimated_hours" required>
        </div>
        <div class="form-group">
            <label><input type="checkbox" name="is_active" value="1" checked> Active</label>
        </div>
        <button type="submit" class="btn btn-primary">Add Service</button>
    </form>
</div>
@endsection
