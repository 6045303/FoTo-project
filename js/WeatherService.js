export default class WeatherService {
    static buildUrl(city) {
        const apiKey = "c6908961755f935a67c1027e0be07b50";
        return `https://api.openweathermap.org/data/2.5/weather?q=${encodeURIComponent(city)}&appid=${apiKey}&units=metric&lang=nl`;
    }

    async getWeather(city) {
        const response = await fetch(WeatherService.buildUrl(city));

        if (!response.ok) {
            throw new Error("Kon het weer niet ophalen.");
        }

        return response.json();
    }
}
