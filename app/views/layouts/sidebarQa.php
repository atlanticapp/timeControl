 <!-- Header fijo para móviles -->
 <header class="lg:hidden fixed top-0 left-0 right-0 bg-white shadow-md z-50 px-4 py-3 flex justify-between items-center">
     <button id="toggleSidebar" class="p-2 rounded-lg hover:bg-gray-100 focus:outline-none">
         <i class="fas fa-bars text-blue-600"></i>
     </button>
     <h1 class="text-xl font-bold text-blue-600 flex items-center">
         <i class="fas fa-chart-line mr-2"></i>Panel QA
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
             <i class="fas fa-chart-line mr-3"></i>Panel QA
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
                    ['icon' => 'fas fa-box-open', 'text' => 'Entregas Validadas', 'color' => 'blue', 'route' => 'validacion'],
                    ['icon' => 'fas fa-tasks', 'text' => 'Control de Producción', 'color' => 'green', 'route' => 'produccion'],
                    ['icon' => 'fas fa-chart-pie', 'text' => 'Reportes', 'color' => 'purple', 'route' => 'reportes'],
                    ['icon' => 'fas fa-cog', 'text' => 'Configuración', 'color' => 'gray', 'route' => 'configuracion']
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
 <div id="notificationDropdown" class="notification-dropdown-transition fixed lg:absolute opacity-0 scale-95 -translate-y-2 hidden right-4 lg:right-auto top-16 lg:top-auto lg:mt-2 w-[calc(100%-2rem)] max-w-sm bg-white shadow-2xl border border-gray-100 rounded-lg overflow-hidden z-50">
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
             // Botón de escritorio
             this.elements.toggleButtonDesktop.addEventListener("click", (e) => {
                 e.stopPropagation();
                 this.toggleDropdown(this.elements.toggleButtonDesktop);
             });

             // Botón móvil
             this.elements.toggleButtonMobile.addEventListener("click", (e) => {
                 e.stopPropagation();
                 this.toggleDropdown(this.elements.toggleButtonMobile);
             });

             this.elements.markAllButton.addEventListener("click", (e) => {
                 e.stopPropagation();
                 this.markAllRead();
             });

             document.addEventListener('click', (e) => this.handleDocumentClick(e));

             if ("Notification" in window && Notification.permission !== "granted") {
                 Notification.requestPermission();
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
                 // Use setTimeout to ensure the transition works properly
                 setTimeout(() => {
                     this.elements.dropdown.classList.remove("opacity-0", "scale-95", "-translate-y-2");
                 }, 10);
             } else {
                 this.closeDropdown();
             }
         }

         positionDropdown(button) {
             // Para versión móvil, el dropdown es fijo
             if (window.innerWidth < 1024) {
                 return;
             }

             // Para versión desktop, posicionar dropdown relativo al botón
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
                 // Simulando notificaciones para demo
                 this.processNotifications(this.getDemoNotifications());

                 // En producción usar esto:
                 // const response = await fetch("/timeControl/public/checkNewNotifications");
                 // const data = await response.json();
                 // if (data.success) this.processNotifications(data.notificaciones);
             } catch (error) {
                 console.error("Error fetching notifications:", error);
             }
         }

        //  // Función solo para demostración, eliminar en producción
        //  getDemoNotifications() {
        //      return [{
        //              id: "1",
        //              mensaje: "Nueva entrega validada: Proyecto ABC",
        //              created_at: new Date(Date.now() - 1000 * 60 * 5).toISOString(),
        //              redirectUrl: "#entrega1"
        //          },
        //          {
        //              id: "2",
        //              mensaje: "Reporte semanal disponible",
        //              created_at: new Date(Date.now() - 1000 * 60 * 60).toISOString(),
        //              redirectUrl: "#reporte"
        //          },
        //          {
        //              id: "3",
        //              mensaje: "Tarea asignada por el supervisor",
        //              created_at: new Date(Date.now() - 1000 * 60 * 60 * 4).toISOString(),
        //              redirectUrl: "#tarea"
        //          }
        //      ];
        //  }

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

             // Actualizar información
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
             item.className = `p-3 hover:bg-gray-50 ${isRead ? '' : 'bg-blue-50/40'}`;
             item.innerHTML = `
                    <div class="flex justify-between items-start">
                        <div class="flex-1">
                            <div class="text-sm mb-1 relative pl-2">
                                ${!isRead ? '<span class="absolute left-0 top-2 w-1 h-1 bg-blue-500 rounded-full"></span>' : ''}
                                ${notif.mensaje}
                            </div>
                            <div class="text-xs text-gray-500">
                                <i class="far fa-clock mr-1"></i>${timeAgo}
                            </div>
                        </div>
                        ${!isRead ? `<button class="text-xs text-blue-500 hover:text-blue-600 hover:bg-blue-50 py-1 px-2 rounded transition-colors duration-200" 
                                data-id="${notif.id}">
                                <i class="fas fa-check mr-1"></i>Leído
                            </button>` : ''}
                    </div>
                `;

             if (!isRead) {
                 const markButton = item.querySelector("button");
                 if (markButton) {
                     markButton.addEventListener("click", (e) => {
                         e.preventDefault();
                         e.stopPropagation();
                         this.markAsRead(notif.id);
                     });
                 }
             }

             item.addEventListener("click", () => {
                 if (!isRead) this.markAsRead(notif.id);
                 if (notif.redirectUrl) window.location.href = notif.redirectUrl;
             });

             this.elements.list.appendChild(item);
         }

         formatTimeAgo(dateStr) {
             const date = new Date(dateStr);
             const now = new Date();
             const seconds = Math.floor((now - date) / 1000);

             if (seconds < 60) return "Hace un momento";

             const minutes = Math.floor(seconds / 60);
             if (minutes < 60) return `Hace ${minutes} min`;

             const hours = Math.floor(minutes / 60);
             if (hours < 24) return `Hace ${hours}h`;

             const days = Math.floor(hours / 24);
             if (days < 7) return `Hace ${days}d`;

             return date.toLocaleDateString();
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

             // Añadir interactividad al toast
             toast.addEventListener('click', () => {
                 if (notif.redirectUrl) window.location.href = notif.redirectUrl;
                 this.markAsRead(notif.id);
                 toast.remove();
             });

             this.elements.toastContainer.appendChild(toast);

             setTimeout(() => {
                 toast.style.opacity = '0';
                 setTimeout(() => toast.remove(), 500);
             }, 4500);
         }

         markAsRead(id) {
             if (!this.readNotifications.has(id)) {
                 this.readNotifications.add(id);
                 localStorage.setItem("readNotifications", JSON.stringify([...this.readNotifications]));
                 this.fetchNotifications();
             }
         }

         markAllRead() {
             const unreadItems = this.elements.list.querySelectorAll('li:not(.py-8)');
             unreadItems.forEach(item => {
                 const markButton = item.querySelector('button[data-id]');
                 if (markButton) {
                     const id = markButton.getAttribute('data-id');
                     this.readNotifications.add(id);
                 }
             });

             localStorage.setItem("readNotifications", JSON.stringify([...this.readNotifications]));
             this.fetchNotifications();
         }

         startAutoRefresh() {
             setInterval(() => this.fetchNotifications(), 30000);
             this.fetchNotifications();
         }
     }

     // Inicialización
     document.addEventListener("DOMContentLoaded", () => {
         new ResponsiveUI();
         new NotificationManager();
     });
 </script>