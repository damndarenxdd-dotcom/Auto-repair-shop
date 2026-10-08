@extends('layouts.app')
@section('title', 'Reports')
@section('content')
<div class="card">
    <h2>Admin Reports</h2>
    <nav style="margin-top: 1rem; display: flex; gap: 1rem; flex-wrap: wrap;">
        <a href="{{ route('admin.reports.revenue') }}" class="btn btn-primary">Revenue Report</a>
    </nav>
</div>
@endsection
