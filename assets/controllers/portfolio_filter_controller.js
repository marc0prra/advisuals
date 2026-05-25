import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    static targets = ['card', 'button'];

    filter(event) {
        const selected = event.currentTarget.dataset.filter;

        // Mise à jour des boutons — classes CSS .filter-btn.is-active
        this.buttonTargets.forEach(btn => {
            const isActive = btn.dataset.filter === selected;
            btn.classList.toggle('is-active', isActive);
            btn.setAttribute('aria-pressed', isActive ? 'true' : 'false');
        });

        // Affichage/masquage des cartes
        this.cardTargets.forEach(card => {
            const category = card.dataset.category;
            const visible  = selected === 'Tout' || category === selected;
            card.style.display = visible ? '' : 'none';
        });
    }
}
