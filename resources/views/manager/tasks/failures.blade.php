@extends('layouts.app')
@section('title', 'Queue Failures')
@section('content')
<div class="dashboard-shell">
    <header class="page-header">
        <div>
            <span class="eyebrow">Ops alert</span>
            <h1>Queue failure reporting</h1>
            <p>Monitor failure spikes and review the job payloads that need attention.</p>
        </div>
        <span class="role-badge role-manager">Manager</span>
    </header>

    <section class="panel">
        <div class="panel-header">
            <h2 class="panel-title">Failed jobs</h2>
        </div>
        <div class="table-wrap">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Connection</th>
                        <th>Queue</th>
                        <th>Failed at</th>
                        <th>Exception</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($failures as $failure)
                        <tr>
                            <td>#{{ $failure->id }}</td>
                            <td>{{ $failure->connection }}</td>
                            <td>{{ $failure->queue }}</td>
                            <td>{{ $failure->failed_at ? \Carbon\Carbon::parse($failure->failed_at)->format('M d, Y H:i') : '—' }}</td>
                            <td style="max-width: 420px; white-space: normal;">{{ Str::limit($failure->exception, 220) }}</td>
                            <td style="white-space:nowrap; text-align:right;">
                                <form method="POST" action="{{ route('manager.tasks.failures.retry', $failure->id) }}" style="display:inline">@csrf
                                    <button class="btn btn-primary" type="submit">Retry</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6">No failed jobs recorded.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="pagination-wrap">
            {{ $failures->links() }}
        </div>
    </section>
</div>
@endsection
