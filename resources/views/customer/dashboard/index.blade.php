@extends('layouts.app')
@section('title', 'Customer Dashboard')
@section('content')
<div class="dashboard-shell">
    <header class="page-header">
        <div>
            <span class="eyebrow">Service account</span>
            <h1>My vehicle services</h1>
            <p>Track repair progress, communications, and invoices for your vehicles.</p>
        </div>
        <span class="role-badge role-customer">Customer</span>
    </header>

    <section class="stat-grid">
        <div class="stat-card">
            <div>
                <p class="stat-number">{{ $totalJobs }}</p>
                <p class="stat-label">Total jobs</p>
            </div>
            <div class="stat-icon">🚘</div>
        </div>
        <div class="stat-card">
            <div>
                <p class="stat-number">{{ $inProgressJobs }}</p>
                <p class="stat-label">In progress</p>
            </div>
            <div class="stat-icon">🔧</div>
        </div>
        <div class="stat-card">
            <div>
                <p class="stat-number">{{ $completedJobs }}</p>
                <p class="stat-label">Completed</p>
            </div>
            <div class="stat-icon">✅</div>
        </div>
        <div class="stat-card">
            <div>
                <p class="stat-number">{{ $unreadNotifications }}</p>
                <p class="stat-label">Notifications</p>
            </div>
            <div class="stat-icon">🔔</div>
        </div>
    </section>

    <section class="panel">
        <div class="panel-header">
            <h2 class="panel-title">Quick actions</h2>
        </div>
        <div class="action-grid">
            <a href="{{ route('customer.jobs.request') }}" class="action-card">
                <strong>Request repair</strong>
                <span>Book a new vehicle service or inspection.</span>
            </a>
            <a href="{{ route('customer.jobs.index') }}" class="action-card">
                <strong>My jobs</strong>
                <span>Review all your current and completed repairs.</span>
            </a>
            <a href="{{ route('customer.notifications.index') }}" class="action-card">
                <strong>Notifications</strong>
                <span>Keep up with workshop updates and progress alerts.</span>
            </a>
            <a href="{{ route('customer.invoices.index') }}" class="action-card">
                <strong>Invoices</strong>
                <span>See billing status and repair payment details.</span>
            </a>
        </div>
    </section>

    <section class="panel weather-panel">
        @include('partials.weather_card')
    </section>
</div>
@endsection
