/**
 * Sound Manager Module
 * Manages background music and sound effects for the game
 */

class SoundManager {
    constructor() {
        this.audioContext = null;
        this.sounds = new Map();
        this.musicElement = null;
        this.initialized = false;
        this.userInteracted = false;

        // Volume settings (0.0 to 1.0)
        this.volumes = {
            master: this.loadVolume('master', 0.7),
            music: this.loadVolume('music', 0.5),
            sfx: this.loadVolume('sfx', 0.8)
        };

        // Sound effect paths
        this.soundPaths = {
            click: '/assets/audio/sfx/click.mp3',
            hover: '/assets/audio/sfx/hover.mp3',
            open: '/assets/audio/sfx/open.mp3',
            close: '/assets/audio/sfx/close.mp3',
            itemPickup: '/assets/audio/sfx/item-pickup.mp3',
            notification: '/assets/audio/sfx/notification.mp3'
        };

        // Music pools by category
        this.musicPools = {
            exploration: [
                '/assets/audio/music/exploration/track1.mp3',
                '/assets/audio/music/exploration/track2.mp3',
                '/assets/audio/music/exploration/track3.mp3'
            ],
            combat: [
                '/assets/audio/music/combat/track1.mp3',
                '/assets/audio/music/combat/track2.mp3',
                '/assets/audio/music/combat/track3.mp3'
            ],
            dungeon: [
                '/assets/audio/music/dungeon/track1.mp3',
            ],
            town: [
                '/assets/audio/music/town/track1.mp3',
                '/assets/audio/music/town/track2.mp3'
            ],
            boss: [
                '/assets/audio/music/boss/track1.mp3',
                '/assets/audio/music/boss/track2.mp3',
                '/assets/audio/music/boss/track3.mp3'
            ]
        };

        // Current music state
        this.currentCategory = 'exploration'; // Default category
        this.currentTrackIndex = -1;
        this.fadeOutDuration = 1000; // Fade duration in ms

        // Bind methods
        this.handleFirstInteraction = this.handleFirstInteraction.bind(this);
        this.handleTrackEnd = this.handleTrackEnd.bind(this);
    }

    /**
     * Initialize the sound system
     */
    async init() {
        if (this.initialized) return;

        console.log('[SoundManager] Initializing...');

        // Note: AudioContext will be created on first user interaction
        // to comply with browser autoplay policy

        // Setup background music element
        this.setupBackgroundMusic();

        // Preload common sound effects
        await this.preloadSounds(['click', 'open', 'close', 'notification']);

        // Listen for first user interaction to start music
        this.setupFirstInteractionListener();

        this.initialized = true;
        console.log('[SoundManager] Initialized successfully');
    }

    /**
     * Setup background music element with lazy loading
     */
    setupBackgroundMusic() {
        this.musicElement = new Audio();
        this.musicElement.preload = 'metadata'; // Lazy loading
        this.musicElement.volume = this.volumes.master * this.volumes.music;

        // Handle loading events
        this.musicElement.addEventListener('loadedmetadata', () => {
            console.log('[SoundManager] Music metadata loaded');
        });

        this.musicElement.addEventListener('canplaythrough', () => {
            console.log('[SoundManager] Music ready to play');
        });

        this.musicElement.addEventListener('error', (e) => {
            console.error('[SoundManager] Music loading error:', e);
        });

        // Handle track end to play next random track
        this.musicElement.addEventListener('ended', this.handleTrackEnd);
    }

    /**
     * Setup listener for first user interaction
     */
    setupFirstInteractionListener() {
        const events = ['click', 'touchstart', 'keydown'];
        events.forEach(event => {
            document.addEventListener(event, this.handleFirstInteraction, { once: true });
        });
    }

    /**
     * Handle first user interaction - start music
     */
    async handleFirstInteraction() {
        if (this.userInteracted) return;

        this.userInteracted = true;
        console.log('[SoundManager] First user interaction detected');

        // Create audio context now (after user gesture)
        if (!this.audioContext) {
            try {
                this.audioContext = new (window.AudioContext || window.webkitAudioContext)();
                console.log('[SoundManager] AudioContext created');
            } catch (e) {
                console.warn('[SoundManager] Web Audio API not supported:', e);
            }
        }

        // Resume audio context if suspended
        if (this.audioContext && this.audioContext.state === 'suspended') {
            await this.audioContext.resume();
        }

        // Start background music
        this.playMusic();
    }

