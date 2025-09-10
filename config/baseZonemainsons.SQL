CREATE DATABASE zonemaisons; 
USE zonemaisons;

/* Tablas */   

CREATE TABLE if NOT EXISTS tbl_mora (mor_id INT(10) AUTO_INCREMENT,
    mor_estado ENUM('Pendiente', 'Pagado','No aplica'),
    PRIMARY KEY (mor_id));  

INSERT INTO tbl_mora (mor_estado)
VALUES ('Pagado'),
('Pendiente'),
('No aplica'); 

/* MODIFICACIÓN*/
CREATE TABLE if NOT EXISTS tbl_usuario (usuario_cc INT (10) AUTO_INCREMENT,
    usu_cedula INT (255) NOT null, 
    usu_tipo_documento ENUM ('Cedula de ciudadania', 'Cedula de extranjeria', 'Pasaporte', 'Permiso especial de permanencia (PEP)'),
    usu_nombre_completo Varchar(60) NOT null,
    usu_telefono BIGINT (10) NOT NULL,
    usu_correo Varchar(50) NOT null,
    usu_password Varchar(225) NOT null,
	usu_apartamento_residencia  Varchar(100) NOT null,
	usu_torre_residencia Varchar(100) NOT null,
	usu_parqueadero_asignado  Varchar(20) NOT null,
	usu_propiedades  Varchar(100) NOT null,  
    usu_rol ENUM ('Administrador', 'Residente', 'Propietario', 'Vigilante', 'Usuario') DEFAULT 'Usuario',
    usu_estado ENUM ('Activo', 'Inactivo') DEFAULT 'Activo' NOT null,
    usu_mora INT DEFAULT 1,
    request_password enum('0','1') NOT NULL DEFAULT '0',
    token_password varchar(200) DEFAULT NULL,
    expired_session varchar(40) DEFAULT NULL,
    PRIMARY KEY (usuario_cc),
    FOREIGN KEY (usu_mora) REFERENCES tbl_mora (mor_id),
    UNIQUE (usu_correo),
    UNIQUE (usu_telefono),
    UNIQUE (usu_cedula)
);                


-- MÓDULO DE RESERVAS Y ZONAS COMUNES ACTUALIZADO
CREATE TABLE IF NOT EXISTS tbl_zonas (
    zona_id INT(11) NOT NULL AUTO_INCREMENT,
    zona_nombre VARCHAR(100) NOT NULL,
    zona_descripcion TEXT DEFAULT NULL,
    zona_capacidad INT(11) DEFAULT NULL,
    zona_estado ENUM('activo','inactivo','mantenimiento') DEFAULT 'activo',
    zona_imagen VARCHAR(255) DEFAULT NULL,
    zona_hora_apertura TIME DEFAULT '08:00:00',
    zona_hora_cierre TIME DEFAULT '20:00:00',
    zona_duracion_maxima INT(11) DEFAULT 2,
    zona_terminos_condiciones TEXT DEFAULT NULL,
    zona_fecha_creacion TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    zona_fecha_actualizacion TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (zona_id),
    KEY idx_zona_estado (zona_estado),
    KEY idx_zona_nombre (zona_nombre)
);

CREATE TABLE IF NOT EXISTS tbl_reservas (
    reserva_id INT(11) NOT NULL AUTO_INCREMENT,
    zona_id INT(11) NOT NULL,
    usuario_id INT(11) NOT NULL,
    reserva_apartamento VARCHAR(50) NOT NULL,
    reserva_nombre_residente VARCHAR(100) NOT NULL,
    reserva_fecha DATE NOT NULL,
    reserva_hora_inicio TIME NOT NULL,
    reserva_hora_fin TIME NOT NULL,
    reserva_estado ENUM('activa','cancelada','completada') DEFAULT 'activa',
    reserva_observaciones TEXT DEFAULT NULL,
    reserva_fecha_creacion TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    reserva_fecha_actualizacion TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (reserva_id),
    KEY idx_zona_id (zona_id),
    KEY idx_usuario_id (usuario_id),
    KEY idx_reserva_fecha (reserva_fecha),
    KEY idx_reserva_estado (reserva_estado),
    KEY idx_reserva_fecha_estado (reserva_fecha, reserva_estado),
    KEY idx_zona_fecha_hora (zona_id, reserva_fecha, reserva_hora_inicio),
    CONSTRAINT fk_reserva_zona FOREIGN KEY (zona_id) REFERENCES tbl_zonas(zona_id) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT fk_reserva_usuario FOREIGN KEY (usuario_id) REFERENCES tbl_usuario(usuario_cc) ON DELETE CASCADE ON UPDATE CASCADE
);

