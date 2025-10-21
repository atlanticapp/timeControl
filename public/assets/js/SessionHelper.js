// SessionHelper.js - Incluir en todas las vistas que hacen peticiones

class SessionHelper {
    /**
     * Obtiene el session_id de la URL o cookie
     */
    static getSessionId() {
        // Primero intentar obtenerlo de la URL
        const urlParams = new URLSearchParams(window.location.search);
        const sidFromUrl = urlParams.get('sid');
        
        if (sidFromUrl) {
            return sidFromUrl;
        }
        
        // Si no está en la URL, obtenerlo de las cookies
        const cookies = document.cookie.split(';');
        for (let cookie of cookies) {
            const [name, value] = cookie.trim().split('=');
            if (name === 'session_id') {
                return value;
            }
        }
        
        return null;
    }

    /**
     * Agrega el session_id a una URL
     */
    static addSessionToUrl(url) {
        const sessionId = this.getSessionId();
        if (!sessionId) return url;
        
        const separator = url.includes('?') ? '&' : '?';
        return `${url}${separator}sid=${sessionId}`;
    }

    /**
     * Agrega el session_id a un FormData
     */
    static addSessionToFormData(formData) {
        const sessionId = this.getSessionId();
        if (sessionId) {
            formData.append('sid', sessionId);
        }
        return formData;
    }

    /**
     * Wrapper para fetch que incluye automáticamente el session_id
     */
    static async fetch(url, options = {}) {
        // Agregar session_id a la URL
        url = this.addSessionToUrl(url);
        
        // Si hay un body que es FormData, agregar el session_id
        if (options.body instanceof FormData) {
            this.addSessionToFormData(options.body);
        }
        
        return fetch(url, options);
    }

    /**
     * Mantiene el session_id en todos los enlaces de navegación
     */
    static initializeLinks() {
        document.addEventListener('DOMContentLoaded', () => {
            const sessionId = this.getSessionId();
            if (!sessionId) return;
            
            // Agregar session_id a todos los enlaces internos
            document.querySelectorAll('a[href^="/"]').forEach(link => {
                const href = link.getAttribute('href');
                if (!href.includes('sid=')) {
                    const separator = href.includes('?') ? '&' : '?';
                    link.setAttribute('href', `${href}${separator}sid=${sessionId}`);
                }
            });
            
            // Agregar session_id a todos los formularios
            document.querySelectorAll('form').forEach(form => {
                if (!form.querySelector('input[name="sid"]')) {
                    const input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = 'sid';
                    input.value = sessionId;
                    form.appendChild(input);
                }
            });
        });
    }

    /**
     * Verifica si la sesión sigue activa
     */
    static async checkSession() {
        try {
            const response = await this.fetch('/timeControl/public/check-session');
            const data = await response.json();
            
            if (!data.active) {
                console.warn('Sesión inactiva detectada');
                window.location.href = '/timeControl/public/login';
                return false;
            }
            
            return true;
        } catch (error) {
            console.error('Error al verificar sesión:', error);
            return false;
        }
    }

    /**
     * Muestra información de la sesión actual (para debugging)
     */
    static showSessionInfo() {
        const sessionId = this.getSessionId();
        console.log('Session ID:', sessionId);
        console.log('Cookies:', document.cookie);
        console.log('JWT Cookie:', document.cookie.split(';').find(c => c.includes('jwt_')));
    }
}

// Inicializar automáticamente cuando se carga la página
SessionHelper.initializeLinks();

// Verificar sesión cada 5 minutos
setInterval(() => SessionHelper.checkSession(), 5 * 60 * 1000);

// Exponer globalmente
window.SessionHelper = SessionHelper;