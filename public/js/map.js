document.addEventListener('DOMContentLoaded', () => {
    fetch('/assets/tiles/map_config.json').then(r => r.json()).then(cfg => {
        const tileSize = cfg.tile_size || 256;
        const maxZoom = cfg.max_zoom || 0;
        const width = cfg.width;
        const height = cfg.height;

        const map = L.map('map', {
            crs: L.CRS.Simple,
            minZoom: maxZoom - 3,
            maxZoom: maxZoom,
            center: [0, 0],
            zoom: maxZoom,
            zoomControl: false,
            attributionControl: false,
            maxBoundsViscosity: 1.0
        });

        const tileLayer = L.tileLayer('/assets/tiles/{z}/{x}/{y}.png', {
            tileSize: tileSize,
            minZoom: maxZoom - 3,
            maxZoom: maxZoom,
            noWrap: true,
            continuousWorld: false,
            attribution: ''
        }).addTo(map);
        console.log(maxZoom);
        const southWest = map.unproject([0, height], maxZoom);
        const northEast = map.unproject([width, 0], maxZoom);
        const bounds = new L.LatLngBounds(southWest, northEast);

        map.setMaxBounds(bounds);
        map.fitBounds(bounds);

        // Expose map globally for adding points
        window.gameMap = map;
        window.gameMapMaxZoom = maxZoom;

        // Trigger event that map is ready
        window.dispatchEvent(new Event('mapReady'));

    }).catch(err => {
        console.error('Impossible de charger map_config.json', err);
        const mapDiv = document.getElementById('map');
        if (mapDiv) {
            mapDiv.innerHTML = '<div class="flex items-center justify-center h-full text-red-500">Erreur de chargement de la carte</div>';
        }
    });
});
