-- Users Table
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- User Tokens Table (for Remember Me functionality)
CREATE TABLE IF NOT EXISTS user_tokens (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    selector VARCHAR(24) NOT NULL,
    hashed_validator VARCHAR(64) NOT NULL,
    expires_at DATETIME NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
CREATE TABLE IF NOT EXISTS classes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) NOT NULL UNIQUE,
    description TEXT,
    base_stats_json JSON,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS characters (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    class_id INT NOT NULL,
    name VARCHAR(50) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    last_played_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (class_id) REFERENCES classes(id)
);

CREATE TABLE IF NOT EXISTS character_stats (
    character_id INT PRIMARY KEY,
    level INT DEFAULT 1,
    xp INT DEFAULT 0,
    strength INT DEFAULT 10,
    dexterity INT DEFAULT 10,
    intelligence INT DEFAULT 10,
    vitality INT DEFAULT 10,
    FOREIGN KEY (character_id) REFERENCES characters(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS character_appearance (
    character_id INT PRIMARY KEY,
    skin_color VARCHAR(20) DEFAULT '#ffdbac',
    hair_style VARCHAR(50) DEFAULT 'bald',
    hair_color VARCHAR(20) DEFAULT '#000000',
    eye_color VARCHAR(20) DEFAULT '#000000',
    face_style VARCHAR(50) DEFAULT 'default',
    FOREIGN KEY (character_id) REFERENCES characters(id) ON DELETE CASCADE
);

-- Insert default classes
INSERT INTO classes (name, description, base_stats_json) VALUES 
('Guerrier', 'Un combattant robuste spécialisé dans le corps à corps.', '{"strength": 15, "dexterity": 10, "intelligence": 5, "vitality": 15}'),
('Mage', 'Un maître des arcanes capable de déchaîner des sorts dévastateurs.', '{"strength": 5, "dexterity": 10, "intelligence": 15, "vitality": 10}'),
('Voleur', 'Un expert de la furtivité et des attaques rapides.', '{"strength": 10, "dexterity": 15, "intelligence": 10, "vitality": 10}')
ON DUPLICATE KEY UPDATE name=name;

-- Items Table
CREATE TABLE IF NOT EXISTS items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    description TEXT,
    type ENUM('equipment', 'consumable', 'material') NOT NULL,
    slot_type ENUM('head', 'shoulders', 'amulet', 'chest', 'belt', 'legs', 'boots', 'ring', 'main_hand', 'off_hand', 'gloves', 'bracers', 'backpack', 'none') NOT NULL DEFAULT 'none',
    two_handed BOOLEAN NOT NULL DEFAULT FALSE,
    width TINYINT NOT NULL DEFAULT 1,
    height TINYINT NOT NULL DEFAULT 1,
    weight DECIMAL(5,2) NOT NULL DEFAULT 0.00,
    icon VARCHAR(255),
    stats JSON,
    max_stack TINYINT NOT NULL DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Character Inventory Table
CREATE TABLE IF NOT EXISTS character_inventory (
    id INT AUTO_INCREMENT PRIMARY KEY,
    character_id INT NOT NULL,
    item_id INT NOT NULL,
    location ENUM('equipped', 'backpack', 'pockets') NOT NULL,
    slot_name ENUM('head', 'shoulders', 'amulet', 'chest', 'belt', 'legs', 'boots', 'ring_1', 'ring_2', 'main_hand', 'off_hand', 'gloves', 'bracers', 'backpack') DEFAULT NULL,
    grid_x TINYINT DEFAULT NULL,
    grid_y TINYINT DEFAULT NULL,
    quantity INT NOT NULL DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (character_id) REFERENCES characters(id) ON DELETE CASCADE,
    FOREIGN KEY (item_id) REFERENCES items(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Seed Items
INSERT INTO items (name, description, type, slot_type, two_handed, width, height, weight, icon, stats, max_stack) VALUES 
('Épée Rouillée', 'Une vieille épée qui a vu des jours meilleurs.', 'equipment', 'main_hand', FALSE, 1, 3, 2.5, 'rusty_sword.png', '{"strength": 2, "damage": 5}', 1),
('Sac à Dos en Cuir', 'Un sac simple mais robuste.', 'equipment', 'backpack', FALSE, 2, 2, 1.0, 'leather_backpack.png', '{"capacity_width": 6, "capacity_height": 4}', 1),
('Potion de Soin', 'Restaure 50 PV.', 'consumable', 'none', FALSE, 1, 1, 0.5, 'health_potion.png', '{"heal": 50}', 5),
('Plastron de Fer', 'Une armure lourde pour les guerriers.', 'equipment', 'chest', FALSE, 2, 3, 8.0, 'iron_chestplate.png', '{"defense": 15, "vitality": 5}', 1),
('Grande Épée', 'Une épée massive nécessitant deux mains.', 'equipment', 'main_hand', TRUE, 1, 4, 5.0, 'greatsword.png', '{"strength": 5, "damage": 15}', 1);

