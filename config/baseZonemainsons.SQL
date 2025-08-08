CREATE DATABASE zonemaisons; 
USE zonemaisons;

/* Tablas */   

CREATE TABLE if NOT EXISTS tbl_mora (mor_id INT(10) AUTO_INCREMENT,
    mor_estado ENUM('Pendiente', 'Pagado'),
    PRIMARY KEY (mor_id));  

INSERT INTO tbl_mora (mor_estado)
VALUES ('Pagado'),
('Pendiente'); 

/* MODIFICACIÓN*/
CREATE TABLE if NOT EXISTS tbl_usuario (usuario_cc INT (10) AUTO_INCREMENT,
    usu_cedula INT (255) NOT null, 
    usu_tipo_documento ENUM ('Cedula de cidadania', 'Cedula de extrangeria', 'Pasaporte', 'Permiso especial de permanencia (PEP)'),
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
    paqu_TipoDoc enum ('Cedula de cidadania', 'Cedula de extrangeria', 'Pasaporte', 'Permiso especial de permanencia (PEP)') NOT NULL,
    paqu_usuario_cedula INTEGER (10) NOT null, 
    paqu_Destinatario VARCHAR (60) NOT null,
    paqu_Asunto text NOT null,
    paqu_FechaLlegada DATE NOT null,
    paqu_Hora time NOT null,
    paqu_image NULL (255) NOT null,
    paqu_Descripcion text NOT null, 
    paqu_estado enum('Entregado','Pendiente') NOT NULL,
    PRIMARY KEY (Paqu_Id),
    FOREIGN KEY (paqu_usuario_cedula) REFERENCES tbl_usuario (usu_cedula)); 

CREATE TABLE IF NOT EXISTS tbl_muro (muro_Id INT AUTO_INCREMENT,
    muro_Destinatario VARCHAR (60) NOT null,
    muro_Asunto text NOT null,
    muro_Fecha DATE NOT null,
    muro_Hora time NOT null,
    muro_image VARCHAR (255) NOT null,
    muro_Descripcion text NOT null, 
    muro_usuario_cedula INT,
    PRIMARY KEY (muro_id),
    FOREIGN KEY (muro_usuario_cedula) REFERENCES tbl_usuario (usu_cedula)); 

CREATE TABLE tbl_visita ( vis_id INT AUTO_INCREMENT,
    vis_hora_entrada TIME,
    vis_hora_salida TIME,
    vis_fecha_entrada DATE,
    vis_fecha_salida DATE,
    PRIMARY KEY (vis_id));



CREATE TABLE IF NOT EXISTS tbl_Visitante (
    visi_id INT(10) AUTO_INCREMENT,
    visi_nombre VARCHAR(100) NOT NULL,
    visi_documento VARCHAR(20) NOT NULL,
    visi_Tipo_documento ENUM('C.C', 'T.I', 'C.E') NOT NULL,
    visi_telefono BIGINT(20) NOT NULL,
    visi_email VARCHAR(100) NOT NULL,
    visi_usuario_cedula INT(10),
    visi_vis_id INT,
    PRIMARY KEY (visi_id),
    FOREIGN KEY (visi_usuario_cedula) REFERENCES tbl_usuario (usu_cedula),
    FOREIGN KEY (visi_vis_id) REFERENCES tbl_visita (vis_id)
);

CREATE TABLE IF NOT EXISTS tbl_alquiler (alqu_id INT(20) NOT NULL AUTO_INCREMENT,
    alqu_observaciones VARCHAR (30000) NOT NULL,
    alqu_precio FLOAT (2) NOT NULL,
    alqu_vis_id INT (10) NOT NULL,
    alqu_num_recibo INT (255) NOT NULL,
    alqu_placa VARCHAR (20) NOT NULL,
    alqu_usuario_cedula INT (10) NOT NULL,
    UNIQUE (alqu_num_recibo),
    PRIMARY KEY (alqu_id),
    FOREIGN KEY (alqu_usuario_cedula) REFERENCES tbl_usuario (usu_cedula),
    FOREIGN KEY (alqu_vis_id) REFERENCES tbl_visita (vis_id));
                                                                                                                                
