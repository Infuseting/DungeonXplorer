<?php
$title = 'DungeonXplorer - Votre Aventure Commence';
$description = 'Plongez dans un RPG textuel immersif. Créez votre destinée, combattez des créatures légendaires et vivez une épopée unique à chaque partie.';
$bodyClass = 'bg-white text-gray-900 antialiased';
$customStyles = '.text-gradient {
    background: linear-gradient(to right, #7c3aed, #4c1d95);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
}';

ob_start();
?>

<!-- Navbar -->
<nav class="fixed w-full z-50 bg-white/80 backdrop-blur-md border-b border-gray-100">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16 items-center">
            <div class="flex-shrink-0 flex items-center">
                <span class="text-2xl font-bold text-violet-600">DungeonXplorer</span>
            </div>
            <div class="hidden md:block">
                <div class="ml-10 flex items-baseline space-x-4">
                    <a href="/" class="text-gray-700 hover:text-violet-600 px-3 py-2 rounded-md text-sm font-medium transition">Accueil</a>
                    <a href="#features" class="text-gray-700 hover:text-violet-600 px-3 py-2 rounded-md text-sm font-medium transition">Fonctionnalités</a>
                    <a href="/login" class="bg-violet-600 text-white px-4 py-2 rounded-full text-sm font-medium hover:bg-violet-700 transition shadow-lg shadow-violet-200">Jouer Maintenant</a>
                </div>
            </div>
        </div>
    </div>
</nav>

<!-- Hero Section -->
<section class="pt-32 pb-20 lg:pt-40 lg:pb-28 overflow-hidden">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative">
        <div class="lg:grid lg:grid-cols-12 lg:gap-8 items-center">
            <div class="sm:text-center md:max-w-2xl md:mx-auto lg:col-span-6 lg:text-left">
                <h1 class="text-4xl tracking-tight font-extrabold text-gray-900 sm:text-5xl md:text-6xl lg:text-5xl xl:text-6xl">
                    <span class="block">L'aventure dont vous êtes</span>
                    <span class="block text-violet-600">le véritable héros</span>
                </h1>
                <p class="mt-3 text-base text-gray-500 sm:mt-5 sm:text-lg sm:max-w-xl sm:mx-auto md:mt-5 md:text-xl lg:mx-0">
                    Plongez dans un RPG textuel immersif. Créez votre destinée, combattez des créatures légendaires et vivez une épopée unique à chaque partie.
                </p>
                <div class="mt-8 sm:max-w-lg sm:mx-auto sm:text-center lg:text-left lg:mx-0">
                    <a href="/register" class="inline-flex items-center justify-center px-8 py-3 border border-transparent text-base font-medium rounded-full text-white bg-violet-600 hover:bg-violet-700 md:py-4 md:text-lg md:px-10 shadow-xl shadow-violet-200 transition transform hover:-translate-y-1">
                        Commencer l'aventure
                    </a>
                    <a href="#features" class="mt-3 sm:mt-0 sm:ml-3 inline-flex items-center justify-center px-8 py-3 border border-gray-200 text-base font-medium rounded-full text-gray-700 bg-white hover:bg-gray-50 md:py-4 md:text-lg md:px-10 transition">
                        En savoir plus
                    </a>
                </div>
            </div>
            <div class="mt-12 relative sm:max-w-lg sm:mx-auto lg:mt-0 lg:max-w-none lg:mx-0 lg:col-span-6 lg:flex lg:items-center">
                <div class="relative mx-auto w-full rounded-lg shadow-lg lg:max-w-md">
                    <div class="relative block w-full bg-white rounded-lg overflow-hidden shadow-2xl shadow-violet-200">
                        <img class="w-full" src="/assets/images/hero.png" alt="Livre magique et aventure">
                        <div class="absolute inset-0 bg-gradient-to-t from-violet-900/20 to-transparent mix-blend-multiply"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Features Section -->
