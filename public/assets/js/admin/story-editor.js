const storyEditor = {
    nodes: [],
    connections: [],
    selectedNode: null,
    selectedConnection: null,
    isDragging: false,
    dragStart: { x: 0, y: 0 },
    nodeDragOffset: { x: 0, y: 0 },
    scale: 1,
    pan: { x: 0, y: 0 },

    // DOM Elements
    container: null,
    nodesLayer: null,
    connectionsLayer: null,
    propertiesPanel: null,
    propertiesContent: null,

    init() {
        this.container = document.getElementById('editor-container');
        this.nodesLayer = document.getElementById('nodes-layer');
        this.connectionsLayer = document.getElementById('connections-layer');
        this.propertiesPanel = document.getElementById('properties-panel');
        this.propertiesContent = document.getElementById('properties-content');

        // Load initial data
        this.nodes = INITIAL_NODES || [];
        this.nodes.forEach(node => {
            // Ensure coordinates exist
            node.node_x = parseInt(node.node_x) || 100;
            node.node_y = parseInt(node.node_y) || 100;

            // Extract connections
            if (node.connections) {
                node.connections.forEach(conn => {
                    this.connections.push({
                        id: conn.id,
                        from: node.id,
                        to: conn.to_node_id,
                        data: conn
                    });
                });
            }
        });

        this.render();
        this.setupEventListeners();
    },

    setupEventListeners() {
        // Panning
        this.container.addEventListener('mousedown', (e) => {
            if (e.target === this.container || e.target === this.nodesLayer || e.target.id === 'canvas-grid') {
                this.isDragging = true;
                this.dragStart = { x: e.clientX - this.pan.x, y: e.clientY - this.pan.y };
                this.deselectAll();
            }
        });

        window.addEventListener('mousemove', (e) => {
            if (this.isDragging) {
                this.pan.x = e.clientX - this.dragStart.x;
                this.pan.y = e.clientY - this.dragStart.y;
                this.updateTransform();
            }
        });

        window.addEventListener('mouseup', () => {
            this.isDragging = false;
        });

        // Zoom
        this.container.addEventListener('wheel', (e) => {
            e.preventDefault();
            const delta = e.deltaY > 0 ? 0.9 : 1.1;
            this.scale = Math.min(Math.max(0.5, this.scale * delta), 2);
            this.updateTransform();
        });
    },

    updateTransform() {
        const transform = `translate(${this.pan.x}px, ${this.pan.y}px) scale(${this.scale})`;
        this.nodesLayer.style.transform = transform;
        // SVG transform is handled differently or we just transform the container content
        // Actually for SVG lines to line up with HTML nodes, they need same transform
        // But SVG inside HTML container inherits transform if applied to container?
        // No, let's apply to a group inside SVG or just the SVG itself if it's absolute
        // Wait, nodesLayer and connectionsLayer are siblings.
        // Let's wrap them in a 'world' div? Or apply to both.
        this.connectionsLayer.style.transform = transform;
        // Need to set transform-origin to 0 0
        this.nodesLayer.style.transformOrigin = '0 0';
        this.connectionsLayer.style.transformOrigin = '0 0';
    },

    render() {
        // Clear layers
        this.nodesLayer.innerHTML = '';
        this.connectionsLayer.innerHTML = '';

        // Render Nodes
        this.nodes.forEach(node => {
            const el = document.createElement('div');
            el.className = `story-node ${node.is_start_node ? 'start-node' : ''} ${node.is_end_node ? 'end-node' : ''}`;
            if (this.selectedNode && this.selectedNode.id === node.id) el.classList.add('selected');

            el.style.left = `${node.node_x}px`;
            el.style.top = `${node.node_y}px`;

            el.innerHTML = `
                <div class="node-header">${this.escapeHtml(node.name)}</div>
                <div class="node-preview">${this.escapeHtml(node.description)}</div>
                <div class="node-handle handle-in" data-node-id="${node.id}"></div>
                <div class="node-handle handle-out" data-node-id="${node.id}"></div>
            `;

            // Setup handle interactions for creating connections
            const handleOut = el.querySelector('.handle-out');
            const handleIn = el.querySelector('.handle-in');

            handleOut.addEventListener('mousedown', (e) => {
                e.stopPropagation();
                this.startConnectionDrag(node, e);
            });

            // Node interaction
            el.addEventListener('mousedown', (e) => {
                e.stopPropagation(); // Prevent panning

                // Select without re-rendering
                this.selectedNode = node;
                this.selectedConnection = null;
                this.showNodeProperties(node);

                // Dragging logic for node
                const startX = e.clientX;
                const startY = e.clientY;
                const startNodeX = node.node_x;
                const startNodeY = node.node_y;

                let hasMoved = false;
                this.isDragging = false; // Prevent canvas panning during node drag

                const moveHandler = (ev) => {
                    if (!hasMoved) {
                        // Hide connections on first move for instant performance
                        this.connectionsLayer.style.display = 'none';
                        hasMoved = true;
                    }

                    const dx = (ev.clientX - startX) / this.scale;
                    const dy = (ev.clientY - startY) / this.scale;
                    node.node_x = startNodeX + dx;
                    node.node_y = startNodeY + dy;

                    // Update position immediately
                    el.style.left = `${node.node_x}px`;
                    el.style.top = `${node.node_y}px`;
                };

                const upHandler = () => {
                    window.removeEventListener('mousemove', moveHandler);
                    window.removeEventListener('mouseup', upHandler);

                    // Show and re-render connections after drag
                    if (hasMoved) {
                        this.connectionsLayer.style.display = '';
                        this.renderConnections();

                        // Auto-save position to database
                        const formData = new FormData();
                        formData.append('id', node.id);
                        formData.append('name', node.name);
                        formData.append('description', node.description);
                        formData.append('image_path', node.image_path || '');
                        formData.append('node_x', Math.round(node.node_x));
                        formData.append('node_y', Math.round(node.node_y));
                        if (node.is_start_node) formData.append('is_start_node', 'on');
                        if (node.is_end_node) formData.append('is_end_node', 'on');

                        fetch(`/admin/stories/nodes/${node.id}/edit`, {
                            method: 'POST',
                            body: formData
                        }).catch(err => console.error('Failed to save position:', err));
                    }
                };

                window.addEventListener('mousemove', moveHandler);
                window.addEventListener('mouseup', upHandler);
            });

            this.nodesLayer.appendChild(el);
        });

        this.renderConnections();
    },

    renderConnections() {
        this.connectionsLayer.innerHTML = ''; // Clear lines

        this.connections.forEach(conn => {
            const fromNode = this.nodes.find(n => n.id == conn.from);
            const toNode = this.nodes.find(n => n.id == conn.to);

            if (!fromNode || !toNode) return;

            // Calculate path
            const startX = fromNode.node_x + 180; // Right side
            const startY = fromNode.node_y + 40;  // Center height approx
            const endX = toNode.node_x;           // Left side
            const endY = toNode.node_y + 40;

            // Curvy line (Bezier)
            const cp1x = startX + 50;
            const cp1y = startY;
            const cp2x = endX - 50;
            const cp2y = endY;

            const d = `M ${startX} ${startY} C ${cp1x} ${cp1y}, ${cp2x} ${cp2y}, ${endX} ${endY}`;

            const path = document.createElementNS('http://www.w3.org/2000/svg', 'path');
            path.setAttribute('d', d);
            path.setAttribute('class', 'connection');
            if (this.selectedConnection && this.selectedConnection.id === conn.id) {
                path.classList.add('selected');
            }

            path.addEventListener('click', (e) => {
                e.stopPropagation();
                this.selectConnection(conn);
            });

            this.connectionsLayer.appendChild(path);
        });
    },

    selectNode(node) {
        this.selectedNode = node;
        this.selectedConnection = null;
        this.render(); // Re-render to show selection state
        this.showNodeProperties(node);
    },

    selectConnection(conn) {
        this.selectedConnection = conn;
        this.selectedNode = null;
        this.render();
        this.showConnectionProperties(conn);
    },

    deselectAll() {
        this.selectedNode = null;
        this.selectedConnection = null;
        this.render();
        this.closePanel();
    },

    showNodeProperties(node) {
        this.propertiesPanel.classList.remove('hidden');
        this.propertiesContent.innerHTML = `
            <form id="node-form" onsubmit="storyEditor.updateNode(event)">
                <input type="hidden" name="id" value="${node.id}">
                <div class="mb-3">
                    <label class="block text-sm text-gray-400 mb-1">Nom</label>
                    <input type="text" name="name" value="${this.escapeHtml(node.name)}" class="w-full bg-gray-900 border border-gray-700 rounded px-2 py-1 text-white">
                </div>
                <div class="mb-3">
                    <label class="block text-sm text-gray-400 mb-1">Description</label>
                    <textarea name="description" rows="3" class="w-full bg-gray-900 border border-gray-700 rounded px-2 py-1 text-white">${this.escapeHtml(node.description)}</textarea>
                </div>
                <div class="mb-3">
                    <label class="block text-sm text-gray-400 mb-1">Image Path</label>
                    <input type="text" name="image_path" id="image_path_input" value="${this.escapeHtml(node.image_path || '')}" readonly class="w-full bg-gray-800 border border-gray-700 rounded px-2 py-1 text-gray-400 cursor-not-allowed mb-2">
                    <div class="flex gap-2">
                        <label class="flex-1 bg-indigo-600 hover:bg-indigo-700 text-white px-3 py-1.5 rounded text-sm font-medium text-center cursor-pointer transition-colors">
                            📤 Upload Image
                            <input type="file" id="image_upload" accept="image/*" class="hidden" onchange="storyEditor.uploadImage(event)">
                        </label>
                        ${node.image_path ? `<button type="button" onclick="storyEditor.clearImage()" class="bg-red-600 hover:bg-red-700 text-white px-3 py-1.5 rounded text-sm">✕</button>` : ''}
                    </div>
                    ${node.image_path ? `<img src="${node.image_path}" class="mt-2 w-full rounded border border-gray-700" alt="Preview">` : ''}
                </div>
                <div class="flex gap-4 mb-4">
                    <label class="flex items-center gap-2 text-sm text-gray-300">
                        <input type="checkbox" name="is_start_node" ${node.is_start_node ? 'checked' : ''}> Départ
                    </label>
                    <label class="flex items-center gap-2 text-sm text-gray-300">
                        <input type="checkbox" name="is_end_node" ${node.is_end_node ? 'checked' : ''}> Fin
                    </label>
                    <label class="flex items-center gap-2 text-sm text-gray-300">
                        <input type="checkbox" name="can_exit" ${node.can_exit ? 'checked' : ''}> Sortie Possible
                    </label>
                </div>
                <div class="flex justify-between">
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-3 py-1 rounded text-sm">Mettre à jour</button>
                    <button type="button" onclick="storyEditor.deleteNode(${node.id})" class="text-red-400 hover:text-red-300 text-sm">Supprimer</button>
                </div>
            </form>
            
            <div class="mt-6 border-t border-gray-700 pt-4">
                <h3 class="font-bold text-gray-300 mb-2">Contenu</h3>
                <div class="space-y-2">
                    <button class="w-full text-left px-3 py-2 bg-gray-750 hover:bg-gray-700 rounded text-sm text-gray-300">
                        👾 Monstres (${node.monsters ? node.monsters.length : 0})
                    </button>
                    <button class="w-full text-left px-3 py-2 bg-gray-750 hover:bg-gray-700 rounded text-sm text-gray-300">
                        👤 PNJs (${node.npcs ? node.npcs.length : 0})
                    </button>
                    <button class="w-full text-left px-3 py-2 bg-gray-750 hover:bg-gray-700 rounded text-sm text-gray-300">
                        💎 Loots (${node.loots ? node.loots.length : 0})
                    </button>
                </div>
            </div>
        `;
    },

    showConnectionProperties(conn) {
        this.propertiesPanel.classList.remove('hidden');

        const fromNode = this.nodes.find(n => n.id == conn.from);
        const toNode = this.nodes.find(n => n.id == conn.to);

        this.propertiesContent.innerHTML = `
            <form id="connection-form" onsubmit="storyEditor.updateConnection(event)">
                <input type="hidden" name="id" value="${conn.id}">
                
                <div class="mb-4 p-3 bg-gray-750 rounded">
                    <div class="text-sm text-gray-400">De: <span class="text-white font-medium">${this.escapeHtml(fromNode?.name || 'N/A')}</span></div>
                    <div class="text-sm text-gray-400">Vers: <span class="text-white font-medium">${this.escapeHtml(toNode?.name || 'N/A')}</span></div>
                </div>

                <h3 class="font-bold text-gray-300 mb-3">Direction Aller</h3>
                
                <div class="mb-3">
                    <label class="block text-sm text-gray-400 mb-1">Texte affiché (ex: "Nord", "Entrer")</label>
                    <input type="text" name="direction_text" value="${this.escapeHtml(conn.data?.direction_text || '')}" 
                           class="w-full bg-gray-900 border border-gray-700 rounded px-2 py-1 text-white"
                           placeholder="Nord, Sud, Entrer...">
                </div>

                <div class="mb-3">
                    <label class="block text-sm text-gray-400 mb-1">Condition d'affichage</label>
                    <select name="condition_type" class="w-full bg-gray-900 border border-gray-700 rounded px-2 py-1 text-white mb-2" onchange="storyEditor.toggleConditionValue(this)">
                        <option value="none" ${conn.data?.condition_type === 'none' ? 'selected' : ''}>Aucune</option>
                        <option value="item" ${conn.data?.condition_type === 'item' ? 'selected' : ''}>Posséder un item</option>
                        <option value="level" ${conn.data?.condition_type === 'level' ? 'selected' : ''}>Niveau minimum</option>
                        <option value="quest_active" ${conn.data?.condition_type === 'quest_active' ? 'selected' : ''}>Quête active</option>
                        <option value="quest_completed" ${conn.data?.condition_type === 'quest_completed' ? 'selected' : ''}>Quête complétée</option>
                        <option value="quest_stage" ${conn.data?.condition_type === 'quest_stage' ? 'selected' : ''}>Stage de quête spécifique</option>
                        <option value="monster_killed" ${conn.data?.condition_type === 'monster_killed' ? 'selected' : ''}>Monstres tués dans la salle</option>
                    </select>
                    <input type="text" name="condition_value" id="condition_value" value="${this.escapeHtml(conn.data?.condition_value || '')}" 
                           class="w-full bg-gray-900 border border-gray-700 rounded px-2 py-1 text-white ${conn.data?.condition_type === 'none' || conn.data?.condition_type === 'monster_killed' ? 'hidden' : ''}"
                           placeholder="ID de l'item, niveau, ID de quête...">
                </div>

                <hr class="border-gray-700 my-4">

                <h3 class="font-bold text-gray-300 mb-3">Direction Retour</h3>

                <div class="mb-3">
                    <label class="flex items-center gap-2 text-sm text-gray-300 mb-2">
                        <input type="checkbox" name="allow_return" ${conn.data?.allow_return ? 'checked' : ''} 
                               onchange="storyEditor.toggleReturnOptions(this)"
                               class="rounded bg-gray-900 border-gray-700 text-indigo-600">
                        Permettre le retour en arrière
                    </label>
                </div>

                <div id="return-options" class="${conn.data?.allow_return ? '' : 'hidden'}">
                    <div class="mb-3">
                        <label class="block text-sm text-gray-400 mb-1">Texte de retour (ex: "Revenir", "Sud")</label>
                        <input type="text" name="return_text" value="${this.escapeHtml(conn.data?.return_text || '')}" 
                               class="w-full bg-gray-900 border border-gray-700 rounded px-2 py-1 text-white"
                               placeholder="Revenir, Sortir...">
                    </div>

                    <div class="mb-3">
                        <label class="block text-sm text-gray-400 mb-1">Condition de retour</label>
                        <select name="return_condition_type" class="w-full bg-gray-900 border border-gray-700 rounded px-2 py-1 text-white mb-2" onchange="storyEditor.toggleReturnConditionValue(this)">
                            <option value="none" ${conn.data?.return_condition_type === 'none' ? 'selected' : ''}>Aucune</option>
                            <option value="item" ${conn.data?.return_condition_type === 'item' ? 'selected' : ''}>Posséder un item</option>
                            <option value="level" ${conn.data?.return_condition_type === 'level' ? 'selected' : ''}>Niveau minimum</option>
                            <option value="quest_active" ${conn.data?.return_condition_type === 'quest_active' ? 'selected' : ''}>Quête active</option>
                            <option value="quest_completed" ${conn.data?.return_condition_type === 'quest_completed' ? 'selected' : ''}>Quête complétée</option>
                            <option value="quest_stage" ${conn.data?.return_condition_type === 'quest_stage' ? 'selected' : ''}>Stage de quête spécifique</option>
                            <option value="monster_killed" ${conn.data?.return_condition_type === 'monster_killed' ? 'selected' : ''}>Monstres tués dans la salle</option>
                        </select>
                        <input type="text" name="return_condition_value" id="return_condition_value" value="${this.escapeHtml(conn.data?.return_condition_value || '')}" 
                               class="w-full bg-gray-900 border border-gray-700 rounded px-2 py-1 text-white ${conn.data?.return_condition_type === 'none' || conn.data?.return_condition_type === 'monster_killed' ? 'hidden' : ''}"
                               placeholder="ID de l'item, niveau, ID de quête...">
                    </div>
                </div>

                <div class="flex justify-between mt-4">
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-3 py-1 rounded text-sm">Mettre à jour</button>
                    <button type="button" onclick="storyEditor.deleteConnection(${conn.id})" class="text-red-400 hover:text-red-300 text-sm">Supprimer</button>
                </div>
            </form>
        `;
    },

    toggleConditionValue(select) {
        const input = document.getElementById('condition_value');
        if (select.value === 'none' || select.value === 'monster_killed') {
            input.classList.add('hidden');
        } else {
            input.classList.remove('hidden');
        }
    },

    toggleReturnConditionValue(select) {
        const input = document.getElementById('return_condition_value');
        if (select.value === 'none' || select.value === 'monster_killed') {
            input.classList.add('hidden');
        } else {
            input.classList.remove('hidden');
        }
    },

    toggleReturnOptions(checkbox) {
        const returnOptions = document.getElementById('return-options');
        if (checkbox.checked) {
            returnOptions.classList.remove('hidden');
        } else {
            returnOptions.classList.add('hidden');
        }
    },

    closePanel() {
        this.propertiesPanel.classList.add('hidden');
    },

    addNode() {
        // Create new node via AJAX
        const name = prompt("Nom du nouveau nœud :");
        if (!name) return;

        const formData = new FormData();
        formData.append('name', name);
        formData.append('description', 'Nouvelle pièce');
        formData.append('node_x', 100 - this.pan.x); // Center relative to view?
        formData.append('node_y', 100 - this.pan.y);

        fetch(`/admin/stories/${STORY_ID}/nodes/create`, {
            method: 'POST',
            body: formData
        })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    // Reload page or add to local state
                    location.reload(); // Simplest for now
                }
            });
    },

    updateNode(e) {
        e.preventDefault();
        const form = e.target;
        const formData = new FormData(form);
        const id = formData.get('id');

        fetch(`/admin/stories/nodes/${id}/edit`, {
            method: 'POST',
            body: formData
        })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    // Update local model
                    const node = this.nodes.find(n => n.id == id);
                    if (node) {
                        node.name = formData.get('name');
                        node.description = formData.get('description');
                        node.image_path = formData.get('image_path');
                        node.is_start_node = formData.get('is_start_node') === 'on';
                        node.is_end_node = formData.get('is_end_node') === 'on';
                        node.can_exit = formData.get('can_exit') === 'on';
                        this.render();
                    }
                }
            });
    },

    deleteNode(id) {
        if (!confirm('Supprimer ce nœud ?')) return;

        fetch(`/admin/stories/nodes/${id}/delete`, {
            method: 'POST'
        })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    this.nodes = this.nodes.filter(n => n.id != id);
                    this.connections = this.connections.filter(c => c.from != id && c.to != id);
                    this.deselectAll();
                    this.render();
                }
            });
    },

    saveLayout() {
        // Save all node positions
        // Ideally batch update, but for now loop? No, that's bad.
        // Let's just save the selected/moved ones or all.
        // For MVP, maybe just alert "Layout saved locally" as we update DB on drop?
        // Actually, I implemented updateNode on drop? No, I implemented drag visual only.
        // I need to save position on drag end.

        // Let's iterate and save all (inefficient but works for MVP)
        // Or better: add a 'save positions' endpoint that takes an array
        alert("Sauvegarde des positions non implémentée en batch. Utilisez le formulaire pour sauvegarder.");
    },

    async uploadImage(event) {
        const file = event.target.files[0];
        if (!file) return;

        const formData = new FormData();
        formData.append('image', file);

        try {
            const response = await fetch('/admin/stories/nodes/upload-image', {
                method: 'POST',
                body: formData
            });
            const data = await response.json();

            if (data.success) {
                // Update the input field
                const input = document.getElementById('image_path_input');
                if (input) {
                    input.value = data.path;
                }

                // Update the selected node
                if (this.selectedNode) {
                    this.selectedNode.image_path = data.path;
                    // Re-render properties to show preview
                    this.showNodeProperties(this.selectedNode);
                }
            } else {
                alert('Erreur: ' + (data.message || 'Upload échoué'));
            }
        } catch (error) {
            console.error('Upload error:', error);
            alert('Erreur lors de l\'upload');
        }
    },

    clearImage() {
        if (this.selectedNode) {
            this.selectedNode.image_path = '';
            const input = document.getElementById('image_path_input');
            if (input) {
                input.value = '';
            }
            // Re-render properties
            this.showNodeProperties(this.selectedNode);
        }
    },

    startConnectionDrag(fromNode, event) {
        event.stopPropagation();

        const startX = fromNode.node_x + 180; // Right side of node
        const startY = fromNode.node_y + 40;  // Center height

        // Create temporary drag line
        const dragLine = document.createElementNS('http://www.w3.org/2000/svg', 'path');
        dragLine.setAttribute('class', 'drag-line');
        this.connectionsLayer.appendChild(dragLine);

        const moveHandler = (e) => {
            // Get mouse position relative to canvas
            const rect = this.container.getBoundingClientRect();
            const mouseX = (e.clientX - rect.left - this.pan.x) / this.scale;
            const mouseY = (e.clientY - rect.top - this.pan.y) / this.scale;

            // Draw bezier curve from handle to mouse
            const cp1x = startX + 50;
            const cp1y = startY;
            const cp2x = mouseX - 50;
            const cp2y = mouseY;

            const d = `M ${startX} ${startY} C ${cp1x} ${cp1y}, ${cp2x} ${cp2y}, ${mouseX} ${mouseY}`;
            dragLine.setAttribute('d', d);
        };

        const upHandler = (e) => {
            window.removeEventListener('mousemove', moveHandler);
            window.removeEventListener('mouseup', upHandler);

            // Remove drag line
            dragLine.remove();

            // Check if released on a handle-in
            const target = document.elementFromPoint(e.clientX, e.clientY);
            if (target && target.classList.contains('handle-in')) {
                const toNodeId = parseInt(target.dataset.nodeId);
                const toNode = this.nodes.find(n => n.id === toNodeId);

                if (toNode && toNode.id !== fromNode.id) {
                    // Create connection
                    this.createConnection(fromNode.id, toNode.id);
                }
            }
        };

        window.addEventListener('mousemove', moveHandler);
        window.addEventListener('mouseup', upHandler);
    },

    async createConnection(fromNodeId, toNodeId) {
        // Check if connection already exists
        const exists = this.connections.some(c => c.from === fromNodeId && c.to === toNodeId);
        if (exists) {
            alert('Cette connexion existe déjà');
            return;
        }

        // Create connection in database
        const formData = new FormData();
        formData.append('from_node_id', fromNodeId);
        formData.append('to_node_id', toNodeId);
        formData.append('direction_text', ''); // Default empty
        formData.append('condition_type', 'none');

        try {
            const response = await fetch(`/admin/stories/connections/create`, {
                method: 'POST',
                body: formData
            });
            const data = await response.json();

            if (data.success) {
                // Add to local state
                this.connections.push({
                    id: data.id,
                    from: fromNodeId,
                    to: toNodeId,
                    data: {
                        direction_text: '',
                        condition_type: 'none'
                    }
                });
                this.renderConnections();
            } else {
                alert('Erreur lors de la création de la connexion');
            }
        } catch (error) {
            console.error('Failed to create connection:', error);
            alert('Erreur lors de la création de la connexion');
        }
    },

    async updateConnection(event) {
        event.preventDefault();
        const form = event.target;
        const formData = new FormData(form);
        const id = formData.get('id');

        try {
            const response = await fetch(`/admin/stories/connections/${id}/edit`, {
                method: 'POST',
                body: formData
            });
            const data = await response.json();

            if (data.success) {
                // Update local state
                const conn = this.connections.find(c => c.id == id);
                if (conn) {
                    conn.data = {
                        direction_text: formData.get('direction_text'),
                        condition_type: formData.get('condition_type'),
                        condition_value: formData.get('condition_value'),
                        allow_return: formData.get('allow_return') === 'on',
                        return_text: formData.get('return_text'),
                        return_condition_type: formData.get('return_condition_type'),
                        return_condition_value: formData.get('return_condition_value')
                    };
                }
                alert('Connexion mise à jour !');
            } else {
                alert('Erreur lors de la mise à jour');
            }
        } catch (error) {
            console.error('Failed to update connection:', error);
            alert('Erreur lors de la mise à jour');
        }
    },

    async deleteConnection(id) {
        if (!confirm('Supprimer cette connexion ?')) return;

        try {
            const response = await fetch(`/admin/stories/connections/${id}/delete`, {
                method: 'POST'
            });
            const data = await response.json();

            if (data.success) {
                this.connections = this.connections.filter(c => c.id != id);
                this.deselectAll();
                this.render();
            } else {
                alert('Erreur lors de la suppression');
            }
        } catch (error) {
            console.error('Failed to delete connection:', error);
            alert('Erreur lors de la suppression');
        }
    },

    escapeHtml(text) {
        if (!text) return '';
        return text
            .replace(/&/g, "&amp;")
            .replace(/</g, "&lt;")
            .replace(/>/g, "&gt;")
            .replace(/"/g, "&quot;")
            .replace(/'/g, "&#039;");
    }
};

document.addEventListener('DOMContentLoaded', () => {
    storyEditor.init();
});
