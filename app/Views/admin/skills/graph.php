<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Éditeur d'Arbre de Compétences</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        .skill-node {
            position: absolute;
            width: 180px;
            background: #1f2937;
            border: 2px solid #374151;
            border-radius: 8px;
            padding: 10px;
            cursor: move;
            user-select: none;
            z-index: 10;
        }
        .skill-node.selected {
            border-color: #3b82f6;
            box-shadow: 0 0 10px rgba(59, 130, 246, 0.5);
        }
        .connection {
            fill: none;
            stroke: #4b5563;
            stroke-width: 2;
        }
    </style>
</head>
<body class="bg-gray-900 text-white h-screen flex flex-col overflow-hidden">

    <div class="bg-gray-800 p-4 border-b border-gray-700 flex justify-between items-center">
        <h1 class="text-xl font-bold">Éditeur de Compétences</h1>
        <div>
            <select id="class-select" class="bg-gray-700 border border-gray-600 rounded px-2 py-1 mr-4" onchange="window.location.href='?class_id='+this.value">
                <?php foreach ($classes as $c): ?>
                    <option value="<?= $c['id'] ?>" <?= $classId == $c['id'] ? 'selected' : '' ?>><?= htmlspecialchars($c['name']) ?></option>
                <?php endforeach; ?>
            </select>
            <a href="/admin" class="text-gray-400 hover:text-white">Retour Admin</a>
        </div>
    </div>

    <div class="flex-1 relative overflow-hidden bg-gray-900" id="editor-container">
        <!-- SVG Layer for Lines -->
        <svg id="connections-layer" class="absolute inset-0 w-full h-full pointer-events-none"></svg>
        
        <!-- Nodes Layer -->
        <div id="nodes-layer" class="absolute inset-0">
            <!-- Nodes rendered by JS -->
        </div>
    </div>

    <script>
        const SKILLS = <?= json_encode($skills) ?>;

        const editor = {
            container: document.getElementById('editor-container'),
            nodesLayer: document.getElementById('nodes-layer'),
            connectionsLayer: document.getElementById('connections-layer'),
            pan: { x: 0, y: 0 },
            isDragging: false,
            dragStart: { x: 0, y: 0 },

            init() {
                this.render();
                this.setupEvents();
            },

            render() {
                this.nodesLayer.innerHTML = '';
                this.connectionsLayer.innerHTML = '';

                // Render Nodes
                SKILLS.forEach(skill => {
                    const el = document.createElement('div');
                    el.className = 'skill-node';
                    el.style.left = `${skill.node_x}px`;
                    el.style.top = `${skill.node_y}px`;
                    el.innerHTML = `
                        <div class="font-bold text-sm text-indigo-300">${skill.name}</div>
                        <div class="text-xs text-gray-400">Lvl ${skill.min_level}</div>
                        <div class="text-xs text-gray-500 truncate">${skill.type}</div>
                    `;
                    
                    // Drag logic
                    el.addEventListener('mousedown', (e) => {
                        e.stopPropagation();
                        this.startDrag(originalEvent = e, skill, el);
                    });

                    this.nodesLayer.appendChild(el);

                    // Render Connections (Lines to parents)
                    if (skill.parent_skill_id) {
                        const parent = SKILLS.find(s => s.id == skill.parent_skill_id);
                        if (parent) {
                            this.drawConnection(parent, skill);
                        }
                    }
                });
            },

            drawConnection(parent, child) {
                const startX = parseInt(parent.node_x) + 90; // Center X (approx width/2)
                const startY = parseInt(parent.node_y) + 60; // Bottom (approx height)
                const endX = parseInt(child.node_x) + 90;   // Center X
                const endY = parseInt(child.node_y);        // Top

                const path = document.createElementNS('http://www.w3.org/2000/svg', 'path');
                // Curvy line
                const d = `M ${startX} ${startY} C ${startX} ${startY + 50}, ${endX} ${endY - 50}, ${endX} ${endY}`;
                
                path.setAttribute('d', d);
                path.setAttribute('class', 'connection');
                this.connectionsLayer.appendChild(path);
            },

            startDrag(e, skill, el) {
                const startX = e.clientX;
                const startY = e.clientY;
                const startNodeX = parseInt(skill.node_x);
                const startNodeY = parseInt(skill.node_y);

                const moveHandler = (ev) => {
                    const dx = ev.clientX - startX;
                    const dy = ev.clientY - startY;
                    skill.node_x = startNodeX + dx;
                    skill.node_y = startNodeY + dy;
                    el.style.left = `${skill.node_x}px`;
                    el.style.top = `${skill.node_y}px`;
                    this.render(); // Re-render lines
                };

                const upHandler = () => {
                    window.removeEventListener('mousemove', moveHandler);
                    window.removeEventListener('mouseup', upHandler);
                    this.savePosition(skill);
                };

                window.addEventListener('mousemove', moveHandler);
                window.addEventListener('mouseup', upHandler);
            },

            savePosition(skill) {
                const formData = new FormData();
                formData.append('id', skill.id);
                formData.append('node_x', skill.node_x);
                formData.append('node_y', skill.node_y);

                fetch('/admin/skills/update-position', {
                    method: 'POST',
                    body: formData
                })
                .then(res => res.json())
                .then(data => {
                    if(!data.success) alert('Erreur de sauvegarde');
                });
            },

            setupEvents() {
                // Panning (optional, simplified for now)
            }
        };

        editor.init();
    </script>
</body>
</html>