CREATE TABLE IF NOT EXISTS tbl_paquetes (paqu_Id INT AUTO_INCREMENT,
    paqu_TipoDoc enum ('Cedula de cidadania', 'Cedula de extranjeria', 'Pasaporte', 'Permiso especial de permanencia (PEP)') NOT NULL,
    paqu_usuario_cedula INTEGER (10) NOT null, 
    paqu_Destinatario VARCHAR (60) NOT null,
    paqu_Asunto text NOT null,
    paqu_FechaLlegada DATE NOT null,
    paqu_Hora time NOT null,
    paqu_image VARCHAR (255) NOT null,
    paqu_Descripcion text NOT null, 
    paqu_estado enum('Entregado','Pendiente') NOT NULL,
    PRIMARY KEY (Paqu_Id),
    FOREIGN KEY (paqu_usuario_cedula) REFERENCES tbl_usuario (usu_cedula)); 

CREATE TABLE IF NOT EXISTS tbl_muro (
    muro_Id INT AUTO_INCREMENT PRIMARY KEY,
    muro_Destinatario ENUM('Administrador', 'Residente', 'Propietario', 'Vigilante', 'Usuario', 'Todos'),
    muro_Asunto TEXT NOT NULL,
    muro_Fecha DATE NOT NULL,
    muro_Hora TIME NOT NULL,
    muro_EnviaHora TIME NOT NULL,
    muro_image VARCHAR(255) DEFAULT NULL,
    muro_Descripcion TEXT NOT NULL, 
    muro_usuario_cedula INT,
    FOREIGN KEY (muro_usuario_cedula) REFERENCES tbl_usuario (usu_cedula)); 


CREATE TABLE IF NOT EXISTS tbl_Visitante (
    visi_id INT(10) AUTO_INCREMENT,
    visi_nombre VARCHAR(100) NOT NULL,
    visi_documento VARCHAR(20) NOT NULL,
    visi_tipo_documento ENUM('C.C', 'T.I', 'C.E') NOT NULL,
    visi_telefono BIGINT(20) NOT NULL,
    visi_email VARCHAR(100) NOT NULL,
    visi_usuario_cedula INT(10),  -- Relación con el residente
    PRIMARY KEY (visi_id),
    FOREIGN KEY (visi_usuario_cedula) REFERENCES tbl_usuario (usu_cedula)
);


CREATE TABLE IF NOT EXISTS tbl_visita (
    vis_id INT AUTO_INCREMENT,
    vis_hora_entrada TIME,
    vis_hora_salida TIME,
    vis_fecha_entrada DATE,
    vis_fecha_salida DATE,
    vis_visi_id INT(10), -- Relación con el visitante
    PRIMARY KEY (vis_id),
    FOREIGN KEY (vis_visi_id) REFERENCES tbl_Visitante (visi_id)
);

CREATE TABLE IF NOT EXISTS tbl_alquiler (alqu_id INT AUTO_INCREMENT,
    alqu_num_recibo BIGINT,
    alqu_tipo_doc_vehi VARCHAR (255) NOT NULL,
    alqu_num_doc_vehi VARCHAR (20) NOT NULL,
    alqu_nombre_propietario VARCHAR (255) NOT NULL,
	alqu_torre Varchar(100),
    alqu_apartamento  Varchar(100),
    alqu_placa VARCHAR (20) NOT NULL,
    alqu_numeroParqueadero INT (100),
    alqu_estadoSalida TEXT NOT NULL,
    alqu_fecha_entrada DATE,
    alqu_fecha_salida DATE,
    alqu_hora_salida TIME,
    alqu_precio FLOAT (2),
    alqu_visita_id INT,
    -- alqu_usuario_cedula INT (10),
    UNIQUE (alqu_num_recibo),
    PRIMARY KEY (alqu_id),
    -- FOREIGN KEY (alqu_usuario_cedula) REFERENCES tbl_usuario (usu_cedula)
    FOREIGN KEY (alqu_visita_id) REFERENCES tbl_visita (vis_id)
    );
    
                                                                                                                            
CREATE TABLE IF NOT EXISTS tbl_parqueadero(parq_id INT (20) NOT NULL AUTO_INCREMENT,
    parq_vehi_placa VARCHAR (10) NOT NULL,
    parq_nombre_propietario_vehi VARCHAR (255) NOT NULL,
    parq_tipo_doc_vehi VARCHAR (255) NOT NULL,
    parq_num_doc_vehi VARCHAR (20) NOT NULL,
    parq_vehi_estadoIngreso TEXT NOT NULL, 
    parq_vehi_alqu_id INT (30),
    parq_numeroParqueadero INT (100),
    parq_fecha_entrada DATE,
    parq_fecha_salida DATE,
    parq_hora_entrada TIME,
    PRIMARY KEY (parq_id),
    FOREIGN KEY (parq_vehi_alqu_id) REFERENCES tbl_alquiler (alqu_id));


