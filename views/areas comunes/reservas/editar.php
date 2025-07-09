<?php
// views/reservas/editar.php
$title = 'Editar Reserva - Sistema de Reservas';
ob_start();
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h1><i class="fas fa-edit"></i> Editar Reserva</h1>
    <a href="index.php?controller=reserva&action=index" class="btn btn-secondary">
        <i class="fas fa-arrow-left"></i> Volver
    </a>
</div>

<div class="card">
    <div class="card-body">
        <form method="POST">
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="zona_id" class="form-label">Zona Común *</label>
                        <select class="form-select" id="zona_id" name="zona_id" required>
                            <option value="">Seleccione una zona</option>
                            <?php foreach ($zonas as $zona): ?>
                                <option value="<?php echo $zona['id']; ?>" 
                                        <?php echo $zona['id'] == $reserva['zona_id'] ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($zona['nombre']); ?> 
                                    (Capacidad: <?php echo $zona['capacidad']; ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="apartamento" class="form-label">Apartamento *</label>
                        <input type="text" class="form-control" id="apartamento" name="apartamento" 
                               value="<?php echo htmlspecialchars($reserva['apartamento']); ?>" required>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="nombre_residente" class="form-label">Nombre del Residente *</label>
                        <input type="text" class="form-control" id="nombre_residente" name="nombre_residente" 
                               value="<?php echo htmlspecialchars($reserva['nombre_residente']); ?>" required>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="telefono" class="form-label">Teléfono</label>
                        <input type="tel" class="form-control" id="telefono" name="telefono" 
                               value="<?php echo htmlspecialchars($reserva['telefono']); ?>">
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-4">
                    <div class="mb-3">
                        <label for="fecha_reserva" class="form-label">Fecha de Reserva *</label>
                        <input type="date" class="form-control" id="fecha_reserva" name="fecha_reserva" 
                               value="<?php echo $reserva['fecha_reserva']; ?>" 
                               min="<?php echo date('Y-m-d'); ?>" required>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="mb-3">
                        <label for="hora_inicio" class="form-label">Hora de Inicio *</label>
                        <input type="time" class="form-control" id="hora_inicio" name="hora_inicio" 
                               value="<?php echo $reserva['hora_inicio']; ?>" required>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="mb-3">
                        <label for="hora_fin" class="form-label">Hora de Fin *</label>
                        <input type="time" class="form-control" id="hora_fin" name="hora_fin" 
                               value="<?php echo $reserva['hora_fin']; ?>" required>
                    </div>
                </div>
            </div>

            <div class="mb-3">
                <label for="observaciones" class="form-label">Observaciones</label>
                <textarea class="form-control" id="observaciones" name="observaciones" rows="3"><?php echo htmlspecialchars($reserva['observaciones']); ?></textarea>
            </div>

            <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                <a href="index.php?controller=reserva&action=index" class="btn btn-secondary me-md-2">
                    Cancelar
                </a>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Actualizar Reserva
                </button>
            </div>
        </form>
    </div>
</div>

<?php
$content = ob_get_clean();
include 'views/layout.php';
?>