    /**
     * Play background music (starts or resumes current track)
     */
    async playMusic() {
        if (!this.musicElement) return;

        // If no track is loaded, load a random one from current category
        if (!this.musicElement.src || this.currentTrackIndex === -1) {
            this.loadRandomTrack(this.currentCategory);
        }

        try {
            await this.musicElement.play();
            console.log('[SoundManager] Background music started');
        } catch (e) {
            console.warn('[SoundManager] Could not play music:', e);
        }
    }

    /**
     * Load a random track from the specified category
     * @param {string} category - Music category
     */
    loadRandomTrack(category) {
        const pool = this.musicPools[category];
        if (!pool || pool.length === 0) {
            console.warn(`[SoundManager] No music tracks found for category: ${category}`);
            return;
        }

        // Select random track (avoid repeating the same track if possible)
        let randomIndex;
        if (pool.length === 1) {
            randomIndex = 0;
        } else {
            do {
                randomIndex = Math.floor(Math.random() * pool.length);
            } while (randomIndex === this.currentTrackIndex && pool.length > 1);
        }

        this.currentTrackIndex = randomIndex;
        this.musicElement.src = pool[randomIndex];
        console.log(`[SoundManager] Loaded track ${randomIndex + 1}/${pool.length} from category: ${category}`);
    }

    /**
     * Handle track end - play next random track from same category
     */
    handleTrackEnd() {
        console.log('[SoundManager] Track ended, loading next random track');
        this.loadRandomTrack(this.currentCategory);
        this.playMusic();
    }

    /**
     * Change music category with smooth transition
     * @param {string} category - New music category
     * @param {boolean} immediate - If true, change immediately without fade
     */
    async changeMusicCategory(category, immediate = false) {
        if (!this.musicPools[category]) {
            console.warn(`[SoundManager] Invalid music category: ${category}`);
            return;
        }

        if (this.currentCategory === category) {
            console.log(`[SoundManager] Already playing category: ${category}`);
            return;
        }

        console.log(`[SoundManager] Changing music category from ${this.currentCategory} to ${category}`);
        this.currentCategory = category;

        if (!this.musicElement || this.musicElement.paused) {
            // Music not playing, just load new track
            this.loadRandomTrack(category);
            return;
        }

        if (immediate) {
            // Immediate change
            this.loadRandomTrack(category);
            await this.playMusic();
        } else {
            // Smooth fade transition
            await this.fadeOutMusic();
            this.loadRandomTrack(category);
            await this.fadeInMusic();
        }
    }

    /**
     * Fade out current music
     */
    async fadeOutMusic() {
        if (!this.musicElement || this.musicElement.paused) return;

        const startVolume = this.musicElement.volume;
        const steps = 20;
        const stepDuration = this.fadeOutDuration / steps;

        for (let i = steps; i >= 0; i--) {
            this.musicElement.volume = (startVolume * i) / steps;
            await new Promise(resolve => setTimeout(resolve, stepDuration));
        }

        this.musicElement.pause();
    }

    /**
     * Fade in current music
     */
    async fadeInMusic() {
        if (!this.musicElement) return;

        const targetVolume = this.volumes.master * this.volumes.music;
        const steps = 20;
        const stepDuration = this.fadeOutDuration / steps;

        this.musicElement.volume = 0;
        await this.musicElement.play();

        for (let i = 0; i <= steps; i++) {
            this.musicElement.volume = (targetVolume * i) / steps;
            await new Promise(resolve => setTimeout(resolve, stepDuration));
        }
    }

    /**
     * Pause background music
     */
    pauseMusic() {
        if (this.musicElement && !this.musicElement.paused) {
            this.musicElement.pause();
            console.log('[SoundManager] Background music paused');
        }
    }

    /**
     * Toggle background music
     */
    toggleMusic() {
        if (!this.musicElement) return;

        if (this.musicElement.paused) {
            this.playMusic();
        } else {
            this.pauseMusic();
        }
    }

