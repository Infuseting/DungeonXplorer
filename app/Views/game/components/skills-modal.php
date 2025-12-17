<!-- Skills Modal -->
<div id="skills-modal" class="fixed inset-0 z-50 hidden">
    <!-- Backdrop -->
    <div id="skills-backdrop" class="absolute inset-0 bg-black/60 backdrop-blur-md transition-opacity"></div>
    
    <!-- Modal Content -->
    <div class="relative z-10 w-full h-full flex items-center justify-center p-4 lg:p-8 pointer-events-none">
        <div class="bg-gray-800/90 border border-gray-600 rounded-2xl shadow-2xl flex flex-col p-4 lg:p-6 max-w-6xl w-full h-[90vh] pointer-events-auto transform transition-all scale-100 overflow-hidden">
            
            <!-- Header -->
            <div class="flex items-center justify-between mb-4 border-b border-gray-700 pb-2">
                 <h2 class="text-xl lg:text-2xl font-bold text-white flex items-center gap-2">
                    <span class="material-symbols-outlined text-amber-500"></span>
                    Arbre de Compétences
                </h2>
                <div class="flex items-center gap-6">
                    <div class="text-right">
                        <p class="text-xs text-gray-400">Points Disponibles</p>
                        <p class="text-2xl font-bold text-amber-400" id="modal-sp-display"><?= $character->getSkillPoints() ?></p>
                    </div>
                    <button id="skills-close-btn" class="text-gray-400 hover:text-white text-3xl leading-none">&times;</button>
                </div>
            </div>

            <!-- Canvas Container -->
            <div class="relative w-full flex-grow bg-gray-900 border border-gray-700 rounded-lg overflow-hidden cursor-grab active:cursor-grabbing" id="skill-tree-container">
                 <!-- SVG Layer for Lines -->
                <svg id="skill-connections-layer" class="absolute inset-0 w-full h-full pointer-events-none transform-origin-0"></svg>
                
                <!-- Nodes Layer -->
                <div id="skill-nodes-layer" class="absolute inset-0 transform-origin-0"></div>

                <div class="absolute bottom-4 right-4 bg-gray-800/80 p-2 rounded text-xs text-gray-400 pointer-events-none select-none">
                    Molette pour zoomer • Glisser pour déplacer
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const modal = document.getElementById('skills-modal');
    const backdrop = document.getElementById('skills-backdrop');
    const closeBtn = document.getElementById('skills-close-btn');
    const toggleBtn = document.getElementById('skills-toggle');     
            const CHARACTER_LEVEL = <?= $character->getLevel() ?>;
    const SKILLS_DATA = <?= $playerSkillsJson ?? '[]' ?>; 

        const SkillTree = {
        container: document.getElementById('skill-tree-container'),
        nodesLayer: document.getElementById('skill-nodes-layer'),
        connectionsLayer: document.getElementById('skill-connections-layer'),
        spDisplay: document.getElementById('modal-sp-display'),
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
            this.pan = { x: 50, y: 50 };
            this.updateTransform();
        },

        open() {
            modal.classList.remove('hidden');
                    },

        close() {
            modal.classList.add('hidden');
        },

        render() {
            this.nodesLayer.innerHTML = '';
            this.connectionsLayer.innerHTML = '';

            SKILLS_DATA.forEach(skill => {
                this.renderNode(skill);
                if (skill.parent_skill_id) {
                    const parent = SKILLS_DATA.find(s => s.id == skill.parent_skill_id);
                    if (parent) this.renderConnection(parent, skill);
                }
            });
        },

        renderNode(skill) {
            const el = document.createElement('div');
            let classes = "absolute w-44 p-3 rounded-lg border-2 transition-all duration-300 select-none flex flex-col gap-1";
            let statusIcon = "";
            let clickAction = null;
            
            if (skill.status === 'unlocked') {
                classes += " bg-gray-800 border-amber-500 shadow-[0_0_15px_rgba(245,158,11,0.3)] z-20";
                statusIcon = `<span class="material-symbols-outlined text-amber-500">check_circle</span>`;
            } else if (skill.status === 'available') {
                classes += " bg-gray-800 border-gray-500 hover:border-amber-400 hover:scale-105 cursor-pointer z-20";
                statusIcon = `<span class="material-symbols-outlined text-green-400 animate-pulse">lock_open</span>`;
                clickAction = () => this.unlockSkill(skill.id, skill.name, skill.cost_sp);
            } else {
                classes += " bg-gray-900 border-gray-800 text-gray-600 opacity-80 z-10 grayscale";
                statusIcon = `<span class="material-symbols-outlined text-gray-700">lock</span>`;
            }

            el.className = classes;
            el.style.left = `${skill.node_x}px`;
            el.style.top = `${skill.node_y}px`;
            if(clickAction) el.onclick = clickAction;

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
            const startX = parseInt(parent.node_x) + 88;
            const startY = parseInt(parent.node_y) + 80;
            const endX = parseInt(child.node_x) + 88;
            const endY = parseInt(child.node_y);

            const path = document.createElementNS('http://www.w3.org/2000/svg', 'path');
            const d = `M ${startX} ${startY} C ${startX} ${startY + 50}, ${endX} ${endY - 50}, ${endX} ${endY}`;
            
            path.setAttribute('d', d);
            let stroke = "#374151"; 
            if (child.status === 'unlocked') stroke = "#d97706";
            else if (child.status === 'available') stroke = "#9ca3af";

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
                                                if (modal.classList.contains('hidden')) return;
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
                if(data.success) {
                                        location.reload(); 
                }
                else alert('Erreur: ' + data.message);
            });
        }
    };

        SkillTree.init();

        if (toggleBtn) {
        toggleBtn.addEventListener('click', (e) => {
            e.preventDefault();             SkillTree.open();
        });
    }
    closeBtn.addEventListener('click', () => SkillTree.close());
    backdrop.addEventListener('click', () => SkillTree.close());
    
        document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape' && !modal.classList.contains('hidden')) {
            SkillTree.close();
        }
    });
});
</script>
