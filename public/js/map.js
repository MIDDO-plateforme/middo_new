// ============================================================
// FICHIER : public/js/map.js
// Carte Leaflet + Segments en temps réel + Transporteurs premium
// + Filtre transporteur + Lien segment
// ============================================================

document.addEventListener("DOMContentLoaded", () => {

    // ------------------------------------------------------------
    // 1) Initialisation de la carte
    // ------------------------------------------------------------
    const map = L.map('middo-map').setView([5.35, -4.01], 6);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
        attribution: '© OpenStreetMap'
    }).addTo(map);

    // ------------------------------------------------------------
    // 2) Segments en temps réel
    // ------------------------------------------------------------
    const segmentLayers = {};

    const segmentsUrl = new URL('http://localhost:3000/.well-known/mercure');
    segmentsUrl.searchParams.append('topic', 'middo/segments');

    const segmentsSource = new EventSource(segmentsUrl);

    segmentsSource.onmessage = (event) => {
        const data = JSON.parse(event.data);

        const id = data.id;
        const coords = [data.start, data.end];

        if (segmentLayers[id]) {
            map.removeLayer(segmentLayers[id]);
        }

        let color = 'blue';
        if (data.status === 'blocked') color = 'red';
        if (data.status === 'delayed') color = 'orange';
        if (data.status === 'completed') color = 'green';

        const polyline = L.polyline(coords, { color, weight: 5 }).addTo(map);
        segmentLayers[id] = polyline;
    };

    // ------------------------------------------------------------
    // 3) Transporteurs en temps réel (premium)
    // ------------------------------------------------------------
    const transporterMarkers = {};
    let transporterFilter = null;

    const transporterIcon = L.icon({
        iconUrl: '/images/truck-icon.png',
        iconSize: [32, 32],
        iconAnchor: [16, 16],
        popupAnchor: [0, -16]
    });

    const transportersUrl = new URL('http://localhost:3000/.well-known/mercure');
    transportersUrl.searchParams.append('topic', 'middo/transporters');

    const transportersSource = new EventSource(transportersUrl);

    transportersSource.onmessage = (event) => {
        const data = JSON.parse(event.data);

        const id = data.transporterId;
        const lat = data.lat;
        const lon = data.lon;

        if (transporterMarkers[id]) {
            transporterMarkers[id].setLatLng([lat, lon]);
        } else {
            transporterMarkers[id] = L.marker([lat, lon], { icon: transporterIcon });
        }

        let popup = `<strong>Transporteur : ${id}</strong><br>`;
        if (data.status) popup += `Statut : ${data.status}<br>`;
        if (data.speed !== null && data.speed !== undefined) popup += `Vitesse : ${data.speed} km/h<br>`;
        if (data.direction) popup += `Direction : ${data.direction}<br>`;
        if (data.eta) popup += `ETA : ${data.eta}<br>`;
        if (data.segmentId) popup += `<a href="/transport/segment/${data.segmentId}/track">Voir le segment</a><br>`;
        popup += `Dernière mise à jour : ${data.updatedAt}`;

        transporterMarkers[id].bindPopup(popup);

        if (!transporterFilter || id.includes(transporterFilter)) {
            transporterMarkers[id].addTo(map);
        } else {
            map.removeLayer(transporterMarkers[id]);
        }
    };

    // ------------------------------------------------------------
    // 4) Filtre transporteur
    // ------------------------------------------------------------
    const filterInput = document.getElementById('transporter-filter');
    const filterClear = document.getElementById('transporter-filter-clear');

    if (filterInput) {
        filterInput.addEventListener('input', () => {
            transporterFilter = filterInput.value.trim() || null;

            Object.entries(transporterMarkers).forEach(([id, marker]) => {
                if (!transporterFilter || id.includes(transporterFilter)) {
                    marker.addTo(map);
                } else {
                    map.removeLayer(marker);
                }
            });
        });
    }

    if (filterClear) {
        filterClear.addEventListener('click', () => {
            transporterFilter = null;
            filterInput.value = '';

            Object.values(transporterMarkers).forEach(marker => marker.addTo(map));
        });
    }

});
