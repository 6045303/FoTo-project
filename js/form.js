import FormValidator from "./FormValidator.js";

document.addEventListener("DOMContentLoaded", () => {
    const form = document.querySelector("form");

    if (!form) {
        console.warn("Geen formulier gevonden.");
        return;
    }

    const validator = new FormValidator(form);
    validator.init();
});