CREATE TABLE IF NOT EXISTS tbl_consultaParqueadero (consulParq_id INT (50) NOT NULL AUTO_INCREMENT,
    consulParq_tipoVehiculo ENUM('Carro', 'Moto') NOT NULL,
    consulParq_placa VARCHAR (10) NOT NULL,
    consulParq_observaciones TEXT (30000) NOT NULL, 
    -- consulParq_usuario_cedula INT (10),
    consulParq_numeroParqueadero INT NOT NULL,
    consulParq_estado ENUM('disponible', 'ocupado', 'mantenimiento') DEFAULT 'disponible',
    consulParq_parq_id INT (20) NOT NULL,
    UNIQUE (consulParq_numeroParqueadero),
    PRIMARY KEY (consulParq_id),
    -- FOREIGN KEY (consulParq_usuario_cedula) REFERENCES tbl_usuario (usu_cedula)
    FOREIGN KEY (consulParq_parq_id) REFERENCES tbl_parqueadero (parq_id)
);


CREATE TABLE IF NOT EXISTS tbl_Designacion (desg_id INT (20) NOT NULL,
    desg_parq_id INT (20) NOT NULL,
    desg_alqu_id INT (20) NOT NULL,
    PRIMARY KEY (desg_id),
    FOREIGN KEY (desg_parq_id) REFERENCES tbl_parqueadero (parq_id),
    FOREIGN KEY (desg_alqu_id) REFERENCES tbl_alquiler (alqu_id));

    

CREATE TABLE IF NOT EXISTS tbl_pqrs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_cc INT NULL,   -- FK hacia tbl_usuario
    nombres VARCHAR(100) NOT NULL,
    apellidos VARCHAR(100) NOT NULL,
    identificacion VARCHAR(20) NOT NULL,
    email VARCHAR(100) NOT NULL,
    telefono VARCHAR(15) NOT NULL,
    tipo_pqr ENUM('peticion','queja','reclamo','sugerencia') NOT NULL,
    asunto VARCHAR(255) NOT NULL,
    mensaje TEXT NOT NULL,
    archivos TEXT,
    respuesta TEXT NULL,
    fecha_respuesta DATETIME NULL,
    respondido_por INT NULL,
    notificacion_enviada ENUM('no','correo') DEFAULT 'no',
    medio_respuesta ENUM('correo') NOT NULL DEFAULT 'correo',
    fecha_creacion DATETIME DEFAULT CURRENT_TIMESTAMP,
    estado ENUM('pendiente','en_proceso','resuelto') DEFAULT 'pendiente',
    adjuntos_respuesta TEXT NULL COMMENT 'JSON con información de archivos adjuntos de la respuesta del administrador',

    FOREIGN KEY (usuario_cc) REFERENCES tbl_usuario(usuario_cc)
    ON DELETE SET NULL ON UPDATE CASCADE,

    INDEX idx_estado (estado),
    INDEX idx_fecha_creacion (fecha_creacion),
    INDEX idx_usuario_cc (usuario_cc)
);


/* TABLA LOG NOTIFICACIONES */
CREATE TABLE IF NOT EXISTS tbl_log_notificaciones (
    id INT AUTO_INCREMENT PRIMARY KEY,
    pqrs_id INT NULL,
    tipo_notificacion ENUM('correo') NOT NULL,
    destinatario VARCHAR(100) NOT NULL,
    asunto VARCHAR(255) NULL,
    mensaje TEXT NOT NULL,
    estado ENUM('enviado','error','pendiente') DEFAULT 'pendiente',
    error TEXT NULL,
    fecha_envio DATETIME DEFAULT CURRENT_TIMESTAMP,
    
    INDEX idx_pqrs_id (pqrs_id),
    INDEX idx_tipo_fecha (tipo_notificacion, fecha_envio),
    INDEX idx_estado (estado),
    
    FOREIGN KEY (pqrs_id) REFERENCES tbl_pqrs(id) 
        ON DELETE SET NULL ON UPDATE CASCADE
);


-- ON DELETE SET NULL: Si se elimina un usuario en 'tbl_usuario', el campo 'usuario_cc' en 'pqrs' quedará en NULL,
--                     evitando la pérdida del historial de PQRs pero indicando que ya no está asociado a un usuario activo.

-- ON UPDATE CASCADE: Si el valor de 'usuario_cc' en 'tbl_usuario' se actualiza, el cambio se propaga automáticamente
--                    a todos los registros relacionados en la tabla 'pqrs', manteniendo la coherencia de los datos.