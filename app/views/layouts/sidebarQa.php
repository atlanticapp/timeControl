<header class="lg:hidden fixed top-0 left-0 right-0 bg-white shadow-lg z-50 px-5 py-4 flex justify-between items-center">
    <button id="toggleSidebar" class="p-2 rounded-lg hover:bg-gray-100 focus:outline-none transition duration-300">
        <i class="fas fa-bars text-blue-700"></i>
    </button>
    <h1 class="text-xl font-bold text-blue-700 flex items-center">
        <i class="fas fa-chart-line mr-2"></i> <a href="/timeControl/public/dashboard">Panel QA</a>
    </h1>
    <div class="relative">
        <button id="toggleNotificationsMobile" class="notification-button relative focus:outline-none p-2 rounded-full hover:bg-gray-200 transition duration-300">
            <i class="fas fa-bell text-gray-700 text-lg"></i>
            <span id="notificationCountMobile" class="absolute -top-1 -right-1 bg-red-500 text-white text-xs font-bold w-5 h-5 flex items-center justify-center rounded-full hidden shadow-lg">0</span>
        </button>
    </div>
</header>

<!-- Overlay mejorado para sidebar en móvil -->
<div id="sidebarOverlay" class="fixed inset-0 bg-black bg-opacity-60 z-30 hidden lg:hidden transition-opacity duration-300"></div>

<!-- Sidebar optimizado -->
<div id="sidebar" class="sidebar-transition w-72 bg-gradient-to-b from-blue-50 to-white shadow-xl h-screen fixed left-0 top-0 -translate-x-full lg:translate-x-0 z-40 overflow-hidden transition-transform duration-300">
    <div class="border-b border-gray-200 bg-white flex justify-between items-center p-6">
        <h2 class="text-2xl font-bold text-blue-700 flex items-center">
            <i class="fas fa-chart-line mr-3"></i><a href="/timeControl/public/dashboard">Panel QA</a>
        </h2>
        <div class="relative flex justify-end">
            <button id="toggleNotificationsDesktop" class="notification-button relative focus:outline-none p-2 rounded-full hover:bg-gray-200 transition duration-300" aria-label="Mostrar notificaciones">
                <i class="fas fa-bell text-gray-700 text-lg"></i>
                <span id="notificationCountDesktop" class="absolute -top-1 -right-1 bg-red-500 text-white text-xs font-bold w-5 h-5 flex items-center justify-center rounded-full hidden shadow-md">0</span>
            </button>
            <button id="closeSidebar" class="lg:hidden p-2 rounded-full hover:bg-gray-200 transition duration-300 ml-2">
                <i class="fas fa-times text-gray-700 text-lg"></i>
            </button>
        </div>
    </div>

    <!-- Menú de Navegación centrado y mejorado -->
    <nav class="p-6">
        <ul class="space-y-3">
            <?php
            $menuItems = [
                 [
                    'icon' => 'fas fa-tachometer-alt',
                    'text' => 'Dashboard',
                    'color' => 'blue',
                    'route' => 'dashboard'
                ],
                ['icon' => 'fas fa-check-circle', 'text' => 'Validación de Entregas', 'color' => 'teal', 'route' => 'validacion'],
                ['icon' => 'fas fa-clipboard-check', 'text' => 'Acción QA', 'color' => 'amber', 'route' => 'accion'],
                ['icon' => 'fas fa-exclamation-triangle', 'text' => 'Retenciones', 'color' => 'yellow', 'route' => 'retenciones'],
                // ['icon' => 'fas fa-box-archive', 'text' => 'Producción Guardada', 'color' => 'green', 'route' => 'produccion/guardada'],
                // [
                //     'icon' => 'fas fa-boxes', 
                //     'text' => 'Destinos', 
                //     'color' => 'blue',
                //     'submenu' => [
                //         [
                //             'icon' => 'fas fa-box-open mr-3',
                //             'text' => 'Producción Final',
                //             'color' => 'blue',
                //             'route' => 'destinos/produccion'
                //         ],
                //         [
                //             'icon' => 'fas fa-recycle',
                //             'text' => 'Retrabajo',
                //             'color' => 'green',
                //             'route' => 'destinos/retrabajo'
                //         ],
                //         [
                //             'icon' => 'fas fa-trash-alt',
                //             'text' => 'Destrucción',
                //             'color' => 'red',
                //             'route' => 'destinos/destruccion'
                //         ],
                //     ]
                // ],
                ['icon' => 'fas fa-search', 'text' => 'Revisiones Pendientes', 'color' => 'rose', 'route' => 'revisiones']
            ];
            foreach ($menuItems as $item): ?>
                <li>
                    <?php if (isset($item['submenu'])): ?>
                        <div class="menu-item flex flex-col">
                            <button class="submenu-toggle flex items-center justify-between w-full p-4 text-gray-700 rounded-lg hover:bg-<?= $item['color'] ?>-50 transition-all duration-300">
                                <div class="flex items-center">
                                    <i class="<?= $item['icon'] ?> mr-3 text-<?= $item['color'] ?>-500 text-lg"></i>
                                    <span class="font-medium"><?= $item['text'] ?></span>
                                </div>
                                <i class="fas fa-chevron-right text-gray-400 transition-transform duration-300"></i>
                            </button>
                            <ul class="submenu ml-8 space-y-2 hidden">
                                <?php foreach ($item['submenu'] as $subitem): ?>
                                    <li>
                                        <a href="/timeControl/public/<?= $subitem['route'] ?>" 
                                           class="flex items-center p-2 text-sm text-gray-600 hover:bg-<?= $subitem['color'] ?>-50 rounded-lg transition-all duration-300 group border border-transparent hover:border-<?= $subitem['color'] ?>-200">
                                            <i class="<?= $subitem['icon'] ?> mr-2 text-<?= $subitem['color'] ?>-400 group-hover:text-<?= $subitem['color'] ?>-500"></i>
                                            <span class="group-hover:text-<?= $subitem['color'] ?>-600"><?= $subitem['text'] ?></span>
                                        </a>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    <?php else: ?>
                        <a href="/timeControl/public/<?= $item['route'] ?>" class="menu-item flex items-center p-4 text-gray-700 hover:bg-<?= $item['color'] ?>-50 rounded-lg transition-all duration-300 group border border-transparent hover:border-<?= $item['color'] ?>-200">
                            <i class="<?= $item['icon'] ?> mr-3 text-<?= $item['color'] ?>-500 group-hover:text-<?= $item['color'] ?>-600 text-lg"></i>
                            <span class="font-medium group-hover:text-<?= $item['color'] ?>-600"><?= $item['text'] ?></span>
                        </a>
                    <?php endif; ?>
                </li>
            <?php endforeach; ?>
        </ul>
    </nav>

    <!-- Información del sistema -->
    <div class="absolute bottom-0 left-0 right-0 p-6 bg-blue-50">
        <div class="text-xs text-gray-500 flex items-center justify-center mb-2">
            <i class="fas fa-clock mr-2"></i>
            <span id="current-date"><?= date('d/m/Y H:i:s') ?></span>
        </div>
        <div class="text-center text-xs text-blue-600">
            <i class="fas fa-shield-alt mr-1"></i> Panel QA
        </div>
    </div>
