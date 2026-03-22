import Alpine from 'alpinejs';

window.Alpine = Alpine;

Alpine.start();

// Registro del Service Worker (PWA)
if ('serviceWorker' in navigator) {
    window.addEventListener('load', () => {
        navigator.serviceWorker.register('/sw.js').catch(() => {});
    });
}
