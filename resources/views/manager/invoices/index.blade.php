@extends('layouts.app')
@section('title', 'Invoices')
@section('content')
<div class="card">
    <h2>Invoices</h2>
    <table class="table">
        <thead>
            <tr><th>Invoice #</th><th>Customer</th><th>Total</th><th>Status</th><th>Actions</th></tr>
        </thead>
        <tbody>
            @forelse($invoices as $invoice)
            <tr>
                <td>{{ $invoice->invoice_number }}</td>
                <td>{{ $invoice->customer->name }}</td>
                <td>${{ number_format($invoice->total, 2) }}</td>
                <td><span style="background: #ffc107; color: white; padding: 0.25rem 0.5rem; border-radius: 4px;">{{ ucfirst($invoice->status) }}</span></td>
                <td>View</td>
            </tr>
            @empty<tr><td colspan="5">No invoices</td></tr>@endforelse
        </tbody>
    </table>
    {{ $invoices->links() }}
</div>
@endsection
