<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vista de QA</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/css/toastr.min.css">
</head>

<div class="container mt-4">
    <h1 class="mb-4"><?php echo $data['title']; ?></h1>

    <div class="card">
        <div class="card-header bg-success text-white">
            <h3 class="mb-0">Historial de Entregas Validadas</h3>
        </div>
        <div class="card-body">
            <!-- Filtros de búsqueda -->
            <div class="mb-4">
                <form action="/timeControl/public/historial" method="get" class="row g-3">
                    <div class="col-md-3">
                        <label for="fecha_desde" class="form-label">Desde:</label>
                        <input type="date" class="form-control" id="fecha_desde" name="fecha_desde"
                            value="<?= isset($_GET['fecha_desde']) ? $_GET['fecha_desde'] : date('Y-m-d', strtotime('-30 days')) ?>">
                    </div>
                    <div class="col-md-3">
                        <label for="fecha_hasta" class="form-label">Hasta:</label>
                        <input type="date" class="form-control" id="fecha_hasta" name="fecha_hasta"
                            value="<?= isset($_GET['fecha_hasta']) ? $_GET['fecha_hasta'] : date('Y-m-d') ?>">
                    </div>
                    <div class="col-md-4">
                        <label for="busqueda" class="form-label">Buscar:</label>
                        <input type="text" class="form-control" id="busqueda" name="busqueda"
                            placeholder="Operador, máquina, item, JT/WO..."
                            value="<?= isset($_GET['busqueda']) ? $_GET['busqueda'] : '' ?>">
                    </div>
                    <div class="col-md-2 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary w-100">Filtrar</button>
                    </div>
                </form>
            </div>

            <!-- Tabla de entregas validadas -->
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th>Operador</th>
                            <th>Máquina</th>
                            <th>Item</th>
                            <th>JT/WO</th>
                            <th>Producción</th>
                            <th>Scrapt</th>
                            <th>Validado Por</th>
                            <th>Fecha Validación</th>
                            <th>Acción</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($data['entregas_validadas'])): ?>
                            <tr>
                                <td colspan="9" class="text-center">No hay entregas validadas que coincidan con los criterios de búsqueda.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($data['entregas_validadas'] as $entrega): ?>
                                <tr>
                                    <td><?= $entrega['nombre_empleado'] ?></td>
                                    <td><?= $entrega['nombre_maquina'] ?></td>
                                    <td><?= $entrega['item'] ?></td>
                                    <td><?= $entrega['jtWo'] ?></td>
                                    <td><?= number_format($entrega['total_produccion'], 2) ?></td>
                                    <td><?= number_format($entrega['total_scrapt'], 2) ?></td>
                                    <td><?= $entrega['validado_por'] ?></td>
                                    <td><?= date('d/m/Y H:i', strtotime($entrega['fecha_validacion'])) ?></td>
                                    <td>
                                        <?php if ($entrega['total_scrapt'] > 0): ?>
                                            <a href="/timeControl/public/reporteScrapt/<?= $entrega['codigo_empleado'] ?>/<?= $entrega['maquina'] ?>/<?= urlencode($entrega['item']) ?>/<?= urlencode($entrega['jtWo']) ?>" class="btn btn-danger btn-sm" title="Ver reporte de scrapt">
                                                <i class="fas fa-file-pdf"></i>
                                            </a>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <!-- Paginación -->
            <?php if (isset($data['paginacion'])): ?>
                <nav aria-label="Navegación de páginas">
                    <ul class="pagination justify-content-center">
                        <?php if ($data['paginacion']['pagina_actual'] > 1): ?>
                            <li class="page-item">
                                <a class="page-link" href="/timeControl/public/historial?pagina=<?= $data['paginacion']['pagina_actual'] - 1 ?>&fecha_desde=<?= isset($_GET['fecha_desde']) ? $_GET['fecha_desde'] : '' ?>&fecha_hasta=<?= isset($_GET['fecha_hasta']) ? $_GET['fecha_hasta'] : '' ?>&busqueda=<?= isset($_GET['busqueda']) ? $_GET['busqueda'] : '' ?>">Anterior</a>
                            </li>
                        <?php endif; ?>

                        <?php for ($i = 1; $i <= $data['paginacion']['total_paginas']; $i++): ?>
                            <li class="page-item <?= $i == $data['paginacion']['pagina_actual'] ? 'active' : '' ?>">
                                <a class="page-link" href="/timeControl/public/historial?pagina=<?= $i ?>&fecha_desde=<?= isset($_GET['fecha_desde']) ? $_GET['fecha_desde'] : '' ?>&fecha_hasta=<?= isset($_GET['fecha_hasta']) ? $_GET['fecha_hasta'] : '' ?>&busqueda=<?= isset($_GET['busqueda']) ? $_GET['busqueda'] : '' ?>"><?= $i ?></a>
                            </li>
                        <?php endfor; ?>

                        <?php if ($data['paginacion']['pagina_actual'] < $data['paginacion']['total_paginas']): ?>
                            <li class="page-item">
                                <a class="page-link" href="/timeControl/public/historial?pagina=<?= $data['paginacion']['pagina_actual'] + 1 ?>&fecha_desde=<?= isset($_GET['fecha_desde']) ? $_GET['fecha_desde'] : '' ?>&fecha_hasta=<?= isset($_GET['fecha_hasta']) ? $_GET['fecha_hasta'] : '' ?>&busqueda=<?= isset($_GET['busqueda']) ? $_GET['busqueda'] : '' ?>">Siguiente</a>
                            </li>
                        <?php endif; ?>
                    </ul>
                </nav>
            <?php endif; ?>

            <div class="mt-3">
                <a href="/timeControl/public/qa" class="btn btn-secondary">Volver al Panel</a>

                <!-- Botón para exportar a Excel -->
                <a href="/timeControl/public/exportar?fecha_desde=<?= isset($_GET['fecha_desde']) ? $_GET['fecha_desde'] : date('Y-m-d', strtotime('-30 days')) ?>&fecha_hasta=<?= isset($_GET['fecha_hasta']) ? $_GET['fecha_hasta'] : date('Y-m-d') ?>&busqueda=<?= isset($_GET['busqueda']) ? $_GET['busqueda'] : '' ?>" class="btn btn-success">
                    <i class="fas fa-file-excel"></i> Exportar a Excel
                </a>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . "/../layouts/footer.php"; ?>