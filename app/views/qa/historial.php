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
    <h1 class="mb-4"><?= htmlspecialchars($data['title']) ?></h1>

    <div class="card shadow">
        <div class="card-header bg-success text-white py-3">
            <h2 class="h5 mb-0">Entregas Validadas</h2>
        </div>

        <div class="card-body">
            <!-- Filtros -->
            <form action="/timeControl/public/historial" method="GET" class="mb-4">
                <div class="row g-3">
                    <div class="col-md-3">
                        <label for="tipo" class="form-label">Tipo</label>
                        <select class="form-select" id="tipo" name="tipo">
                            <option value="">Todos</option>
                            <option value="Parcial" <?= (isset($_GET['tipo']) && $_GET['tipo'] === 'Parcial') ? 'selected' : '' ?>>Parcial</option>
                            <option value="Produccion" <?= (isset($_GET['tipo']) && $_GET['tipo'] === 'Produccion') ? 'selected' : '' ?>>Producción</option>
                        </select>
                    </div>

                    <div class="col-md-3">
                        <label for="item" class="form-label">Item</label>
                        <input type="text" class="form-control" id="item" name="item"
                            placeholder="Filtrar por item"
                            value="<?= htmlspecialchars($_GET['item'] ?? '') ?>">
                    </div>

                    <div class="col-md-3">
                        <label for="jtWo" class="form-label">JT/WO</label>
                        <input type="text" class="form-control" id="jtWo" name="jtWo"
                            placeholder="Filtrar por JT/WO"
                            value="<?= htmlspecialchars($_GET['jtWo'] ?? '') ?>">
                    </div>

                    <div class="col-md-3">
                        <label for="codigo_empleado" class="form-label">Código Empleado</label>
                        <input type="text" class="form-control" id="codigo_empleado" name="codigo_empleado"
                            placeholder="Filtrar por código"
                            value="<?= htmlspecialchars($_GET['codigo_empleado'] ?? '') ?>">
                    </div>

                    <div class="col-md-12">
                        <label for="operador" class="form-label">Operador</label>
                        <input type="text" class="form-control" id="operador" name="operador"
                            placeholder="Filtrar por operador"
                            value="<?= htmlspecialchars($_GET['operador'] ?? '') ?>">
                    </div>

                    <div class="col-md-12 d-grid">
                        <button type="submit" class="btn btn-primary btn-lg">
                            <i class="fas fa-filter me-2"></i>Filtrar
                        </button>
                    </div>
                </div>
            </form>

            <!-- Tabla de resultados -->
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Tipo</th>
                            <th>Operador</th>
                            <th>Máquina</th>
                            <th>Item</th>
                            <th>JT/WO</th>
                            <th>Producción</th>
                            <th>Scrapt</th>
                            <th>Validador</th>
                            <th>Fecha Validación</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($data['entregas_validadas'])): ?>
                            <tr>
                                <td colspan="9" class="text-center py-4 text-muted">
                                    <i class="fas fa-database fa-2x mb-3"></i><br>
                                    No se encontraron registros
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($data['entregas_validadas'] as $entrega): ?>
                                <tr>
                                    <td>
                                        <span class="badge <?= $entrega['tipo_boton'] === 'Parcial' ? 'bg-warning' : 'bg-success' ?>">
                                            <?= htmlspecialchars($entrega['tipo_boton']) ?>
                                        </span>
                                    </td>
                                    <td><?= htmlspecialchars($entrega['nombre_empleado']) ?></td>
                                    <td><?= htmlspecialchars($entrega['nombre_maquina']) ?></td>
                                    <td><code><?= htmlspecialchars($entrega['item']) ?></code></td>
                                    <td><code><?= htmlspecialchars($entrega['jtWo']) ?></code></td>
                                    <td><?= number_format((float)$entrega['cantidad_produccion'], 2) ?></td>
                                    <td><?= number_format((float)$entrega['cantidad_scrapt'], 2) ?></td>
                                    <td><?= htmlspecialchars($entrega['validado_por']) ?></td>
                                    <td><?= date('d/m/Y H:i', strtotime($entrega['fecha_validacion'])) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>


<script>
    // Inicializar tooltips de Bootstrap
    document.addEventListener('DOMContentLoaded', function() {
        [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
            .forEach(tooltip => new bootstrap.Tooltip(tooltip));
    });
</script>


<?php include __DIR__ . "/../layouts/footer.php"; ?>