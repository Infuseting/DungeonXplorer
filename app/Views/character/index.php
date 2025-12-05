<?php
$title = 'Sélection du Personnage - DungeonXplorer';
$bodyClass = 'bg-gray-900 min-h-screen overflow-hidden';
$customStyles = '
    .character-card {
        transition: all 0.3s ease;
    }
    .character-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 25px -5px rgba(124, 58, 237, 0.5);
    }
    .selected-glow {
        box-shadow: 0 0 30px 5px rgba(124, 58, 237, 0.6);
    }
';

ob_start();
?>

<!-- Background Image -->
<div class="absolute inset-0 z-0">
    <img src="/assets/images/character_select_bg.png" alt="Background" class="w-full h-full object-cover opacity-50">
    <div class="absolute inset-0 bg-gradient-to-t from-gray-900 via-transparent to-gray-900/80"></div>
    <div class="absolute inset-0 bg-gradient-to-r from-gray-900/80 via-transparent to-gray-900/80"></div>
</div>

<div class="relative z-10 min-h-screen flex flex-col">
    <!-- Header -->
    <header class="flex justify-between items-center p-4 md:p-6 bg-gray-900/50 backdrop-blur-sm lg:bg-transparent relative z-50">
        <!-- Mobile: Character Menu Button / Desktop: Title -->
        <button onclick="toggleCharacterMenu()" class="lg:hidden flex items-center gap-2 px-4 py-2 bg-gray-800/90 border border-gray-700 rounded-lg text-white shadow-lg backdrop-blur">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M3 5a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zM3 10a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zM3 15a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z" clip-rule="evenodd" />
            </svg>
            <span>Changer de Héros</span>
        </button>
        <div class="hidden lg:block text-xl md:text-2xl font-bold text-violet-500 tracking-wider uppercase">DungeonXplorer</div>
        
        <!-- User Menu (Top Right) -->
        <div class="relative">
            <button id="user-menu-button" class="w-12 h-12 bg-violet-600 hover:bg-violet-700 text-white rounded-full shadow-lg border-2 border-violet-400 flex items-center justify-center transition-transform hover:scale-110 active:scale-95 text-xl font-bold">
                <?= strtoupper(substr($_SESSION['username'], 0, 1)) ?>
            </button>
            
            <!-- Dropdown Menu -->
            <div id="user-dropdown" class="hidden absolute top-14 right-0 w-48 bg-gray-800 border-2 border-gray-600 rounded-lg shadow-xl overflow-hidden z-50">
                <button id="settings-button" class="w-full px-4 py-3 text-left text-white hover:bg-gray-700 transition-colors flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                    Paramètres
                </button>
                <a href="/logout" class="block w-full px-4 py-3 text-left text-white hover:bg-gray-700 transition-colors flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                    </svg>
                    Déconnexion
                </a>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="flex-1 flex items-center justify-center px-4 py-8 md:py-0 pb-32 lg:pb-0 relative overflow-hidden">
        <div class="w-full max-w-7xl grid grid-cols-1 lg:grid-cols-12 gap-8 items-center h-full">
            
            <!-- Mobile Drawer Menu (Character List) -->
            <div id="mobile-menu-overlay" class="fixed inset-0 bg-black/50 z-30 hidden lg:hidden transition-opacity duration-300" onclick="toggleCharacterMenu()"></div>
            <div id="mobile-menu" class="fixed inset-y-0 left-0 w-80 bg-gray-900 border-r border-gray-800 z-[60] transform -translate-x-full transition-transform duration-300 lg:hidden flex flex-col">
                <div class="p-6 border-b border-gray-800 flex justify-between items-center">
                    <h2 class="text-xl font-bold text-white">Vos Héros</h2>
                    <button onclick="toggleCharacterMenu()" class="text-gray-400 hover:text-white">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
                <div class="flex-1 overflow-y-auto p-4 space-y-4">
                    <?php foreach ($characters as $char): 
                        $image = $classImages[$char['class_name']] ?? 'class_warrior.png';
                    ?>
                        <div class="character-card char-card-<?= $char['id'] ?> cursor-pointer bg-gray-800 border border-gray-700 p-3 rounded-lg flex items-center gap-4 <?= ($char['id'] === $selectedCharacter['id']) ? 'border-violet-500 ring-1 ring-violet-500' : '' ?>"
                             onclick="selectCharacter(<?= $char['id'] ?>)">
                            <div class="w-10 h-10 rounded-full bg-gray-700 flex items-center justify-center overflow-hidden border border-gray-600 flex-shrink-0">
                                <img src="/assets/images/<?= $image ?>" alt="<?= $char['class_name'] ?>" class="w-full h-full object-cover">
                            </div>
                            <div>
                                <div class="text-white font-bold"><?= htmlspecialchars($char['name']) ?></div>
                                <div class="text-xs text-gray-400">Niveau <?= $char['level'] ?> <?= $char['class_name'] ?></div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                    
                    <a href="/personnage/create" class="block w-full py-3 border-2 border-dashed border-gray-700 text-gray-500 rounded-lg text-center hover:border-violet-500 hover:text-violet-400 transition">
                        <span class="text-xl block mb-1">+</span>
                        Créer un nouveau héros
                    </a>
                </div>
            </div>
            <!-- Stats / Actions (Right on Desktop, Fixed Bottom on Mobile) -->
            <div class="hidden lg:block lg:order-3 lg:col-span-3 bg-gray-800/80 backdrop-blur p-4 md:p-6 rounded-xl border border-gray-700">
                <!-- Stats -->
                <div class="space-y-3 mb-6 md:mb-8">
                    <h3 class="text-lg font-medium text-white mb-4 border-b border-gray-700 pb-2">Statistiques</h3>
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-400">Force</span>
                        <span id="stat-strength" class="text-white font-bold"><?= $selectedCharacter['strength'] ?? 10 ?></span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-400">Dextérité</span>
                        <span id="stat-dexterity" class="text-white font-bold"><?= $selectedCharacter['dexterity'] ?? 10 ?></span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-400">Intelligence</span>
                        <span id="stat-intelligence" class="text-white font-bold"><?= $selectedCharacter['intelligence'] ?? 10 ?></span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-400">Vitalité</span>
                        <span id="stat-vitality" class="text-white font-bold"><?= $selectedCharacter['vitality'] ?? 10 ?></span>
                    </div>
                </div>

                <div class="flex flex-col gap-3">
                    <form action="/game" method="POST">
                        <input type="hidden" id="selected-character-id" name="character_id" value="<?= $selectedCharacter['id'] ?>">
                        <button type="submit" class="w-full py-3 md:py-4 bg-violet-600 hover:bg-violet-700 text-white font-bold rounded-lg shadow-lg shadow-violet-600/30 transition transform hover:-translate-y-1 uppercase tracking-wider text-lg">
                            Jouer
                        </button>
                    </form>
                    <button onclick="openDeleteModal()" class="w-full py-2 border border-gray-600 text-gray-300 hover:bg-gray-700 rounded-lg transition text-sm">
                        Supprimer
                    </button>
                </div>
            </div>

            <!-- Mobile Action Buttons (Fixed Bottom) -->
            <div class="lg:hidden fixed bottom-0 left-0 right-0 z-50 bg-gray-900/95 backdrop-blur-lg border-t border-gray-800 p-4" style="padding-bottom: calc(1rem + env(safe-area-inset-bottom));">
                <div class="flex flex-col gap-3 max-w-md mx-auto">
                    <form action="/game" method="POST">
                        <input type="hidden" class="mobile-character-id" name="character_id" value="<?= $selectedCharacter['id'] ?>">
                        <button type="submit" class="w-full py-3 bg-violet-600 hover:bg-violet-700 text-white font-bold rounded-lg shadow-lg shadow-violet-600/30 transition active:scale-95 uppercase tracking-wider text-base">
                            Jouer
                        </button>
                    </form>
                    <button onclick="openDeleteModal()" class="w-full py-2 border border-gray-600 text-gray-300 hover:bg-gray-700 rounded-lg transition text-sm active:scale-95">
                        Supprimer
                    </button>
                </div>
            </div>
            <!-- Character Preview (Center) -->
            <div class="order-1 lg:order-2 lg:col-span-6 flex flex-col items-center justify-between relative h-[50vh] lg:h-[70vh] py-4">
                <!-- Pedestal Effect -->
                <div class="absolute bottom-0 w-full h-1/4 bg-gradient-to-t from-violet-900/20 to-transparent rounded-full blur-3xl"></div>
                
                <!-- Character Model avec le helper -->
                <div class="relative z-10 flex-1 w-full flex items-end justify-center pb-4" id="character-container" style="max-height: calc(100% - 120px);">
                    <?= renderCharacter($selectedCharacter, [
                        'size' => 'full',
                        'showFilter' => true,
                        'id' => 'character-' . $selectedCharacter['id'],
                        'class' => 'h-full w-auto max-w-full object-contain drop-shadow-2xl hover:brightness-110 transition duration-500'
                    ]) ?>
                </div>

                <!-- Selected Character Info -->
                <div class="relative z-10 text-center pb-2" style="min-height: 100px;">
                    <h1 id="character-name" class="text-3xl md:text-4xl lg:text-5xl font-bold text-white mb-1 md:mb-2 text-shadow-lg"><?= htmlspecialchars($selectedCharacter['name']) ?></h1>
                    <p id="character-details" class="text-base md:text-lg lg:text-xl text-violet-400 font-medium tracking-wide">
                        Niveau <?= $selectedCharacter['level'] ?> <?= $selectedCharacter['class_name'] ?>
                    </p>
                </div>
            </div>

            <!-- Desktop Character List -->
            <div class="hidden lg:block lg:col-span-3 space-y-4 max-h-[60vh] overflow-y-auto pr-2 custom-scrollbar lg:order-1">
                <h2 class="text-xl text-gray-400 font-medium mb-6 border-b border-gray-700 pb-2">Vos Héros</h2>
                <?php foreach ($characters as $char): ?>
                    <div class="character-card char-card-<?= $char['id'] ?> cursor-pointer bg-gray-800/80 backdrop-blur border border-gray-700 p-4 rounded-lg flex items-center gap-4 <?= ($char['id'] === $selectedCharacter['id']) ? 'border-violet-500 ring-1 ring-violet-500 bg-gray-800' : 'hover:border-gray-500' ?>"
                         onclick="selectCharacter(<?= $char['id'] ?>)">
                        <div class="w-12 h-12 rounded-full bg-gray-700 flex items-center justify-center overflow-hidden border border-gray-600">
                            <?= renderCharacter($char, [
                                'size' => 'small',
                                'showFilter' => true,
                                'id' => 'card-char-' . $char['id'],
                                'class' => 'w-full h-full object-cover'
                            ]) ?>
                        </div>
                        <div>
                            <div class="text-white font-bold"><?= htmlspecialchars($char['name']) ?></div>
                            <div class="text-sm text-gray-400">Niveau <?= $char['level'] ?> <?= $char['class_name'] ?></div>
                        </div>
                    </div>
                <?php endforeach; ?>
                <a href="/personnage/create" class="block w-full py-4 border-2 border-dashed border-gray-700 text-gray-500 rounded-lg text-center hover:border-violet-500 hover:text-violet-400 transition group">
                    <span class="text-2xl block mb-1">+</span>
                    Créer un nouveau héros
                </a>
            </div>

            

        </div>
    </main>
    
    <!-- Delete Confirmation Modal -->
    <div id="delete-modal" class="fixed inset-0 z-50 hidden">
        <!-- Backdrop -->
        <div class="absolute inset-0 bg-black/70 backdrop-blur-sm transition-opacity opacity-0 duration-300" id="delete-modal-backdrop" onclick="closeDeleteModal()"></div>
        
        <!-- Modal Content -->
        <div class="absolute inset-0 flex items-center justify-center p-4 pointer-events-none">
            <div class="bg-gray-900 border border-gray-700 rounded-xl shadow-2xl max-w-md w-full transform scale-95 opacity-0 transition-all duration-300 pointer-events-auto" id="delete-modal-content">
                <div class="p-6 text-center">
                    <div class="w-16 h-16 bg-red-500/10 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-white mb-2">Supprimer ce héros ?</h3>
                    <p class="text-gray-400 mb-6">Cette action est irréversible. Toutes les données associées à ce personnage seront perdues.</p>
                    
                    <div class="flex gap-3">
                        <button onclick="closeDeleteModal()" class="flex-1 py-2 px-4 bg-gray-800 hover:bg-gray-700 text-gray-300 rounded-lg transition border border-gray-700">
                            Annuler
                        </button>
                        <form action="/personnage/delete" method="POST" class="flex-1">
                            <input type="hidden" name="character_id" id="modal-character-id">
                            <button type="submit" class="w-full py-2 px-4 bg-red-600 hover:bg-red-700 text-white rounded-lg transition shadow-lg shadow-red-600/20">
                                Supprimer
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </div>
    
    <!-- Settings Modal -->
    <?php require __DIR__ . '/../game/components/settings-modal.php'; ?>
