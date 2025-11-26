<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - DungeonXplorer</title>
    <link rel="stylesheet" href="/assets/css/output.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <style>
        :root {
            --sidebar-width: 280px;
            --header-height: 70px;
            --primary: #6366f1;
            --primary-dark: #4f46e5;
            --bg-dark: #0f172a;
            --bg-darker: #020617;
            --text-light: #e2e8f0;
            --text-muted: #94a3b8;
            --border: #1e293b;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            background: var(--bg-darker);
            color: var(--text-light);
        }
        
        .admin-layout {
            display: flex;
            min-height: 100vh;
        }
        
        .sidebar {
            width: var(--sidebar-width);
            background: var(--bg-dark);
            border-right: 1px solid var(--border);
            position: fixed;
            height: 100vh;
            overflow-y: auto;
            z-index: 100;
        }
        
        .sidebar-header {
            padding: 1.5rem;
            border-bottom: 1px solid var(--border);
        }
        
        .sidebar-logo {
            font-size: 1.5rem;
            font-weight: 700;
            background: linear-gradient(135deg, var(--primary), #a855f7);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        
        .sidebar-nav {
            padding: 1rem 0;
        }
        
        .nav-item {
            display: flex;
            align-items: center;
            padding: 0.875rem 1.5rem;
            color: var(--text-muted);
            text-decoration: none;
            transition: all 0.2s;
            border-left: 3px solid transparent;
        }
        
        .nav-item:hover {
            background: rgba(99, 102, 241, 0.1);
            color: var(--text-light);
            border-left-color: var(--primary);
        }
        
        .nav-item.active {
            background: rgba(99, 102, 241, 0.15);
            color: var(--primary);
            border-left-color: var(--primary);
        }
        
        .nav-icon {
            margin-right: 0.75rem;
            font-size: 1.25rem;
        }
        
        .main-content {
            flex: 1;
            margin-left: var(--sidebar-width);
        }
        
        .header {
            height: var(--header-height);
            background: var(--bg-dark);
            border-bottom: 1px solid var(--border);
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 2rem;
            position: sticky;
            top: 0;
            z-index: 50;
        }
        
        .header-title {
            font-size: 1.5rem;
            font-weight: 600;
        }
        
        .user-menu {
            display: flex;
            align-items: center;
            gap: 1rem;
        }
        
        .user-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--primary), #a855f7);
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
        }
        
        .content {
            padding: 2rem;
        }
        
        .btn {
            padding: 0.625rem 1.25rem;
            border-radius: 0.5rem;
            border: none;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.2s;
            text-decoration: none;
            display: inline-block;
        }
        
        .btn-primary {
            background: var(--primary);
            color: white;
        }
        
        .btn-primary:hover {
            background: var(--primary-dark);
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(99, 102, 241, 0.4);
        }
        
        .card {
            background: var(--bg-dark);
            border: 1px solid var(--border);
            border-radius: 0.75rem;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
        }
        
        .card-header {
            font-size: 1.25rem;
            font-weight: 600;
            margin-bottom: 1rem;
        }
    </style>
</head>
<body>
    <div class="admin-layout">
        <aside class="sidebar">
            <div class="sidebar-header">
                <div class="sidebar-logo">⚔️ DungeonXplorer</div>
            </div>
            <nav class="sidebar-nav">
                <a href="/admin" class="nav-item <?= $_SERVER['REQUEST_URI'] === '/admin' ? 'active' : '' ?>">
                    <span class="nav-icon">📊</span>
                    Dashboard
                </a>
                <a href="/admin/map" class="nav-item <?= strpos($_SERVER['REQUEST_URI'], '/admin/map') !== false ? 'active' : '' ?>">
                    <span class="nav-icon">🗺️</span>
                    Gestion de la Carte
                </a>
                <a href="/admin/points" class="nav-item <?= strpos($_SERVER['REQUEST_URI'], '/admin/points') !== false ? 'active' : '' ?>">
                    <span class="nav-icon">⚙️</span>
                    Gestion des lieux
                </a>
                <a href="/admin/items" class="nav-item <?= strpos($_SERVER['REQUEST_URI'], '/admin/items') !== false ? 'active' : '' ?>">
                    <span class="nav-icon">🎒</span>
                    Gestion des Items
                </a>
                <a href="/admin/npcs" class="nav-item <?= strpos($_SERVER['REQUEST_URI'], '/admin/npcs') === 0 ? 'active' : '' ?>">
                    <span class="nav-icon">👥</span>
                    Gestion des PNJ
                </a>
                <a href="/admin/dialogues" class="nav-item <?= strpos($_SERVER['REQUEST_URI'], '/admin/dialogues') === 0 ? 'active' : '' ?>">
                    <span class="nav-icon">💬</span>
                    Gestion des Dialogues
                </a>
                <a href="/admin/quests" class="nav-item <?= strpos($_SERVER['REQUEST_URI'], '/admin/quests') === 0 ? 'active' : '' ?>">
                    <span class="nav-icon">📜</span>
                    Gestion des Quêtes
                </a>
                <a href="/" class="nav-item">
                    <span class="nav-icon">🏠</span>
                    Retour au Jeu
                </a>
            </nav>
        </aside>
        
        <div class="main-content">
            <header class="header">
                <h1 class="header-title"><?= $pageTitle ?? 'Admin Panel' ?></h1>
                <div class="user-menu">
                    <div class="user-avatar">
                        <?= strtoupper(substr($_SESSION['username'] ?? 'A', 0, 1)) ?>
                    </div>
                </div>
            </header>
            
            <main class="content">
                <?php echo $content ?? ''; ?>
            </main>
        </div>
    </div>
</body>
</html>
