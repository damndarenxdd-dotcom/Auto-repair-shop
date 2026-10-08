@extends('layouts.app')
@section('title', 'Notifications')
@section('content')
<div class="card">
    <h2>Notifications</h2>
    <form method="POST" action="{{ route('customer.notifications.readAll') }}" style="margin-bottom: 1rem;">@csrf<button type="submit" class="btn btn-primary">Mark All as Read</button></form>
    <div>
        @forelse($notifications as $notif)
        <div style="background: {{ $notif->read ? '#f8f9fa' : '#e7f3ff' }}; padding: 1rem; margin: 0.5rem 0; border-left: 4px solid {{ $notif->read ? '#ddd' : '#007bff' }};">
            <h4>{{ $notif->title }}</h4>
            <p>{{ $notif->message }}</p>
            <small>{{ $notif->created_at->format('M d, Y H:i') }}</small>
            @if(!$notif->read)<form method="POST" action="{{ route('customer.notifications.read', $notif) }}" style="display: inline;">@csrf @method('PUT')<button type="submit" class="btn btn-primary">Mark as Read</button></form>@endif
        </div>
        @empty<p>No notifications</p>@endforelse
    </div>
    {{ $notifications->links() }}
</div>
@endsection
