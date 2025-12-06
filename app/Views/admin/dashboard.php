<?php
$pageTitle = 'Dashboard';
ob_start();
?>



<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    <div class="bg-gradient-to-br from-gray-900 to-indigo-500/10 border border-gray-700 rounded-xl p-6 relative overflow-hidden before:content-[''] before:absolute before:top-0 before:left-0 before:w-1 before:h-full before:bg-gradient-to-b before:from-indigo-500 before:to-purple-500">
        <div class="absolute top-4 right-4 text-4xl opacity-20">👥</div>
        <div class="text-gray-400 text-sm mb-2">Total Utilisateurs</div>
        <div class="text-4xl font-bold text-transparent bg-clip-text bg-gradient-to-br from-indigo-500 to-purple-500"><?= $stats['total_users'] ?? 0 ?></div>
    </div>
    
    <div class="bg-gradient-to-br from-gray-900 to-indigo-500/10 border border-gray-700 rounded-xl p-6 relative overflow-hidden before:content-[''] before:absolute before:top-0 before:left-0 before:w-1 before:h-full before:bg-gradient-to-b before:from-indigo-500 before:to-purple-500">
        <div class="absolute top-4 right-4 text-4xl opacity-20">⚔️</div>
        <div class="text-gray-400 text-sm mb-2">Total Personnages</div>
        <div class="text-4xl font-bold text-transparent bg-clip-text bg-gradient-to-br from-indigo-500 to-purple-500"><?= $stats['total_characters'] ?? 0 ?></div>
    </div>
    
    <div class="bg-gradient-to-br from-gray-900 to-indigo-500/10 border border-gray-700 rounded-xl p-6 relative overflow-hidden before:content-[''] before:absolute before:top-0 before:left-0 before:w-1 before:h-full before:bg-gradient-to-b before:from-indigo-500 before:to-purple-500">
        <div class="absolute top-4 right-4 text-4xl opacity-20">🗺️</div>
        <div class="text-gray-400 text-sm mb-2">Total Cartes</div>
        <div class="text-4xl font-bold text-transparent bg-clip-text bg-gradient-to-br from-indigo-500 to-purple-500"><?= $stats['total_maps'] ?? 0 ?></div>
    </div>
    
    <div class="bg-gradient-to-br from-gray-900 to-indigo-500/10 border border-gray-700 rounded-xl p-6 relative overflow-hidden before:content-[''] before:absolute before:top-0 before:left-0 before:w-1 before:h-full before:bg-gradient-to-b before:from-indigo-500 before:to-purple-500">
        <div class="absolute top-4 right-4 text-4xl opacity-20">📜</div>
        <div class="text-gray-400 text-sm mb-2">Total Quêtes</div>
        <div class="text-4xl font-bold text-transparent bg-clip-text bg-gradient-to-br from-indigo-500 to-purple-500"><?= $stats['total_quests'] ?? 0 ?></div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
    <div class="bg-gray-900 border border-gray-700 rounded-xl p-6">
        <h3 class="text-lg font-semibold mb-4">Personnages par Classe</h3>
        <canvas id="classChart"></canvas>
    </div>
    
    <div class="bg-gray-900 border border-gray-700 rounded-xl p-6">
        <h3 class="text-lg font-semibold mb-4">Activité des 7 derniers jours</h3>
        <canvas id="activityChart"></canvas>
    </div>
</div>

<div class="bg-gray-900 border border-gray-700 rounded-xl p-6">
    <h3 class="text-lg font-semibold mb-4">Personnages Récents</h3>
    <?php if (!empty($recentCharacters)): ?>
        <?php foreach ($recentCharacters as $character): ?>
            <div class="flex items-center p-4 border-b border-gray-700 last:border-0">
                <div class="w-10 h-10 rounded-full bg-indigo-500/20 flex items-center justify-center mr-4">⚔️</div>
                <div class="flex-1">
                    <div class="font-medium mb-1">
                        <?= htmlspecialchars($character['name']) ?> 
                        (<?= htmlspecialchars($character['class_name']) ?>)
                    </div>
                    <div class="text-gray-400 text-sm">
                        Par <?= htmlspecialchars($character['username']) ?> - 
                        <?= date('d/m/Y H:i', strtotime($character['created_at'])) ?>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <p class="text-gray-400 text-center py-8">
            Aucun personnage récent
        </p>
    <?php endif; ?>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    fetch('/admin/stats')
        .then(res => res.json())
        .then(data => {
            if (!data.success) {
                console.error('Failed to fetch dashboard stats');
                return;
            }

            // Class Distribution Chart
            const classLabels = data.class_distribution.map(c => c.name);
            const classData = data.class_distribution.map(c => c.count);
            const classCtx = document.getElementById('classChart').getContext('2d');
            new Chart(classCtx, {
                type: 'doughnut',
                data: {
                    labels: classLabels,
                    datasets: [{
                        data: classData,
                        backgroundColor: [
                            'rgba(99, 102, 241, 0.8)',
                            'rgba(168, 85, 247, 0.8)',
                            'rgba(236, 72, 153, 0.8)',
                            'rgba(34, 197, 94, 0.8)',
                            'rgba(59, 130, 246, 0.8)'
                        ],
                        borderColor: [
                            'rgba(99, 102, 241, 1)',
                            'rgba(168, 85, 247, 1)',
                            'rgba(236, 72, 153, 1)',
                            'rgba(34, 197, 94, 1)',
                            'rgba(59, 130, 246, 1)'
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

            // Activity Chart (last 7 days)
            const activityLabels = data.activity.map(a => {
                // Convert YYYY-MM-DD to localized short weekday
                const d = new Date(a.date + 'T00:00:00');
                return d.toLocaleDateString('fr-FR', { weekday: 'short' });
            });
            const activityData = data.activity.map(a => a.count);
            const activityCtx = document.getElementById('activityChart').getContext('2d');
            new Chart(activityCtx, {
                type: 'line',
                data: {
                    labels: activityLabels,
                    datasets: [{
                        label: 'Nouveaux Personnages',
                        data: activityData,
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
        })
        .catch(err => console.error('Error loading dashboard stats:', err));
});
</script>

<?php
$content = ob_get_clean();
require_once __DIR__ . '/../layouts/admin.php';
?>
