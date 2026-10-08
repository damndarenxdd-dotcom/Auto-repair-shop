@extends('layouts.app')
@section('title', 'Users Management')
@section('content')
<div class="card">
    <h2>Users Management</h2>
    <a href="{{ route('admin.users.create') }}" class="btn btn-primary">Add New User</a>
    <table class="table" style="margin-top: 1rem;">
        <thead>
            <tr><th>Name</th><th>Email</th><th>Role</th><th>Phone</th><th>Actions</th></tr>
        </thead>
        <tbody>
            @forelse($users as $user)
            <tr>
                <td>{{ $user->name }}</td>
                <td>{{ $user->email }}</td>
                <td>{{ $user->role?->name ?? 'N/A' }}</td>
                <td>{{ $user->phone }}</td>
                <td>
                    <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-primary">Edit</a>
                    <form action="{{ route('admin.users.destroy', $user) }}" method="POST" style="display: inline;" onsubmit="return confirm('Delete this user?')">
                        @csrf @method('DELETE')
                        <button type="submit" class="btn btn-danger">Delete</button>
                    </form>
                </td>
            </tr>
            @empty
            <tr><td colspan="5">No users found</td></tr>
            @endforelse
        </tbody>
    </table>
    {{ $users->links() }}
</div>
@endsection
