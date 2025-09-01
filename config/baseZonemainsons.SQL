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

CREATE TABLE IF NOT EXISTS zonas_comunes (
    id INT(11) NOT NULL AUTO_INCREMENT,
    nombre VARCHAR(100) NOT NULL,
    descripcion TEXT,
    capacidad INT(11) DEFAULT NULL,
    estado ENUM('activo','inactivo','mantenimiento') DEFAULT 'activo',
    imagen VARCHAR(255) DEFAULT NULL,
    hora_apertura TIME DEFAULT '08:00:00',
    hora_cierre TIME DEFAULT '20:00:00',
    duracion_maxima INT(11) DEFAULT 2,
    terminos_y_condiciones TEXT DEFAULT NULL,
    PRIMARY KEY (id)
);


CREATE TABLE IF NOT EXISTS reservas (
    id INT(11) NOT NULL AUTO_INCREMENT,
    zona_id INT(11) NOT NULL,
    apartamento VARCHAR(50) NOT NULL,
    nombre_residente VARCHAR(100) NOT NULL,
    fecha_reserva DATE NOT NULL,
    hora_inicio TIME NOT NULL,
    hora_fin TIME NOT NULL,
    estado ENUM('activa','cancelada','completada') DEFAULT 'activa',
    observaciones TEXT,
    fecha_creacion TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    KEY idx_zona_id (zona_id),
    CONSTRAINT fk_zona_reserva FOREIGN KEY (zona_id) REFERENCES zonas_comunes(id) ON DELETE CASCADE ON UPDATE CASCADE
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
    alqu_num_recibo BIGINT NOT NULL,
    alqu_tipo_doc_vehi VARCHAR (255) NOT NULL,
    alqu_num_doc_vehi VARCHAR (20) NOT NULL,
    alqu_nombre_propietario VARCHAR (255) NOT NULL,
	alqu_torre Varchar(100) NOT null,
    alqu_apartamento  Varchar(100) NOT null,
    alqu_placa VARCHAR (20) NOT NULL,
    alqu_numeroParqueadero INT (100),
    alqu_estadoSalida TEXT NOT NULL,
    alqu_fecha_entrada DATE,
    alqu_fecha_salida DATE,
    alqu_hora_salida TIME,
    alqu_precio FLOAT (2) NOT NULL,
    -- alqu_usuario_cedula INT (10),
    UNIQUE (alqu_num_recibo),
    PRIMARY KEY (alqu_id)
    -- FOREIGN KEY (alqu_usuario_cedula) REFERENCES tbl_usuario (usu_cedula)
    );
    

CREATE TABLE IF NOT EXISTS tbl_consultaParqueadero (consulParq_id INT (50) NOT NULL AUTO_INCREMENT,
    consulParq_tipoVehiculo ENUM('Carro', 'Moto') NOT NULL,
    consulParq_placa VARCHAR (10) NOT NULL,
    consulParq_observaciones TEXT (30000) NOT NULL, 
    -- consulParq_usuario_cedula INT (10),
    consulParq_numeroParqueadero INT NOT NULL,
    consulParq_estado ENUM('disponible', 'ocupado', 'mantenimiento') DEFAULT 'disponible',
    UNIQUE (consulParq_numeroParqueadero),
    PRIMARY KEY (consulParq_id)
    -- FOREIGN KEY (consulParq_usuario_cedula) REFERENCES tbl_usuario (usu_cedula)
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



CREATE TABLE IF NOT EXISTS tbl_Designacion (desg_id INT (20) NOT NULL,
    desg_parq_id INT (20) NOT NULL,
    desg_alqu_id INT (20) NOT NULL,
    PRIMARY KEY (desg_id),
    FOREIGN KEY (desg_parq_id) REFERENCES tbl_parqueadero (parq_id),
    FOREIGN KEY (desg_alqu_id) REFERENCES tbl_alquiler (alqu_id));

    

CREATE TABLE IF NOT EXISTS tbl_pqrs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_cc INT NULL,
    nombres VARCHAR(100) NOT NULL,
    apellidos VARCHAR(100) NOT NULL,
    identificacion VARCHAR(20) NOT NULL,
    email VARCHAR(100) NOT NULL,
    telefono VARCHAR(15) NOT NULL,
    tipo_pqr ENUM('peticion', 'queja', 'reclamo', 'sugerencia') NOT NULL,
    asunto VARCHAR(255) NOT NULL,
    mensaje TEXT NOT NULL,
    archivos TEXT, -- puedes guardar los nombres o rutas de archivos
    medio_respuesta SET('correo', 'sms'),
    fecha_creacion DATETIME DEFAULT CURRENT_TIMESTAMP,
    estado ENUM('pendiente', 'en_proceso', 'resuelto') DEFAULT 'pendiente',
    CONSTRAINT fk_usuario_pqr FOREIGN KEY (usuario_cc) REFERENCES tbl_usuario(usuario_cc)
    ON DELETE SET NULL ON UPDATE CASCADE
);


-- ON DELETE SET NULL: Si se elimina un usuario en 'tbl_usuario', el campo 'usuario_cc' en 'pqrs' quedará en NULL,
--                     evitando la pérdida del historial de PQRs pero indicando que ya no está asociado a un usuario activo.

-- ON UPDATE CASCADE: Si el valor de 'usuario_cc' en 'tbl_usuario' se actualiza, el cambio se propaga automáticamente
--                    a todos los registros relacionados en la tabla 'pqrs', manteniendo la coherencia de los datos.