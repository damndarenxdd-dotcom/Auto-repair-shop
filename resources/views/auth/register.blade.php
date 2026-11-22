@extends('layouts.app')
@section('title', 'Register')
@section('content')
<div class="auth-shell">
    <div class="auth-panel">
        <div class="auth-hero">
            <div class="auth-hero-inner">
                <div>
                    <span class="auth-badge">New workshop account</span>
                    <h1>Set up your team access.</h1>
                    <p>Create a user profile and start managing repair requests, billing, and daily service operations from your dashboard.</p>
                </div>

                <div class="auth-points">
                    <div class="auth-point"><i>01</i> <span>Start with customer or team access</span></div>
                    <div class="auth-point"><i>02</i> <span>Monitor workshop performance</span></div>
                    <div class="auth-point"><i>03</i> <span>Connect directly to repair workflows</span></div>
                </div>
            </div>
        </div>

        <div class="auth-form-panel">
            <div class="auth-form">
                <div class="form-heading">
                    <h2>Create account</h2>
                    <p>Join the repair shop system.</p>
                </div>

                <form method="POST" action="{{ route('register') }}">
                    @csrf
                    <div class="field">
                        <label for="name">Full name</label>
                        <input id="name" type="text" name="name" value="{{ old('name') }}" required>
                    </div>

                    <div class="field">
                        <label for="email">Email address</label>
                        <input id="email" type="email" name="email" value="{{ old('email') }}" required>
                    </div>

                    <div class="field">
                        <label for="password">Password</label>
                        <input id="password" type="password" name="password" required>
                    </div>

                    <div class="field">
                        <label for="password_confirmation">Confirm password</label>
                        <input id="password_confirmation" type="password" name="password_confirmation" required>
                    </div>

                    <div class="auth-actions">
                        <button type="submit" class="btn btn-primary" style="width:100%;">Register account</button>
                    </div>
                </form>

                <div style="margin: 1rem 0; text-align: center; color: var(--muted);">or</div>

                <a href="{{ route('google.login') }}" class="btn btn-secondary" style="width:100%; justify-content:center; gap:10px; margin-bottom: 1rem;">
                    <span aria-hidden="true">G</span>
                    Continue with Google
                </a>

                <p class="auth-link">Already have an account? <a href="{{ route('login') }}">Log in</a></p>
            </div>
        </div>
    </div>
</div>
@endsection
