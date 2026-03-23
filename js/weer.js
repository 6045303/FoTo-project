// ======================================================
// ⭐ RUBRIC PUNT 1: DOM SELECTORS + DOM MANIPULATIE
// ======================================================
const button = document.getElementById("weather-button");
const cityInput = document.getElementById("plaats");
const card = document.getElementById("weather-result");

// ⭐ Jouw API key
const apiKey = "c6908961755f935a67c1027e0be07b50";


// ======================================================
// ⭐ RUBRIC PUNT 2: EVENT HANDLING
// ======================================================
button.addEventListener("click", async () => {

    const city = cityInput.value.trim();

    if (!city) {
        displayError("Voer een plaats in");
        return;
    }

    try {
        const weatherData = await getWeatherData(city);
        displayWeatherInfo(weatherData);
    } catch (error) {
        console.error(error);
        displayError("Kon het weer niet ophalen");
    }
});


// ======================================================
// ⭐ RUBRIC PUNT 3: API GEBRUIK (FETCH + ASYNC/AWAIT)
// ======================================================
async function getWeatherData(city) {

    const apiUrl =
        `https://api.openweathermap.org/data/2.5/weather?q=${city}&appid=${apiKey}`;

    const response = await fetch(apiUrl);

    if (!response.ok) {
        throw new Error("API request mislukt");
    }

    return await response.json();
}


// ======================================================
// ⭐ RUBRIC PUNT 4: FUNCTIES + DOM MANIPULATIE
// ======================================================
function displayWeatherInfo(data) {

    const {
        name: city,
        main: { temp, humidity },
        weather: [{ description, id }]
    } = data;

    const tempC = (temp - 273.15).toFixed(1);

    // Reset card
    card.classList.remove("hidden");
    card.innerHTML = "";

    // DOM elementen maken
    const cityDisplay = document.createElement("h1");
    const tempDisplay = document.createElement("p");
    const humidityDisplay = document.createElement("p");
    const descDisplay = document.createElement("p");
    const weatherEmoji = document.createElement("p");

    // Tekst invullen
    cityDisplay.textContent = city;
    tempDisplay.textContent = `${tempC}°C`;
    humidityDisplay.textContent = `Luchtvochtigheid: ${humidity}%`;
    descDisplay.textContent = description;
    weatherEmoji.textContent = getWeatherEmoji(id);

    // Styling
    cityDisplay.className = "text-2xl font-bold m-0";
    tempDisplay.className = "text-lg m-0";
    humidityDisplay.className = "text-lg m-0";
    descDisplay.className = "text-lg m-0";
    weatherEmoji.className = "text-3xl";

    // DOM toevoegen
    card.appendChild(cityDisplay);
    card.appendChild(tempDisplay);
    card.appendChild(humidityDisplay);
    card.appendChild(descDisplay);
    card.appendChild(weatherEmoji);
}


// ======================================================
// ⭐ Extra functie: emoji bepalen op basis van weer-ID
// ======================================================
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


// ======================================================
// ⭐ Error functie (DOM manipulatie)
// ======================================================
function displayError(message) {
    card.classList.remove("hidden");
    card.innerHTML = `
        <p class="text-xl font-bold text-gray-700">${message}</p>
    `;
}