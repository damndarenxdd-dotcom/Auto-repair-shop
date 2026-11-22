@extends('layouts.app')
@section('title', 'Task History')
@section('content')
<div class="dashboard-shell">
    <header class="page-header">
        <div>
            <span class="eyebrow">Audit trail</span>
            <h1>Task history</h1>
            <p>Review all queued workflow activity for the workshop system.</p>
        </div>
        <span class="role-badge role-admin">Admin</span>
    </header>

    <section class="panel">
        <div class="panel-header">
            <h2 class="panel-title">All task records</h2>
        </div>
        <div class="table-wrap">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Title</th>
                        <th>Type</th>
                        <th>Status</th>
                        <th>Queue</th>
                        <th>Created</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($tasks as $task)
                        <tr>
                            <td>#{{ $task->id }}</td>
                            <td>{{ $task->title }}</td>
                            <td>{{ $task->type }}</td>
                            <td><span class="status-pill status-{{ $task->status }}">{{ ucfirst($task->status) }}</span></td>
                            <td>{{ $task->queue_name }}</td>
                            <td>{{ $task->created_at?->format('M d, Y H:i') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6">No task records yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="pagination-wrap">
            {{ $tasks->links() }}
        </div>
    </section>
</div>
@endsection
