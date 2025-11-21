<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DungeonXplorer - Votre Aventure Commence</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
        .text-gradient {
            background: linear-gradient(to right, #7c3aed, #4c1d95);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
    </style>
</head>
<body class="bg-white text-gray-900 antialiased">

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
                    <!-- Feature 1 -->
                    <div class="pt-6">
                        <div class="flow-root bg-white rounded-2xl px-6 pb-8 shadow-lg hover:shadow-xl transition duration-300 h-full border border-gray-100">
                            <div class="-mt-6">
                                <div>
                                    <span class="inline-flex items-center justify-center p-3 bg-violet-600 rounded-xl shadow-lg shadow-violet-300">
                                        <!-- Icon: User/Class -->
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

                    <!-- Feature 2 -->
                    <div class="pt-6">
                        <div class="flow-root bg-white rounded-2xl px-6 pb-8 shadow-lg hover:shadow-xl transition duration-300 h-full border border-gray-100">
                            <div class="-mt-6">
                                <div>
                                    <span class="inline-flex items-center justify-center p-3 bg-violet-600 rounded-xl shadow-lg shadow-violet-300">
                                        <!-- Icon: Sword/Combat -->
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

                    <!-- Feature 3 -->
                    <div class="pt-6">
                        <div class="flow-root bg-white rounded-2xl px-6 pb-8 shadow-lg hover:shadow-xl transition duration-300 h-full border border-gray-100">
                            <div class="-mt-6">
                                <div>
                                    <span class="inline-flex items-center justify-center p-3 bg-violet-600 rounded-xl shadow-lg shadow-violet-300">
                                        <!-- Icon: Skull/Boss -->
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

                    <!-- Feature 4 -->
                    <div class="pt-6">
                        <div class="flow-root bg-white rounded-2xl px-6 pb-8 shadow-lg hover:shadow-xl transition duration-300 h-full border border-gray-100">
                            <div class="-mt-6">
                                <div>
                                    <span class="inline-flex items-center justify-center p-3 bg-violet-600 rounded-xl shadow-lg shadow-violet-300">
                                        <!-- Icon: Save/Multiple -->
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

    <!-- Footer -->
    <footer class="bg-gray-900">
        <div class="max-w-7xl mx-auto py-12 px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center">
                <div class="text-white text-xl font-bold">DungeonXplorer</div>
                <p class="text-gray-400 text-sm">© 2025 DungeonXplorer. Tous droits réservés.</p>
            </div>
        </div>
    </footer>

</body>
</html>
