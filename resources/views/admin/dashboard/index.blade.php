@extends('layouts.app')
@section('title', 'Admin Dashboard')
@section('content')
<div class="dashboard-shell">
    <header class="page-header">
        <div>
            <span class="eyebrow">Operations overview</span>
            <h1>Admin control center</h1>
            <p>Monitor service demand, revenue, workforce, and workshop activity in one place.</p>
        </div>
        <span class="role-badge role-admin">Admin</span>
    </header>

    <section class="stat-grid">
        <div class="stat-card">
            <div>
                <p class="stat-number">{{ $totalUsers }}</p>
                <p class="stat-label">Users</p>
            </div>
            <div class="stat-icon">👥</div>
        </div>
        <div class="stat-card">
            <div>
                <p class="stat-number">{{ $totalJobs }}</p>
                <p class="stat-label">Jobs</p>
            </div>
            <div class="stat-icon">🔧</div>
        </div>
        <div class="stat-card">
            <div>
                <p class="stat-number">${{ number_format($totalRevenue, 2) }}</p>
                <p class="stat-label">Revenue</p>
            </div>
            <div class="stat-icon">💰</div>
        </div>
        <div class="stat-card">
            <div>
                <p class="stat-number">{{ $pendingJobs }}</p>
                <p class="stat-label">Pending</p>
            </div>
            <div class="stat-icon">⏳</div>
        </div>
        <div class="stat-card">
            <div>
                <p class="stat-number">{{ $completedJobs }}</p>
                <p class="stat-label">Completed</p>
            </div>
            <div class="stat-icon">✅</div>
        </div>
    </section>

    <section class="panel">
        <div class="panel-header">
            <h2 class="panel-title">Workshop actions</h2>
        </div>
        <div class="action-grid">
            <a href="{{ route('admin.users.index') }}" class="action-card">
                <strong>Manage users</strong>
                <span>Update staff, customer, and role access.</span>
            </a>
            <a href="{{ route('admin.services.index') }}" class="action-card">
                <strong>Services</strong>
                <span>Review and maintain available repair services.</span>
            </a>
            <a href="{{ route('admin.jobs.index') }}" class="action-card">
                <strong>All jobs</strong>
                <span>Track active repair throughput across the shop.</span>
            </a>
            <a href="{{ route('admin.reports.index') }}" class="action-card">
                <strong>Reports</strong>
                <span>Review productivity, revenue and operational trends.</span>
            </a>
            <a href="{{ route('admin.tasks.dashboard') }}" class="action-card">
                <strong>Task monitoring</strong>
                <span>Inspect queue health, current task status, and task history.</span>
            </a>
        </div>
    </section>

    <section class="panel chart-panel">
        <div class="panel-header">
            <h2 class="panel-title">Revenue trend</h2>
            <span class="trend trend-positive">+12.4% vs prior period</span>
        </div>
        <div class="chart-wrap">
            <div class="metric-row">
                <div class="mini-metric">
                    <div>
                        <span>Monthly revenue</span>
                        <strong>${{ number_format($monthlyRevenue[array_key_last($monthlyRevenue)] ?? 0, 2) }}</strong>
                    </div>
                    <div class="role-icon admin-primary">💸</div>
                </div>
                <div class="mini-metric">
                    <div>
                        <span>Open jobs</span>
                        <strong>{{ $pendingJobs + $jobBreakdown['assigned'] + $jobBreakdown['in-progress'] }}</strong>
                    </div>
                    <div class="role-icon admin-primary">📊</div>
                </div>
                <div class="mini-metric">
                    <div>
                        <span>Completion rate</span>
                        <strong>{{ $totalJobs > 0 ? round(($completedJobs / $totalJobs) * 100, 1) : 0 }}%</strong>
                    </div>
                    <div class="role-icon admin-primary">✅</div>
                </div>
            </div>

            <div style="margin-top:20px; height:230px; padding: 16px; border-radius: 18px; background: rgba(255,255,255,0.02); border: 1px solid var(--line);">
                <svg viewBox="0 0 700 190" width="100%" height="100%" aria-label="Revenue chart" role="img" style="display:block;">
                    <defs>
                        <linearGradient id="adminRevenueLine" x1="0" x2="1">
                            <stop offset="0%" stop-color="#f97316" />
                            <stop offset="100%" stop-color="#fdba74" />
                        </linearGradient>
                    </defs>
                    <g stroke="rgba(255,255,255,0.08)" stroke-width="1">
                        <line x1="40" y1="20" x2="660" y2="20"/>
                        <line x1="40" y1="70" x2="660" y2="70"/>
                        <line x1="40" y1="120" x2="660" y2="120"/>
                        <line x1="40" y1="170" x2="660" y2="170"/>
                    </g>
                    <path d="M40 150 C120 110, 160 110, 200 100 S310 50, 360 72 S470 30, 520 54 S600 28, 660 30 L660 170 L40 170 Z" fill="rgba(249,115,22,0.10)"/>
                    <path d="M40 150 C120 110, 160 110, 200 100 S310 50, 360 72 S470 30, 520 54 S600 28, 660 30" fill="none" stroke="url(#adminRevenueLine)" stroke-width="4" stroke-linecap="round"/>
                    @php $chartIndex = 0; @endphp
                    @foreach($monthlyLabels as $label)
                        <circle cx="{{ 40 + $chartIndex * 120 }}" cy="{{ 170 - ((($monthlyRevenue[$chartIndex] ?? 0) / max(1, max($monthlyRevenue)) * 120)) }}" r="5" fill="#f97316"/>
                        @php $chartIndex++; @endphp
                    @endforeach
                    @foreach($monthlyLabels as $label)
                        <text x="{{ 40 + $loop->index * 120 }}" y="185" fill="#a7b8c4" font-size="11" text-anchor="middle">{{ $label }}</text>
                    @endforeach
                </svg>
            </div>

            <div class="chart-legend">
                <span><span class="legend-dot" style="background:#f97316;"></span>Revenue</span>
                <span><span class="legend-dot" style="background:#fdba74;"></span>Service growth</span>
            </div>
        </div>
    </section>

    <section class="panel">
        <div class="panel-header">
            <h2 class="panel-title">Job mix</h2>
            <span class="trend trend-warning">Live shop demand</span>
        </div>
        <div class="chart-wrap">
            <div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(170px, 1fr)); gap:14px; margin-top:10px;">
                @foreach(['pending' => 'Pending', 'assigned' => 'Assigned', 'in-progress' => 'In progress', 'completed' => 'Completed'] as $key => $label)
                    <div class="mini-metric">
                        <div>
                            <span>{{ $label }}</span>
                            <strong>{{ $jobBreakdown[$key] ?? 0 }}</strong>
                        </div>
                        <div class="role-icon admin-primary">{{ ['pending' => '⏳', 'assigned' => '📌', 'in-progress' => '🛠️', 'completed' => '✅'][$key] }}</div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    <section class="panel weather-panel">
        @include('partials.weather_card')
    </section>
