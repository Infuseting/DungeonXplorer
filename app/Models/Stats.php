<?php
namespace App\Models;

enum Stats : string {
    case Strength = 'strength';
    case Damage = 'damage';
    case Vitality = 'vitality';
    case Intelligence = 'intelligence';
    case Dexterity = 'dexterity';
    case Defense = 'defense';
}

