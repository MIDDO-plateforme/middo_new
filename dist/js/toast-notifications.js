// MIDDO Toast Notifications System
// SESSION 19 - Version finale avec styles inline

class ToastNotification {
    constructor() {
        this.container = null;
        this.init();
    }

    init() {
        if (!document.getElementById('toast-container')) {
            this.container = document.createElement('div');
            this.container.id = 'toast-container';
            this.container.style.cssText = 'position: fixed; top: 20px; right: 20px; z-index: 999999; max-width: 400px; pointer-events: none;';
            document.body.appendChild(this.container);
        } else {
            this.container = document.getElementById('toast-container');
            this.container.style.cssText = 'position: fixed; top: 20px; right: 20px; z-index: 999999; max-width: 400px; pointer-events: none;';
        }
    }

    show(message, type = 'info', duration = 4000) {
        const toast = this.createToast(message, type);
        this.container.appendChild(toast);

        setTimeout(() => {
            toast.style.opacity = '1';
            toast.style.transform = 'translateX(0)';
        }, 10);

        if (duration > 0) {
            setTimeout(() => {
                this.dismiss(toast);
            }, duration);
        }

        return toast;
    }

    createToast(message, type) {
        const toast = document.createElement('div');
        toast.style.cssText = `
            opacity: 0;
            transform: translateX(100%);
            transition: all 0.3s ease-out;
            pointer-events: auto;
            margin-bottom: 12px;
        `;

        const config = this.getConfig(type);
        
        toast.innerHTML = `
            <div style="
                background: white;
                border-radius: 8px;
                box-shadow: 0 10px 25px rgba(0,0,0,0.15);
                border-left: 4px solid ${config.borderColorHex};
                padding: 16px;
                display: flex;
                align-items: start;
                gap: 12px;
                max-width: 400px;
            ">
                <div style="flex-shrink: 0;">
                    ${config.icon}
                </div>
                <div style="flex: 1; min-width: 0;">
                    <p style="font-size: 14px; font-weight: 600; color: ${config.titleColorHex}; margin: 0 0 4px 0;">${config.title}</p>
                    <p style="font-size: 14px; color: #4b5563; margin: 0;">${message}</p>
                </div>
                <button onclick="this.closest('div[style*=opacity]').remove()" style="
                    flex-shrink: 0;
                    background: none;
                    border: none;
                    color: #9ca3af;
                    cursor: pointer;
                    padding: 0;
                    pointer-events: auto;
                " onmouseover="this.style.color='#4b5563'" onmouseout="this.style.color='#9ca3af'">
                    <svg style="width: 20px; height: 20px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
        `;

        return toast;
    }

    getConfig(type) {
        const configs = {
            success: {
                title: 'Succès',
                icon: '<svg style="width: 24px; height: 24px; color: #10b981;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>',
                borderColorHex: '#10b981',
                titleColorHex: '#065f46'
            },
            error: {
                title: 'Erreur',
                icon: '<svg style="width: 24px; height: 24px; color: #ef4444;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>',
                borderColorHex: '#ef4444',
                titleColorHex: '#991b1b'
            },
            warning: {
                title: 'Attention',
                icon: '<svg style="width: 24px; height: 24px; color: #f59e0b;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>',
                borderColorHex: '#f59e0b',
                titleColorHex: '#92400e'
            },
            info: {
                title: 'Information',
                icon: '<svg style="width: 24px; height: 24px; color: #3b82f6;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>',
                borderColorHex: '#3b82f6',
                titleColorHex: '#1e40af'
            },
            ai: {
                title: 'Assistant IA',
                icon: '<svg style="width: 24px; height: 24px; color: #8b5cf6;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path></svg>',
                borderColorHex: '#8b5cf6',
                titleColorHex: '#5b21b6'
            }
        };

        return configs[type] || configs.info;
    }

    dismiss(toast) {
        toast.style.opacity = '0';
        toast.style.transform = 'translateX(100%)';
        
        setTimeout(() => {
            if (toast.parentNode) {
                toast.remove();
            }
        }, 300);
    }

    success(message, duration = 4000) {
        return this.show(message, 'success', duration);
    }

    error(message, duration = 5000) {
        return this.show(message, 'error', duration);
    }

    warning(message, duration = 4000) {
        return this.show(message, 'warning', duration);
    }

    info(message, duration = 4000) {
        return this.show(message, 'info', duration);
    }

    ai(message, duration = 4000) {
        return this.show(message, 'ai', duration);
    }
}

const toast = new ToastNotification();
window.toast = toast;