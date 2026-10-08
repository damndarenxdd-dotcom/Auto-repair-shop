@extends('layouts.app')
@section('title', 'Admin Dashboard')
@section('content')
<div class="dashboard-grid">
    <div class="card stat-card">
        <h3>{{ $totalUsers }}</h3>
        <p>Total Users</p>
    </div>
    <div class="card stat-card">
        <h3>{{ $totalJobs }}</h3>
        <p>Total Jobs</p>
    </div>
    <div class="card stat-card">
        <h3>${{ number_format( $totalRevenue, 2) }}</h3>
        <p>Total Revenue</p>
    </div>
    <div class="card stat-card">
        <h3>{{ $pendingJobs }}</h3>
        <p>Pending Jobs</p>
    </div>
    <div class="card stat-card">
        <h3>{{ $completedJobs }}</h3>
        <p>Completed Jobs</p>
    </div>
    @include('partials.weather_card')
</div>

<div class="card">
    <h2>Admin Panel</h2>
    <nav style="margin-top: 1rem; display: flex; gap: 1rem; flex-wrap: wrap;">
        <a href="{{ route('admin.users.index') }}" class="btn btn-primary">Manage Users</a>
        <a href="{{ route('admin.services.index') }}" class="btn btn-primary">Manage Services</a>
        <a href="{{ route('admin.jobs.index') }}" class="btn btn-primary">View All Jobs</a>
        <a href="{{ route('admin.reports.index') }}" class="btn btn-primary">Reports</a>
    </nav>
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