</div>

<!-- Panel de Notificaciones rediseñado -->
<div id="notificationDropdown" class="notification-dropdown-transition fixed lg:absolute opacity-0 scale-95 -translate-y-2 hidden left-4 lg:left-4 lg:right-auto top-16 lg:top-20 w-[calc(100%-2rem)] max-w-sm bg-white shadow-2xl border border-gray-100 rounded-lg overflow-hidden z-50">
    <div class="py-3 px-4 bg-gradient-to-r from-blue-600 to-blue-700 text-white font-semibold flex justify-between items-center">
        <div class="flex items-center">
            <i class="fas fa-bell mr-2"></i>
            <span>Notificaciones</span>
        </div>
        <button id="markAllRead" class="text-white text-sm hover:bg-blue-500 py-1 px-3 rounded transition-colors duration-200" aria-label="Marcar todas como leídas">
            <i class="fas fa-check-double mr-1"></i>Marcar todo
        </button>
    </div>
    <div class="notification-header-info bg-blue-50 px-4 py-2 text-xs text-blue-600 border-b border-gray-100 hidden" id="notificationInfo">
        <i class="fas fa-info-circle mr-1"></i>
        <span id="notificationInfoText">Tienes notificaciones no leídas</span>
    </div>
    <ul id="notificationList" class="max-h-72 overflow-y-auto divide-y divide-gray-100"></ul>
    <div class="p-3 bg-gray-50 text-center border-t border-gray-100 text-sm text-gray-500" id="notificationFooter">
        No hay nuevas notificaciones
    </div>
