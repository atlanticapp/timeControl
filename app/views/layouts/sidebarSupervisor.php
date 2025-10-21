<aside class="fixed top-0 left-0 w-72 h-full bg-gradient-to-b from-[#5BA4CF] to-[#4A9BC7] text-white shadow-2xl transition-all duration-300 lg:block hidden z-50">
    <div class="flex flex-col h-full">
        <!-- Header -->
        <div class="p-6 border-b border-white/20">
            <div class="flex items-center justify-center mb-4">
                <img src="/timeControl/public/assets/img/logo.png" alt="Control Tiempos Atlantic KPG" class="h-20 w-auto drop-shadow-lg">
            </div>
            <h2 class="text-xl font-bold text-center text-white/95 flex items-center justify-center">
                <i class="fas fa-tachometer-alt mr-2"></i> 
                Panel Supervisor
            </h2>
        </div>

        <!-- Navigation -->
        <nav class="flex-1 p-4 overflow-y-auto">
            <ul class="space-y-2">
                <li>
                    <a href="/timeControl/public/supervisor" 
                       class="flex items-center px-4 py-3 rounded-xl hover:bg-white/20 transition-all duration-300 group backdrop-blur-sm border border-transparent hover:border-white/30 hover:shadow-lg">
                        <div class="w-10 h-10 rounded-lg bg-white/20 flex items-center justify-center mr-3 group-hover:bg-white/30 transition-all duration-300">
                            <i class="fas fa-home text-lg"></i>
                        </div>
                        <span class="font-semibold text-white/95 group-hover:text-white">Dashboard</span>
                    </a>
                </li>
                
                <!-- NUEVO: Enlace a Revisiones -->
                <li>
                    <a href="/timeControl/public/supervisor/revisiones" 
                       class="flex items-center px-4 py-3 rounded-xl hover:bg-white/20 transition-all duration-300 group backdrop-blur-sm border border-transparent hover:border-white/30 hover:shadow-lg">
                        <div class="w-10 h-10 rounded-lg bg-white/20 flex items-center justify-center mr-3 group-hover:bg-white/30 transition-all duration-300">
                            <i class="fas fa-search text-lg"></i>
                        </div>
                        <div class="flex-1">
                            <span class="font-semibold text-white/95 group-hover:text-white block">Revisiones</span>
                            <span class="text-xs text-white/70" id="badge-revisiones">Pendientes</span>
                        </div>
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
                    <?php echo isset($_SESSION['nombre']) ? htmlspecialchars($_SESSION['nombre']) : 'Supervisor'; ?>
                </p>
            </div>
            
            <a href="/timeControl/public/logout" 
               onclick="return confirm('¿Estás seguro de cerrar sesión?')"
               class="flex items-center px-4 py-3 rounded-xl bg-red-500/80 hover:bg-red-600 transition-all duration-300 group shadow-lg hover:shadow-xl">
                <div class="w-10 h-10 rounded-lg bg-white/20 flex items-center justify-center mr-3 group-hover:bg-white/30 transition-all duration-300">
                    <i class="fas fa-sign-out-alt text-lg"></i>
                </div>
                <span class="font-semibold">Cerrar Sesión</span>
            </a>
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
        <div class="p-6 border-b border-white/20 flex items-center justify-between">
            <div class="flex items-center">
                <img src="/timeControl/public/assets/img/logo.png" alt="Control Tiempos" class="h-12 w-auto drop-shadow-lg mr-3">
                <h2 class="text-lg font-bold text-white/95">
                    Supervisor
                </h2>
            </div>
            <button id="mobile-close-btn" class="text-white/80 hover:text-white transition-colors">
                <i class="fas fa-times text-2xl"></i>
            </button>
        </div>

        <!-- Navigation -->
        <nav class="flex-1 p-4 overflow-y-auto">
            <ul class="space-y-2">
                <li>
                    <a href="/timeControl/public/supervisor" 
                       class="flex items-center px-4 py-3 rounded-xl hover:bg-white/20 transition-all duration-300 group">
                        <div class="w-10 h-10 rounded-lg bg-white/20 flex items-center justify-center mr-3">
                            <i class="fas fa-home text-lg"></i>
                        </div>
                        <span class="font-semibold">Dashboard</span>
                    </a>
                </li>
                
                <!-- NUEVO: Enlace a Revisiones (Mobile) -->
                <li>
                    <a href="/timeControl/public/supervisor/revisiones" 
                       class="flex items-center px-4 py-3 rounded-xl hover:bg-white/20 transition-all duration-300 group">
                        <div class="w-10 h-10 rounded-lg bg-white/20 flex items-center justify-center mr-3">
                            <i class="fas fa-search text-lg"></i>
                        </div>
                        <div class="flex-1">
                            <span class="font-semibold block">Revisiones</span>
                            <span class="text-xs text-white/70">Pendientes</span>
                        </div>
                    </a>
                </li>
            </ul>
        </nav>

        <!-- User Info (Mobile) -->
        <div class="p-4 border-t border-white/20 bg-[#4A4A4A]/30">
            <div class="mb-3 px-4 py-2 bg-white/10 rounded-lg">
                <p class="text-xs text-white/70 mb-1">Usuario actual</p>
                <p class="text-sm font-semibold text-white flex items-center">
                    <i class="fas fa-user-circle mr-2"></i>
                    <?php echo isset($_SESSION['nombre']) ? htmlspecialchars($_SESSION['nombre']) : 'Supervisor'; ?>
                </p>
            </div>
            
            <a href="/timeControl/public/logout" 
               onclick="return confirm('¿Estás seguro de cerrar sesión?')"
               class="flex items-center px-4 py-3 rounded-xl bg-red-500/80 hover:bg-red-600 transition-all duration-300 shadow-lg">
                <div class="w-10 h-10 rounded-lg bg-white/20 flex items-center justify-center mr-3">
                    <i class="fas fa-sign-out-alt text-lg"></i>
                </div>
                <span class="font-semibold">Cerrar Sesión</span>
            </a>
        </div>
    </div>
</aside>

<script>
    // Mobile menu functionality
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

        // Close mobile menu when clicking on a link
        const mobileLinks = mobileSidebar?.querySelectorAll('a');
        mobileLinks?.forEach(link => {
            link.addEventListener('click', closeMobileMenu);
        });
    });
</script>