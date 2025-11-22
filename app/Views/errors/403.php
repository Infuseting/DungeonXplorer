<?php
$title = '403 - Accès Interdit | DungeonXplorer';
$bodyClass = 'bg-gray-900 min-h-screen flex items-center justify-center px-4';

ob_start();
?>
<div class="max-w-4xl w-full">
    <div class="bg-gray-800 rounded-2xl shadow-2xl overflow-hidden">
        <div class="md:flex">
            <!-- Image Section -->
            <div class="md:w-1/2 relative">
                <img src="/assets/images/error_403.png" alt="Porte verrouillée" class="w-full h-full object-cover">
                <div class="absolute inset-0 bg-gradient-to-r from-gray-800 via-transparent to-transparent"></div>
            </div>
            
            <!-- Content Section -->
            <div class="md:w-1/2 p-8 md:p-12 flex flex-col justify-center">
                <div class="text-violet-400 text-6xl font-bold mb-4">403</div>
                <h1 class="text-3xl md:text-4xl font-bold text-white mb-4">
                    Accès Interdit
                </h1>
                <p class="text-gray-300 mb-6 leading-relaxed">
                    Cette zone est protégée par de puissantes runes magiques. Vous n'avez pas les permissions nécessaires pour accéder à cette ressource.
                </p>
                
                <div class="space-y-3">
                    <a href="/" class="inline-flex items-center justify-center w-full px-6 py-3 border border-transparent text-base font-medium rounded-lg text-white bg-violet-600 hover:bg-violet-700 transition duration-200 shadow-lg shadow-violet-500/50">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                            <path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z" />
                        </svg>
                        Retour à l'Accueil
                    </a>
                    <a href="/login" class="inline-flex items-center justify-center w-full px-6 py-3 border border-gray-600 text-base font-medium rounded-lg text-gray-300 bg-transparent hover:bg-gray-700 transition duration-200">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd" />
                        </svg>
                        Se Connecter
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
<?php
$content = ob_get_clean();
require __DIR__ . '/../layouts/base.php';
