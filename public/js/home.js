// public/js/home.js

document.addEventListener('DOMContentLoaded', function () {

    // --- 1. Global/Common Features (These were already in app.js, this is home-specific) ---

    // --- Feature: Interactive Map ---
    // This map is on the homepage. Coordinates for stats are hardcoded in HTML, not dynamic yet.
    const mapElement = document.getElementById('distributionMap');
    if (mapElement) {
        const map = L.map('distributionMap').setView([18.7357, -70.1627], 8); // Central DR
        L.tileLayer('https://{s}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}{r}.png', {
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors &copy; <a href="https://carto.com/attributions">CARTO</a>'
        }).addTo(map);

        const customIcon = L.divIcon({
            className: 'custom-div-icon',
            html: `<div style='background-color: var(--primary-color); width: 20px; height: 20px; border-radius: 50%; border: 3px solid #fff; box-shadow: 0 0 10px var(--primary-color);'></div>`,
            iconSize: [20, 20],
            iconAnchor: [10, 10]
        });
        const hubs = [
            { lat: 18.4861, lng: -69.9312, name: 'Hub Principal (Santo Domingo)' },
            { lat: 19.4517, lng: -70.6970, name: 'Hub Cibao (Santiago)' },
            { lat: 18.5828, lng: -68.4043, name: 'Hub del Este (Punta Cana)' },
            { lat: 18.4273, lng: -68.9728, name: 'Hub Sureste (La Romana)' },
            { lat: 19.7808, lng: -70.6871, name: 'Hub del Norte (Puerto Plata)' }
        ];
        hubs.forEach(hub => {
            L.marker([hub.lat, hub.lng], { icon: customIcon })
                .addTo(map)
                .bindPopup(`<b>${hub.name}</b><br>Fully operational.`);
        });
    }

    // --- Feature: Count-Up Animation for Stats ---
    // These stats are currently hardcoded in HTML, but can be made dynamic from Global Settings.
    const statsContainer = document.querySelector('.map-stats');
    if (statsContainer) {
        const numbersToAnimate = statsContainer.querySelectorAll('.number[data-goal]');
        const statsObserver = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    numbersToAnimate.forEach(numberEl => {
                        const goal = parseInt(numberEl.dataset.goal);
                        if (isNaN(goal)) return;

                        let current = 0;
                        const duration = 2000; // 2 seconds
                        const stepTime = 20;
                        const increment = goal / (duration / stepTime);

                        const timer = setInterval(() => {
                            current += increment;
                            if (current >= goal) {
                                numberEl.textContent = goal;
                                clearInterval(timer);
                            } else {
                                numberEl.textContent = Math.ceil(current);
                            }
                        }, stepTime);
                    });
                    statsObserver.unobserve(entry.target); // Animate only once
                }
            });
        }, { threshold: 0.5 });
        statsObserver.observe(statsContainer);
    }

    // Note: Scroll reveal and mobile menu JS logic are now in app.js (global script).
});