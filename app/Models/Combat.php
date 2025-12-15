<?php
namespace App\Models;

if (isset($_POST['diceRoll'])) {
    $_SESSION['diceRoll'] = intval($_POST['diceRoll']);
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

  

        public function dice()
        {
            return rand(1, 20);
        }

        public function isAlive($entity)
        {
            if ($entity->getVitality() <= 0) {
                $this->end = true;
                return false;
            }
            return true;
        }

       

        public function playerTurn($action){
            $bool = false;

          switch ($action) {
            case 'attack':
                if ($this->isAttaqueSuccessfulFromPlayer()) {
                    $bool = true;
                    $damage = $this->joueur->getAttaqueClass();
                    $this->boss->reduceVitality($damage);
                    $message = $this->joueur->getName() . " hits " . $this->boss->getName() . " for " . $damage . " damage!\n";
                } else {
                    $message = $this->joueur->getName() . " misses " . $this->boss->getName() . "!\n";
                }
                break;

            case 'defend':
                // Stocker la défense initiale
                $_SESSION['initialDefence'] = $this->joueur->getArmorClass();

                // Bonus temporaire (ici basé sur le dé)
                $bonus = $_SESSION['diceRoll'];
                $this->joueur->setArmorClass($_SESSION['initialDefence'] + $bonus);

                $message = $this->joueur->getName() . " takes a defensive stance (+$bonus armor)!\n";
                break;
            case 'usePotion':
                $health =$this->joueur->getVitality() + 5;
                if($health > $_SESSION['maxHpPlayer']){
                    $this->joueur->setVitality($_SESSION['maxHpPlayer']);
                    $message = $this->joueur->getName() . " takes a heal  (max health)!\n";
                }
                else{
                    $this->joueur->setVitality($health);
                     $message = $this->joueur->getName() . " takes a heal  (+5 health)!\n";

                }
                break;

            default:
                $message = $this->joueur->getName() . " does nothing...\n";
                break;
        }

            if(!$this->isAlive($this->boss)) {
                $message .= $this->joueur->getName() . " wins the combat!\n";
                $this->endCombat();
            }

            return [$message,$bool];

            
        }

        public function monsterTurn()
        {
            $dice = $this->dice();
            $bool = false;

            if($this->isMonsterAlive()){
                if($this->isAttaqueSuccessfulFromMonster()) {
                    $bool = true;
                
                    $damage = $this->boss->getAttaque();
                    $this->joueur->reduceVitality($damage);
                    $message = $this->boss->getName() . " hits " . $this->joueur->getName() . " for " . $damage . " damage!\n";
                    } else {
                        $message =  $this->boss->getName() . " misses " . $this->joueur->getName() . "!\n";
                    }
                    if(!$this->isAlive($this->joueur)) {
                        $message .= $this->boss->getName() . " wins the combat!\n";
                        $this->endCombat();
                }
                
            }else{
                $message = $this->boss->getName()." a été vaincu ! ";
            }
            

         
            return [$message,$bool];
        }

        public function isEnd(){
            return $this->end;
        }


        public function getJoueur()
        {
            return $this->joueur;
        }

        public function getBoss()
        {
            return $this->boss;
        }

        public function getPlayerHp(){
            return $this->joueur->getVitality();
        }

        public function isAttaqueSuccessfulFromPlayer()
        {
            $attackRoll = $this->joueur->getAttaqueClass()+$_SESSION['diceRoll'];
            $defenseRoll = $this->boss->getArmorClass()+$this->dice()/3;

            
            
            return $attackRoll >= $defenseRoll;
        }
        
        public function isAttaqueSuccessfulFromMonster()
        {
            $attackRoll = $this->boss->getAttaqueClass()+$this->dice()/2;
            $defenseRoll = $this->joueur->getArmorClass()+$_SESSION['diceRoll'];

            
            
            return $attackRoll >= $defenseRoll;
        }

        public function isMonsterAlive(){
            return  $this->boss->isAlive();
        }

        public function endCombat()
        {
            $this->end = true;
        }

       

        public function getActions() {
        return ['attack', 'usePotion', 'defend', 'run'];
    }


    }