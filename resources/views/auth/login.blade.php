@extends('layouts.app')
@section('title', 'Login')
@section('content')
<div class="auth-shell">
    <div class="auth-panel">
        <div class="auth-hero">
            <div class="auth-hero-inner">
                <div>
                    <span class="auth-badge">Service portal</span>
                    <h1>Keep every vehicle moving.</h1>
                    <p>Track repair jobs, service requests, technician performance, and workshop activity from one operational dashboard.</p>
                </div>

                <div class="auth-points">
                    <div class="auth-point"><i>01</i> <span>Real-time job visibility</span></div>
                    <div class="auth-point"><i>02</i> <span>Service scheduling & reporting</span></div>
                    <div class="auth-point"><i>03</i> <span>Customer communication controls</span></div>
                </div>
            </div>
        </div>

        <div class="auth-form-panel">
            <div class="auth-form">
                <div class="form-heading">
                    <h2>Welcome back</h2>
                    <p>Sign in to your garage workspace.</p>
                </div>

                @if (session('error'))
                    <div class="alert alert-error" style="margin-bottom: 1rem;">{{ session('error') }}</div>
                @endif

                <form method="POST" action="{{ route('login') }}">
                    @csrf
                    <div class="field">
                        <label for="email">Email address</label>
                        <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus>
                    </div>

                    <div class="field">
                        <label for="password">Password</label>
                        <input id="password" type="password" name="password" required>
                    </div>

                    <div class="auth-actions">
                        <button type="submit" class="btn btn-primary" style="width:100%;">Login to dashboard</button>
                    </div>
                </form>

                <div style="margin: 1rem 0; text-align: center; color: var(--muted);">or</div>

                <a href="{{ route('google.login') }}" class="btn btn-secondary" style="width:100%; justify-content:center; gap:10px; margin-bottom: 1rem;">
                    <span aria-hidden="true">G</span>
                    Login with Google
                </a>

                <p class="auth-link">Need an account? <a href="{{ route('register') }}">Create one</a></p>
            </div>
        </div>
    </div>
</div>
@endsection
