// Weer client (KNMI token)
// NOTE: this legacy script is no longer used; replaced by ES modules under src/JS

document.addEventListener('DOMContentLoaded', () => {    const cityInput = document.querySelector('.cityInput');
    const card = document.querySelector('.card');
    const weatherButton = document.getElementById('weather-button');

    // KNMI dataplatform token (provided)
    const KNMI_TOKEN = 'eyJvcmciOiI1ZTU1NGUxOTI3NGE5NjAwMDEyYTNlYjEiLCJpZCI6IjMyYjljZmFlMTJmODQzZTdhNzM2OTliYzM2ZjU2NzQyIiwiaCI6Im11cm11cjEyOCJ9';
    // Default KNMI endpoint - replace if you have a specific collection URL
    const KNMI_ENDPOINT = 'https://api.dataplatform.knmi.nl/odata/2.0/observations';

    function showError(msg) {
        if (!card) return alert(msg);
        card.innerHTML = '';
        const p = document.createElement('p');
        p.textContent = msg;
        p.className = 'text-red-600 text-sm mt-2';
        card.appendChild(p);
        card.style.display = 'block';
    }

    function getWeatherEmoji(WeatherId){
        switch (true) {
            case WeatherId >= 200 && WeatherId < 300: return '⛈️';
            case WeatherId >= 300 && WeatherId < 400: return '🌧️';
            case WeatherId >= 500 && WeatherId < 600: return '🌧️';
            case WeatherId >= 600 && WeatherId < 700: return '❄️';
            case WeatherId >= 700 && WeatherId < 800: return '🌫️';
            case WeatherId === 800: return '☀️';
            case WeatherId > 800 && WeatherId < 900: return '☁️';
            default: return '❓';
        }
    }

    // Best-effort parser for KNMI JSON: looks for temperature/humidity/name/description
    function parseKNMIData(data){
        if (!data) return {};
        // If data already in OWM format
        if (data.main) return { name: data.name || '', temp: data.main.temp, humidity: data.main.humidity, description: (data.weather && data.weather[0] && data.weather[0].description) || '' };

        let found = { name: null, temp: null, humidity: null, description: null };
        function walk(obj){
            if (!obj || typeof obj !== 'object') return;
            for (const k of Object.keys(obj)){
                const v = obj[k];
                const key = k.toLowerCase();
                if (found.temp === null && (key.includes('temp') || key === 't' || key === 't2m' || key.includes('airtemperature') || key.includes('air_temp'))){
                    if (typeof v === 'number' && v > -60 && v < 60) found.temp = v;
                }
                if (found.humidity === null && (key.includes('humid') || key === 'rh' || key.includes('luchtvocht') || key.includes('relativehumidity'))){
                    if (typeof v === 'number' && v >= 0 && v <= 100) found.humidity = v;
                }
                if (!found.name && (key.includes('station') || key.includes('plaats') || key.includes('location') || key.includes('name'))){
                    if (typeof v === 'string') found.name = v;
                }
                if (!found.description && (key.includes('desc') || key.includes('weer') || key.includes('summary') || key.includes('description'))){
                    if (typeof v === 'string') found.description = v;
                }
                if (typeof v === 'object') walk(v);
            }
        }
        walk(data);
        // arrays
        if ((found.temp === null || found.humidity === null) && Array.isArray(data)){
            for (const it of data) { if (typeof it === 'object') { walk(it); if (found.temp !== null && found.humidity !== null) break; } }
        }
        return found;
    }

    async function getWeatherData(city){
        if (!KNMI_TOKEN) throw new Error('Geen KNMI-token ingesteld.');
        if (!KNMI_ENDPOINT) throw new Error('Geen KNMI endpoint ingesteld. Pas KNMI_ENDPOINT aan.');

        // Try simple q parameter first
        let url = `${KNMI_ENDPOINT}?q=${encodeURIComponent(city)}`;
        // If OData endpoint, attempt a simple contains filter (best-effort)
        if (KNMI_ENDPOINT.toLowerCase().includes('/odata')){
            const filter = encodeURIComponent(`contains(tolower(stationName),'${city.toLowerCase()}') or contains(tolower(plaats),'${city.toLowerCase()}')`);
            url = `${KNMI_ENDPOINT}?$filter=${filter}&$top=5`;
        }

        const res = await fetch(url, { headers: { 'Authorization': 'Bearer ' + KNMI_TOKEN, 'Accept': 'application/json' } });
        if (!res.ok){
            if (res.status === 401) throw new Error('Invalid KNMI token (401).');
            let msg = `Fout ${res.status}`;
            try { const j = await res.json(); if (j && (j.message || j.error)) msg = j.message || j.error; } catch(e){}
            throw new Error(msg);
        }
        return res.json();
    }

    function displayWeatherInfo(data){
        if (!card) return console.log(data);
        card.innerHTML = '';
        card.style.display = 'block';
        card.classList.add('flex','flex-col');

        // Try parse
        const parsed = parseKNMIData(data);
        const cityName = parsed.name || (data && data.location_name) || '';
        const temp = (parsed.temp !== null ? parsed.temp : null);
        const humidity = (parsed.humidity !== null ? parsed.humidity : null);
        const description = parsed.description || '';

        const title = document.createElement('h2');
        title.className = 'text-xl font-semibold mb-1';
        title.textContent = cityName ? `Weer in ${cityName}` : 'Weergegevens';

        const descP = document.createElement('p');
        descP.className = 'text-sm italic text-gray-600';
        if (description) descP.textContent = description.charAt(0).toUpperCase() + description.slice(1);

        const row = document.createElement('div'); row.className = 'flex items-center gap-4';
        const tempP = document.createElement('p'); tempP.className = 'text-lg'; tempP.textContent = (typeof temp === 'number') ? `Temperatuur: ${temp.toFixed(1)}°C` : 'Temperatuur: n.v.t.';
        const humP = document.createElement('p'); humP.className = 'text-sm text-gray-700'; humP.textContent = (typeof humidity === 'number') ? `Luchtvochtigheid: ${humidity}%` : 'Luchtvochtigheid: n.v.t.';
        row.appendChild(tempP); row.appendChild(humP);

        const emoji = document.createElement('div'); emoji.className = 'text-3xl ml-auto';
        // no reliable weatherId from KNMI generic JSON
        row.appendChild(emoji);

        card.appendChild(title);
        if (description) card.appendChild(descP);
        card.appendChild(row);

        // if parsing failed, show JSON for debugging
        if (temp === null && humidity === null){
            const pre = document.createElement('pre'); pre.className = 'mt-2 text-xs p-2 rounded bg-gray-50 overflow-auto';
            pre.textContent = JSON.stringify(data, null, 2);
            card.appendChild(pre);
        }
    }

    async function handleRequest(e){
        if (e && e.preventDefault) e.preventDefault();
        if (!cityInput) return showError('Vul alstublieft een stad in.');
        const city = cityInput.value.trim();
        if (!city) return showError('Vul alstublieft een stad in.');
        try{
            if (card) { card.innerHTML = '<p class="text-sm">Laden…</p>'; card.style.display = 'block'; }
            const data = await getWeatherData(city);
            displayWeatherInfo(data);
        } catch(err){
            showError('Fout bij ophalen weer: ' + err.message);
        }
    }

    if (weatherButton) weatherButton.addEventListener('click', handleRequest);
    if (cityInput) cityInput.addEventListener('keydown', ev => { if (ev.key === 'Enter'){ ev.preventDefault(); handleRequest(); } });
});
