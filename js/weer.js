class WeatherApp {

    static apiKey = "c6908961755f935a67c1027e0be07b50";

    constructor() {
        // DOM selectors
        this.button = document.getElementById("weather-button");
        this.cityInput = document.getElementById("plaats");
        this.card = document.getElementById("weather-result");

        this.initEvents();
    }

    // ======================================================
    // EVENT HANDLING
    // ======================================================
    initEvents() {
        this.button.addEventListener("click", async () => {

            const city = this.cityInput.value.trim();

            if (!city) {
                this.displayError("Voer een plaats in");
                return;
            }

            try {
                const weatherData = await this.getWeatherData(city);
                this.displayWeatherInfo(weatherData);
            } catch (error) {
                console.error(error);
                this.displayError("Kon het weer niet ophalen");
            }
        });
    }

    // ======================================================
    // API GEBRUIK
    // ======================================================
    async getWeatherData(city) {

        const apiUrl =
            `https://api.openweathermap.org/data/2.5/weather?q=${city}&appid=${WeatherApp.apiKey}`;

        const response = await fetch(apiUrl);

        if (!response.ok) {
            throw new Error("API request mislukt");
        }

        return await response.json();
    }

    // ======================================================
    // DOM MANIPULATIE
    // ======================================================
    displayWeatherInfo(data) {

        const {
            name: city,
            main: { temp, humidity },
            weather: [{ description, id }]
        } = data;

        const tempC = (temp - 273.15).toFixed(1);

        this.card.classList.remove("hidden");
        this.card.innerHTML = "";

        const cityDisplay = document.createElement("h1");
        const tempDisplay = document.createElement("p");
        const humidityDisplay = document.createElement("p");
        const descDisplay = document.createElement("p");
        const weatherEmoji = document.createElement("p");

        cityDisplay.textContent = city;
        tempDisplay.textContent = `${tempC}°C`;
        humidityDisplay.textContent = `Luchtvochtigheid: ${humidity}%`;
        descDisplay.textContent = description;
        weatherEmoji.textContent = this.getWeatherEmoji(id);

        cityDisplay.className = "text-2xl font-bold m-0";
        tempDisplay.className = "text-lg m-0";
        humidityDisplay.className = "text-lg m-0";
        descDisplay.className = "text-lg m-0";
        weatherEmoji.className = "text-3xl";

        this.card.appendChild(cityDisplay);
        this.card.appendChild(tempDisplay);
        this.card.appendChild(humidityDisplay);
        this.card.appendChild(descDisplay);
        this.card.appendChild(weatherEmoji);
    }

    // ======================================================
    // HELPER FUNCTIE
    // ======================================================
    getWeatherEmoji(weatherId) {
        switch (true) {
            case (weatherId >= 200 && weatherId < 300): return "⛈";
            case (weatherId >= 300 && weatherId < 400): return "🌧";
            case (weatherId >= 500 && weatherId < 600): return "🌧";
            case (weatherId >= 600 && weatherId < 700): return "❄";
            case (weatherId >= 700 && weatherId < 800): return "🌫";
            case (weatherId === 800): return "☀";
            case (weatherId >= 801 && weatherId < 810): return "☁";
            default: return "❓";
        }
    }

    // ======================================================
    // ERROR HANDLING
    // ======================================================
    displayError(message) {
        this.card.classList.remove("hidden");
        this.card.innerHTML = `
            <p class="text-xl font-bold text-gray-700">${message}</p>
        `;
    }
}


// ======================================================
// APP STARTEN
// ======================================================
new WeatherApp();