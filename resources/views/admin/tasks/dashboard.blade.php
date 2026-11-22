@extends('layouts.app')
@section('title', 'Task Monitoring')
@section('content')
<div class="dashboard-shell">
    <header class="page-header">
        <div>
            <span class="eyebrow">System operations</span>
            <h1>Task monitoring dashboard</h1>
            <p>Track queued jobs, monitor processing health, and review pipeline failures.</p>
        </div>
        <span class="role-badge role-admin">Admin</span>
    </header>

    <section class="stat-grid">
        <div class="stat-card">
            <div>
                <p class="stat-number">{{ $stats['total'] }}</p>
                <p class="stat-label">Total tasks</p>
            </div>
            <div class="stat-icon">📦</div>
        </div>
        <div class="stat-card">
            <div>
                <p class="stat-number">{{ $stats['queued'] }}</p>
                <p class="stat-label">Queued</p>
            </div>
            <div class="stat-icon">⏳</div>
        </div>
        <div class="stat-card">
            <div>
                <p class="stat-number">{{ $stats['processing'] }}</p>
                <p class="stat-label">Processing</p>
            </div>
            <div class="stat-icon">⚙️</div>
        </div>
        <div class="stat-card">
            <div>
                <p class="stat-number">{{ $stats['completed'] }}</p>
                <p class="stat-label">Completed</p>
            </div>
            <div class="stat-icon">✅</div>
        </div>
        <div class="stat-card">
            <div>
                <p class="stat-number">{{ $stats['failed'] }}</p>
                <p class="stat-label">Failed</p>
            </div>
            <div class="stat-icon">❌</div>
        </div>
    </section>

    <section class="panel">
        <div class="panel-header">
            <h2 class="panel-title">Task queue actions</h2>
        </div>
        <div class="action-grid">
            <a href="{{ route('admin.tasks.history') }}" class="action-card">
                <strong>Task history</strong>
                <span>Review the complete lifecycle for all queued tasks.</span>
            </a>
            <a href="{{ route('admin.tasks.failures') }}" class="action-card">
                <strong>Queue failures</strong>
                <span>Inspect jobs that failed and require intervention.</span>
            </a>
            <a href="{{ route('admin.dashboard') }}" class="action-card">
                <strong>Back to dashboard</strong>
                <span>Return to the main admin operations overview.</span>
            </a>
        </div>
    </section>

    <section class="panel">
        <div class="panel-header">
            <h2 class="panel-title">Recent task activity</h2>
        </div>
        <div class="table-wrap">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Task</th>
                        <th>Type</th>
                        <th>Status</th>
                        <th>Queue</th>
                        <th>Updated</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($recentTasks as $task)
                        <tr>
                            <td>{{ $task->title }}</td>
                            <td>{{ $task->type }}</td>
                            <td><span class="status-pill status-{{ $task->status }}">{{ ucfirst($task->status) }}</span></td>
                            <td>{{ $task->queue_name }}</td>
                            <td>{{ $task->updated_at?->diffForHumans() ?? '—' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5">No tasks have been queued yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>

    <section class="panel">
        <div class="panel-header">
            <h2 class="panel-title">Failed queue summary</h2>
        </div>
        <div class="table-wrap">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Queue</th>
                        <th>Failures</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($failureSummary as $row)
                        <tr>
                            <td>{{ $row->queue }}</td>
                            <td>{{ $row->failures }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="2">No failed jobs recorded.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>
</div>
@endsection
