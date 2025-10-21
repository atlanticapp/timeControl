/**
 * ============================================================
 * tipoColores.js - Sistema Centralizado de Colores
 * ============================================================
 * Gestiona todos los colores de la aplicación de planificación
 * 
 * Uso:
 * - TipoColores.estado.pendiente.color
 * - TipoColores.obtenerColorEstado('en_proceso')
 * - TipoColores.obtenerBadgeEstado('completada')
 */

const TipoColores = {
    
    // ============ ESTADOS DE ORDEN ============
    estado: {
        pendiente: {
            badge: 'bg-secondary',
            color: '#6c757d',
            colorClaro: '#e2e3e5',
            colorOscuro: '#5a6268',
            colorTexto: '#fff',
            nombre: 'Pendiente',
            descripcion: 'Orden programada, en espera de iniciar',
            icono: 'bi-hourglass-split'
        },
        en_proceso: {
            badge: 'bg-info',
            color: '#0dcaf0',
            colorClaro: '#d1ecf1',
            colorOscuro: '#0aa2c0',
            colorTexto: '#fff',
            nombre: 'En Proceso',
            descripcion: 'Orden actualmente en producción',
            icono: 'bi-play-circle'
        },
        completada: {
            badge: 'bg-success',
            color: '#198754',
            colorClaro: '#d1e7dd',
            colorOscuro: '#146c43',
            colorTexto: '#fff',
            nombre: 'Completada',
            descripcion: 'Orden finalizada correctamente',
            icono: 'bi-check-circle'
        },
        pausada: {
            badge: 'bg-warning',
            color: '#ffc107',
            colorClaro: '#fff3cd',
            colorOscuro: '#cc9a06',
            colorTexto: '#000',
            nombre: 'Pausada',
            descripcion: 'Orden pausada temporalmente',
            icono: 'bi-pause-circle'
        },
        cancelada: {
            badge: 'bg-danger',
            color: '#dc3545',
            colorClaro: '#f8d7da',
            colorOscuro: '#bd2130',
            colorTexto: '#fff',
            nombre: 'Cancelada',
            descripcion: 'Orden cancelada',
            icono: 'bi-x-circle'
        }
    },

    // ============ PRIORIDADES ============
    prioridad: {
        baja: {
            badge: 'bg-secondary',
            color: '#6c757d',
            colorClaro: '#e2e3e5',
            nombre: 'Baja',
            descripcion: 'Baja prioridad',
            numero: 1
        },
        media: {
            badge: 'bg-info',
            color: '#0dcaf0',
            colorClaro: '#d1ecf1',
            nombre: 'Media',
            descripcion: 'Prioridad media',
            numero: 2
        },
        alta: {
            badge: 'bg-warning',
            color: '#ffc107',
            colorClaro: '#fff3cd',
            nombre: 'Alta',
            descripcion: 'Alta prioridad',
            numero: 3
        },
        urgente: {
            badge: 'bg-danger',
            color: '#dc3545',
            colorClaro: '#f8d7da',
            nombre: 'Urgente',
            descripcion: 'Prioridad urgente',
            numero: 4
        }
    },

    // ============ DISTRIBUCIÓN / CUMPLIMIENTO ============
    cumplimiento: {
        critico: {
            color: '#dc3545',      // Rojo
            nombre: '0-25%',
            descripcion: 'Crítico - Menos del 25% completado'
        },
        bajo: {
            color: '#fd7e14',      // Naranja
            nombre: '25-50%',
            descripcion: 'Bajo - 25% a 50% completado'
        },
        medio: {
            color: '#ffc107',      // Amarillo
            nombre: '50-75%',
            descripcion: 'Medio - 50% a 75% completado'
        },
        alto: {
            color: '#198754',      // Verde
            nombre: '75-99%',
            descripcion: 'Alto - 75% a 99% completado'
        },
        completo: {
            color: '#0d6efd',      // Azul
            nombre: '100%',
            descripcion: 'Completo - 100% completado'
        }
    },

    // ============ ESTADOS DE DISTRIBUCIÓN DIARIA ============
    distribucionEstado: {
        pendiente: {
            color: '#6c757d',
            nombre: 'Pendiente'
        },
        en_proceso: {
            color: '#0dcaf0',
            nombre: 'En Proceso'
        },
        completada: {
            color: '#198754',
            nombre: 'Completada'
        },
        pausada: {
            color: '#ffc107',
            nombre: 'Pausada'
        }
    },

    // ============ ALERTAS Y VALIDACIONES ============
    alerta: {
        exito: {
            color: '#198754',
            fondo: '#d1e7dd',
            borde: '#badbcc',
            icono: 'bi-check-circle-fill'
        },
        error: {
            color: '#dc3545',
            fondo: '#f8d7da',
            borde: '#f5c2c7',
            icono: 'bi-exclamation-circle-fill'
        },
        advertencia: {
            color: '#ffc107',
            fondo: '#fff3cd',
            borde: '#ffecb5',
            icono: 'bi-exclamation-triangle-fill'
        },
        info: {
            color: '#0dcaf0',
            fondo: '#d1ecf1',
            borde: '#b6e4f1',
            icono: 'bi-info-circle-fill'
        }
    },

    // ============ MÉTODOS ÚTILES ============

    /**
     * Obtener objeto completo de color por estado
     */
    obtenerColorEstado(estado) {
        return this.estado[estado] || this.estado.pendiente;
    },

    /**
     * Obtener objeto completo de color por prioridad
     */
    obtenerColorPrioridad(prioridad) {
        return this.prioridad[prioridad] || this.prioridad.media;
    },

    /**
     * Obtener clase badge Bootstrap por estado
     */
    obtenerBadgeEstado(estado) {
        return this.obtenerColorEstado(estado).badge;
    },

    /**
     * Obtener clase badge Bootstrap por prioridad
     */
    obtenerBadgePrioridad(prioridad) {
        return this.obtenerColorPrioridad(prioridad).badge;
    },

    /**
     * Obtener color hexadecimal por estado
     */
    obtenerHexEstado(estado) {
        return this.obtenerColorEstado(estado).color;
    },

    /**
     * Obtener color hexadecimal por prioridad
     */
    obtenerHexPrioridad(prioridad) {
        return this.obtenerColorPrioridad(prioridad).color;
    },

    /**
     * Obtener color según porcentaje de cumplimiento
     */
    obtenerColorCumplimiento(porcentaje) {
        if (porcentaje < 25) return this.cumplimiento.critico.color;
        if (porcentaje < 50) return this.cumplimiento.bajo.color;
        if (porcentaje < 75) return this.cumplimiento.medio.color;
        if (porcentaje < 100) return this.cumplimiento.alto.color;
        return this.cumplimiento.completo.color;
    },

    /**
     * Crear HTML de badge para estado
     */
    crearBadgeEstado(estado) {
        const config = this.obtenerColorEstado(estado);
        return `<span class="badge ${config.badge}" title="${config.descripcion}">
                    <i class="bi ${config.icono} me-1"></i>${config.nombre}
                </span>`;
    },

    /**
     * Crear HTML de badge para prioridad
     */
    crearBadgePrioridad(prioridad) {
        const config = this.obtenerColorPrioridad(prioridad);
        return `<span class="badge ${config.badge}" title="${config.descripcion}">
                    ${config.nombre}
                </span>`;
    },

    /**
     * Crear HTML de etiqueta con color para listados
     */
    crearEtiqueta(texto, tipo = 'estado', valor) {
        let config;
        
        if (tipo === 'estado') {
            config = this.obtenerColorEstado(valor);
        } else if (tipo === 'prioridad') {
            config = this.obtenerColorPrioridad(valor);
        } else {
            config = { color: '#6c757d', colorClaro: '#e2e3e5', colorTexto: '#fff' };
        }
        
        return `<span style="
            display: inline-block;
            background-color: ${config.colorClaro};
            color: ${config.colorTexto};
            padding: 6px 12px;
            border-radius: 6px;
            font-size: 0.85rem;
            border-left: 4px solid ${config.color};
            font-weight: 500;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        ">
            ${texto}
        </span>`;
    },

    /**
     * Obtener configuración completa de color para evento de calendario
     */
    obtenerConfigCalendario(estado) {
        const config = this.obtenerColorEstado(estado);
        return {
            backgroundColor: config.colorClaro,
            borderColor: config.color,
            textColor: config.colorTexto,
            color: config.color
        };
    },

    /**
     * Crear indicador circular de estado
     */
    crearIndicador(estado, tamaño = '12px') {
        const config = this.obtenerColorEstado(estado);
        return `<span style="
            display: inline-block;
            width: ${tamaño};
            height: ${tamaño};
            border-radius: 50%;
            background-color: ${config.color};
            margin-right: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.2);
            border: 2px solid #fff;
        " title="${config.nombre}"></span>`;
    },

    /**
     * Obtener color para barra de progreso basado en porcentaje
     */
    obtenerColorProgreso(porcentaje) {
        if (porcentaje >= 100) return this.cumplimiento.completo.color;
        if (porcentaje >= 75) return this.cumplimiento.alto.color;
        if (porcentaje >= 50) return this.cumplimiento.medio.color;
        if (porcentaje >= 25) return this.cumplimiento.bajo.color;
        return this.cumplimiento.critico.color;
    },

    /**
     * Crear barra de progreso coloreada
     */
    crearBarraProgreso(porcentaje, altura = '24px') {
        const color = this.obtenerColorProgreso(porcentaje);
        const porcentajeReal = Math.min(porcentaje, 100);
        
        return `<div style="
            width: 100%;
            height: ${altura};
            background-color: #f0f0f0;
            border-radius: 4px;
            overflow: hidden;
            border: 1px solid #ddd;
        ">
            <div style="
                width: ${porcentajeReal}%;
                height: 100%;
                background-color: ${color};
                display: flex;
                align-items: center;
                justify-content: center;
                color: #fff;
                font-weight: bold;
                font-size: 0.85rem;
                transition: width 0.3s ease;
            ">
                ${porcentajeReal > 10 ? porcentajeReal.toFixed(1) + '%' : ''}
            </div>
        </div>`;
    },

    /**
     * Obtener paleta completa de colores para exportar/imprimir
     */
    obtenerPaleta(tipo = 'estado') {
        if (tipo === 'estado') {
            return Object.keys(this.estado).map(key => ({
                nombre: this.estado[key].nombre,
                color: this.estado[key].color,
                colorClaro: this.estado[key].colorClaro
            }));
        } else if (tipo === 'prioridad') {
            return Object.keys(this.prioridad).map(key => ({
                nombre: this.prioridad[key].nombre,
                color: this.prioridad[key].color,
                colorClaro: this.prioridad[key].colorClaro
            }));
        }
    }
};

// Exportar para módulos (si se usa en entorno de módulos)
if (typeof module !== 'undefined' && module.exports) {
    module.exports = TipoColores;
}