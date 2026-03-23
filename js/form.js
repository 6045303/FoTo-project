import FormValidator from "./FormValidator.js";

document.addEventListener("DOMContentLoaded", () => {
    const form = document.querySelector("form[data-form-type='booking']");

    if (!form) {
        return;
    }

    const validator = new FormValidator(form);
    validator.init();
});
