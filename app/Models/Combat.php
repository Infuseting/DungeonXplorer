 <?php

namespace App\Models;

use App\Config\Database;

class Combat
{
    private Character $joueur;
    private $boss;

    public function __construct( Character $joueur, $boss)
    {
        $this->joueur = $joueur;
        $this->boss = $boss;
    }

    // Add combat-related methods here
}