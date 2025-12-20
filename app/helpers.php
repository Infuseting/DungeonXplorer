<?php

/**
 * Render a view with a layout
 * 
 * @param string $view Path to the view file (relative to Views directory)
 * @param array $data Data to pass to the view
 * @param string $layout Layout to use (default: 'base')
 */
function view($view, $data = [], $layout = 'base') {
        extract($data);
    
        ob_start();
    require __DIR__ . "/../Views/{$view}.php";
    $content = ob_get_clean();
    
        require __DIR__ . "/../Views/layouts/{$layout}.php";
}

/**
 * Include a component
 * 
 * @param string $component Component name
 * @param array $data Data to pass to the component
 */
function component($component, $data = []) {
    extract($data);
    require __DIR__ . "/../Views/components/{$component}.php";
}

/**
 * Generate character preview HTML with appearance layers
 * 
 * @param array $character Character data with class and appearance
 * @param array $options Options: size (small, medium, large), showFilter (bool), id (string)
 * @return string HTML string
 */
function renderCharacter($character, $options = []) {
    $defaults = [
        'size' => 'medium',
        'showFilter' => false,
        'id' => 'character-' . uniqid(),
        'class' => ''
    ];
    
    $options = array_merge($defaults, $options);
    
    $className = strtolower($character['class']['name'] ?? $character['class_name'] ?? 'guerrier');
    $imageBase = "/assets/images/{$className}";
    
    // Parse appearance if it's a JSON string
    $appearance = $character['appearance'] ?? [];
    if (is_string($appearance)) {
        $appearance = json_decode($appearance, true) ?? [];
    }
    
    // Size classes
    $sizeClasses = [
        'small' => 'w-32 h-32',
        'medium' => 'w-64 h-64',
        'large' => 'w-96 h-96',
        'full' => 'w-full h-full'
    ];
    
    $sizeClass = $sizeClasses[$options['size']] ?? $sizeClasses['medium'];
    
    // Hair color values
    $hairRedCyan = $appearance['hair']['redCyan'] ?? 100;
    $hairGreenMagenta = $appearance['hair']['greenMagenta'] ?? 100;
    $hairBlueYellow = $appearance['hair']['blueYellow'] ?? 100;
    $isNaturalHair = $appearance['hair']['natural'] ?? false;
    
    // Eye color
    $eyeColor = $appearance['eyes']['color'] ?? 'brown';
    
    ob_start();
    ?>
    <div id="<?= htmlspecialchars($options['id']) ?>" class="character-preview <?= $sizeClass ?> <?= htmlspecialchars($options['class']) ?> relative inline-block" data-character-id="<?= htmlspecialchars($options['id']) ?>">
        <img src="<?= $imageBase ?>/<?= $className ?>.png" 
             alt="<?= htmlspecialchars($character['name'] ?? 'Personnage') ?>" 
             class="character-base w-full h-full object-contain"
             onerror="console.error('Failed to load base image:', this.src)">
        
        <!-- Eyes Layer -->
        <?php if ($eyeColor !== 'brown'): ?>
            <img src="<?= $imageBase ?>/eyes/eyes_<?= htmlspecialchars($eyeColor) ?>.png" 
                 alt="Yeux <?= htmlspecialchars($eyeColor) ?>" 
                 class="character-layer-eyes absolute top-0 left-0 w-full h-full object-contain"
                 onerror="console.warn('Eye color image not found:', this.src)">
        <?php endif; ?>
        
        <!-- Hair Layer -->
        <?php if (!$isNaturalHair): ?>
            <img src="<?= $imageBase ?>/hair.png" 
                 alt="Cheveux" 
                 class="character-layer-hair absolute top-0 left-0 w-full h-full object-contain"
                 data-hair-red="<?= $hairRedCyan ?>"
                 data-hair-green="<?= $hairGreenMagenta ?>"
                 data-hair-blue="<?= $hairBlueYellow ?>"
                 onerror="console.warn('Hair image not found:', this.src)">
        <?php endif; ?>
        
        <!-- Makeup/Tattoo Layers -->
        <?php if (isset($appearance['makeup']) && is_array($appearance['makeup'])): ?>
            <?php foreach ($appearance['makeup'] as $makeupFile => $isActive): ?>
                <?php if ($isActive === true): ?>
                    <img src="<?= $imageBase ?>/makeup/<?= htmlspecialchars($makeupFile) ?>.png" 
                         alt="Maquillage <?= htmlspecialchars($makeupFile) ?>" 
                         class="character-layer-makeup absolute top-0 left-0 w-full h-full object-contain"
                         onerror="console.warn('Makeup image not found:', this.src)">
                <?php endif; ?>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
    
    <?php if ($options['showFilter'] && !$isNaturalHair): ?>
    <script>
    (function() {
        const characterId = '<?= addslashes($options['id']) ?>';
        
        // Wait for image to load
        function applyFilter() {
            const hairLayer = document.querySelector(`[data-character-id="${characterId}"] .character-layer-hair`);
            
            if (!hairLayer) {
                console.warn('Hair layer not found for character:', characterId);
                return;
            }
            
            const r = parseInt(hairLayer.dataset.hairRed) || 100;
            const g = parseInt(hairLayer.dataset.hairGreen) || 100;
            const b = parseInt(hairLayer.dataset.hairBlue) || 100;
            
            console.log('Applying hair filter:', {characterId, r, g, b});
            
            const filterId = 'colorFilter-' + characterId;
            let svg = document.getElementById('svg-' + characterId);
            
            if (!svg) {
                svg = document.createElementNS('http://www.w3.org/2000/svg', 'svg');
                svg.id = 'svg-' + characterId;
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
            }
            
            hairLayer.style.filter = `url(#${filterId})`;
        }
        
        // Apply filter after image loads
        const hairImg = document.querySelector(`[data-character-id="${characterId}"] .character-layer-hair`);
        if (hairImg) {
            if (hairImg.complete) {
                applyFilter();
            } else {
                hairImg.addEventListener('load', applyFilter);
            }
        }
    })();
    </script>
    <?php endif; ?>
    <?php
    
    return ob_get_clean();
}
