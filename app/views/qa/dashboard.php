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

    <div class="row">
        <div class="col-md-6 mb-4">
            <div class="card h-100">
                <div class="card-header bg-primary text-white">
                    <h3 class="mb-0">Validación de entregas</h3>
                </div>
                <div class="card-body d-flex flex-column">
                    <div class="text-center mb-3">
                        <h1 class="display-4">
                            <!-- Aquí sería ideal mostrar la cantidad de entregas pendientes -->
                            <?= isset($data['stats']['pendientes']) ? $data['stats']['pendientes'] : '0' ?>
                        </h1>
                    </div>
                    <p>Entregas que requieren validación de cantidades de producción.</p>
                    <div class="mt-auto">
                        <a href="/timeControl/public/validacion" class="btn btn-primary btn-sm">Ver</a>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6 mb-4">
            <div class="card h-100">
                <div class="card-header bg-success text-white">
                    <h3 class="mb-0">En espera por acción Qa.</h3>
                </div>
                <div class="card-body d-flex flex-column">
                    <div class="text-center mb-3">
                        <h1 class="display-4">
                            <!-- Aquí sería ideal mostrar la cantidad de entregas validadas -->
                            <?= isset($data['stats']['validadas']) ? $data['stats']['validadas'] : '0' ?>
                        </h1>
                    </div>
                    <p>Entregas validadas. <i>(Qt.)</i></p>
                    <div class="mt-auto">
                        <a href="/timeControl/public/entregas" class="btn btn-success btn-sm">Ver Entregas</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6 mb-4">
            <div class="card h-100">
                <div class="card-header bg-warning">
                    <h3 class="mb-0">Producción Total</h3>
                </div>
                <div class="card-body">
                    <div class="text-center mb-3">
                        <h1 class="display-4">
                            <!-- Aquí mostrar la producción total validada -->
                            <?= isset($data['stats']['produccion_total']) ? number_format($data['stats']['produccion_total'], 2) : '0.00' ?>
                        </h1>
                    </div>
                    <p>Total de unidades producidas (validadas por QA).</p>
                </div>
            </div>
        </div>

        <div class="col-md-6 mb-4">
            <div class="card h-100">
                <div class="card-header bg-danger text-white">
                    <h3 class="mb-0">Scrapt Total</h3>
                </div>
                <div class="card-body">
                    <div class="text-center mb-3">
                        <h1 class="display-4">
                            <!-- Aquí mostrar el scrapt total validado -->
                            <?= isset($data['stats']['scrapt_total']) ? number_format($data['stats']['scrapt_total'], 2) : '0.00' ?>
                        </h1>
                    </div>
                    <p>Total de scrap registrado (validado por QA).</p>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12 mb-4">
            <div class="card">
                <div class="card-header bg-info text-white">
                    <h3 class="mb-0">Estadísticas por Máquina</h3>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>Máquina</th>
                                    <th>Producción</th>
                                    <th>Scrapt</th>
                                    <th>% Scrapt</th>
                                    <th>Entregas</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (isset($data['stats_maquinas']) && !empty($data['stats_maquinas'])): ?>
                                    <?php foreach ($data['stats_maquinas'] as $maquina): ?>
                                        <tr>
                                            <td><?= $maquina['nombre_maquina'] ?></td>
                                            <td><?= number_format($maquina['produccion'], 2) ?></td>
                                            <td><?= number_format($maquina['scrapt'], 2) ?></td>
                                            <td><?= number_format(($maquina['scrapt'] / ($maquina['produccion'] + $maquina['scrapt'])) * 100, 2) ?>%</td>
                                            <td><?= $maquina['entregas'] ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="5" class="text-center">No hay datos disponibles</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12 mb-4">
            <div class="card">
                <div class="card-header bg-secondary text-white">
                    <h3 class="mb-0">Validaciones Recientes</h3>
                </div>
                <div class="card-body">
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
                                    <th>Fecha Validación</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (isset($data['validaciones_recientes']) && !empty($data['validaciones_recientes'])): ?>
                                    <?php foreach ($data['validaciones_recientes'] as $validacion): ?>
                                        <tr>
                                            <td><?= $validacion['nombre_empleado'] ?></td>
                                            <td><?= $validacion['nombre_maquina'] ?></td>
                                            <td><?= $validacion['item'] ?></td>
                                            <td><?= $validacion['jtWo'] ?></td>
                                            <td><?= number_format($validacion['total_produccion'], 2) ?></td>
                                            <td><?= number_format($validacion['total_scrapt'], 2) ?></td>
                                            <td><?= date('d/m/Y H:i', strtotime($validacion['fecha_validacion'])) ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="7" class="text-center">No hay validaciones recientes</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-3 text-end">
                        <a href="<?= __DIR__ ?>/qa/historial" class="btn btn-secondary">Ver Historial Completo</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . "/../layouts/footer.php"; ?>