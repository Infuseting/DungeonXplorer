<?php
namespace App\Models;

if (isset($_POST['diceRoll'])) {
    $_SESSION['diceRoll'] = intval($_POST['diceRoll']);
}
else {
    http_response_code(400);
    echo "Erreur : aucune valeur reçue";
}




class Combat
    { 
        private  $joueur;
        private $boss;
        private $end = false;

        public function __construct(  $joueur, $boss)
        {
            $this->joueur = $joueur;
            $this->boss = $boss;
        }

        public function start()
        {
            while (!$this->end) {
                echo $this->joueur->getName() . "'s turn:\n";
                if ($this->isAttaqueSuccessfulFromPlayer()) {
                    $damage = $this->joueur->getAttaqueClass()-$this->boss->getArmorClass()/4;
                    $this->boss->setVitality($this->boss->getVitality() - $damage);
                    echo $this->joueur->getName() . " hits " . $this->boss->getName() . " for " . $damage . " damage!\n";
                } else {
                    echo $this->joueur->getName() . " misses!\n";
                }
                if (!$this->boss->isAlive()) {
                    echo $this->boss->getName() . " has been defeated!\n";
                    $this->endCombat();
                    break;
                }
                echo $this->boss->getName() . "'s turn:\n";
                if ($this->isAttaqueSuccessfulFromMonster()) {
                    $damage = $this->boss->getAttaqueClass()-$this->joueur->getArmorClass();
                    $this->joueur->setVitality($this->joueur->getVitality() - $damage);
                    echo $this->boss->getName() . " hits " . $this->joueur->getName() . " for " . $damage . " damage!\n";
                } else {
                    echo $this->boss->getName() . " misses!\n";
                }
                if (!$this->joueur->isAlive()) {
                    echo $this->joueur->getName() . " has been defeated!\n";
                   $this-> endCombat();
                    break;
                }
                

               

            }
        }

        public function dice()
        {
            return rand(1, 20);
        }

        public function isAlive($entity)
        {
            if ($entity->getVitality() <= 0) {
                echo $entity->getName() . " has been defeated!\n";
                $this->end = true;
                return false;
            }
            return true;
        }

        public function getJoueur()
        {
            return $this->joueur;
        }

        public function getBoss()
        {
            return $this->boss;
        }

        public function isAttaqueSuccessfulFromPlayer()
        {
            $attackRoll = $this->joueur->getAttaqueClass()+$this->getDice();
            $defenseRoll = $this->boss->getArmorClass()+$this->getDice()/2;

            echo "Attack Roll: " . $attackRoll . " vs Defense Roll: " . $defenseRoll . "\n";
            
            return $attackRoll >= $defenseRoll;
        }
        
        public function isAttaqueSuccessfulFromMonster()
        {
            $attackRoll = $this->boss->getAttaqueClass()+$this->getDice()/2;
            $defenseRoll = $this->joueur->getArmorClass()+$this->getDice();

            echo "Attack Roll: " . $attackRoll . " vs Defense Roll: " . $defenseRoll . "\n";
            
            return $attackRoll >= $defenseRoll;
        }

        public function endCombat()
        {
            $this->end = true;
        }

        public function getDice()
        {
            $jet1 = $this->dice();
            $jet2 = $this->dice();
            if($jet1 > $jet2){
                return $jet1;
            } else {
                return $jet2;
            }
            if($jet1 == $jet2){
                return 20;
            }
        
        }

    }