// src/JS/api/WeatherAPI.js
export class WeatherAPI {
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