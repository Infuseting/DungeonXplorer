<?php

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" />
    <script src="https://cdn.tailwindcss.com"></script>
    <title>Combat</title>
</head>
<body class="bg-gray-900 text-white min-h-screen  ">
 
    <div class="relative z-10 min-h-screen flex flex-col">

    <!-- Header -->
        <header class="flex justify-between items-center p-4 md:p-6 md:pb-2 bg-gray-900/50 backdrop-blur-sm lg:bg-transparent">
            <div class="text-xl md:text-2xl font-bold text-violet-500 tracking-wider uppercase">DungeonXplorer</div>
            <div class = "flex items-center gap-4">
                <span class="hidden md:inline text-gray-300">Bienvenue, <?php echo '<span class="text-white font semi-bold">'.$_SESSION['username'].'</span>'?> </span>
                <a href="/logout" class="px-3 py-1 md:px-4 md:py-2 border border-red-500/50 text-red-400 rounded hover:bg-red-500/10 transition text-sm md:text:base">Déconnexion</a>
            </div>
        </header>

    <!-- Combat Interface -->
       <main class="flex-grow p-4 pt-0 md:p-4 md:pt-0 backdrop-blur-sm rounded-lg  m-2">
     

        <!-- Interface Graphique Combat -->
        <div class="flex flex-col md:flex-col justify-center items-center gap-8 ">
            <!-- Décor -->
          
            <div class="w-full md:w-[55%] h-[500px] bg-gray-700 rounded-lg flex items-center justify-center drop-shadow-[0_0_5px_#a78bfa]">
              <img src="/assets/images/bg_combat_default.png" class="w-full h-full" id="bg" alt="">

                <div class="absolute top-8 flex flex-col text-justify">
                    <h2 class="text-2xl md:text-3xl font-bold text-white text-center drop-shadow-lg mb-4">VS <?php echo $monsterModel->getName()?></h2>
                    <div id="combat-log"></div>

                </div>
                <div class ="absolute top-25 z-50">
                    <div id="win?"></div>
                </div>
               <div class="absolute bottom-7 w-64 h-64 overflow-hidden">
                    <img src= <?php echo $monsterModel->getImagePath()?> alt="Monstre" class="object-cover w-full h-full">
                </div>
                <div id="dice" class="absolute bottom-3 right-3 text-4xl font-bold text-violet-400 drop-shadow-[0_0_6px_#a78bfa]">
                    🎲 ?
                </div>
