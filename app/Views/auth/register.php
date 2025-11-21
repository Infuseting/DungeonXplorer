<!DOCTYPE html>
<html lang="fr" class="h-full bg-white">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inscription - DungeonXplorer</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="h-full">
    <div class="min-h-full flex">
        
        <!-- Left Side: Image -->
        <div class="hidden lg:block relative w-0 flex-1">
            <img class="absolute inset-0 h-full w-full object-cover" src="/assets/images/auth_bg.png" alt="Portail magique">
            <div class="absolute inset-0 bg-violet-900/40 mix-blend-multiply"></div>
            <div class="absolute inset-0 bg-gradient-to-t from-gray-900 via-transparent to-transparent opacity-90"></div>
            
            <div class="absolute bottom-0 left-0 p-12 text-white">
                <blockquote class="mt-8">
                    <p class="text-xl font-medium text-violet-100 italic">
                        "Chaque légende commence par un premier pas. Le vôtre commence ici, dans les ténèbres de l'inconnu."
                    </p>
                    <footer class="mt-4">
                        <p class="text-base font-semibold text-violet-200">Le Chroniqueur</p>
                    </footer>
                </blockquote>
            </div>
        </div>

        <!-- Right Side: Form -->
        <div class="flex-1 flex flex-col justify-center py-12 px-4 sm:px-6 lg:flex-none lg:px-20 xl:px-24 bg-white">
            <div class="mx-auto w-full max-w-sm lg:w-96">
                <div>
                    <a href="/" class="text-violet-600 hover:text-violet-500 flex items-center gap-2 mb-8 transition-colors">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z" clip-rule="evenodd" />
                        </svg>
                        Retour à l'accueil
                    </a>
                    <h2 class="mt-6 text-3xl font-extrabold text-gray-900 tracking-tight">
                        Rejoignez l'aventure
                    </h2>
                    <p class="mt-2 text-sm text-gray-600">
                        Déjà un héros ?
                        <a href="/login" class="font-medium text-violet-600 hover:text-violet-500 transition-colors">
                            Connectez-vous à votre compte
                        </a>
                    </p>
                </div>

                <div class="mt-8">
                    <div class="mt-6">
                        <form action="#" method="POST" class="space-y-6">
                            
                            <div>
                                <label for="username" class="block text-sm font-medium text-gray-700">
                                    Nom d'utilisateur
                                </label>
                                <div class="mt-1">
                                    <input id="username" name="username" type="text" required class="appearance-none block w-full px-3 py-3 border border-gray-300 rounded-lg shadow-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-violet-500 focus:border-transparent transition duration-200 sm:text-sm">
                                </div>
                            </div>

                            <div>
                                <label for="email" class="block text-sm font-medium text-gray-700">
                                    Adresse Email
                                </label>
                                <div class="mt-1">
                                    <input id="email" name="email" type="email" autocomplete="email" required class="appearance-none block w-full px-3 py-3 border border-gray-300 rounded-lg shadow-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-violet-500 focus:border-transparent transition duration-200 sm:text-sm">
                                </div>
                            </div>

                            <div class="space-y-1">
                                <label for="password" class="block text-sm font-medium text-gray-700">
                                    Mot de passe
                                </label>
                                <div class="mt-1">
                                    <input id="password" name="password" type="password" autocomplete="new-password" required class="appearance-none block w-full px-3 py-3 border border-gray-300 rounded-lg shadow-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-violet-500 focus:border-transparent transition duration-200 sm:text-sm">
                                </div>
                            </div>

                            <div>
                                <button type="submit" class="w-full flex justify-center py-3 px-4 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-violet-600 hover:bg-violet-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-violet-500 transition duration-200 transform hover:-translate-y-0.5">
                                    Créer mon personnage
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
