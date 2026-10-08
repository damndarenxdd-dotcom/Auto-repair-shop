@extends('layouts.app')
@section('title', 'Reset Password')
@section('content')
<div style="max-width: 400px; margin: 2rem auto;">
    <div class="card">
        <h2>Reset Password</h2>
        <form method="POST" action="{{ route('password.store') }}">@csrf
            <input type="hidden" name="token" value="{{ $request->route('token') }}">
            <div class="form-group">
                <label>Email</label>
                <input type="email" name="email" value="{{ $request->email }}">
            </div>
            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password" required>
            </div>
            <div class="form-group">
                <label>Confirm Password</label>
                <input type="password" name="password_confirmation" required>
            </div>
            <button type="submit" class="btn btn-primary" style="width: 100%;">Reset Password</button>
        </form>
    </div>
</div>
@endsection
