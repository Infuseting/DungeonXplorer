<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Arbre de Compétences</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@400;700&family=Lato:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" />
</head>
<body class="bg-gray-900 text-gray-100 font-lato min-h-screen flex flex-col">

    <!-- Navbar -->
    <nav class="bg-gray-800 border-b border-gray-700 p-4">
        <div class="container mx-auto flex justify-between items-center">
            <h1 class="text-2xl font-cinzel text-amber-500">Compétences - <?= htmlspecialchars($character->getName()) ?></h1>
            <a href="/game" class="text-gray-400 hover:text-white flex items-center gap-2">
                <span class="material-symbols-outlined">arrow_back</span> Retour
            </a>
        </div>
    </nav>

    <main class="flex-grow container mx-auto p-6">
        
        <!-- Header Info -->
        <div class="flex justify-between items-center mb-8 bg-gray-800 p-4 rounded-lg border border-gray-700">
            <div class="flex items-center gap-4">
                <div class="bg-gray-700 p-3 rounded-full">
                    <span class="material-symbols-outlined text-amber-500 text-3xl"></span>
                </div>
                <div>
                    <p class="text-sm text-gray-400">Points Disponibles</p>
                    <p class="text-3xl font-bold text-amber-400" id="sp-display"><?= $character->getSkillPoints() ?></p>
                </div>
            </div>
            
            <div class="text-right">
                <p class="text-sm text-gray-400">Niveau actuel</p>
                <p class="text-xl font-bold"><?= $character->getLevel() ?></p>
            </div>
        </div>


        <!-- Canvas Container -->
        <div class="relative w-full h-[600px] bg-gray-900 border border-gray-700 rounded-lg overflow-hidden cursor-grab active:cursor-grabbing" id="skill-tree-container">
             <!-- SVG Layer for Lines -->
            <svg id="connections-layer" class="absolute inset-0 w-full h-full pointer-events-none transform-origin-0"></svg>
            
            <!-- Nodes Layer -->
            <div id="nodes-layer" class="absolute inset-0 transform-origin-0"></div>

            <div class="absolute bottom-4 right-4 bg-gray-800/80 p-2 rounded text-xs text-gray-400">
                Molette pour zoomer • Glisser pour déplacer
            </div>
        </div>

    </main>

    <script>
        // Data populated from PHP
        const CHARACTER_LEVEL = <?= $character->getLevel() ?>;
        const SKILL_POINTS = <?= $character->getSkillPoints() ?>;
        const UNLOCKED_IDS = <?= json_encode($unlockedIds) ?>;
        
        // Prepare Skill Data with Status
        const SKILLS = <?= json_encode(array_map(function($s) use ($character, $unlockedIds) {
            $isUnlocked = in_array($s['id'], $unlockedIds);
            $canAfford = $character->getSkillPoints() >= $s['cost_sp'];
            $levelMet = $character->getLevel() >= $s['min_level'];
            $prereqMet = true;
            if ($s['parent_skill_id']) $prereqMet = in_array($s['parent_skill_id'], $unlockedIds);
            
            $s['status'] = 'locked';
            if ($isUnlocked) $s['status'] = 'unlocked';
            else if ($canAfford && $levelMet && $prereqMet) $s['status'] = 'available';
            
            return $s;
        }, $classSkills)) ?>;

        const tree = {
            container: document.getElementById('skill-tree-container'),
            nodesLayer: document.getElementById('nodes-layer'),
            connectionsLayer: document.getElementById('connections-layer'),
            pan: { x: 0, y: 0 },
            scale: 1,
            isDragging: false,
            dragStart: { x: 0, y: 0 },

            init() {
                this.render();
                this.setupEvents();
                this.centerView();
            },

            centerView() {
                // Find bounds or just center 0,0 ? Usually start nodes around 100,100
                // For now just consistent default
                this.pan = { x: 20, y: 20 };
                this.updateTransform();
            },

            render() {
                this.nodesLayer.innerHTML = '';
                this.connectionsLayer.innerHTML = '';

                SKILLS.forEach(skill => {
                    this.renderNode(skill);
                    if (skill.parent_skill_id) {
                        const parent = SKILLS.find(s => s.id == skill.parent_skill_id);
                        if (parent) this.renderConnection(parent, skill);
                    }
                });
            },

            renderNode(skill) {
                const el = document.createElement('div');
                // Base classes
                let classes = "absolute w-44 p-3 rounded-lg border-2 transition-all duration-300 select-none flex flex-col gap-1";
                let statusIcon = "";
                
                if (skill.status === 'unlocked') {
                    classes += " bg-gray-800 border-amber-500 shadow-[0_0_15px_rgba(245,158,11,0.3)] z-20";
                    statusIcon = `<span class="material-symbols-outlined text-amber-500">check_circle</span>`;
                } else if (skill.status === 'available') {
                    classes += " bg-gray-800 border-gray-500 hover:border-amber-400 hover:scale-105 cursor-pointer z-20";
                    statusIcon = `<span class="material-symbols-outlined text-green-400 animate-pulse">lock_open</span>`;
                    
                    el.onclick = () => this.unlockSkill(skill.id, skill.name, skill.cost_sp);
                } else {
                    classes += " bg-gray-900 border-gray-800 text-gray-600 opacity-80 z-10 grayscale";
                    statusIcon = `<span class="material-symbols-outlined text-gray-700">lock</span>`;
                }

                el.className = classes;
                el.style.left = `${skill.node_x}px`;
                el.style.top = `${skill.node_y}px`;

                el.innerHTML = `
                    <div class="flex justify-between items-start">
                        <div class="font-bold text-sm ${skill.status === 'available' ? 'text-amber-100' : ''}">${skill.name}</div>
                        ${statusIcon}
                    </div>
                    <div class="text-[10px] uppercase font-bold tracking-wide ${skill.type==='passive'?'text-blue-400':'text-red-400'}">${skill.type}</div>
                    <div class="text-xs mt-1 text-gray-400 leading-tight line-clamp-2" title="${skill.description}">${skill.description}</div>
                    <div class="mt-2 text-xs font-mono flex gap-2">
                        <span class="${skill.status==='available'?'text-green-400':''}">${skill.cost_sp} SP</span>
                        ${skill.min_level > CHARACTER_LEVEL ? '<span class="text-red-500">Lvl '+skill.min_level+'</span>' : '<span class="opacity-50">Lvl '+skill.min_level+'</span>'}
                    </div>
                `;

                this.nodesLayer.appendChild(el);
            },

            renderConnection(parent, child) {
                const startX = parseInt(parent.node_x) + 88; // Center X
                const startY = parseInt(parent.node_y) + 80; // Bottom (approx)
                const endX = parseInt(child.node_x) + 88;   // Center X
                const endY = parseInt(child.node_y);        // Top

                const path = document.createElementNS('http://www.w3.org/2000/svg', 'path');
                // Curvy connection
                const d = `M ${startX} ${startY} C ${startX} ${startY + 50}, ${endX} ${endY - 50}, ${endX} ${endY}`;
                
                path.setAttribute('d', d);
                // Style based on child status (if child is unlocked/available, line is brighter)
                let stroke = "#374151"; // gray-700
                if (child.status === 'unlocked') stroke = "#d97706"; // amber-600
                else if (child.status === 'available') stroke = "#9ca3af"; // gray-400

                path.setAttribute('stroke', stroke);
                path.setAttribute('stroke-width', '2');
                path.setAttribute('fill', 'none');
                
                this.connectionsLayer.appendChild(path);
            },

            setupEvents() {
                this.container.addEventListener('mousedown', e => {
                    this.isDragging = true;
                    this.dragStart = { x: e.clientX - this.pan.x, y: e.clientY - this.pan.y };
                });
                
                window.addEventListener('mousemove', e => {
                    if (!this.isDragging) return;
                    this.pan.x = e.clientX - this.dragStart.x;
                    this.pan.y = e.clientY - this.dragStart.y;
                    this.updateTransform();
                });

                window.addEventListener('mouseup', () => this.isDragging = false);

                this.container.addEventListener('wheel', e => {
                    e.preventDefault();
                    const delta = e.deltaY > 0 ? 0.9 : 1.1;
                    this.scale = Math.min(Math.max(0.5, this.scale * delta), 2);
                    this.updateTransform();
                });
            },

            updateTransform() {
                const t = `translate(${this.pan.x}px, ${this.pan.y}px) scale(${this.scale})`;
                this.nodesLayer.style.transform = t;
                this.connectionsLayer.style.transform = t;
            },

            unlockSkill(id, name, cost) {
                if(!confirm(`Débloquer ${name} pour ${cost} SP ?`)) return;
                
                fetch('/game/skills/unlock', {
                    method: 'POST',
                    headers: {'Content-Type': 'application/json'},
                    body: JSON.stringify({ skill_id: id })
                })
                .then(res => res.json())
                .then(data => {
                    if(data.success) location.reload();
                    else alert('Erreur: ' + data.message);
                });
            }
        };

        // Init on load
        tree.init();
</body>
</html>
