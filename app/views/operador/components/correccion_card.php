<div class="card mb-3">
    <div class="card-body">
        <h6 class="card-title">
            Item: <?= htmlspecialchars($correccion['item']) ?> - 
            JT/WO: <?= htmlspecialchars($correccion['jtWo']) ?>
        </h6>
        <p class="card-text">
            <strong>Motivo:</strong> <?= htmlspecialchars($correccion['motivo']) ?><br>
            <strong>Fecha:</strong> <?= date('d/m/Y H:i', strtotime($correccion['fecha_solicitud'])) ?>
        </p>
        <form method="POST" action="/timeControl/public/procesarCorreccion" class="correction-form">
            <input type="hidden" name="solicitud_id" value="<?= $correccion['solicitud_id'] ?>">
            <input type="hidden" name="registro_id" value="<?= $correccion['registro_id'] ?>">
            <input type="hidden" name="tipo" value="<?= $correccion['tipo_cantidad'] ?>">
            
            <div class="mb-3">
                <label class="form-label">Nueva cantidad:</label>
                <input type="number" name="cantidad" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Comentario:</label>
                <textarea name="comentario" class="form-control" required></textarea>
            </div>
            <button type="submit" class="btn btn-primary">Procesar Corrección</button>
        </form>
    </div>
</div>