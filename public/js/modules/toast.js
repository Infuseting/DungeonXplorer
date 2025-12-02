/**
 * Toast Notification Module
 * Handles toast notifications display
 */

import { playSound } from './soundManager.js';

/**
 * Show a toast notification
 * @param {string} message - Message to display
 * @param {string} type - Type of toast ('info', 'success', 'error')
 */
export function showToast(message, type = 'info') {
    const container = document.getElementById('toast-container');
    const toast = document.createElement('div');

    // Play notification sound
    playSound('notification');

    // Colors based on type
    let bgClass = 'bg-gray-800';
    let borderClass = 'border-gray-600';
    let icon = '';

    if (type === 'error') {
        bgClass = 'bg-red-900/90';
        borderClass = 'border-red-700';
        icon = `<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" /></svg>`;
    } else if (type === 'success') {
        bgClass = 'bg-green-900/90';
        borderClass = 'border-green-700';
        icon = `<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-green-400" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" /></svg>`;
    }

    toast.className = `toast pointer-events-auto flex items-center gap-3 px-4 py-3 rounded-lg border shadow-lg backdrop-blur text-white min-w-[300px] ${bgClass} ${borderClass}`;
    toast.innerHTML = `
        ${icon}
        <span class="text-sm font-medium">${message}</span>
    `;

    container.appendChild(toast);

    // Auto remove
    setTimeout(() => {
        toast.classList.add('hiding');
        toast.addEventListener('animationend', () => {
            toast.remove();
        });
    }, 3000);
}
