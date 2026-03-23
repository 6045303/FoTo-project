import WeatherService from "./WeatherService.js";

class WeatherApp extends WeatherService {
    constructor() {
        super();
        this.button = document.getElementById("weather-button");
        this.cityInput = document.getElementById("plaats");
        this.result = document.getElementById("weather-result");
    }

    static emojiFor(weatherId) {
        if (weatherId >= 200 && weatherId < 300) return "Onweer";
        if (weatherId >= 300 && weatherId < 600) return "Regen";
        if (weatherId >= 600 && weatherId < 700) return "Sneeuw";
        if (weatherId === 800) return "Zonnig";
        if (weatherId > 800) return "Bewolkt";
        return "Onbekend";
    }

    createTemplate(weather) {
        return `
            <div class="rounded-xl shadow-lg p-5 bg-gradient-to-b from-[#0B0B45] to-[#D3B69C] text-white text-center">
                <h3 class="text-2xl font-bold">${weather.city}</h3>
                <p class="mt-2">Temperatuur: ${weather.temperature} °C</p>
                <p>Luchtvochtigheid: ${weather.humidity}%</p>
                <p>Beschrijving: ${weather.description}</p>
                <p>Status: ${weather.label}</p>
            </div>
        `;
    }

    showMessage(message) {
        if (!this.result) {
            return;
        }

        this.result.classList.remove("hidden");
        this.result.innerHTML = `<div class="rounded-xl bg-white border border-gray-300 p-4 text-center text-gray-700">${message}</div>`;
    }

    async handleClick() {
        if (!this.cityInput || !this.result) {
            return;
        }

        const city = this.cityInput.value.trim();

        if (city === "") {
            this.showMessage("Vul eerst een plaats in.");
            return;
        }

        this.showMessage("Weer wordt opgehaald...");

        try {
            const data = await this.getWeather(city);
            const weather = {
                city: data.name,
                temperature: Number(data.main.temp).toFixed(1),
                humidity: data.main.humidity,
                description: data.weather[0].description,
                label: WeatherApp.emojiFor(data.weather[0].id),
            };

            this.result.classList.remove("hidden");
            this.result.innerHTML = this.createTemplate(weather);
        } catch (error) {
            this.showMessage("Kon het weer niet ophalen. Controleer de plaatsnaam.");
        }
    }

    init() {
        if (!this.button) {
            return;
        }

        this.button.addEventListener("click", () => {
            this.handleClick();
        });
    }
}

document.addEventListener("DOMContentLoaded", () => {
    const app = new WeatherApp();
    app.init();
});
