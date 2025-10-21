<?php
// Sidebar para el Operador - Control Tiempos Atlantic KPG
// Obtener la página actual para marcar el elemento activo
$current_page = basename($_SERVER['PHP_SELF'], '.php');
?>

<style>
    /* Sidebar Styles */
    .sidebar {
        position: fixed;
        top: 0;
        left: 0;
        height: 100vh;
        width: 280px;
        background: linear-gradient(180deg, #5BA4CF 0%, #4A9BC7 100%);
        box-shadow: 4px 0 10px rgba(0, 0, 0, 0.1);
        z-index: 1000;
        transition: transform 0.3s ease;
        display: flex;
        flex-direction: column;
    }

    /* Logo Section */
    .sidebar-logo {
        padding: 25px 20px;
        text-align: center;
        background: rgba(255, 255, 255, 0.1);
        border-bottom: 2px solid rgba(255, 255, 255, 0.2);
    }

    .sidebar-logo img {
        max-width: 180px;
        height: auto;
        filter: drop-shadow(0 2px 4px rgba(0, 0, 0, 0.1));
    }

    /* Navigation */
    .sidebar-nav {
        flex: 1;
        padding: 20px 0;
        overflow-y: auto;
    }

    .sidebar-nav ul {
        list-style: none;
        padding: 0;
        margin: 0;
    }

    .sidebar-nav li {
        margin: 8px 15px;
    }

    .sidebar-nav a {
        display: flex;
        align-items: center;
        padding: 14px 18px;
        color: white;
        text-decoration: none;
        border-radius: 10px;
        transition: all 0.3s ease;
        font-weight: 500;
        font-size: 15px;
    }

    .sidebar-nav a:hover {
        background: rgba(255, 255, 255, 0.15);
        transform: translateX(5px);
    }

    .sidebar-nav a.active {
        background: rgba(255, 255, 255, 0.25);
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
    }

    .sidebar-nav a i {
        margin-right: 12px;
        font-size: 20px;
        width: 24px;
        text-align: center;
    }

    /* User Info Footer */
    .sidebar-footer {
        padding: 20px;
        background: rgba(0, 0, 0, 0.1);
        border-top: 2px solid rgba(255, 255, 255, 0.2);
    }

    .user-info {
        color: white;
        margin-bottom: 15px;
        padding: 12px;
        background: rgba(255, 255, 255, 0.1);
        border-radius: 8px;
    }

    .user-info p {
        margin: 5px 0;
        font-size: 13px;
        display: flex;
        align-items: center;
    }

    .user-info p i {
        margin-right: 8px;
        font-size: 14px;
    }

    .user-info strong {
        font-weight: 600;
    }

    .logout-btn {
        width: 100%;
        padding: 12px;
        background: rgba(220, 53, 69, 0.9);
        color: white;
        border: none;
        border-radius: 8px;
        cursor: pointer;
        transition: all 0.3s ease;
        font-weight: 500;
        display: flex;
        align-items: center;
        justify-content: center;
        text-decoration: none;
    }

    .logout-btn:hover {
        background: rgba(220, 53, 69, 1);
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
    }

    .logout-btn i {
        margin-right: 8px;
    }

    /* Mobile Toggle Button */
    .sidebar-toggle {
        position: fixed;
        top: 20px;
        left: 20px;
        z-index: 1001;
        background: linear-gradient(135deg, #5BA4CF, #4A9BC7);
        color: white;
        border: none;
        width: 50px;
        height: 50px;
        border-radius: 12px;
        cursor: pointer;
        display: none;
        align-items: center;
        justify-content: center;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        transition: all 0.3s ease;
    }

    .sidebar-toggle:hover {
        transform: scale(1.05);
        box-shadow: 0 6px 16px rgba(0, 0, 0, 0.2);
    }

    .sidebar-toggle i {
        font-size: 24px;
    }

    /* Overlay for mobile */
    .sidebar-overlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.5);
        z-index: 999;
        display: none;
        opacity: 0;
        transition: opacity 0.3s ease;
    }

    .sidebar-overlay.active {
        display: block;
        opacity: 1;
    }

    /* Main Content Adjustment */
    .main-content {
        margin-left: 280px;
        transition: margin-left 0.3s ease;
        min-height: 100vh;
        padding: 20px;
    }

    /* Mobile Responsive */
    @media (max-width: 768px) {
        .sidebar {
            transform: translateX(-100%);
        }

        .sidebar.active {
            transform: translateX(0);
        }

        .sidebar-toggle {
            display: flex;
        }

        .main-content {
            margin-left: 0;
        }

        .sidebar-toggle.active {
            left: 300px;
        }
    }

    /* Scrollbar Styling */
    .sidebar-nav::-webkit-scrollbar {
        width: 6px;
    }

    .sidebar-nav::-webkit-scrollbar-track {
        background: rgba(255, 255, 255, 0.1);
    }

    .sidebar-nav::-webkit-scrollbar-thumb {
        background: rgba(255, 255, 255, 0.3);
        border-radius: 3px;
    }

    .sidebar-nav::-webkit-scrollbar-thumb:hover {
        background: rgba(255, 255, 255, 0.5);
    }
