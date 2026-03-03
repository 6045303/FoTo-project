// src/JS/main.js
import { WeatherAPI } from './api/WeatherAPI.js';
import { WeatherCard } from './ui/WeatherCard.js';

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