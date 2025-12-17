// gameRouter.js - Client-Side Router for SPA
import { initMap, loadMapPoints } from '/js/modules/map.js';

const GameRouter = {
    currentUrl: window.location.href,

    init() {
        console.log('[Router] Initializing...');

        // Handle Back/Forward Browser Buttons
        window.addEventListener('popstate', (event) => {
            if (event.state && event.state.url) {
                this.navigate(event.state.url, false);
            } else {
                if (window.location.pathname === '/game') {
                    this.showMap();
                } else {
                    // Try to load current URL content
                    this.navigate(window.location.href, false);
                }
            }
        });

        // Intercept internal links validation? 
        // We usually call GameRouter.navigate() explicitly.
    },

    /**
     * Navigate to a URL via AJAX
     * @param {string} url - The URL to load
     * @param {boolean} addToHistory - Whether to push to browser history
     */
    async navigate(url, addToHistory = true) {
        console.log(`[Router] Navigating to: ${url}`);

        try {
            const response = await fetch(url, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });

            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }

            const html = await response.text();

            // Determine relevant JS module based on URL convention
            let modulePath = null;
            if (url.includes('/story/')) modulePath = '/js/story.js';
            if (url.includes('/combat/')) modulePath = '/js/combat.js'; // Future support

            this.renderScene(html, modulePath, url);

            if (addToHistory) {
                history.pushState({ url: url }, '', url);
            }

            this.currentUrl = url;

        } catch (error) {
            console.error('[Router] Navigation failed:', error);
            // Fallback? Show Map?
        }
    },

    /**
     * Render the scene content and initialize modules
     */
    async renderScene(html, modulePath, url) {
        const sceneContainer = document.getElementById('game-scene');
        const mapContainer = document.getElementById('map-container');

        if (!sceneContainer || !mapContainer) return;

        // Hide Map, Show Scene
        mapContainer.classList.add('hidden');
        sceneContainer.classList.remove('hidden');

        // Inject Content
        sceneContainer.innerHTML = html;

        // Load Module if specified
        if (modulePath) {
            try {
                console.log(`[Router] Loading module: ${modulePath}`);
                const module = await import(modulePath + '?t=' + Date.now()); // Bust cache
                if (module.init) {
                    await module.init();
                }
            } catch (e) {
                console.error(`[Router] Failed to load module ${modulePath}:`, e);
            }
        }
    },

    /**
     * Return to the Map View
     */
    showMap() {
        console.log('[Router] Showing Map');
        const sceneContainer = document.getElementById('game-scene');
        const mapContainer = document.getElementById('map-container');

        if (sceneContainer) {
            sceneContainer.classList.add('hidden');
            sceneContainer.innerHTML = ''; // Cleanup
        }
        if (mapContainer) {
            mapContainer.classList.remove('hidden');
        }

        history.pushState({ url: '/game' }, '', '/game');

        // Re-initialize map/UI if needed? 
        // Usually map state is preserved in existing DOM.
        // But we might want to refresh data (health, etc)
    }
};

// Expose globally
window.GameRouter = GameRouter;
export default GameRouter;
