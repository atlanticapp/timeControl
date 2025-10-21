class NotificationManager {
    static instance;

    static getInstance() {
        if (!NotificationManager.instance) {
            NotificationManager.instance = new NotificationManager();
        }
        return NotificationManager.instance;
    }

    constructor() {
        if (NotificationManager.instance) {
            return NotificationManager.instance;
        }

        this.readNotifications = new Set(JSON.parse(localStorage.getItem("readNotifications") || "[]"));
        this.lastUpdate = Date.now();

        if (this.checkRequiredElements()) {
            this.elements = this.initElements();
            this.initEventListeners();
            this.startAutoRefresh();
        }
    }

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
            sidebar: document.getElementById("mobile-sidebar") || null
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
        const { dropdown, toggleButtonDesktop, toggleButtonMobile } = this.elements;
        if (dropdown &&
            !dropdown.contains(e.target) &&
            ((toggleButtonDesktop && !toggleButtonDesktop.contains(e.target)) || !toggleButtonDesktop) &&
            ((toggleButtonMobile && !toggleButtonMobile.contains(e.target)) || !toggleButtonMobile)) {
            this.closeDropdown();
        }
    }

    toggleDropdown(button) {
        const { dropdown } = this.elements;
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
        const { dropdown, sidebar } = this.elements;
        if (!dropdown || !sidebar) return;

        if (window.innerWidth < 1024) {
            // En móvil, el dropdown está dentro del sidebar, no necesita reposicionamiento
            dropdown.style.position = 'relative';
            dropdown.style.top = 'auto';
            dropdown.style.right = 'auto';
        } else {
            // En escritorio, posicionar debajo del botón
            const buttonRect = button.getBoundingClientRect();
            const sidebarRect = sidebar.getBoundingClientRect();
            dropdown.style.position = 'absolute';
            dropdown.style.top = `${buttonRect.bottom + window.scrollY}px`;
            dropdown.style.right = `${window.innerWidth - (sidebarRect.right + window.scrollX)}px`;
        }
    }

    closeDropdown() {
        const { dropdown } = this.elements;
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
        const { list, countBadgeDesktop, countBadgeMobile } = this.elements;
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
                this.showBrowserNotification(notif);
                this.showToastNotification(notif);
            }
            this.createNotificationItem(notif, isRead);
        });

        this.updateNotificationInfo(newCount);

        if (countBadgeDesktop) {
            countBadgeDesktop.textContent = newCount;
            countBadgeDesktop.classList.toggle("hidden", newCount === 0);
        }

        if (countBadgeMobile) {
            countBadgeMobile.textContent = newCount;
            countBadgeMobile.classList.toggle("hidden", newCount === 0);
        }

        this.lastUpdate = currentTime;

        document.dispatchEvent(new CustomEvent('notificationsUpdated', {
            detail: { count: newCount, notifications }
        }));
    }

    updateNotificationInfo(count) {
        const { notificationInfo, notificationInfoText, notificationFooter } = this.elements;
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
        const { list, notificationInfo, notificationFooter } = this.elements;
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
        const { list } = this.elements;
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

        document.dispatchEvent(new CustomEvent('notificationRead', {
            detail: { id: notificationId }
        }));
    }

    markAllRead() {
        const { list } = this.elements;
        if (!list) return;

        const items = list.querySelectorAll('li');
        items.forEach(item => {
            const link = item.querySelector('a');
            if (link && link.dataset.id) {
                this.readNotifications.add(link.dataset.id);
            }
        });

        localStorage.setItem("readNotifications", JSON.stringify(Array.from(this.readNotifications)));
        this.fetchNotifications();

        document.dispatchEvent(new CustomEvent('allNotificationsRead'));
    }

    startAutoRefresh() {
        setInterval(() => this.fetchNotifications(), 30000);
    }

    showBrowserNotification(notif) {
        if (!("Notification" in window)) {
            console.log("Este navegador no soporta notificaciones de escritorio");
            return;
        }

        if (Notification.permission === "granted") {
            this._createNotification(notif);
        } else if (Notification.permission !== "denied") {
            Notification.requestPermission().then(permission => {
                if (permission === "granted") {
                    this._createNotification(notif);
                }
            });
        }
    }

    _createNotification(notif) {
        try {
            const options = {
                body: notif.mensaje,
                icon: "/timeControl/public/assets/img/notification-icon.png",
                data: { url: notif.redirectUrl },
                tag: `notif-${notif.id}`
            };

            const notification = new Notification("Nueva notificación", options);

            notification.onclick = () => {
                window.focus();
                if (notif.redirectUrl) window.location.href = notif.redirectUrl;
                notification.close();
            };

            setTimeout(() => notification.close(), 5000);
        } catch (error) {
            console.error("Error al crear notificación:", error);
        }
    }

    showToastNotification(notif) {
        const { toastContainer } = this.elements;

        const toast = document.createElement("div");
        toast.className = "toast-notification bg-white border-l-4 border-[#5BA4CF] rounded-lg shadow-lg p-4 flex items-start w-full";
        toast.innerHTML = `
            <i class="fas fa-bell text-[#5BA4CF] mt-1 mr-3"></i>
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

        if (toastContainer) {
            toastContainer.appendChild(toast);
        } else {
            const newContainer = document.createElement('div');
            newContainer.id = 'dynamicToastContainer';
            newContainer.className = 'fixed bottom-4 right-4 z-50 space-y-2 max-w-sm';
            newContainer.appendChild(toast);
            document.body.appendChild(newContainer);
        }

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

// Inicialización
document.addEventListener('DOMContentLoaded', () => {
    const notificationManager = NotificationManager.getInstance();

    document.addEventListener('notificationsUpdated', (e) => {
        console.log('Notificaciones actualizadas en otra página', e.detail);
    });

    document.addEventListener('notificationRead', (e) => {
        const notificationManager = NotificationManager.getInstance();
        notificationManager.readNotifications.add(e.detail.id);
        localStorage.setItem("readNotifications", JSON.stringify(Array.from(notificationManager.readNotifications)));
    });

    document.addEventListener('allNotificationsRead', () => {
        const notificationManager = NotificationManager.getInstance();
        notificationManager.fetchNotifications();
    });
});