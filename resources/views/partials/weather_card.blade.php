<div class="weather-panel" id="weather-card" style="border-radius: 22px; background: rgba(11,17,24,0.8); border: 1px solid rgba(255,255,255,0.08); overflow: hidden; box-shadow: 0 20px 40px rgba(2,6,23,0.28);">
    <div class="panel-header" style="padding: 18px 22px 0;">
        <h3 class="panel-title" style="margin:0;">Workshop weather</h3>
    </div>
    <div id="weather-content" class="weather-body" style="padding: 0 22px 22px;">
        <div id="weather-spinner" style="display:none;margin-bottom:0.8rem;color:#dfeaf2; font-size:0.85rem;">Loading weather...</div>

        @if(isset($weather) && $weather)
            <div class="weather-summary">
                @if(!empty($weather['icon']))
                    <img id="weather-icon" src="https://openweathermap.org/img/wn/{{ $weather['icon'] }}@2x.png" alt="weather icon" />
                @endif
                <div>
                    <p style="margin:0; color: var(--muted); font-size: 0.8rem; text-transform: uppercase; letter-spacing: 0.1em;">Location</p>
                    <p style="margin: 4px 0 0; font-size: 1.1rem; font-weight: 700;">
                        <span id="weather-location-text">{{ $weather['city'] }}</span>
                        <button id="weather-location-edit" style="background: transparent; border: none; color: #ffb37d; font-weight: 700; cursor: pointer; margin-left: 0.5rem;">Edit</button>
                    </p>
                </div>
            </div>

            <div class="weather-summary" style="padding-top:0; align-items:flex-end;">
                <div class="weather-temp" data-c="{{ $weather['temperature'] ?? '' }}">{{ $weather['temperature'] ?? 'N/A' }}</div>
                <span class="weather-unit" style="font-size: 1rem; color: var(--muted);">°C</span>
            </div>

            <div class="weather-meta" style="margin-top: 18px;">
                <p><strong>Condition:</strong> {{ ucfirst($weather['description'] ?? 'N/A') }}</p>
                <p><strong>Feels like:</strong> <span class="weather-feels" data-c="{{ $weather['feels_like'] ?? '' }}">{{ $weather['feels_like'] ?? 'N/A' }}</span> <span class="weather-unit">°C</span></p>
                <p><strong>Min / Max:</strong> <span class="weather-min" data-c="{{ $weather['temp_min'] ?? '' }}">{{ $weather['temp_min'] ?? 'N/A' }}</span>° / <span class="weather-max" data-c="{{ $weather['temp_max'] ?? '' }}">{{ $weather['temp_max'] ?? 'N/A' }}</span>°</p>
                <p><strong>Humidity:</strong> {{ $weather['humidity'] ?? 'N/A' }}%</p>
                <p><strong>Wind:</strong> <span class="weather-wind">{{ $weather['wind_speed'] ?? 'N/A' }}</span> m/s <span class="weather-wind-dir" data-deg="{{ $weather['wind_deg'] ?? '' }}">{{ $weather['wind_deg'] ?? '' }}</span><span class="weather-wind-label"></span></p>
                @if(!empty($weather['sunrise']) && !empty($weather['sunset']))
                    <p><strong>Sunrise:</strong> <span class="weather-sunrise">{{ $weather['sunrise'] }}</span></p>
                    <p><strong>Sunset:</strong> <span class="weather-sunset">{{ $weather['sunset'] }}</span></p>
                @endif
                <p style="grid-column: 1 / -1; margin-top: 4px; font-size: 0.78rem; color: var(--muted);">Last updated: <span id="weather-last-updated">{{ $weather['fetched_at'] ?? '' }}</span></p>
            </div>
        @else
            <p id="weather-fallback" style="margin:0; padding: 18px 0 0; color: var(--muted);">{{ $weather_error ?? 'Weather information is currently unavailable.' }}</p>
        @endif
    </div>
</div>