    /**
     * Preload sound effects
     */
    async preloadSounds(soundNames) {
        const promises = soundNames.map(name => this.loadSound(name));
        await Promise.all(promises);
        console.log(`[SoundManager] Preloaded ${soundNames.length} sounds`);
    }

    /**
     * Load a single sound effect
     */
    async loadSound(soundName) {
        if (this.sounds.has(soundName)) return;

        const path = this.soundPaths[soundName];
        if (!path) {
            console.warn(`[SoundManager] Sound path not found for: ${soundName}`);
            return;
        }

        try {
            const audio = new Audio();
            audio.src = path;
            audio.preload = 'auto';
            audio.volume = this.volumes.master * this.volumes.sfx;

            // Wait for the sound to be ready
            await new Promise((resolve, reject) => {
                audio.addEventListener('canplaythrough', resolve, { once: true });
                audio.addEventListener('error', reject, { once: true });
            });

            this.sounds.set(soundName, audio);
        } catch (e) {
            console.warn(`[SoundManager] Failed to load sound: ${soundName}`, e);
        }
    }

    /**
     * Play a sound effect
     */
    async playSound(soundName) {
        if (!this.userInteracted) {
            // Can't play sounds before user interaction
            return;
        }

        // Load sound if not already loaded
        if (!this.sounds.has(soundName)) {
            await this.loadSound(soundName);
        }

        const audio = this.sounds.get(soundName);
        if (!audio) return;

        try {
            // Clone the audio element to allow overlapping sounds
            const clone = audio.cloneNode();
            clone.volume = this.volumes.master * this.volumes.sfx;
            await clone.play();
        } catch (e) {
            console.warn(`[SoundManager] Failed to play sound: ${soundName}`, e);
        }
    }

    /**
     * Set master volume
     */
    setMasterVolume(volume) {
        this.volumes.master = Math.max(0, Math.min(1, volume));
        this.saveVolume('master', this.volumes.master);
        this.updateVolumes();
    }

    /**
     * Set music volume
     */
    setMusicVolume(volume) {
        this.volumes.music = Math.max(0, Math.min(1, volume));
        this.saveVolume('music', this.volumes.music);
        this.updateVolumes();
    }

    /**
     * Set SFX volume
     */
    setSFXVolume(volume) {
        this.volumes.sfx = Math.max(0, Math.min(1, volume));
        this.saveVolume('sfx', this.volumes.sfx);
        this.updateVolumes();
    }

    /**
     * Update all audio element volumes
     */
    updateVolumes() {
        // Update music volume
        if (this.musicElement) {
            this.musicElement.volume = this.volumes.master * this.volumes.music;
        }

        // Update cached sound volumes
        this.sounds.forEach(audio => {
            audio.volume = this.volumes.master * this.volumes.sfx;
        });
    }

    /**
     * Load volume from localStorage
     */
    loadVolume(key, defaultValue) {
        const stored = localStorage.getItem(`volume_${key}`);
        return stored !== null ? parseFloat(stored) : defaultValue;
    }

    /**
     * Save volume to localStorage
     */
    saveVolume(key, value) {
        localStorage.setItem(`volume_${key}`, value.toString());
    }

    /**
     * Get current volumes
     */
    getVolumes() {
        return { ...this.volumes };
    }

    /**
     * Mute all sounds
     */
    muteAll() {
        this.setMasterVolume(0);
    }

    /**
     * Unmute all sounds
     */
    unmuteAll() {
        this.setMasterVolume(0.7);
    }
}

// Create singleton instance
const soundManager = new SoundManager();

// Export functions
export async function initSoundManager() {
    await soundManager.init();
}

export function playSound(soundName) {
    soundManager.playSound(soundName);
}

export function playMusic() {
    soundManager.playMusic();
}

export function pauseMusic() {
    soundManager.pauseMusic();
}

export function toggleMusic() {
    soundManager.toggleMusic();
}

export function changeMusicCategory(category, immediate = false) {
    soundManager.changeMusicCategory(category, immediate);
}

export function setMasterVolume(volume) {
    soundManager.setMasterVolume(volume);
}

export function setMusicVolume(volume) {
    soundManager.setMusicVolume(volume);
}

export function setSFXVolume(volume) {
    soundManager.setSFXVolume(volume);
}

export function getVolumes() {
    return soundManager.getVolumes();
}

export default soundManager;
