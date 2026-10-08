@extends('layouts.app')
@section('title', 'Forgot Password')
@section('content')
<div style="max-width: 400px; margin: 2rem auto;">
    <div class="card">
        <h2>Forgot Password</h2>
        <p>Enter your email to receive password reset link.</p>
        <form method="POST" action="{{ route('password.email') }}">@csrf
            <div class="form-group">
                <label>Email</label>
                <input type="email" name="email" required>
            </div>
            <button type="submit" class="btn btn-primary" style="width: 100%;">Send Reset Link</button>
        </form>
    </div>
</div>
@endsection
