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
              <img src="/assets/images/bg_combat_default.png" class="w-full h-full" alt="">

                <div class="absolute top-8">
                    <h2 class="text-2xl md:text-3xl font-bold text-white text-center drop-shadow-lg">Infos du combat !</h2>

                </div>
               <div class="absolute bottom-7 w-64 h-64 overflow-hidden">
                    <img src="/assets/images/minautoreAsset.png" alt="Monstre" class="object-cover w-full h-full">
                </div>
                <div id="dice" class="absolute bottom-3 right-3 text-4xl font-bold text-violet-400 drop-shadow-[0_0_6px_#a78bfa]">
                    🎲 ?
                </div>

            </div>

            
               
           
              <div class="w-full md:w-[55%] bg-gray-800 rounded-lg flex flex-col md:flex-row relative overflow-visible p-4">

                <!-- Stats à gauche -->
                <div class="w-full md:w-1/3 flex flex-col mb-4 md:mb-0">
                    <?php
                    echo "<h3 class='text-violet-400 font-bold mb-2 text-center mt-2 md:text-xl'>Stats de " . htmlspecialchars($characterModel->getName()) . "</h3>";
                    ?>
                    <ul class="text-sm grid grid-cols-2 gap-6 mx-auto">
                    <li class="flex items-center gap-2  w-full">
                        <span class="material-symbols-outlined text-violet-400" title="Santé du joueur">favorite</span>
                        <span><?php echo htmlspecialchars($characterModel->getVitality()); ?></span>
                    </li>
                    <li class="flex items-center gap-2 w-full">
                        <span class="material-symbols-outlined text-violet-400" title="Puissance d'attaque">swords</span>
                        <span><?php echo htmlspecialchars($characterModel->getAttaqueClass()); ?></span>
                    </li>
                    <li class="flex items-center gap-2 w-full">
                        <span class="material-symbols-outlined text-violet-400" title="Défense du joueur">shield</span>
                        <span><?php echo htmlspecialchars($characterModel->getArmorClass()); ?></span>
                    </li>
                    <li class="flex items-center gap-2 w-full">
                        <span class="material-symbols-outlined text-violet-400" title="Force du joueur">fitness_center</span>
                        <span><?php echo htmlspecialchars($characterModel->getStrength()); ?></span>
                    </li>
                    <li class="flex items-center gap-2 w-full">
                        <span class="material-symbols-outlined text-violet-400" title="Intelligence du joueur">psychology</span>
                        <span><?php echo htmlspecialchars($characterModel->getIntelligence()); ?></span>
                    </li>
                    <li class="flex items-center gap-2 w-full">
                        <span class="material-symbols-outlined text-violet-400" title="Dextérié du joueur">directions_run</span>
                        <span><?php echo htmlspecialchars($characterModel->getDexterity()); ?></span>
                    </li>


                    </ul>
                    
                </div>

                <!-- Cercle au centre -->
                <div class="w-full md:w-1/3 flex items-center justify-center relative mb-4 md:mb-0">
                    <div class="w-48 h-48 rounded-full border-4 border-violet-500 bg-gray-700 overflow-hidden">
                    <img src="/assets/images/warrior.png" alt="Personnage" class="object-cover w-full h-full">
                    </div>
                </div>

                <!-- Capacités à droite -->
                <div class="w-full md:w-1/3 flex flex-col">
                    <h3 class='text-violet-400 font-bold mb-2 text-center mt-2 md:text-xl'>Capacités de combat</h3>
                    <ul class="text-sm grid grid-cols-2 gap-2 mx-auto">
                    <li>
                        <button class="w-full px-4 py-2 bg-gray-700 text-red-400 rounded-lg border border-violet-500/50 
                                    hover:bg-gray-600 hover:text-white hover:scale-105 transition transform duration-200">
                        Attack
                        </button>
                    </li>
                    <li>
                        <button class="w-full px-4 py-2 bg-gray-700 text-blue-400 rounded-lg border border-violet-500/50 
                                    hover:bg-gray-600 hover:text-white hover:scale-105 transition transform duration-200">
                        Shield
                        </button>
                    </li>
                    <li>
                        <button class="w-full px-4 py-2 bg-gray-700 text-yellow-400 rounded-lg border border-violet-500/50 
                                    hover:bg-gray-600 hover:text-white hover:scale-105 transition transform duration-200">
                        Heal
                        </button>
                    </li>
                    <li>
                        <button class="w-full px-4 py-2 bg-gray-700 text-green-400 rounded-lg border border-violet-500/50 
                                    hover:bg-gray-600 hover:text-white hover:scale-105 transition transform duration-200">
                        ...
                        </button>
                    </li>
                    </ul>
                    <div class="mt-4 text-center">
                        <button onclick="rollDice()" class="text-violet-400 p-2 rounded-lg border  border-violet-500 hover:bg-gray-600 hover:text-white hover:scale-105 transition transform duration-200">Lancer le dés</button>
                    </div>
                </div>

            </div>
        </div>

      
        

       </main>
    </div>


    <script>
        function rollDice() {
        const dice = document.getElementById("dice");
        let result = Math.floor(Math.random() * 20) + 1;

        dice.textContent = "🎲 ..."; // effet suspense
        dice.classList.add("animate-spin"); // rotation Tailwind

        setTimeout(() => {
            dice.classList.remove("animate-spin");
            dice.textContent = "🎲 " + result;
        }, 1000);

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
            console.error("There was a problem with the fetch operation:", error)})
            ;


        }
</script>

  
    
</body>
</html>