</div>

<!-- Contenedor de Toasts mejorado -->
<div id="toastContainer" class="fixed bottom-6 right-6 space-y-3 z-50 w-auto max-w-xs"></div>




<!-- Scripts para funcionalidad -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Funcionalidad para el sidebar móvil
        const toggleSidebar = document.getElementById('toggleSidebar');
        const closeSidebar = document.getElementById('closeSidebar');
        const sidebar = document.getElementById('sidebar');
        const overlay = document.getElementById('sidebarOverlay');

        toggleSidebar.addEventListener('click', function() {
            sidebar.classList.toggle('-translate-x-full');
            overlay.classList.toggle('hidden');
        });

        closeSidebar.addEventListener('click', function() {
            sidebar.classList.add('-translate-x-full');
            overlay.classList.add('hidden');
        });

        overlay.addEventListener('click', function() {
            sidebar.classList.add('-translate-x-full');
            overlay.classList.add('hidden');
        });

        // Actualizar la fecha y hora
        function updateDateTime() {
            const now = new Date();
            const dateStr = now.toLocaleDateString() + ' ' + now.toLocaleTimeString();
            document.getElementById('current-date').textContent = dateStr;
            if (document.getElementById('current-date-desktop')) {
                document.getElementById('current-date-desktop').textContent = dateStr;
            }
        }

        setInterval(updateDateTime, 1000);
        updateDateTime();
    });
</script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Manejador para los toggles de submenú
    const submenuToggles = document.querySelectorAll('.submenu-toggle');
    
    submenuToggles.forEach(toggle => {
        toggle.addEventListener('click', function() {
            // Encuentra el submenu asociado
            const submenu = this.nextElementSibling;
            const chevron = this.querySelector('.fa-chevron-right');
            
            // Toggle de la clase hidden
            submenu.classList.toggle('hidden');
            
            // Rota el chevron cuando el menú está abierto
            if (!submenu.classList.contains('hidden')) {
                chevron.style.transform = 'rotate(90deg)';
                toggle.classList.add('bg-blue-50');
            } else {
                chevron.style.transform = 'rotate(0deg)';
                toggle.classList.remove('bg-blue-50');
            }
        });
    });
});
</script>

