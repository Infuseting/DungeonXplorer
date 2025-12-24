<?php
$title = 'Personnalisation - DungeonXplorer';
$bodyClass = 'bg-gradient-to-br from-slate-950 to-indigo-950 overflow-hidden';
$customStyles = '
    .character-container {
        background: url("/assets/images/character_appearance_bg.png") no-repeat center/cover;
        min-height: 100vh;
        max-height: 100vh;
        overflow: hidden;
        background-attachment: fixed;
    }
    
    .character-viewer {
        padding: 2rem;
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
        max-height: calc(100vh - 12rem);
        overflow-y: auto;
    }
    
    /* Scrollbar personnalisée pour le panel */
    .customization-panel::-webkit-scrollbar {
        width: 8px;
    }
    
    .customization-panel::-webkit-scrollbar-track {
        background: rgba(30, 41, 59, 0.5);
        border-radius: 4px;
    }
    
    .customization-panel::-webkit-scrollbar-thumb {
        background: rgba(139, 92, 246, 0.5);
        border-radius: 4px;
    }
    
    .customization-panel::-webkit-scrollbar-thumb:hover {
        background: rgba(139, 92, 246, 0.7);
    }
    
    .tab-navigation {
        display: flex;
        gap: 0.5rem;
        margin-bottom: 2rem;
        border-bottom: 2px solid rgba(139, 92, 246, 0.2);
        padding-bottom: 0.5rem;
        flex-wrap: wrap;
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
        flex: 1;
        text-align: center;
        min-width: fit-content;
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
    
    .eye-radio-wrapper {
        transition: all 0.3s ease;
    }
    
    .eye-radio-wrapper:hover {
        background: rgba(139, 92, 246, 0.1);
        transform: translateX(5px);
    }
    
    .eye-radio-wrapper:has(input:checked) {
        background: rgba(139, 92, 246, 0.15);
        border-color: rgba(139, 92, 246, 0.6);
    }
    
    .eye-radio {
        cursor: pointer;
        accent-color: #8b5cf6;
    }
    
    .eye-radio:focus {
        outline: 2px solid rgba(139, 92, 246, 0.5);
        outline-offset: 2px;
    }
    
    /* ==================== RESPONSIVE STYLES ==================== */
    
    /* Tablets et petits écrans (iPad, etc.) */
    @media (max-width: 1024px) {
        .character-viewer {
            padding: 1.5rem;
        }
        
        .character-preview-wrapper {
            width: 450px;
            height: 550px;
        }
        
        .character-viewer.zoomed .character-preview-wrapper {
            width: 450px;
            height: 500px;
        }
        
        .customization-panel {
            padding: 1.25rem;
            /* Allow flex to handle height */
            max-height: none; 
            height: 100%;
        }
        
        .tab-item {
            padding: 0.65rem 1rem;
            font-size: 0.9rem;
        }
        
        .section-title {
            font-size: 1.1rem;
        }
    }
    
    /* Petites tablettes et grands mobiles (paysage) */
    @media (max-width: 768px) {
        .character-container {
            min-height: 100vh;
            max-height: 100vh;
        }
        
        .character-viewer {
            padding: 1rem;
        }
        
        .character-preview-wrapper {
            width: 300px;
            height: 380px;
        }
        
        .character-viewer.zoomed .character-preview-wrapper {
            width: 300px;
            height: 350px;
        }
        
        .character-viewer.zoomed .character-preview-wrapper img {
            transform: scale(1.5) translateY(0%);
        }
        
        .customization-panel {
            padding: 1rem;
            /* Allow flex to handle height */
            max-height: none;
            height: 100%;
        }
        
        .tab-navigation {
            gap: 0.35rem;
            margin-bottom: 1.5rem;
        }
        
        .tab-item {
            padding: 0.6rem 0.75rem;
            font-size: 0.85rem;
        }
        
        .preset-grid {
            grid-template-columns: repeat(2, 1fr);
            gap: 0.6rem;
        }
        
        .section-title {
            font-size: 1rem;
            margin-bottom: 1.25rem;
        }
        
        .control-label {
            font-size: 0.9rem;
        }
        
        .btn-submit {
            padding: 1rem;
            font-size: 1rem;
        }
    }
    
    /* Mobiles en mode portrait */
    @media (max-width: 640px) {
        .character-viewer {
            padding: 0.5rem;
        }
        
        .character-preview-wrapper {
            width: 250px;
            height: 320px;
        }
        
        .character-viewer.zoomed .character-preview-wrapper {
            width: 250px;
            height: 300px;
        }
        
        .character-viewer.zoomed .character-preview-wrapper img {
            transform: scale(1.4) translateY(0%);
        }
        
        .customization-panel {
            padding: 0.875rem;
            /* Allow flex to handle height */
            max-height: none;
            height: 100%;
        }
        
        .tab-navigation {
            gap: 0.25rem;
            margin-bottom: 1rem;
        }
        
        .tab-item {
            padding: 0.5rem 0.5rem;
            font-size: 0.75rem;
            flex: 1 1 auto;
        }
        
        .control-group {
            margin-bottom: 1.25rem;
        }
        
        .control-label {
            font-size: 0.85rem;
        }
        
        .control-value {
            min-width: 2.5rem;
            font-size: 0.9rem;
        }
        
        .preset-grid {
            grid-template-columns: repeat(2, 1fr);
            gap: 0.5rem;
        }
        
        .preset-btn {
            padding: 0.6rem;
            font-size: 0.8rem;
        }
        
        .section-title {
            font-size: 0.95rem;
            margin-bottom: 1rem;
        }
        
        .finish-message {
            font-size: 0.95rem;
            margin-bottom: 1.5rem;
        }
        
        .btn-submit {
            padding: 0.875rem;
            font-size: 0.95rem;
        }
        
        .btn-reset {
            padding: 0.75rem;
            font-size: 0.9rem;
        }
        
        .custom-select {
            padding: 0.875rem 2.5rem 0.875rem 1rem;
            font-size: 0.9rem;
        }
        
        .makeup-checkbox-wrapper {
            padding: 0.65rem;
        }
        
        .makeup-checkbox-wrapper span {
            font-size: 0.9rem;
        }
        
        input[type="range"]::-webkit-slider-thumb {
            width: 16px;
            height: 16px;
        }
        
        input[type="range"]::-moz-range-thumb {
            width: 16px;
            height: 16px;
        }
    }
    
    /* Très petits mobiles */
    @media (max-width: 375px) {
        .character-preview-wrapper {
            width: 220px;
            height: 280px;
        }
        
        .character-viewer.zoomed .character-preview-wrapper {
            width: 220px;
            height: 260px;
        }
        
        .character-viewer.zoomed .character-preview-wrapper img {
            transform: scale(1.3) translateY(0%);
        }
        
        .tab-item {
            padding: 0.45rem 0.4rem;
            font-size: 0.7rem;
        }
        
        .customization-panel {
            padding: 0.75rem;
            max-height: none;
        }
        
        .section-title {
            font-size: 0.9rem;
        }
        
        .control-label {
            font-size: 0.8rem;
        }
        
        .preset-btn {
            padding: 0.5rem;
            font-size: 0.75rem;
        }
        
        .btn-submit,
        .btn-reset {
            font-size: 0.875rem;
        }
    }
    
    /* Ajustement du header pour mobile */
    @media (max-width: 640px) {
        .max-w-7xl .text-center.mb-8 h1 {
            font-size: 1.5rem;
        }
        
        .max-w-7xl .text-center.mb-8 p {
            font-size: 0.95rem;
        }
        
        .max-w-7xl .text-center.mb-8 a {
            font-size: 0.875rem;
        }
        
        .max-w-7xl .text-center.mb-8 svg {
            width: 1rem;
            height: 1rem;
        }
    }
';

$className = strtolower($character['class']['name']);
$imageBase = "/assets/images/{$className}";

ob_start();
?>

<div class="character-container">
    <div class="max-w-7xl mx-auto p-6 sm:p-4 md:p-6 h-screen flex flex-col">
        <!-- Header -->
        <div class="text-center mb-6 sm:mb-4 flex-shrink-0">
            <div class="flex items-center justify-between mb-3 sm:mb-2">
                <a href="/personnage/<?= $character['id'] === 'preview' ? 'create' : '' ?>" 
                   class="text-gray-400 hover:text-white transition flex items-center gap-2 sm:gap-1">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 sm:h-4 sm:w-4" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z" clip-rule="evenodd" />
                    </svg>
                    <span class="sm:text-sm"><?= $character['id'] === 'preview' ? 'Annuler' : 'Retour' ?></span>
                </a>
                <h1 class="text-4xl sm:text-2xl md:text-3xl font-bold text-white">Créer personnage</h1>
                <div class="w-24 sm:w-16"></div> <!-- Spacer pour centrer le titre -->
            </div>
            <p class="text-violet-300 text-lg sm:text-base"><?= htmlspecialchars($character['name']) ?></p>
        </div>
        
        <div class="flex flex-col lg:grid lg:grid-cols-2 gap-4 lg:gap-6 flex-1 min-h-0 overflow-hidden">
            <!-- Character Preview -->
            <div class="character-viewer flex items-center justify-center lg:order-1 flex-shrink-0 lg:flex-shrink" id="characterViewer">
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
            <div class="customization-panel lg:order-2 flex-1 min-h-0 overflow-y-auto">
                <form action="/personnage/apparence/<?= $character['id'] ?>" method="POST" id="appearanceForm">
                    
                    <!-- Tab Navigation -->
                    <div class="tab-navigation">
                        <div class="tab-item" data-tab="hair">Cheveux</div>
                        <div class="tab-item" data-tab="eyes">Yeux</div>
                        <div class="tab-item" data-tab="makeup">Maquillage</div>
                        <div class="tab-item" data-tab="finish">Terminer</div>
                    </div>

                    <!-- intro text -->
                    <div class="intro-text mb-4 lg:mb-6 text-gray-300 mx-4 lg:mx-8 hidden lg:block">
                        <p class="mb-2 text-3xl text-font-bold">Personnalisez l'apparence de votre personnage avant de commencer votre aventure dans DungeonXplorer !<br><br> Utilisez les onglets ci-dessus pour modifier les cheveux, les yeux et le maquillage. <br><br>Une fois satisfait, cliquez sur "Commencer l'aventure".</p>
                    </div>

                    <!-- Hair Section -->
                    <div class="edit-section hidden" id="hairSection">
                        <h3 class="section-title">Modifier le style et la couleur des cheveux</h3>
                        
                        <!-- Option Naturelle/Custom -->
                        <div class="control-group">
                            <label class="flex items-center gap-3 p-3 bg-gray-800/50 border border-gray-700 rounded-lg cursor-pointer hover:border-violet-500 transition">
                                <input type="checkbox" 
                                       name="hair_natural" 
                                       id="hairNaturalCheckbox"
                                       class="w-5 h-5 rounded border-gray-600 text-violet-600 focus:ring-violet-500 focus:ring-offset-gray-900"
                                       <?= isset($character['appearance']['hair']['natural']) && $character['appearance']['hair']['natural'] === true ? 'checked' : '' ?>>
                                <span class="text-white font-medium">Cheveux naturels (sans coloration)</span>
                            </label>
                        </div>

                        <div id="hairCustomization">
                            <div class="control-group">
                                <div class="control-label">
                                    <span>Cyan / Rouge</span>
                                    <span id="redCyanValue" class="control-value">100</span>
                                </div>
                                <input type="range" name="hair_red_cyan" id="redCyanSlider" min="0" max="200" value="<?= $character['appearance']['hair']['redCyan'] ?? 100 ?>">
                            </div>
                            
                            <div class="control-group">
                                <div class="control-label">
                                    <span>Magenta / Vert</span>
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
                    </div>

                    <!-- Eyes Section -->
                    <div class="edit-section hidden" id="eyesSection">
                        <h3 class="section-title">Couleur des yeux</h3>
                        <p class="text-gray-400 text-sm mb-4 sm:text-xs">Sélectionnez une couleur d'yeux</p>
                        <div class="space-y-3 sm:space-y-2">
                            <!-- Option par défaut : Marron -->
                            <label class="eye-radio-wrapper flex items-center gap-3 sm:gap-2 p-3 sm:p-2 bg-gray-800/50 border border-gray-700 rounded-lg cursor-pointer hover:border-violet-500 transition">
                                <input type="radio" 
                                       name="eye_color" 
                                       value="brown" 
                                       class="eye-radio w-5 h-5 sm:w-4 sm:h-4 border-gray-600 text-violet-600 focus:ring-violet-500 focus:ring-offset-gray-900"
                                       <?= ($character['appearance']['eyes']['color'] ?? 'brown') === 'brown' ? 'checked' : '' ?>
                                       data-eye-color="brown">
                                <span class="text-white font-medium sm:text-sm">Marron (défaut)</span>
                            </label>
                            
                            <?php foreach ($appearanceOptions['eyes'] as $eyeType => $eyeLabel): ?>
                                <label class="eye-radio-wrapper flex items-center gap-3 sm:gap-2 p-3 sm:p-2 bg-gray-800/50 border border-gray-700 rounded-lg cursor-pointer hover:border-violet-500 transition">
                                    <input type="radio" 
                                           name="eye_color" 
                                           value="<?= htmlspecialchars($eyeType) ?>" 
                                           class="eye-radio w-5 h-5 sm:w-4 sm:h-4 border-gray-600 text-violet-600 focus:ring-violet-500 focus:ring-offset-gray-900"
                                           <?= ($character['appearance']['eyes']['color'] ?? '') === $eyeType ? 'checked' : '' ?>
                                           data-eye-color="<?= htmlspecialchars($eyeType) ?>">
                                    <span class="text-white font-medium sm:text-sm"><?= htmlspecialchars($eyeLabel) ?></span>
                                </label>
                            <?php endforeach; ?>
                            
                            <?php if (empty($appearanceOptions['eyes'])): ?>
                                <p class="text-gray-500 text-center py-4 sm:py-3 sm:text-sm">Aucune option disponible pour cette classe</p>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Makeup Section -->
                    <div class="edit-section hidden" id="makeupSection">
                        <h3 class="section-title">Maquillage et tatouages</h3>
                        <p class="text-gray-400 text-sm mb-4 sm:text-xs">Vous pouvez sélectionner plusieurs options</p>
                        <div class="space-y-3 sm:space-y-2">
                            <?php foreach ($appearanceOptions['makeup'] as $makeupFile => $makeupLabel): 
                                $isChecked = isset($character['appearance']['makeup'][$makeupFile]) && $character['appearance']['makeup'][$makeupFile] === true;
                            ?>
                                <label class="makeup-checkbox-wrapper flex items-center gap-3 sm:gap-2 p-3 sm:p-2 bg-gray-800/50 border border-gray-700 rounded-lg cursor-pointer hover:border-violet-500 transition">
                                    <input type="checkbox" 
                                           name="makeup[]" 
                                           value="<?= htmlspecialchars($makeupFile) ?>" 
                                           class="makeup-checkbox w-5 h-5 sm:w-4 sm:h-4 rounded border-gray-600 text-violet-600 focus:ring-violet-500 focus:ring-offset-gray-900"
                                           <?= $isChecked ? 'checked' : '' ?>
                                           onchange="toggleMakeup(<?= htmlspecialchars(json_encode($makeupFile)) ?>, this.checked)">
                                    <span class="text-white font-medium sm:text-sm"><?= htmlspecialchars($makeupLabel) ?></span>
                                </label>
                            <?php endforeach; ?>
                            
                            <?php if (empty($appearanceOptions['makeup'])): ?>
                                <p class="text-gray-500 text-center py-4 sm:py-3 sm:text-sm">Aucun maquillage disponible pour cette classe</p>
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

const eyeOptions = <?= json_encode($appearanceOptions['eyes']) ?>;
const makeupOptions = <?= json_encode(array_keys($appearanceOptions['makeup'])) ?>;

const hairNaturalCheckbox = document.getElementById('hairNaturalCheckbox');
const hairCustomization = document.getElementById('hairCustomization');

function toggleHairNatural(isNatural) {
    if (isNatural) {
                hairImage.style.display = 'none';
                hairCustomization.style.opacity = '0.5';
        hairCustomization.style.pointerEvents = 'none';
                redCyanSlider.disabled = true;
        greenMagentaSlider.disabled = true;
        blueYellowSlider.disabled = true;
    } else {
                hairImage.style.display = 'block';
                hairCustomization.style.opacity = '1';
        hairCustomization.style.pointerEvents = 'auto';
                redCyanSlider.disabled = false;
        greenMagentaSlider.disabled = false;
        blueYellowSlider.disabled = false;
                updateFilter();
    }
}

hairNaturalCheckbox?.addEventListener('change', function() {
    toggleHairNatural(this.checked);
});

const presets = {
    original: { redCyan: 100, greenMagenta: 100, blueYellow: 100 },
    blonde: { redCyan: 110, greenMagenta: 90, blueYellow: 10 },
    redhead: { redCyan: 200, greenMagenta: 60, blueYellow: 0 },
    brown: { redCyan: 115, greenMagenta: 70, blueYellow: 35 },
    black: { redCyan: 25, greenMagenta: 25, blueYellow: 25 },
    blue: { redCyan: 10, greenMagenta: 50, blueYellow: 200 },
    pink: { redCyan: 200, greenMagenta: 30, blueYellow: 110 }
};

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

let currentEyeLayer = null;

function setEyeColor(eyeColor) {
        if (currentEyeLayer) {
        currentEyeLayer.remove();
        currentEyeLayer = null;
    }
    
        if (eyeColor === 'brown') return;
    
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

const activeMakeupLayers = new Map();

function toggleMakeup(makeupFile, isChecked) {
    if (isChecked) {
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
                const layer = activeMakeupLayers.get(makeupFile);
        if (layer) {
            layer.remove();
            activeMakeupLayers.delete(makeupFile);
        }
    }
}

document.querySelectorAll('.tab-item').forEach(tab => {
    tab.addEventListener('click', () => {
        document.querySelectorAll('.tab-item').forEach(t => t.classList.remove('active'));
        tab.classList.add('active');
        
        document.querySelectorAll('.intro-text').forEach(t => t.classList.add('hidden'));
        document.querySelectorAll('.edit-section').forEach(s => s.classList.add('hidden'));
        document.getElementById(tab.dataset.tab + 'Section').classList.remove('hidden');
        
        if (tab.dataset.tab === 'finish') {
            characterViewer.classList.remove('zoomed');
        } else {
            characterViewer.classList.add('zoomed');
        }
    });
});

document.querySelectorAll('.eye-radio').forEach(radio => {
    radio.addEventListener('change', function() {
        if (this.checked) {
            const eyeColor = this.dataset.eyeColor;
            setEyeColor(eyeColor);
        }
    });
});

function initAppearance() {
    updateFilter();
    
        const isNatural = <?= isset($character['appearance']['hair']['natural']) && $character['appearance']['hair']['natural'] === true ? 'true' : 'false' ?>;
    if (isNatural) {
        toggleHairNatural(true);
    }
    
        const savedEyeColor = '<?= $character['appearance']['eyes']['color'] ?? 'brown' ?>';
    setEyeColor(savedEyeColor);
    
        <?php if (isset($character['appearance']['makeup']) && is_array($character['appearance']['makeup'])): ?>
        <?php foreach ($character['appearance']['makeup'] as $makeupFile => $isActive): ?>
            <?php if ($isActive === true): ?>
                toggleMakeup('<?= htmlspecialchars($makeupFile) ?>', true);
            <?php endif; ?>
        <?php endforeach; ?>
    <?php endif; ?>
}

initAppearance();
</script>

<?php
$content = ob_get_clean();
require __DIR__ . '/../layouts/base.php';