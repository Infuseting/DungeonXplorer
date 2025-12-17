
document.addEventListener('DOMContentLoaded', () => {
    const canvas = document.getElementById('skillTreeCanvas');
    if (!canvas) return; // Not on edit page

    const ctx = canvas.getContext('2d');
    // Parse nodes to ensure numbers
    const nodes = (window.skillNodes || []).map(n => ({
        ...n,
        node_x: parseInt(n.node_x || 0),
        node_y: parseInt(n.node_y || 0)
    }));

    let isDragging = false;
    let draggedNode = null;
    let dragOffsetX = 0;
    let dragOffsetY = 0;

    // Canvas sizing (responsive-ish)
    function resizeCanvas() {
        const container = canvas.parentElement;
        if (container.clientWidth > 0) {
            canvas.width = container.clientWidth;
            canvas.height = 600; // Fixed height for now
            draw();
        }
    }
    // Specific event for tab switching to ensure resize
    window.addEventListener('resize', resizeCanvas);

    // Initial resize try (might be 0 if hidden, but tab switch will fix)
    resizeCanvas();

    // Event Listeners
    canvas.addEventListener('mousedown', (e) => {
        const { x, y } = getMousePos(e);
        // Reverse search to pick top-most node if overlap
        for (let i = nodes.length - 1; i >= 0; i--) {
            const n = nodes[i];
            if (x >= n.node_x && x <= n.node_x + 150 &&
                y >= n.node_y && y <= n.node_y + 80) {
                draggedNode = n;
                isDragging = true;
                dragOffsetX = x - n.node_x;
                dragOffsetY = y - n.node_y;
                canvas.style.cursor = 'grabbing';
                break;
            }
        }
    });

    canvas.addEventListener('mousemove', (e) => {
        const { x, y } = getMousePos(e);

        if (isDragging && draggedNode) {
            draggedNode.node_x = x - dragOffsetX;
            draggedNode.node_y = y - dragOffsetY;
            draw();
        } else {
            // Hover cursor
            let hovered = false;
            for (const n of nodes) {
                if (x >= n.node_x && x <= n.node_x + 150 &&
                    y >= n.node_y && y <= n.node_y + 80) {
                    hovered = true;
                    break;
                }
            }
            canvas.style.cursor = hovered ? 'grab' : 'default';
        }
    });

    canvas.addEventListener('mouseup', () => {
        isDragging = false;
        draggedNode = null;
        canvas.style.cursor = 'default';
        draw(); // Redraw to clear cursor state if needed
    });

    canvas.addEventListener('mouseleave', () => {
        isDragging = false;
        draggedNode = null;
    });

    // Save Button
    const saveBtn = document.getElementById('savePositionsBtn');
    if (saveBtn) {
        saveBtn.addEventListener('click', savePositions);
    }

    function getMousePos(evt) {
        const rect = canvas.getBoundingClientRect();
        return {
            x: evt.clientX - rect.left,
            y: evt.clientY - rect.top
        };
    }

    function draw() {
        ctx.clearRect(0, 0, canvas.width, canvas.height);

        // Draw Grid
        ctx.strokeStyle = '#1f2937'; // gray-800
        ctx.lineWidth = 1;
        const gridSize = 40;
        for (let x = 0; x < canvas.width; x += gridSize) {
            ctx.beginPath();
            ctx.moveTo(x, 0);
            ctx.lineTo(x, canvas.height);
            ctx.stroke();
        }
        for (let y = 0; y < canvas.height; y += gridSize) {
            ctx.beginPath();
            ctx.moveTo(0, y);
            ctx.lineTo(canvas.width, y);
            ctx.stroke();
        }

        // Draw Connections
        ctx.strokeStyle = '#6366f1'; // indigo-500
        ctx.lineWidth = 2;
        nodes.forEach(node => {
            if (node.parent_skill_id) {
                const parent = nodes.find(n => n.id == node.parent_skill_id);
                if (parent) {
                    drawConnection(parent, node);
                }
            }
        });

        // Draw Nodes
        nodes.forEach(drawNode);
    }

    function drawConnection(start, end) {
        const startX = start.node_x + 75; // Center X
        const startY = start.node_y + 80; // Bottom
        const endX = end.node_x + 75;   // Center X
        const endY = end.node_y;        // Top

        ctx.beginPath();
        ctx.moveTo(startX, startY);
        // Bezier curve
        const cp1x = startX;
        const cp1y = startY + (endY - startY) / 2;
        const cp2x = endX;
        const cp2y = endY - (endY - startY) / 2;

        ctx.bezierCurveTo(cp1x, cp1y, cp2x, cp2y, endX, endY);
        ctx.stroke();
    }

    function drawNode(node) {
        const w = 150;
        const h = 80;
        const x = node.node_x || 0;
        const y = node.node_y || 0;

        // Background
        ctx.fillStyle = '#111827'; // gray-900
        ctx.fillRect(x, y, w, h);

        // Border
        ctx.strokeStyle = node.type === 'active' ? '#3b82f6' : '#10b981'; // blue or emerald
        ctx.lineWidth = 2;
        ctx.strokeRect(x, y, w, h);

        // Text
        ctx.fillStyle = '#f3f4f6'; // gray-100
        ctx.font = 'bold 14px sans-serif';
        ctx.textAlign = 'center';
        ctx.fillText(node.name, x + w / 2, y + 25);

        ctx.fillStyle = '#9ca3af'; // gray-400
        ctx.font = '12px sans-serif';
        ctx.fillText(`Lvl ${node.min_level}`, x + w / 2, y + 45);

        ctx.font = '10px sans-serif';
        ctx.fillText(node.type.toUpperCase(), x + w / 2, y + 65);
    }

    async function savePositions() {
        const positions = nodes.map(n => ({
            id: n.id,
            x: n.node_x,
            y: n.node_y
        }));

        try {
            const btn = document.getElementById('savePositionsBtn');
            const originalText = btn.textContent;
            btn.textContent = 'Sauvegarde...';
            btn.disabled = true;

            const response = await fetch('/admin/classes/skills/save-positions', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ positions })
            });

            const result = await response.json();
            if (result.success) {
                // Show simple toast or alert logic here (or just rely on button state)
                btn.textContent = 'Sauvegardé !';
                setTimeout(() => {
                    btn.textContent = originalText;
                    btn.disabled = false;
                }, 2000);
            } else {
                alert('Erreur lors de la sauvegarde: ' + (result.error || 'Erreur inconnue'));
                btn.textContent = originalText;
                btn.disabled = false;
            }
        } catch (e) {
            console.error(e);
            alert('Erreur réseau');
        }
    }
});
