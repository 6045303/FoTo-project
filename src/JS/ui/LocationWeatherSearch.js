// src/JS/ui/LocationWeatherSearch.js

class WeatherAPILocal {
    static BASE_URL = 'https://api.openweathermap.org/data/2.5/weather';

    constructor(apiKey) {
        if (!apiKey) throw new Error('API key required');
        this.apiKey = apiKey;
    }

    async fetchByCity(city) {
        const url = `${WeatherAPILocal.BASE_URL}?q=${encodeURIComponent(city)}&appid=${this.apiKey}`;
        const res = await fetch(url);
        if (!res.ok) {
            throw new Error('Stad niet gevonden');
        }
        return res.json();
    }

    static kelvinToCelsius(kelvin) {
        return kelvin - 273.15;
    }
}

export class LocationWeatherSearch {
    constructor(inputElement, hiddenInputElement) {
        this.inputElement = inputElement;
        this.hiddenInputElement = hiddenInputElement;
        this.api = new WeatherAPILocal('c6908961755f935a67c1027e0be07b50');
        this.dropdownContainer = null;
        this.selectedLocation = null;

        this.init();
    }

    init() {
        // Create dropdown container
        this.dropdownContainer = document.createElement('div');
        this.dropdownContainer.id = 'weather-dropdown';
        this.dropdownContainer.className = 'weather-dropdown';
        this.dropdownContainer.style.cssText = `
            display: none;
            position: absolute;
            top: 100%;
            left: 0;
            right: 0;
            margin-top: 0.5rem;
            background: white;
            border: 2px solid #D3B69C;
            border-radius: 0.5rem;
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
            z-index: 50;
            max-height: 300px;
            overflow-y: auto;
        `;
        this.inputElement.parentElement.style.position = 'relative';
        this.inputElement.parentElement.appendChild(this.dropdownContainer);

        // Listen for input
        this.inputElement.addEventListener('input', (e) => this.handleInput(e));
        this.inputElement.addEventListener('blur', () => {
            setTimeout(() => this.hideDropdown(), 200);
        });
    }

    async handleInput(e) {
        const city = e.target.value.trim();
        
        if (city.length < 2) {
            this.hideDropdown();
            return;
        }

        try {
            const data = await this.api.fetchByCity(city);
            this.displayResults(data);
        } catch (err) {
            this.displayError(err.message);
        }
    }

    displayResults(data) {
        const { name, main: { temp, humidity }, weather: [{ description }] } = data;

        this.dropdownContainer.innerHTML = '';
        this.dropdownContainer.style.display = 'block';

        const item = document.createElement('div');
        item.style.cssText = `
            padding: 1rem;
            cursor: pointer;
            border-bottom: 1px solid #D3B69C;
            transition: background-color 0.2s;
        `;
        item.innerHTML = `
            <div style="font-weight: bold; color: #0B0B45; margin-bottom: 0.5rem;">${name}</div>
            <div style="color: #666666; font-size: 0.875rem; margin-bottom: 0.5rem;">${description}</div>
            <div style="color: #888888; font-size: 0.75rem;">
                <span style="color: #D4A574; font-weight: bold;">${WeatherAPILocal.kelvinToCelsius(temp).toFixed(1)}°C</span> • Vochtigheid: ${humidity}%
            </div>
        `;

        item.addEventListener('mouseover', () => {
            item.style.backgroundColor = 'rgba(212, 165, 116, 0.15)';
        });
        
        item.addEventListener('mouseout', () => {
            item.style.backgroundColor = 'transparent';
        });

        item.addEventListener('click', () => {
            this.selectLocation(name, data);
        });

        this.dropdownContainer.appendChild(item);
    }

    displayError(msg) {
        this.dropdownContainer.innerHTML = `<div style="padding: 0.75rem; color: #c33; background-color: #fee; border: 1px solid #fcc; font-size: 0.875rem;">${msg}</div>`;
        this.dropdownContainer.style.display = 'block';
    }

    selectLocation(name, data) {
        this.inputElement.value = name;
        this.hiddenInputElement.value = name;
        this.selectedLocation = { name, data };
        this.hideDropdown();
    }

    hideDropdown() {
        this.dropdownContainer.style.display = 'none';
    }
}
