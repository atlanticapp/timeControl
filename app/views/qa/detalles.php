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

    <?php if (count($data['detalles']) > 0): ?>
        <?php $primerDetalle = $data['detalles'][0]; ?>

        <div class="card mb-4">
            <div class="card-header bg-primary text-white">
                <h3 class="mb-0">Información General</h3>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <p><strong>Operador:</strong> <?= $primerDetalle['nombre_empleado'] ?></p>
                        <p><strong>Máquina:</strong> <?= $primerDetalle['nombre_maquina'] ?></p>
                        <p><strong>Item:</strong> <?= $primerDetalle['item'] ?></p>
                        <p><strong>JT/WO:</strong> <?= $primerDetalle['jtWo'] ?></p>
                    </div>
                    <div class="col-md-6">
                        <p><strong>Fecha de registro:</strong> <?= date('d/m/Y H:i', strtotime($primerDetalle['fecha_registro'])) ?></p>

                        <?php
                        $total_produccion = 0;
                        $total_scrapt = 0;
                        foreach ($data['detalles'] as $detalle) {
                            $total_produccion += $detalle['cantidad_produccion'];
                            $total_scrapt += $detalle['cantidad_scrapt'];
                        }
                        ?>

                        <p><strong>Total Producción:</strong> <?= number_format($total_produccion, 2) ?></p>
                        <p><strong>Total Scrapt:</strong> <?= number_format($total_scrapt, 2) ?></p>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Panel de Producción -->
            <div class="col-md-6">
                <div class="card mb-4">
                    <div class="card-header bg-primary text-white">
                        <h3 class="mb-0">Validación de Producción</h3>
                    </div>
                    <div class="card-body">
                        <h4>Cantidad total: <?= number_format($total_produccion, 2) ?></h4>

                        <form action="<?= __DIR__ ?>/qa/validar" method="post" class="mt-3">
                            <input type="hidden" name="empleado_id" value="<?= $data['codigo_empleado'] ?>">
                            <input type="hidden" name="maquina_id" value="<?= $data['maquina_id'] ?>">
                            <input type="hidden" name="item" value="<?= $data['item'] ?>">
                            <input type="hidden" name="jtwo" value="<?= $data['jtwo'] ?>">
                            <input type="hidden" name="tipo" value="produccion">

                            <div class="form-group mb-3">
                                <label for="comentario_produccion">Comentario (opcional):</label>
                                <textarea class="form-control" id="comentario_produccion" name="comentario" rows="3"></textarea>
                            </div>

                            <div class="d-flex justify-content-between">
                                <button type="submit" class="btn btn-success">Aceptar</button>
                                <button type="button" class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#modalCorregirProduccion">Corregir</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Panel de Scrapt -->
            <div class="col-md-6">
                <div class="card mb-4">
                    <div class="card-header bg-danger text-white">
                        <h3 class="mb-0">Validación de Scrapt</h3>
                    </div>
                    <div class="card-body">
                        <h4>Cantidad total: <?= number_format($total_scrapt, 2) ?></h4>

                        <form action="<?= __DIR__ ?>/qa/validar" method="post" class="mt-3">
                            <input type="hidden" name="empleado_id" value="<?= $data['codigo_empleado'] ?>">
                            <input type="hidden" name="maquina_id" value="<?= $data['maquina_id'] ?>">
                            <input type="hidden" name="item" value="<?= $data['item'] ?>">
                            <input type="hidden" name="jtwo" value="<?= $data['jtwo'] ?>">
                            <input type="hidden" name="tipo" value="scrapt">

                            <div class="form-group mb-3">
                                <label for="comentario_scrapt">Comentario (opcional):</label>
                                <textarea class="form-control" id="comentario_scrapt" name="comentario" rows="3"></textarea>
                            </div>

                            <div class="d-flex justify-content-between">
                                <button type="submit" class="btn btn-success">Aceptar</button>
                                <button type="button" class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#modalCorregirScrapt">Corregir</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Detalle de las entregas -->
        <div class="card mb-4">
            <div class="card-header bg-info text-white">
                <h3 class="mb-0">Detalle de Entregas</h3>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>Fecha/Hora</th>
                                <th>Tipo de Entrega</th>
                                <th>Producción</th>
                                <th>Scrapt</th>
                                <th>Notas</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($data['detalles'] as $detalle): ?>
                                <tr>
                                    <td><?= date('d/m/Y H:i', strtotime($detalle['fecha_registro'])) ?></td>
                                    <td>
                                        <?php
                                        if ($detalle['tipo_boton'] === 'final_produccion') {
                                            echo 'Final de Producción';
                                        } else {
                                            echo 'Parcial';
                                        }
                                        ?>
                                    </td>
                                    <td class="font-weight-bold"><?= number_format($detalle['cantidad_produccion'], 2) ?></td>
                                    <td class="font-weight-bold"><?= number_format($detalle['cantidad_scrapt'], 2) ?></td>
                                    <td><?= $detalle['comentario'] ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    <?php else: ?>
        <div class="alert alert-warning">
            No se encontraron detalles para esta entrega.
        </div>
    <?php endif; ?>

    <div class="mt-3">
        <a href="/timeControl/public/validacion" class="btn btn-secondary">Volver al panel</a>
    </div>
