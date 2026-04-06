export default class InviteManager {
    constructor(activityId, activityName) {
        this.activityId = activityId;
        this.activityName = activityName;
        this.apiUrl = '/invite_api.php';
        this.init();
    }

    init() {
        this.createUI();
        this.attachEvents();
    }

    // HTML Template
    createUI() {
        const inviteBtn = document.createElement('button');
        inviteBtn.className = 'invite-btn primary-btn px-4 py-2 rounded shadow';
        inviteBtn.textContent = '📧 Iemand uitnodigen';
        inviteBtn.dataset.activityId = this.activityId;

        const container = document.createElement('div');
        container.className = 'invite-container';
        container.setAttribute('data-activity-id', this.activityId);

        container.appendChild(inviteBtn);
        this.container = container;
        return container;
    }

    attachEvents() {
        const btn = this.container.querySelector('.invite-btn');
        btn.addEventListener('click', () => this.showModal());
    }

    showModal() {
        const overlay = document.createElement('div');
        overlay.className = 'fixed inset-0 bg-black bg-opacity-40 flex items-center justify-center z-50';

        const modal = document.createElement('div');
        modal.className = 'bg-white rounded-lg p-6 shadow-xl max-w-sm w-full';

        modal.innerHTML = `
            <h3 class="text-xl font-bold mb-4">Uitnodig naar: ${this.activityName}</h3>
            <div class="space-y-3">
                <input 
                    type="email" 
                    class="invite-email w-full p-2 border rounded focus:ring-2 focus:ring-blue-500" 
                    placeholder="E-mailadres van gebruiker"
                    required
                >
                <button class="invite-send primary-btn w-full py-2 rounded">Verzenden</button>
                <button class="invite-cancel secondary-btn w-full py-2 rounded">Annuleren</button>
            </div>
            <div class="invite-response mt-4 text-sm"></div>
        `;

        overlay.appendChild(modal);
        document.body.appendChild(overlay);

        // Event Listeners
        modal.querySelector('.invite-send').addEventListener('click', () => {
            this.sendInvite(
                modal.querySelector('.invite-email').value,
                overlay
            );
        });

        modal.querySelector('.invite-cancel').addEventListener('click', () => {
            overlay.remove();
        });
    }

    async sendInvite(email, overlay) {
        const responseDiv = overlay.querySelector('.invite-response');

        if (!email) {
            this.showResponse(responseDiv, 'E-mail invoeren!', 'error');
            return;
        }

        try {
            const response = await fetch(this.apiUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    recipient_email: email,
                    activity_id: this.activityId
                })
            });

            const data = await response.json();

            if (response.ok) {
                this.showResponse(responseDiv, data.message, 'success');
                setTimeout(() => overlay.remove(), 1500);
            } else {
                this.showResponse(responseDiv, data.error, 'error');
            }
        } catch (error) {
            this.showResponse(responseDiv, 'Fout bij verzenden: ' + error.message, 'error');
        }
    }

    showResponse(div, message, type) {
        div.className = `invite-response mt-4 text-sm p-2 rounded ${
            type === 'success' ? 'bg-green-200 text-green-800' : 'bg-red-200 text-red-800'
        }`;
        div.textContent = message;
    }
}
