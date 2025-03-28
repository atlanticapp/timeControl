<!-- Sidebar -->
<div class="w-64 bg-white shadow-xl h-screen fixed left-0 top-0 z-40 custom-scrollbar overflow-y-auto">
    <div class="p-5 border-b border-gray-200">
        <h2 class="text-2xl font-bold text-blue-600 flex items-center">
            <i class="fas fa-chart-line mr-3"></i>Panel QA
        </h2>
    </div>
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
                    <a href="/timeControl/public/<?= $item['route'] ?>" class="flex items-center p-3 text-gray-700 hover:bg-<?= $item['color'] ?>-50 rounded-lg transition-colors duration-300 group">
                        <i class="<?= $item['icon'] ?> mr-3 text-<?= $item['color'] ?>-500 group-hover:text-<?= $item['color'] ?>-600"></i>
                        <span class="font-medium group-hover:text-<?= $item['color'] ?>-600"><?= $item['text'] ?></span>
                    </a>
                </li>
            <?php endforeach; ?>
        </ul>
    </nav>
</div>