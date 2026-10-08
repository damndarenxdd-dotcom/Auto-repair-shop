@extends('layouts.app')
@section('title', 'Verify Email')
@section('content')
<div style="max-width: 400px; margin: 2rem auto;">
    <div class="card">
        <h2>Verify Email</h2>
        <p>Please verify your email address by clicking the link we sent.</p>
        <form method="POST" action="{{ route('verification.send') }}">@csrf
            <button type="submit" class="btn btn-primary" style="width: 100%;">Resend Verification Email</button>
        </form>
    </div>
</div>
@endsection
