export default class FormValidator {

    constructor(form) {
        this.form = form;
        this.datumInput = form.querySelector("input[name='datum']");
    }

    validateDate() {
        if (!this.datumInput || !this.datumInput.value) return true;

        const gekozen = new Date(this.datumInput.value);
        const morgen = new Date();
        morgen.setDate(morgen.getDate() + 1);
        morgen.setHours(0, 0, 0, 0);

        return gekozen >= morgen;
    }

    showError(message) {
        const overlay = document.createElement("div");
        overlay.className = "fixed inset-0 bg-black bg-opacity-40 flex items-center justify-center z-50";

        const card = document.createElement("div");
        card.className = "bg-red-200 border border-red-600 text-black p-6 rounded-lg shadow-xl max-w-sm w-full text-center";

        const icon = document.createElement("div");
        icon.innerHTML = "⚠️";
        icon.className = "text-4xl mb-3";

        const text = document.createElement("p");
        text.className = "mb-4 font-semibold";
        text.textContent = message;

        const button = document.createElement("button");
        button.textContent = "OK";
        button.className = "mt-2 px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700 transition";
        button.addEventListener("click", () => overlay.remove());

        card.appendChild(icon);
        card.appendChild(text);
        card.appendChild(button);
        overlay.appendChild(card);
        document.body.appendChild(overlay);
    }

    init() {
        this.form.addEventListener("submit", (e) => {
            if (!this.validateDate()) {
                e.preventDefault();
                this.showError("Let op! Selecteer een datum vanaf morgen.");
            }
        });
    }
}