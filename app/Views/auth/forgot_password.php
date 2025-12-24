<?php
$title = 'Mot de passe oublié - DungeonXplorer';
$quote = '"Les secrets perdus peuvent être retrouvés, mais seulement par ceux qui osent chercher."';
$quoteAuthor = 'L\'Oracle des Mystères';

ob_start();
?>
<div class="mx-auto w-full max-w-sm lg:w-96">
    <div>
        <a href="/login" class="text-violet-600 hover:text-violet-500 flex items-center gap-2 mb-8 transition-colors">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd"
                    d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z"
                    clip-rule="evenodd" />
            </svg>
            Retour à la connexion
        </a>
        <h2 class="mt-6 text-3xl font-extrabold text-gray-900 tracking-tight">
            Réinitialisation du mot de passe
        </h2>
        <p class="mt-2 text-sm text-gray-600">
            Entrez votre email, le code reçu et votre nouveau mot de passe.
        </p>
    </div>

    <?php
    $errorMessages = [
        'passwords_do_not_match' => 'Les mots de passe ne correspondent pas.',
        'invalid_email' => 'Aucun compte associé à cet email.',
        'invalid_code' => 'Code invalide ou expiré.',
        'missing_fields' => 'Veuillez remplir tous les champs.',
    ];

    if (isset($_GET['error']) && isset($errorMessages[$_GET['error']])):
        ?>
        <div class="mt-4 bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg">
            <div class="flex">
                <svg class="h-5 w-5 text-red-400 mr-2" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd"
                        d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                        clip-rule="evenodd" />
                </svg>
                <span class="text-sm font-medium"><?= htmlspecialchars($errorMessages[$_GET['error']]) ?></span>
            </div>
        </div>
    <?php endif; ?>

    <div class="mt-8">
        <div class="mt-6">
            <form class="space-y-6" action="/forgot-password" method="POST">
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700">
                        Adresse email
                    </label>
                    <div class="mt-1">
                        <input id="email" name="email" type="email" autocomplete="email" required
                            class="appearance-none block w-full px-3 py-3 border border-gray-300 rounded-lg shadow-sm placeholder-gray-400 focus:outline-none focus:ring-violet-500 focus:border-violet-500 transition duration-200">
                    </div>
                </div>

                <div>
                    <label for="code" class="block text-sm font-medium text-gray-700">
                        Code de réinitialisation
                    </label>
                    <div class="mt-1">
                        <input id="code" name="code" type="text" maxlength="6" placeholder="------" required
                            class="appearance-none block w-full px-3 py-3 border border-gray-300 rounded-lg shadow-sm placeholder-gray-400 focus:outline-none focus:ring-violet-500 focus:border-violet-500 transition duration-200 font-mono text-center text-xl tracking-widest">
                    </div>
                    <p class="mt-1 text-xs text-gray-500">Code à 6 chiffres fourni par l'administrateur</p>
                </div>

                <div>
                    <label for="new_password" class="block text-sm font-medium text-gray-700">
                        Nouveau mot de passe
                    </label>
                    <div class="mt-1">
                        <input id="new_password" name="new_password" type="password" autocomplete="new-password"
                            required
                            class="appearance-none block w-full px-3 py-3 border border-gray-300 rounded-lg shadow-sm placeholder-gray-400 focus:outline-none focus:ring-violet-500 focus:border-violet-500 transition duration-200">
                    </div>
                </div>

                <div>
                    <label for="confirm_password" class="block text-sm font-medium text-gray-700">
                        Confirmer le mot de passe
                    </label>
                    <div class="mt-1">
                        <input id="confirm_password" name="confirm_password" type="password" autocomplete="new-password"
                            required
                            class="appearance-none block w-full px-3 py-3 border border-gray-300 rounded-lg shadow-sm placeholder-gray-400 focus:outline-none focus:ring-violet-500 focus:border-violet-500 transition duration-200">
                    </div>
                </div>

                <div>
                    <button type="submit"
                        class="w-full flex justify-center py-3 px-4 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-violet-600 hover:bg-violet-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-violet-500 transition duration-200 transform hover:-translate-y-0.5">
                        Réinitialiser le mot de passe
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php
$content = ob_get_clean();
require __DIR__ . '/../layouts/auth.php';
