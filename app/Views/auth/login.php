<?php
$title = 'Connexion - DungeonXplorer';
$quote = '"Le portail s\'ouvre devant vous. Oserez-vous franchir le seuil et affronter votre destin ?"';
$quoteAuthor = 'Le Gardien du Donjon';

ob_start();
?>
<div class="mx-auto w-full max-w-sm lg:w-96">
    <div>
        <a href="/" class="text-violet-600 hover:text-violet-500 flex items-center gap-2 mb-8 transition-colors">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z" clip-rule="evenodd" />
            </svg>
            Retour à l'accueil
        </a>
        <h2 class="mt-6 text-3xl font-extrabold text-gray-900 tracking-tight">
            Bon retour, Aventurier
        </h2>
        <p class="mt-2 text-sm text-gray-600">
            Ou
            <a href="/register" class="font-medium text-violet-600 hover:text-violet-500 transition-colors">
                commencez votre première quête
            </a>
        </p>
    </div>

    <?php
    // Display error messages
    $errorMessages = [
        'invalid_credentials' => 'Email ou mot de passe incorrect.',
        'missing_fields' => 'Veuillez remplir tous les champs.',
        'password_reset' => 'Mot de passe réinitialisé avec succès !',
    ];
    
    // Display success messages
    $successMessages = [
        'registered' => 'Inscription réussie ! Vous pouvez maintenant vous connecter.',
        'password_reset' => 'Mot de passe réinitialisé avec succès !',
    ];
    
    if (isset($_GET['error']) && isset($errorMessages[$_GET['error']])):
    ?>
        <div class="mt-4 bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg">
            <div class="flex">
                <svg class="h-5 w-5 text-red-400 mr-2" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                </svg>
                <span class="text-sm font-medium"><?= htmlspecialchars($errorMessages[$_GET['error']]) ?></span>
            </div>
        </div>
    <?php endif; ?>
    
    <?php if (isset($_GET['success']) && isset($successMessages[$_GET['success']])): ?>
        <div class="mt-4 bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg">
            <div class="flex">
                <svg class="h-5 w-5 text-green-400 mr-2" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                </svg>
                <span class="text-sm font-medium"><?= htmlspecialchars($successMessages[$_GET['success']]) ?></span>
            </div>
        </div>
    <?php endif; ?>

    <div class="mt-8">
        <div class="mt-6">
            <form class="space-y-6" action="/login" method="POST">
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700">
                        Adresse email
                    </label>
                    <div class="mt-1">
                        <input id="email" name="email" type="email" autocomplete="email" required class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm placeholder-gray-400 focus:outline-none focus:ring-violet-500 focus:border-violet-500 transition duration-200">
                    </div>
                </div>

                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700">
                        Mot de passe
                    </label>
                    <div class="mt-1">
                        <input id="password" name="password" type="password" autocomplete="current-password" required class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm placeholder-gray-400 focus:outline-none focus:ring-violet-500 focus:border-violet-500 transition duration-200">
                    </div>
                </div>

                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <input id="remember-me" name="remember-me" type="checkbox" class="h-4 w-4 text-violet-600 focus:ring-violet-500 border-gray-300 rounded transition duration-200">
                        <label for="remember-me" class="ml-2 block text-sm text-gray-900">
                            Se souvenir de moi
                        </label>
                    </div>

                    <div class="text-sm">
                        <a href="/forgot-password" class="font-medium text-violet-600 hover:text-violet-500 transition-colors">
                            Mot de passe oublié ?
                        </a>
                    </div>
                </div>

                <div>
                    <button type="submit" class="w-full flex justify-center py-3 px-4 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-violet-600 hover:bg-violet-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-violet-500 transition duration-200 transform hover:-translate-y-0.5">
                        Ouvrir le portail
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php
$content = ob_get_clean();
require __DIR__ . '/../layouts/auth.php';
