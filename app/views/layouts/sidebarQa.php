<script src="https://cdn.tailwindcss.com"></script>
<!-- Header fijo para móviles -->
<header class="lg:hidden fixed top-0 left-0 right-0 bg-white shadow-md z-50 px-4 py-3 flex justify-between items-center">
    <button id="toggleSidebar" class="p-2 rounded-lg hover:bg-gray-100 focus:outline-none">
        <i class="fas fa-bars text-blue-600"></i>
    </button>
    <h1 class="text-xl font-bold text-blue-600 flex items-center">
        <i class="fas fa-chart-line mr-2"></i> <a href="/timeControl/public/dashboard">Panel QA</a>
    </h1>
    <div class="relative">
        <!-- Botón de notificaciones móvil -->
        <button id="toggleNotificationsMobile" class="notification-button relative focus:outline-none p-2 rounded-full hover:bg-gray-200 transition duration-200">
            <i class="fas fa-bell text-gray-700 text-xl"></i>
            <span id="notificationCountMobile" class="absolute -top-1 -right-1 bg-red-500 text-white text-xs font-bold w-5 h-5 flex items-center justify-center rounded-full hidden shadow-md">0</span>
        </button>
    </div>
</header>

<!-- Overlay para cerrar el sidebar en modo móvil -->
<div id="sidebarOverlay" class="fixed inset-0 bg-black bg-opacity-50 z-30 hidden lg:hidden"></div>

<!-- Sidebar mejorado -->
<div id="sidebar" class="sidebar-transition w-full max-w-xs lg:w-64 bg-white shadow-xl h-screen fixed left-0 top-0 -translate-x-full lg:translate-x-0 z-40 custom-scrollbar overflow-y-auto">
    <div class="p-5 border-b border-gray-200 flex justify-between items-center">
        <h2 class="text-2xl font-bold text-blue-600 flex items-center">
            <i class="fas fa-chart-line mr-3"></i><a href="/timeControl/public/dashboard">Panel QA</a>
        </h2>
        <div class="relative flex justify-end">
            <!-- Botón de notificaciones desktop -->
            <button id="toggleNotificationsDesktop" class="notification-button relative focus:outline-none p-2 rounded-full hover:bg-gray-200 transition duration-200" aria-label="Mostrar notificaciones">
                <i class="fas fa-bell text-gray-700 text-xl"></i>
                <span id="notificationCountDesktop" class="absolute -top-1 -right-1 bg-red-500 text-white text-xs font-bold w-5 h-5 flex items-center justify-center rounded-full hidden shadow-md">0</span>
            </button>

            <button id="closeSidebar" class="lg:hidden p-2 rounded-full hover:bg-gray-200 transition duration-200 ml-2">
                <i class="fas fa-times text-gray-700 text-xl"></i>
            </button>
        </div>
    </div>

    <!-- Menú de Navegación -->
    <nav class="p-4">
        <ul class="space-y-2">
            <?php
            $menuItems = [
                ['icon' => 'fas fa-check-circle', 'text' => 'Validación de Entregas', 'color' => 'teal', 'route' => 'validacion'],
                ['icon' => 'fas fa-clipboard-check', 'text' => 'Acción QA', 'color' => 'amber', 'route' => 'accion'],
                ['icon' => 'fas fa-exclamation-triangle', 'text' => 'Revisiones Pendientes', 'color' => 'rose', 'route' => 'revisiones-pendientes']
            ];
            foreach ($menuItems as $item): ?>
                <li>
                    <a href="/timeControl/public/<?= $item['route'] ?>" class="menu-item flex items-center p-3 text-gray-700 hover:bg-<?= $item['color'] ?>-50 rounded-lg transition-colors duration-300 group">
                        <i class="<?= $item['icon'] ?> mr-3 text-<?= $item['color'] ?>-500 group-hover:text-<?= $item['color'] ?>-600"></i>
                        <span class="font-medium group-hover:text-<?= $item['color'] ?>-600"><?= $item['text'] ?></span>
                    </a>
                </li>
            <?php endforeach; ?>
        </ul>
    </nav>

</div>

