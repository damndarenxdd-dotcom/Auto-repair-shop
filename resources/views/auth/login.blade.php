@extends('layouts.app')
@section('title', 'Login')
@section('content')
<div style="max-width: 400px; margin: 2rem auto;">
    <div class="card">
        <h2>Login</h2>
        <form method="POST" action="{{ route('login') }}">@csrf
            <div class="form-group">
                <label>Email</label>
                <input type="email" name="email" value="{{ old('email') }}" required>
            </div>
            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password" required>
            </div>
            <button type="submit" class="btn btn-primary" style="width: 100%;">Login</button>
        </form>
        <p style="margin-top: 1rem;"><a href="{{ route('register') }}">Create Account</a></p>
    </div>
</div>
@endsection
