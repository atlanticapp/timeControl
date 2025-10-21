
<?php
$menuItems = [
    [
        'icon' => 'fas fa-tachometer-alt',
        'text' => 'Dashboard',
        'color' => 'white',
        'route' => 'dashboard'
    ],
    ['icon' => 'fas fa-check-circle', 'text' => 'Validación de Entregas', 'color' => 'white', 'route' => 'validacion'],
    ['icon' => 'fas fa-clipboard-check', 'text' => 'Acción QA', 'color' => 'white', 'route' => 'accion'],
    ['icon' => 'fas fa-exclamation-triangle', 'text' => 'Retenciones', 'color' => 'white', 'route' => 'retenciones'],
    ['icon' => 'fas fa-search', 'text' => 'Revisiones Pendientes', 'color' => 'white', 'route' => 'revisiones'],
    ['icon' => 'fas fa-file-alt', 'text' => 'Reporte de Entrega', 'color' => 'white', 'route' => 'reporte-entrega'],
    ['icon' => 'fas fa-trash-alt', 'text' => 'Reporte Scrap', 'color' => 'white', 'route' => 'reporte_scrap'],
    ['icon' => 'fas fa-circle-xmark', 'text' => 'Destino Destruccion', 'color' => 'white', 'route' => 'destinos/destruccion'],
    ['icon' => 'fas fa-box', 'text' => 'Destino Produccion', 'color' => 'white', 'route' => 'destinos/produccion'],
    ['icon' => 'fas fa-recycle', 'text' => 'Destino Retrabajo', 'color' => 'white', 'route' => 'destinos/retrabajo']



];
?>

<!-- Desktop Sidebar -->
<aside class="fixed top-0 left-0 w-72 h-full bg-gradient-to-b from-[#5BA4CF] to-[#4A9BC7] text-white shadow-2xl transition-all duration-300 lg:block hidden z-50">
    <div class="flex flex-col h-full">
        <!-- Header -->
        <div class="p-6 border-b border-white/20">
            <div class="flex items-center justify-center mb-4">
                <img src="/timeControl/public/assets/img/logo.png" alt="Control Tiempos Atlantic KPG" class="h-20 w-auto drop-shadow-lg">
            </div>
            <h2 class="text-xl font-bold text-center text-white/95 flex items-center justify-center">
                <i class="fas fa-chart-line mr-2"></i> 
                Panel QA
            </h2>
        </div>

        <!-- Navigation -->
        <nav class="flex-1 p-4 overflow-y-auto">
            <ul class="space-y-2">
                <?php foreach ($menuItems as $item): ?>
                    <li>
                        <a href="/timeControl/public/<?= $item['route'] ?>" 
                           class="flex items-center px-4 py-3 rounded-xl hover:bg-white/20 transition-all duration-300 group backdrop-blur-sm border border-transparent hover:border-white/30 hover:shadow-lg">
                            <div class="w-10 h-10 rounded-lg bg-white/20 flex items-center justify-center mr-3 group-hover:bg-white/30 transition-all duration-300">
                                <i class="<?= $item['icon'] ?> text-lg"></i>
                            </div>
                            <span class="font-semibold text-white/95 group-hover:text-white"><?= $item['text'] ?></span>
                        </a>
                    </li>
                <?php endforeach; ?>
                <li>
                    <a href="/timeControl/public/logout" 
                       onclick="return confirm('¿Estás seguro de cerrar sesión?')"
                       class="flex items-center px-4 py-3 rounded-xl bg-red-500/80 hover:bg-red-600 transition-all duration-300 group shadow-lg hover:shadow-xl">
                        <div class="w-10 h-10 rounded-lg bg-white/20 flex items-center justify-center mr-3 group-hover:bg-white/30 transition-all duration-300">
                            <i class="fas fa-sign-out-alt text-lg"></i>
                        </div>
                        <span class="font-semibold">Cerrar Sesión</span>
                    </a>
                </li>
            </ul>
        </nav>

        <!-- User Info -->
        <div class="p-4 border-t border-white/20 bg-[#4A4A4A]/30 backdrop-blur-sm">
            <div class="mb-3 px-4 py-2 bg-white/10 rounded-lg">
                <p class="text-xs text-white/70 mb-1">Usuario actual</p>
                <p class="text-sm font-semibold text-white flex items-center">
                    <i class="fas fa-user-circle mr-2"></i>
                    <?php echo isset($_SESSION['nombre']) ? htmlspecialchars($_SESSION['nombre']) : 'Usuario QA'; ?>
                </p>
            </div>
            <div class="relative">
                <button id="toggleNotificationsDesktop" class="notification-button relative focus:outline-none p-2 rounded-full hover:bg-white/20 transition duration-300 w-full flex items-center">
                    <div class="w-10 h-10 rounded-lg bg-white/20 flex items-center justify-center mr-3">
                        <i class="fas fa-bell text-lg"></i>
                    </div>
                    <span class="font-semibold">Notificaciones</span>
                    <span id="notificationCountDesktop" class="absolute -top-1 -right-1 bg-red-500 text-white text-xs font-bold w-5 h-5 flex items-center justify-center rounded-full hidden shadow-md">0</span>
                </button>
            </div>
        </div>
    </div>
</aside>

<!-- Mobile Menu Button -->
<button id="mobile-menu-btn" class="lg:hidden fixed top-4 left-4 z-50 bg-gradient-to-br from-[#5BA4CF] to-[#4A9BC7] text-white p-3 rounded-xl shadow-lg hover:shadow-xl transition-all duration-300">
    <i class="fas fa-bars text-xl"></i>
</button>

