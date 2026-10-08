@extends('layouts.app')
@section('title', 'Edit User')
@section('content')
<div class="card" style="max-width: 600px;">
    <h2>Edit User</h2>
    <form method="POST" action="{{ route('admin.users.update', $user) }}">@csrf @method('PUT')
        <div class="form-group">
            <label>Name</label>
            <input type="text" name="name" value="{{ $user->name }}" required>
        </div>
        <div class="form-group">
            <label>Email</label>
            <input type="email" name="email" value="{{ $user->email }}" required>
        </div>
        <div class="form-group">
            <label>Password (leave blank to keep current)</label>
            <input type="password" name="password">
        </div>
        <div class="form-group">
            <label>Role</label>
            <select name="role_id" required>
                @foreach($roles as $role)
                <option value="{{ $role->id }}" @selected($user->role_id === $role->id)>{{ ucfirst($role->name) }}</option>
                @endforeach
            </select>
        </div>
        <div class="form-group">
            <label>Phone</label>
            <input type="text" name="phone" value="{{ $user->phone }}">
        </div>
        <div class="form-group">
            <label>Address</label>
            <textarea name="address" rows="3">{{ $user->address }}</textarea>
        </div>
        <button type="submit" class="btn btn-primary">Update User</button>
        <a href="{{ route('admin.users.index') }}" class="btn btn-primary">Cancel</a>
    </form>
</div>
@endsection
