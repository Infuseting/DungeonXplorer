# README - Système de Musique Contextuelle

Le jeu utilise un système de musique contextuelle avec des pools de musiques par catégorie. Chaque catégorie peut contenir plusieurs pistes qui seront choisies aléatoirement.

## Structure des Dossiers

```
public/assets/audio/
├── music/
│   ├── exploration/
│   │   ├── track1.mp3
│   │   ├── track2.mp3
│   │   └── track3.mp3
│   ├── combat/
│   │   ├── track1.mp3
│   │   └── track2.mp3
│   ├── dungeon/
│   │   ├── track1.mp3
│   │   ├── track2.mp3
│   │   └── track3.mp3
│   ├── town/
│   │   ├── track1.mp3
│   │   └── track2.mp3
│   └── boss/
│       └── track1.mp3
└── sfx/
    ├── click.mp3
    ├── hover.mp3
    ├── open.mp3
    ├── close.mp3
    ├── item-pickup.mp3
    └── notification.mp3
```

## Catégories de Musique

### 🌍 Exploration (Défaut)
Musique d'ambiance pour l'exploration du monde. C'est la catégorie par défaut au démarrage du jeu.
- **Nombre de pistes recommandées** : 3+
- **Style** : Calme, atmosphérique, exploration

### ⚔️ Combat
Musique dynamique pour les phases de combat.
- **Nombre de pistes recommandées** : 2+
- **Style** : Énergique, intense, action

### 🏰 Dungeon
Musique pour les donjons et zones dangereuses.
- **Nombre de pistes recommandées** : 3+
- **Style** : Sombre, mystérieux, tension

### 🏘️ Town
Musique pour les villes et zones sûres.
- **Nombre de pistes recommandées** : 2+
- **Style** : Paisible, accueillant, social

### 👹 Boss
Musique épique pour les combats de boss.
- **Nombre de pistes recommandées** : 1+
- **Style** : Épique, dramatique, intense

## Fonctionnement

### Sélection Aléatoire
- Quand une catégorie est activée, une piste est choisie **aléatoirement** parmi celles disponibles
- Le système évite de rejouer la même piste deux fois de suite (si plusieurs pistes sont disponibles)
- Quand une piste se termine, une nouvelle piste aléatoire de la même catégorie est automatiquement chargée

### Transitions
- **Transition douce (par défaut)** : Fade out de 1 seconde → Changement de piste → Fade in de 1 seconde
- **Transition immédiate** : Changement instantané sans fade

### Lazy Loading
- Seules les métadonnées sont chargées au départ
- Le fichier audio complet est chargé progressivement (streaming) lors de la lecture
- Optimisé pour éviter les longs temps de chargement

## Utilisation dans le Code

```javascript
import { changeMusicCategory } from './soundManager.js';

// Changer de catégorie avec transition douce
changeMusicCategory('combat');

// Changer de catégorie immédiatement
changeMusicCategory('dungeon', true);

// Catégories disponibles:
// - 'exploration' (défaut)
// - 'combat'
// - 'dungeon'
// - 'town'
// - 'boss'
```

## Exemples d'Intégration

### Entrer en Combat
```javascript
// Dans votre système de combat
function startCombat() {
    changeMusicCategory('combat');
    // ... reste du code de combat
}

function endCombat() {
    changeMusicCategory('exploration');
    // ... reste du code
}
```

### Entrer dans un Donjon
```javascript
// Lors du chargement d'une sous-carte (donjon)
function loadDungeon() {
    changeMusicCategory('dungeon');
    // ... chargement du donjon
}
```

### Combat de Boss
```javascript
// Lors du déclenchement d'un combat de boss
function startBossFight() {
    changeMusicCategory('boss', true); // Transition immédiate pour l'impact
    // ... combat de boss
}
```

## Sources Audio Gratuites Recommandées

### Musique
- **OpenGameArt.org** : https://opengameart.org/art-search-advanced?keys=&field_art_type_tid%5B%5D=12
  - Recherchez par tags : "combat", "dungeon", "ambient", "boss"
- **Incompetech** : https://incompetech.com/music/royalty-free/music.html
  - Filtres par genre et mood
- **FreePD** : https://freepd.com/
- **Purple Planet** : https://www.purple-planet.com/

### Effets Sonores
- **Freesound.org** : https://freesound.org/
- **Zapsplat** : https://www.zapsplat.com/
- **Mixkit** : https://mixkit.co/free-sound-effects/

## Format Recommandé

### Musique
- **Format** : MP3 (meilleure compatibilité)
- **Bitrate** : 128-192 kbps
- **Taille** : < 5 MB par piste
- **Durée** : 2-5 minutes par piste
- **Boucle** : Pas nécessaire (le système charge automatiquement la piste suivante)

### Effets Sonores
- **Format** : MP3
- **Bitrate** : 64-128 kbps
- **Taille** : < 100 KB chacun
- **Durée** : < 2 secondes

## Configuration Avancée

### Ajouter une Nouvelle Catégorie

Dans `soundManager.js`, ajoutez votre catégorie dans `musicPools` :

```javascript
this.musicPools = {
    // ... catégories existantes
    custom: [
        '/assets/audio/music/custom/track1.mp3',
        '/assets/audio/music/custom/track2.mp3'
    ]
};
```

Puis créez le dossier correspondant :
```
public/assets/audio/music/custom/
```

### Modifier le Temps de Fade

Dans `soundManager.js`, modifiez `fadeOutDuration` :

```javascript
this.fadeOutDuration = 2000; // 2 secondes au lieu de 1
```

## Dépannage

### La musique ne démarre pas
- Vérifiez que les fichiers audio sont bien présents dans les dossiers
- Vérifiez la console du navigateur pour les erreurs
- La musique démarre après la première interaction utilisateur (politique autoplay)

### Les pistes ne changent pas
- Vérifiez que vous avez plusieurs pistes dans la catégorie
- Vérifiez les chemins dans `musicPools`
- Vérifiez la console pour les warnings

### Transitions saccadées
- Réduisez la taille des fichiers audio
- Vérifiez votre connexion réseau
- Essayez de précharger les pistes (modification avancée)

## Notes Importantes

1. **Autoplay Policy** : La musique démarre automatiquement après la première interaction utilisateur
2. **Catégorie par défaut** : `exploration` est la catégorie par défaut au démarrage
3. **Volumes** : Les préférences de volume sont sauvegardées dans localStorage
4. **Performance** : Le lazy loading optimise les temps de chargement

## Test

1. Ajoutez vos fichiers audio dans les dossiers appropriés
2. Ouvrez le jeu dans le navigateur
3. Cliquez n'importe où pour démarrer la musique
4. Ouvrez la console (F12) pour voir les logs du SoundManager
5. Testez les changements de catégorie avec `changeMusicCategory('combat')`

Bon jeu ! 🎮🎵
