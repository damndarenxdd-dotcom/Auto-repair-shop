@extends('layouts.app')
@section('title', 'Manager Dashboard')
@section('content')
<div class="dashboard-shell">
    <header class="page-header">
        <div>
            <span class="eyebrow">Workflow control</span>
            <h1>Manager dashboard</h1>
            <p>Keep jobs moving, assign work efficiently, and monitor service progress.</p>
        </div>
        <span class="role-badge role-manager">Manager</span>
    </header>

    <section class="stat-grid">
        <div class="stat-card">
            <div>
                <p class="stat-number">{{ $assignedJobs }}</p>
                <p class="stat-label">Assigned</p>
            </div>
            <div class="stat-icon">📋</div>
        </div>
        <div class="stat-card">
            <div>
                <p class="stat-number">{{ $inProgressJobs }}</p>
                <p class="stat-label">In progress</p>
            </div>
            <div class="stat-icon">🛠️</div>
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
                <p class="stat-number">{{ $pendingJobs }}</p>
                <p class="stat-label">Pending</p>
            </div>
            <div class="stat-icon">⏳</div>
        </div>
    </section>

    <section class="panel">
        <div class="panel-header">
            <h2 class="panel-title">Management actions</h2>
        </div>
        <div class="action-grid">
            <a href="{{ route('manager.jobs.index') }}" class="action-card">
                <strong>View jobs</strong>
                <span>Review all scheduled and live repair orders.</span>
            </a>
            <a href="{{ route('manager.jobs.create') }}" class="action-card">
                <strong>Create job</strong>
                <span>Open a new service request and assign it quickly.</span>
            </a>
            <a href="{{ route('manager.invoices.index') }}" class="action-card">
                <strong>Invoices</strong>
                <span>Manage quotes, billing, and payment follow-up.</span>
            </a>
            <a href="{{ route('manager.tasks.dashboard') }}" class="action-card">
                <strong>Task monitoring</strong>
                <span>Review workflow status, queue health, and task history.</span>
            </a>
        </div>
    </section>

    <section class="panel chart-panel">
        <div class="panel-header">
            <h2 class="panel-title">Workload trend</h2>
            <span class="trend trend-positive">Team utilization on track</span>
        </div>
        <div class="chart-wrap">
            <div class="metric-row">
                <div class="mini-metric">
                    <div>
                        <span>Jobs this month</span>
                        <strong>{{ $monthlyPerformance[array_key_last($monthlyPerformance)] ?? 0 }}</strong>
                    </div>
                    <div class="role-icon manager-primary">📦</div>
                </div>
                <div class="mini-metric">
                    <div>
                        <span>Open tasks</span>
                        <strong>{{ $statusSummary['pending'] + $statusSummary['assigned'] + $statusSummary['in-progress'] }}</strong>
                    </div>
                    <div class="role-icon manager-primary">🧭</div>
                </div>
                <div class="mini-metric">
                    <div>
                        <span>Completion</span>
                        <strong>{{ $assignedJobs > 0 ? round(($completedJobs / $assignedJobs) * 100, 1) : 0 }}%</strong>
                    </div>
                    <div class="role-icon manager-primary">🎯</div>
                </div>
            </div>

            <div style="margin-top:20px; height:230px; padding: 16px; border-radius: 18px; background: rgba(255,255,255,0.02); border: 1px solid var(--line);">
                <svg viewBox="0 0 700 190" width="100%" height="100%" aria-label="Workload chart" role="img" style="display:block;">
                    <defs>
                        <linearGradient id="managerWorkloadLine" x1="0" x2="1">
                            <stop offset="0%" stop-color="#60a5fa" />
                            <stop offset="100%" stop-color="#93c5fd" />
                        </linearGradient>
                    </defs>
                    <g stroke="rgba(255,255,255,0.08)" stroke-width="1">
                        <line x1="40" y1="20" x2="660" y2="20"/>
                        <line x1="40" y1="70" x2="660" y2="70"/>
                        <line x1="40" y1="120" x2="660" y2="120"/>
                        <line x1="40" y1="170" x2="660" y2="170"/>
                    </g>
                    <path d="M40 150 C120 95, 160 90, 200 88 S310 74, 360 80 S470 42, 520 52 S600 44, 660 35 L660 170 L40 170 Z" fill="rgba(96,165,250,0.10)"/>
                    <path d="M40 150 C120 95, 160 90, 200 88 S310 74, 360 80 S470 42, 520 52 S600 44, 660 35" fill="none" stroke="url(#managerWorkloadLine)" stroke-width="4" stroke-linecap="round"/>
                    @php $chartIndex = 0; @endphp
                    @foreach($monthlyLabels as $label)
                        <circle cx="{{ 40 + $chartIndex * 120 }}" cy="{{ 170 - ((($monthlyPerformance[$chartIndex] ?? 0) / max(1, max($monthlyPerformance)) * 120)) }}" r="5" fill="#60a5fa"/>
                        @php $chartIndex++; @endphp
                    @endforeach
                    @foreach($monthlyLabels as $label)
                        <text x="{{ 40 + $loop->index * 120 }}" y="185" fill="#a7b8c4" font-size="11" text-anchor="middle">{{ $label }}</text>
                    @endforeach
                </svg>
            </div>

            <div class="chart-legend">
                <span><span class="legend-dot" style="background:#60a5fa;"></span>New jobs</span>
                <span><span class="legend-dot" style="background:#93c5fd;"></span>Scheduling load</span>
            </div>
        </div>
    </section>

    <section class="panel">
        <div class="panel-header">
            <h2 class="panel-title">Job pipeline</h2>
            <span class="trend trend-positive">Capacity balanced</span>
        </div>
        <div class="chart-wrap">
            <div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(170px, 1fr)); gap:14px; margin-top:10px;">
                @foreach(['pending' => 'Pending', 'assigned' => 'Assigned', 'in-progress' => 'In progress', 'completed' => 'Completed'] as $key => $label)
                    <div class="mini-metric">
                        <div>
                            <span>{{ $label }}</span>
                            <strong>{{ $statusSummary[$key] ?? 0 }}</strong>
                        </div>
                        <div class="role-icon manager-primary">{{ ['pending' => '⏳', 'assigned' => '📌', 'in-progress' => '🛠️', 'completed' => '✅'][$key] }}</div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    <section class="panel weather-panel">
        @include('partials.weather_card')
    </section>
</div>
@endsection
