-- Creación de la base de datos
CREATE DATABASE zonemaisons; 
USE zonemaisons;

-- ****** GESTIÓN DE USUARIOS ******
CREATE TABLE tbl_rol (
    rol_id INT AUTO_INCREMENT,
    rol_nombre ENUM('Administrador', 'Residente', 'Propietario', 'Vigilante') NOT NULL,
    PRIMARY KEY (rol_id)
);

CREATE TABLE tbl_usuario (
    usuario_cc INT(10) NOT NULL AUTO_INCREMENT,
    usu_nombre_completo VARCHAR(60) NOT NULL,
    usu_telefono BIGINT(10) NOT NULL,
    usu_correo VARCHAR(50) NOT NULL,
    usu_password VARCHAR(30) NOT NULL,
    usu_estado ENUM('Activo', 'Inactivo') NOT NULL,
    usu_rol_id INT NOT NULL,
    PRIMARY KEY (usuario_cc),
    UNIQUE (usu_correo),
    UNIQUE (usu_telefono),
    FOREIGN KEY (usu_rol_id) REFERENCES tbl_rol(rol_id)
);

-- Tabla para torres/edificios del conjunto residencial
CREATE TABLE tbl_torre (
    torre_id INT AUTO_INCREMENT,
    torre_nombre VARCHAR(100) NOT NULL,
    torre_descripcion VARCHAR(255),
    PRIMARY KEY (torre_id)
);

-- Tabla para apartamentos
CREATE TABLE tbl_apartamento (
    apto_id INT AUTO_INCREMENT,
    apto_numero VARCHAR(20) NOT NULL,
    apto_torre_id INT NOT NULL,
    apto_area DECIMAL(8,2),
    PRIMARY KEY (apto_id),
    FOREIGN KEY (apto_torre_id) REFERENCES tbl_torre(torre_id),
    UNIQUE (apto_numero, apto_torre_id)
);

-- Tabla para parqueaderos
CREATE TABLE tbl_parqueadero (
    parq_id INT AUTO_INCREMENT,
    parq_codigo VARCHAR(20) NOT NULL,
    parq_estado ENUM('Disponible', 'Ocupado', 'Mantenimiento') NOT NULL,
    parq_tipo ENUM('Residente', 'Visitante') NOT NULL,
    PRIMARY KEY (parq_id),
    UNIQUE (parq_codigo)
);

-- Tabla relación usuario-apartamento (permite múltiples propiedades)
CREATE TABLE tbl_usuario_apartamento (
    usapto_id INT AUTO_INCREMENT,
    usapto_usuario_cc INT NOT NULL,
    usapto_apto_id INT NOT NULL,
    usapto_es_residente BOOLEAN NOT NULL DEFAULT FALSE,
    PRIMARY KEY (usapto_id),
    FOREIGN KEY (usapto_usuario_cc) REFERENCES tbl_usuario(usuario_cc),
    FOREIGN KEY (usapto_apto_id) REFERENCES tbl_apartamento(apto_id),
    UNIQUE (usapto_usuario_cc, usapto_apto_id)
);

-- Tabla relación usuario-parqueadero (permite múltiples asignaciones)
CREATE TABLE tbl_usuario_parqueadero (
    usparq_id INT AUTO_INCREMENT,
    usparq_usuario_cc INT NOT NULL,
    usparq_parq_id INT NOT NULL,
    usparq_fecha_asignacion DATE NOT NULL,
    PRIMARY KEY (usparq_id),
    FOREIGN KEY (usparq_usuario_cc) REFERENCES tbl_usuario(usuario_cc),
    FOREIGN KEY (usparq_parq_id) REFERENCES tbl_parqueadero(parq_id),
    UNIQUE (usparq_parq_id)  -- Un parqueadero solo puede estar asignado a un usuario
);

-- ****** GESTIÓN DE PAGOS Y MORAS ******
CREATE TABLE tbl_concepto_pago (
    conpag_id INT AUTO_INCREMENT,
    conpag_nombre VARCHAR(100) NOT NULL,
    conpag_descripcion VARCHAR(255),
    PRIMARY KEY (conpag_id)
);

