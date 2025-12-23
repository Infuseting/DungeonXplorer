<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/gsap.min.js"></script>
    <title>Fin du périple - DungeonXplorer</title>
</head>
<body class="bg-black text-white min-h-screen flex items-center justify-center overflow-hidden">

    <div class="absolute inset-0 bg-radial-gradient from-red-900/20 to-black pointer-events-none"></div>

    <div class="relative z-10 text-center px-4">
        <h1 id="death-title" class="text-6xl md:text-8xl font-black text-red-700 tracking-tighter opacity-0 mb-4 uppercase italic">
            Ton périple s'arrête ici
        </h1>
        
        <p id="death-subtitle" class="text-gray-400 text-lg md:text-xl mb-12 opacity-0">
            Les ténèbres du donjon ont fini par t'engloutir...
        </p>

        <div id="death-actions" class="flex flex-col md:flex-row gap-6 justify-center items-center opacity-0">
            <a href="/game" class="group relative px-8 py-3 font-bold text-white transition-all">
                <span class="absolute inset-0 w-full h-full bg-red-900 rounded-lg group-hover:bg-red-700 transition-colors"></span>
                <span class="relative">RESSUSCITER</span>
            </a>
            
            <a href="/menu" class="text-gray-500 hover:text-white transition-colors border-b border-transparent hover:border-white">
                Retourner au menu principal
            </a>
        </div>
    </div>

    <script>
        window.addEventListener('DOMContentLoaded', () => {
            const tl = gsap.timeline();

            // Animation de l'écran qui s'éclaircit légèrement depuis le noir total
            tl.to("#death-title", { 
                opacity: 1, 
                duration: 2, 
                scale: 1.1,
                ease: "power4.out" 
            })
            .to("#death-subtitle", { 
                opacity: 1, 
                duration: 1.5,
                y: -10,
                ease: "power2.out" 
            }, "-=1")
            .to("#death-actions", { 
                opacity: 1, 
                duration: 1,
                ease: "power1.inOut" 
            }, "-=0.5");

            // Petit effet de battement sur le titre
            gsap.to("#death-title", {
                textShadow: "0px 0px 20px rgba(185, 28, 28, 0.8)",
                repeat: -1,
                yoyo: true,
                duration: 2
            });
        });
    </script>

    <style>
        body {
            background: radial-gradient(circle, #1a0505 0%, #000000 100%);
        }
    </style>
</body>
</html>