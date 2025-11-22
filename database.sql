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
