@extends('layouts.app')
@section('title', 'Job Details')
@section('content')
<div class="card">
    <h2>Job Details: {{ $job->vehicle_model }}</h2>
    <div style="margin: 2rem 0;">
        <p><strong>License Plate:</strong> {{ $job->license_plate }}</p>
        <p><strong>Status:</strong> <span style="background: #17a2b8; color: white; padding: 0.25rem 0.5rem; border-radius: 4px;">{{ ucfirst($job->status) }}</span></p>
        <p><strong>Mechanic:</strong> <span id="job-mechanic">{{ $job->mechanic?->name ?? 'Not assigned yet' }}</span></p>
        <p><strong>Manager:</strong> <span id="job-manager">{{ $job->manager?->name ?? 'Not assigned yet' }}</span></p>
        <p><strong>Estimated Cost:</strong> ${{ number_format($job->estimated_cost ?? 0, 2) }}</p>
        <p><strong>Description:</strong></p>
        <p>{{ $job->description }}</p>
    </div>
    <a href="{{ route('customer.jobs.index') }}" class="btn btn-primary">Back to Jobs</a>
</div>
@endsection

@section('scripts')
<script>
    (function() {
        const jobId = {{ $job->id }};
        const url = '{{ route('customer.jobs.json', ['id' => $job->id]) }}';
        let lastUpdated = '{{ $job->updated_at?->toDateTimeString() }}';

        async function fetchJob() {
            try {
                const res = await fetch(url, { headers: { 'Accept': 'application/json' } });
                if (!res.ok) return;
                const data = await res.json();
                if (!data) return;

                // If updated_at changed, update displayed fields
                if (data.updated_at && data.updated_at !== lastUpdated) {
                    lastUpdated = data.updated_at;
                    const mechanicEl = document.getElementById('job-mechanic');
                    const managerEl = document.getElementById('job-manager');
                    const statusEls = document.querySelectorAll('p strong');

                    if (mechanicEl) {
                        mechanicEl.textContent = data.mechanic?.name ?? 'Not assigned yet';
                    }
                    if (managerEl) {
                        managerEl.textContent = data.manager?.name ?? 'Not assigned yet';
                    }

                    // Update status badge text
                    const statusBadge = document.querySelector('p span[style*="background: #17a2b8"]');
                    if (statusBadge && data.status) {
                        statusBadge.textContent = data.status.charAt(0).toUpperCase() + data.status.slice(1);
                    }
                }
            } catch (e) {
                // silent
            }
        }

        // Poll every 5 seconds
        setInterval(fetchJob, 5000);
    })();
</script>
@endsection
