# DungeonXplorer

School project for a web application to play a dungeon crawler game.

## Setup

1.  **Install Dependencies**:
    ```bash
    composer install
    ```

2.  **Environment Configuration**:
    - Copy `.env.example` to `.env`:
      ```bash
      cp .env.example .env
      ```
    - Edit `.env` with your database credentials.

3.  **Run the Project**:
    - Start the PHP built-in server:
      ```bash
      php -S localhost:8000 -t public
      ```
    - Open [http://localhost:8000](http://localhost:8000) in your browser.

## Features

### Admin
![Dashboard](/image/dashboard.png)
- **Logs**: Display all logs of the website. Like NPC interaction, Fight, ...
- **Characters**: Display all characters of the website.
- **Easy Spells & Class implementation**: Allow to create, edit and delete spells and classes.
- **Maps**: Easy way to add new maps, points and edit them.
- **NPC**: Easy way to add new NPCs, edit and delete them.
- **Quest**: Fully custom way to implement quest, with custom rewards and custom requirements.
- **Monster**: Easy way to add new monsters, edit and delete them.
- **Item**: Easy way to add new items, edit and delete them.
- **Dungeon & History**: An easy way to use node system to create different dungeons and history.
- **Houses** : Create your own houses with custom stuff, furnish and storage.
### Login and Register
![Login](/image/login.png)
- **OAUTH System**: Allow to login with Google, Discord, Github, and you can easely add custom providers.
- **Password Reset**: Allow you to reset your password if you lost it using code.
- **OAUTH and Email**: You can link your account with all the OAUTH system.
### Customisation
![Customisation](/image/customisation.png)
- **Skins Customisation**: Allow you to customise your character include skin, hair, eyes, etc.
- **Spells** : You can create your own build with spells, stuff, ...
- **Houses**: Custom your house with furnish, storage, etc.
- **Class**: Choose your class between Mage, Warrior, Rogue, etc.
### Inventory
![Inventory](/image/inventory.png)
- **Inventory**: Allow you to store your items in your inventory.
- **Equipment**: Allow you to equip items with random stats to improve your character.
- **Filter**: Allow you to filter your inventory by type, name, etc.
- **Weight**: Allow you to see the weight of your inventory. And get malus if you are too heavy.
- **Sell**: Allow you to sell your items to get gold.
- **Trash**: Allow you to delete your items.
- **Compare**: Allow you to compare items equip and not equip.
### Shop
![Shop](/image/shop.png)
- **Shop**: Allow you to buy and sell items with gold.
- **Stats**: Allow you to see the stats of the items.
### History & Quest
![History](/image/history.png)
- **History**: Allow you to move, loot and fight in dungeon.
- **Quest**: Allow you to do quest and get rewards.





### 



## Contributors

- [Antoine Matter](https://github.com/Antoin9-e)
- [Arthur Langlois](https://github.com/FxBam)
- [aR7dx](https://github.com/aR7dx)
- [Remy Leber](https://github.com/Remynder0)