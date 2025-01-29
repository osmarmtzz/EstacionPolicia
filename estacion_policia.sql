-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 17-12-2024 a las 12:19:30
-- Versión del servidor: 10.4.32-MariaDB
-- Versión de PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `estacion_policia`
--

DELIMITER $$
--
-- Procedimientos
--
CREATE DEFINER=`root`@`localhost` PROCEDURE `archivar_casos_sin_evidencia` ()   BEGIN
    UPDATE caso
    SET estado_caso = 'Archivado'
    WHERE id_caso NOT IN (SELECT DISTINCT id_caso FROM evidencia)
    AND fecha_creacion_caso <= DATE_SUB(CURDATE(), INTERVAL 1 YEAR);
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `listar_casos_abiertos` ()   BEGIN
    DECLARE done INT DEFAULT FALSE;
    DECLARE caso_id INT;
    DECLARE oficial_id INT;
    DECLARE cur CURSOR FOR 
        SELECT a.id_caso, a.id_oficial 
        FROM asignacioncasosoficiales a
        JOIN caso c ON a.id_caso = c.id_caso
        WHERE c.estado_caso = 'Abierto';
    DECLARE CONTINUE HANDLER FOR NOT FOUND SET done = TRUE;
    
    OPEN cur;
    read_loop: LOOP
        FETCH cur INTO caso_id, oficial_id;
        IF done THEN
            LEAVE read_loop;
        END IF;
        SELECT CONCAT('Caso ID: ', caso_id, ' - Oficial ID: ', oficial_id);
    END LOOP;
    CLOSE cur;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `reporte_delitos_categoria` ()   BEGIN
    SELECT d.categoria_delito, COUNT(*) AS total_delitos
    FROM delito d
    JOIN caso c ON d.id_delito = c.id_caso
    GROUP BY d.categoria_delito;
END$$

--
-- Funciones
--
CREATE DEFINER=`root`@`localhost` FUNCTION `contar_casos_por_estado` (`estado` VARCHAR(20)) RETURNS INT(11) DETERMINISTIC BEGIN
    DECLARE total INT;
    SELECT COUNT(*) INTO total FROM caso WHERE estado_caso = estado;
    RETURN total;
END$$

CREATE DEFINER=`root`@`localhost` FUNCTION `promedio_casos_estacion` () RETURNS DECIMAL(10,2) DETERMINISTIC BEGIN
    DECLARE promedio DECIMAL(10,2);
    SELECT AVG(casos_totales) INTO promedio
    FROM (SELECT COUNT(*) AS casos_totales FROM caso GROUP BY id_estacion) AS subconsulta;
    RETURN promedio;
END$$

DELIMITER ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `asignacioncasosoficiales`
--

