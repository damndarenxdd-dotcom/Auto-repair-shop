@extends('layouts.app')
@section('title', 'Create User')
@section('content')
<div class="card" style="max-width: 600px;">
    <h2>Create New User</h2>
    <form method="POST" action="{{ route('admin.users.store') }}">@csrf
        <div class="form-group">
            <label>Name</label>
            <input type="text" name="name" required>
        </div>
        <div class="form-group">
            <label>Email</label>
            <input type="email" name="email" required>
        </div>
        <div class="form-group">
            <label>Password</label>
            <input type="password" name="password" required>
        </div>
        <div class="form-group">
            <label>Role</label>
            <select name="role_id" required>
                <option value="">-- Select Role --</option>
                @foreach($roles as $role)
                <option value="{{ $role->id }}">{{ ucfirst($role->name) }}</option>
                @endforeach
            </select>
        </div>
        <div class="form-group">
            <label>Phone (optional)</label>
            <input type="text" name="phone">
        </div>
        <div class="form-group">
            <label>Address (optional)</label>
            <textarea name="address" rows="3"></textarea>
        </div>
        <button type="submit" class="btn btn-primary">Create User</button>
        <a href="{{ route('admin.users.index') }}" class="btn btn-primary">Cancel</a>
    </form>
</div>
@endsection
