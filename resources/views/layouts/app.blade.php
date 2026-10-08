<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title') - Auto Repair Shop</title>
    <style>
        :root{
            --bg:#f6f8fa;
            --card:#ffffff;
            --muted:#6b7280;
            --primary:#2563eb;
            --danger:#dc2626;
            --surface-shadow: 0 6px 18px rgba(15,23,42,0.06);
            --radius:10px;
        }
        *{box-sizing:border-box;margin:0;padding:0}
        html,body{height:100%}
        body{font-family:Inter,system-ui,-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,Helvetica,Arial; background:linear-gradient(180deg,var(--bg),#eef2f7); color:#0f172a}
        .navbar{background:#0b1220;color:white;padding:0.75rem 1.25rem;box-shadow:var(--surface-shadow);}
        .container{max-width:1200px;margin:0 auto;padding:2rem}
        .card{background:var(--card);border-radius:var(--radius);padding:1.25rem;margin:1rem 0;box-shadow:var(--surface-shadow)}
        .stat-card{padding:1.25rem;text-align:center}
        .stat-card h3{font-size:1.75rem;margin-bottom:0.25rem}
        .stat-card p{color:var(--muted);margin:0}
        .btn{padding:0.5rem 0.9rem;border-radius:8px;border:none;cursor:pointer;text-decoration:none;display:inline-block;font-weight:600}
        .btn-primary{background:var(--primary);color:#fff}
        .btn-danger{background:var(--danger);color:#fff}
        .btn-outline{background:transparent;border:1px solid #e6eef8;color:var(--primary)}
        .form-group{margin:1rem 0}
        label{display:block;margin-bottom:0.5rem;font-weight:600;color:#111827}
        input,textarea,select{width:100%;padding:0.6rem;border:1px solid #e6eef8;border-radius:8px;font-family:inherit}
        .alert{padding:1rem;margin:1rem 0;border-radius:8px}
        .alert-success{background:#ecfdf5;color:#065f46}
        .alert-error{background:#fff1f2;color:#7f1d1d}
        .table{width:100%;border-collapse:collapse}
        .table th,.table td{padding:0.75rem;text-align:left;border-bottom:1px solid #f1f5f9}
        .table th{background:transparent;font-weight:700;color:#111827}
        .dashboard-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(220px,1fr));gap:1rem;margin-bottom:2rem}
        .muted{color:var(--muted)}
        @media (max-width:640px){.container{padding:1rem}.card{padding:1rem}}
    </style>
</head>
<body>
    @auth
    <div class="navbar">
        <div style="display: flex; justify-content: space-between; align-items: center;">
            <h1>Auto Repair Shop</h1>
            <div style="display: flex; align-items: center; gap: 0.75rem; flex-wrap: wrap;">
                @if(auth()->check())
                    @php
                        $userRole = auth()->user()->role?->name;
                    @endphp
                    @if(
                        ($userRole === 'admin' && !request()->routeIs('admin.dashboard')) ||
                        ($userRole === 'manager' && !request()->routeIs('manager.dashboard')) ||
                        ($userRole === 'mechanic' && !request()->routeIs('mechanic.dashboard')) ||
                        ($userRole === 'customer' && !request()->routeIs('customer.dashboard'))
                    )
                        @if($userRole === 'admin')
                            <a href="{{ route('admin.dashboard') }}" class="btn btn-primary">Back to Dashboard</a>
                        @elseif($userRole === 'manager')
                            <a href="{{ route('manager.dashboard') }}" class="btn btn-primary">Back to Dashboard</a>
                        @elseif($userRole === 'mechanic')
                            <a href="{{ route('mechanic.dashboard') }}" class="btn btn-primary">Back to Dashboard</a>
                        @elseif($userRole === 'customer')
                            <a href="{{ route('customer.dashboard') }}" class="btn btn-primary">Back to Dashboard</a>
                        @endif
                    @endif
                @endif
                <span>{{ auth()->user()->name }} ({{ auth()->user()->role?->name ?? 'N/A' }})</span>
                <form action="{{ route('logout') }}" method="POST" style="display: inline;">@csrf<button class="btn btn-primary">Logout</button></form>
            </div>
        </div>
    </div>
    @endauth
    <div class="container">
        @if ($errors->any())<div class="alert alert-error"><ul>@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif
        @if (session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif
        @yield('content')
    </div>
</body>
</html>
