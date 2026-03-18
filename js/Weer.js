// Selectors
const button = document.getElementById("weather-button");
const cityInput = document.getElementById("plaats");
const card = document.getElementById("weather-result");

// JOUW API KEY HIER
const apiKey = "c6908961755f935a67c1027e0be07b50";

// Button click event
button.addEventListener("click", async () => {

    const city = cityInput.value.trim();

    if (!city) {
        displayError("Please enter a city");
        return;
    }

    try {
        const weatherData = await getWeatherData(city);
        displayWeatherInfo(weatherData);
    } catch (error) {
        console.error(error);
        displayError("Could not fetch weather data");
    }
});

// Fetch weather data
async function getWeatherData(city) {

    const apiUrl = `https://api.openweathermap.org/data/2.5/weather?q=${city}&appid=${apiKey}`;

    const response = await fetch(apiUrl);

    if (!response.ok) {
        throw new Error("Could not fetch weather data");
    }

    return await response.json();
}

// Display weather info
function displayWeatherInfo(data) {

    const { name: city,
        main: { temp, humidity },
        weather: [{ description, id }]
    } = data;

    
    const tempC = (temp - 273.15).toFixed(1);

    // Reset card
    card.classList.remove("hidden");
    card.innerHTML = "";

    // Build elements
    const cityDisplay = document.createElement("h1");
    const tempDisplay = document.createElement("p");
    const humidityDisplay = document.createElement("p");
    const descDisplay = document.createElement("p");
    const weatherEmoji = document.createElement("p");

    cityDisplay.textContent = city;
    tempDisplay.textContent = `${tempC}°C`;
    humidityDisplay.textContent = `Humidity: ${humidity}%`;
    descDisplay.textContent = description;
    weatherEmoji.textContent = getWeatherEmoji(id);

    // Tailwind classes
    cityDisplay.className = "text-2xl font-bold m-0";
    tempDisplay.className = "text-lg m-0";
    humidityDisplay.className = "text-lg m-0";
    descDisplay.className = "text-lg m-0";
    weatherEmoji.className = "text-3xl";

    // Append
    card.appendChild(cityDisplay);
    card.appendChild(tempDisplay);
    card.appendChild(humidityDisplay);
    card.appendChild(descDisplay);
    card.appendChild(weatherEmoji);
}

// Emoji logic
function getWeatherEmoji(weatherId) {
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

// Error display
function displayError(message) {

    card.classList.remove("hidden");
    card.innerHTML = `
        <p class="text-xl font-bold text-gray-700">${message}</p>
    `;
}