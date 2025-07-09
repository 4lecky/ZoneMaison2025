<?php
// views/zonas/editar.php
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Zona Común</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-4">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h4 class="mb-0">
                            <i class="fas fa-edit"></i> Editar Zona Común
                        </h4>
                    </div>
                    <div class="card-body">
                        <!-- Botón volver -->
                        <div class="mb-3">
                            <a href="index.php?controller=zona&action=index" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Volver al listado
                            </a>
                        </div>

                        <!-- Mensajes de error -->
                        <?php if (isset($_SESSION['mensaje_error'])): ?>
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <?php echo $_SESSION['mensaje_error']; unset($_SESSION['mensaje_error']); ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        <?php endif; ?>

                        <?php if ($zona): ?>
                            <!-- Formulario -->
                            <form method="POST" enctype="multipart/form-data" id="formZona">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="nombre" class="form-label">
                                                <i class="fas fa-tag"></i> Nombre *
                                            </label>
                                            <input type="text" 
                                                   class="form-control" 
                                                   id="nombre" 
                                                   name="nombre" 
                                                   required 
                                                   maxlength="100"
                                                   value="<?php echo htmlspecialchars($zona['nombre']); ?>">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="capacidad" class="form-label">
                                                <i class="fas fa-users"></i> Capacidad *
                                            </label>
                                            <input type="number" 
                                                   class="form-control" 
                                                   id="capacidad" 
                                                   name="capacidad" 
                                                   required 
                                                   min="1" 
                                                   max="1000"
                                                   value="<?php echo htmlspecialchars($zona['capacidad']); ?>">
                                        </div>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label for="descripcion" class="form-label">
                                        <i class="fas fa-align-left"></i> Descripción
                                    </label>
                                    <textarea class="form-control" 
                                              id="descripcion" 
                                              name="descripcion" 
                                              rows="3" 
                                              maxlength="500"><?php echo htmlspecialchars($zona['descripcion']); ?></textarea>
                                </div>

                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label for="hora_apertura" class="form-label">
                                                <i class="fas fa-clock"></i> Hora Apertura
                                            </label>
                                            <input type="time" 
                                                   class="form-control" 
                                                   id="hora_apertura" 
                                                   name="hora_apertura" 
                                                   value="<?php echo htmlspecialchars($zona['hora_apertura']); ?>">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label for="hora_cierre" class="form-label">
                                                <i class="fas fa-clock"></i> Hora Cierre
                                            </label>
                                            <input type="time" 
                                                   class="form-control" 
                                                   id="hora_cierre" 
                                                   name="hora_cierre" 
                                                   value="<?php echo htmlspecialchars($zona['hora_cierre']); ?>">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label for="duracion_maxima" class="form-label">
                                                <i class="fas fa-hourglass-half"></i> Duración Máxima (horas)
                                            </label>
                                            <input type="number" 
                                                   class="form-control" 
                                                   id="duracion_maxima" 
                                                   name="duracion_maxima" 
                                                   min="1" 
                                                   max="24" 
                                                   value="<?php echo htmlspecialchars($zona['duracion_maxima']); ?>">
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="estado" class="form-label">
                                                <i class="fas fa-toggle-on"></i> Estado
                                            </label>
                                            <select class="form-select" id="estado" name="estado">
                                                <option value="activo" <?php echo $zona['estado'] === 'activo' ? 'selected' : ''; ?>>Activo</option>
                                                <option value="inactivo" <?php echo $zona['estado'] === 'inactivo' ? 'selected' : ''; ?>>Inactivo</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="imagen" class="form-label">
                                                <i class="fas fa-image"></i> Imagen
                                            </label>
                                            <input type="file" 
                                                   class="form-control" 
                                                   id="imagen" 
                                                   name="imagen" 
                                                   accept="image/*"
                                                   onchange="previewImage(this)">
                                            <small class="form-text text-muted">
                                                Formatos permitidos: JPG, PNG, GIF (máx. 2MB)
                                            </small>
                                        </div>
                                    </div>
                                </div>

                                <!-- Imagen actual -->
                                <?php if ($zona['imagen']): ?>
                                    <div class="mb-3">
                                        <label class="form-label">Imagen actual:</label>
                                        <div>
                                            <img src="assets/img/zonas/<?php echo htmlspecialchars($zona['imagen']); ?>" 
                                                 alt="<?php echo htmlspecialchars($zona['nombre']); ?>" 
                                                 class="img-thumbnail" 
                                                 style="max-width: 200px;"
                                                 id="currentImage">
                                        </div>
                                    </div>
                                <?php endif; ?>

                                <!-- Preview de nueva imagen -->
                                <div class="mb-3" id="imagePreview" style="display: none;">
                                    <label class="form-label">Nueva imagen:</label>
                                    <div>
                                        <img id="preview" src="" alt="Vista previa" class="img-thumbnail" style="max-width: 200px;">
                                    </div>
                                </div>

                                <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                                    <a href="index.php?controller=zona&action=index" class="btn btn-secondary me-md-2">
                                        <i class="fas fa-times"></i> Cancelar
                                    </a>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save"></i> Actualizar Zona
                                    </button>
                                </div>
                            </form>
                        <?php else: ?>
                            <div class="alert alert-danger">
                                <i class="fas fa-exclamation-triangle"></i> No se encontró la zona común especificada.
                            </div>
                            <a href="index.php?controller=zona&action=index" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Volver al listado
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function previewImage(input) {
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    document.getElementById('preview').src = e.target.result;
                    document.getElementById('imagePreview').style.display = 'block';
                    
                    // Ocultar imagen actual si existe
                    const currentImage = document.getElementById('currentImage');
                    if (currentImage) {
                        currentImage.style.opacity = '0.5';
                    }
                }
                reader.readAsDataURL(input.files[0]);
            }
        }

        // Validación del formulario
        document.getElementById('formZona').addEventListener('submit', function(e) {
            const nombre = document.getElementById('nombre').value.trim();
            const capacidad = document.getElementById('capacidad').value;
            const horaApertura = document.getElementById('hora_apertura').value;
            const horaCierre = document.getElementById('hora_cierre').value;

            if (!nombre) {
                alert('El nombre es requerido');
                e.preventDefault();
                return;
            }

            if (!capacidad || capacidad < 1) {
                alert('La capacidad debe ser mayor a 0');
                e.preventDefault();
                return;
            }

            if (horaApertura && horaCierre && horaApertura >= horaCierre) {
                alert('La hora de apertura debe ser menor a la hora de cierre');
                e.preventDefault();
                return;
            }
        });
    </script>
</body>
</html>