</style>

<!-- Sidebar Toggle Button (Mobile) -->
<button class="sidebar-toggle" id="sidebarToggle">
    <i class="bi bi-list"></i>
</button>

<!-- Sidebar Overlay (Mobile) -->
<div class="sidebar-overlay" id="sidebarOverlay"></div>

<!-- Sidebar -->
<aside class="sidebar" id="sidebar">
    <!-- Logo Section -->
    <div class="sidebar-logo">
        <img src="https://hebbkx1anhila5yf.public.blob.vercel-storage.com/image-0HdQXzZCWmsjDkGkcqET2fpjJ9yAQ5.png" alt="Control Tiempos - Atlantic KPG">
    </div>

    <!-- Navigation -->
    <nav class="sidebar-nav">
        <ul>
            <li>
                <a href="/timeControl/public/datos_trabajo_maquina" class="<?= $current_page === 'datos_trabajo_maquina' ? 'active' : '' ?>">
                    <i class="bi bi-gear-wide-connected"></i>
                    <span>Seleccionar Máquina</span>
                </a>
            </li>
            <li>
                <a href="/timeControl/public/operador/tabulados" class="<?= $current_page === 'tabulados' ? 'active' : '' ?>">
                    <i class="bi bi-table"></i>
                    <span>Tabulados</span>
                </a>
            </li>
            <li>
                <a href="/timeControl/public/operador/control" class="<?= $current_page === 'control' ? 'active' : '' ?>">
                    <i class="bi bi-speedometer2"></i>
                    <span>Control</span>
                </a>
            </li>
        </ul>
    </nav>

    <!-- User Info & Logout -->
    <div class="sidebar-footer">
        <?php if (isset($_SESSION['nombre'])): ?>
        <div class="user-info">
            <p><i class="bi bi-person-circle"></i><strong><?= htmlspecialchars($_SESSION['nombre']) ?></strong></p>
            <?php if (isset($_SESSION['maquina'])): ?>
            <p><i class="bi bi-gear"></i>Máquina: <?= htmlspecialchars($_SESSION['maquina']) ?></p>
            <?php endif; ?>
        </div>
        <?php endif; ?>
        
        <a href="/timeControl/public/logout" class="logout-btn" onclick="return confirm('¿Estás seguro de cerrar sesión?')">
            <i class="bi bi-box-arrow-right"></i>
            Cerrar Sesión
        </a>
    </div>
</aside>

<script>
    // Sidebar Toggle Functionality
    document.addEventListener('DOMContentLoaded', function() {
        const sidebar = document.getElementById('sidebar');
        const sidebarToggle = document.getElementById('sidebarToggle');
        const sidebarOverlay = document.getElementById('sidebarOverlay');

        function toggleSidebar() {
            sidebar.classList.toggle('active');
            sidebarToggle.classList.toggle('active');
            sidebarOverlay.classList.toggle('active');
        }

        sidebarToggle.addEventListener('click', toggleSidebar);
        sidebarOverlay.addEventListener('click', toggleSidebar);

        // Close sidebar on navigation (mobile)
        if (window.innerWidth <= 768) {
            const navLinks = document.querySelectorAll('.sidebar-nav a');
            navLinks.forEach(link => {
                link.addEventListener('click', function() {
                    sidebar.classList.remove('active');
                    sidebarToggle.classList.remove('active');
                    sidebarOverlay.classList.remove('active');
                });
            });
        }
    });
</script>
