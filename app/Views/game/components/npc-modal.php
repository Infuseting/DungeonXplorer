<!-- NPC Interaction Modal -->
<div id="npc-modal" class="npc-modal" style="display: none;">
    <div class="npc-modal-overlay"></div>
    <div class="npc-modal-content">
        <!-- NPC Portrait -->
        <div class="npc-portrait">
            <img id="npc-portrait-img" src="" alt="NPC">
        </div>
        
        <!-- Dialogue Box -->
        <div class="npc-dialogue-box">
            <div class="npc-name" id="npc-name">NPC Name</div>
            <div class="npc-text" id="npc-text">Dialogue text goes here...</div>
            
            <!-- Dialogue Choices -->
            <div class="npc-choices" id="npc-choices" style="display: none;">
                <!-- Choices will be inserted here -->
            </div>
            
            <!-- Action Buttons (for merchants, etc.) -->
            <div class="npc-actions" id="npc-actions">
                <!-- Actions will be inserted here -->
            </div>
            
            <!-- Close Button -->
            <button class="npc-close-btn" onclick="closeNPCModal()">✕</button>
        </div>
    </div>
</div>

<style>
.npc-modal {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    z-index: 10000;
    display: flex;
    align-items: flex-end;
    justify-content: center;
}

.npc-modal-overlay {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.7);
    backdrop-filter: blur(5px);
}

.npc-modal-content {
    position: relative;
    width: 100%;
    max-width: 1200px;
    margin: 0 auto;
    padding: 2rem;
    display: flex;
    gap: 2rem;
    align-items: flex-end;
    z-index: 1;
}

.npc-portrait {
    width: 400px;
    height: 600px;
    background: linear-gradient(135deg, rgba(99, 102, 241, 0.1), rgba(168, 85, 247, 0.1));
    border: 3px solid rgba(99, 102, 241, 0.3);
    border-radius: 1rem;
    overflow: hidden;
    display: flex;
    align-items: center;
    justify-content: center;
    box-shadow: 0 10px 40px rgba(0, 0, 0, 0.5);
}

.npc-portrait img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.npc-dialogue-box {
    flex: 1;
    background: linear-gradient(135deg, #1a1a2e, #16213e);
    border: 3px solid rgba(99, 102, 241, 0.5);
    border-radius: 1rem;
    padding: 2rem;
    box-shadow: 0 10px 40px rgba(0, 0, 0, 0.5);
    position: relative;
    min-height: 250px;
    display: flex;
    flex-direction: column;
}

.npc-name {
    font-size: 1.5rem;
    font-weight: 700;
    color: #fbbf24;
    margin-bottom: 1rem;
    text-shadow: 0 2px 10px rgba(251, 191, 36, 0.5);
}

.npc-text {
    font-size: 1.125rem;
    line-height: 1.8;
    color: #e5e7eb;
    margin-bottom: 1.5rem;
    flex: 1;
}

.npc-choices {
    display: flex;
    flex-direction: column;
    gap: 0.75rem;
    margin-top: 1rem;
}

.npc-choice-btn {
    background: linear-gradient(135deg, rgba(99, 102, 241, 0.2), rgba(168, 85, 247, 0.2));
    border: 2px solid rgba(99, 102, 241, 0.5);
    color: #e5e7eb;
    padding: 1rem 1.5rem;
    border-radius: 0.5rem;
    font-size: 1rem;
    cursor: pointer;
    transition: all 0.3s;
    text-align: left;
}

.npc-choice-btn:hover {
    background: linear-gradient(135deg, rgba(99, 102, 241, 0.4), rgba(168, 85, 247, 0.4));
    border-color: #6366f1;
    transform: translateX(10px);
}

.npc-actions {
    display: flex;
    flex-wrap: wrap;
    gap: 1rem;
    margin-top: 1rem;
}

.npc-action-btn {
    background: linear-gradient(135deg, #fbbf24, #f59e0b);
    border: none;
    color: #1a1a2e;
    padding: 1rem 2rem;
    border-radius: 0.5rem;
    font-size: 1rem;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s;
    box-shadow: 0 4px 15px rgba(251, 191, 36, 0.3);
}

.npc-action-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(251, 191, 36, 0.5);
}

.npc-action-btn.secondary {
    background: linear-gradient(135deg, rgba(99, 102, 241, 0.3), rgba(168, 85, 247, 0.3));
    color: #e5e7eb;
    border: 2px solid rgba(99, 102, 241, 0.5);
}

.npc-close-btn {
    position: absolute;
    top: 1rem;
    right: 1rem;
    background: rgba(239, 68, 68, 0.2);
    border: 2px solid rgba(239, 68, 68, 0.5);
    color: #fca5a5;
    width: 40px;
    height: 40px;
    border-radius: 50%;
    font-size: 1.5rem;
    cursor: pointer;
    transition: all 0.3s;
    display: flex;
    align-items: center;
    justify-content: center;
}

.npc-close-btn:hover {
    background: rgba(239, 68, 68, 0.4);
    border-color: #ef4444;
}

/* Animation */
@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(50px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.npc-modal.active .npc-modal-content {
    animation: fadeInUp 0.5s ease-out;
}
</style>
<script type="module" src="/js/modules/npcModal.js"></script>