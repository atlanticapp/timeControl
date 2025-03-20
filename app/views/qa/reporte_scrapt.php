<?php include __DIR__ . "/../layouts/header.php"; ?>

<div class="container mt-4">
    <div class="card">
        <div class="card-header bg-danger text-white">
            <h1 class="mb-0">Reporte de Scrapt Final</h1>
        </div>
        <div class="card-body">
            <div class="mb-4">
                <div class="row">
                    <div class="col-md-6">
                        <h5>Información General</h5>
                        <p><strong>Operador:</strong> <?= $data['detalles'][0]['nombre_empleado'] ?> (ID: <?= $data['empleado_id'] ?>)</p>
                        <p><strong>Máquina:</strong> <?= $data['nombre_maquina'] ?> (ID: <?= $data['maquina_id'] ?>)</p>
                        <p><strong>Item:</strong> <?= $data['item'] ?></p>
                        <p><strong>JT/WO:</strong> <?= $data['jtwo'] ?></p>
                    </div>
                    <div class="col-md-6">
                        <h5>Información de Validación</h5>
                        <p><strong>Validado por:</strong> <?= $data['qa_nombre'] ?> (ID: <?= $data['qa_id'] ?>)</p>
                        <p><strong>Fecha:</strong> <?= date('d/m/Y H:i') ?></p>
                        <p><strong>Total Scrapt:</strong> <?= number_format($data['total_scrapt'], 2) ?></p>
                    </div>
                </div>
            </div>

            <h5 class="mb-3">Detalle de Scrapt</h5>
            <div class="table-responsive">
                <table class="table table-striped table-bordered">
                    <thead>
                        <tr>
                            <th>Fecha</th>
                            <th>Hora</th>
                            <th>Cantidad</th>
                            <th>Notas</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($data['detalles'] as $detalle): ?>
                            <?php if ($detalle['cantidad_scrapt'] > 0): ?>
                            <tr>
                                <td><?= date('d/m/Y', strtotime($detalle['fecha_registro'])) ?></td>
                                <td><?= $detalle['hora_inicio'] ?> - <?= $detalle['hora_fin'] ?></td>
                                <td><?= number_format($detalle['cantidad_scrapt'], 2) ?></td>
                                <td><?= $detalle['notas'] ?></td>
                            </tr>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </tbody>
                    <tfoot>
                        <tr>
                            <th colspan="2">Total:</th>
                            <th><?= number_format($data['total_scrapt'], 2) ?></th>
                            <th></th>
                        </tr>
                    </tfoot>
                </table>
            </div>

            <div class="mt-4">
                <h5>Firma de Validación</h5>
                <div class="row mt-4">
                    <div class="col-md-6">
                        <div class="border-top border-dark pt-2">
                            <p class="text-center mb-0">QA: <?= $data['qa_nombre'] ?></p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="border-top border-dark pt-2">
                            <p class="text-center mb-0">Operador: <?= $data['detalles'][0]['nombre_empleado'] ?></p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mt-5 text-center">
                <button class="btn btn-primary" onclick="window.print()">
                    <i class="fas fa-print"></i> Imprimir Reporte
                </button>
                <a href="<?= __DIR__ ?>/qa" class="btn btn-secondary ms-2">
                    <i class="fas fa-arrow-left"></i> Volver al Panel
                </a>
            </div>
        </div>
    </div>
</div>

<style>
    @media print {
        .btn, .navbar, footer {
            display: none !important;
        }
        .container {
            width: 100% !important;
            max-width: 100% !important;
        }
        .card {
            border: none !important;
        }
        .card-header {
            background-color: #f8d7da !important;
            color: #000 !important;
        }
    }
</style>
<?php include __DIR__ . "/../layouts/footer.php"; ?>