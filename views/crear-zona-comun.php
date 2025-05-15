<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ZONEMAISONS - Administrador</title>
    <link rel="stylesheet" href="../assets/css/areas-comunes/crear-zona-comun.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
    <header>
        <div class="logo-container">
            <img src="img/logo.jpg" alt="ZONEMAISONS Logo" class="logo">
            <div class="title-container">
                <h1>ZONEMAISONS</h1>
                <div class="underline"></div>
            </div>
        </div>
        <nav class="menu-button">
            <div class="hamburger">≡</div>
        </nav>
    </header>
    
    <nav class="main-nav">
        <ul>
            <li><a href="admin-dashboard.html"><i class="fas fa-tachometer-alt"></i> Panel</a></li>
            <li><a href="admin-reservations.html"><i class="fas fa-calendar-check"></i> Reservas</a></li>
            <li><a href="admin-zones.html" class="active"><i class="fas fa-home"></i> Zonas Comunes</a></li>
            <li><a href="admin-users.html"><i class="fas fa-users"></i> Usuarios</a></li>
            <li><a href="admin-pqrs.html"><i class="fas fa-comments"></i> PQRS</a></li>
        </ul>
    </nav>
    
    <main class="admin-page">
        <div class="admin-header">
            <h2><i class="fas fa-plus-circle"></i> Crear Nueva Zona Común</h2>
            <div class="breadcrumb">
                <a href="admin-zones.html">Zonas Comunes</a> / <span>Nueva Zona</span>
            </div>
        </div>
        
        <div class="admin-content">
            <form id="create-zone-form" class="admin-form">
                <div class="form-section">
                    <h3><i class="fas fa-info-circle"></i> Información Básica</h3>
                    <div class="form-group">
                        <label for="zone-name">Nombre de la Zona*</label>
                        <input type="text" id="zone-name" name="zone-name" required placeholder="Ej: Salón Comunal, Piscina, Gimnasio">
                    </div>
                    
                    <div class="form-group">
                        <label for="zone-description">Descripción*</label>
                        <textarea id="zone-description" name="zone-description" rows="4" required placeholder="Describe las características principales de la zona"></textarea>
                    </div>
                    
                    <div class="form-group">
                        <label for="zone-capacity">Capacidad Máxima</label>
                        <input type="number" id="zone-capacity" name="zone-capacity" placeholder="Número de personas">
                    </div>
                    
                    <div class="form-group">
                        <label for="zone-category">Categoría</label>
                        <select id="zone-category" name="zone-category">
                            <option value="">Seleccione una categoría</option>
                            <option value="salon">Salón de Eventos</option>
                            <option value="deporte">Área Deportiva</option>
                            <option value="recreacion">Zona de Recreación</option>
                            <option value="otros">Otros</option>
                        </select>
                    </div>
                </div>
                
                <div class="form-section">
                    <h3><i class="fas fa-images"></i> Imágenes</h3>
                    <div class="image-upload-container">
                        <div class="image-upload-box">
                            <input type="file" id="zone-images" name="zone-images" accept="image/*" multiple class="hidden-upload">
                            <label for="zone-images" class="upload-label">
                                <i class="fas fa-cloud-upload-alt"></i>
                                <span>Arrastra imágenes aquí o haz clic para seleccionar</span>
                                <span class="upload-hint">Máximo 5 imágenes (JPEG/PNG, 5MB max cada una)</span>
                            </label>
                        </div>
                        <div id="image-preview" class="image-preview-grid">
                            <!-- Las imágenes seleccionadas aparecerán aquí -->
                        </div>
                    </div>
                </div>
                
                <div class="form-section">
                    <h3><i class="fas fa-clock"></i> Horarios y Disponibilidad</h3>
                    
                    <div class="form-row">
                        <div class="form-group half-width">
                            <label for="opening-time">Hora de Apertura*</label>
                            <input type="time" id="opening-time" name="opening-time" required>
                        </div>
                        
                        <div class="form-group half-width">
                            <label for="closing-time">Hora de Cierre*</label>
                            <input type="time" id="closing-time" name="closing-time" required>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label>Días Disponibles*</label>
                        <div class="checkbox-group">
                            <label class="checkbox-label">
                                <input type="checkbox" name="available-days" value="Lunes" checked> Lunes
                            </label>
                            <label class="checkbox-label">
                                <input type="checkbox" name="available-days" value="Martes" checked> Martes
                            </label>
                            <label class="checkbox-label">
                                <input type="checkbox" name="available-days" value="Miércoles" checked> Miércoles
                            </label>
                            <label class="checkbox-label">
                                <input type="checkbox" name="available-days" value="Jueves" checked> Jueves
                            </label>
                            <label class="checkbox-label">
                                <input type="checkbox" name="available-days" value="Viernes" checked> Viernes
                            </label>
                            <label class="checkbox-label">
                                <input type="checkbox" name="available-days" value="Sábado" checked> Sábado
                            </label>
                            <label class="checkbox-label">
                                <input type="checkbox" name="available-days" value="Domingo" checked> Domingo
                            </label>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="max-hours">Duración Máxima de Reserva (horas)</label>
                        <input type="number" id="max-hours" name="max-hours" min="1" max="24" value="5">
                    </div>
                    
                    <div class="form-group">
                        <label for="advance-notice">Anticipación Mínima para Reservar (horas)</label>
                        <input type="number" id="advance-notice" name="advance-notice" min="1" value="48">
                    </div>
                </div>
                
                <div class="form-section">
                    <h3><i class="fas fa-file-contract"></i> Términos y Condiciones</h3>
                    <div class="form-group">
                        <label for="default-terms">Términos Predeterminados</label>
                        <select id="default-terms" name="default-terms" class="terms-select">
                            <option value="">Seleccionar plantilla</option>
                            <option value="salon">Salón de Eventos</option>
                            <option value="deportivo">Área Deportiva</option>
                            <option value="piscina">Piscina</option>
                            <option value="custom">Personalizado</option>
                        </select>
                    </div>
                    
                    <div class="form-group" id="custom-terms-container" style="display: none;">
                        <label for="custom-terms">Términos Personalizados</label>
                        <div id="terms-editor">
                            <div class="term-item">
                                <input type="text" class="term-input" placeholder="Nuevo término">
                                <button type="button" class="remove-term"><i class="fas fa-times"></i></button>
                            </div>
                        </div>
                        <button type="button" id="add-term" class="add-term-button">
                            <i class="fas fa-plus"></i> Agregar Término
                        </button>
                    </div>
                    
                    <div class="form-group">
                        <label for="additional-rules">Reglas Adicionales</label>
                        <textarea id="additional-rules" name="additional-rules" rows="3" placeholder="Otras reglas o consideraciones especiales"></textarea>
                    </div>
                </div>
                
                <div class="form-section">
                    <h3><i class="fas fa-cog"></i> Configuración Adicional</h3>
                    
                    <div class="form-group">
                        <label class="switch-label">
                            <input type="checkbox" id="requires-approval" name="requires-approval">
                            <span class="switch-slider"></span>
                            <span class="switch-text">¿Requiere aprobación del administrador?</span>
                        </label>
                    </div>
                    
                    <div class="form-group">
                        <label class="switch-label">
                            <input type="checkbox" id="allow-recurring" name="allow-recurring" checked>
                            <span class="switch-slider"></span>
                            <span class="switch-text">Permitir reservas recurrentes</span>
                        </label>
                    </div>
                    
                    <div class="form-group">
                        <label class="switch-label">
                            <input type="checkbox" id="is-active" name="is-active" checked>
                            <span class="switch-slider"></span>
                            <span class="switch-text">Activar zona para reservas</span>
                        </label>
                    </div>
                </div>
                
                <div class="form-actions">
                    <button type="button" class="cancel-button">
                        <i class="fas fa-times"></i> Cancelar
                    </button>
                    <button type="submit" class="save-button">
                        <i class="fas fa-save"></i> Guardar Zona
                    </button>
                </div>
            </form>
        </div>
    </main>
    
    <script src="js/admin-zones.js"></script>
</body>
</html>