<!-- Panel de Notificaciones Optimizado -->
<div id="notificationDropdown" class="notification-dropdown-transition fixed lg:absolute opacity-0 scale-95 -translate-y-2 hidden left-4 lg:right-auto top-16 lg:top-auto lg:mt-2 w-[calc(100%-2rem)] max-w-sm bg-white shadow-2xl border border-gray-100 rounded-lg overflow-hidden z-50">
    <div class="py-3 px-4 bg-gradient-to-r from-blue-50 to-blue-100 text-blue-700 font-semibold flex justify-between items-center border-b border-gray-100">
        <div class="flex items-center">
            <i class="fas fa-bell mr-2"></i>
            <span>Notificaciones</span>
        </div>
        <button id="markAllRead" class="text-blue-600 text-sm hover:bg-blue-50 py-1 px-2 rounded transition-colors duration-200" aria-label="Marcar todas como leídas">
            <i class="fas fa-check-double mr-1"></i>Marcar todo
        </button>
    </div>
    <div class="notification-header-info bg-blue-50 px-4 py-2 text-xs text-blue-600 border-b border-gray-100 hidden" id="notificationInfo">
        <i class="fas fa-info-circle mr-1"></i>
        <span id="notificationInfoText">Tienes notificaciones no leídas</span>
    </div>
    <ul id="notificationList" class="max-h-72 overflow-y-auto divide-y divide-gray-100 custom-scrollbar"></ul>
    <div class="p-3 bg-gray-50 text-center border-t border-gray-100 text-sm text-gray-500" id="notificationFooter">
        No hay nuevas notificaciones
    </div>
</div>


