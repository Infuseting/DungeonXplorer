<?php
$title = 'Création de Personnage - DungeonXplorer';
$bodyClass = 'bg-gray-900 min-h-screen overflow-hidden';
$customStyles = '
    .class-card {
        transition: all 0.3s ease;
    }
    .class-card:hover {
        transform: translateY(-2px);
        border-color: #8b5cf6;
    }
    .class-card.selected {
        border-color: #7c3aed;
        background-color: rgba(124, 58, 237, 0.1);
        box-shadow: 0 0 15px rgba(124, 58, 237, 0.3);
    }
';

// Image mapping
$classImages = [
    'Guerrier' => 'warrior.png',
    'Mage' => 'wizard.png',
    'Voleur' => 'thief.png'
];

ob_start();
?>

<!-- Background -->
<div class="absolute inset-0 z-0">
    <img src="/assets/images/character_create_bg.png" alt="Background" class="w-full h-full object-cover opacity-60">
    <div class="absolute inset-0 bg-gradient-to-b from-gray-900 via-transparent to-gray-900"></div>
    <div class="absolute inset-0 bg-gradient-to-r from-gray-900 via-transparent to-gray-900"></div>
</div>

<div class="relative z-10 min-h-screen flex flex-col">
    <!-- Header -->
    <header class="flex justify-between items-center p-4 md:p-6 bg-gray-900/50 backdrop-blur-sm lg:bg-transparent">
        <div class="flex items-center gap-4">
            <a href="/personnage" class="text-gray-400 hover:text-white transition flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z" clip-rule="evenodd" />
                </svg>
                Retour
            </a>
            <h1 class="text-xl md:text-2xl font-bold text-white">Création de Héros</h1>
        </div>
    </header>

    <form action="/personnage/create" method="POST" class="flex-1 flex flex-col lg:flex-row overflow-hidden">
        
        <!-- Left Panel: Class Selection (Top on Mobile) -->
        <div class="w-full lg:w-1/3 p-4 md:p-6 overflow-y-auto custom-scrollbar bg-gray-900/80 backdrop-blur border-b lg:border-b-0 lg:border-r border-gray-800 max-h-[30vh] lg:max-h-full order-1">
            <h2 class="text-lg font-medium text-violet-400 mb-4 md:mb-6 uppercase tracking-wider">Choisissez votre Destinée</h2>
            
            <div class="flex lg:flex-col gap-4 overflow-x-auto lg:overflow-visible pb-2 lg:pb-0">
                <?php foreach ($classes as $class): 
                    $bigImage = $classImages[$class['name']] ?? 'warrior.png';
                ?>
                    <label class="class-card flex-shrink-0 w-64 lg:w-full block cursor-pointer border border-gray-700 rounded-lg p-3 md:p-4 relative group">
                        <input type="radio" name="class_id" value="<?= $class['id'] ?>" class="peer sr-only" required onchange="updateClassPreview(this, '<?= $bigImage ?>', '<?= htmlspecialchars($class['description']) ?>')">
                        
                        <div class="flex items-center gap-4">
                            <div class="w-12 h-12 md:w-16 md:h-16 rounded bg-gray-800 border border-gray-600 flex items-center justify-center group-hover:border-violet-500 transition overflow-hidden flex-shrink-0">
                                <img src="/assets/images/class_<?= strtolower($class['name']) ?>.png" alt="<?= $class['name'] ?>" class="w-10 h-10 md:w-12 md:h-12 object-contain">
                            </div>
                            <div>
                                <div class="font-bold text-lg md:text-xl text-white group-hover:text-violet-300 transition"><?= $class['name'] ?></div>
                                <div class="text-xs md:text-sm text-gray-500 mt-1 line-clamp-2"><?= htmlspecialchars($class['description']) ?></div>
                            </div>
                        </div>
                        
                        <!-- Selection Indicator -->
                        <div class="absolute inset-0 border-2 border-violet-500 rounded-lg opacity-0 peer-checked:opacity-100 transition pointer-events-none"></div>
                    </label>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Center Panel: Preview & Name (Bottom on Mobile) -->
        <div class="flex-1 relative flex flex-col items-center justify-center order-2 py-8 lg:py-0">
            
            <!-- Character Preview -->
            <div class="relative w-full h-full flex items-center justify-center pb-24 md:pb-32">
                <div id="character-preview" class="relative w-full max-w-[300px] md:max-w-[500px] h-[40vh] md:h-[700px] flex items-center justify-center transition-all duration-500">
                    <!-- Placeholder for Full Body Image -->
                    <img id="preview-image" src="" alt="Aperçu" class="max-w-full max-h-full object-contain drop-shadow-2xl filter brightness-0 invert opacity-20">
                    <div id="preview-placeholder" class="absolute text-gray-500 text-lg md:text-xl font-medium text-center px-4">
                        Sélectionnez une classe
                    </div>
                </div>
            </div>

            <!-- Name Input (Floating) -->
            <div class="absolute bottom-6 md:bottom-12 w-full max-w-md px-6">
                <div class="relative group">
                    <div class="absolute -inset-1 bg-gradient-to-r from-violet-600 to-indigo-600 rounded-lg blur opacity-25 group-hover:opacity-75 transition duration-1000 group-hover:duration-200"></div>
                    <input type="text" name="name" required placeholder="Nom de votre Héros" 
                           class="relative w-full bg-gray-900 border-2 border-gray-700 rounded-lg py-3 md:py-4 px-6 text-center text-xl md:text-2xl font-bold text-white placeholder-gray-600 focus:border-violet-500 focus:outline-none transition shadow-xl">
                </div>
                <button type="submit" class="mt-4 md:mt-6 w-full py-3 md:py-4 bg-violet-600 hover:bg-violet-700 text-white font-bold rounded-lg shadow-lg shadow-violet-600/30 transition transform hover:-translate-y-1 uppercase tracking-wider text-base md:text-lg">
                    Commencer l'aventure
                </button>
            </div>
        </div>

    </form>
</div>

<script>
function updateClassPreview(input, imageName, desc) {
    const previewImage = document.getElementById('preview-image');
    const placeholder = document.getElementById('preview-placeholder');
    
    // Update image source
    previewImage.src = '/assets/images/' + imageName;
    previewImage.classList.remove('brightness-0', 'invert', 'opacity-20');
    placeholder.style.display = 'none';
}
</script>

<?php
$content = ob_get_clean();
require __DIR__ . '/../layouts/base.php';
