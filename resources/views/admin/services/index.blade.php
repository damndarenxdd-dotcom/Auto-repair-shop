@extends('layouts.app')
@section('title', 'Services Management')
@section('content')
<div class="card">
    <h2>Services Management</h2>
    <a href="{{ route('admin.services.create') }}" class="btn btn-primary">Add Service</a>
    <table class="table" style="margin-top: 1rem;">
        <thead>
            <tr><th>Name</th><th>Price</th><th>Hours</th><th>Active</th><th>Actions</th></tr>
        </thead>
        <tbody>
            @forelse($services as $service)
            <tr>
                <td>{{ $service->name }}</td>
                <td>${{ number_format($service->price, 2) }}</td>
                <td>{{ $service->estimated_hours }}h</td>
                <td>{{ $service->is_active ? 'Yes' : 'No' }}</td>
                <td>
                    <a href="{{ route('admin.services.edit', $service) }}" class="btn btn-primary">Edit</a>
                    <form action="{{ route('admin.services.destroy', $service) }}" method="POST" style="display: inline;" onsubmit="return confirm('Delete?')">@csrf @method('DELETE')<button type="submit" class="btn btn-danger">Delete</button></form>
                </td>
            </tr>
            @empty<tr><td colspan="5">No services found</td></tr>@endforelse
        </tbody>
    </table>
    {{ $services->links() }}
</div>
@endsection
