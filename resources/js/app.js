/**
 * E-Learning — app.js
 * Bootstrap est chargé via CDN dans le layout.
 * Ce fichier gère les initialisations JS globales.
 */

// Auto-dismiss des alertes flash après 5 secondes
document.addEventListener('DOMContentLoaded', () => {
    setTimeout(() => {
        document.querySelectorAll('.alert.alert-dismissible').forEach(el => {
            const bsAlert = bootstrap.Alert.getOrCreateInstance(el);
            bsAlert.close();
        });
    }, 5000);
});

// Confirmation avant suppression
document.querySelectorAll('[data-confirm]').forEach(el => {
    el.addEventListener('click', e => {
        const msg = el.dataset.confirm || 'Êtes-vous sûr de vouloir continuer ?';
        if (!confirm(msg)) e.preventDefault();
    });
});