CREATE TABLE tbl_estado_pago (
    estpag_id INT AUTO_INCREMENT,
    estpag_nombre ENUM('Pendiente', 'Pagado', 'Vencido', 'En mora') NOT NULL,
    PRIMARY KEY (estpag_id)
);

CREATE TABLE tbl_pago (
    pago_id INT AUTO_INCREMENT,
    pago_usuario_cc INT NOT NULL,
    pago_apto_id INT NOT NULL,
    pago_concepto_id INT NOT NULL,
    pago_valor DECIMAL(10,2) NOT NULL,
    pago_fecha_emision DATE NOT NULL,
    pago_fecha_vencimiento DATE NOT NULL,
    pago_fecha_pago DATE,
    pago_estado_id INT NOT NULL,
    PRIMARY KEY (pago_id),
    FOREIGN KEY (pago_usuario_cc) REFERENCES tbl_usuario(usuario_cc),
    FOREIGN KEY (pago_apto_id) REFERENCES tbl_apartamento(apto_id),
    FOREIGN KEY (pago_concepto_id) REFERENCES tbl_concepto_pago(conpag_id),
    FOREIGN KEY (pago_estado_id) REFERENCES tbl_estado_pago(estpag_id)
);

-- ****** GESTIÓN DE ÁREAS COMUNES Y EVENTOS ******
CREATE TABLE tbl_area_comun (
    area_id INT AUTO_INCREMENT,
    area_nombre VARCHAR(100) NOT NULL,
    area_descripcion VARCHAR(255) NOT NULL,
    area_estado ENUM('Disponible', 'No disponible', 'Mantenimiento') NOT NULL,
    area_capacidad INT NOT NULL,
    area_costo_hora DECIMAL(10,2) NOT NULL,
    area_terminos_condiciones TEXT NOT NULL,
    PRIMARY KEY (area_id)
);

-- Tabla para almacenar multimedia de áreas comunes
CREATE TABLE tbl_area_multimedia (
    armul_id INT AUTO_INCREMENT,
    armul_area_id INT NOT NULL,
    armul_tipo ENUM('Foto', 'Video', 'URL') NOT NULL,
    armul_ruta VARCHAR(255) NOT NULL,
    armul_descripcion VARCHAR(255),
    PRIMARY KEY (armul_id),
    FOREIGN KEY (armul_area_id) REFERENCES tbl_area_comun(area_id)
);

CREATE TABLE tbl_evento (
    even_id INT AUTO_INCREMENT,
    even_titulo VARCHAR(100) NOT NULL,
    even_descripcion TEXT,
    even_area_id INT NOT NULL,
    even_fecha DATE NOT NULL,
    even_hora_inicio TIME NOT NULL,
    even_hora_fin TIME NOT NULL,
    even_organizador_cc INT NOT NULL,
    PRIMARY KEY (even_id),
    FOREIGN KEY (even_area_id) REFERENCES tbl_area_comun(area_id),
    FOREIGN KEY (even_organizador_cc) REFERENCES tbl_usuario(usuario_cc)
);

-- ****** SISTEMA DE RESERVAS ******
CREATE TABLE tbl_estado_reserva (
    estreser_id INT AUTO_INCREMENT,
    estreser_nombre ENUM('Solicitada', 'Confirmada', 'Cancelada', 'Finalizada') NOT NULL,
    PRIMARY KEY (estreser_id)
);

CREATE TABLE tbl_reserva (
    rese_id INT AUTO_INCREMENT,
    rese_usuario_cc INT NOT NULL,
    rese_area_id INT NOT NULL,
    rese_descripcion VARCHAR(255) NOT NULL,
    rese_fecha_inicio DATE NOT NULL,
    rese_fecha_fin DATE NOT NULL,
    rese_hora_inicio TIME NOT NULL,
    rese_hora_fin TIME NOT NULL,
    rese_valor_base DECIMAL(10,2) NOT NULL,
    rese_impuesto DECIMAL(10,2) NOT NULL,
    rese_valor_total DECIMAL(10,2) NOT NULL,
    rese_estado_id INT NOT NULL,
    rese_fecha_solicitud TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (rese_id),
    FOREIGN KEY (rese_usuario_cc) REFERENCES tbl_usuario(usuario_cc),
    FOREIGN KEY (rese_area_id) REFERENCES tbl_area_comun(area_id),
    FOREIGN KEY (rese_estado_id) REFERENCES tbl_estado_reserva(estreser_id)
);

