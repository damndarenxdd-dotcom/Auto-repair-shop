<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title') - Auto Repair Shop</title>
    <style>
        :root {
            --garage-950: #0d1117;
            --garage-900: #121b22;
            --garage-800: #1a2a33;
            --garage-700: #223640;
            --garage-600: #2d4752;
            --steel: #d7e3ec;
            --steel-strong: #b8c9d7;
            --accent: #f97316;
            --accent-soft: #ffedd5;
            --accent-strong: #ea580c;
            --success: #22c55e;
            --warning: #f59e0b;
            --danger: #ef4444;
            --body: #e9eef2;
            --card: rgba(15, 23, 30, 0.72);
            --card-strong: #0f1b22;
            --ink: #edf6fb;
            --muted: #a7b8c4;
            --line: rgba(255,255,255,0.09);
            --shadow: 0 20px 40px rgba(2, 6, 23, 0.35);
            --radius: 22px;
        }

        * { box-sizing: border-box; }

        html, body { height: 100%; }

        body {
            margin: 0;
            font-family: Inter, "Segoe UI", sans-serif;
            background:
                radial-gradient(circle at top left, rgba(249,115,22,0.22), transparent 26%),
                linear-gradient(135deg, #091118 0%, #111c24 30%, #0b1720 100%);
            color: var(--ink);
        }

        a { text-decoration: none; }

        .garage-app {
            min-height: 100vh;
            color: var(--ink);
        }

        .app-shell {
            max-width: 1360px;
            margin: 0 auto;
            padding: 20px 24px 42px;
        }

        .app-navbar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            padding: 18px 22px;
            border: 1px solid var(--line);
            border-radius: 18px;
            background: rgba(15, 23, 30, 0.7);
            backdrop-filter: blur(8px);
            box-shadow: var(--shadow);
            margin-bottom: 28px;
        }

        .brand {
            display: flex;
            align-items: center;
            gap: 14px;
            min-width: 0;
        }

        .brand-mark {
            width: 40px;
            height: 40px;
            border-radius: 12px;
            display: grid;
            place-items: center;
            background: linear-gradient(135deg, var(--accent) 0%, var(--accent-strong) 100%);
            box-shadow: 0 12px 20px rgba(249,115,22,0.35);
            overflow: hidden;
        }

        .brand-mark svg {
            width: 24px;
            height: 24px;
            display: block;
            stroke: #fff;
            fill: none;
            stroke-width: 2.2;
            stroke-linecap: round;
            stroke-linejoin: round;
        }

        .brand-copy {
            min-width: 0;
        }

        .brand-title {
            margin: 0;
            font-size: 1.05rem;
            font-weight: 700;
            letter-spacing: 0.04em;
            text-transform: uppercase;
        }

        .brand-subtitle {
            margin: 0;
            font-size: 0.72rem;
            color: var(--muted);
            letter-spacing: 0.12em;
            text-transform: uppercase;
        }

        .nav-group {
            display: flex;
            align-items: center;
            gap: 10px;
            flex-wrap: wrap;
        }

        .user-pill {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            padding: 8px 12px;
            border-radius: 999px;
            background: rgba(255,255,255,0.04);
            border: 1px solid var(--line);
            color: var(--ink);
            font-size: 0.9rem;
        }

        .user-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: var(--success);
            box-shadow: 0 0 0 5px rgba(34,197,94,0.16);
        }

        .btn {
            appearance: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            padding: 0.8rem 1.2rem;
            border-radius: 12px;
            border: 1px solid transparent;
            font-weight: 700;
            font-size: 0.92rem;
            cursor: pointer;
            transition: transform 0.2s ease, box-shadow 0.2s ease, border-color 0.2s ease;
            text-decoration: none;
        }

        .btn:hover {
            transform: translateY(-1px);
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--accent) 0%, var(--accent-strong) 100%);
            color: white;
            box-shadow: 0 16px 30px rgba(249,115,22,0.28);
        }

        .btn-secondary {
            background: rgba(255,255,255,0.04);
            color: var(--ink);
            border-color: var(--line);
        }

        .btn-ghost {
            background: transparent;
            color: var(--ink);
            border-color: var(--line);
        }

        .content-wrap {
            width: min(100%, 1200px);
            margin: 0 auto;
        }

        .panel {
            background: rgba(12, 19, 26, 0.76);
            border: 1px solid var(--line);
            border-radius: var(--radius);
            box-shadow: var(--shadow);
        }

        .dashboard-shell {
            display: flex;
            flex-direction: column;
            gap: 24px;
        }

        .page-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            padding: 26px 28px;
            border-radius: var(--radius);
            background: linear-gradient(135deg, rgba(25, 39, 48, 0.88), rgba(11, 17, 24, 0.9));
            border: 1px solid var(--line);
            box-shadow: var(--shadow);
        }

        .eyebrow {
            display: inline-block;
            font-size: 0.72rem;
            text-transform: uppercase;
            letter-spacing: 0.14em;
            color: var(--accent-soft);
            margin-bottom: 10px;
        }

        .page-header h1 {
            margin: 0;
            font-size: clamp(1.8rem, 2vw, 2.6rem);
            line-height: 1.1;
        }

        .page-header p {
            margin: 8px 0 0;
            color: var(--muted);
        }

        .role-badge {
            padding: 10px 14px;
            border-radius: 999px;
            font-size: 0.72rem;
            text-transform: uppercase;
            letter-spacing: 0.12em;
            font-weight: 700;
            border: 1px solid transparent;
        }

        .role-admin { background: rgba(249,115,22,0.12); border-color: rgba(249,115,22,0.5); color: #ffd4b0; }
        .role-manager { background: rgba(59,130,246,0.14); border-color: rgba(96,165,250,0.5); color: #d8ebff; }
        .role-mechanic { background: rgba(34,197,94,0.12); border-color: rgba(74,222,128,0.5); color: #d8ffe8; }
        .role-customer { background: rgba(168,85,247,0.12); border-color: rgba(192,132,252,0.5); color: #f1d8ff; }

        .stat-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
            gap: 18px;
        }

        .stat-card {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            padding: 20px 22px;
            background: rgba(12, 19, 26, 0.76);
            border: 1px solid var(--line);
            border-radius: 18px;
            box-shadow: var(--shadow);
        }

        .stat-icon {
            width: 52px;
            height: 52px;
            border-radius: 16px;
            display: grid;
            place-items: center;
            background: linear-gradient(135deg, rgba(249,115,22,0.18), rgba(59,130,246,0.16));
            border: 1px solid rgba(255,255,255,0.08);
            font-size: 1.3rem;
        }

        .stat-number {
            margin: 0;
            font-size: clamp(1.8rem, 2vw, 2.4rem);
            line-height: 1;
            font-weight: 800;
        }

        .stat-label {
            margin: 4px 0 0;
            color: var(--muted);
            font-size: 0.82rem;
            text-transform: uppercase;
            letter-spacing: 0.08em;
        }

        .panel-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 12px;
            padding: 20px 22px 0;
        }

        .panel-title {
            margin: 0;
            font-size: 1.15rem;
        }

        .action-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
            gap: 16px;
            padding: 18px 22px 22px;
        }

        .action-card {
            display: block;
            padding: 18px 18px;
            border-radius: 16px;
            background: rgba(17, 24, 39, 0.65);
            border: 1px solid var(--line);
            color: var(--ink);
            transition: transform 0.2s ease, border-color 0.2s ease;
        }

        .action-card:hover {
            transform: translateY(-2px);
            border-color: rgba(249,115,22,0.45);
        }

        .action-card strong {
            display: block;
            margin-bottom: 6px;
            font-size: 1rem;
        }

        .action-card span {
            color: var(--muted);
            font-size: 0.85rem;
        }

        .weather-panel {
            padding: 0;
            overflow: hidden;
        }

        .weather-panel .panel-header {
            padding-bottom: 16px;
        }

        .weather-body {
            padding: 0 22px 22px;
        }

        .weather-summary {
            display: flex;
            align-items: center;
            gap: 18px;
            padding: 18px 0 10px;
        }

        .weather-summary img {
            width: 64px;
            height: 64px;
            filter: drop-shadow(0 10px 18px rgba(250, 204, 21, 0.18));
        }

        .weather-temp {
            font-size: 2.2rem;
            font-weight: 800;
            line-height: 1;
        }

        .weather-meta {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(160px, 1fr));
            gap: 12px;
            margin-top: 4px;
        }

        .weather-meta p {
            margin: 0;
            padding: 12px 14px;
            border-radius: 14px;
            background: rgba(255,255,255,0.03);
            border: 1px solid var(--line);
            color: var(--muted);
        }

        .weather-meta strong {
            color: var(--ink);
        }

        .auth-shell {
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: calc(100vh - 120px);
            padding: 32px 0;
        }

        .auth-panel {
            width: min(100%, 980px);
            display: grid;
            grid-template-columns: 1.05fr 0.95fr;
            background: rgba(12, 19, 26, 0.7);
            border: 1px solid var(--line);
            border-radius: 28px;
            overflow: hidden;
            box-shadow: var(--shadow);
        }

        .auth-hero {
            position: relative;
            padding: 38px 34px 28px;
            background:
                linear-gradient(160deg, rgba(249,115,22,0.2), rgba(15, 23, 30, 0.1)),
                linear-gradient(135deg, #101a21 0%, #172833 100%);
            border-right: 1px solid var(--line);
        }

        .auth-hero::before {
            content: "";
            position: absolute;
            inset: 0;
            background: linear-gradient(180deg, rgba(255,255,255,0.04), transparent 38%);
            pointer-events: none;
        }

        .auth-hero-inner {
            position: relative;
            z-index: 1;
            height: 100%;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            gap: 20px;
        }

        .auth-badge {
            width: fit-content;
            padding: 8px 12px;
            border-radius: 999px;
            background: rgba(255,255,255,0.06);
            border: 1px solid rgba(255,255,255,0.1);
            color: var(--accent-soft);
            text-transform: uppercase;
            letter-spacing: 0.12em;
            font-size: 0.7rem;
            font-weight: 700;
        }

        .auth-hero h1 {
            margin: 0;
            font-size: clamp(2.2rem, 3.7vw, 3.5rem);
            line-height: 1.05;
            letter-spacing: -0.04em;
        }

        .auth-hero p {
            margin: 16px 0 0;
            max-width: 420px;
            color: var(--muted);
            font-size: 1rem;
            line-height: 1.7;
        }

        .auth-points {
            display: grid;
            gap: 12px;
            margin-top: 20px;
        }

        .auth-point {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px 14px;
            border-radius: 14px;
            background: rgba(255,255,255,0.03);
            border: 1px solid rgba(255,255,255,0.06);
            color: var(--ink);
        }

        .auth-point i {
            width: 28px;
            height: 28px;
            display: grid;
            place-items: center;
            border-radius: 10px;
            background: rgba(249,115,22,0.15);
            color: var(--accent-soft);
            font-weight: 700;
            font-style: normal;
        }

        .auth-form-panel {
            padding: 34px 28px;
            background: rgba(9, 14, 19, 0.85);
        }

        .auth-form {
            max-width: 420px;
            margin: 0 auto;
        }

        .form-heading {
            margin-bottom: 18px;
        }

        .form-heading h2 {
            margin: 0 0 6px;
            font-size: clamp(1.8rem, 2vw, 2.4rem);
        }

        .form-heading p {
            margin: 0;
            color: var(--muted);
        }

        .field {
            margin-bottom: 16px;
        }

        .field label {
            display: block;
            margin-bottom: 8px;
            color: var(--ink);
            font-weight: 600;
        }

        .field input {
            width: 100%;
            border: 1px solid rgba(255,255,255,0.1);
            background: rgba(255,255,255,0.02);
            color: var(--ink);
            border-radius: 12px;
            padding: 0.9rem 1rem;
            font: inherit;
            outline: none;
            transition: border-color 0.2s ease, box-shadow 0.2s ease;
        }

        .field input:focus {
            border-color: rgba(249,115,22,0.7);
            box-shadow: 0 0 0 4px rgba(249,115,22,0.12);
        }

        .auth-actions {
            display: flex;
            flex-direction: column;
            gap: 12px;
            margin-top: 10px;
        }

        .auth-link {
            text-align: center;
            color: var(--muted);
            margin-top: 18px;
            font-size: 0.92rem;
        }

        .auth-link a {
            color: var(--accent-soft);
            font-weight: 700;
        }

        .alert {
            padding: 0.9rem 1rem;
            border-radius: 12px;
            margin: 0 0 18px;
            border: 1px solid transparent;
            font-size: 0.92rem;
        }

        .alert-success {
            background: rgba(34,197,94,0.08);
            border-color: rgba(34,197,94,0.35);
            color: #d7ffe3;
        }

        .alert-error {
            background: rgba(239,68,68,0.09);
            border-color: rgba(239,68,68,0.35);
            color: #ffd8d8;
        }

        .table-wrap {
            overflow-x: auto;
        }

        .table {
            width: 100%;
            border-collapse: collapse;
        }

        .table th,
        .table td {
            padding: 12px 10px;
            text-align: left;
            border-bottom: 1px solid rgba(255,255,255,0.08);
        }

        .table th {
            font-size: 0.72rem;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            color: var(--muted);
        }

        .status-pill {
            display: inline-block;
            padding: 6px 10px;
            border-radius: 999px;
            font-size: 0.74rem;
            font-weight: 700;
            text-transform: capitalize;
            background: rgba(59,130,246,0.12);
            border: 1px solid rgba(96,165,250,0.3);
            color: #cfe8ff;
        }

        .status-queued { background: rgba(59,130,246,0.12); border-color: rgba(96,165,250,0.3); color: #dfeeff; }
        .status-processing { background: rgba(245,158,11,0.12); border-color: rgba(245,158,11,0.35); color: #ffe7b3; }
        .status-completed { background: rgba(34,197,94,0.12); border-color: rgba(34,197,94,0.35); color: #d7ffe3; }
        .status-failed { background: rgba(239,68,68,0.12); border-color: rgba(239,68,68,0.35); color: #ffd8d8; }
        .status-cancelled { background: rgba(148,163,184,0.12); border-color: rgba(148,163,184,0.35); color: #e2e8f0; }

        .chart-panel {
            padding: 0;
        }

        .chart-wrap {
            padding: 0 22px 22px;
        }

        .mini-metric {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            padding: 12px 14px;
            border-radius: 14px;
            background: rgba(255,255,255,0.03);
            border: 1px solid var(--line);
            color: var(--muted);
        }

        .mini-metric strong {
            display: block;
            color: var(--ink);
            font-size: 1.2rem;
            margin-top: 2px;
        }

        .chart-legend {
            display: flex;
            align-items: center;
            gap: 14px;
            flex-wrap: wrap;
            margin-top: 8px;
            color: var(--muted);
            font-size: 0.75rem;
            letter-spacing: 0.05em;
            text-transform: uppercase;
        }

        .legend-dot {
            display: inline-block;
            width: 10px;
            height: 10px;
            border-radius: 50%;
            margin-right: 6px;
        }

        .trend {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 8px 10px;
            border-radius: 999px;
            font-weight: 700;
            font-size: 0.76rem;
            letter-spacing: 0.06em;
            text-transform: uppercase;
        }

        .trend-positive {
            background: rgba(34,197,94,0.12);
            color: #d6ffe1;
            border: 1px solid rgba(34,197,94,0.35);
        }

        .trend-warning {
            background: rgba(245,158,11,0.12);
            color: #ffe7b3;
            border: 1px solid rgba(245,158,11,0.35);
        }

        .metric-row {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(190px, 1fr));
            gap: 14px;
            margin-top: 16px;
        }

        .role-icon {
            width: 42px;
            height: 42px;
            border-radius: 13px;
            display: grid;
            place-items: center;
            font-size: 1.2rem;
            background: rgba(255,255,255,0.04);
            border: 1px solid var(--line);
        }

        .admin-primary { background: rgba(249,115,22,0.12); }
        .manager-primary { background: rgba(59,130,246,0.12); }
        .mechanic-primary { background: rgba(34,197,94,0.12); }
        .customer-primary { background: rgba(168,85,247,0.12); }

        @media (max-width: 820px) {
            .app-shell { padding: 16px 14px 30px; }
            .app-navbar { flex-direction: column; align-items: stretch; }
            .brand { justify-content: center; }
            .nav-group { justify-content: center; }
            .auth-panel { grid-template-columns: 1fr; }
            .auth-hero { border-right: none; border-bottom: 1px solid var(--line); }
            .page-header { flex-direction: column; align-items: flex-start; }
        }
    </style>
</head>
<body class="garage-app">
    @auth
    <div class="app-shell">
        <div class="app-navbar">
            <div class="brand">
                <div class="brand-mark">
                    <svg viewBox="0 0 24 24" aria-hidden="true">
                        <path d="M4 14.5 7 9.5h10l3 5"/>
                        <path d="M6 14.5h12v4.5H6z"/>
                        <path d="M8 9.5V6.5h8v3"/>
                        <circle cx="8.5" cy="18" r="1.3"/>
                        <circle cx="15.5" cy="18" r="1.3"/>
                        <path d="M7.5 12.5h9"/>
                    </svg>
                </div>
                <div class="brand-copy">
                    <p class="brand-title">Auto Repair Shop</p>
                    <p class="brand-subtitle">Workshop Management</p>
                </div>
            </div>

            <div class="nav-group">
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
                            <a href="{{ route('admin.dashboard') }}" class="btn btn-primary">Dashboard</a>
                        @elseif($userRole === 'manager')
                            <a href="{{ route('manager.dashboard') }}" class="btn btn-primary">Dashboard</a>
                        @elseif($userRole === 'mechanic')
                            <a href="{{ route('mechanic.dashboard') }}" class="btn btn-primary">Dashboard</a>
                        @elseif($userRole === 'customer')
                            <a href="{{ route('customer.dashboard') }}" class="btn btn-primary">Dashboard</a>
                        @endif
                    @endif
                @endif

                <div class="user-pill">
                    <span class="user-dot"></span>
                    <span>{{ auth()->user()->name }} · {{ ucfirst(auth()->user()->role?->name ?? 'User') }}</span>
                </div>

                <form action="{{ route('logout') }}" method="POST" style="display: inline;">@csrf<button class="btn btn-secondary" type="submit">Logout</button></form>
            </div>
        </div>
    </div>
    @endauth

    <div class="content-wrap">
        @if ($errors->any())
            <div class="alert alert-error"><ul>@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>
        @endif
        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @yield('content')
    </div>
</body>
</html>
