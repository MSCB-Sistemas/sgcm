-- cementerio.auditoria definition

CREATE TABLE `auditoria` (
  `id_auditoria` int NOT NULL AUTO_INCREMENT,
  `id_usuario` int NOT NULL,
  `model` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_spanish2_ci DEFAULT NULL,
  `accion` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_spanish2_ci DEFAULT NULL,
  `creado_en` datetime DEFAULT CURRENT_TIMESTAMP,
  `query_sql` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_spanish2_ci NOT NULL,
  `parametros` json DEFAULT NULL,
  PRIMARY KEY (`id_auditoria`),
  KEY `fk_asistencia_usuario` (`id_usuario`),
  CONSTRAINT `fk_asistencia_usuario` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id_usuario`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=49 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish2_ci;

-- cementerio.deudo definition

CREATE TABLE `deudo` (
  `id_deudo` int NOT NULL AUTO_INCREMENT,
  `dni` int DEFAULT NULL,
  `nombre` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_spanish2_ci DEFAULT NULL,
  `apellido` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_spanish2_ci DEFAULT NULL,
  `telefono` int DEFAULT NULL,
  `email` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_spanish2_ci DEFAULT NULL,
  `domicilio` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_spanish2_ci DEFAULT NULL,
  `localidad` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_spanish2_ci DEFAULT NULL,
  `codigo_postal` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_spanish2_ci DEFAULT NULL,
  PRIMARY KEY (`id_deudo`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish2_ci;

-- cementerio.difunto definition

CREATE TABLE `difunto` (
  `id_difunto` int NOT NULL AUTO_INCREMENT,
  `id_deudo` int NOT NULL,
  `nombre` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_spanish2_ci DEFAULT NULL,
  `apellido` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_spanish2_ci DEFAULT NULL,
  `dni` int DEFAULT NULL,
  `edad` int DEFAULT NULL,
  `fecha_fallecimiento` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `id_sexo` int DEFAULT NULL,
  `id_nacionalidad` int DEFAULT NULL,
  `id_estado_civil` int DEFAULT NULL,
  `domicilio` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_spanish2_ci DEFAULT NULL,
  `localidad` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_spanish2_ci DEFAULT NULL,
  `codigo_postal` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_spanish2_ci DEFAULT NULL,
  PRIMARY KEY (`id_difunto`),
  KEY `difunto_FK` (`id_deudo`),
  KEY `difunto_FK_1` (`id_sexo`),
  KEY `difunto_FK_2` (`id_estado_civil`),
  KEY `difunto_FK_3` (`id_nacionalidad`),
  CONSTRAINT `difunto_FK` FOREIGN KEY (`id_deudo`) REFERENCES `deudo` (`id_deudo`),
  CONSTRAINT `difunto_FK_1` FOREIGN KEY (`id_sexo`) REFERENCES `sexo` (`id_sexo`),
  CONSTRAINT `difunto_FK_2` FOREIGN KEY (`id_estado_civil`) REFERENCES `estado_civil` (`id_estado_civil`),
  CONSTRAINT `difunto_FK_3` FOREIGN KEY (`id_nacionalidad`) REFERENCES `nacionalidades` (`id_nacionalidad`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish2_ci;

-- cementerio.estado_civil definition

CREATE TABLE `estado_civil` (
  `id_estado_civil` int NOT NULL AUTO_INCREMENT,
  `descripcion` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_spanish2_ci NOT NULL,
  PRIMARY KEY (`id_estado_civil`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish2_ci;

-- cementerio.nacionalidades definition

CREATE TABLE `nacionalidades` (
  `id_nacionalidad` int NOT NULL AUTO_INCREMENT,
  `nacionalidad` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_spanish2_ci NOT NULL,
  PRIMARY KEY (`id_nacionalidad`)
) ENGINE=InnoDB AUTO_INCREMENT=184 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish2_ci;

-- cementerio.orientacion definition

CREATE TABLE `orientacion` (
  `id_orientacion` int NOT NULL AUTO_INCREMENT,
  `descripcion` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_spanish2_ci NOT NULL,
  PRIMARY KEY (`id_orientacion`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish2_ci;

-- cementerio.pago definition

CREATE TABLE `pago` (
  `id_pago` int NOT NULL AUTO_INCREMENT,
  `id_deudo` int NOT NULL,
  `id_parcela` int NOT NULL,
  `fecha_pago` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `importe` int DEFAULT NULL,
  `recargo` int DEFAULT NULL,
  `total` int DEFAULT NULL,
  `id_usuario` int NOT NULL,
  `fecha_vencimiento` datetime NOT NULL,
  PRIMARY KEY (`id_pago`),
  KEY `pago_FK` (`id_deudo`),
  KEY `pago_FK_1` (`id_parcela`),
  KEY `pago_FK_2` (`id_usuario`),
  CONSTRAINT `pago_FK` FOREIGN KEY (`id_deudo`) REFERENCES `deudo` (`id_deudo`),
  CONSTRAINT `pago_FK_1` FOREIGN KEY (`id_parcela`) REFERENCES `parcela` (`id_parcela`),
  CONSTRAINT `pago_FK_2` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id_usuario`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish2_ci;

-- cementerio.parcela definition

CREATE TABLE `parcela` (
  `id_parcela` int NOT NULL AUTO_INCREMENT,
  `id_tipo_parcela` int NOT NULL,
  `id_deudo` int DEFAULT NULL,
  `numero_ubicacion` int DEFAULT NULL,
  `hilera` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_spanish2_ci DEFAULT NULL,
  `seccion` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_spanish2_ci DEFAULT NULL,
  `fraccion` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_spanish2_ci DEFAULT NULL,
  `nivel` int DEFAULT NULL,
  `id_orientacion` int DEFAULT NULL,
  PRIMARY KEY (`id_parcela`),
  KEY `parcela_FK` (`id_tipo_parcela`),
  KEY `parcela_FK_1` (`id_deudo`),
  KEY `parcela_FK_2` (`id_orientacion`),
  CONSTRAINT `parcela_FK` FOREIGN KEY (`id_tipo_parcela`) REFERENCES `tipo_parcela` (`id_tipo_parcela`),
  CONSTRAINT `parcela_FK_1` FOREIGN KEY (`id_deudo`) REFERENCES `deudo` (`id_deudo`),
  CONSTRAINT `parcela_FK_2` FOREIGN KEY (`id_orientacion`) REFERENCES `orientacion` (`id_orientacion`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish2_ci;

-- cementerio.permisos definition

CREATE TABLE `permisos` (
  `id_permiso` int NOT NULL AUTO_INCREMENT,
  `nombre_permiso` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_spanish2_ci NOT NULL,
  PRIMARY KEY (`id_permiso`),
  UNIQUE KEY `nombre_permiso` (`nombre_permiso`)
) ENGINE=InnoDB AUTO_INCREMENT=50 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish2_ci;

-- cementerio.sexo definition

CREATE TABLE `sexo` (
  `id_sexo` int NOT NULL AUTO_INCREMENT,
  `descripcion` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_spanish2_ci NOT NULL,
  PRIMARY KEY (`id_sexo`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish2_ci;

-- cementerio.tipo_parcela definition

CREATE TABLE `tipo_parcela` (
  `id_tipo_parcela` int NOT NULL AUTO_INCREMENT,
  `nombre_parcela` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_spanish2_ci NOT NULL,
  PRIMARY KEY (`id_tipo_parcela`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish2_ci;

-- cementerio.tipos_usuarios definition

CREATE TABLE `tipos_usuarios` (
  `id_tipo_usuario` int NOT NULL AUTO_INCREMENT,
  `rol` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_spanish2_ci NOT NULL,
  PRIMARY KEY (`id_tipo_usuario`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish2_ci;

-- cementerio.tipos_usuarios_permisos definition

CREATE TABLE `tipos_usuarios_permisos` (
  `id_tipo_usuario` int DEFAULT NULL,
  `id_permiso` int DEFAULT NULL,
  KEY `rol_permiso_tipos_usuarios_FK` (`id_tipo_usuario`),
  KEY `rol_permiso_permisos_fk` (`id_permiso`),
  CONSTRAINT `rol_permiso_permisos_fk` FOREIGN KEY (`id_permiso`) REFERENCES `permisos` (`id_permiso`),
  CONSTRAINT `rol_permiso_tipos_usuarios_FK` FOREIGN KEY (`id_tipo_usuario`) REFERENCES `tipos_usuarios` (`id_tipo_usuario`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish2_ci;

-- cementerio.ubicacion_difunto definition

CREATE TABLE `ubicacion_difunto` (
  `id_ubicacion_difunto` int NOT NULL AUTO_INCREMENT,
  `id_parcela` int NOT NULL,
  `id_difunto` int NOT NULL,
  `fecha_ingreso` date DEFAULT NULL,
  `fecha_retiro` date DEFAULT NULL,
  PRIMARY KEY (`id_ubicacion_difunto`),
  KEY `ubicacion_difunto_FK` (`id_parcela`),
  KEY `ubicacion_difunto_FK_1` (`id_difunto`),
  CONSTRAINT `ubicacion_difunto_FK` FOREIGN KEY (`id_parcela`) REFERENCES `parcela` (`id_parcela`),
  CONSTRAINT `ubicacion_difunto_FK_1` FOREIGN KEY (`id_difunto`) REFERENCES `difunto` (`id_difunto`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish2_ci;

-- cementerio.usuarios definition

CREATE TABLE `usuarios` (
  `id_usuario` int NOT NULL AUTO_INCREMENT,
  `usuario` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_spanish2_ci NOT NULL,
  `contrasenia` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_spanish2_ci NOT NULL,
  `nombre` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_spanish2_ci NOT NULL,
  `apellido` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_spanish2_ci NOT NULL,
  `cargo` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_spanish2_ci DEFAULT NULL,
  `sector` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_spanish2_ci DEFAULT NULL,
  `telefono` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_spanish2_ci DEFAULT NULL,
  `email` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_spanish2_ci NOT NULL,
  `id_tipo_usuario` int NOT NULL,
  `activo` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`id_usuario`),
  KEY `usuarios_FK` (`id_tipo_usuario`),
  CONSTRAINT `usuarios_tipos_usuarios_FK` FOREIGN KEY (`id_tipo_usuario`) REFERENCES `tipos_usuarios` (`id_tipo_usuario`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish2_ci;