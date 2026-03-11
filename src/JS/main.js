// weather.js

class WeatherAPI {
    static BASE_URL = 'https://api.openweathermap.org/data/2.5/weather';

    constructor(apiKey) {
        if (!apiKey) throw new Error('API key required');
        this.apiKey = apiKey;
    }

    async fetchByCity(city) {
        const url = `${WeatherAPI.BASE_URL}?q=${encodeURIComponent(city)}&appid=${this.apiKey}`;
        const res = await fetch(url);

        if (!res.ok) {
            throw new Error('Could not fetch weather data');
        }

        return res.json();
    }

    static kelvinToCelsius(kelvin) {
        return kelvin - 273.15;
    }
}

class WeatherCard {
    constructor(container) {
        this.container = container;
    }

    render(data) {
        const {
            name,
            main: { temp, humidity },
            weather: [{ description }]
        } = data;

        this.container.innerHTML = `
            <h2>${name}</h2>
            <p>${description}</p>
            <p><strong>${WeatherAPI.kelvinToCelsius(temp).toFixed(1)}°C</strong></p>
            <p>Humidity: ${humidity}%</p>
        `;
    }

    renderError(message) {
        this.container.innerHTML = `<p style="color:red;">${message}</p>`;
    }
}

class LocationWeatherSearch {

    constructor(inputElement, hiddenInputElement) {
        this.inputElement = inputElement;
        this.hiddenInputElement = hiddenInputElement;
        this.api = new WeatherAPI('c6908961755f935a67c1027e0be07b50');
        this.dropdownContainer = null;
        this.selectedLocation = null;

        this.init();
    }

    init() {

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

        const {
            name,
            main: { temp, humidity },
            weather: [{ description }]
        } = data;

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
            <div style="font-weight:bold;color:#0B0B45;margin-bottom:0.5rem;">${name}</div>
            <div style="color:#666;font-size:0.875rem;margin-bottom:0.5rem;">${description}</div>
            <div style="color:#888;font-size:0.75rem;">
                <span style="color:#D4A574;font-weight:bold;">
                    ${WeatherAPI.kelvinToCelsius(temp).toFixed(1)}°C
                </span>
                • Vochtigheid: ${humidity}%
            </div>
        `;

        item.addEventListener('mouseover', () => {
            item.style.backgroundColor = 'rgba(212,165,116,0.15)';
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

        this.dropdownContainer.innerHTML =
            `<div style="padding:0.75rem;color:#c33;background:#fee;border:1px solid #fcc;font-size:0.875rem;">
                ${msg}
            </div>`;

        this.dropdownContainer.style.display = 'block';
    }

    selectLocation(name, data) {

        this.inputElement.value = name;

        if (this.hiddenInputElement) {
            this.hiddenInputElement.value = name;
        }

        this.selectedLocation = { name, data };

        this.hideDropdown();
    }

    hideDropdown() {
        this.dropdownContainer.style.display = 'none';
    }
}

document.addEventListener('DOMContentLoaded', () => {

    const form = document.querySelector('.weatherForm');
    const input = document.querySelector('.cityInput');
    const cardEl = document.querySelector('.card');

    if (!form || !input || !cardEl) {
        return;
    }

    const api = new WeatherAPI('c6908961755f935a67c1027e0be07b50');
    const widget = new WeatherCard(cardEl);

    form.addEventListener('submit', async e => {

        e.preventDefault();

        const city = input.value.trim();

        if (!city) {
            widget.renderError('Please enter a city');
            return;
        }

        try {

            const data = await api.fetchByCity(city);
            widget.render(data);

        } catch (err) {

            widget.renderError(err.message);

        }

    });

});