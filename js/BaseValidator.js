export default class BaseValidator {
    constructor(form) {
        this.form = form;
    }

    static createMessage(message) {
        const text = document.createElement("p");
        text.className = "mb-4 font-semibold";
        text.textContent = message;
        return text;
    }
}