-- ****** CONTROL DE PAQUETERÍA ******
CREATE TABLE tbl_estado_paquete (
    estpaq_id INT AUTO_INCREMENT,
    estpaq_nombre ENUM('Recibido', 'Notificado', 'Entregado') NOT NULL,
    PRIMARY KEY (estpaq_id)
);

CREATE TABLE tbl_paquete (
    paq_id INT AUTO_INCREMENT,
    paq_destinatario_cc INT NOT NULL,
    paq_descripcion VARCHAR(255) NOT NULL,
    paq_fecha_llegada DATETIME NOT NULL,
    paq_fecha_entrega DATETIME,
    paq_estado_id INT NOT NULL,
    paq_recibido_por INT NOT NULL,
    paq_entregado_por INT,
    PRIMARY KEY (paq_id),
    FOREIGN KEY (paq_destinatario_cc) REFERENCES tbl_usuario(usuario_cc),
    FOREIGN KEY (paq_estado_id) REFERENCES tbl_estado_paquete(estpaq_id),
    FOREIGN KEY (paq_recibido_por) REFERENCES tbl_usuario(usuario_cc),
    FOREIGN KEY (paq_entregado_por) REFERENCES tbl_usuario(usuario_cc)
);

CREATE TABLE tbl_paquete_firma (
    paqfir_id INT AUTO_INCREMENT,
    paqfir_paquete_id INT NOT NULL,
    paqfir_firma BLOB NOT NULL,
    paqfir_fecha DATETIME NOT NULL,
    PRIMARY KEY (paqfir_id),
    FOREIGN KEY (paqfir_paquete_id) REFERENCES tbl_paquete(paq_id)
);

-- ****** GESTIÓN DE NOTIFICACIONES ******
CREATE TABLE tbl_tipo_notificacion (
    tipnot_id INT AUTO_INCREMENT,
    tipnot_nombre VARCHAR(50) NOT NULL,
    tipnot_descripcion VARCHAR(255),
    PRIMARY KEY (tipnot_id)
);

CREATE TABLE tbl_notificacion (
    noti_id INT AUTO_INCREMENT,
    noti_tipo_id INT NOT NULL,
    noti_usuario_cc INT NOT NULL,
    noti_fecha_creacion DATETIME NOT NULL,
    noti_fecha_lectura DATETIME,
    noti_titulo VARCHAR(100) NOT NULL,
    noti_contenido TEXT NOT NULL,
    noti_referencia_id INT,  -- ID de referencia (paquete, evento, pago, etc.)
    noti_tabla_referencia VARCHAR(50),  -- Nombre de la tabla referenciada
    PRIMARY KEY (noti_id),
    FOREIGN KEY (noti_tipo_id) REFERENCES tbl_tipo_notificacion(tipnot_id),
    FOREIGN KEY (noti_usuario_cc) REFERENCES tbl_usuario(usuario_cc)
);

-- ****** NOVEDADES DEL CONJUNTO ******
CREATE TABLE tbl_novedad (
    nov_id INT AUTO_INCREMENT,
    nov_titulo VARCHAR(100) NOT NULL,
    nov_contenido TEXT NOT NULL,
    nov_fecha_publicacion DATETIME NOT NULL,
    nov_fecha_fin DATETIME,
    nov_publicado_por INT NOT NULL,
    nov_destacado BOOLEAN NOT NULL DEFAULT FALSE,
    PRIMARY KEY (nov_id),
    FOREIGN KEY (nov_publicado_por) REFERENCES tbl_usuario(usuario_cc)
);

CREATE TABLE tbl_novedad_multimedia (
    novmul_id INT AUTO_INCREMENT,
    novmul_novedad_id INT NOT NULL,
    novmul_tipo ENUM('Imagen', 'Video', 'Documento', 'URL') NOT NULL,
    novmul_ruta VARCHAR(255) NOT NULL,
    novmul_descripcion VARCHAR(255),
    PRIMARY KEY (novmul_id),
    FOREIGN KEY (novmul_novedad_id) REFERENCES tbl_novedad(nov_id)
);

