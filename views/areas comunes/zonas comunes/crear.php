<?php require_once __DIR__ . '/../Layout/header.php'; ?>

<div class="container mt-4">
    <h2>Crear Nueva Zona Común</h2>
    
    <form action="index.php?controller=zona&action=crear" method="POST" enctype="multipart/form-data">
        <div class="row">
            <div class="col-md-6">
                <div class="mb-3">
                    <label for="nombre" class="form-label">Nombre *</label>
                    <input type="text" class="form-control" id="nombre" name="nombre" required>
                </div>
                
                <div class="mb-3">
                    <label for="descripcion" class="form-label">Descripción</label>
                    <textarea class="form-control" id="descripcion" name="descripcion" rows="3"></textarea>
                </div>
                
                <div class="mb-3">
                    <label for="imagen" class="form-label">Imagen</label>
                    <input type="file" class="form-control" id="imagen" name="imagen" accept="image/*">
                </div>
            </div>
            
            <div class="col-md-6">
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="capacidad" class="form-label">Capacidad *</label>
                            <input type="number" class="form-control" id="capacidad" name="capacidad" min="1" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="duracion_maxima" class="form-label">Duración Máxima (horas) *</label>
                            <input type="number" class="form-control" id="duracion_maxima" name="duracion_maxima" min="1" required>
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="hora_apertura" class="form-label">Hora Apertura *</label>
                            <input type="time" class="form-control" id="hora_apertura" name="hora_apertura" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="hora_cierre" class="form-label">Hora Cierre *</label>
                            <input type="time" class="form-control" id="hora_cierre" name="hora_cierre" required>
                        </div>
                    </div>
                </div>
                
                <div class="mb-3">
                    <label for="estado" class="form-label">Estado *</label>
                    <select class="form-select" id="estado" name="estado" required>
                        <option value="activo">Activo</option>
                        <option value="inactivo">Inactivo</option>
                        <option value="mantenimiento">En Mantenimiento</option>
                    </select>
                </div>
            </div>
        </div>
        
        <div class="d-grid gap-2 d-md-flex justify-content-md-end mt-3">
            <a href="index.php?controller=zona&action=index" class="btn btn-secondary me-md-2">
                <i class="fas fa-times"></i> Cancelar
            </a>
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save"></i> Guardar Zona
            </button>
        </div>
    </form>
</div>

<?php require_once __DIR__ . '/../Layout/footer.php'; ?>