</div>

<script>
const characters = <?= json_encode($characters) ?>;
const classImages = <?= json_encode($classImages) ?>;

// User Menu & Settings Logic
document.addEventListener('DOMContentLoaded', async () => {
    // User menu dropdown
    const userMenuButton = document.getElementById('user-menu-button');
    const userDropdown = document.getElementById('user-dropdown');
    
    if (userMenuButton && userDropdown) {
        userMenuButton.addEventListener('click', (e) => {
            e.stopPropagation();
            userDropdown.classList.toggle('hidden');
        });
        
        // Close dropdown when clicking outside
        document.addEventListener('click', (e) => {
            if (!userMenuButton.contains(e.target) && !userDropdown.contains(e.target)) {
                userDropdown.classList.add('hidden');
            }
        });
    }
    
    // Settings modal
    const settingsButton = document.getElementById('settings-button');
    const settingsModal = document.getElementById('settings-modal');
    const settingsCloseBtn = document.getElementById('settings-close-btn');
    const settingsSaveBtn = document.getElementById('settings-save-btn');
    
    if (settingsButton && settingsModal) {
        settingsButton.addEventListener('click', async () => {
            userDropdown.classList.add('hidden');
            settingsModal.classList.remove('hidden');
            
            // Load current volumes
            try {
                const { getVolumes } = await import('/js/modules/soundManager.js');
                const volumes = getVolumes();
                document.getElementById('master-volume').value = Math.round(volumes.master * 100);
                document.getElementById('music-volume').value = Math.round(volumes.music * 100);
                document.getElementById('sfx-volume').value = Math.round(volumes.sfx * 100);
                updateVolumeDisplays();
            } catch (e) {
                console.warn('SoundManager not loaded yet', e);
            }
        });
    }
    
    if (settingsCloseBtn) {
        settingsCloseBtn.addEventListener('click', () => {
            settingsModal.classList.add('hidden');
        });
    }
    
    // Volume sliders
    const masterVolumeSlider = document.getElementById('master-volume');
    const musicVolumeSlider = document.getElementById('music-volume');
    const sfxVolumeSlider = document.getElementById('sfx-volume');
    
    function updateVolumeDisplays() {
        if (masterVolumeSlider) document.getElementById('master-volume-value').textContent = masterVolumeSlider.value + '%';
        if (musicVolumeSlider) document.getElementById('music-volume-value').textContent = musicVolumeSlider.value + '%';
        if (sfxVolumeSlider) document.getElementById('sfx-volume-value').textContent = sfxVolumeSlider.value + '%';
    }
    
    if (masterVolumeSlider) masterVolumeSlider.addEventListener('input', updateVolumeDisplays);
    if (musicVolumeSlider) musicVolumeSlider.addEventListener('input', updateVolumeDisplays);
    if (sfxVolumeSlider) sfxVolumeSlider.addEventListener('input', updateVolumeDisplays);
    
    // Save settings
    if (settingsSaveBtn) {
        settingsSaveBtn.addEventListener('click', async () => {
            try {
                const { setMasterVolume, setMusicVolume, setSFXVolume } = await import('/js/modules/soundManager.js');
                setMasterVolume(parseInt(masterVolumeSlider.value) / 100);
                setMusicVolume(parseInt(musicVolumeSlider.value) / 100);
                setSFXVolume(parseInt(sfxVolumeSlider.value) / 100);
                
                settingsModal.classList.add('hidden');
                
                // Show toast
                const { showToast } = await import('/js/modules/toast.js');
                showToast('Paramètres sauvegardés !', 'success');
            } catch (e) {
                console.error('Error saving settings:', e);
            }
        });
    }

    // Global SFX Handler
    document.addEventListener('click', async (e) => {
        // Check if element or parent is clickable
        const target = e.target.closest('button, a, .character-card, .slot, .quest-item, .leaflet-interactive');
        
        if (target) {
            // Don't play if sound was already played by specific handler (optional check)
            // For now, we rely on soundManager to handle concurrency or just play it
            try {
                const { playSound } = await import('/js/modules/soundManager.js');
                playSound('click');
            } catch (err) {
                // Sound manager might not be loaded yet
            }
        }
    });
});