-- ****** CONTROL DE VISITAS ******
CREATE TABLE tbl_tipo_visitante (
    tipvis_id INT AUTO_INCREMENT,
    tipvis_nombre ENUM('Familiar', 'Amigo', 'Servicio', 'Proveedor', 'Otro') NOT NULL,
    PRIMARY KEY (tipvis_id)
);

CREATE TABLE tbl_visitante (
    visi_id INT AUTO_INCREMENT,
    visi_nombre VARCHAR(100) NOT NULL,
    visi_documento VARCHAR(20) NOT NULL,
    visi_telefono BIGINT(20) NOT NULL,
    visi_tipo_id INT NOT NULL,
    visi_observaciones VARCHAR(255),
    PRIMARY KEY (visi_id),
    UNIQUE (visi_documento),
    FOREIGN KEY (visi_tipo_id) REFERENCES tbl_tipo_visitante(tipvis_id)
);

CREATE TABLE tbl_visita (
    vis_id INT AUTO_INCREMENT,
    vis_visitante_id INT NOT NULL,
    vis_usuario_destino_cc INT NOT NULL,
    vis_apartamento_id INT NOT NULL,
    vis_fecha_entrada DATETIME NOT NULL,
    vis_fecha_salida DATETIME,
    vis_autorizada_por INT,
    vis_registrada_por INT NOT NULL,
    vis_motivo VARCHAR(255),
    PRIMARY KEY (vis_id),
    FOREIGN KEY (vis_visitante_id) REFERENCES tbl_visitante(visi_id),
    FOREIGN KEY (vis_usuario_destino_cc) REFERENCES tbl_usuario(usuario_cc),
    FOREIGN KEY (vis_apartamento_id) REFERENCES tbl_apartamento(apto_id),
    FOREIGN KEY (vis_autorizada_por) REFERENCES tbl_usuario(usuario_cc),
    FOREIGN KEY (vis_registrada_por) REFERENCES tbl_usuario(usuario_cc)
);

-- ****** ALQUILER DE PARQUEADEROS VISITANTES ******
CREATE TABLE tbl_vehiculo (
    vehi_id INT AUTO_INCREMENT,
    vehi_placa VARCHAR(10) NOT NULL,
    vehi_marca VARCHAR(50) NOT NULL,
    vehi_modelo VARCHAR(50) NOT NULL,
    vehi_color VARCHAR(30) NOT NULL,
    vehi_tipo ENUM('Automóvil', 'Motocicleta', 'Camioneta', 'Otro') NOT NULL,
    vehi_visitante_id INT,
    vehi_usuario_cc INT,
    PRIMARY KEY (vehi_id),
    UNIQUE (vehi_placa),
    FOREIGN KEY (vehi_visitante_id) REFERENCES tbl_visitante(visi_id),
    FOREIGN KEY (vehi_usuario_cc) REFERENCES tbl_usuario(usuario_cc)
);

CREATE TABLE tbl_alquiler_parqueadero (
    alqp_id INT AUTO_INCREMENT,
    alqp_parqueadero_id INT NOT NULL,
    alqp_vehiculo_id INT NOT NULL,
    alqp_fecha_ingreso DATETIME NOT NULL,
    alqp_fecha_salida DATETIME,
    alqp_precio_hora DECIMAL(10,2) NOT NULL,
    alqp_impuesto DECIMAL(10,2) NOT NULL,
    alqp_horas INT,
    alqp_valor_total DECIMAL(10,2),
    alqp_registrado_por INT NOT NULL,
    alqp_observaciones VARCHAR(255),
    PRIMARY KEY (alqp_id),
    FOREIGN KEY (alqp_parqueadero_id) REFERENCES tbl_parqueadero(parq_id),
    FOREIGN KEY (alqp_vehiculo_id) REFERENCES tbl_vehiculo(vehi_id),
    FOREIGN KEY (alqp_registrado_por) REFERENCES tbl_usuario(usuario_cc)
);

