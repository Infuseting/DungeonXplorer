# Configuration de la Musique par Carte

Chaque carte peut définir sa propre catégorie de musique via le fichier `map_config.json`.

## Propriété `musicCategory`

Ajoutez la propriété `musicCategory` dans votre `map_config.json` pour définir quelle musique jouer sur cette carte :

```json
{
  "width": 12678,
  "height": 7983,
  "tile_size": 256,
  "max_zoom": 6,
  "format": "png",
  "musicCategory": "exploration"
}
```

## Catégories Disponibles

- **`exploration`** : Musique d'exploration (carte principale)
- **`town`** : Musique de ville (zones sûres)
- **`dungeon`** : Musique de donjon (zones dangereuses)
- **`combat`** : Musique de combat
- **`boss`** : Musique de boss

## Fonctionnement

Lorsqu'une carte est chargée :
1. Le système lit le fichier `map_config.json`
2. Si `musicCategory` est défini, la musique change automatiquement
3. La transition est douce (fade out → fade in sur 1 seconde)

## Exemples de Configuration

### Carte Principale (Exploration)
```json
{
  "musicCategory": "exploration"
}
```

### Ville
```json
{
  "musicCategory": "town"
}
```

### Donjon
```json
{
  "musicCategory": "dungeon"
}
```

### Zone de Combat
```json
{
  "musicCategory": "combat"
}
```

### Arène de Boss
```json
{
  "musicCategory": "boss"
}
```

## Notes

- Si `musicCategory` n'est pas défini, la musique actuelle continue de jouer
- Le changement de musique se fait automatiquement lors du chargement de la carte
- Vous pouvez utiliser la même catégorie pour plusieurs cartes
- Les fichiers audio doivent être présents dans `/assets/audio/music/{category}/`
