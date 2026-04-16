import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    static targets = ['service', 'duration', 'resultContainer', 'price', 'formContainer', 'serviceIdField', 'totalField'];

    calculate() {
        const serviceOption = this.serviceTarget.options[this.serviceTarget.selectedIndex];
        const basePrice = parseFloat(this.serviceTarget.value);
        const multiplier = parseFloat(this.durationTarget.value);

        if (isNaN(basePrice) || isNaN(multiplier) || basePrice <= 0 || multiplier <= 0) {
            return;
        }

        const total = Math.round(basePrice * multiplier);

        this.priceTarget.textContent = total.toLocaleString('fr-FR') + ' €';
        this.resultContainerTarget.classList.remove('hidden');
        this.formContainerTarget.classList.remove('hidden');

        // Renseigner les champs cachés du formulaire
        if (this.hasServiceIdFieldTarget) {
            this.serviceIdFieldTarget.value = this.serviceTarget.value;
        }
        if (this.hasTotalFieldTarget) {
            this.totalFieldTarget.value = total;
        }
    }
}