function toggleCharacterMenu() {
    const menu = document.getElementById('mobile-menu');
    const overlay = document.getElementById('mobile-menu-overlay');
    
    if (menu.classList.contains('-translate-x-full')) {
        menu.classList.remove('-translate-x-full');
        overlay.classList.remove('hidden');
        setTimeout(() => overlay.classList.remove('opacity-0'), 10);
    } else {
        menu.classList.add('-translate-x-full');
        overlay.classList.add('opacity-0');
        setTimeout(() => overlay.classList.add('hidden'), 300);
    }
}

function selectCharacter(id) {
    const char = characters.find(c => c.id == id);
    if (!char) return;

    // Update Stats
    document.getElementById('stat-strength').textContent = char.strength || 10;
    document.getElementById('stat-dexterity').textContent = char.dexterity || 10;
    document.getElementById('stat-intelligence').textContent = char.intelligence || 10;
    document.getElementById('stat-vitality').textContent = char.vitality || 10;

    // Recharger le personnage avec le helper (via AJAX)
    fetch(`/api/character/${id}/render`)
        .then(response => response.text())
        .then(html => {
            const container = document.getElementById('character-container');
            container.innerHTML = html;
            
            // Réappliquer les filtres de cheveux
            if (window.CharacterRenderer) {
                const renderer = new CharacterRenderer();
                renderer.initAll();
            }
        })
        .catch(error => {
            console.error('Erreur de chargement du personnage:', error);
            // Fallback sur l'ancienne méthode
            updateCharacterFallback(char);
        });

    // Update Info
    document.getElementById('character-name').textContent = char.name;
    document.getElementById('character-details').textContent = `Niveau ${char.level} ${char.class_name}`;
    
    // Update Hidden Inputs (Desktop and Mobile)
    document.getElementById('selected-character-id').value = char.id;
    const mobileInputs = document.querySelectorAll('.mobile-character-id');
    mobileInputs.forEach(input => input.value = char.id);

    // Update Selection Visuals
    document.querySelectorAll('.character-card').forEach(card => {
        card.classList.remove('border-violet-500', 'ring-1', 'ring-violet-500', 'bg-gray-800');
        card.classList.add('hover:border-gray-500');
    });

    const selectedCards = document.querySelectorAll(`.char-card-${id}`);
    selectedCards.forEach(card => {
        card.classList.remove('hover:border-gray-500');
        card.classList.add('border-violet-500', 'ring-1', 'ring-violet-500', 'bg-gray-800');
    });

    // Close mobile menu if open
    const menu = document.getElementById('mobile-menu');
    if (!menu.classList.contains('-translate-x-full')) {
        toggleCharacterMenu();
    }
}

