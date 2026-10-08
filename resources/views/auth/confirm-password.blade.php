@extends('layouts.app')
@section('title', 'Confirm Password')
@section('content')
<div style="max-width: 400px; margin: 2rem auto;">
    <div class="card">
        <h2>Confirm Password</h2>
        <p>This is a secure area of the application. Please confirm your password before continuing.</p>
        <form method="POST" action="{{ route('password.confirm') }}">@csrf
            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password" required autofocus>
            </div>
            <button type="submit" class="btn btn-primary" style="width: 100%;">Confirm</button>
        </form>
    </div>
</div>
@endsection
