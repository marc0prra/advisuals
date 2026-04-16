import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    static targets = ['card', 'button'];

    filter(event) {
        const selected = event.currentTarget.dataset.filter;

        // Mise à jour des boutons
        this.buttonTargets.forEach(btn => {
            const isActive = btn.dataset.filter === selected;
            btn.classList.toggle('text-[#C5A059]', isActive);
            btn.classList.toggle('border-b', isActive);
            btn.classList.toggle('border-[#C5A059]', isActive);
            btn.classList.toggle('pb-1', isActive);
            btn.classList.toggle('text-gray-500', !isActive);
        });

        // Affichage/masquage des cartes
        this.cardTargets.forEach(card => {
            const category = card.dataset.category;
            const visible = selected === 'Tout' || category === selected;
            card.style.display = visible ? '' : 'none';
        });
    }
}