.
            </div>

            
               
           
              <div class="w-full md:w-[55%] bg-gray-800 rounded-lg flex flex-col md:flex-row relative overflow-visible p-4">

                <!-- Stats à gauche -->
                <div class="w-full md:w-1/3 flex flex-col mb-4 md:mb-0 bg-gray-900/50 rounded-lg ">
                    <?php
                    echo "<h3 class='text-violet-400 font-bold mb-2 text-center mt-2 md:text-xl'>Stats de " . htmlspecialchars($characterModel->getName()) . "</h3>";
                    ?>
                    <ul class="text-sm grid grid-cols-2 gap-6 mx-auto">
                    <li class="flex items-center gap-2  w-full">
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
                    $id = $characterModel->getClassName();
                    switch ($id) {
                        case 1:
                            $classImage = '/assets/images/warrior.png';
                            break;
                        case 2:
                            $classImage = '/assets/images/wizard.png';
                            break;
                        case 3:
                            $classImage = '/assets/images/thief.png';
                            break;
                        default:
                            $classImage = '/assets/images/default_class.png';
                            break;
                    }
                ?>

                <!-- Cercle au centre -->
                <div class="w-full md:w-1/3 flex items-center justify-center relative mb-4 md:mb-0">
                    <div class="w-48 h-48 rounded-full border-4 border-violet-500 bg-gray-700 overflow-hidden">
                    <img src=<?php echo $classImage ?> alt="Personnage" class="object-cover w-full h-full">
                    </div>
                </div>

                <!-- Capacités à droite -->
                <div class="w-full md:w-1/3 flex flex-col bg-gray-900/50 rounded-lg">
                    <h3 class='text-violet-400 font-bold mb-2 text-center mt-2 md:text-xl'>Capacités de combat</h3>
                    <ul class="text-sm grid grid-cols-2 gap-2 mx-auto">
                    <li>
                        <button     id="btn-attack"
                                    onclick="sendAction('attack')"
                                    class="w-full px-4 py-2 bg-gray-700 text-red-400 rounded-lg border border-violet-500/50 
                                    hover:bg-gray-600 hover:text-white hover:scale-105 transition transform duration-200">
                        Attack
                        </button>
                    </li>
                    <li>
                        <button     id="btn-defend"
                                    onclick="sendAction('defend')"
                                    class="w-full px-4 py-2 bg-gray-700 text-blue-400 rounded-lg border border-violet-500/50 
                                    hover:bg-gray-600 hover:text-white hover:scale-105 transition transform duration-200">
                        Shield
                        </button>
                    </li>
                    <li>
                        <button     id="btn-heal"
                                    onclick="sendAction('usePotion')"
                                    class="w-full px-4 py-2 bg-gray-700 text-yellow-400 rounded-lg border border-violet-500/50 
                                    hover:bg-gray-600 hover:text-white hover:scale-105 transition transform duration-200">
                        Heal
                        </button>
                    </li>
                    <li>
                        <button     id="btn-run"
                                    onclick="sendAction('run')"
                                    class="w-full px-4 py-2 bg-gray-700 text-green-400 rounded-lg border border-violet-500/50 
                                    hover:bg-gray-600 hover:text-white hover:scale-105 transition transform duration-200">
                        ...
                        </button>
                    </li>
                    </ul>
                    <div class="mt-4 text-center">
                        <button onclick="rollDice()" id="rollBtn"class="text-violet-400 p-2 rounded-lg border  border-violet-500 hover:bg-gray-600 hover:text-white hover:scale-105 transition transform duration-200">Lancer le dés</button>
                    </div>
                </div>

            </div>
        </div>

      
        

       </main>
    </div>


    <script>


        const winOrLoss = document.getElementById('win?');
        const bg = document.getElementById('bg');
        const btn = document.getElementById("rollBtn");
                // Récupération des boutons dans des constantes
        const btnAttack = document.getElementById("btn-attack");
        const btnDefend = document.getElementById("btn-defend");
        const btnHeal   = document.getElementById("btn-heal");
        const btnRun    = document.getElementById("btn-run");

        // Exemple : désactiver tous les boutons pendant le tour du monstre
        function disableActions() {
        btnAttack.disabled = true;
        btnDefend.disabled = true;
        btnHeal.disabled   = true;
        btnRun.disabled    = true;
        }

        // Exemple : réactiver tous les boutons au début du tour du joueur
        function enableActions() {
        btnAttack.disabled = false;
        btnDefend.disabled = false;
        btnHeal.disabled   = false;
        btnRun.disabled    = false;
        }


   function sendAction(action) {
    disableActions();
    fetch("/game/combat/action", {
        method: "POST",
        headers: { "Content-Type": "application/x-www-form-urlencoded" },
        body: "action=" + encodeURIComponent(action)
    })
    .then(res => res.text())
    .then(text => {
        try {
            const data = JSON.parse(text);
            const log = document.getElementById("combat-log");

            // Réinitialiser le log
            log.innerHTML = "";

            if (data.player) {
                log.innerHTML += `<p class="text-blue-400 font-semibold mb-1">
                     ${data.player}
                </p>`;
                }

                if (data.monster) {
                log.innerHTML += `<p class="text-red-400 font-semibold mb-1">
                     ${data.monster}
                </p>`;
                }

                setTimeout(() => {
                         if (!data.success) {
                log.innerHTML += `<p class="text-red-600 font-bold mb-2">
                     ${data.message}
                </p>`;
                }

                if (data.newTurn) {
                log.innerHTML += `<p class="text-yellow-400 font-bold animate-pulse mt-2">
                     À vous de jouer !
                </p>`;
                }
                    
                }, 1000);

           

            if(data.win == false){
                playerWin();
            }

            // Mettre à jour les HP du joueur
            if (typeof data.playerHp !== "undefined") {
                document.getElementById('player-hp').innerHTML = "";
                if(data.playerHp <= 0){
                    playerLoss();
                    document.getElementById('player-hp').innerHTML += "<p style='color:red'>"+ data.playerHp + "</p>";
                }
                else document.getElementById('player-hp').textContent = data.playerHp;
            }

           
            btn.disabled = false;
            console.log("HP joueur:", data.playerHp);
        } catch (e) {
            console.error("Réponse non JSON:", text);
        }
    });
}
        function rollDice() {
            enableActions();

        btn.disabled = true;
        const dice = document.getElementById("dice");
        let result = Math.floor(Math.random() * 20) + 1;

        dice.textContent = "🎲 ..."; // effet suspense
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

        function playerWin(){

            setTimeout(() => {
                document.getElementById('combat-log').textContent ="";
            winOrLoss.classList.add("text-green-600", "animate-pulse");
            bg.classList.add("brightness-125", "saturate-150", "contrast-125", "hue-rotate-30");

            winOrLoss.innerHTML = '<p class ="text-4xl font-bold z-9999"> YOU WIN </p>';
                
            },1000);
            
        }

        function playerLoss(){
            setTimeout(() => {
                document.getElementById('combat-log').textContent ="";
            console.log('loose');
            bg.classList.add("sepia", "saturate-200", "hue-rotate-[-50deg]", "brightness-75");
            winOrLoss.classList.add("text-red-600", "animate-pulse");
            winOrLoss.innerHTML = '<p class ="text-4xl font-bold z-9999"> YOU LOSS </p>';
                
            }, 1000);
            
        }
</script>

  
    
</body>
</html>