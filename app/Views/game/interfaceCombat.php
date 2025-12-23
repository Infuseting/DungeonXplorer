<?php

?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" />
        <script src="https://cdn.tailwindcss.com"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/gsap.min.js"></script>
        <title>Combat</title>
        <style>
            @layer utilities {
            @keyframes fadeInOut {
                0%   { opacity: 0; }
                50%  { opacity: 1; }
                100% { opacity: 0; }
            }

            .animate-fadeInOut {
                animation: fadeInOut 4s ease-in-out forwards;
            }
            }
  </style>

    </head>

    <body class="bg-gray-900 text-white min-h-screen">
        <div class="relative z-10 min-h-screen flex flex-col">

            <!-- Header -->
            <header class="flex justify-between items-center p-4 md:p-6 md:pb-2 bg-gray-900/50 backdrop-blur-sm lg:bg-transparent">
                <div class="text-xl md:text-2xl font-bold text-violet-500 tracking-wider uppercase">DungeonXplorer</div>
                <div class="flex items-center gap-4">
                    <span class="hidden md:inline text-gray-300">Bienvenue, <?php echo '<span class="text-white font semi-bold">'.$_SESSION['username'].'</span>'?> </span>
                    <a href="/logout" class="px-3 py-1 md:px-4 md:py-2 border border-red-500/50 text-red-400 rounded hover:bg-red-500/10 transition text-sm md:text:base">Déconnexion</a>
                </div>
            </header>

            <!-- Combat Interface -->
            <main class="flex-grow p-4 pt-0 md:p-4 md:pt-0 backdrop-blur-sm rounded-lg m-2">

                <!-- Interface Graphique Combat -->
                <div class="flex flex-col md:flex-col justify-center items-center gap-8">

                    <div class="w-full md:w-[55%] h-[500px] bg-gray-700 rounded-lg flex items-center justify-center drop-shadow-[0_0_5px_#a78bfa]">
                        <img src=<?php echo $monsterModel->getSallePath() ?> class="w-full h-full" id="bg" alt="">

                        <div class="absolute top-8 flex flex-col text-justify">
                            <h2 class="text-2xl md:text-3xl font-bold text-white text-center drop-shadow-lg mb-4">VS <?php echo $monsterModel->getName()?></h2>
                            <div id="combat-log"></div>
                        </div>

                        <div class="absolute top-25 z-50">
                            <div id="win?"></div>
                            <div id='redirectBtn'></div>
                        </div>

                        <div class="absolute bottom-7 w-64 h-64 overflow-hidden">
                            <img  id='monster'src= <?php echo $monsterModel->getImagePath()?> alt="Monstre" class="object-cover w-full h-full transition-opacity duration-1000 opacity-100" >
                        </div>
                       

                        <div id="dice" class="absolute bottom-3 right-3 text-4xl font-bold text-violet-400 drop-shadow-[0_0_6px_#a78bfa]">
                            🎲 
                        </div>
                    </div>

                    <div class="w-full md:w-[55%] bg-gray-800 rounded-lg flex flex-col md:flex-row relative overflow-visible p-4">

                        <!-- Stats à gauche -->
                        <div class="w-full md:w-1/3 flex flex-col mb-4 md:mb-0 bg-gray-900/50 rounded-lg">
                            <?php
                                echo "<h3 class='text-violet-400 font-bold mb-2 text-center mt-2 md:text-xl'>Stats de " . htmlspecialchars($characterModel->getName()) . "</h3>";
                            ?>

                            <ul class="text-sm grid grid-cols-2 gap-6 mx-auto">
                                <li class="flex items-center gap-2 w-full">
                                    <span class="material-symbols-outlined text-violet-400" title="Santé du joueur">favorite</span>
                                    <span class="text-xl font-bold" id="player-hp"><?php echo htmlspecialchars($characterModel->getVitality()); ?></span>
                                </li>

                                <li class="flex items-center gap-2 w-full">
                                    <span class="material-symbols-outlined text-violet-400" title="Puissance d'attaque">swords</span>
                                    <span class="text-xl font-bold"><?php echo htmlspecialchars($characterModel->getAttaqueClass()); ?></span>
                                </li>

                                <li class="flex items-center gap-2 w-full">
                                    <span class="material-symbols-outlined text-violet-400" title="Défense du joueur">shield</span>
                                    <span class="text-xl font-bold"><?php echo htmlspecialchars($characterModel->getArmorClass()); ?></span>
                                </li>

                                <li class="flex items-center gap-2 w-full">
                                    <span class="material-symbols-outlined text-violet-400" title="Force du joueur">fitness_center</span>
                                    <span class="text-xl font-bold"><?php echo htmlspecialchars($characterModel->getStrength()); ?></span>
                                </li>

                                <li class="flex items-center gap-2 w-full">
                                    <span class="material-symbols-outlined text-violet-400" title="Intelligence du joueur">psychology</span>
                                    <span class="text-xl font-bold"><?php echo htmlspecialchars($characterModel->getIntelligence()); ?></span>
                                </li>

                                <li class="flex items-center gap-2 w-full">
                                    <span class="material-symbols-outlined text-violet-400" title="Dextérié du joueur">directions_run</span>
                                    <span class="text-xl font-bold"><?php echo htmlspecialchars($characterModel->getDexterity()); ?></span>
                                </li>
                            </ul>
                        </div>

                        <?php
                            $character =  $characterModel->toArray();
                        ?>
                        <!--object-cover w-full h-full -->
                        <!-- Cercle au centre -->
                       <div class="w-full md:w-1/3 flex items-center justify-center relative mb-4 md:mb-0">
                            <div id='character'class="w-48 h-48 rounded-full border-4 border-violet-500 bg-gray-700 overflow-hidden flex items-center justify-center">
                                <?= renderCharacter($character, [
                                    'size' => 'full',
                                    'showFilter' => true,
                                    'id' => 'character',
                                    'class' => 'object-cover w-full h-full drop-shadow-2xl hover:brightness-110 transition duration-500'
                                ]); ?>

                            </div>
                        </div>

      <div class="w-full md:w-1/3 p-3 bg-gray-900/50 rounded-lg ">
  
                    <h3 class='text-violet-400 font-semibold mb-3 text-center text-lg uppercase tracking-wider border-b border-gray-700 pb-2'>
                        Actions
                    </h3>

                    <ul class="grid grid-cols-2 gap-3 mb-4">
                        
                        <li>
                        <button id="btn-attack" onclick="sendAction('attack')"
                            class="
                            w-full px-3 py-2 text-sm uppercase font-medium
                            bg-gray-700/50 text-red-400 border border-red-800 rounded
                            hover:bg-red-900/40 hover:text-white transition duration-150
                            shadow-md shadow-red-900/30
                            ">
                            Attaque
                        </button>
                        </li>

                        <li>
                        <button id="btn-defend" onclick="sendAction('defend')"
                            class="
                            w-full px-3 py-2 text-sm uppercase font-medium
                            bg-gray-700/50 text-blue-400 border border-blue-800 rounded
                            hover:bg-blue-900/40 hover:text-white transition duration-150
                            shadow-md shadow-blue-900/30
                            ">
                            Défense
                        </button>
                        </li>

                        <li>
                        <button id="btn-heal" onclick="sendAction('usePotion')"
                            class="
                            w-full px-3 py-2 text-sm uppercase font-medium
                            bg-gray-700/50 text-yellow-400 border border-yellow-800 rounded
                            hover:bg-yellow-900/40 hover:text-white transition duration-150
                            shadow-md shadow-yellow-900/30
                            ">
                            Potion
                        </button>
                        </li>

                        <li>
                        <button id="btn-special" onclick="sendAction('specialCapacity')"
                            class="
                            w-full px-3 py-2 text-sm uppercase font-medium
                            bg-gray-700/50 text-violet-300 border border-violet-500 rounded
                            hover:bg-violet-900/40 hover:text-white transition duration-150
                            shadow-md shadow-violet-500/30
                            ">
                            Spécial
                        </button>
                        </li>
                        
                    </ul>
                    
                    <div class="text-center">
                        <button onclick="rollDice()" id="rollBtn"
                        class=" 
                            w-full py-2 px-4 text-sm font-bold uppercase
                            bg-purple-900 hover:bg-purple-700 text-white 
                            rounded-md border-b-2 border-purple-950 active:border-b-0 active:translate-y-px
                            shadow-lg shadow-purple-950/70 transition duration-150
                            flex items-center justify-center space-x-2 
                        ">
                        
                        <span>LANCER LES DÉS</span>
                        </button>
                    </div>
                    <audio id="ost-audio" src="/assets/audio/combat/ostCombat.mp3" loop preload="auto"></audio>

                    
                    </div>
                </div>
            </main>
        </div>

        <script>
            let end = false;
            const winOrLoss = document.getElementById('win?');
            const slashSound = new Audio('/assets/audio/combat/special.mp3');
            const diceSound = new Audio('/assets/audio/combat/diceRoll.mp3');
            const attaqueSound = new Audio('/assets/audio/combat/attaque.mp3');
            const bouclierSound = new Audio('/assets/audio/combat/bouclier.mp3');
            const spellSound = new Audio('/assets/audio/combat/spell.mp3');
            const rumbleSound = new Audio('/assets/audio/combat/rumble.mp3');
            const potionSound = new Audio('/assets/audio/combat/potion.mp3');
            const ost = document.getElementById('ost-audio');
            ost.play();
         
            slashSound.preload = "auto";
            diceSound.preload = "auto";
            attaqueSound.preload = "auto";
            bouclierSound.preload = "auto";
            spellSound.preload = "auto";
            rumbleSound.preload = "auto";

            const classId = <?= (int) $characterModel->getClassId() ?>;



            const redirection = document.getElementById('redirectBtn');
            const dice = document.getElementById("dice");
            const bg = document.getElementById('bg');
            const btn = document.getElementById("rollBtn");
            const btnAttack = document.getElementById("btn-attack");
            const btnDefend = document.getElementById("btn-defend");
            const btnHeal = document.getElementById("btn-heal");
            const btnRun = document.getElementById("btn-special");
            const monster = document.getElementById("monster");
            const character = document.getElementById('character');
            let choice;
            function disableActions() {
                btnAttack.disabled = true;
                btnDefend.disabled = true;
                btnHeal.disabled = true;
                btnRun.disabled = true;
            }

            function enableActions() {
                btnAttack.disabled = false;
                btnDefend.disabled = false;
                btnHeal.disabled = false;
                btnRun.disabled = false;
            }

            function sendAction(action) {
                disableActions();
                 dice.textContent = "🎲 "; 
                 choice = action;

                fetch("/game/combat/action", {
                    method: "POST",
                    headers: { "Content-Type": "application/x-www-form-urlencoded" },
                    body: "action=" + encodeURIComponent(action)
                })
                .then(res => res.text())
                .then(text => {
                    try {
                        const data = JSON.parse(text);
                        console.log(data);
                        const log = document.getElementById("combat-log");

                        log.innerHTML = "";

                        if (data.player) {//Affichage du message joueur et animation des dégats pris par le monstre si l'attaque a touchée
                            log.innerHTML += `<p class="text-blue-400 font-semibold mb-1">${data.player}</p>`;
                             if(data.damageM){
                                ennemyHit();
                             }else{
                                getAudio(false);
                             }

                        }

                        //Délai d'une seconde entre les deux affichage ( gestion flux informations)
                        setTimeout(() => {
                             if (data.monster) { //Affichage du message monstre et animation des degat pris par le joueur si attaque a touchée
                                log.innerHTML += `<p class="text-red-400 font-semibold mb-1">${data.monster}</p>`;
                                if(data.damageJ){
                                    playerHit();
                                }
                             }
                            setTimeout(() => {
                                if (!data.success) {
                                    log.innerHTML += `<p class="text-red-600 font-bold mb-2">${data.message}</p>`;
                                }

                                if (data.newTurn && !data.win) {
                                    log.innerHTML += `<p class="text-yellow-400 font-bold animate-pulse mt-2">À vous de jouer !</p>`;
                                }
                             }, 1000);
                        if (data.win ) {
                            end  = true;
                            playerWin();
                        }
                        // Mettre à jour les HP du joueur
                        if (typeof data.playerHp !== "undefined") {
                            document.getElementById('player-hp').innerHTML = "";
                            if (data.playerHp <= 0) {
                                playerLoss();
                                document.getElementById('player-hp').innerHTML += "<p style='color:red'>" + data.playerHp + "</p>";
                            } else {
                                document.getElementById('player-hp').textContent = data.playerHp;
                            }
                        }
                        btn.disabled = false;
                        console.log("HP joueur:", data.playerHp);      
                        }, 1500);
                    } catch (e) {
                        console.error("Réponse non JSON:", text);
                        console.log(e.message);
                    }
                });
            }

            function rollDice() {
                if(end)return;
                diceSound.currentTime = 0;
                diceSound.play();
                enableActions();
                btn.disabled = true;
                
                let result = Math.floor(Math.random() * 20) + 1;

                dice.textContent = "🎲 "; // effet suspense
                dice.classList.add("animate-spin"); // rotation Tailwind

                setTimeout(() => {
                    dice.classList.remove("animate-spin");
                    dice.textContent = "🎲 " + result;
                }, 500);

                fetch("/game/combat/roll-dice", {
                    method: "POST",
                    headers: { "Content-Type": "application/x-www-form-urlencoded" },
                    body: "diceRoll=" + result
                }).then(response => {
                    if (!response.ok) {
                        throw new Error("Network response was not ok");
                    }
                    return response.text();
                }).then(data => {
                    console.log("Dice roll sent to server:", data);
                }).catch(error => {
                    console.error("There was a problem with the fetch operation:", error);
                });
            }

            function playerWin() {
                disableActions();
                    setTimeout(() => {
                    bossDie();
                    setTimeout(() => {
                        document.getElementById('combat-log').textContent = "";
                        winOrLoss.classList.add("text-black-600", "animate-pulse");
                        let redirect = document.createElement("button");
                        redirect.textContent = "Achever le combat"
                        redirect.classList.add('p-4','border','border-purple-500/50','bg-gray-800/50','mt-5','rounded-lg','z-999','hover:scale-105');
                        redirect.onclick = () =>{
                            window.location.href = "http://localhost:8080/game";
                            //window.location.href = "https://dungeonxplorer.infuseting.fr/game"
                        }
                        winOrLoss.innerHTML = '<p class ="text-4xl font-bold z-9999"> YOU WIN </p>';
                        redirection.appendChild(redirect);
                    }, 1000);   
                }, 1000);
            }

            function playerLoss() {
                end = true;
                disableActions();
                btn.disabled = true;
                playerDie();
                setTimeout(() => {
                    setTimeout(() => {
                    document.getElementById('combat-log').textContent = "";
                    console.log('loose');
                    bg.classList.add("sepia", "saturate-200", "hue-rotate-[-50deg]", "brightness-75");
                    winOrLoss.classList.add("text-red-600", "animate-pulse");
                    winOrLoss.innerHTML = '<p class ="text-4xl font-bold z-9999"> YOU LOST </p>';
                     }, 1500);    
                }, 1000);
            }
            //animation plus graphique
            function ennemyHit(){
                    //adapter son en fonction de l'attaque
                    getAudio(true);

                    gsap.from(monster, {
                    x: "-8",     // recul
                    duration: 0.1,
                    yoyo: true, 
                    repeat: 2,
                    ease: "power2.out"
                });
                }
            function playerHit(){
                   slashSound.currentTime = 0;
                   slashSound.play();
                   
                   gsap.from(character, {
                    x: "-8",     // recul
                    duration: 0.1,
                    yoyo: true,
                    repeat: 2,
                    ease: "power2.out"
                });
                

            }

            function getAudio(bool){
                console.log(choice);
                console.log(bool);
                console.log(classId);
                switch(choice){
                    case 'defend':
                        bouclierSound.currentTime = 0;
                        bouclierSound.play();
                        break;
                    case 'specialCapacity':
                        slashSound.currentTime = 0;
                        slashSound.play();
                        break;
                    case 'usePotion':
                        potionSound.currentTime = 0;
                        potionSound.play();
                        break;
                    case 'attack':
                        if(bool){
                            if(classId == 2){
                                spellSound.currentTime = 0;
                                spellSound.play();
                            }else{
                                attaqueSound.currentTime = 0;
                                attaqueSound.play();
                            }
                        }
                      
                    default:
                        break;
                }
            }
          function playerDie() {
    setTimeout(() => {
        rumbleSound.currentTime = 0;
        rumbleSound.play();
        gsap.fromTo("body", 
            { x: -10 }, 
            { x: 10, duration: 0.05, repeat: 15, yoyo: true }
        );

        // --- AJOUT : Redirection après le tremblement ---
        setTimeout(() => {
            // On peut ajouter un fondu au noir avant de partir
            gsap.to("body", { 
                opacity: 0, 
                duration: 1, 
                onComplete: () => {
                    window.location.href = '/game/death'; // Ou ton URL de mort
                }
            });
        }, 1000); 
        // ------------------------------------------------
    }, 1000);
}
            function bossDie() {
                setTimeout(() => {

                      rumbleSound.currentTime = 0;
                rumbleSound.play();
                gsap.fromTo("body", 
                    { x: -10 }, 
                    { x: 10, duration: 0.05, repeat: 15, yoyo: true }
                );
                const tl = gsap.timeline();

                tl.to(monster, {
                    filter: "brightness(3)",
                    duration: 0.1,
                });

                tl.to(monster, {
                    x: "+=5",
                    repeat: 5,
                    yoyo: true,
                    duration: 0.05
                });

                tl.to(monster, {
                    scale: 0,
                    opacity: 0,
                    duration: 0.4,
                    ease: "power3.in"
                });
                    
                }, 1000);
              
            }  
        </script>
    </body>
</html>