function updateCharacterFallback(char) {
    const imagePath = '/assets/images/' + (classImages[char.class_name] || 'class_warrior.png');
    const imgElement = document.querySelector('#character-container img');
    if (imgElement) {
        imgElement.src = imagePath;
        imgElement.alt = char.class_name;
    }
}

function openDeleteModal() {
    const modal = document.getElementById('delete-modal');
    const backdrop = document.getElementById('delete-modal-backdrop');
    const content = document.getElementById('delete-modal-content');
    const charId = document.getElementById('selected-character-id').value;
    
    document.getElementById('modal-character-id').value = charId;
    
    modal.classList.remove('hidden');
    // Trigger reflow
    void modal.offsetWidth;
    
    backdrop.classList.remove('opacity-0');
    content.classList.remove('scale-95', 'opacity-0');
    content.classList.add('scale-100', 'opacity-100');
}

function closeDeleteModal() {
    const modal = document.getElementById('delete-modal');
    const backdrop = document.getElementById('delete-modal-backdrop');
    const content = document.getElementById('delete-modal-content');
    
    backdrop.classList.add('opacity-0');
    content.classList.remove('scale-100', 'opacity-100');
    content.classList.add('scale-95', 'opacity-0');
    
    setTimeout(() => {
        modal.classList.add('hidden');
    }, 300);
}
</script>

<?php
$content = ob_get_clean();
require __DIR__ . '/../layouts/base.php';
