import BaseValidator from "./BaseValidator.js";

export default class FormValidator extends BaseValidator {
    constructor(form) {
        super(form);
        this.datumInput = form.querySelector("input[name='datum']");
    }

    validateDate() {
        if (!this.datumInput || !this.datumInput.value) {
            return true;
        }

        const gekozenDatum = new Date(this.datumInput.value);
        const morgen = new Date();
        morgen.setDate(morgen.getDate() + 1);
        morgen.setHours(0, 0, 0, 0);

        return gekozenDatum >= morgen;
    }

    showError(message) {
        const overlay = document.createElement("div");
        overlay.className = "fixed inset-0 bg-black/40 flex items-center justify-center z-50";

        const card = document.createElement("div");
        card.className = "bg-red-200 border border-red-600 text-black p-6 rounded-lg shadow-xl max-w-sm w-full text-center";

        const title = document.createElement("div");
        title.textContent = "Let op";
        title.className = "text-2xl mb-3 font-bold";

        const button = document.createElement("button");
        button.textContent = "Sluiten";
        button.className = "mt-2 px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700 transition";
        button.addEventListener("click", () => overlay.remove());

        card.appendChild(title);
        card.appendChild(BaseValidator.createMessage(message));
        card.appendChild(button);
        overlay.appendChild(card);
        document.body.appendChild(overlay);
    }

    init() {
        this.form.addEventListener("submit", (event) => {
            if (!this.validateDate()) {
                event.preventDefault();
                this.showError("Kies een datum vanaf morgen.");
            }
        });
    }
}
