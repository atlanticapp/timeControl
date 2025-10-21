  (function() {
            'use strict';

            
            const DOM_CACHE = {
                tabLinks: null,
                tabContents: null,
                timeElements: null,
                filterInputs: null,
                entregaRows: null,
                mobileRows: null
            };

            function initDOMCache() {
                DOM_CACHE.tabLinks = document.querySelectorAll('.tab-link');
                DOM_CACHE.tabContents = document.querySelectorAll('.tab-content');
                DOM_CACHE.filterInputs = {
                    fecha: document.getElementById('filtroFecha'),
                    item: document.getElementById('filtroItem'),
                    jtwo: document.getElementById('filtroJtWo'),
                    po: document.getElementById('filtroPO'),
                    cliente: document.getElementById('filtroCliente')
                };
            }

            const TimeUpdateSystem = {
                rows: [],
                animationFrameId: null,
                isRunning: false,
                lastUpdate: 0,
                UPDATE_INTERVAL: 1000, 

                init() {
                    this.cacheTimeRows();
                    this.start();
                },

                cacheTimeRows() {
                    
                    const tableRows = document.querySelectorAll('tbody tr[data-start-time]');
                    this.rows = Array.from(tableRows).map(row => {
                        const timeElement = row.querySelector('.tiempo');
                        const startTime = parseInt(row.dataset.startTime, 10) * 1000; // Convertir a milisegundos
                        
                        return {
                            element: timeElement,
                            startTime: startTime,
                            row: row
                        };
                    }).filter(item => item.element && !isNaN(item.startTime));
                },

                updateTime(timestamp) {
                    
                    if (timestamp - this.lastUpdate < this.UPDATE_INTERVAL) {
                        if (this.isRunning) {
                            this.animationFrameId = requestAnimationFrame((ts) => this.updateTime(ts));
                        }
                        return;
                    }

                    this.lastUpdate = timestamp;
                    const now = Date.now();

                    // Actualizar todos los tiempos en un solo ciclo
                    for (let i = 0; i < this.rows.length; i++) {
                        const item = this.rows[i];
                        const diff = Math.floor((now - item.startTime) / 1000); // Diferencia en segundos

                        const hours = Math.floor(diff / 3600);
                        const minutes = Math.floor((diff % 3600) / 60);
                        const seconds = diff % 60;

                        // Formatear tiempo
                        const timeString = 
                            String(hours).padStart(2, '0') + ':' +
                            String(minutes).padStart(2, '0') + ':' +
                            String(seconds).padStart(2, '0');

                        // Actualizar solo si cambió (evita reflows innecesarios)
                        if (item.element.textContent !== timeString) {
                            item.element.textContent = timeString;
                        }

                        // Actualizar clases de color solo si es necesario
                        const newClass = hours >= 8 ? 'text-red-600' : 
                                       (hours >= 6 ? 'text-yellow-600' : 'text-green-600');
                        
                        if (!item.element.classList.contains(newClass)) {
                            item.element.className = 'tiempo font-bold ' + newClass;
                        }
                    }

                    // Continuar el loop
                    if (this.isRunning) {
                        this.animationFrameId = requestAnimationFrame((ts) => this.updateTime(ts));
                    }
                },

                start() {
                    if (this.isRunning) return;
                    this.isRunning = true;
                    this.animationFrameId = requestAnimationFrame((ts) => this.updateTime(ts));
                },

                stop() {
                    this.isRunning = false;
                    if (this.animationFrameId) {
                        cancelAnimationFrame(this.animationFrameId);
                        this.animationFrameId = null;
                    }
                },

                refresh() {
                    // Refrescar cache cuando cambian los datos
                    this.stop();
                    this.cacheTimeRows();
                    this.start();
                }
            };

           
            function debounce(func, wait) {
                let timeout;
                return function executedFunction(...args) {
                    const later = () => {
                        clearTimeout(timeout);
                        func(...args);
                    };
                    clearTimeout(timeout);
                    timeout = setTimeout(later, wait);
                };
            }

           
            const FilterSystem = {
                cachedRows: null,

                init() {
                    this.cacheRows();
                    this.attachListeners();
                },

                cacheRows() {
                    
                    const desktopRows = document.querySelectorAll('.entrega-row');
                    const mobileRows = document.querySelectorAll('.mobile-table-row');
                    
                    this.cachedRows = [...desktopRows, ...mobileRows].map(row => ({
                        element: row,
                        data: {
                            fecha: row.dataset.fecha || '',
                            item: (row.dataset.item || '').toLowerCase(),
                            jtwo: (row.dataset.jtwo || '').toLowerCase(),
                            po: (row.dataset.po || '').toLowerCase(),
                            cliente: (row.dataset.cliente || '').toLowerCase()
                        }
                    }));
                },

                filter() {
                    const filters = {
                        fecha: DOM_CACHE.filterInputs.fecha?.value || '',
                        item: (DOM_CACHE.filterInputs.item?.value || '').toLowerCase(),
                        jtwo: (DOM_CACHE.filterInputs.jtwo?.value || '').toLowerCase(),
                        po: (DOM_CACHE.filterInputs.po?.value || '').toLowerCase(),
                        cliente: (DOM_CACHE.filterInputs.cliente?.value || '').toLowerCase()
                    };

                    // Filtrar en un solo pase
                    for (let i = 0; i < this.cachedRows.length; i++) {
                        const row = this.cachedRows[i];
                        const data = row.data;
                        
                        const match = 
                            (!filters.fecha || data.fecha === filters.fecha) &&
                            (!filters.item || data.item.includes(filters.item)) &&
                            (!filters.jtwo || data.jtwo.includes(filters.jtwo)) &&
                            (!filters.po || data.po.includes(filters.po)) &&
                            (!filters.cliente || data.cliente.includes(filters.cliente));

                        row.element.style.display = match ? '' : 'none';
                    }
                },

                clear() {
                    // Limpiar todos los filtros
                    Object.values(DOM_CACHE.filterInputs).forEach(input => {
                        if (input) input.value = '';
                    });
                    this.filter();
                },

                attachListeners() {
                    const debouncedFilter = debounce(() => this.filter(), 300);
                    
                    Object.values(DOM_CACHE.filterInputs).forEach(input => {
                        if (input) {
                            input.addEventListener('input', debouncedFilter);
                            input.addEventListener('change', debouncedFilter);
                        }
                    });

                    const clearBtn = document.getElementById('btnLimpiarFiltros');
                    if (clearBtn) {
                        clearBtn.addEventListener('click', (e) => {
                            e.preventDefault();
                            this.clear();
                        });
                    }
                }
            };

            
            const TabSystem = {
                init() {
                    DOM_CACHE.tabLinks.forEach(link => {
                        link.addEventListener('click', (e) => {
                            e.preventDefault();
                            this.switchTab(link);
                        });
                    });

                    // Activar tab por defecto
                    const defaultTab = document.querySelector('#tab-operaciones');
                    if (defaultTab) {
                        this.switchTab(defaultTab);
                    }
                },

                switchTab(activeLink) {
                    // Remover clases activas
                    DOM_CACHE.tabLinks.forEach(link => link.classList.remove('active'));
                    DOM_CACHE.tabContents.forEach(content => content.classList.add('hidden'));

                    // Activar tab seleccionado
                    activeLink.classList.add('active');
                    const targetId = activeLink.getAttribute('href');
                    const targetContent = document.querySelector(targetId);
                    if (targetContent) {
                        targetContent.classList.remove('hidden');
                    }

                    // Si cambiamos a operaciones abiertas, refrescar tiempos
                    if (targetId === '#operaciones-abiertas') {
                        TimeUpdateSystem.refresh();
                    }
                }
            };

            document.addEventListener('DOMContentLoaded', function() {
                // Inicializar cache DOM
                initDOMCache();

                // Inicializar sistemas
                TabSystem.init();
                TimeUpdateSystem.init();
                FilterSystem.init();

                // Notificación de nuevas entregas (optimizado)
                document.addEventListener('newDelivery', (e) => {
                    toastr.info('Nueva entrega detectada', 'Hay nuevos registros disponibles', {
                        timeOut: 5000,
                        extendedTimeOut: 1000,
                        closeButton: true,
                        tapToDismiss: false,
                        onclick: () => window.location.reload()
                    });
                });

                // Limpiar al salir de la página
                window.addEventListener('beforeunload', () => {
                    TimeUpdateSystem.stop();
                });
            });

            
            window.confirmLogout = function() {
                if (confirm("¿Estás seguro de que deseas cerrar sesión?")) {
                    document.cookie = 'jwt=; Path=/; Expires=Thu, 01 Jan 1970 00:00:01 GMT;';
                    window.location.href = "/timeControl/public/logout";
                }
            };

        })();