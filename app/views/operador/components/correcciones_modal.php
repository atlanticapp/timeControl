<div class="alert alert-warning">
    <h4>¡Atención! Tienes correcciones pendientes</h4>
    <p>Hay correcciones pendientes para esta máquina.</p>
    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#correccionesModal">
        Ver Correcciones
    </button>
</div>

<div class="modal fade" id="correccionesModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Correcciones Pendientes</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <?php foreach ($correcciones_pendientes as $correccion): ?>
                    <?php include 'correccion_card.php'; ?>
                <?php endforeach; ?>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>