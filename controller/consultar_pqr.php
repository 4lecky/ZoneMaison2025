<?php
require_once '../models/pqrsModel.php';

if (!isset($_GET['cedula'])) {
    echo "<p>No se recibió la cédula.</p>";
    exit;
}

$cedula = $_GET['cedula'];
$model = new PqrsModel();
$resultados = $model->obtenerPorIdentificacion($cedula);

if (!$resultados) {
    echo "<p>No se encontraron PQR con esa cédula.</p>";
    exit;
}
?>

<table id="tabla-resultado" class="display">
    <thead>
        <tr>
            <th>ID</th>
            <th>Asunto</th>
            <th>Mensaje</th>
            <th>Tipo</th>
            <th>Estado</th>
            <th>Fecha</th>
            <th>Acciones</th>
        </tr>
    </thead>
    <tbody>
    <?php foreach ($resultados as $pqr): ?>
        <tr>
            <td><?= $pqr['id'] ?></td>
            <td><?= htmlspecialchars($pqr['asunto']) ?></td>
            <td><?= htmlspecialchars($pqr['mensaje']) ?></td>
            <td><?= $pqr['tipo_pqr'] ?></td>
            <td><?= $pqr['estado'] ?></td>
            <td><?= $pqr['fecha_creacion'] ?></td>
            <td>
                <?php if ($pqr['estado'] === 'pendiente'): ?>
                    <button class="btn-eliminar" data-id="<?= $pqr['id'] ?>">Eliminar</button>
                    <button class="btn-editar"
                        data-id="<?= $pqr['id'] ?>"
                        data-nombres="<?= htmlspecialchars($pqr['nombres']) ?>"
                        data-apellidos="<?= htmlspecialchars($pqr['apellidos']) ?>"
                        data-identificacion="<?= $pqr['identificacion'] ?>"
                        data-email="<?= $pqr['email'] ?>"
                        data-telefono="<?= $pqr['telefono'] ?>"
                        data-tipo="<?= $pqr['tipo_pqr'] ?>"
                        data-asunto="<?= htmlspecialchars($pqr['asunto']) ?>"
                        data-mensaje="<?= htmlspecialchars($pqr['mensaje']) ?>"
                        data-medio="<?= $pqr['medio_respuesta'] ?>"
                    >Editar</button>
                <?php else: ?>
                    <span style="color: gray;">No disponible</span>
                <?php endif; ?>
            </td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>

<!-- Modal de edición (se inyecta con el resultado, JS lo activará) -->
<div id="modalEditar" class="modal" style="display:none;">
  <div class="modal-content" style="width:90%;max-height:80vh;overflow-y:auto;">
    <span class="close-editar">&times;</span>
    <h2>Editar PQR</h2>
    <form id="form-editar" enctype="multipart/form-data">
      <input type="hidden" name="id" id="edit-id">

      <label>Nombres:</label>
      <input type="text" name="nombres" id="edit-nombres" required>

      <label>Apellidos:</label>
      <input type="text" name="apellidos" id="edit-apellidos" required>

      <label>Identificación:</label>
      <input type="text" name="identificacion" id="edit-identificacion" required>

      <label>Email:</label>
      <input type="email" name="email" id="edit-email" required>

      <label>Teléfono:</label>
      <input type="text" name="telefono" id="edit-telefono" required>

      <label>Tipo PQR:</label>
      <select name="tipo_pqr" id="edit-tipo" required>
        <option value="peticion">Petición</option>
        <option value="queja">Queja</option>
        <option value="reclamo">Reclamo</option>
        <option value="sugerencia">Sugerencia</option>
      </select>

      <label>Asunto:</label>
      <input type="text" name="asunto" id="edit-asunto" required>

      <label>Mensaje:</label>
      <textarea name="mensaje" id="edit-mensaje" required></textarea>

      <label>¿Cómo quieres recibir respuesta?</label><br>
      <label><input type="checkbox" name="respuesta[]" value="correo" id="edit-resp-correo"> Correo</label>
      <label><input type="checkbox" name="respuesta[]" value="sms" id="edit-resp-sms"> SMS</label>

      <br><br>
      <label>Reemplazar archivo (opcional):</label>
      <input type="file" name="archivos" id="edit-archivos">

      <br><br>
      <button type="submit" class="btn">Guardar Cambios</button>
    </form>
  </div>
</div>

