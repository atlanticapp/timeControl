<?php
// Vista: Reporte Entrega Scrap Almacén
// Espera recibir $entrega con los datos de la entrega de scrap
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reporte Entrega Scrap Almacén</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/css/toastr.min.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/timeControl/public/assets/css/qa/detalle_scrap.css">

</head>

<body>
    <div class="max-w-4xl mx-auto print-hidden mb-4">
        <div class="buttons-container">
            <div>
                <a href="/timeControl/public/reporte_scrap" class="px-4 py-2 bg-gray-200 hover:bg-gray-300 rounded-lg text-gray-700 flex items-center inline-block">
                    <i class="fas fa-arrow-left mr-2"></i> Volver al Reporte
                </a>
            </div>
            <div class="flex items-center gap-2">
                <button id="btnImprimirTodo" class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg flex items-center transition-colors duration-200">
                    <i class="fas fa-print mr-2"></i> Imprimir todas las hojas
                </button>
            </div>
        </div>
    </div>

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
                <div class="form-title">REPORTE ENTREGA SCRAP ALMACEN</div>
                <div class="form-code" style="display: flex; flex-direction: column; align-items: flex-end; gap: 6px; margin-left: 10px;">
                    <div style="border: 1.5px solid; font-weight: bold; font-size: 10px; min-width: 110px; text-align: center;">Código: AL-F04-S</div>
                    <div style="border: 1.5px solid; font-weight: bold; font-size: 10px; min-width: 110px; text-align: center;">Revisión: 01</div>
                    <div class="form-code-details">
                        <b><?= $color['nombre'] ?>:</b> <?= $color['destino'] ?><br>
                    </div>
                </div>
            </div>
            <div class="form-number"><?= sprintf("%06d", $entrega['id'] ?? 'NULL') ?></div>
            <table>
                <thead>
                    <tr>
                        <th><b>P/N</b></th>
                        <th><b>Lote (Job Ticket)</b></th>
                        <th><b>PO (Orden de compra)</b></th>
                        <th><b>TOTAL SCRAP</b></th>
                        <th><b>TIPO</b></th>
                        <th><b>CLIENTE</b></th>
                        <th><b>LINEA</b></th>
                        <th><b>OBSERVACIONES</b></th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><?= htmlspecialchars($entrega['item'] ?? '') ?></td>
                        <td><?= htmlspecialchars($entrega['jtWo'] ?? '') ?></td>
                        <td><?= htmlspecialchars($entrega['po'] ?? 'N/A') ?></td>
                        <td><span class="font-semibold text-red-700"><?= number_format($entrega['cantidad'], 2, '.', ',') ?> Lb</span></td>
                        <td>
                            <div class="transfer-type-container">
                                <div class="transfer-type-item selected-transfer">
                                    <span class="ml-1">SCRAP ✓</span>
                                </div>
                                <div class="transfer-type-item">
                                    <span class="ml-1">RETRABAJO</span>
                                </div>
                                <div class="transfer-type-item">
                                    <span class="ml-1">DESTRUCCIÓN</span>
                                </div>
                            </div>
                        </td>
                        <td><?= htmlspecialchars($entrega['cliente'] ?? 'N/A') ?></td>
                        <td><?= htmlspecialchars($entrega['nombre_maquina'] ?? 'N/A') ?></td>
                        <td><?= htmlspecialchars($entrega['observaciones'] ?? '') ?></td>
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
            <div class="footer-text">Atlantic Caribbean Packaging - Reporte de Scrap</div>
        </div>
    <?php endforeach; ?>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            // Manejo de error del logo
            var logoImg = document.querySelector('.logo-image');
            if (logoImg) {
                logoImg.addEventListener('error', function() {
                    var logoSpan = document.querySelector('.logo-space span');
                    if (logoSpan) {
                        logoSpan.style.display = 'block';
                    }
                });
            }

            // Fetch status
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

            // Evento del botón imprimir
            var btnImprimir = document.getElementById('btnImprimirTodo');
            if (btnImprimir) {
                btnImprimir.addEventListener('click', function(e) {
                    console.log('Botón imprimir clickeado');
                    window.print();
                });
            } else {
                console.error('No se encontró el botón btnImprimirTodo');
            }
        });

        // Mostrar todas las hojas antes de imprimir
        window.addEventListener('beforeprint', function() {
            console.log('Evento beforeprint activado');
            var containers = document.querySelectorAll('.form-container');
            containers.forEach(function(div) {
                div.style.display = 'block';
            });
        });

        // Ocultar hojas después de imprimir (excepto la primera)
        window.addEventListener('afterprint', function() {
            console.log('Evento afterprint activado');
            var containers = document.querySelectorAll('.form-container');
            containers.forEach(function(div, idx) {
                if (idx !== 0) {
                    div.style.display = 'none';
                }
            });
        });
    </script>
</body>

</html>