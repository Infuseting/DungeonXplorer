<?php
$pageTitle = 'Dashboard';
ob_start();
?>

<style>
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 1.5rem;
        margin-bottom: 2rem;
    }
    
    .stat-card {
        background: linear-gradient(135deg, var(--bg-dark) 0%, rgba(99, 102, 241, 0.1) 100%);
        border: 1px solid var(--border);
        border-radius: 0.75rem;
        padding: 1.5rem;
        position: relative;
        overflow: hidden;
    }
    
    .stat-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 4px;
        height: 100%;
        background: linear-gradient(180deg, var(--primary), #a855f7);
    }
    
    .stat-label {
        color: var(--text-muted);
        font-size: 0.875rem;
        margin-bottom: 0.5rem;
    }
    
    .stat-value {
        font-size: 2.5rem;
        font-weight: 700;
        background: linear-gradient(135deg, var(--primary), #a855f7);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }
    
    .stat-icon {
        position: absolute;
        top: 1rem;
        right: 1rem;
        font-size: 2.5rem;
        opacity: 0.2;
    }
    
    .charts-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(400px, 1fr));
        gap: 1.5rem;
        margin-bottom: 2rem;
    }
    
    .chart-card {
        background: var(--bg-dark);
        border: 1px solid var(--border);
        border-radius: 0.75rem;
        padding: 1.5rem;
    }
    
    .chart-title {
        font-size: 1.125rem;
        font-weight: 600;
        margin-bottom: 1rem;
    }
    
    .recent-activity {
        background: var(--bg-dark);
        border: 1px solid var(--border);
        border-radius: 0.75rem;
        padding: 1.5rem;
    }
    
    .activity-item {
        display: flex;
        align-items: center;
        padding: 1rem;
        border-bottom: 1px solid var(--border);
    }
    
    .activity-item:last-child {
        border-bottom: none;
    }
    
    .activity-icon {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background: rgba(99, 102, 241, 0.2);
        display: flex;
        align-items: center;
        justify-content: center;
        margin-right: 1rem;
    }
    
    .activity-details {
        flex: 1;
    }
    
    .activity-title {
        font-weight: 500;
        margin-bottom: 0.25rem;
    }
    
    .activity-time {
        color: var(--text-muted);
        font-size: 0.875rem;
    }
</style>

<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-icon">👥</div>
        <div class="stat-label">Total Utilisateurs</div>
        <div class="stat-value"><?= $stats['total_users'] ?? 0 ?></div>
    </div>
    
    <div class="stat-card">
        <div class="stat-icon">⚔️</div>
        <div class="stat-label">Total Personnages</div>
        <div class="stat-value"><?= $stats['total_characters'] ?? 0 ?></div>
    </div>
    
    <div class="stat-card">
        <div class="stat-icon">🗺️</div>
        <div class="stat-label">Total Cartes</div>
        <div class="stat-value"><?= $stats['total_maps'] ?? 0 ?></div>
    </div>
    
    <div class="stat-card">
        <div class="stat-icon">📜</div>
        <div class="stat-label">Total Quêtes</div>
        <div class="stat-value"><?= $stats['total_quests'] ?? 0 ?></div>
    </div>
</div>

<div class="charts-grid">
    <div class="chart-card">
        <h3 class="chart-title">Personnages par Classe</h3>
        <canvas id="classChart"></canvas>
    </div>
    
    <div class="chart-card">
        <h3 class="chart-title">Activité des 7 derniers jours</h3>
        <canvas id="activityChart"></canvas>
    </div>
</div>

<div class="recent-activity">
    <h3 class="chart-title">Personnages Récents</h3>
    <?php if (!empty($recentCharacters)): ?>
        <?php foreach ($recentCharacters as $character): ?>
            <div class="activity-item">
                <div class="activity-icon">⚔️</div>
                <div class="activity-details">
                    <div class="activity-title">
                        <?= htmlspecialchars($character['name']) ?> 
                        (<?= htmlspecialchars($character['class_name']) ?>)
                    </div>
                    <div class="activity-time">
                        Par <?= htmlspecialchars($character['username']) ?> - 
                        <?= date('d/m/Y H:i', strtotime($character['created_at'])) ?>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <p style="color: var(--text-muted); text-align: center; padding: 2rem;">
            Aucun personnage récent
        </p>
    <?php endif; ?>
</div>

<script>
    // Class Distribution Chart
    const classCtx = document.getElementById('classChart').getContext('2d');
    new Chart(classCtx, {
        type: 'doughnut',
        data: {
            labels: ['Guerrier', 'Mage', 'Voleur'],
            datasets: [{
                data: [12, 8, 5], // TODO: Get real data from backend
                backgroundColor: [
                    'rgba(99, 102, 241, 0.8)',
                    'rgba(168, 85, 247, 0.8)',
                    'rgba(236, 72, 153, 0.8)'
                ],
                borderColor: [
                    'rgba(99, 102, 241, 1)',
                    'rgba(168, 85, 247, 1)',
                    'rgba(236, 72, 153, 1)'
                ],
                borderWidth: 2
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        color: '#e2e8f0',
                        padding: 15
                    }
                }
            }
        }
    });
    
    // Activity Chart
    const activityCtx = document.getElementById('activityChart').getContext('2d');
    new Chart(activityCtx, {
        type: 'line',
        data: {
            labels: ['Lun', 'Mar', 'Mer', 'Jeu', 'Ven', 'Sam', 'Dim'],
            datasets: [{
                label: 'Nouveaux Personnages',
                data: [3, 5, 2, 8, 4, 6, 7], // TODO: Get real data from backend
                borderColor: 'rgba(99, 102, 241, 1)',
                backgroundColor: 'rgba(99, 102, 241, 0.1)',
                tension: 0.4,
                fill: true
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    labels: {
                        color: '#e2e8f0'
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        color: '#94a3b8'
                    },
                    grid: {
                        color: '#1e293b'
                    }
                },
                x: {
                    ticks: {
                        color: '#94a3b8'
                    },
                    grid: {
                        color: '#1e293b'
                    }
                }
            }
        }
    });
</script>

<?php
$content = ob_get_clean();
require_once __DIR__ . '/../layouts/admin.php';
?>
