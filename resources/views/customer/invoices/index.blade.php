@extends('layouts.app')
@section('title', 'Invoices')
@section('content')
<div class="card">
    <h2>My Invoices</h2>
    <table class="table">
        <thead>
            <tr><th>Invoice #</th><th>Amount</th><th>Status</th><th>Due Date</th><th>Actions</th></tr>
        </thead>
        <tbody>
            @forelse($invoices as $inv)
            <tr>
                <td>{{ $inv->invoice_number }}</td>
                <td>${{ number_format($inv->total, 2) }}</td>
                <td><span style="background: #ffc107; color: white; padding: 0.25rem 0.5rem; border-radius: 4px;">{{ ucfirst($inv->status) }}</span></td>
                <td>{{ $inv->due_date?->format('M d, Y') ?? 'N/A' }}</td>
                <td><a href="{{ route('customer.invoices.show', $inv) }}" class="btn btn-primary">View</a></td>
            </tr>
            @empty<tr><td colspan="5">No invoices</td></tr>@endforelse
        </tbody>
    </table>
    {{ $invoices->links() }}
</div>
@endsection
