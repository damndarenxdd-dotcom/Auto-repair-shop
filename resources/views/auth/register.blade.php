@extends('layouts.app')
@section('title', 'Register')
@section('content')
<div style="max-width: 400px; margin: 2rem auto;">
    <div class="card">
        <h2>Create Account</h2>
        <form method="POST" action="{{ route('register') }}">@csrf
            <div class="form-group">
                <label>Name</label>
                <input type="text" name="name" value="{{ old('name') }}" required>
            </div>
            <div class="form-group">
                <label>Email</label>
                <input type="email" name="email" value="{{ old('email') }}" required>
            </div>
            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password" required>
            </div>
            <div class="form-group">
                <label>Confirm Password</label>
                <input type="password" name="password_confirmation" required>
            </div>
            <button type="submit" class="btn btn-primary" style="width: 100%;">Register</button>
        </form>
        <p style="margin-top: 1rem;"><a href="{{ route('login') }}">Already have account? Login</a></p>
    </div>
</div>
@endsection
