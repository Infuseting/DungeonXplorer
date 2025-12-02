/**
 * Character Renderer - Apply hair color filters to all character previews
 */
class CharacterRenderer {
    constructor() {
        this.filters = new Map();
    }
    
    /**
     * Apply hair color filter to a character preview
     * @param {HTMLElement} characterElement 
     */
    applyHairFilter(characterElement) {
        const hairLayer = characterElement.querySelector('.character-layer-hair');
        if (!hairLayer) return;
        
        const characterId = characterElement.dataset.characterId;
        const r = parseInt(hairLayer.dataset.hairRed) || 100;
        const g = parseInt(hairLayer.dataset.hairGreen) || 100;
        const b = parseInt(hairLayer.dataset.hairBlue) || 100;
        
        const filterId = `colorFilter-${characterId}`;
        
        // Create or get SVG filter
        if (!this.filters.has(characterId)) {
            this.createFilter(characterId, filterId, r, g, b);
        }
        
        hairLayer.style.filter = `url(#${filterId})`;
    }
    
    /**
     * Create SVG filter for hair color
     */
    createFilter(characterId, filterId, r, g, b) {
        const svgId = `svg-${characterId}`;
        let svg = document.getElementById(svgId);
        
        if (!svg) {
            svg = document.createElementNS('http://www.w3.org/2000/svg', 'svg');
            svg.id = svgId;
            svg.style.position = 'absolute';
            svg.style.width = '0';
            svg.style.height = '0';
            document.body.appendChild(svg);
            
            const defs = document.createElementNS('http://www.w3.org/2000/svg', 'defs');
            svg.appendChild(defs);
            
            const filter = document.createElementNS('http://www.w3.org/2000/svg', 'filter');
            filter.id = filterId;
            defs.appendChild(filter);
            
            const colorMatrix = document.createElementNS('http://www.w3.org/2000/svg', 'feColorMatrix');
            colorMatrix.setAttribute('type', 'matrix');
            
            const rVal = r / 100;
            const gVal = g / 100;
            const bVal = b / 100;
            
            const matrix = `${rVal} 0 0 0 0 0 ${gVal} 0 0 0 0 0 ${bVal} 0 0 0 0 0 1 0`;
            colorMatrix.setAttribute('values', matrix);
            filter.appendChild(colorMatrix);
            
            this.filters.set(characterId, filterId);
        }
    }
    
    /**
     * Initialize all character previews on the page
     */
    initAll() {
        const characters = document.querySelectorAll('.character-preview');
        characters.forEach(char => this.applyHairFilter(char));
    }
}

// Auto-initialize when DOM is ready
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', () => {
        const renderer = new CharacterRenderer();
        renderer.initAll();
    });
} else {
    const renderer = new CharacterRenderer();
    renderer.initAll();
}

// Export for manual use
window.CharacterRenderer = CharacterRenderer;