<!-- Mobile Sidebar Overlay -->
<div id="mobile-sidebar-overlay" class="lg:hidden fixed inset-0 bg-black/50 backdrop-blur-sm z-40 hidden transition-opacity duration-300"></div>

<!-- Mobile Sidebar -->
<aside id="mobile-sidebar" class="lg:hidden fixed top-0 left-0 w-72 h-full bg-gradient-to-b from-[#5BA4CF] to-[#4A9BC7] text-white shadow-2xl transition-transform duration-300 transform -translate-x-full z-50">
    <div class="flex flex-col h-full">
        <!-- Header -->
        <div class="p-6 border-b border-white/20">
            <div class="flex items-center justify-between mb-4">
                <div class="flex items-center">
                    <img src="/timeControl/public/assets/img/logo.png" alt="Control Tiempos" class="h-12 w-auto drop-shadow-lg mr-3">
                    <h2 class="text-lg font-bold text-white/95">
                        Panel QA
                    </h2>
                </div>
                <button id="mobile-close-btn" class="text-white/80 hover:text-white transition-colors">
                    <i class="fas fa-times text-2xl"></i>
                </button>
            </div>
            <!-- Notification Button and Dropdown -->
            <div class="relative">
                <button id="toggleNotificationsMobile" class="notification-button relative focus:outline-none p-2 rounded-xl hover:bg-white/20 transition duration-300 w-full flex items-center">
                    <div class="w-10 h-10 rounded-lg bg-white/20 flex items-center justify-center mr-3">
                        <i class="fas fa-bell text-lg"></i>
                    </div>
                    <span class="font-semibold">Notificaciones</span>
                    <span id="notificationCountMobile" class="absolute -top-1 -right-1 bg-red-500 text-white text-xs font-bold w-5 h-5 flex items-center justify-center rounded-full hidden shadow-md">0</span>
                </button>
                <div id="notificationDropdown" class="notification-dropdown-transition absolute left-0 right-0 mt-2 w-full bg-white shadow-2xl border border-gray-100 rounded-lg overflow-hidden hidden z-50">
                    <div class="py-3 px-4 bg-gradient-to-r from-[#5BA4CF] to-[#4A9BC7] text-white font-semibold flex justify-between items-center">
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
            </div>
        </div>

        <!-- Navigation -->
        <nav class="flex-1 p-4 overflow-y-auto">
            <ul class="space-y-2">
                <?php foreach ($menuItems as $item): ?>
                    <li>
                        <a href="/timeControl/public/<?= $item['route'] ?>" 
                           class="flex items-center px-4 py-3 rounded-xl hover:bg-white/20 transition-all duration-300 group">
                            <div class="w-10 h-10 rounded-lg bg-white/20 flex items-center justify-center mr-3">
                                <i class="<?= $item['icon'] ?> text-lg"></i>
                            </div>
                            <span class="font-semibold"><?= $item['text'] ?></span>
                        </a>
                    </li>
                <?php endforeach; ?>
                <li>
                    <a href="/timeControl/public/logout" 
                       onclick="return confirm('¿Estás seguro de cerrar sesión?')"
                       class="flex items-center px-4 py-3 rounded-xl bg-red-500/80 hover:bg-red-600 transition-all duration-300 shadow-lg">
                        <div class="w-10 h-10 rounded-lg bg-white/20 flex items-center justify-center mr-3">
                            <i class="fas fa-sign-out-alt text-lg"></i>
                        </div>
                        <span class="font-semibold">Cerrar Sesión</span>
                    </a>
                </li>
            </ul>
        </nav>
    </div>
</aside>

<!-- Toast Container -->
<div id="toastContainer" class="fixed bottom-6 right-6 space-y-3 z-50 w-auto max-w-xs"></div>


<!-- Scripts para funcionalidad -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const mobileMenuBtn = document.getElementById('mobile-menu-btn');
    const mobileSidebar = document.getElementById('mobile-sidebar');
    const mobileOverlay = document.getElementById('mobile-sidebar-overlay');
    const mobileCloseBtn = document.getElementById('mobile-close-btn');

    function openMobileMenu() {
        mobileSidebar.classList.remove('-translate-x-full');
        mobileOverlay.classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }

    function closeMobileMenu() {
        mobileSidebar.classList.add('-translate-x-full');
        mobileOverlay.classList.add('hidden');
        document.body.style.overflow = '';
        // Cerrar el dropdown de notificaciones al cerrar el sidebar
        const dropdown = document.getElementById('notificationDropdown');
        if (dropdown) {
            dropdown.classList.add('hidden');
            dropdown.classList.add('opacity-0', 'scale-95', '-translate-y-2');
        }
    }

    if (mobileMenuBtn) {
        mobileMenuBtn.addEventListener('click', openMobileMenu);
    }

    if (mobileCloseBtn) {
        mobileCloseBtn.addEventListener('click', closeMobileMenu);
    }

    if (mobileOverlay) {
        mobileOverlay.addEventListener('click', closeMobileMenu);
    }

    const mobileLinks = mobileSidebar?.querySelectorAll('a');
    mobileLinks?.forEach(link => {
        link.addEventListener('click', closeMobileMenu);
    });

    // Actualizar la fecha y hora
    function updateDateTime() {
        const now = new Date();
        const dateStr = now.toLocaleDateString() + ' ' + now.toLocaleTimeString();
        const currentDateElement = document.getElementById('current-date');
        if (currentDateElement) {
            currentDateElement.textContent = dateStr;
        }
    }

    setInterval(updateDateTime, 1000);
    updateDateTime();
});
</script>
<script src="assets/js/qa/sliderbarQA.js"></script>


