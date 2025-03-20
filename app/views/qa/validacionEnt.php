<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vista de QA</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/css/toastr.min.css">
</head>

<body>

    <div class="container mt-4">
        <h1 class="mb-4"><?php echo $data['title']; ?></h1>

        <ul class="nav nav-tabs mb-4" id="myTab" role="tablist">
            <li class="nav-item" role="presentation">
                <a class="nav-link active" id="pendientes-tab" data-toggle="tab" href="#pendientes" role="tab" aria-controls="pendientes" aria-selected="true">Pendientes de Validación</a>
            </li>
            <li class="nav-item" role="presentation">
                <a class="nav-link" id="validadas-tab" data-toggle="tab" href="#validadas" role="tab" aria-controls="validadas" aria-selected="false">Pendientes de Corrección</a>
            </li>
        </ul>

        <div class="tab-content" id="myTabContent">
            <!-- Tab de Entregas Pendientes -->
            <div class="tab-pane fade show active" id="pendientes" role="tabpanel" aria-labelledby="pendientes-tab">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h3 class="mb-0">Entregas pendientes de validación</h3>
                    </div>
                    <div class="card-body">
                        <?php if (empty($data['entregas_produccion']) && empty($data['entregas_scrap'])): ?>
                            <div class="alert alert-info">
                                No hay entregas pendientes de validación.
                            </div>
                        <?php else: ?>
                            <!-- Sección de Producción -->
                            <h4 class="mt-3 mb-3 text-success">Registro de Producción</h4>
                            <div class="table-responsive mb-4">
                                <table class="table table-bordered table-striped table-hover">
                                    <thead class="thead-dark">
                                        <tr>
                                            <th>Operador</th>
                                            <th>Máquina</th>
                                            <th>Item</th>
                                            <th>JT/WO</th>
                                            <th>Tipo</th>
                                            <th>Fecha/Hora</th>
                                            <th>Cantidad</th>
                                            <th>Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (empty($data['entregas_produccion'])): ?>
                                            <tr>
                                                <td colspan="8" class="text-center">No hay registros de producción pendientes.</td>
                                            </tr>
                                        <?php else: ?>
                                            <?php foreach ($data['entregas_produccion'] as $entrega): ?>
                                                <tr>
                                                    <td><?= htmlspecialchars($entrega['nombre_empleado']) ?></td>
                                                    <td><?= htmlspecialchars($entrega['nombre_maquina']) ?></td>
                                                    <td><?= htmlspecialchars($entrega['item']) ?></td>
                                                    <td><?= htmlspecialchars($entrega['jtWo']) ?></td>
                                                    <td>
                                                        <?php if ($entrega['tipo_boton'] == 'final_produccion'): ?>
                                                            <span class="badge badge-success">Final</span>
                                                        <?php else: ?>
                                                            <span class="badge badge-warning">Parcial</span>
                                                        <?php endif; ?>
                                                    </td>
                                                    <td><?= date('d/m/Y H:i', strtotime($entrega['fecha_registro'])) ?></td>
                                                    <td class="text-right font-weight-bold">
                                                        <?= htmlspecialchars($entrega['cantidad']) ?>
                                                    </td>
                                                    <td>
                                                        <a href="/timeControl/public/revisar?id=<?= $entrega['id'] ?>&tipo=produccion"
                                                            class="btn btn-outline-secondary btn-sm">Revisar</a>

                                                        <a href="/timeControl/public/validarEntrega?id=<?= $entrega['id'] ?>&tipo=produccion"
                                                            class="btn btn-success btn-sm">Validar</a>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>

                            <!-- Sección de Scrap -->
                            <h4 class="mt-4 mb-3 text-danger">Registro de Scrap</h4>
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped table-hover">
                                    <thead class="thead-dark">
                                        <tr>
                                            <th>Operador</th>
                                            <th>Máquina</th>
                                            <th>Item</th>
                                            <th>JT/WO</th>
                                            <th>Tipo</th>
                                            <th>Fecha/Hora</th>
                                            <th>Cantidad</th>
                                            <th>Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (empty($data['entregas_scrap'])): ?>
                                            <tr>
                                                <td colspan="8" class="text-center">No hay registros de scrap pendientes.</td>
                                            </tr>
                                        <?php else: ?>
                                            <?php foreach ($data['entregas_scrap'] as $entrega): ?>
                                                <tr>
                                                    <td><?= htmlspecialchars($entrega['nombre_empleado']) ?></td>
                                                    <td><?= htmlspecialchars($entrega['nombre_maquina']) ?></td>
                                                    <td><?= htmlspecialchars($entrega['item']) ?></td>
                                                    <td><?= htmlspecialchars($entrega['jtWo']) ?></td>
                                                    <td>
                                                        <?php if ($entrega['tipo_boton'] == 'final_produccion'): ?>
                                                            <span class="badge badge-success">Final</span>
                                                        <?php else: ?>
                                                            <span class="badge badge-warning">Parcial</span>
                                                        <?php endif; ?>
                                                    </td>
                                                    <td><?= date('d/m/Y H:i', strtotime($entrega['fecha_registro'])) ?></td>
                                                    <td class="text-right font-weight-bold">
                                                        <?= htmlspecialchars($entrega['cantidad']) ?>
                                                    </td>
                                                    <td>
                                                        <a href="/timeControl/public/validarEntrega?id=<?= $entrega['id'] ?>&tipo=scrap"
                                                            class="btn btn-danger btn-sm">Validar</a>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
        <div class="mt-3">
            <a href="/timeControl/public/dashboard" class="btn btn-secondary">Volver al Dashboard</a>
        </div>
    </div>


    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js"></script>

    <script>
        $(document).ready(function() {
            // Función para manejar el cambio entre pestañas
            $('#myTab a').on('click', function(e) {
                e.preventDefault();
                $(this).tab('show');
            });

            // Mostrar mensajes Toastr según estado
            fetch('/timeControl/public/getStatus')
                .then(response => response.json())
                .then(data => {
                    if (data.status && data.message) {
                        const toastrFunction = data.status === "success" ? toastr.success : toastr.error;
                        toastrFunction(data.message, '', {
                            timeOut: 2000
                        });
                    }
                })
                .catch(error => console.error('Error al obtener el estado:', error));
        });
    </script>

    <?php include __DIR__ . "/../layouts/footer.php"; ?>