<section id="features" class="py-20 bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center">
            <h2 class="text-base text-violet-600 font-semibold tracking-wide uppercase">Fonctionnalités</h2>
            <p class="mt-2 text-3xl leading-8 font-extrabold tracking-tight text-gray-900 sm:text-4xl">
                Tout ce dont vous avez besoin pour votre quête
            </p>
            <p class="mt-4 max-w-2xl text-xl text-gray-500 mx-auto">
                Une expérience de jeu riche et personnalisable, conçue pour les amateurs de JDR et de livres dont vous êtes le héros.
            </p>
        </div>

        <div class="mt-16">
            <div class="grid grid-cols-1 gap-8 sm:grid-cols-2 lg:grid-cols-4">
                
                <div class="pt-6">
                    <div class="flow-root bg-white rounded-2xl px-6 pb-8 shadow-lg hover:shadow-xl transition duration-300 h-full border border-gray-100">
                        <div class="-mt-6">
                            <div>
                                <span class="inline-flex items-center justify-center p-3 bg-violet-600 rounded-xl shadow-lg shadow-violet-300">
                                    <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                                </span>
                            </div>
                            <h3 class="mt-8 text-lg font-medium text-gray-900 tracking-tight">Classes Custom</h3>
                            <p class="mt-5 text-base text-gray-500">
                                Créez des personnages uniques avec des classes entièrement personnalisables. Définissez vos propres styles de jeu.
                            </p>
                        </div>
                    </div>
                </div>

                <div class="pt-6">
                    <div class="flow-root bg-white rounded-2xl px-6 pb-8 shadow-lg hover:shadow-xl transition duration-300 h-full border border-gray-100">
                        <div class="-mt-6">
                            <div>
                                <span class="inline-flex items-center justify-center p-3 bg-violet-600 rounded-xl shadow-lg shadow-violet-300">
                                    <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01"></path></svg>
                                </span>
                            </div>
                            <h3 class="mt-8 text-lg font-medium text-gray-900 tracking-tight">Customisation Poussée</h3>
                            <p class="mt-5 text-base text-gray-500">
                                Personnalisez l'apparence, les attributs et les compétences de votre héros. Chaque choix forge votre légende unique.
                            </p>
                        </div>
                    </div>
                </div>

                <div class="pt-6">
                    <div class="flow-root bg-white rounded-2xl px-6 pb-8 shadow-lg hover:shadow-xl transition duration-300 h-full border border-gray-100">
                        <div class="-mt-6">
                            <div>
                                <span class="inline-flex items-center justify-center p-3 bg-violet-600 rounded-xl shadow-lg shadow-violet-300">
                                    <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                </span>
                            </div>
                            <h3 class="mt-8 text-lg font-medium text-gray-900 tracking-tight">Quêtes & Histoire</h3>
                            <p class="mt-5 text-base text-gray-500">
                                Plongez dans une narration riche avec des quêtes épiques. Vos décisions façonnent le destin du royaume et votre destinée.
                            </p>
                        </div>
                    </div>
                </div>

                <div class="pt-6">
                    <div class="flow-root bg-white rounded-2xl px-6 pb-8 shadow-lg hover:shadow-xl transition duration-300 h-full border border-gray-100">
                        <div class="-mt-6">
                            <div>
                                <span class="inline-flex items-center justify-center p-3 bg-violet-600 rounded-xl shadow-lg shadow-violet-300">
                                    <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                                </span>
                            </div>
                            <h3 class="mt-8 text-lg font-medium text-gray-900 tracking-tight">Donjons Procéduraux</h3>
                            <p class="mt-5 text-base text-gray-500">
                                Explorez des donjons générés aléatoirement. Chaque exploration est unique avec des défis, trésors et dangers inédits.
                            </p>
                        </div>
                    </div>
                </div>

                <div class="pt-6">
                    <div class="flow-root bg-white rounded-2xl px-6 pb-8 shadow-lg hover:shadow-xl transition duration-300 h-full border border-gray-100">
                        <div class="-mt-6">
                            <div>
                                <span class="inline-flex items-center justify-center p-3 bg-violet-600 rounded-xl shadow-lg shadow-violet-300">
                                    <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                                </span>
                            </div>
                            <h3 class="mt-8 text-lg font-medium text-gray-900 tracking-tight">Combat Tour par Tour</h3>
                            <p class="mt-5 text-base text-gray-500">
                                Un système de combat stratégique au tour par tour. Affrontez des boss épiques et utilisez vos compétences avec sagesse.
                            </p>
                        </div>
                    </div>
                </div>

                <div class="pt-6">
                    <div class="flow-root bg-white rounded-2xl px-6 pb-8 shadow-lg hover:shadow-xl transition duration-300 h-full border border-gray-100">
                        <div class="-mt-6">
                            <div>
                                <span class="inline-flex items-center justify-center p-3 bg-violet-600 rounded-xl shadow-lg shadow-violet-300">
                                    <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                                </span>
                            </div>
                            <h3 class="mt-8 text-lg font-medium text-gray-900 tracking-tight">Lore Étoffé</h3>
                            <p class="mt-5 text-base text-gray-500">
                                Découvrez un univers riche en histoire et en mystères. Chaque lieu, personnage et objet raconte une histoire captivante.
                            </p>
                        </div>
                    </div>
                </div>

                <div class="pt-6">
                    <div class="flow-root bg-white rounded-2xl px-6 pb-8 shadow-lg hover:shadow-xl transition duration-300 h-full border border-gray-100">
                        <div class="-mt-6">
                            <div>
                                <span class="inline-flex items-center justify-center p-3 bg-violet-600 rounded-xl shadow-lg shadow-violet-300">
                                    <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                </span>
                            </div>
                            <h3 class="mt-8 text-lg font-medium text-gray-900 tracking-tight">Bestiaire Unique</h3>
                            <p class="mt-5 text-base text-gray-500">
                                Découvrez des créatures uniques, des boss redoutables et collectez des items légendaires introuvables ailleurs.
                            </p>
                        </div>
                    </div>
                </div>

                <div class="pt-6">
                    <div class="flow-root bg-white rounded-2xl px-6 pb-8 shadow-lg hover:shadow-xl transition duration-300 h-full border border-gray-100">
                        <div class="-mt-6">
                            <div>
                                <span class="inline-flex items-center justify-center p-3 bg-violet-600 rounded-xl shadow-lg shadow-violet-300">
                                    <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"></path></svg>
                                </span>
                            </div>
                            <h3 class="mt-8 text-lg font-medium text-gray-900 tracking-tight">Multi-Sauvegardes</h3>
                            <p class="mt-5 text-base text-gray-500">
                                Gérez plusieurs parties simultanément. Explorez différentes voies et conséquences sans perdre votre progression.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- CTA Section -->
<section class="bg-violet-700">
    <div class="max-w-7xl mx-auto py-12 px-4 sm:px-6 lg:py-16 lg:px-8 lg:flex lg:items-center lg:justify-between">
        <h2 class="text-3xl font-extrabold tracking-tight text-white sm:text-4xl">
            <span class="block">Prêt à écrire votre histoire ?</span>
            <span class="block text-violet-200">Rejoignez l'aventure dès aujourd'hui.</span>
        </h2>
        <div class="mt-8 flex lg:mt-0 lg:flex-shrink-0">
            <div class="inline-flex rounded-md shadow">
                <a href="/register" class="inline-flex items-center justify-center px-5 py-3 border border-transparent text-base font-medium rounded-md text-violet-600 bg-white hover:bg-violet-50">
                    Créer un compte
                </a>
            </div>
        </div>
    </div>
</section>

<footer class="bg-gray-900">
    <div class="max-w-7xl mx-auto py-12 px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center">
            <div class="text-white text-xl font-bold">DungeonXplorer</div>
            <p class="text-gray-400 text-sm">© 2025 DungeonXplorer. Tous droits réservés.</p>
        </div>
    </div>
</footer>

<?php
$content = ob_get_clean();
require __DIR__ . '/layouts/base.php';
