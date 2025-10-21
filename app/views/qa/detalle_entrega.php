<?php
// Vista: Reporte Entrega Producto Terminado Almacen
// Espera recibir $entrega con los datos de la entrega
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reporte Entrega Producto Terminado Almacen</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/css/toastr.min.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/timeControl/public/assets/css/qa/detalle_entrega.css">
 
</head>

<body>
    <div class="max-w-4xl mx-auto print-hidden mb-4">
        <div class="buttons-container">
            <div>
                <a href="/timeControl/public/reporte-entrega" class="px-4 py-2 bg-gray-200 hover:bg-gray-300 rounded-lg text-gray-700 flex items-center inline-block">
                    <i class="fas fa-arrow-left mr-2"></i> Volver al Reporte
                </a>
            </div>
            <div class="flex items-center gap-2">
                <!-- Se oculta el selector de color, ya no es necesario -->
                <?php if (isset($_GET['editar']) && $_GET['editar'] == 1): ?>
                    <button id="btnGuardar" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg flex items-center transition-colors duration-200 mr-2">
                        <i class="fas fa-save mr-2"></i> Guardar
                    </button>
                <?php else: ?>
                    <a href="?editar=1" class="px-4 py-2 bg-yellow-500 hover:bg-yellow-600 text-white rounded-lg flex items-center transition-colors duration-200 mr-2">
                        <i class="fas fa-edit mr-2"></i> Editar paletas/cajas/piezas
                    </a>
                <?php endif; ?>
                <button id="btnImprimirTodo" class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg flex items-center transition-colors duration-200">
                    <i class="fas fa-print mr-2"></i> Imprimir todas las hojas
                </button>
            </div>
        </div>
    </div>
    <script>
        // Eliminar el selector de color y su script
    </script>

    <?php
    $colores = [
        ['clase' => 'hoja-blanca', 'nombre' => 'BLANCA', 'destino' => 'CONTROL DE INV.'],
        ['clase' => 'hoja-amarilla', 'nombre' => 'AMARILLA', 'destino' => 'ALMACEN'],
        ['clase' => 'hoja-rosada', 'nombre' => 'ROSADA', 'destino' => 'SERV. AL CLIENTE'],
        ['clase' => 'hoja-verde', 'nombre' => 'VERDE', 'destino' => 'PRODUCCIÓN'],
    ];
    foreach ($colores as $i => $color):
    ?>
        <div class="form-container <?= $color['clase'] ?>"
            style="<?php if ($i !== 0): ?>display:none;<?php endif; ?>"
            data-hoja-color="<?= $color['clase'] ?>">
            <div class="form-header">
                <div class="logo-space">
                    <img src="/timeControl/public/assets/img/logoprint.png" alt="Atlantic Caribbean Packaging" class="logo-image" style="max-width: 120px; max-height: 60px; display: block; margin: 0 auto;">
                </div>
                <div class="form-title">REPORTE ENTREGA PRODUCTO TERMINADO ALMACEN</div>
                <div class="form-code" style="display: flex; flex-direction: column; align-items: flex-end; gap: 6px; margin-left: 10px;">
                    <div style="border: 1.5px solid; font-weight: bold; font-size: 10px; min-width: 110px; text-align: center;">Código: AL-F04</div>
                    <div style="border: 1.5px solid; font-weight: bold; font-size: 10px; min-width: 110px; text-align: center;">Revisión: 06</div>
                    <div class="form-code-details">
                        <b><?= $color['nombre'] ?>:</b> <?= $color['destino'] ?><br>
                    </div>
                </div>
            </div>
            <div class="form-number"><?= sprintf("%06d", $entrega['id'] ?? 'NULL') ?></div>
            <form class="formEntrega" method="post" action="/timeControl/public/guardar-entrega">
                <input type="hidden" name="entrega_id" value="<?= $entrega['id'] ?? '' ?>">
                <table>
                    <thead>
                        <tr>
                            <th><b>P/N</b></th>
                            <th><b>Lote (Job Ticket)</b></th>
                            <th><b>PO (Orden de compra)</b></th>
                            <th><b>Cant. Entregada</b></th>
                            <th><b>TOTAL PROD. TERMINADO EN SU UD.</b></th>
                            <th><b>CANTIDAD DE PALETAS</b></th>
                            <th><b>TIPO DE TRANSFERENCIA</b></th>
                            <th><b>CLIENTE</b></th>
                            <th><b>LINEA</b></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>
                                <?= htmlspecialchars($entrega['item'] ?? '') ?>
                            </td>
                            <td>
                                <?= htmlspecialchars($entrega['jtWo'] ?? '') ?>
                            </td>
                            <td>
                                <?= htmlspecialchars($entrega['po'] ?? '') ?>
                            </td>
                            <td>
                                <?php $cajas = isset($entrega['cajas']) ? intval($entrega['cajas']) : null; ?>
                                <?php $piezas = isset($entrega['piezas']) ? intval($entrega['piezas']) : null; ?>
                                <div class="text-center">
                                    <span class="font-semibold">
                                        <span style="border-bottom: 1px solid #000;">
                                            <?= ($cajas !== null && $cajas !== '') ? $cajas : '-' ?>
                                        </span> Cajas
                                    </span>
                                </div>
                                <div class="my-1"></div>
                                <div class="text-center">
                                    <span class="font-semibold">
                                        <span style="border-bottom: 1px solid #000;">
                                            <?= ($piezas !== null && $piezas !== '') ? $piezas : '-' ?>
                                        </span> Piezas
                                    </span>
                                </div>
                                <?php if (isset($_GET['editar']) && $_GET['editar'] == 1): ?>
                                    <div class="mt-2">
                                        <input type="number" name="cajas" placeholder="Cajas" value="<?= ($entrega['cajas'] !== null && $entrega['cajas'] !== '') ? htmlspecialchars($entrega['cajas']) : '' ?>" min="0" required>
                                    </div>
                                    <div class="mt-1">
                                        <input type="number" name="piezas" placeholder="Piezas" value="<?= ($entrega['piezas'] !== null && $entrega['piezas'] !== '') ? htmlspecialchars($entrega['piezas']) : '' ?>" min="0" required>
                                    </div>
                                <?php endif; ?>
                            </td>
                            <td>
                               <span class="font-semibold"><?= number_format($entrega['cantidad_produccion'], 2, '.', ',') ?> Lb</span>
                            </td>
                            <td>
                                <?php $paletas = isset($entrega['paletas']) ? intval($entrega['paletas']) : null; ?>
                                <div class="text-center">
                                    <span class="font-semibold">
                                        <span style="border-bottom: 1px solid #000;">
                                            <?= ($paletas !== null && $paletas !== '') ? $paletas : '-' ?>
                                        </span> Paletas
                                    </span>
                                </div>
                                <?php if (isset($_GET['editar']) && $_GET['editar'] == 1): ?>
                                    <div class="mt-2">
                                        <input type="number" name="paletas" placeholder="Paletas" value="<?= ($entrega['paletas'] !== null && $entrega['paletas'] !== '') ? htmlspecialchars($entrega['paletas']) : '' ?>" min="0" required>
                                    </div>
                                <?php endif; ?>
                            </td>
                            <td>
                                <div class="transfer-type-container">
                                    <div class="transfer-type-item selected-transfer">
                                        <span class="ml-1">MANUFACTURADO ✓</span>
                                    </div>
                                    <div class="transfer-type-item">
                                        <span class="ml-1">SOLO EMPACAR</span>
                                    </div>
                                    <div class="transfer-type-item">
                                        <span class="ml-1">RETRABAJO</span>
                                    </div>
                                    <div class="transfer-type-item">
                                        <span class="ml-1">INVENTARIO</span>
                                    </div>
                                </div>
                                <input type="hidden" name="tipo_transferencia" value="MANUFACTURADO">
                            </td>
                            <td>
                                <?= htmlspecialchars($entrega['cliente'] ?? '') ?>
                            </td>
                            <td>
                                <?= htmlspecialchars($entrega['nombre_maquina'] ?? '') ?>
                            </td>
                        </tr>
                    </tbody>
                </table>

                <div class="signature-section">
                    <div>
                        <div class="signature-line"><b>Realizado Por: <br>Firma/Fecha/Hora (Producción)</b></div>
                    </div>
                    <div>
                        <div class="signature-line"><b>Firma/Fecha/Hora de recibo (Almacén)</b></div>
                    </div>
                    <div>
                        <div class="signature-line"><b>Firma/Fecha/Hora de recibo (Inventario)</b></div>
                    </div>
                </div>
            </form>
            <div class="footer-text">Atlantic Caribbean Packaging</div>
        </div>
    <?php endforeach; ?>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            // Si la imagen del logo no se puede cargar, mostrar texto alternativo
            document.querySelector('.logo-image').addEventListener('error', function() {
                document.querySelector('.logo-space span').style.display = 'block';
            });

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
    <script>
        <?php if (isset($_GET['editar']) && $_GET['editar'] == 1): ?>
            document.getElementById('btnGuardar').addEventListener('click', function(e) {
                var cajas = document.querySelector('input[name="cajas"]').value;
                var piezas = document.querySelector('input[name="piezas"]').value;
                var paletas = document.querySelector('input[name="paletas"]').value;
                if (!cajas || !piezas || !paletas || cajas <= 0 || piezas <= 0 || paletas <= 0) {
                    alert('Debes ingresar valores mayores a 0 para cajas, piezas y paletas antes de guardar o imprimir.');
                    e.preventDefault();
                    return false;
                }
                document.querySelector('.formEntrega').submit();
            });
        <?php else: ?>
            document.getElementById('btnImprimirTodo').addEventListener('click', function(e) {
                var cajas = <?= $cajas ?? 0 ?>;
                var piezas = <?= $piezas ?? 0 ?>;
                var paletas = <?= $paletas ?? 0 ?>;
                if (!cajas || !piezas || !paletas || cajas <= 0 || piezas <= 0 || paletas <= 0) {
                    alert('No puedes imprimir la hoja si no se han definido cantidades válidas para cajas, piezas y paletas. Haz clic en Editar para completarlas.');
                    e.preventDefault();
                    return false;
                }
                // // Nueva lógica: marcar como impresa antes de imprimir
                // e.preventDefault();
                // fetch('/timeControl/public/marcar-impresa?id=<?= $entrega['id'] ?? '' ?>', {
                //     method: 'POST',
                //     headers: {
                //         'Content-Type': 'application/json'
                //     }
                // })
                // .then(response => response.json())
                // .then(data => {
                //     if (data.status === 'success') {
                //         window.print();
                //     } else {
                //         alert('No se pudo marcar como impresa. Intenta de nuevo.');
                //     }
                // })
                // .catch(() => {
                //     alert('Error al marcar como impresa.');
                // });
                window.print();
            });
        <?php endif; ?>
    </script>
    <script>
        // Mostrar solo la hoja blanca en pantalla, pero imprimir todas
        window.addEventListener('beforeprint', function() {
            document.querySelectorAll('.form-container').forEach(function(div) {
                div.style.display = 'block';
            });
        });
        window.addEventListener('afterprint', function() {
            document.querySelectorAll('.form-container').forEach(function(div, idx) {
                if (idx !== 0) div.style.display = 'none';
            });
        });
    </script>
</body>

</html>