</div>

<!-- Modal para corregir producción -->
<div class="modal fade" id="modalCorregirProduccion" tabindex="-1" aria-labelledby="modalCorregirProduccionLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-warning">
                <h5 class="modal-title" id="modalCorregirProduccionLabel">Corregir Producción</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="<?= __DIR__ ?>/qa/corregir" method="post">
                <div class="modal-body">
                    <input type="hidden" name="empleado_id" value="<?= $data['codigo_empleado'] ?>">
                    <input type="hidden" name="maquina_id" value="<?= $data['maquina_id'] ?>">
                    <input type="hidden" name="item" value="<?= $data['item'] ?>">
                    <input type="hidden" name="jtwo" value="<?= $data['jtwo'] ?>">

                    <div class="form-group mb-3">
                        <label for="comentario">Comentario de corrección:</label>
                        <textarea class="form-control" id="comentario" name="comentario" rows="4" required></textarea>
                        <small class="form-text text-muted">Explique claramente qué debe corregir el operador.</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Enviar Corrección</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal para corregir scrapt -->
<div class="modal fade" id="modalCorregirScrapt" tabindex="-1" aria-labelledby="modalCorregirScrapLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-warning">
                <h5 class="modal-title" id="modalCorregirScrapLabel">Corregir Scrapt</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="<?= __DIR__ ?>/qa/corregir" method="post">
                <div class="modal-body">
                    <input type="hidden" name="empleado_id" value="<?= $data['codigo_empleado'] ?>">
                    <input type="hidden" name="maquina_id" value="<?= $data['maquina_id'] ?>">
                    <input type="hidden" name="item" value="<?= $data['item'] ?>">
                    <input type="hidden" name="jtwo" value="<?= $data['jtwo'] ?>">

                    <div class="form-group mb-3">
                        <label for="comentario">Comentario de corrección:</label>
                        <textarea class="form-control" id="comentario" name="comentario" rows="4" required></textarea>
                        <small class="form-text text-muted">Explique claramente qué debe corregir el operador.</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Enviar Corrección</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    // Función para mostrar mensajes según el parámetro 'status' en la URL
    document.addEventListener("DOMContentLoaded", function() {
        fetch('/timeControl/public/getStatus') // Llama al endpoint de PHP
            .then(response => response.json())
            .then(data => {
                if (data.status && data.message) {
                    const toastrFunction = data.status === "success" ? toastr.success : toastr.error;

                    toastrFunction(data.message, '', {
                        timeOut: 2000
                    });

                    setTimeout(() => {
                        window.location.href = "/timeControl/public/verDetalles"; // Limpia la URL
                    }, 2000);
                }
            });
    });
</script>
<?php include __DIR__ . "/../layouts/footer.php"; ?>