<!-- Contenedor de Toasts Mejorado -->
<div id="toastContainer" class="fixed bottom-4 right-4 md:right-6 space-y-2 z-50 w-auto max-w-xs"></div>



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
        constructor() {
            this.readNotifications = new Set(JSON.parse(localStorage.getItem("readNotifications") || "[]"));
            this.lastUpdate = new Date().getTime();
            this.initElements();
            this.initEventListeners();
            this.startAutoRefresh();
        }

        initElements() {
            this.elements = {
                toggleButtonDesktop: document.getElementById("toggleNotificationsDesktop"),
                toggleButtonMobile: document.getElementById("toggleNotificationsMobile"),
                countBadgeDesktop: document.getElementById("notificationCountDesktop"),
                countBadgeMobile: document.getElementById("notificationCountMobile"),
                dropdown: document.getElementById("notificationDropdown"),
                list: document.getElementById("notificationList"),
                markAllButton: document.getElementById("markAllRead"),
                toastContainer: document.getElementById("toastContainer"),
                notificationInfo: document.getElementById("notificationInfo"),
                notificationInfoText: document.getElementById("notificationInfoText"),
                notificationFooter: document.getElementById("notificationFooter"),
                sidebar: document.getElementById("sidebar")
            };
        }

        initEventListeners() {
            this.elements.toggleButtonDesktop.addEventListener("click", (e) => {
                e.stopPropagation();
                this.toggleDropdown(this.elements.toggleButtonDesktop);
            });

            this.elements.toggleButtonMobile.addEventListener("click", (e) => {
                e.stopPropagation();
                this.toggleDropdown(this.elements.toggleButtonMobile);
            });

            this.elements.markAllButton.addEventListener("click", (e) => {
                e.stopPropagation();
                this.markAllRead();
            });

            document.addEventListener('click', (e) => this.handleDocumentClick(e));

            if ("Notification" in window) {
                Notification.requestPermission().then(permission => {
                    if (permission === "granted") {
                        console.log("Permiso otorgado para notificaciones.");
                    } else {
                        console.log("Permiso denegado o no otorgado.");
                    }
                });
            }

        }

        handleDocumentClick(e) {
            if (!this.elements.dropdown.contains(e.target) &&
                !this.elements.toggleButtonDesktop.contains(e.target) &&
                !this.elements.toggleButtonMobile.contains(e.target)) {
                this.closeDropdown();
            }
        }

        toggleDropdown(button) {
            const isHidden = this.elements.dropdown.classList.contains("hidden");

            if (isHidden) {
                this.positionDropdown(button);
                this.elements.dropdown.classList.remove("hidden");
                setTimeout(() => {
                    this.elements.dropdown.classList.remove("opacity-0", "scale-95", "-translate-y-2");
                }, 10);
            } else {
                this.closeDropdown();
            }
        }

        positionDropdown(button) {
            if (window.innerWidth < 1024) {
                return;
            }

            const buttonRect = button.getBoundingClientRect();
            const sidebar = this.elements.sidebar;
            const sidebarRect = sidebar.getBoundingClientRect();

            this.elements.dropdown.style.position = 'absolute';
            this.elements.dropdown.style.top = `${buttonRect.bottom + window.scrollY}px`;
            this.elements.dropdown.style.right = `${window.innerWidth - (sidebarRect.right + window.scrollX)}px`;
        }

        closeDropdown() {
            this.elements.dropdown.classList.add("opacity-0", "scale-95", "-translate-y-2");

            setTimeout(() => {
                this.elements.dropdown.classList.add("hidden");
            }, 300);
        }

        async fetchNotifications() {
            try {
                // Realizar la consulta de notificaciones en producción
                const response = await fetch("/timeControl/public/checkNewNotifications");
                const data = await response.json();
                if (data.success) this.processNotifications(data.notificaciones);
            } catch (error) {
                console.error("Error fetching notifications:", error);
            }
        }

        processNotifications(notifications) {
            let newCount = 0;
            this.elements.list.innerHTML = "";

            if (notifications.length === 0) {
                this.showEmptyState();
                return;
            }

            notifications.forEach(notif => {
                const isRead = this.readNotifications.has(notif.id);
                if (!isRead) {
                    newCount++;

                    // Solo mostrar notificaciones si son nuevas desde la última actualización
                    const notifTime = new Date(notif.created_at).getTime();
                    if (notifTime > this.lastUpdate) {
                        this.showBrowserNotification(notif);
                        this.showToastNotification(notif);
                    }
                }
                this.createNotificationItem(notif, isRead);
            });

            this.updateNotificationInfo(newCount);
            this.elements.countBadgeDesktop.textContent = newCount;
            this.elements.countBadgeDesktop.classList.toggle("hidden", newCount === 0);
            this.elements.countBadgeMobile.textContent = newCount;
            this.elements.countBadgeMobile.classList.toggle("hidden", newCount === 0);

            this.lastUpdate = new Date().getTime();
        }

        updateNotificationInfo(count) {
            if (count > 0) {
                this.elements.notificationInfo.classList.remove("hidden");
                this.elements.notificationInfoText.textContent = `Tienes ${count} notificación${count !== 1 ? 'es' : ''} sin leer`;
                this.elements.notificationFooter.classList.add("hidden");
            } else {
                this.elements.notificationInfo.classList.add("hidden");
                this.elements.notificationFooter.classList.remove("hidden");
            }
        }

        showEmptyState() {
            this.elements.list.innerHTML = `
            <li class="py-8 text-center">
                <i class="fas fa-bell-slash text-3xl text-gray-300 mb-3 block"></i>
                <p class="text-gray-500">No hay notificaciones</p>
            </li>
        `;
            this.elements.notificationInfo.classList.add("hidden");
            this.elements.notificationFooter.classList.add("hidden");
        }

        createNotificationItem(notif, isRead) {
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
            this.elements.list.appendChild(item);
        }

        markAsRead(notificationId) {
            this.readNotifications.add(notificationId);
            localStorage.setItem("readNotifications", JSON.stringify(Array.from(this.readNotifications)));
            this.processNotifications([]); // Re-fetch to update UI
        }

        startAutoRefresh() {
            setInterval(() => {
                this.fetchNotifications(); // Auto-refresh every 30 seconds
            }, 30000);
        }

        showBrowserNotification(notif) {
            if (Notification.permission === "granted") {
                const notification = new Notification("Nueva notificación", {
                    body: notif.mensaje,
                    icon: "/timeControl/public/assets/img/notification-icon.png",
                    data: {
                        url: notif.redirectUrl
                    }
                });

                notification.onclick = () => {
                    window.focus();
                    if (notif.redirectUrl) window.location.href = notif.redirectUrl;
                    notification.close();
                };

                // Auto cerrar después de 5 segundos
                setTimeout(() => notification.close(), 5000);
            }
        }

        showToastNotification(notif) {
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
            closeButton.addEventListener('click', (e) => {
                e.stopPropagation();
                toast.remove();
            });
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


    // Inicialización
    document.addEventListener("DOMContentLoaded", () => {
        new ResponsiveUI();
        new NotificationManager();
    });
</script>