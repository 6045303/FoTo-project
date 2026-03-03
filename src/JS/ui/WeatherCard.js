// src/JS/ui/WeatherCard.js
import { WeatherAPI } from '../api/WeatherAPI.js';

export class WeatherCard {
    constructor(container) {
        this.container = container;
    }

    render(data) {
        this.container.innerHTML = '';
        const { name, main: { temp, humidity }, weather: [{ description, id }] } = data;

        const card = document.createElement('div');
        card.className = 'bg-gradient-to-b from-blue-200 to-yellow-200 p-6 rounded-lg shadow flex flex-col items-center';

        card.innerHTML = `
            <h1 class="text-4xl font-bold mb-4">${name}</h1>
            <p class="text-3xl font-semibold">${WeatherAPI.kelvinToCelsius(temp).toFixed(1)}°C</p>
            <p class="text-lg mb-2">Humidity: ${humidity}%</p>
            <p class="italic">${description}</p>
            <p class="text-6xl mt-4">${this.getEmoji(id)}</p>
        `;
        this.container.appendChild(card);
    }

    renderError(msg) {
        this.container.innerHTML = `<p class="text-red-600 font-bold">${msg}</p>`;
    }

    getEmoji(id) {
        switch (true) {
            case id >= 200 && id < 300:
                return '⛈';
            case id >= 300 && id < 600:
                return '🌧';
            case id >= 600 && id < 700:
                return '❄';
            case id === 800:
                return '☀';
            case id > 800:
                return '☁';
            default:
                return '❓';
        }
    }
}