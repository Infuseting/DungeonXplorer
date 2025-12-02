<?php
$title = 'Personnalisation - DungeonXplorer';
$bodyClass = 'bg-black min-h-screen';
$customStyles = '
    .character-container {
        background: linear-gradient(135deg, #0f172a 0%, #1e1b4b 100%);
        min-height: 100vh;
    }
    
    .character-viewer {
        background: transparent;
        padding: 2rem;
        backdrop-filter: blur(10px);
        transition: all 0.3s ease;
    }
    
    .character-viewer.zoomed {
        padding: 1rem;
    }
    
    .character-preview-wrapper {
        position: relative;
        width: 600px;
        height: 700px;
        margin: 0 auto;
        transition: all 0.5s cubic-bezier(0.4, 0, 0.2, 1);
        overflow: hidden;
    }
    
    .character-viewer.zoomed .character-preview-wrapper {
        width: 600px;
        height: 600px;
    }
    
    .character-preview-wrapper img {
        width: 100%;
        height: auto;
        object-fit: contain;
        transition: all 0.5s cubic-bezier(0.4, 0, 0.2, 1);
    }
    
    /* Zoom sur le buste pendant l\'édition */
    .character-viewer.zoomed .character-preview-wrapper img {
        transform: scale(1.8) translateY(0%);
        transform-origin: center top;
    }
    
    .customization-panel {
        background: rgba(15, 23, 42, 0.9);
        border: 1px solid rgba(139, 92, 246, 0.2);
        border-radius: 12px;
        padding: 1.5rem;
        backdrop-filter: blur(10px);
    }
    
    .tab-navigation {
        display: flex;
        gap: 0.5rem;
        margin-bottom: 2rem;
        border-bottom: 2px solid rgba(139, 92, 246, 0.2);
        padding-bottom: 0.5rem;
    }
    
    .tab-item {
        padding: 0.75rem 1.5rem;
        background: rgba(30, 41, 59, 0.5);
        border: 1px solid transparent;
        border-radius: 8px 8px 0 0;
        color: #94a3b8;
        cursor: pointer;
        transition: all 0.3s ease;
        font-weight: 500;
        position: relative;
    }
    
    .tab-item:hover {
        background: rgba(51, 65, 85, 0.7);
        color: #cbd5e1;
    }
    
    .tab-item.active {
        background: rgba(139, 92, 246, 0.2);
        border-color: rgba(139, 92, 246, 0.5);
        color: #c4b5fd;
    }
    
    .tab-item.active::after {
        content: "";
        position: absolute;
        bottom: -2px;
        left: 0;
        right: 0;
        height: 2px;
        background: #8b5cf6;
    }
    
    .control-group {
        margin-bottom: 1.5rem;
    }
    
    .control-label {
        display: flex;
        justify-content: space-between;
        align-items: center;
        color: #e2e8f0;
        font-size: 0.95rem;
        margin-bottom: 0.75rem;
        font-weight: 500;
    }
    
    .control-value {
        color: #8b5cf6;
        font-weight: 600;
        min-width: 3rem;
        text-align: right;
    }
    
    input[type="range"] {
        width: 100%;
        height: 6px;
        border-radius: 3px;
        background: linear-gradient(90deg, rgba(139, 92, 246, 0.2) 0%, rgba(139, 92, 246, 0.5) 100%);
        outline: none;
        -webkit-appearance: none;
        cursor: pointer;
    }
    
    input[type="range"]::-webkit-slider-thumb {
        -webkit-appearance: none;
        width: 18px;
        height: 18px;
        border-radius: 50%;
        background: linear-gradient(135deg, #8b5cf6 0%, #a78bfa 100%);
        cursor: pointer;
        box-shadow: 0 0 10px rgba(139, 92, 246, 0.5);
        border: 2px solid rgba(255, 255, 255, 0.2);
        transition: all 0.2s ease;
    }
    
    input[type="range"]::-webkit-slider-thumb:hover {
        transform: scale(1.2);
        box-shadow: 0 0 15px rgba(139, 92, 246, 0.8);
    }
    
    input[type="range"]::-moz-range-thumb {
        width: 18px;
        height: 18px;
        border-radius: 50%;
        background: linear-gradient(135deg, #8b5cf6 0%, #a78bfa 100%);
        cursor: pointer;
        box-shadow: 0 0 10px rgba(139, 92, 246, 0.5);
        border: 2px solid rgba(255, 255, 255, 0.2);
        transition: all 0.2s ease;
    }
    
    input[type="range"]::-moz-range-thumb:hover {
        transform: scale(1.2);
        box-shadow: 0 0 15px rgba(139, 92, 246, 0.8);
    }
    
    /* Custom Dropdown Styling */
    .custom-select-wrapper {
        position: relative;
    }
    
    .custom-select {
        width: 100%;
        background: linear-gradient(135deg, rgba(30, 41, 59, 0.9) 0%, rgba(51, 65, 85, 0.9) 100%);
        border: 2px solid rgba(139, 92, 246, 0.3);
        color: #ffffff;
        padding: 1rem 3rem 1rem 1.25rem;
        border-radius: 10px;
        font-size: 1rem;
        font-weight: 500;
        cursor: pointer;
        transition: all 0.3s ease;
        appearance: none;
        position: relative;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.2);
    }
    
    .custom-select:hover {
        border-color: rgba(139, 92, 246, 0.6);
        background: linear-gradient(135deg, rgba(51, 65, 85, 0.9) 0%, rgba(71, 85, 105, 0.9) 100%);
        box-shadow: 0 4px 15px rgba(139, 92, 246, 0.2);
        transform: translateY(-1px);
    }
    
    .custom-select:focus {
        outline: none;
        border-color: #8b5cf6;
        box-shadow: 0 0 0 4px rgba(139, 92, 246, 0.15), 0 4px 20px rgba(139, 92, 246, 0.3);
        transform: translateY(-2px);
    }
    
    /* Style des options sur Chrome/Edge */
    .custom-select option {
        background: #1e293b;
        color: #ffffff;
        padding: 1rem;
        font-weight: 500;
        border: none;
    }
    
    /* Firefox supporte mieux le style des options */
    @-moz-document url-prefix() {
        .custom-select option {
            background: #1e293b;
            color: #ffffff;
        }
        
        .custom-select option:hover {
            background: #334155 !important;
            color: #e2e8f0 !important;
        }
        
        .custom-select option:checked {
            background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%) !important;
            color: #ffffff !important;
        }
    }
    
    /* Pour Safari et Chrome - style alternatif */
    @supports (-webkit-appearance: none) {
        .custom-select {
            color: #ffffff;
            text-shadow: 0 0 0 #ffffff;
        }
        
        .custom-select option {
            background: #1e293b;
            color: #ffffff;
        }
        
        .custom-select option:checked {
            background: #8b5cf6;
            color: #ffffff;
            font-weight: 600;
        }
    }
    
    /* Custom Dropdown Arrow */
    .custom-select-wrapper::after {
        content: "";
        position: absolute;
        right: 1.25rem;
        top: 50%;
        transform: translateY(-50%);
        width: 0;
        height: 0;
        border-left: 6px solid transparent;
        border-right: 6px solid transparent;
        border-top: 6px solid #8b5cf6;
        pointer-events: none;
        transition: all 0.3s ease;
    }
    
    .custom-select:focus ~ .custom-select-wrapper::after,
    .custom-select-wrapper:has(select:focus)::after {
        border-top-color: #a78bfa;
        transform: translateY(-50%) rotate(180deg);
    }
    
    .preset-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 0.75rem;
        margin-top: 1rem;
    }
    
    .preset-btn {
        padding: 0.75rem;
        background: rgba(30, 41, 59, 0.6);
        border: 1px solid rgba(139, 92, 246, 0.2);
        color: #e2e8f0;
        border-radius: 8px;
        cursor: pointer;
        transition: all 0.3s ease;
        font-size: 0.875rem;
        font-weight: 500;
    }
    
    .preset-btn:hover {
        background: rgba(139, 92, 246, 0.2);
        border-color: rgba(139, 92, 246, 0.4);
        color: #ffffff;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(139, 92, 246, 0.3);
    }
    
    .btn-reset {
        width: 100%;
        padding: 0.875rem;
        background: rgba(30, 41, 59, 0.8);
        border: 1px solid rgba(139, 92, 246, 0.3);
        color: #e2e8f0;
        border-radius: 8px;
        cursor: pointer;
        transition: all 0.3s ease;
        font-weight: 500;
        margin-bottom: 1rem;
    }
    
    .btn-reset:hover {
        background: rgba(51, 65, 85, 0.8);
        border-color: rgba(139, 92, 246, 0.5);
        color: #ffffff;
        transform: translateY(-1px);
    }
    
    .btn-submit {
        width: 100%;
        padding: 1.25rem;
        background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%);
        border: none;
        color: white;
        border-radius: 8px;
        cursor: pointer;
        font-size: 1.125rem;
        font-weight: 600;
        transition: all 0.3s ease;
        box-shadow: 0 4px 20px rgba(139, 92, 246, 0.4);
    }
    
    .btn-submit:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 25px rgba(139, 92, 246, 0.6);
        background: linear-gradient(135deg, #7c3aed 0%, #6d28d9 100%);
    }
    
    .btn-submit:active {
        transform: translateY(0);
    }
    
    .section-title {
        color: #e2e8f0;
        font-size: 1.25rem;
        font-weight: 600;
        margin-bottom: 1.5rem;
        padding-bottom: 0.75rem;
        border-bottom: 1px solid rgba(139, 92, 246, 0.2);
    }
    
    .finish-message {
        text-align: center;
        color: #cbd5e1;
        margin-bottom: 2rem;
        font-size: 1.05rem;
        line-height: 1.6;
    }
    
    /* Animations */
    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: translateY(10px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    
    .edit-section {
        animation: fadeIn 0.4s ease;
    }
    
    .makeup-checkbox-wrapper {
        transition: all 0.3s ease;
    }
    
    .makeup-checkbox-wrapper:hover {
        background: rgba(139, 92, 246, 0.1);
        transform: translateX(5px);
    }
    
    .makeup-checkbox-wrapper:has(input:checked) {
        background: rgba(139, 92, 246, 0.15);
        border-color: rgba(139, 92, 246, 0.6);
    }
    
    .makeup-checkbox {
        cursor: pointer;
        accent-color: #8b5cf6;
    }
    
    .makeup-checkbox:focus {
        outline: 2px solid rgba(139, 92, 246, 0.5);
        outline-offset: 2px;
    }
';

$className = strtolower($character['class']['name']);
$imageBase = "/assets/images/{$className}";

ob_start();
?>

<div class="character-container">
    <div class="max-w-7xl mx-auto p-6">
        <!-- Header -->
        <div class="text-center mb-8">
            <div class="flex items-center justify-between mb-4">
                <a href="/personnage/<?= $character['id'] === 'preview' ? 'create' : '' ?>" 
                   class="text-gray-400 hover:text-white transition flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z" clip-rule="evenodd" />
                    </svg>
                    <?= $character['id'] === 'preview' ? 'Annuler' : 'Retour' ?>
                </a>
                <h1 class="text-4xl font-bold text-white">Créer personnage</h1>
                <div class="w-24"></div> <!-- Spacer pour centrer le titre -->
            </div>
            <p class="text-violet-300 text-lg"><?= htmlspecialchars($character['name']) ?></p>
        </div>
        
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- Character Preview -->
            <div class="character-viewer" id="characterViewer">
                <div class="character-preview-wrapper">
                    <div class="character-preview relative inline-block w-full h-full" data-character-id="character-edit" id="characterPreview">
                        <!-- Base Character -->
                        <img id="characterImage" src="<?= $imageBase ?>/<?= $className ?>.png" 
                             alt="<?= htmlspecialchars($character['name']) ?>" 
                             class="character-base absolute top-0 left-0 w-full h-full object-contain">
                        
                        <!-- Hair Layer (toujours présent) -->
                        <img id="hairImage" src="<?= $imageBase ?>/hair.png" 
                             alt="Cheveux" 
                             class="character-layer-hair absolute top-0 left-0 w-full h-full object-contain"
                             data-hair-red="<?= $character['appearance']['hair']['redCyan'] ?? 100 ?>"
                             data-hair-green="<?= $character['appearance']['hair']['greenMagenta'] ?? 100 ?>"
                             data-hair-blue="<?= $character['appearance']['hair']['blueYellow'] ?? 100 ?>">
                        
                        <!-- Eyes Layer Container (créé dynamiquement) -->
                        <div id="eyesContainer"></div>
                        
                        <!-- Makeup Layers Container (créé dynamiquement) -->
                        <div id="makeupContainer"></div>
                    </div>
                </div>
            </div>

            <!-- Customization Panel -->
            <div class="customization-panel">
                <form action="/personnage/apparence/<?= $character['id'] ?>" method="POST" id="appearanceForm">
                    
                    <!-- Tab Navigation -->
                    <div class="tab-navigation">
                        <div class="tab-item active" data-tab="hair">Cheveux</div>
                        <div class="tab-item" data-tab="eyes">Yeux</div>
                        <div class="tab-item" data-tab="makeup">Maquillage</div>
                        <div class="tab-item" data-tab="finish">Terminer</div>
                    </div>

                    <!-- Hair Section -->
                    <div class="edit-section" id="hairSection">
                        <h3 class="section-title">Modifier le style et la couleur des cheveux</h3>
                        
                        <div class="control-group">
                            <div class="control-label">
                                <span>Rouge / Cyan</span>
                                <span id="redCyanValue" class="control-value">100</span>
                            </div>
                            <input type="range" name="hair_red_cyan" id="redCyanSlider" min="0" max="200" value="<?= $character['appearance']['hair']['redCyan'] ?? 100 ?>">
                        </div>
                        
                        <div class="control-group">
                            <div class="control-label">
                                <span>Vert / Magenta</span>
                                <span id="greenMagentaValue" class="control-value">100</span>
                            </div>
                            <input type="range" name="hair_green_magenta" id="greenMagentaSlider" min="0" max="200" value="<?= $character['appearance']['hair']['greenMagenta'] ?? 100 ?>">
                        </div>
                        
                        <div class="control-group">
                            <div class="control-label">
                                <span>Bleu / Jaune</span>
                                <span id="blueYellowValue" class="control-value">100</span>
                            </div>
                            <input type="range" name="hair_blue_yellow" id="blueYellowSlider" min="0" max="200" value="<?= $character['appearance']['hair']['blueYellow'] ?? 100 ?>">
                        </div>

                        <button type="button" id="resetBtn" class="btn-reset">Réinitialiser</button>
                        
                        <div class="preset-grid">
                            <button type="button" class="preset-btn" data-preset="blonde">Blond</button>
                            <button type="button" class="preset-btn" data-preset="redhead">Roux</button>
                            <button type="button" class="preset-btn" data-preset="brown">Châtain</button>
                            <button type="button" class="preset-btn" data-preset="black">Noir</button>
                            <button type="button" class="preset-btn" data-preset="blue">Bleu</button>
                            <button type="button" class="preset-btn" data-preset="pink">Rose</button>
                        </div>
                    </div>

                    <!-- Eyes Section -->
                    <div class="edit-section hidden" id="eyesSection">
                        <h3 class="section-title">Couleur des yeux</h3>
                        <div class="control-group">
                            <label class="control-label">
                                <span>Sélectionnez une couleur</span>
                            </label>
                            <div class="custom-select-wrapper">
                                <select name="eye_color" id="eyeColorSelect" class="custom-select">
                                    <option value="brown" <?= ($character['appearance']['eyes']['color'] ?? 'brown') === 'brown' ? 'selected' : '' ?>>Marron (défaut)</option>
                                    <?php foreach ($appearanceOptions['eyes'] as $eyeType => $eyeLabel): ?>
                                        <option value="<?= htmlspecialchars($eyeType) ?>" 
                                                <?= ($character['appearance']['eyes']['color'] ?? '') === $eyeType ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($eyeLabel) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Makeup Section -->
                    <div class="edit-section hidden" id="makeupSection">
                        <h3 class="section-title">Maquillage et tatouages</h3>
                        <p class="text-gray-400 text-sm mb-4">Vous pouvez sélectionner plusieurs options</p>
                        <div class="space-y-3">
                            <?php foreach ($appearanceOptions['makeup'] as $makeupFile => $makeupLabel): 
                                $isChecked = isset($character['appearance']['makeup'][$makeupFile]) && $character['appearance']['makeup'][$makeupFile] === true;
                            ?>
                                <label class="makeup-checkbox-wrapper flex items-center gap-3 p-3 bg-gray-800/50 border border-gray-700 rounded-lg cursor-pointer hover:border-violet-500 transition">
                                    <input type="checkbox" 
                                           name="makeup[]" 
                                           value="<?= htmlspecialchars($makeupFile) ?>" 
                                           class="makeup-checkbox w-5 h-5 rounded border-gray-600 text-violet-600 focus:ring-violet-500 focus:ring-offset-gray-900"
                                           <?= $isChecked ? 'checked' : '' ?>
                                           onchange="toggleMakeup('<?= htmlspecialchars($makeupFile) ?>', this.checked)">
                                    <span class="text-white font-medium"><?= htmlspecialchars($makeupLabel) ?></span>
                                </label>
                            <?php endforeach; ?>
                            
                            <?php if (empty($appearanceOptions['makeup'])): ?>
                                <p class="text-gray-500 text-center py-4">Aucun maquillage disponible pour cette classe</p>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Finish Section -->
                    <div class="edit-section hidden" id="finishSection">
                        <h3 class="section-title">Finalisation</h3>
                        <div class="finish-message">
                            <p>Votre personnage est prêt à commencer l'aventure !</p>
                        </div>
                        <button type="submit" class="btn-submit">Commencer l'aventure</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
const hairImage = document.getElementById('hairImage');
const characterViewer = document.getElementById('characterViewer');
const characterPreview = document.getElementById('characterPreview');
const eyesContainer = document.getElementById('eyesContainer');
const makeupContainer = document.getElementById('makeupContainer');

const redCyanSlider = document.getElementById('redCyanSlider');
const greenMagentaSlider = document.getElementById('greenMagentaSlider');
const blueYellowSlider = document.getElementById('blueYellowSlider');
const redCyanValue = document.getElementById('redCyanValue');
const greenMagentaValue = document.getElementById('greenMagentaValue');
const blueYellowValue = document.getElementById('blueYellowValue');
const eyeColorSelect = document.getElementById('eyeColorSelect');

const className = '<?= $className ?>';
const imageBase = '<?= $imageBase ?>';

// Options disponibles
const eyeOptions = <?= json_encode($appearanceOptions['eyes']) ?>;
const makeupOptions = <?= json_encode(array_keys($appearanceOptions['makeup'])) ?>;

const presets = {
    original: { redCyan: 100, greenMagenta: 100, blueYellow: 100 },
    blonde: { redCyan: 115, greenMagenta: 110, blueYellow: 70 },
    redhead: { redCyan: 140, greenMagenta: 80, blueYellow: 120 },
    brown: { redCyan: 110, greenMagenta: 105, blueYellow: 115 },
    black: { redCyan: 100, greenMagenta: 100, blueYellow: 100 },
    blue: { redCyan: 70, greenMagenta: 90, blueYellow: 150 },
    pink: { redCyan: 135, greenMagenta: 70, blueYellow: 90 }
};

// Gestion des cheveux
function updateFilter() {
    if (!hairImage) return;
    
    const r = parseInt(redCyanSlider.value);
    const g = parseInt(greenMagentaSlider.value);
    const b = parseInt(blueYellowSlider.value);
    
    redCyanValue.textContent = r;
    greenMagentaValue.textContent = g;
    blueYellowValue.textContent = b;
    
    const filterId = 'colorBalanceFilter';
    let svgFilter = document.getElementById(filterId);
    
    if (!svgFilter) {
        const svg = document.createElementNS('http://www.w3.org/2000/svg', 'svg');
        svg.style.position = 'absolute';
        svg.style.width = '0';
        svg.style.height = '0';
        document.body.appendChild(svg);
        
        const defs = document.createElementNS('http://www.w3.org/2000/svg', 'defs');
        svg.appendChild(defs);
        
        svgFilter = document.createElementNS('http://www.w3.org/2000/svg', 'filter');
        svgFilter.id = filterId;
        defs.appendChild(svgFilter);
    }
    
    svgFilter.innerHTML = '';
    
    const colorMatrix = document.createElementNS('http://www.w3.org/2000/svg', 'feColorMatrix');
    colorMatrix.setAttribute('type', 'matrix');
    
    const rVal = r / 100;
    const gVal = g / 100;
    const bVal = b / 100;
    
    const matrix = `${rVal} 0 0 0 0 0 ${gVal} 0 0 0 0 0 ${bVal} 0 0 0 0 0 1 0`;
    colorMatrix.setAttribute('values', matrix);
    svgFilter.appendChild(colorMatrix);
    
    hairImage.style.filter = `url(#${filterId})`;
}

redCyanSlider?.addEventListener('input', updateFilter);
greenMagentaSlider?.addEventListener('input', updateFilter);
blueYellowSlider?.addEventListener('input', updateFilter);

document.getElementById('resetBtn')?.addEventListener('click', () => {
    redCyanSlider.value = 100;
    greenMagentaSlider.value = 100;
    blueYellowSlider.value = 100;
    updateFilter();
});

document.querySelectorAll('.preset-btn').forEach(btn => {
    btn.addEventListener('click', () => {
        const preset = presets[btn.dataset.preset];
        redCyanSlider.value = preset.redCyan;
        greenMagentaSlider.value = preset.greenMagenta;
        blueYellowSlider.value = preset.blueYellow;
        updateFilter();
    });
});

// Gestion des yeux - Création dynamique
let currentEyeLayer = null;

function setEyeColor(eyeColor) {
    // Supprimer l'ancien layer
    if (currentEyeLayer) {
        currentEyeLayer.remove();
        currentEyeLayer = null;
    }
    
    // Si brown, ne rien créer
    if (eyeColor === 'brown') return;
    
    // Créer le nouveau layer
    const eyeImg = document.createElement('img');
    eyeImg.src = `${imageBase}/eyes/eyes_${eyeColor}.png`;
    eyeImg.alt = `Yeux ${eyeColor}`;
    eyeImg.className = 'character-layer-eyes absolute top-0 left-0 w-full h-full object-contain';
    eyeImg.id = `eyes-${eyeColor}`;
    
    eyesContainer.appendChild(eyeImg);
    currentEyeLayer = eyeImg;
}

eyeColorSelect?.addEventListener('change', function() {
    setEyeColor(this.value);
});

// Gestion du maquillage - Création dynamique
const activeMakeupLayers = new Map();

function toggleMakeup(makeupFile, isChecked) {
    if (isChecked) {
        // Créer le layer si coché
        if (!activeMakeupLayers.has(makeupFile)) {
            const makeupImg = document.createElement('img');
            makeupImg.src = `${imageBase}/makeup/${makeupFile}.png`;
            makeupImg.alt = 'Maquillage';
            makeupImg.className = 'character-layer-makeup absolute top-0 left-0 w-full h-full object-contain';
            makeupImg.id = `makeup-${makeupFile.replace(/[^a-zA-Z0-9]/g, '-')}`;
            
            makeupContainer.appendChild(makeupImg);
            activeMakeupLayers.set(makeupFile, makeupImg);
        }
    } else {
        // Supprimer le layer si décoché
        const layer = activeMakeupLayers.get(makeupFile);
        if (layer) {
            layer.remove();
            activeMakeupLayers.delete(makeupFile);
        }
    }
}

// Tabs avec zoom
document.querySelectorAll('.tab-item').forEach(tab => {
    tab.addEventListener('click', () => {
        document.querySelectorAll('.tab-item').forEach(t => t.classList.remove('active'));
        tab.classList.add('active');
        
        document.querySelectorAll('.edit-section').forEach(s => s.classList.add('hidden'));
        document.getElementById(tab.dataset.tab + 'Section').classList.remove('hidden');
        
        if (tab.dataset.tab === 'finish') {
            characterViewer.classList.remove('zoomed');
        } else {
            characterViewer.classList.add('zoomed');
        }
    });
});

// Initialisation - Charger l'apparence sauvegardée
function initAppearance() {
    updateFilter();
    
    // Charger les yeux sauvegardés
    const savedEyeColor = '<?= $character['appearance']['eyes']['color'] ?? 'brown' ?>';
    setEyeColor(savedEyeColor);
    
    // Charger les maquillages sauvegardés
    <?php if (isset($character['appearance']['makeup']) && is_array($character['appearance']['makeup'])): ?>
        <?php foreach ($character['appearance']['makeup'] as $makeupFile => $isActive): ?>
            <?php if ($isActive === true): ?>
                toggleMakeup('<?= htmlspecialchars($makeupFile) ?>', true);
            <?php endif; ?>
        <?php endforeach; ?>
    <?php endif; ?>
    
    // Démarrer avec le zoom activé
    characterViewer.classList.add('zoomed');
}

// Lancer l'initialisation
initAppearance();
</script>

<?php
$content = ob_get_clean();
require __DIR__ . '/../layouts/base.php';