-- Tabla para multas asociadas a un alquiler
CREATE TABLE tbl_multa (
    mult_id INT AUTO_INCREMENT,
    mult_alquiler_id INT NOT NULL,
    mult_valor DECIMAL(10,2) NOT NULL,
    mult_fecha DATETIME NOT NULL,
    mult_vigencia_hasta DATE NOT NULL,
    mult_descripcion VARCHAR(255) NOT NULL,
    mult_registrada_por INT NOT NULL,
    PRIMARY KEY (mult_id),
    FOREIGN KEY (mult_alquiler_id) REFERENCES tbl_alquiler_parqueadero(alqp_id),
    FOREIGN KEY (mult_registrada_por) REFERENCES tbl_usuario(usuario_cc)
);

-- ****** SISTEMA PQRS ******
CREATE TABLE tbl_tipo_pqrs (
    tippqr_id INT AUTO_INCREMENT,
    tippqr_nombre ENUM('Petición', 'Queja', 'Reclamo', 'Sugerencia') NOT NULL,
    PRIMARY KEY (tippqr_id)
);

CREATE TABLE tbl_estado_pqrs (
    estpqr_id INT AUTO_INCREMENT,
    estpqr_nombre ENUM('Recibido', 'En proceso', 'Resuelto', 'Rechazado') NOT NULL,
    PRIMARY KEY (estpqr_id)
);

CREATE TABLE tbl_pqrs (
    pqr_id INT AUTO_INCREMENT,
    pqr_usuario_cc INT NOT NULL,
    pqr_tipo_id INT NOT NULL,
    pqr_titulo VARCHAR(100) NOT NULL,
    pqr_descripcion TEXT NOT NULL,
    pqr_fecha_creacion DATETIME NOT NULL,
    pqr_fecha_respuesta DATETIME,
    pqr_respuesta TEXT,
    pqr_estado_id INT NOT NULL,
    pqr_asignado_a INT,
    PRIMARY KEY (pqr_id),
    FOREIGN KEY (pqr_usuario_cc) REFERENCES tbl_usuario(usuario_cc),
    FOREIGN KEY (pqr_tipo_id) REFERENCES tbl_tipo_pqrs(tippqr_id),
    FOREIGN KEY (pqr_estado_id) REFERENCES tbl_estado_pqrs(estpqr_id),
    FOREIGN KEY (pqr_asignado_a) REFERENCES tbl_usuario(usuario_cc)
);

CREATE TABLE tbl_archivo_pqrs (
    archpqr_id INT AUTO_INCREMENT,
    archpqr_pqrs_id INT NOT NULL,
    archpqr_nombre VARCHAR(255) NOT NULL,
    archpqr_tipo VARCHAR(100) NOT NULL,
    archpqr_ruta VARCHAR(255) NOT NULL,
    archpqr_fecha_carga DATETIME NOT NULL,
    PRIMARY KEY (archpqr_id),
    FOREIGN KEY (archpqr_pqrs_id) REFERENCES tbl_pqrs(pqr_id)
);

-- Inserciones iniciales para catálogos
INSERT INTO tbl_rol (rol_nombre) VALUES 
('Administrador'), ('Residente'), ('Propietario'), ('Vigilante');

INSERT INTO tbl_estado_pago (estpag_nombre) VALUES 
('Pendiente'), ('Pagado'), ('Vencido'), ('En mora');

INSERT INTO tbl_estado_reserva (estreser_nombre) VALUES 
('Solicitada'), ('Confirmada'), ('Cancelada'), ('Finalizada');

INSERT INTO tbl_estado_paquete (estpaq_nombre) VALUES 
('Recibido'), ('Notificado'), ('Entregado');

INSERT INTO tbl_tipo_visitante (tipvis_nombre) VALUES 
('Familiar'), ('Amigo'), ('Servicio'), ('Proveedor'), ('Otro');

INSERT INTO tbl_tipo_pqrs (tippqr_nombre) VALUES 
('Petición'), ('Queja'), ('Reclamo'), ('Sugerencia');

INSERT INTO tbl_estado_pqrs (estpqr_nombre) VALUES 
('Recibido'), ('En proceso'), ('Resuelto'), ('Rechazado');