</div>
@endsection

    <script>
    (function(){
        const url = "{{ route('admin.weather') }}";
        const contentEl = document.getElementById('weather-content');
        const spinnerEl = document.getElementById('weather-spinner');
        const lastUpdatedEl = document.getElementById('weather-last-updated');
        const UNIT_KEY = 'weather_unit';
        const STORAGE_KEY = 'weather_live_updates';
        const unitToggleId = 'weather-unit-toggle';
        const liveToggleId = 'weather-live-toggle';
        const prefGetUrl = "{{ route('admin.weather.preferences.get') }}";
        const prefSaveUrl = "{{ route('admin.weather.preferences.save') }}";
        let intervalId = null;

        // Load server-side preferences (if authenticated) and apply
        async function loadPreferences(){
            try{
                const res = await fetch(prefGetUrl, { credentials: 'same-origin', headers: { 'X-Requested-With': 'XMLHttpRequest' } });
                if (!res.ok) return applyLocalPreferences();
                const json = await res.json();
                if (json.success){
                    const unit = json.weather_unit || localStorage.getItem(UNIT_KEY) || 'C';
                    localStorage.setItem(UNIT_KEY, unit);
                    const live = (typeof json.weather_live_updates !== 'undefined') ? (json.weather_live_updates ? '1' : '0') : localStorage.getItem(STORAGE_KEY);
                    if (live === null) localStorage.setItem(STORAGE_KEY, '1'); else localStorage.setItem(STORAGE_KEY, live);
                }
            } catch (e) {
                // ignore and fall back to local
            } finally {
                applyLocalPreferences();
            }
        }

        function applyLocalPreferences(){
            const unitEl = document.getElementById(unitToggleId);
            const liveEl = document.getElementById(liveToggleId);
            const unit = localStorage.getItem(UNIT_KEY) || 'C';
            unitEl && (unitEl.checked = unit === 'F');
            liveEl && (liveEl.checked = localStorage.getItem(STORAGE_KEY) !== '0');
            updateDisplayedUnits();
            if (localStorage.getItem(STORAGE_KEY) !== '0') startPolling();
            else stopPolling();
        }

        async function updateWeather(){
            try{
                spinnerEl.style.display = 'block';
                const res = await fetch(url, { cache: 'no-store' });
                if (!res.ok) throw new Error('No weather');
                const json = await res.json();
                if (json.success && json.weather){
                    const w = json.weather;
                    // update fields inside weather-content instead of replacing full container so toggles remain
                    // update icon if present
                    const iconEl = document.getElementById('weather-icon');
                    if (iconEl && w.icon) iconEl.src = `https://openweathermap.org/img/wn/${w.icon}@2x.png`;

                    // update values (create or update elements)
                    document.querySelector('.weather-temp')?.setAttribute('data-c', w.temperature ?? '');
                    document.querySelector('.weather-feels')?.setAttribute('data-c', w.feels_like ?? '');
                    document.querySelector('.weather-min')?.setAttribute('data-c', w.temp_min ?? '');
                    document.querySelector('.weather-max')?.setAttribute('data-c', w.temp_max ?? '');
                    document.querySelector('.weather-wind') && (document.querySelector('.weather-wind').textContent = (w.wind_speed ?? 'N/A'));
                    // numeric wind degrees element
                    const windDegEl = document.querySelector('.weather-wind-dir');
                    if (windDegEl) {
                        windDegEl.setAttribute('data-deg', w.wind_deg ?? '');
                        windDegEl.textContent = (w.wind_deg ?? '') + (w.wind_deg ? '°' : '');
                    }
                    // show server-provided wind label if available
                    const windLabelEl = contentEl.querySelector('.weather-wind-label');
                    if (windLabelEl) {
                        if (w.wind_label) windLabelEl.textContent = `(${w.wind_label})`;
                        else windLabelEl.textContent = '';
                    }
                    // sunrise/sunset (prefer server-formatted strings)
                    document.querySelector('.weather-sunrise') && (document.querySelector('.weather-sunrise').textContent = w.sunrise ?? '');
                    document.querySelector('.weather-sunset') && (document.querySelector('.weather-sunset').textContent = w.sunset ?? '');

                    // update location and condition text
                    const pEls = Array.from(contentEl.querySelectorAll('p'));
                    const locationP = pEls.find(p => p.textContent.trim().startsWith('Location'));
                    if (locationP) locationP.innerHTML = `<strong>Location:</strong> ${w.city ?? ''}`;
                    const conditionP = pEls.find(p => p.textContent.trim().startsWith('Condition'));
                    if (conditionP) conditionP.innerHTML = `<strong>Condition:</strong> ${w.description ? (w.description.charAt(0).toUpperCase()+w.description.slice(1)) : 'N/A'}`;

                    // update last-updated and convert units/display
                    if (lastUpdatedEl) lastUpdatedEl.textContent = w.fetched_at ?? '';

                    updateDisplayedUnits();
                } else {
                    document.getElementById('weather-fallback') && (document.getElementById('weather-fallback').textContent = (json.message ?? 'Weather information is currently unavailable.'));
                }
            } catch (e){
                document.getElementById('weather-fallback') && (document.getElementById('weather-fallback').textContent = 'Weather information is currently unavailable.');
            } finally {
                spinnerEl.style.display = 'none';
            }
        }

        function startPolling(){
            if (intervalId) return; // already polling
            // immediate update then interval
            updateWeather();
            intervalId = setInterval(updateWeather, 60000);
        }

        function stopPolling(){
            if (intervalId) {
                clearInterval(intervalId);
                intervalId = null;
            }
        }

        // Unit toggle (C/F)
        function getUnit(){
            const u = localStorage.getItem(UNIT_KEY);
            return u === 'F' ? 'F' : 'C';
        }

        function setUnit(u){
            localStorage.setItem(UNIT_KEY, u);
            updateDisplayedUnits();
        }

        function cToF(c){ return (c * 9/5 + 32).toFixed(1); }

        function degToCompass(num) {
            if (num === null || num === undefined || num === '') return '';
            const val = Math.floor((num / 22.5) + 0.5);
            const arr = ["N","NNE","NE","ENE","E","ESE","SE","SSE","S","SSW","SW","WSW","W","WNW","NW","NNW"];
            return arr[(val % 16)];
        }

        function updateDisplayedUnits(){
            const unit = getUnit();
            document.querySelectorAll('.weather-unit').forEach(el => el.textContent = unit === 'F' ? '°F' : '°C');
            // convert temps using data-c attributes if present
            ['.weather-temp', '.weather-feels', '.weather-min', '.weather-max'].forEach(selector => {
                document.querySelectorAll(selector).forEach(el => {
                    const cStr = el.getAttribute('data-c');
                    if (!cStr) return;
                    const num = parseFloat(cStr);
                    if (isNaN(num)) return;
                    el.textContent = unit === 'F' ? cToF(num) : parseFloat(num).toFixed(1);
                    // color temperature (only for main temp)
                    if (selector === '.weather-temp') {
                        const n = unit === 'F' ? parseFloat(cToF(num)) : num;
                        el.style.color = n <= 5 ? '#2b9cff' : (n <= 20 ? '#3498db' : '#e74c3c');
                    }
                });
            });
            // update wind label
            document.querySelectorAll('.weather-wind-dir').forEach(el => {
                const deg = el.getAttribute('data-deg');
                const label = deg ? degToCompass(parseFloat(deg)) : '';
                const labelEl = el.parentElement.querySelector('.weather-wind-label');
                if (labelEl) labelEl.textContent = label ? `(${label})` : '';
            });
            // rotate icon based on wind_deg
            const icon = document.getElementById('weather-icon');
            const windDegEl = document.querySelector('.weather-wind-dir');
            if (icon && windDegEl) {
                const deg = parseFloat(windDegEl.getAttribute('data-deg') || 0);
                icon.style.transform = `rotate(${deg}deg)`;
            }
        }

        // add unit toggle UI and live update toggle
        const controlsLabel = document.createElement('div');
        controlsLabel.style.cssText = 'display:flex;gap:0.75rem;align-items:center;margin-left:0.75rem;';
        controlsLabel.innerHTML = `
            <label style="font-size:0.9rem;display:flex;align-items:center;gap:0.5rem;"><input type="checkbox" id="${unitToggleId}" /> <span>Celsius / Fahrenheit</span></label>
            <label style="font-size:0.9rem;display:flex;align-items:center;gap:0.5rem;"><input type="checkbox" id="${liveToggleId}" /> <span>Live Updates</span></label>
        `;
        document.querySelector('#weather-card > div').appendChild(controlsLabel);

        const unitEl = document.getElementById(unitToggleId);
        const liveEl = document.getElementById(liveToggleId);
        unitEl.checked = getUnit() === 'F';
        unitEl.addEventListener('change', async () => {
            const unit = unitEl.checked ? 'F' : 'C';
            setUnit(unit);
            // try save to server
            try{
                await fetch(prefSaveUrl, {
                    method: 'POST',
                    credentials: 'same-origin',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: JSON.stringify({ weather_unit: unit })
                });
            } catch (e) {}
        });

        liveEl.addEventListener('change', async () => {
            const enabled = liveEl.checked;
            localStorage.setItem(STORAGE_KEY, enabled ? '1' : '0');
            if (enabled) startPolling(); else stopPolling();
            // save preference server-side
            try{
                await fetch(prefSaveUrl, {
                    method: 'POST',
                    credentials: 'same-origin',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: JSON.stringify({ weather_live_updates: enabled ? 1 : 0 })
                });
            } catch (e) {}
        });

        // Load and apply preferences then start polling as appropriate
        await loadPreferences();
    })();
    </script>