<script>
    class ResponsiveUI {

        constructor() {
            this.initElements();
            this.initEventListeners();
        }

        initElements() {
            this.elements = {
                sidebar: document.getElementById("sidebar"),
                toggleSidebar: document.getElementById("toggleSidebar"),
                closeSidebar: document.getElementById("closeSidebar"),
                sidebarOverlay: document.getElementById("sidebarOverlay")
            };
        }

        initEventListeners() {
            this.elements.toggleSidebar.addEventListener("click", () => this.openSidebar());
            this.elements.closeSidebar.addEventListener("click", () => this.closeSidebar());
            this.elements.sidebarOverlay.addEventListener("click", () => this.closeSidebar());

            // Cerrar sidebar en tamaños grandes
            window.addEventListener('resize', () => {
                if (window.innerWidth >= 1024) { // lg breakpoint
                    this.elements.sidebarOverlay.classList.add('hidden');
                }
            });
        }

        openSidebar() {
            this.elements.sidebar.classList.remove('-translate-x-full');
            this.elements.sidebar.classList.add('translate-x-0');
            this.elements.sidebarOverlay.classList.remove('hidden');
        }

        closeSidebar() {
            this.elements.sidebar.classList.remove('translate-x-0');
            this.elements.sidebar.classList.add('-translate-x-full');
            this.elements.sidebarOverlay.classList.add('hidden');
        }
    }

    class NotificationManager {
        static instance;

        // Implementación del patrón Singleton
        static getInstance() {
            if (!NotificationManager.instance) {
                NotificationManager.instance = new NotificationManager();
            }
            return NotificationManager.instance;
        }

        constructor() {
            // Si ya existe una instancia, no crear otra
            if (NotificationManager.instance) {
                return NotificationManager.instance;
            }

            this.readNotifications = new Set(JSON.parse(localStorage.getItem("readNotifications") || "[]"));
            this.lastUpdate = Date.now();

            // Verificar que estamos en una página con los elementos necesarios
            if (this.checkRequiredElements()) {
                this.elements = this.initElements();
                this.initEventListeners();
                this.startAutoRefresh();
            }
        }

        // Verificar que los elementos esenciales existen en el DOM
        checkRequiredElements() {
            const requiredElements = [
                "toggleNotificationsDesktop",
                "toggleNotificationsMobile",
                "notificationList"
            ];

            return requiredElements.every(id => document.getElementById(id) !== null);
        }

        initElements() {
            return {
                toggleButtonDesktop: document.getElementById("toggleNotificationsDesktop"),
                toggleButtonMobile: document.getElementById("toggleNotificationsMobile"),
                countBadgeDesktop: document.getElementById("notificationCountDesktop") || null,
                countBadgeMobile: document.getElementById("notificationCountMobile") || null,
                dropdown: document.getElementById("notificationDropdown") || null,
                list: document.getElementById("notificationList") || null,
                markAllButton: document.getElementById("markAllRead") || null,
                toastContainer: document.getElementById("toastContainer") || null,
                notificationInfo: document.getElementById("notificationInfo") || null,
                notificationInfoText: document.getElementById("notificationInfoText") || null,
                notificationFooter: document.getElementById("notificationFooter") || null,
                sidebar: document.getElementById("sidebar") || null
            };
        }

        initEventListeners() {
            if ("Notification" in window) {
                Notification.requestPermission()
                    .then(permission => console.log(permission === "granted" ?
                        "Permiso otorgado para notificaciones." :
                        "Permiso denegado o no otorgado."));
            }

            const handleToggleClick = (e, button) => {
                e.stopPropagation();
                this.toggleDropdown(button);
            };

            if (this.elements.toggleButtonDesktop) {
                this.elements.toggleButtonDesktop.addEventListener("click",
                    e => handleToggleClick(e, this.elements.toggleButtonDesktop));
            }

            if (this.elements.toggleButtonMobile) {
                this.elements.toggleButtonMobile.addEventListener("click",
                    e => handleToggleClick(e, this.elements.toggleButtonMobile));
            }

            if (this.elements.markAllButton) {
                this.elements.markAllButton.addEventListener("click", e => {
                    e.stopPropagation();
                    this.markAllRead();
                });
            }

            document.addEventListener('click', e => this.handleDocumentClick(e));
        }

        handleDocumentClick(e) {
            const {
                dropdown,
                toggleButtonDesktop,
                toggleButtonMobile
            } = this.elements;

            if (dropdown &&
                !dropdown.contains(e.target) &&
                ((toggleButtonDesktop && !toggleButtonDesktop.contains(e.target)) || !toggleButtonDesktop) &&
                ((toggleButtonMobile && !toggleButtonMobile.contains(e.target)) || !toggleButtonMobile)) {
                this.closeDropdown();
            }
        }

        toggleDropdown(button) {
            const {
                dropdown
            } = this.elements;
            if (!dropdown) return;

            const isHidden = dropdown.classList.contains("hidden");

            if (isHidden) {
                this.positionDropdown(button);
                dropdown.classList.remove("hidden");
                setTimeout(() => {
                    dropdown.classList.remove("opacity-0", "scale-95", "-translate-y-2");
                }, 10);
            } else {
                this.closeDropdown();
            }
        }

        positionDropdown(button) {
            if (window.innerWidth < 1024 || !this.elements.dropdown || !this.elements.sidebar) return;

            const {
                dropdown,
                sidebar
            } = this.elements;
            const buttonRect = button.getBoundingClientRect();
            const sidebarRect = sidebar.getBoundingClientRect();

            dropdown.style.position = 'absolute';
            dropdown.style.top = `${buttonRect.bottom + window.scrollY}px`;
            dropdown.style.right = `${window.innerWidth - (sidebarRect.right + window.scrollX)}px`;
        }

        closeDropdown() {
            const {
                dropdown
            } = this.elements;
            if (!dropdown) return;

            dropdown.classList.add("opacity-0", "scale-95", "-translate-y-2");
            setTimeout(() => dropdown.classList.add("hidden"), 300);
        }

        async fetchNotifications() {
            try {
                const response = await fetch("/timeControl/public/checkNewNotifications");
                const data = await response.json();
                if (data.success) this.processNotifications(data.notificaciones);
            } catch (error) {
                console.error("Error fetching notifications:", error);
            }
        }

        processNotifications(notifications) {
            const {
                list,
                countBadgeDesktop,
                countBadgeMobile
            } = this.elements;
            if (!list) return;

            list.innerHTML = "";

            if (!notifications.length) {
                this.showEmptyState();
                return;
            }

            const currentTime = Date.now();
            let newCount = 0;

            notifications.forEach(notif => {
                const isRead = this.readNotifications.has(notif.id);
                if (!isRead) {
                    newCount++;
                    const notifTime = new Date(notif.created_at).getTime();
                    // Forzar notificación para pruebas o deshacer la condición de tiempo
                    // if (notifTime > this.lastUpdate) {  // Comentar o eliminar esta línea
                    console.log("Mostrando notificación para:", notif.mensaje);
                    this.showBrowserNotification(notif);
                    this.showToastNotification(notif);
                    // }  // Comentar o eliminar esta línea
                }
                this.createNotificationItem(notif, isRead);
            });

            this.updateNotificationInfo(newCount);

            // Update notification badges
            if (countBadgeDesktop) {
                countBadgeDesktop.textContent = newCount;
                countBadgeDesktop.classList.toggle("hidden", newCount === 0);
            }

            if (countBadgeMobile) {
                countBadgeMobile.textContent = newCount;
                countBadgeMobile.classList.toggle("hidden", newCount === 0);
            }

            this.lastUpdate = currentTime;

            // Disparar evento personalizado para notificar a otras páginas
            document.dispatchEvent(new CustomEvent('notificationsUpdated', {
                detail: {
                    count: newCount,
                    notifications
                }
            }));
        }

        updateNotificationInfo(count) {
            const {
                notificationInfo,
                notificationInfoText,
                notificationFooter
            } = this.elements;
            if (!notificationInfo || !notificationInfoText || !notificationFooter) return;

            if (count > 0) {
                notificationInfo.classList.remove("hidden");
                notificationInfoText.textContent = `Tienes ${count} notificación${count !== 1 ? 'es' : ''} sin leer`;
                notificationFooter.classList.add("hidden");
            } else {
                notificationInfo.classList.add("hidden");
                notificationFooter.classList.remove("hidden");
            }
        }

        showEmptyState() {
            const {
                list,
                notificationInfo,
                notificationFooter
            } = this.elements;
            if (!list) return;

            list.innerHTML = `
            <li class="py-8 text-center">
                <i class="fas fa-bell-slash text-3xl text-gray-300 mb-3 block"></i>
                <p class="text-gray-500">No hay notificaciones</p>
            </li>
        `;

            if (notificationInfo) notificationInfo.classList.add("hidden");
            if (notificationFooter) notificationFooter.classList.add("hidden");
        }

        createNotificationItem(notif, isRead) {
            const {
                list
            } = this.elements;
            if (!list) return;

            const timeAgo = this.formatTimeAgo(notif.created_at || new Date());
            const item = document.createElement("li");

            item.classList.add("p-3", "cursor-pointer", isRead ? "bg-gray-100" : "bg-blue-50");
            item.innerHTML = `
            <a href="${notif.redirectUrl}" class="flex justify-between items-center">
                <span class="text-sm text-gray-700">${notif.mensaje}</span>
                <span class="text-xs text-gray-500">${timeAgo}</span>
            </a>
        `;
            item.addEventListener("click", () => this.markAsRead(notif.id));
            list.appendChild(item);
        }

        markAsRead(notificationId) {
            this.readNotifications.add(notificationId);
            localStorage.setItem("readNotifications", JSON.stringify(Array.from(this.readNotifications)));
            this.fetchNotifications();

            // Disparar evento para sincronizar en otras páginas
            document.dispatchEvent(new CustomEvent('notificationRead', {
                detail: {
                    id: notificationId
                }
            }));
        }

        markAllRead() {
            // Implementación para marcar todas las notificaciones como leídas
            const {
                list
            } = this.elements;
            if (!list) return;

            // Obtener todos los items de notificación y marcarlos como leídos
            const items = list.querySelectorAll('li');
            items.forEach(item => {
                const link = item.querySelector('a');
                if (link && link.dataset.id) {
                    this.readNotifications.add(link.dataset.id);
                }
            });

            localStorage.setItem("readNotifications", JSON.stringify(Array.from(this.readNotifications)));
            this.fetchNotifications();

            // Disparar evento para sincronizar en otras páginas
            document.dispatchEvent(new CustomEvent('allNotificationsRead'));
        }

        startAutoRefresh() {
            setInterval(() => this.fetchNotifications(), 30000);
        }

        showBrowserNotification(notif) {
            console.log("Intentando mostrar notificación del navegador:", Notification.permission);

            if (!("Notification" in window)) {
                console.log("Este navegador no soporta notificaciones de escritorio");
                return;
            }

            // Si el permiso ya fue otorgado
            if (Notification.permission === "granted") {
                this._createNotification(notif);
            }
            // Si no se ha pedido permiso aún
            else if (Notification.permission !== "denied") {
                Notification.requestPermission().then(permission => {
                    if (permission === "granted") {
                        this._createNotification(notif);
                    }
                });
            }
        }

        // Función auxiliar para crear la notificación
        _createNotification(notif) {
            try {
                const options = {
                    body: notif.mensaje,
                    icon: "/timeControl/public/assets/img/notification-icon.png",
                    data: {
                        url: notif.redirectUrl
                    },
                    tag: `notif-${notif.id}` // Evita duplicados
                };

                const notification = new Notification("Nueva notificación", options);

                notification.onclick = () => {
                    window.focus();
                    if (notif.redirectUrl) window.location.href = notif.redirectUrl;
                    notification.close();
                };

                setTimeout(() => notification.close(), 5000);
                console.log("Notificación creada exitosamente");
            } catch (error) {
                console.error("Error al crear notificación:", error);
            }
        }

        showToastNotification(notif) {
            const {
                toastContainer
            } = this.elements;

            const toast = document.createElement("div");
            toast.className = "toast-notification bg-white border-l-4 border-blue-500 rounded-lg shadow-lg p-4 flex items-start w-full";
            toast.innerHTML = `
            <i class="fas fa-bell text-blue-500 mt-1 mr-3"></i>
            <div class="flex-1">
                <div class="text-sm font-medium">${notif.mensaje}</div>
                <div class="text-xs text-gray-500 mt-1">Ahora</div>
            </div>
            <button class="text-gray-400 hover:text-gray-600 ml-2 -mt-1">
                <i class="fas fa-times"></i>
            </button>
        `;

            const closeButton = toast.querySelector('button');
            closeButton.addEventListener('click', e => {
                e.stopPropagation();
                toast.remove();
            });

            // Agregar toast al contenedor si existe, o al body si no
            if (toastContainer) {
                toastContainer.appendChild(toast);
            } else {
                // Crear un contenedor si no existe
                const newContainer = document.createElement('div');
                newContainer.id = 'dynamicToastContainer';
                newContainer.className = 'fixed bottom-4 right-4 z-50 space-y-2 max-w-sm';
                newContainer.appendChild(toast);
                document.body.appendChild(newContainer);
            }

            // Auto-eliminar después de 5 segundos
            setTimeout(() => toast.remove(), 5000);
        }

        formatTimeAgo(dateString) {
            const now = new Date();
            const date = new Date(dateString);
            const diff = Math.floor((now - date) / 1000);

            const minutes = Math.floor(diff / 60);
            const hours = Math.floor(diff / 3600);
            const days = Math.floor(diff / 86400);

            if (days > 0) return `${days} day${days > 1 ? 's' : ''} ago`;
            if (hours > 0) return `${hours} hour${hours > 1 ? 's' : ''} ago`;
            return `${minutes} minute${minutes > 1 ? 's' : ''} ago`;
        }
    }

    // Script de inicialización
    document.addEventListener('DOMContentLoaded', () => {
        // Crear una instancia singleton del NotificationManager
        const notificationManager = NotificationManager.getInstance();

        // Escuchar eventos de notificaciones de otras páginas
        document.addEventListener('notificationsUpdated', (e) => {
            // Actualizar la UI si es necesario
            console.log('Notificaciones actualizadas en otra página', e.detail);
        });

        document.addEventListener('notificationRead', (e) => {
            // Sincronizar notificaciones leídas
            const notificationManager = NotificationManager.getInstance();
            notificationManager.readNotifications.add(e.detail.id);
            localStorage.setItem("readNotifications", JSON.stringify(Array.from(notificationManager.readNotifications)));
        });

        document.addEventListener('allNotificationsRead', () => {
            // Refrescar notificaciones cuando se marcan todas como leídas en otra página
            const notificationManager = NotificationManager.getInstance();
            notificationManager.fetchNotifications();
        });
    });


    // Inicialización
    document.addEventListener("DOMContentLoaded", () => {
        new ResponsiveUI();
    });
</script>