CREATE TABLE IF NOT EXISTS tbl_parqueadero(parq_id INT (20) NOT NULL AUTO_INCREMENT,
    parq_vehi_placa VARCHAR (6) NOT NULL,
    parq_nombre_propietario_vehi VARCHAR (255) NOT NULL,
    parq_tipo_doc_vehi VARCHAR (255) NOT NULL,
    parq_num_doc_vehi INT (20) NOT NULL,
    parq_vehi_estadiIngreso VARCHAR (90) NOT NULL,
    parq_vehi_alqu_id INT (30) NOT NULL,
    parq_usuario_cedula INT (10) NOT NULL,
    parq_visita_id INT (30) NOT NULL, 
    PRIMARY KEY (parq_id),
    FOREIGN KEY (parq_usuario_cedula) REFERENCES tbl_usuario (usu_cedula),
    FOREIGN KEY (parq_visita_id) REFERENCES tbl_visita (vis_id),
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




/* Inserts */

INSERT INTO tbl_mora (mor_nombre_usuarios, mor_estado)
VALUES 
    ('Luis Rodriguez', 'Pendiente'),
    ('Carlos Perez', 'Pagado'),
    ('Maria Lopez', 'Pendiente');



INSERT INTO tbl_tiempo_mora (tiem_id, tiem_usuario_cc,tiem_mor_id)
VALUES (NULL, 2, 1),
(NULL, 5, 2),
(NULL, 3, 3);


insert into tbl_paquetes (Paqu_Id,Paqu_Destinatario,Paqu_Firma,paqu_FechaLlegada,paqu_usuario_cc) 
values ("11100","Juan Perez","[firma]","2024-10-02",6),
("22200","Luis Rodriguez","[firma]","2024-10-07",7), 
("33300","Maria Lopez","[firma]",'2024-10-08',8),
("44400","Luis Rodriguez","[firma]","2024-10-09",7),
("55500","Carlos Perez","[firma]","2024-10-15",10);




insert into tbl_notificaciones 
(noti_id,Noti_Fecha,Noti_Descripción,Noti_Contenido,Noti_Paqu_id,Noti_usuario_cc)
values (12,"2024-09-03","!Importante¡","Próximamente se iniciará el mantenimiento de las aéreas verdes del conjunto","11100",6),
(13,"2024-09-10","Paquete Recibido","Ha llegado un paquete para usted en la recepción.","22200",7),
(14,"2024-09-10","Mantenimiento Programado","El mantenimiento del ascensor se realizará el 10 de septiembre. El servicio estará interrumpido de 8:00 a 12:00.","33300",8),
(15,"2024-09-19","Evento:","La junta comenzará en 30 minutos en el salón comunal.","44400",8),
(16,"2024-09-25","Alerta de Seguridad:","Se ha reportado un corte de energía en el bloque C.","55500",10);




insert into tbl_novedades 
(Nove_Id,Nove_Fecha,Nove_ImageNovedades,Nove_Novedades,Nove_noti_id) 
values (21,"2024-09-03","[imagen]","Estimados residentes, Les informamos que el próximo lunes 6 de septiembre se llevará a cabo el mantenimiento de las áreas verdes del conjunto. Durante ese día, las áreas comunes estarán cerradas desde las 8:00 a.m. hasta las 4:00 p.m. Les pedimos amablemente que eviten el uso de estas áreas durante ese período para garantizar un trabajo eficiente.

Para cualquier pregunta o inconveniente, por favor, contacten a la administración

Agradecemos su comprensión y cooperación.

Atentamente,
[Nombre del Responsable]
Administración del Conjunto Residencial [Nombre conjunto residencial]",12),

(31,"2024-09-01","[imagen]","Recordatorio de Pago de Cuotas
Estimados Residentes, Este es un recordatorio de que la fecha límite para el pago de las cuotas de mantenimiento del mes de octubre es el próximo lunes 15 de septiembre. Les solicitamos que realicen el pago a tiempo para evitar recargos por demora y garantizar la continuidad en los servicios del conjunto.

Para realizar el pago, pueden hacerlo en la oficina administrativa durante el horario de atención.

Agradecemos su atención a este asunto.

Atentamente,
[Nombre del Responsable] 
Administrador del Conjunto Residencial [Nombre conjunto residencial]",13),

(41,"2024-09-15","[imagen]","Reunión
Estimados Residentes, Se convoca a todos los residentes a la próxima reunión de la Junta de Propietarios que se celebrará el jueves 19 de septiembre a las 7:00 p.m. en el salón de comunal del conjunto.

En esta reunión se discutirán temas importantes, incluyendo el presupuesto para el próximo año y proyectos de mejora para las instalaciones comunes. Su participación es fundamental para asegurar una gestión efectiva del conjunto.

Esperamos contar con su presencia.

Atentamente,
[Nombre del Responsable]
Administrador del Conjunto Residencial [Nombre conjunto residencial]",14),

(51,"2024-09-23","[imagen]","Cambio en el Horario de Recolección de Basura
Estimados Residentes,Les informamos que a partir del 1 de octubre, el horario de recolección de basura en nuestro conjunto cambiará. El nuevo horario será los lunes, miércoles y viernes entre las 7:00 a.m. y las 9:00 a.m.

Este ajuste tiene como objetivo mejorar la eficiencia del servicio y garantizar que la recolección se realice de manera oportuna. Les pedimos que coloquen sus desechos en los puntos designados antes de las 7:00 a.m. para asegurar que sean recogidos a tiempo.

Para cualquier pregunta, pueden contactar a la administración

Atentamente,
[Nombre del Responsable]
Administrador del Conjunto Residencial [Nombre conjunto residencial]",15),

(61,"2024-09-20","[imagen]","Reapertura de Área Recreativa
Estimados Residentes, Nos complace informarles que el área recreativa del conjunto, que estuvo cerrada por renovación, reabrirá el próximo sábado 23 de septiembre. Las nuevas instalaciones incluyen una zona de juegos actualizada.

La inauguración oficial será a las 10:00 a.m., y todos están invitados a asistir y disfrutar de las nuevas mejoras.

Para cualquier duda o consulta, pueden contactar a la administración

Esperamos verlos en la inauguración.

Atentamente,
[Nombre del Responsable]
Administrador del Conjunto Residencial [Nombre conjunto residencial]",16);


INSERT INTO tbl_visita (vis_id,vis_hora_inicio, vis_hora_salida, vis_fecha_entrada, vis_fecha_salida, vis_usuario_id)
VALUES (null,'08:00:00', '09:00:00', '2024-09-01', '2024-09-01', 6),
       (null,'10:00:00', '11:00:00', '2024-09-02', '2024-09-02', 7),
       (null,'14:00:00', '15:00:00', '2024-09-03', '2024-09-03', 8),
       (null,'09:00:00', '10:00:00', '2024-09-04', '2024-09-04', 10),
       (null,'13:00:00', '14:00:00', '2024-09-05', '2024-09-05', 6);



INSERT INTO Tbl_Visitante (visi_id,visi_nombre, visi_documento, visi_reporte, visi_telefono, visi_vis_id)
VALUES (null,'Carlos Pérez', '1234567890', 'Ninguno',3001234567,1),
       (null,'Ana Martinez', '2345678901', 'Informe de visita',3002345678,2),
       (null,'Luis Rodríguez', '3456789012', 'Visita de inspección',3003456789,3),
       (null,'María López', '4567890123', 'Visita programada',3004567890,4),
       (null,'Pedro Gomez', '5678901234', 'Visita de cortesía',3005678901,5);


INSERT INTO tbl_parqueadero (parq_id, parq_estado, parq_vehi_placa, parq_vehi_modelo, parq_vehi_color, parq_vehi_estadiIngreso, parq_vehi_alqu_id, parq_usuario_cc)
VALUES  (NULL, "Buenas condiciones", "AAA-123", "BMW X3 xDriv (2018)", "Rojo", "Ingreso en buenas condiciones", 1, 1),
	(NULL, "Medianas condiciones", "DOL-159", "Infiniti QX50 (2019)", "Gris", "Ingreso con abolladura en la puerta derecha", 2, 2),
	(NULL, "Malas condiciones", "BMX-672", "Volkswagen Taos (2015)", "Azul", "Ingreso sin bobilla led derecha", 3, 3),
	(NULL, "Buenas condiciones", "ZKS-904", "Nissan Sentra (2012)", "Negro", "Ingreso en buenas condiciones", 4, 4);

INSERT INTO tbl_alquiler (alqu_id, alqu_horaSalida, alqu_fechaSalida, alqu_observaciones, alqu_impuesto, alqu_precio, alqu_fechaIngreso, alqu_horaIngreso, alqu_mult_id, alqu_mult_valor, alqu_mult_fecha, alqu_mult_vigencia, alqu_mult_dercripcion, alqu_mult_alqu_id,alqu_vis_id )
VALUES (NULL, '16:30:00', '2024-15-03', "No supero limite de tiempo", 20.00, 50.00, '2024-15-03', '16:00:00', 000, 00.00,  '2024-15-03', '2024-15-03', "No aplica, cumplio con las horas establecidas", 024, 1),
	(NULL, '18:00:00', '2024-31-09', "Supero limite de tiempo", 10.00, 40.00, '2024-31-09', '07:00:00', 023, 20.00, '2024-31-09', '2024-31-09', "Si aplica, no cumplio con las horas establecidas", 000, 2),
	(NULL, '15:30:00', '2024-22-05', "No supero limite de tiempo", 15.00, 45.00, '2024-22-05', '05:00:00', 000, 00.00,  '2024-22-05', '2024-22-05', "No aplica, cumplio con las horas establecidas", 000, 3),
	(NULL, '12:00:00', '2024-03-12', "Supero limite de tiempo", 25.00, 60.00, '2024-03-12', '11:00:00', 009, 10.00, '2024-03-12', '2024-03-12', "Si aplica, no cumplio con las horas establecidas", 009, 4),
	(NULL, '13:00:00', '2024-12-11', "No supero limite de tiempo", 30.00, 80.00, '2024-12-11', '10:00:00', 000, 00.00, '2024-12-11', '2024-12-11', "No aplica, cumplio con las horas establecidas", 000, 5); 

INSERT INTO tbl_designacion (desg_id, desg_parq_id, desg_alqu_id) 	VALUES (001, 1, 6),
							(002, 2, 7),
							(003, 3, 8),
							(004, 4, 9);


INSERT INTO tbl_PQRS (pqr_id, pqr_descripcion, pqr_fecha_creacion, pqr_fecha_respuesta, pqr_estado, pqr_est_id, pqr_regis_id)
VALUES (null, 'Problema con la puerta de entrada', '2024-09-01', '2024-09-05', 'reclamo', 1,1),
(null, 'Solicitud de mejora de jardines', '2024-09-02', '2024-09-06', 'peticion', 2,2),
(null, 'Queja por ruido excesivo', '2024-09-03', '2024-09-07', 'queja', 3,3),
(null, 'Revisión de luces en áreas comunes', '2024-09-04', '2024-09-08', 'sugerencia', 4,4),
(null, 'Reclamo por retraso en mantenimiento', '2024-09-05', '2024-09-09', 'reclamo', 5,5);


INSERT INTO tbl_registrar (regis_id,regis_usuario_id, regis_pqr_id)
VALUES (null,7,1),
       (null,8,2),
       (null,6,3),
       (null,10,4),
       (null,10,5);



INSERT INTO tbl_Archivos (arch_id, arch_nombre, arch_tipo, arch_ruta, arch_pqr_id)
VALUES (1, 'PuertaEntrada.jpg', 'imagen', '/archivos/PuertaEntrada.jpg',1),
(2, 'MejoraJardines.docx', 'documento', '/archivos/MejoraJardines.docx',2),
(3, 'RuidoExcesivo.mp3', 'video', '/archivos/RuidoExcesivo.mp3',3),
(4, 'RevisiónLuces.pdf', 'documento', '/archivos/RevisiónLuces.pdf',4),
(5, 'RetrasoMantenimiento.pdf', 'documento', '/archivos/RetrasoMantenimiento.pdf',5);


INSERT INTO zonas_comunes (id, nombre, descripcion, capacidad, estado, imagen, hora_apertura, hora_cierre, duracion_maxima) 
VALUES
(1, 'Gimnasio', 'Zona equipada con máquinas de ejercicios.', 20, 'activo', 'gimnasio.jpg', '08:00:00', '22:00:00', 2),
(2, 'Salón Comunal', 'Espacio para eventos y reuniones.', 50, 'activo', 'salon-comunal.jpg', '08:00:00', '20:00:00', 3),
(3, 'Cancha de Vóley', 'Cancha de vóley para uso recreativo y deportivo.', 10, 'activo', 'voley.jpg', '08:00:00', '20:00:00', 2);


INSERT INTO reservas (zona_id, apartamento, nombre_residente, fecha_reserva, hora_inicio, hora_fin, estado, observaciones)
VALUES
(1, 'Apt 101', 'Carlos Pérez', '2025-08-05', '09:00:00', '11:00:00', 'activa', 'Reserva para entrenamiento matutino en el gimnasio.'),
(1, 'Apt 102', 'María López', '2025-08-06', '18:00:00', '20:00:00', 'activa', 'Reserva para sesión de pesas.'),
(2, 'Apt 201', 'Juan Gómez', '2025-08-07', '10:00:00', '13:00:00', 'activa', 'Reunión familiar, con acceso a proyector.'),
(2, 'Apt 202', 'Sofía Martínez', '2025-08-08', '14:00:00', '17:00:00', 'activa', 'Evento social en salón comunal.'),
(3, 'Apt 301', 'Pedro Sánchez', '2025-08-09', '16:00:00', '18:00:00', 'activa', 'Partido de vóley con amigos.'),
(3, 'Apt 302', 'Laura Rodríguez', '2025-08-10', '08:00:00', '10:00:00', 'activa', 'Práctica recreativa en la cancha de vóley.');