CREATE TABLE `asignacioncasosoficiales` (
  `id_caso` int(11) NOT NULL,
  `id_oficial` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Disparadores `asignacioncasosoficiales`
--
DELIMITER $$
CREATE TRIGGER `evitar_asignacion_caso_cerrado` BEFORE INSERT ON `asignacioncasosoficiales` FOR EACH ROW BEGIN
    DECLARE estado_actual VARCHAR(20);
    SELECT estado_caso INTO estado_actual FROM caso WHERE id_caso = NEW.id_caso;
    IF estado_actual = 'Cerrado' THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'No se puede asignar un oficial a un caso cerrado';
    END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `caso`
--

CREATE TABLE `caso` (
  `id_caso` int(11) NOT NULL,
  `descripcion_caso` text NOT NULL,
  `fecha_creacion_caso` date NOT NULL,
  `estado_caso` enum('Abierto','En investigación','Cerrado','Archivado') NOT NULL,
  `id_estacion` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `caso`
--

INSERT INTO `caso` (`id_caso`, `descripcion_caso`, `fecha_creacion_caso`, `estado_caso`, `id_estacion`) VALUES
(10, 'Asesinato', '2024-11-28', 'Abierto', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `delito`
--

CREATE TABLE `delito` (
  `id_delito` int(11) NOT NULL,
  `nombre_delito` varchar(100) NOT NULL,
  `descripcion_delito` text NOT NULL,
  `categoria_delito` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `delito`
--

INSERT INTO `delito` (`id_delito`, `nombre_delito`, `descripcion_delito`, `categoria_delito`) VALUES
(1, 'Robo Tienda', 'Sustracción de bienes sin autorización del civil', 'Crimen violento robo a mano armada');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `estacion`
--

CREATE TABLE `estacion` (
  `id_estacion` int(11) NOT NULL,
  `nombre_estacion` varchar(100) NOT NULL,
  `direccion_estacion` varchar(255) NOT NULL,
  `capacidad_estacion` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `estacion`
--

INSERT INTO `estacion` (`id_estacion`, `nombre_estacion`, `direccion_estacion`, `capacidad_estacion`) VALUES
(1, 'Estación Central', 'Av. Principal 123, Ciudad', 50),
(2, 'Estación Norte', 'Calle Norte 45, Ciudad', 30);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `evidencia`
--

CREATE TABLE `evidencia` (
  `id_evidencia` int(11) NOT NULL,
  `tipo_evidencia` varchar(50) NOT NULL,
  `descripcion_evidencia` text NOT NULL,
  `fecha_registro_evidencia` date NOT NULL,
  `lugar_recolectada_evidencia` varchar(255) NOT NULL,
  `id_caso` int(11) NOT NULL,
  `id_oficial` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Disparadores `evidencia`
--
DELIMITER $$
CREATE TRIGGER `cerrar_caso_si_evidencia` AFTER INSERT ON `evidencia` FOR EACH ROW BEGIN
    UPDATE caso
    SET estado_caso = 'Cerrado'
    WHERE id_caso = NEW.id_caso;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `oficial`
--

CREATE TABLE `oficial` (
  `id_oficial` int(11) NOT NULL,
  `nombre_oficial` varchar(100) NOT NULL,
  `rango_oficial` varchar(50) NOT NULL,
  `años_servicio_oficial` int(11) NOT NULL,
  `id_estacion` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `oficial`
--

INSERT INTO `oficial` (`id_oficial`, `nombre_oficial`, `rango_oficial`, `años_servicio_oficial`, `id_estacion`) VALUES
(17, 'Osmar Enrique Martinez Lopez', 'Capitan', 6, 2);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `seq_id_caso`
--

CREATE TABLE `seq_id_caso` (
  `next_not_cached_value` bigint(21) NOT NULL,
  `minimum_value` bigint(21) NOT NULL,
  `maximum_value` bigint(21) NOT NULL,
  `start_value` bigint(21) NOT NULL COMMENT 'start value when sequences is created or value if RESTART is used',
  `increment` bigint(21) NOT NULL COMMENT 'increment value',
  `cache_size` bigint(21) UNSIGNED NOT NULL,
  `cycle_option` tinyint(1) UNSIGNED NOT NULL COMMENT '0 if no cycles are allowed, 1 if the sequence should begin a new cycle when maximum_value is passed',
  `cycle_count` bigint(21) NOT NULL COMMENT 'How many cycles have been done'
) ENGINE=InnoDB;

--
-- Volcado de datos para la tabla `seq_id_caso`
--

INSERT INTO `seq_id_caso` (`next_not_cached_value`, `minimum_value`, `maximum_value`, `start_value`, `increment`, `cache_size`, `cycle_option`, `cycle_count`) VALUES
(1, 1, 9223372036854775806, 1, 1, 0, 0, 0);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sospechoso`
--

CREATE TABLE `sospechoso` (
  `id_sospechoso` int(11) NOT NULL,
  `nombre_sospechoso` varchar(100) NOT NULL,
  `direccion_sospechoso` varchar(255) NOT NULL,
  `estado_arresto_sospechoso` enum('Arrestado','No arrestado') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `sospechoso`
--

INSERT INTO `sospechoso` (`id_sospechoso`, `nombre_sospechoso`, `direccion_sospechoso`, `estado_arresto_sospechoso`) VALUES
(1, 'Alexis Valdez', 'Avenida Universidad', 'Arrestado');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `supervisor`
--

CREATE TABLE `supervisor` (
  `id_supervisor` int(11) NOT NULL,
  `nombre_supervisor` varchar(100) NOT NULL,
  `rango_supervisor` varchar(50) NOT NULL,
  `id_estacion` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `supervisor`
--

INSERT INTO `supervisor` (`id_supervisor`, `nombre_supervisor`, `rango_supervisor`, `id_estacion`) VALUES
(80, 'Carlos Rodriguez', 'Comandante', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `victima`
--

CREATE TABLE `victima` (
  `id_victima` int(11) NOT NULL,
  `nombre_victima` varchar(100) NOT NULL,
  `direccion_victima` varchar(255) NOT NULL,
  `estado_seguridad_victima` enum('Protegida','En riesgo') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `victima`
--

INSERT INTO `victima` (`id_victima`, `nombre_victima`, `direccion_victima`, `estado_seguridad_victima`) VALUES
(2, 'Andrés Torres', 'Av. Sur 654, Ciudad', 'En riesgo');

-- --------------------------------------------------------

--
-- Estructura Stand-in para la vista `vista_casos_estaciones`
-- (Véase abajo para la vista actual)
--
CREATE TABLE `vista_casos_estaciones` (
`id_caso` int(11)
,`descripcion_caso` text
,`fecha_creacion_caso` date
,`estado_caso` enum('Abierto','En investigación','Cerrado','Archivado')
,`nombre_estacion` varchar(100)
);

-- --------------------------------------------------------

--
-- Estructura Stand-in para la vista `vista_casos_oficiales`
-- (Véase abajo para la vista actual)
--
CREATE TABLE `vista_casos_oficiales` (
`id_caso` int(11)
,`descripcion_caso` text
,`id_oficial` int(11)
);

-- --------------------------------------------------------

--
-- Estructura Stand-in para la vista `vista_delitos_casos`
-- (Véase abajo para la vista actual)
--
CREATE TABLE `vista_delitos_casos` (
`id_delito` int(11)
,`nombre_delito` varchar(100)
,`categoria_delito` varchar(50)
,`id_caso` int(11)
);

-- --------------------------------------------------------

--
-- Estructura para la vista `vista_casos_estaciones`
--
DROP TABLE IF EXISTS `vista_casos_estaciones`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `vista_casos_estaciones`  AS SELECT `c`.`id_caso` AS `id_caso`, `c`.`descripcion_caso` AS `descripcion_caso`, `c`.`fecha_creacion_caso` AS `fecha_creacion_caso`, `c`.`estado_caso` AS `estado_caso`, `e`.`nombre_estacion` AS `nombre_estacion` FROM (`caso` `c` join `estacion` `e` on(`c`.`id_estacion` = `e`.`id_estacion`)) ;

-- --------------------------------------------------------

--
-- Estructura para la vista `vista_casos_oficiales`
--
DROP TABLE IF EXISTS `vista_casos_oficiales`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `vista_casos_oficiales`  AS SELECT `c`.`id_caso` AS `id_caso`, `c`.`descripcion_caso` AS `descripcion_caso`, `o`.`id_oficial` AS `id_oficial` FROM (`caso` `c` join `asignacioncasosoficiales` `o` on(`c`.`id_caso` = `o`.`id_caso`)) ;

-- --------------------------------------------------------

--
-- Estructura para la vista `vista_delitos_casos`
--
DROP TABLE IF EXISTS `vista_delitos_casos`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `vista_delitos_casos`  AS SELECT `d`.`id_delito` AS `id_delito`, `d`.`nombre_delito` AS `nombre_delito`, `d`.`categoria_delito` AS `categoria_delito`, `a`.`id_caso` AS `id_caso` FROM (`delito` `d` join `asignacioncasosoficiales` `a` on(`d`.`id_delito` = `a`.`id_caso`)) ;

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `asignacioncasosoficiales`
--
ALTER TABLE `asignacioncasosoficiales`
  ADD PRIMARY KEY (`id_caso`,`id_oficial`),
  ADD KEY `id_oficial` (`id_oficial`);

--
-- Indices de la tabla `caso`
--
ALTER TABLE `caso`
  ADD PRIMARY KEY (`id_caso`),
  ADD KEY `id_estacion` (`id_estacion`),
  ADD KEY `idx_id_estacion` (`id_estacion`);

--
-- Indices de la tabla `delito`
--
ALTER TABLE `delito`
  ADD PRIMARY KEY (`id_delito`);

--
-- Indices de la tabla `estacion`
--
ALTER TABLE `estacion`
  ADD PRIMARY KEY (`id_estacion`);

--
-- Indices de la tabla `evidencia`
--
ALTER TABLE `evidencia`
  ADD PRIMARY KEY (`id_evidencia`),
  ADD KEY `id_caso` (`id_caso`),
  ADD KEY `id_oficial` (`id_oficial`);

--
-- Indices de la tabla `oficial`
--
ALTER TABLE `oficial`
  ADD PRIMARY KEY (`id_oficial`),
  ADD KEY `id_estacion` (`id_estacion`);

--
-- Indices de la tabla `sospechoso`
--
ALTER TABLE `sospechoso`
  ADD PRIMARY KEY (`id_sospechoso`);

--
-- Indices de la tabla `supervisor`
--
ALTER TABLE `supervisor`
  ADD PRIMARY KEY (`id_supervisor`),
  ADD KEY `id_estacion` (`id_estacion`);

--
-- Indices de la tabla `victima`
--
ALTER TABLE `victima`
  ADD PRIMARY KEY (`id_victima`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `caso`
--
ALTER TABLE `caso`
  MODIFY `id_caso` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT de la tabla `delito`
--
ALTER TABLE `delito`
  MODIFY `id_delito` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT de la tabla `estacion`
--
ALTER TABLE `estacion`
  MODIFY `id_estacion` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `evidencia`
--
ALTER TABLE `evidencia`
  MODIFY `id_evidencia` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `oficial`
--
ALTER TABLE `oficial`
  MODIFY `id_oficial` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT de la tabla `sospechoso`
--
ALTER TABLE `sospechoso`
  MODIFY `id_sospechoso` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `supervisor`
--
ALTER TABLE `supervisor`
  MODIFY `id_supervisor` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=82;

--
-- AUTO_INCREMENT de la tabla `victima`
--
ALTER TABLE `victima`
  MODIFY `id_victima` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `asignacioncasosoficiales`
--
ALTER TABLE `asignacioncasosoficiales`
  ADD CONSTRAINT `asignacioncasosoficiales_ibfk_1` FOREIGN KEY (`id_caso`) REFERENCES `caso` (`id_caso`) ON DELETE CASCADE,
  ADD CONSTRAINT `asignacioncasosoficiales_ibfk_2` FOREIGN KEY (`id_oficial`) REFERENCES `oficial` (`id_oficial`) ON DELETE CASCADE;

--
-- Filtros para la tabla `caso`
--
ALTER TABLE `caso`
  ADD CONSTRAINT `caso_ibfk_1` FOREIGN KEY (`id_estacion`) REFERENCES `estacion` (`id_estacion`) ON DELETE CASCADE;

--
-- Filtros para la tabla `evidencia`
--
ALTER TABLE `evidencia`
  ADD CONSTRAINT `evidencia_ibfk_1` FOREIGN KEY (`id_caso`) REFERENCES `caso` (`id_caso`) ON DELETE CASCADE,
  ADD CONSTRAINT `evidencia_ibfk_2` FOREIGN KEY (`id_oficial`) REFERENCES `oficial` (`id_oficial`) ON DELETE SET NULL;

--
-- Filtros para la tabla `oficial`
--
ALTER TABLE `oficial`
  ADD CONSTRAINT `oficial_ibfk_1` FOREIGN KEY (`id_estacion`) REFERENCES `estacion` (`id_estacion`) ON DELETE CASCADE;

--
-- Filtros para la tabla `supervisor`
--
ALTER TABLE `supervisor`
  ADD CONSTRAINT `supervisor_ibfk_1` FOREIGN KEY (`id_estacion`) REFERENCES `estacion` (`id_estacion`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