<script>
(function(){
    const url = "{{ route('weather.live') }}";
    const contentEl = document.getElementById('weather-content');
    const spinnerEl = document.getElementById('weather-spinner');
    const lastUpdatedEl = document.getElementById('weather-last-updated');
    const UNIT_KEY = 'weather_unit';
    const STORAGE_KEY = 'weather_live_updates';
    const unitToggleId = 'weather-unit-toggle';
    const liveToggleId = 'weather-live-toggle';
    const prefGetUrl = "{{ route('weather.preferences.get') }}";
    const prefSaveUrl = "{{ route('weather.preferences.save') }}";
    let intervalId = null;
    const locationTextEl = document.getElementById('weather-location-text');
    const locationEditBtn = document.getElementById('weather-location-edit');
    const CITY_KEY = 'weather_city_selected';

    function getSelectedCity(){ return localStorage.getItem(CITY_KEY) || ''; }

    function applySelectedCity(city){
        if (!city) return;
        localStorage.setItem(CITY_KEY, city);
        if (locationTextEl) locationTextEl.textContent = city;
    }

    function getWeatherRequestUrl(cityOverride = null){
        const selectedCity = cityOverride ?? getSelectedCity();
        if (!selectedCity) return url;
        return `${url}?city=${encodeURIComponent(selectedCity)}`;
    }

    if (locationEditBtn) {
        locationEditBtn.addEventListener('click', () => {
            const current = locationTextEl ? locationTextEl.textContent.trim() : '';
            const input = document.createElement('input');
            input.type = 'text';
            input.value = current;
            input.placeholder = 'Enter city';
            input.style.padding = '0.45rem 0.6rem';
            input.style.marginLeft = '0.5rem';
            input.style.borderRadius = '8px';
            input.style.border = '1px solid rgba(255,255,255,0.1)';
            input.style.background = 'rgba(255,255,255,0.02)';
            input.style.color = '#edf6fb';

            const saveBtn = document.createElement('button');
            saveBtn.textContent = 'Save';
            saveBtn.className = 'btn btn-primary';
            saveBtn.style.marginLeft = '0.5rem';
            const cancelBtn = document.createElement('button');
            cancelBtn.textContent = 'Cancel';
            cancelBtn.className = 'btn btn-ghost';
            cancelBtn.style.marginLeft = '0.5rem';

            locationEditBtn.style.display = 'none';
            locationTextEl.style.display = 'none';
            locationEditBtn.parentElement.appendChild(input);
            locationEditBtn.parentElement.appendChild(saveBtn);
            locationEditBtn.parentElement.appendChild(cancelBtn);

            cancelBtn.addEventListener('click', () => {
                input.remove(); saveBtn.remove(); cancelBtn.remove();
                locationEditBtn.style.display = ''; locationTextEl.style.display = '';
            });

            saveBtn.addEventListener('click', async () => {
                const v = input.value.trim();
                if (!v) return;
                try {
                    await fetch(prefSaveUrl, {
                        method: 'POST',
                        credentials: 'same-origin',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        body: JSON.stringify({ weather_city: v })
                    });
                } catch (e) {}

                if (locationTextEl) locationTextEl.textContent = v;
                localStorage.setItem(CITY_KEY, v);
                input.remove(); saveBtn.remove(); cancelBtn.remove();
                locationEditBtn.style.display = ''; locationTextEl.style.display = '';
                try { await fetch(getWeatherRequestUrl(v), { cache: 'no-store' }); } catch (e) {}
                updateWeather();
            });
        });
    }

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
                if (json.weather_city) {
                    localStorage.setItem(CITY_KEY, json.weather_city);
                    if (locationTextEl) locationTextEl.textContent = json.weather_city;
                }
            }
        } catch (e) {
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
        if (localStorage.getItem(STORAGE_KEY) !== '0') startPolling(); else stopPolling();
    }

    async function updateWeather(){
        try{
            spinnerEl.style.display = 'block';
            const requestUrl = getWeatherRequestUrl();
            const res = await fetch(requestUrl, { cache: 'no-store' });
            if (!res.ok) throw new Error('No weather');
            const json = await res.json();
            if (json.success && json.weather){
                const w = json.weather;
                const iconEl = document.getElementById('weather-icon');
                if (iconEl && w.icon) iconEl.src = `https://openweathermap.org/img/wn/${w.icon}@2x.png`;
                document.querySelector('.weather-temp')?.setAttribute('data-c', w.temperature ?? '');
                document.querySelector('.weather-feels')?.setAttribute('data-c', w.feels_like ?? '');
                document.querySelector('.weather-min')?.setAttribute('data-c', w.temp_min ?? '');
                document.querySelector('.weather-max')?.setAttribute('data-c', w.temp_max ?? '');
                document.querySelector('.weather-wind') && (document.querySelector('.weather-wind').textContent = (w.wind_speed ?? 'N/A'));
                const windDegEl = document.querySelector('.weather-wind-dir');
                if (windDegEl) { windDegEl.setAttribute('data-deg', w.wind_deg ?? ''); windDegEl.textContent = (w.wind_deg ?? '') + (w.wind_deg ? '°' : ''); }
                const windLabelEl = contentEl.querySelector('.weather-wind-label'); if (windLabelEl) windLabelEl.textContent = w.wind_label ? `(${w.wind_label})` : '';
                document.querySelector('.weather-sunrise') && (document.querySelector('.weather-sunrise').textContent = w.sunrise ?? '');
                document.querySelector('.weather-sunset') && (document.querySelector('.weather-sunset').textContent = w.sunset ?? '');
                if (locationTextEl) locationTextEl.textContent = w.city ?? getSelectedCity();
                applySelectedCity(w.city ?? getSelectedCity());
                const conditionP = Array.from(contentEl.querySelectorAll('p')).find(p => p.textContent.trim().startsWith('Condition'));
                if (conditionP) conditionP.innerHTML = `<strong>Condition:</strong> ${w.description ? (w.description.charAt(0).toUpperCase()+w.description.slice(1)) : 'N/A'}`;
                if (lastUpdatedEl) lastUpdatedEl.textContent = w.fetched_at ?? '';
                updateDisplayedUnits();
            } else {
                document.getElementById('weather-fallback') && (document.getElementById('weather-fallback').textContent = (json.message ?? 'Weather information is currently unavailable.'));
            }
        } catch (e){
            document.getElementById('weather-fallback') && (document.getElementById('weather-fallback').textContent = 'Weather information is currently unavailable.');
        } finally { spinnerEl.style.display = 'none'; }
    }

    function startPolling(){ if (intervalId) return; updateWeather(); intervalId = setInterval(updateWeather, 60000); }
    function stopPolling(){ if (intervalId) { clearInterval(intervalId); intervalId = null; } }
    function getUnit(){ const u = localStorage.getItem(UNIT_KEY); return u === 'F' ? 'F' : 'C'; }
    function setUnit(u){ localStorage.setItem(UNIT_KEY, u); updateDisplayedUnits(); }
    function cToF(c){ return (c * 9/5 + 32).toFixed(1); }
    function degToCompass(num){ if (num === null || num === undefined || num === '') return ''; const val = Math.floor((num / 22.5) + 0.5); const arr = ["N","NNE","NE","ENE","E","ESE","SE","SSE","S","SSW","SW","WSW","W","WNW","NW","NNW"]; return arr[(val % 16)]; }

    function updateDisplayedUnits(){
        const unit = getUnit();
        document.querySelectorAll('.weather-unit').forEach(el => el.textContent = unit === 'F' ? '°F' : '°C');
        ['.weather-temp','.weather-feels','.weather-min','.weather-max'].forEach(selector => {
            document.querySelectorAll(selector).forEach(el => {
                const cStr = el.getAttribute('data-c'); if (!cStr) return; const num = parseFloat(cStr); if (isNaN(num)) return;
                el.textContent = unit === 'F' ? cToF(num) : parseFloat(num).toFixed(1);
                if (selector === '.weather-temp') { const n = unit === 'F' ? parseFloat(cToF(num)) : num; el.style.color = n <= 5 ? '#8fe3ff' : (n <= 20 ? '#7ab9ff' : '#ffb37d'); }
            });
        });
        document.querySelectorAll('.weather-wind-dir').forEach(el => { const deg = el.getAttribute('data-deg'); const label = deg ? degToCompass(parseFloat(deg)) : ''; const labelEl = el.parentElement.querySelector('.weather-wind-label'); if (labelEl) labelEl.textContent = label ? `(${label})` : ''; });
        const icon = document.getElementById('weather-icon'); const windDegEl = document.querySelector('.weather-wind-dir'); if (icon && windDegEl) { const deg = parseFloat(windDegEl.getAttribute('data-deg') || 0); icon.style.transform = `rotate(${deg}deg)`; }
    }

    const controlsLabel = document.createElement('div');
    controlsLabel.style.cssText = 'display:flex;flex-wrap:wrap;gap:0.75rem;align-items:center;margin-top:18px;';
    controlsLabel.innerHTML = `
        <label style="font-size:0.82rem;display:flex;align-items:center;gap:0.5rem;color:#dfeaf2;"><input type="checkbox" id="${unitToggleId}" /> <span>°C / °F</span></label>
        <label style="font-size:0.82rem;display:flex;align-items:center;gap:0.5rem;color:#dfeaf2;"><input type="checkbox" id="${liveToggleId}" /> <span>Live updates</span></label>
    `;
    document.getElementById('weather-content').appendChild(controlsLabel);

    const unitEl = document.getElementById(unitToggleId);
    const liveEl = document.getElementById(liveToggleId);
    unitEl.checked = getUnit() === 'F';
    unitEl.addEventListener('change', async () => {
        const unit = unitEl.checked ? 'F' : 'C'; setUnit(unit);
        try{ await fetch(prefSaveUrl, { method: 'POST', credentials: 'same-origin', headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'), 'X-Requested-With': 'XMLHttpRequest' }, body: JSON.stringify({ weather_unit: unit }) }); } catch (e) {}
    });

    liveEl.addEventListener('change', async () => {
        const enabled = liveEl.checked; localStorage.setItem(STORAGE_KEY, enabled ? '1' : '0'); if (enabled) startPolling(); else stopPolling();
        try{ await fetch(prefSaveUrl, { method: 'POST', credentials: 'same-origin', headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'), 'X-Requested-With': 'XMLHttpRequest' }, body: JSON.stringify({ weather_live_updates: enabled ? 1 : 0 }) }); } catch (e) {}
    });

    loadPreferences();
})();
</script>
