-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 22-04-2025 a las 19:58:03
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
-- Base de datos: `control`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `area`
--

CREATE TABLE `area` (
  `id` int(11) NOT NULL,
  `nombre` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `area`
--

INSERT INTO `area` (`id`, `nombre`) VALUES
(1, 'Offset'),
(2, 'Flexo'),
(3, 'Flexible'),
(4, 'Converting');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `maquinas`
--

CREATE TABLE `maquinas` (
  `id` int(11) NOT NULL,
  `nombre` varchar(255) NOT NULL,
  `area_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `maquinas`
--

INSERT INTO `maquinas` (`id`, `nombre`, `area_id`) VALUES
(1, 'Perfecta2', 1),
(2, 'Perfecta3', 1),
(3, 'SM-74', 1),
(4, 'CX-102', 1),
(5, 'Guillotina Offset', 1),
(6, 'Bobts 1', 1),
(7, 'Bobts 2', 1),
(8, 'B30', 1),
(9, 'B26', 1),
(10, 'Vijuk', 1),
(11, 'Omega 1', 1),
(12, 'Omega 2', 1),
(13, 'Jaguemberg', 1),
(14, 'Echo', 1),
(15, 'Grapadora', 1),
(16, 'Empaque', 1),
(17, 'Tape-Corto', 1),
(18, 'Sheeter', 1),
(19, 'Core Winder 1', 4),
(20, 'Core Winder 2', 4),
(21, 'Guillotina Convertig', 4),
(22, 'Core Cutters Huanlong', 4),
(23, 'Cortadora Foam', 4),
(24, 'Fusion C', 3),
(25, 'Laminación', 3),
(26, 'Slitter', 3),
(27, 'Pouch 1', 3),
(34, 'Pouch 2', 3),
(35, 'FZ 4120', 2),
(36, 'FZ AF 1650', 2),
(37, 'FZ AF 1658', 2),
(38, 'HP6000', 2),
(39, 'PAPER STICK MACHINE', 2),
(40, 'Rotowork', 2),
(41, 'Rotoflex digicut', 2),
(42, 'Arpeco', 2),
(43, 'CHINA', 2),
(44, 'Rotoflex #1', 2),
(45, 'Rotoflex #2', 2),
(46, 'Table top numbering system 1', 2),
(47, 'Table top numbering system 2', 2),
(48, 'V-System#1', 2),
(49, 'V-System #2', 2),
(50, 'Packing LG', 2),
(51, 'Core Winder 3', 4),
(54, 'MBO-B30#2', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `notificaciones`
--

CREATE TABLE `notificaciones` (
  `id` int(11) NOT NULL,
  `tipo` varchar(50) NOT NULL,
  `mensaje` text NOT NULL,
  `area_id` int(11) NOT NULL,
  `fecha` datetime DEFAULT current_timestamp(),
  `estado` enum('pendiente','leído') DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `notificaciones`
--

INSERT INTO `notificaciones` (`id`, `tipo`, `mensaje`, `area_id`, `fecha`, `estado`) VALUES
(1, 'info', 'Se ha registrado una nueva cantidad de producción o scrap en la máquina 1.', 1, '2025-04-21 21:07:46', 'leído'),
(2, 'info', 'Se ha registrado una nueva cantidad de producción o scrap en la máquina 1.', 1, '2025-04-21 22:17:02', 'leído'),
(3, 'info', 'Se ha registrado una nueva cantidad de producción o scrap en la máquina 1.', 1, '2025-04-21 22:17:19', 'leído'),
(4, 'info', 'Se ha registrado una nueva cantidad de producción o scrap en la máquina 1.', 1, '2025-04-21 22:17:31', 'leído'),
(5, 'info', 'Se ha registrado una nueva cantidad de producción o scrap en la máquina 1.', 1, '2025-04-21 22:40:34', 'leído'),
(6, 'info', 'Se ha registrado una nueva cantidad de producción o scrap en la máquina 1.', 1, '2025-04-21 22:40:46', 'leído'),
(7, 'info', 'Se ha registrado una nueva cantidad de producción o scrap en la máquina 1.', 1, '2025-04-21 22:45:48', 'leído'),
(8, 'info', 'Se ha registrado una nueva cantidad de producción o scrap en la máquina 1.', 1, '2025-04-21 22:46:03', 'leído');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `operacion`
--

CREATE TABLE `operacion` (
  `id` int(11) NOT NULL,
  `maquina_id` int(11) NOT NULL,
  `tipo_operacion` varchar(200) NOT NULL,
  `descripcion` varchar(200) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `operacion`
--

INSERT INTO `operacion` (`id`, `maquina_id`, `tipo_operacion`, `descripcion`) VALUES
(1, 1, 'Preparación', 'ESPERANDO MATERIAL'),
(2, 1, 'Preparación', 'AJUSTES DE PRE PRENSA'),
(3, 1, 'Preparación', 'ESPERA POR APROBACIÓN CLIENTE'),
(4, 1, 'Contratiempos', 'ESPERA POR APROBACIÓN CALIDAD'),
(5, 1, 'Preparación', 'CAMBIO DE MANTILLA'),
(6, 1, 'Preparación', 'CONTAMINACION DE BATERIA BATERIA DE ROLLOS'),
(7, 1, 'Preparación', 'CANTAMINACION DE AGUA'),
(8, 1, 'Preparación', 'CAMBIO DE BARNIZ'),
(9, 1, 'Preparación', 'LIMPIEZA DE MANTILLA'),
(10, 2, 'Preparación', 'ESPERANDO MATERIAL'),
(11, 2, 'Preparación', 'AJUSTES DE PRE PRENSA'),
(12, 2, 'Preparación', 'ESPERA POR APROBACIÓN CLIENTE'),
(13, 2, 'Contratiempos', 'ESPERA POR APROBACIÓN CALIDAD'),
(14, 2, 'Preparación', 'CAMBIO DE MANTILLA'),
(15, 2, 'Preparación', 'CONTAMINACION DE BATERIA BATERIA DE ROLLOS'),
(16, 2, 'Preparación', 'CANTAMINACION DE AGUA'),
(17, 2, 'Preparación', 'CAMBIO DE BARNIZ'),
(18, 2, 'Preparación', 'LIMPIEZA DE MANTILLA'),
(19, 3, 'Preparación', 'ESPERANDO MATERIAL'),
(20, 3, 'Preparación', 'AJUSTES DE PRE PRENSA'),
(21, 3, 'Preparación', 'ESPERA POR APROBACIÓN CLIENTE'),
(22, 3, 'Contratiempos', 'ESPERA POR APROBACIÓN CALIDAD'),
(23, 3, 'Preparación', 'CAMBIO DE MANTILLA'),
(24, 3, 'Preparación', 'CONTAMINACION DE BATERIA BATERIA DE ROLLOS'),
(25, 3, 'Preparación', 'CANTAMINACION DE AGUA'),
(26, 3, 'Preparación', 'CAMBIO DE BARNIZ'),
(27, 3, 'Preparación', 'LIMPIEZA DE MANTILLA'),
(28, 4, 'Preparación', 'ESPERANDO MATERIAL'),
(29, 4, 'Preparación', 'AJUSTES DE PRE PRENSA'),
(30, 4, 'Preparación', 'ESPERA POR APROBACIÓN CLIENTE'),
(31, 4, 'Contratiempos', 'ESPERA POR APROBACIÓN CALIDAD'),
(32, 4, 'Preparación', 'CAMBIO DE MANTILLA'),
(33, 4, 'Preparación', 'CONTAMINACION DE BATERIA BATERIA DE ROLLOS'),
(34, 4, 'Preparación', 'CANTAMINACION DE AGUA'),
(35, 4, 'Preparación', 'CAMBIO DE BARNIZ'),
(36, 4, 'Preparación', 'LIMPIEZA DE MANTILLA'),
(38, 5, 'Preparación', 'PP-IMP2  LIMPIEZA OPERACION'),
(39, 5, 'Preparación', 'PR-05    Preparacion ajuste por GRAMAJE'),
(40, 5, 'Preparación', 'PR-IMP5  ESPERA POR APROBACIÓN CALIDAD'),
(41, 5, 'Preparación', 'PP-IMP5  ENSAYOS DE LABORATORIO'),
(42, 5, 'Preparación', 'EP-01    Esperando Trabajo'),
(44, 6, 'Preparación', 'PP-IMP2  LIMPIEZA OPERACION'),
(45, 6, 'Preparación', 'PR-05    Preparacion ajuste por GRAMAJE'),
(46, 6, 'Preparación', 'PR-IMP5  ESPERA POR APROBACIÓN CALIDAD'),
(47, 6, 'Preparación', 'PP-IMP5  ENSAYOS DE LABORATORIO'),
(48, 6, 'Preparación', 'EP-01    Esperando Trabajo'),
(50, 7, 'Preparación', 'PP-IMP2  LIMPIEZA OPERACION'),
(51, 7, 'Preparación', 'PR-05    Preparacion ajuste por GRAMAJE'),
(52, 7, 'Preparación', 'PR-IMP5  ESPERA POR APROBACIÓN CALIDAD'),
(53, 7, 'Preparación', 'PP-IMP5  ENSAYOS DE LABORATORIO'),
(54, 7, 'Preparación', 'EP-01    Esperando Trabajo'),
(56, 8, 'Preparación', 'PP-IMP2  LIMPIEZA OPERACION'),
(57, 8, 'Preparación', 'PR-05    Preparacion ajuste de maquina'),
(58, 8, 'Preparación', 'PR-IMP5  ESPERA POR APROBACIÓN CALIDAD'),
(59, 8, 'Preparación', 'PP-IMP5  ENSAYOS DE LABORATORIO'),
(60, 8, 'Preparación', 'EP-01    Esperando Trabajo'),
(62, 9, 'Preparación', 'PP-IMP2  LIMPIEZA OPERACION'),
(63, 9, 'Preparación', 'PR-05    Preparacion ajuste de maquina'),
(64, 9, 'Preparación', 'PR-IMP5  ESPERA POR APROBACIÓN CALIDAD'),
(65, 9, 'Preparación', 'PP-IMP5  ENSAYOS DE LABORATORIO'),
(66, 9, 'Preparación', 'EP-01    Esperando Trabajo'),
(68, 10, 'Preparación', 'PP-IMP2  LIMPIEZA OPERACION'),
(69, 10, 'Preparación', 'PR-05    Preparacion ajuste de maquina'),
(70, 10, 'Preparación', 'PR-IMP5  ESPERA POR APROBACIÓN CALIDAD'),
(71, 10, 'Preparación', 'PP-IMP5  ENSAYOS DE LABORATORIO'),
(72, 10, 'Preparación', 'EP-01    Esperando Trabajo'),
(74, 11, 'Preparación', 'PP-IMP2  LIMPIEZA OPERACION'),
(75, 11, 'Preparación', 'PR-05    Preparacion ajuste de maquina'),
(76, 11, 'Preparación', 'PR-IMP5  ESPERA POR APROBACIÓN CALIDAD'),
(77, 11, 'Preparación', 'PP-IMP5  ENSAYOS DE LABORATORIO'),
(78, 11, 'Preparación', 'EP-01    Esperando Trabajo'),
(80, 12, 'Preparación', 'PP-IMP2  LIMPIEZA OPERACION'),
(81, 12, 'Preparación', 'PR-05    Preparacion ajuste de maquina'),
(82, 12, 'Preparación', 'PR-IMP5  ESPERA POR APROBACIÓN CALIDAD'),
(83, 12, 'Preparación', 'PP-IMP5  ENSAYOS DE LABORATORIO'),
(84, 12, 'Preparación', 'EP-01    Esperando Trabajo'),
(86, 13, 'Preparación', 'PP-IMP2  LIMPIEZA OPERACION'),
(87, 13, 'Preparación', 'PR-05    Preparacion ajuste de maquina'),
(88, 13, 'Preparación', 'PR-IMP5  ESPERA POR APROBACIÓN CALIDAD'),
(89, 13, 'Preparación', 'PP-IMP5  ENSAYOS DE LABORATORIO'),
(90, 13, 'Preparación', 'EP-01    Esperando Trabajo'),
(92, 14, 'Preparación', 'PP-IMP2  LIMPIEZA OPERACION'),
(93, 14, 'Preparación', 'PR-05    Preparacion ajuste de maquina'),
(94, 14, 'Preparación', 'PR-IMP5  ESPERA POR APROBACIÓN CALIDAD'),
(95, 14, 'Preparación', 'PP-IMP5  ENSAYOS DE LABORATORIO'),
(96, 14, 'Preparación', 'EP-01    Esperando Trabajo'),
(98, 15, 'Preparación', 'PP-IMP2  LIMPIEZA OPERACION'),
(99, 15, 'Preparación', 'PR-05    Preparacion ajuste de maquina'),
(100, 15, 'Preparación', 'PR-IMP5  ESPERA POR APROBACIÓN CALIDAD'),
(101, 15, 'Preparación', 'PP-IMP5  ENSAYOS DE LABORATORIO'),
(102, 15, 'Preparación', 'EP-01    Esperando Trabajo'),
(104, 16, 'Preparación', 'PP-IMP2  LIMPIEZA OPERACION'),
(105, 16, 'Preparación', 'PR-05    Preparacion ajuste de maquina'),
(106, 16, 'Preparación', 'PR-IMP5  ESPERA POR APROBACIÓN CALIDAD'),
(107, 16, 'Preparación', 'PP-IMP5  ENSAYOS DE LABORATORIO'),
(108, 16, 'Preparación', 'EP-01    Esperando Trabajo'),
(110, 17, 'Preparación', 'PP-IMP2  LIMPIEZA OPERACION'),
(111, 17, 'Preparación', 'PR-05    Preparacion ajuste de maquina'),
(112, 17, 'Preparación', 'PR-IMP5  ESPERA POR APROBACIÓN CALIDAD'),
(113, 17, 'Preparación', 'PP-IMP5  ENSAYOS DE LABORATORIO'),
(114, 17, 'Preparación', 'EP-01    Esperando Trabajo'),
(115, 15, 'Contratiempos', 'Falta de personal'),
(116, 18, 'Preparación', 'PP-IMP2  LIMPIEZA OPERACION'),
(117, 18, 'Preparación', 'PR-05    Preparacion ajuste de maquina'),
(118, 18, 'Preparación', 'PR-IMP5  ESPERA POR APROBACIÓN CALIDAD'),
(119, 18, 'Preparación', 'PP-IMP5  ENSAYOS DE LABORATORIO'),
(120, 18, 'Preparación', 'EP-01    Esperando Trabajo'),
(121, 1, 'Contratiempos', 'FALTA DE MATIRIA PRIMA (ALMACEN PAPEL )'),
(122, 1, 'Contratiempos', 'FALTA DE MATERIA PRIMA (TINTA )'),
(123, 1, 'Contratiempos', 'MANTEMIMIENTO NO PROGRAMANDO'),
(124, 1, 'Contratiempos', 'RETOQUE DE TINTAS'),
(125, 1, 'Contratiempos', 'ESPERA DE TINTAS EN PRENSA'),
(126, 1, 'Contratiempos', 'ERROR EN LA PROGRAMACIÓN'),
(127, 1, 'Contratiempos', 'PARO POR EVENTO NATURAL'),
(128, 1, 'Contratiempos', 'CAMBIO DE MANTILLA POR ROTURA EN LA CORRIDA'),
(129, 1, 'Contratiempos', 'CAMBIO DE QUIMICOS DEL EQUIPO (SOLUCION DE FURNTE )'),
(130, 1, 'Contratiempos', 'CAMBIO DE LOS FLUIDOS DE LA MAQUINA (ACEITE y REFRIGERANTE)'),
(131, 2, 'Contratiempos', 'FALTA DE MATIRIA PRIMA (ALMACEN PAPEL )'),
(132, 2, 'Contratiempos', 'FALTA DE MATERIA PRIMA (TINTA )'),
(133, 2, 'Contratiempos', 'MANTEMIMIENTO NO PROGRAMANDO'),
(134, 2, 'Contratiempos', 'RETOQUE DE TINTAS'),
(135, 2, 'Contratiempos', 'ESPERA DE TINTAS EN PRENSA'),
(136, 2, 'Contratiempos', 'ERROR EN LA PROGRAMACIÓN'),
(137, 2, 'Contratiempos', 'PARO POR EVENTO NATURAL'),
(138, 2, 'Contratiempos', 'CAMBIO DE MANTILLA POR ROTURA EN LA CORRIDA'),
(139, 2, 'Contratiempos', 'CAMBIO DE QUIMICOS DEL EQUIPO (SOLUCION DE FURNTE )'),
(140, 2, 'Contratiempos', 'CAMBIO DE LOS FLUIDOS DE LA MAQUINA (ACEITE y REFRIGERANTE)'),
(141, 3, 'Contratiempos', 'FALTA DE MATIRIA PRIMA (ALMACEN PAPEL )'),
(142, 3, 'Contratiempos', 'FALTA DE MATERIA PRIMA (TINTA )'),
(143, 3, 'Contratiempos', 'MANTEMIMIENTO NO PROGRAMANDO'),
(144, 3, 'Contratiempos', 'RETOQUE DE TINTAS'),
(145, 3, 'Contratiempos', 'ESPERA DE TINTAS EN PRENSA'),
(146, 3, 'Contratiempos', 'ERROR EN LA PROGRAMACIÓN'),
(147, 3, 'Contratiempos', 'PARO POR EVENTO NATURAL'),
(148, 3, 'Contratiempos', 'CAMBIO DE MANTILLA POR ROTURA EN LA CORRIDA'),
(149, 3, 'Contratiempos', 'CAMBIO DE QUIMICOS DEL EQUIPO (SOLUCION DE FURNTE )'),
(150, 3, 'Contratiempos', 'CAMBIO DE LOS FLUIDOS DE LA MAQUINA (ACEITE y REFRIGERANTE)'),
(151, 4, 'Contratiempos', 'FALTA DE MATIRIA PRIMA (ALMACEN PAPEL )'),
(152, 4, 'Contratiempos', 'FALTA DE MATERIA PRIMA (TINTA )'),
(153, 4, 'Contratiempos', 'MANTEMIMIENTO NO PROGRAMANDO'),
(154, 4, 'Contratiempos', 'RETOQUE DE TINTAS'),
(155, 4, 'Contratiempos', 'ESPERA DE TINTAS EN PRENSA'),
(156, 4, 'Contratiempos', 'ERROR EN LA PROGRAMACIÓN'),
(157, 4, 'Contratiempos', 'PARO POR EVENTO NATURAL'),
(158, 4, 'Contratiempos', 'CAMBIO DE MANTILLA POR ROTURA EN LA CORRIDA'),
(159, 4, 'Contratiempos', 'CAMBIO DE QUIMICOS DEL EQUIPO (SOLUCION DE FURNTE )'),
(160, 4, 'Contratiempos', 'CAMBIO DE LOS FLUIDOS DE LA MAQUINA (ACEITE y REFRIGERANTE)'),
(161, 5, 'Contratiempos', 'PM-01    Produccion Mala'),
(162, 5, 'Contratiempos', 'PR-IMP6  CAMBIO DE COMPONENTES'),
(163, 5, 'Contratiempos', 'PP-01    Parada Caida Tension'),
(164, 5, 'Contratiempos', 'PP-IMP4  ERROR EN LA PROGRAMACIÓN'),
(165, 5, 'Contratiempos', 'PP-03    Parada Empate / Ruptura de films'),
(166, 5, 'Contratiempos', 'MT01     Parada mantenimiento correctivo electrico'),
(167, 5, 'Contratiempos', 'MT01     Parada mantenimiento correctivo mecanico'),
(168, 5, 'Contratiempos', 'MT04     Parada mantenimiento preventivo mecanico'),
(169, 5, 'Contratiempos', 'MT03     Parada mantenimiento preventivo electrico'),
(170, 5, 'Contratiempos', 'PP-04    Almuerzo/Break'),
(171, 5, 'Contratiempos', 'PP-IMP8  FALTA DE MATERIAL'),
(172, 6, 'Contratiempos', 'PM-01    Produccion Mala'),
(173, 6, 'Contratiempos', 'PR-IMP6  CAMBIO DE COMPONENTES'),
(174, 6, 'Contratiempos', 'PP-01    Parada Caida Tension'),
(175, 6, 'Contratiempos', 'PP-IMP4  ERROR EN LA PROGRAMACIÓN'),
(176, 6, 'Contratiempos', 'PP-03    Parada Empate / Ruptura de films'),
(177, 6, 'Contratiempos', 'MT01     Parada mantenimiento correctivo electrico'),
(178, 6, 'Contratiempos', 'MT01     Parada mantenimiento correctivo mecanico'),
(179, 6, 'Contratiempos', 'MT04     Parada mantenimiento preventivo mecanico'),
(180, 6, 'Contratiempos', 'MT03     Parada mantenimiento preventivo electrico'),
(181, 6, 'Contratiempos', 'PP-04    Almuerzo/Break'),
(182, 6, 'Contratiempos', 'PP-IMP8  FALTA DE MATERIAL'),
(183, 7, 'Contratiempos', 'PM-01    Produccion Mala'),
(184, 7, 'Contratiempos', 'PR-IMP6  CAMBIO DE COMPONENTES'),
(185, 7, 'Contratiempos', 'PP-01    Parada Caida Tension'),
(186, 7, 'Contratiempos', 'PP-IMP4  ERROR EN LA PROGRAMACIÓN'),
(187, 7, 'Contratiempos', 'PP-03    Parada Empate / Ruptura de films'),
(188, 7, 'Contratiempos', 'MT01     Parada mantenimiento correctivo electrico'),
(189, 7, 'Contratiempos', 'MT01     Parada mantenimiento correctivo mecanico'),
(190, 7, 'Contratiempos', 'MT04     Parada mantenimiento preventivo mecanico'),
(191, 7, 'Contratiempos', 'MT03     Parada mantenimiento preventivo electrico'),
(192, 7, 'Contratiempos', 'PP-04    Almuerzo/Break'),
(193, 7, 'Contratiempos', 'PP-IMP8  FALTA DE MATERIAL'),
(194, 8, 'Contratiempos', 'PM-01    Produccion Mala'),
(195, 8, 'Contratiempos', 'PR-IMP6  CAMBIO DE COMPONENTES'),
(196, 8, 'Contratiempos', 'PP-01    Parada Caida Tension'),
(197, 8, 'Contratiempos', 'PP-IMP4  ERROR EN LA PROGRAMACIÓN'),
(198, 8, 'Contratiempos', 'PP-03    Parada Empate / Ruptura de films'),
(199, 8, 'Contratiempos', 'MT01     Parada mantenimiento correctivo electrico'),
(200, 8, 'Contratiempos', 'MT01     Parada mantenimiento correctivo mecanico'),
(201, 8, 'Contratiempos', 'MT04     Parada mantenimiento preventivo mecanico'),
(202, 8, 'Contratiempos', 'MT03     Parada mantenimiento preventivo electrico'),
(203, 8, 'Contratiempos', 'PP-04    Almuerzo/Break'),
(204, 8, 'Contratiempos', 'PP-IMP8  FALTA DE MATERIAL'),
(205, 8, 'Contratiempos', 'PP-IMP9 CAMBIO DE ROLLO'),
(206, 9, 'Contratiempos', 'PM-01    Produccion Mala'),
(207, 9, 'Contratiempos', 'PR-IMP6  CAMBIO DE COMPONENTES'),
(208, 9, 'Contratiempos', 'PP-01    Parada Caida Tension'),
(209, 9, 'Contratiempos', 'PP-IMP4  ERROR EN LA PROGRAMACIÓN'),
(210, 9, 'Contratiempos', 'PP-03    Parada Empate / Ruptura de films'),
(211, 9, 'Contratiempos', 'MT01     Parada mantenimiento correctivo electrico'),
(212, 9, 'Contratiempos', 'MT01     Parada mantenimiento correctivo mecanico'),
(213, 9, 'Contratiempos', 'MT04     Parada mantenimiento preventivo mecanico'),
(214, 9, 'Contratiempos', 'MT03     Parada mantenimiento preventivo electrico'),
(215, 9, 'Contratiempos', 'PP-04    Almuerzo/Break'),
(216, 9, 'Contratiempos', 'PP-IMP8  FALTA DE MATERIAL'),
(217, 9, 'Contratiempos', 'PP-IMP9 CAMBIO DE ROLLO'),
(218, 10, 'Contratiempos', 'PM-01    Produccion Mala'),
(219, 10, 'Contratiempos', 'PR-IMP6  CAMBIO DE COMPONENTES'),
(220, 10, 'Contratiempos', 'PP-01    Parada Caida Tension'),
(221, 10, 'Contratiempos', 'PP-IMP4  ERROR EN LA PROGRAMACIÓN'),
(222, 10, 'Contratiempos', 'PP-03    Parada Empate / Ruptura de films'),
(223, 10, 'Contratiempos', 'MT01     Parada mantenimiento correctivo electrico'),
(224, 10, 'Contratiempos', 'MT01     Parada mantenimiento correctivo mecanico'),
(225, 10, 'Contratiempos', 'MT04     Parada mantenimiento preventivo mecanico'),
(226, 10, 'Contratiempos', 'MT03     Parada mantenimiento preventivo electrico'),
(227, 10, 'Contratiempos', 'PP-04    Almuerzo/Break'),
(228, 10, 'Contratiempos', 'PP-IMP8  FALTA DE MATERIAL'),
(229, 10, 'Contratiempos', 'PP-IMP9 CAMBIO DE ROLLO'),
(230, 11, 'Contratiempos', 'PM-01    Produccion Mala'),
(231, 11, 'Contratiempos', 'PR-IMP6  CAMBIO DE COMPONENTES'),
(232, 11, 'Contratiempos', 'PP-01    Parada Caida Tension'),
(233, 11, 'Contratiempos', 'PP-IMP4  ERROR EN LA PROGRAMACIÓN'),
(234, 11, 'Contratiempos', 'PP-03    Parada Empate / Ruptura de films'),
(235, 11, 'Contratiempos', 'MT01     Parada mantenimiento correctivo electrico'),
(236, 11, 'Contratiempos', 'MT01     Parada mantenimiento correctivo mecanico'),
(237, 11, 'Contratiempos', 'MT04     Parada mantenimiento preventivo mecanico'),
(238, 11, 'Contratiempos', 'MT03     Parada mantenimiento preventivo electrico'),
(239, 11, 'Contratiempos', 'PP-04    Almuerzo/Break'),
(240, 11, 'Contratiempos', 'PP-IMP8  FALTA DE MATERIAL'),
(241, 11, 'Contratiempos', 'PP-IMP9 CAMBIO DE ROLLO'),
(242, 12, 'Contratiempos', 'PM-01    Produccion Mala'),
(243, 12, 'Contratiempos', 'PR-IMP6  CAMBIO DE COMPONENTES'),
(244, 12, 'Contratiempos', 'PP-01    Parada Caida Tension'),
(245, 12, 'Contratiempos', 'PP-IMP4  ERROR EN LA PROGRAMACIÓN'),
(246, 12, 'Contratiempos', 'PP-03    Parada Empate / Ruptura de films'),
(247, 12, 'Contratiempos', 'MT01     Parada mantenimiento correctivo electrico'),
(248, 12, 'Contratiempos', 'MT01     Parada mantenimiento correctivo mecanico'),
(249, 12, 'Contratiempos', 'MT04     Parada mantenimiento preventivo mecanico'),
(250, 12, 'Contratiempos', 'MT03     Parada mantenimiento preventivo electrico'),
(251, 12, 'Contratiempos', 'PP-04    Almuerzo/Break'),
(252, 12, 'Contratiempos', 'PP-IMP8  FALTA DE MATERIAL'),
(253, 12, 'Contratiempos', 'PP-IMP9 CAMBIO DE ROLLO'),
(254, 13, 'Contratiempos', 'PM-01    Produccion Mala'),
(255, 13, 'Contratiempos', 'PR-IMP6  CAMBIO DE COMPONENTES'),
(256, 13, 'Contratiempos', 'PP-01    Parada Caida Tension'),
(257, 13, 'Contratiempos', 'PP-IMP4  ERROR EN LA PROGRAMACIÓN'),
(258, 13, 'Contratiempos', 'PP-03    Parada Empate / Ruptura de films'),
(259, 13, 'Contratiempos', 'MT01     Parada mantenimiento correctivo electrico'),
(260, 13, 'Contratiempos', 'MT01     Parada mantenimiento correctivo mecanico'),
(261, 13, 'Contratiempos', 'MT04     Parada mantenimiento preventivo mecanico'),
(262, 13, 'Contratiempos', 'MT03     Parada mantenimiento preventivo electrico'),
(263, 13, 'Contratiempos', 'PP-04    Almuerzo/Break'),
(264, 13, 'Contratiempos', 'PP-IMP8  FALTA DE MATERIAL'),
(265, 13, 'Contratiempos', 'PP-IMP9 CAMBIO DE ROLLO'),
(266, 14, 'Contratiempos', 'PM-01    Produccion Mala'),
(267, 14, 'Contratiempos', 'PR-IMP6  CAMBIO DE COMPONENTES'),
(268, 14, 'Contratiempos', 'PP-01    Parada Caida Tension'),
(269, 14, 'Contratiempos', 'PP-IMP4  ERROR EN LA PROGRAMACIÓN'),
(270, 14, 'Contratiempos', 'PP-03    Parada Empate / Ruptura de films'),
(271, 14, 'Contratiempos', 'MT01     Parada mantenimiento correctivo electrico'),
(272, 14, 'Contratiempos', 'MT01     Parada mantenimiento correctivo mecanico'),
(273, 14, 'Contratiempos', 'MT04     Parada mantenimiento preventivo mecanico'),
(274, 14, 'Contratiempos', 'MT03     Parada mantenimiento preventivo electrico'),
(275, 14, 'Contratiempos', 'PP-04    Almuerzo/Break'),
(276, 14, 'Contratiempos', 'PP-IMP8  FALTA DE MATERIAL'),
(277, 14, 'Contratiempos', 'PP-IMP9 CAMBIO DE ROLLO'),
(278, 15, 'Contratiempos', 'PM-01    Produccion Mala'),
(279, 15, 'Contratiempos', 'PR-IMP6  CAMBIO DE COMPONENTES'),
(280, 15, 'Contratiempos', 'PP-01    Parada Caida Tension'),
(281, 15, 'Contratiempos', 'PP-IMP4  ERROR EN LA PROGRAMACIÓN'),
(282, 15, 'Contratiempos', 'PP-03    Parada Empate / Ruptura de films'),
(283, 15, 'Contratiempos', 'MT01     Parada mantenimiento correctivo electrico'),
(284, 15, 'Contratiempos', 'MT01     Parada mantenimiento correctivo mecanico'),
(285, 15, 'Contratiempos', 'MT04     Parada mantenimiento preventivo mecanico'),
(286, 15, 'Contratiempos', 'MT03     Parada mantenimiento preventivo electrico'),
(287, 15, 'Contratiempos', 'PP-04    Almuerzo/Break'),
(288, 15, 'Contratiempos', 'PP-IMP8  FALTA DE MATERIAL'),
(289, 15, 'Contratiempos', 'PP-IMP9 CAMBIO DE ROLLO'),
(290, 16, 'Contratiempos', 'PM-01    Produccion Mala'),
(291, 16, 'Contratiempos', 'PR-IMP6  CAMBIO DE COMPONENTES'),
(292, 16, 'Contratiempos', 'PP-01    Parada Caida Tension'),
(293, 16, 'Contratiempos', 'PP-IMP4  ERROR EN LA PROGRAMACIÓN'),
(294, 16, 'Contratiempos', 'PP-03    Parada Empate / Ruptura de films'),
(295, 16, 'Contratiempos', 'MT01     Parada mantenimiento correctivo electrico'),
(296, 16, 'Contratiempos', 'MT01     Parada mantenimiento correctivo mecanico'),
(297, 16, 'Contratiempos', 'MT04     Parada mantenimiento preventivo mecanico'),
(298, 16, 'Contratiempos', 'MT03     Parada mantenimiento preventivo electrico'),
(299, 16, 'Contratiempos', 'PP-04    Almuerzo/Break'),
(300, 16, 'Contratiempos', 'PP-IMP8  FALTA DE MATERIAL'),
(301, 16, 'Contratiempos', 'PP-IMP9 CAMBIO DE ROLLO'),
(302, 17, 'Contratiempos', 'PM-01    Produccion Mala'),
(303, 17, 'Contratiempos', 'PR-IMP6  CAMBIO DE COMPONENTES'),
(304, 17, 'Contratiempos', 'PP-01    Parada Caida Tension'),
(305, 17, 'Contratiempos', 'PP-IMP4  ERROR EN LA PROGRAMACIÓN'),
(306, 17, 'Contratiempos', 'PP-03    Parada Empate / Ruptura de films'),
(307, 17, 'Contratiempos', 'MT01     Parada mantenimiento correctivo electrico'),
(308, 17, 'Contratiempos', 'MT01     Parada mantenimiento correctivo mecanico'),
(309, 17, 'Contratiempos', 'MT04     Parada mantenimiento preventivo mecanico'),
(310, 17, 'Contratiempos', 'MT03     Parada mantenimiento preventivo electrico'),
(311, 17, 'Contratiempos', 'PP-04    Almuerzo/Break'),
(312, 17, 'Contratiempos', 'PP-IMP8  FALTA DE MATERIAL'),
(313, 17, 'Contratiempos', 'PP-IMP9 CAMBIO DE ROLLO'),
(314, 18, 'Contratiempos', 'PM-01    Produccion Mala'),
(315, 18, 'Contratiempos', 'PR-IMP6  CAMBIO DE COMPONENTES'),
(316, 18, 'Contratiempos', 'PP-01    Parada Caida Tension'),
(317, 18, 'Contratiempos', 'PP-IMP4  ERROR EN LA PROGRAMACIÓN'),
(318, 18, 'Contratiempos', 'PP-03    Parada Empate / Ruptura de films'),
(319, 18, 'Contratiempos', 'MT01     Parada mantenimiento correctivo electrico'),
(320, 18, 'Contratiempos', 'MT01     Parada mantenimiento correctivo mecanico'),
(321, 18, 'Contratiempos', 'MT04     Parada mantenimiento preventivo mecanico'),
(322, 18, 'Contratiempos', 'MT03     Parada mantenimiento preventivo electrico'),
(323, 18, 'Contratiempos', 'PP-04    Almuerzo/Break'),
(324, 18, 'Contratiempos', 'PP-IMP8  FALTA DE MATERIAL'),
(325, 18, 'Contratiempos', 'PP-IMP9 CAMBIO DE ROLLO'),
(329, 24, 'Preparación', 'PR-IMP1  CAMBIO DE PEDIDO'),
(330, 24, 'Preparación', 'PR-03    Preparacion cyrel'),
(331, 24, 'Preparación', 'PP-IMP2  LIMPIEZA OPERACION'),
(332, 24, 'Preparación', 'PR-IMP3  AJUSTE DE TONOS DE PRENSA'),
(333, 24, 'Preparación', 'PR-05    Preparacion ajuste por reg'),
(334, 24, 'Preparación', 'PR-IMP4  ESPERA POR APROBACIÓN CLIENTE'),
(336, 24, 'Preparación', 'PP-IMP5  ENSAYOS DE LABORATORIIO'),
(338, 24, 'Preparación', 'PP-IMP11 PREPRENSA'),
(340, 24, 'Contratiempos', 'PM-01    Produccion Mala'),
(341, 24, 'Contratiempos', 'PR-IMP6  CAMBIO DE COMPONENTES'),
(342, 24, 'Contratiempos', 'PP-01    Parada Caida Tension'),
(343, 24, 'Contratiempos', 'PP-IMP7  RETOQUE DE TINTAS'),
(344, 24, 'Contratiempos', 'PP-03    Parada Empate / Ruptura de films'),
(345, 24, 'Contratiempos', 'MT01     Parada mantenimiento correctivo electrico'),
(346, 24, 'Contratiempos', 'MT01     Parada mantenimiento correctivo mecanico'),
(347, 24, 'Contratiempos', 'MT04     Parada mantenimiento preventivo mecanico'),
(348, 24, 'Contratiempos', 'MT03     Parada mantenimiento preventivo electrico'),
(349, 24, 'Contratiempos', 'PP-IMP1  ESPERA DE TINTAS EN PRENSA'),
(350, 24, 'Contratiempos', 'PR-02    Preparacion ink'),
(351, 24, 'Contratiempos', 'PP-04    Almuerzo/Break'),
(352, 24, 'Contratiempos', 'PP-IMP8  FALTA DE MATERIAL'),
(353, 24, 'Contratiempos', 'PP-IMP4  ERROR EN LA PROGRAMACIÓN'),
(354, 25, 'Preparación', 'PR-IMP1  CAMBIO DE PEDIDO'),
(355, 25, 'Preparación', 'PP-IMP2  LIMPIEZA OPERACION'),
(356, 25, 'Preparación', 'PR-05    Preparacion ajuste por GRAMAJE'),
(358, 25, 'Preparación', 'PP-IMP5  ENSAYOS DE LABORATORIIO'),
(360, 25, 'Contratiempos', 'PM-01    Produccion Mala'),
(361, 25, 'Contratiempos', 'PR-IMP6  CAMBIO DE COMPONENTES'),
(362, 25, 'Contratiempos', 'PP-01    Parada Caida Tension'),
(363, 25, 'Contratiempos', 'PP-IMP4  ERROR EN LA PROGRAMACIÓN'),
(364, 25, 'Contratiempos', 'PP-03    Parada Empate / Ruptura de films'),
(365, 25, 'Contratiempos', 'MT01     Parada mantenimiento correctivo electrico'),
(366, 25, 'Contratiempos', 'MT01     Parada mantenimiento correctivo mecanico'),
(367, 25, 'Contratiempos', 'MT04     Parada mantenimiento preventivo mecanico'),
(368, 25, 'Contratiempos', 'MT03     Parada mantenimiento preventivo electrico'),
(369, 25, 'Contratiempos', 'PP-04    Almuerzo/Break'),
(370, 25, 'Contratiempos', 'PP-IMP8  FALTA DE MATERIAL'),
(371, 26, 'Preparación', 'PR-IMP1  CAMBIO DE PEDIDO'),
(372, 26, 'Preparación', 'PP-IMP2  LIMPIEZA OPERACION'),
(373, 26, 'Preparación', 'PR-05    Preparacion ajuste de maquina'),
(375, 26, 'Preparación', 'PP-IMP5  ENSAYOS DE LABORATORIIO'),
(377, 26, 'Contratiempos', 'PM-01    Produccion Mala'),
(378, 26, 'Contratiempos', 'PR-IMP6  CAMBIO DE CUCHILLAS'),
(379, 26, 'Contratiempos', 'PP-01    Parada Caida Tension'),
(380, 26, 'Contratiempos', 'PP-IMP4  ERROR EN LA PROGRAMACIÓN'),
(381, 26, 'Contratiempos', 'PP-03    Parada Empate / Ruptura de films'),
(382, 26, 'Contratiempos', 'MT01     Parada mantenimiento correctivo electrico'),
(383, 26, 'Contratiempos', 'MT01     Parada mantenimiento correctivo mecanico'),
(384, 26, 'Contratiempos', 'MT04     Parada mantenimiento preventivo mecanico'),
(385, 26, 'Contratiempos', 'MT03     Parada mantenimiento preventivo electrico'),
(386, 26, 'Contratiempos', 'PP-04    Almuerzo/Break'),
(387, 26, 'Contratiempos', 'PP-IMP8  ESPERA DE MATERIAL'),
(388, 26, 'Contratiempos', 'PP-IMP9 BOBINA MADRE TERMINADA '),
(389, 27, 'Preparación', 'PR-IMP1  CAMBIO DE PEDIDO'),
(390, 27, 'Preparación', 'PP-IMP2  LIMPIEZA OPERACION'),
(391, 27, 'Preparación', 'PR-05    Preparacion ajuste de maquina'),
(393, 27, 'Preparación', 'PP-IMP5  ENSAYOS DE LABORATORIIO'),
(395, 27, 'Contratiempos', 'PM-01    Produccion Mala'),
(396, 27, 'Contratiempos', 'PR-IMP6  CAMBIO DE COMPONENTES'),
(397, 27, 'Contratiempos', 'PP-01    Parada Caida Tension'),
(398, 27, 'Contratiempos', 'PP-IMP4  ERROR EN LA PROGRAMACIÓN'),
(399, 27, 'Contratiempos', 'PP-03    Parada Empate / Ruptura de films'),
(400, 27, 'Contratiempos', 'MT01     Parada mantenimiento correctivo electrico'),
(401, 27, 'Contratiempos', 'MT01     Parada mantenimiento correctivo mecanico'),
(402, 27, 'Contratiempos', 'MT04     Parada mantenimiento preventivo mecanico'),
(403, 27, 'Contratiempos', 'MT03     Parada mantenimiento preventivo electrico'),
(404, 27, 'Contratiempos', 'PP-04    Almuerzo/Break'),
(405, 27, 'Contratiempos', 'PP-IMP8  FALTA DE MATERIAL'),
(406, 27, 'Contratiempos', 'PP-IMP9 CAMBIO DE ROLLO'),
(407, 19, 'Preparacion', 'SETEO O PREPARACION'),
(408, 19, 'Preparacion', 'ESPERA DE MATERIAL'),
(409, 19, 'Preparacion', 'ESPERA POR APROBACIÓN'),
(410, 20, 'Preparación', 'SETEO O PREPARACION'),
(411, 20, 'Preparación', 'ESPERA DE MATERIAL'),
(412, 20, 'Preparación', 'ESPERA POR APROBACIÓN'),
(413, 19, 'Contratiempos', 'ESPERA DE MATERIAL'),
(414, 19, 'Contratiempos', 'MANTEMIMIENTO NO PROGRAMANDO'),
(415, 19, 'Contratiempos', 'PROBLEMAS CALENTADORES DE COLA'),
(416, 19, 'Contratiempos', 'PROBLEMAS CUCHILLA DE CORTE'),
(417, 19, 'Contratiempos', 'FALTA DE AIRE COMPRIMIDO'),
(418, 19, 'Contratiempos', 'PROBLEMAS GRUA ESTANTE DE ROLLOS'),
(419, 19, 'Contratiempos', 'PROBLEMAS ELECTICOS DEL EQUIPO'),
(420, 19, 'Contratiempos', 'ALMUERZO'),
(421, 20, 'Contratiempos', 'ESPERA DE MATERIAL'),
(422, 20, 'Contratiempos', 'MANTEMIMIENTO NO PROGRAMANDO'),
(423, 20, 'Contratiempos', 'PROBLEMAS CALENTADORES DE COLA'),
(424, 20, 'Contratiempos', 'PROBLEMAS CUCHILLA DE CORTE'),
(425, 20, 'Contratiempos', 'FALTA DE AIRE COMPRIMIDO'),
(426, 20, 'Contratiempos', 'PROBLEMAS GRUA ESTANTE DE ROLLOS'),
(427, 20, 'Contratiempos', 'PROBLEMAS ELECTICOS DEL EQUIPO'),
(428, 20, 'Contratiempos', 'ALMUERZO'),
(429, 21, 'Preparacion', 'SETEO O PREPARACION'),
(430, 21, 'Preparacion', 'ESPERA DE MATERIAL'),
(431, 21, 'Preparacion', 'ESPERA POR APROBACIÓN'),
(432, 22, 'Preparacion', 'SETEO O PREPARACION'),
(433, 22, 'Preparacion', 'ESPERA DE MATERIAL'),
(434, 22, 'Preparacion', 'ESPERA POR APROBACIÓN'),
(435, 23, 'Preparacion', 'SETEO O PREPARACION'),
(436, 23, 'Preparacion', 'ESPERA DE MATERIAL'),
(437, 23, 'Preparacion', 'ESPERA POR APROBACIÓN'),
(438, 21, 'Contratiempos', 'ESPERA DE MATERIAL'),
(439, 21, 'Contratiempos', 'PROBLEMAS CUCHILLA DE CORTE'),
(440, 21, 'Contratiempos', 'PROBLEMAS ELECTICOS DEL EQUIPO'),
(441, 21, 'Contratiempos', 'FALTA DE AIRE COMPRIMIDO'),
(442, 21, 'Contratiempos', 'ALMUERZO'),
(443, 22, 'Contratiempos', 'ESPERA DE MATERIAL'),
(444, 22, 'Contratiempos', 'PROBLEMAS CUCHILLA DE CORTE'),
(445, 22, 'Contratiempos', 'PROBLEMAS ELECTICOS DEL EQUIPO'),
(446, 22, 'Contratiempos', 'FALTA DE AIRE COMPRIMIDO'),
(447, 22, 'Contratiempos', 'ALMUERZO'),
(448, 23, 'Contratiempos', 'ESPERA DE MATERIAL'),
(449, 23, 'Contratiempos', 'PROBLEMAS CUCHILLA DE CORTE'),
(450, 23, 'Contratiempos', 'PROBLEMAS ELECTICOS DEL EQUIPO'),
(451, 23, 'Contratiempos', 'FALTA DE AIRE COMPRIMIDO'),
(452, 23, 'Contratiempos', 'ALMUERZO'),
(453, 24, 'Contratiempos', 'Cambio de Rasqueta'),
(454, 24, 'Contratiempos', 'Limpieza de Tambor'),
(455, 24, 'Contratiempos', 'Lavado de Anilox'),
(456, 24, 'Contratiempos', 'Levantamiento de Plancha'),
(457, 5, 'Contratiempos', 'ESPERA POR APROBACIÓN CALIDAD'),
(458, 6, 'Contratiempos', 'ESPERA POR APROBACIÓN CALIDAD'),
(459, 7, 'Contratiempos', 'ESPERA POR APROBACIÓN CALIDAD'),
(460, 8, 'Contratiempos', 'ESPERA POR APROBACIÓN CALIDAD'),
(461, 9, 'Contratiempos', 'ESPERA POR APROBACIÓN CALIDAD'),
(462, 10, 'Contratiempos', 'ESPERA POR APROBACIÓN CALIDAD'),
(463, 11, 'Contratiempos', 'ESPERA POR APROBACIÓN CALIDAD'),
(464, 12, 'ESPERA POR APROBACIÓN CALIDAD', 'Contratiempos'),
(465, 13, 'ESPERA POR APROBACIÓN CALIDAD', 'Contratiempos'),
(466, 14, 'Contratiempos', 'ESPERA POR APROBACIÓN CALIDAD'),
(467, 15, 'Contratiempos', 'ESPERA POR APROBACIÓN CALIDAD'),
(468, 16, 'Contratiempos', 'ESPERA POR APROBACIÓN CALIDAD'),
(469, 17, 'Contratiempos', 'ESPERA POR APROBACIÓN CALIDAD'),
(470, 18, 'Contratiempos', 'ESPERA POR APROBACIÓN CALIDAD'),
(471, 19, 'Contratiempos', 'ESPERA POR APROBACIÓN CALIDAD'),
(472, 20, 'Contratiempos', 'ESPERA POR APROBACIÓN CALIDAD'),
(473, 21, 'Contratiempos', 'ESPERA POR APROBACIÓN CALIDAD'),
(474, 22, 'Contratiempos', 'ESPERA POR APROBACIÓN CALIDAD'),
(475, 23, 'Contratiempos', 'ESPERA POR APROBACIÓN CALIDAD'),
(476, 24, 'Contratiempos', 'ESPERA POR APROBACIÓN CALIDAD'),
(477, 25, 'Contratiempos', 'ESPERA POR APROBACIÓN CALIDAD'),
(478, 26, 'Contratiempos', 'ESPERA POR APROBACIÓN CALIDAD'),
(480, 12, 'Contratiempos', 'ESPERA POR APROBACIÓN CALIDAD'),
(481, 13, 'Contratiempos', 'ESPERA POR APROBACIÓN CALIDAD'),
(536, 34, 'Preparación', 'PR-IMP1 CAMBIO DE PEDIDO'),
(537, 34, 'Preparación', 'PP-IMP2 LIMPIEZA OPERACION'),
(538, 34, 'Preparación', 'PR-05 Preparacion ajuste de maquina'),
(540, 34, 'Preparación', 'PP-IMP5 ENSAYOS DE LABORATORIIO'),
(541, 34, 'Contratiempos', 'PM-01 Produccion Mala'),
(542, 34, 'Contratiempos', 'PR-IMP6 CAMBIO DE COMPONENTES'),
(543, 34, 'Contratiempos', 'PP-01 Parada Caida Tension'),
(544, 34, 'Contratiempos', 'PP-IMP4 ERROR EN LA PROGRAMACIÓN'),
(545, 34, 'Contratiempos', 'PP-03 Parada Empate / Ruptura de films'),
(546, 34, 'Contratiempos', 'MT01 Parada mantenimiento correctivo electrico'),
(547, 34, 'Contratiempos', 'MT01 Parada mantenimiento correctivo mecanico'),
(548, 34, 'Contratiempos', 'MT04 Parada mantenimiento preventivo mecanico'),
(549, 34, 'Contratiempos', 'MT03 Parada mantenimiento preventivo electrico'),
(550, 34, 'Contratiempos', 'PP-04 Almuerzo/Break'),
(551, 34, 'Contratiempos', 'PP-IMP8 FALTA DE MATERIAL'),
(552, 34, 'Contratiempos', 'PP-IMP9 CAMBIO DE ROLLO'),
(553, 25, 'Contratiempos', 'Limpieza de máquina'),
(554, 27, 'Contratiempos', 'Cambio de Zipper'),
(556, 34, 'Contratiempos', 'Cambio de Zipper'),
(563, 27, 'Contratiempos', 'Revisión de Pauch'),
(564, 34, 'Contratiempos', 'Revisión de Pouch'),
(565, 27, 'Contratiempos', 'ESPERA POR APROBACIÓN CALIDAD'),
(566, 34, 'Contratiempos', 'ESPERA POR APROBACIÓN CALIDAD'),
(567, 26, 'Contratiempos', 'BOBINA FRONTAL TERMINADA'),
(568, 15, 'Contratiempos', 'Falta de personal'),
(569, 15, 'Contratiempos', 'Falta de aire'),
(570, 15, 'Contratiempos', 'Falla en la platificadora'),
(571, 8, 'Contratiempos', 'Falta de correa'),
(572, 9, 'Contratiempos', 'Falta de correa'),
(573, 15, 'Contratiempos', 'Falta de correa'),
(574, 8, 'Contratiempos', 'Problema de plato'),
(575, 9, 'Contratiempos', 'Problema de plato'),
(576, 15, 'Contratiempos', 'Problema de plato'),
(577, 8, 'Contratiempos', 'Falla de impresión'),
(578, 9, 'Contratiempos', 'Falla de impresión'),
(579, 15, 'Contratiempos', 'Falla de impresión'),
(580, 8, 'Contratiempos', 'Variación de corte'),
(581, 9, 'Contratiempos', 'Variación de corte'),
(582, 15, 'Contratiempos', 'Variación de corte'),
(583, 8, 'Contratiempos', 'En espera de mantenimiento'),
(584, 9, 'Contratiempos', 'En espera de mantenimiento'),
(585, 15, 'Contratiempos', 'En espera de mantenimiento'),
(586, 8, 'Contratiempos', 'Limpieza de rolo'),
(587, 9, 'Contratiempos', 'Limpieza de rolo'),
(588, 15, 'Contratiempos', 'Limpieza de rolo'),
(589, 8, 'Contratiempos', 'Falta de cuchilla'),
(590, 9, 'Contratiempos', 'Falta de cuchilla'),
(591, 15, 'Contratiempos', 'Falta de cuchilla'),
(592, 16, 'Contratiempos', 'Falta de correa '),
(593, 16, 'Contratiempos', 'Problema de plato'),
(594, 16, 'Contratiempos', 'Falla de impresión'),
(595, 16, 'Contratiempos', 'Variación de corte'),
(596, 16, 'Contratiempos', 'En espera de mantenimiento'),
(597, 16, 'Contratiempos', 'Limpieza de rolo'),
(598, 16, 'Contratiempos', 'Falta de cuchilla'),
(600, 34, 'Contratiempos', 'Problema de Laminación');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `produccion_final`
--

CREATE TABLE `produccion_final` (
  `id` int(11) NOT NULL,
  `registro_id` int(11) NOT NULL,
  `usuario_id` varchar(50) NOT NULL,
  `tipo_validacion` enum('produccion') NOT NULL,
  `comentario` text DEFAULT NULL,
  `fecha_validacion` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `registro`
--

CREATE TABLE `registro` (
  `id` int(11) NOT NULL,
  `tipo_boton` varchar(255) NOT NULL,
  `codigo_empleado` int(11) NOT NULL,
  `area_id` int(11) DEFAULT NULL,
  `maquina` int(11) NOT NULL,
  `item` varchar(255) NOT NULL,
  `jtWo` varchar(255) NOT NULL,
  `po` varchar(255) DEFAULT NULL,
  `cliente` varchar(255) DEFAULT NULL,
  `descripcion` varchar(255) NOT NULL,
  `cantidad_scrapt` decimal(10,2) NOT NULL,
  `cantidad_produccion` decimal(10,2) NOT NULL,
  `fecha_registro` timestamp NOT NULL DEFAULT current_timestamp(),
  `fecha_fin` datetime DEFAULT NULL,
  `comentario` varchar(255) NOT NULL,
  `validado_por` int(11) DEFAULT NULL,
  `estado_validacion` enum('Pendiente','Validado','Correccion','Retenido','Guardado') NOT NULL DEFAULT 'Pendiente'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `registro`
--

INSERT INTO `registro` (`id`, `tipo_boton`, `codigo_empleado`, `area_id`, `maquina`, `item`, `jtWo`, `po`, `cliente`, `descripcion`, `cantidad_scrapt`, `cantidad_produccion`, `fecha_registro`, `fecha_fin`, `comentario`, `validado_por`, `estado_validacion`) VALUES
(133, 'Producción', 0, 1, 1, 'V', 'V', NULL, NULL, 'Parcial', 0.00, 55.00, '2025-04-22 02:17:02', '2025-04-21 22:17:14', '', NULL, 'Pendiente'),
(134, 'Producción', 0, 1, 1, 'V', 'V', NULL, NULL, 'Parcial', 60.00, 0.00, '2025-04-22 02:17:02', '2025-04-21 22:17:03', '', NULL, 'Correccion'),
(135, 'Preparación', 0, 1, 1, 'V', 'V', NULL, NULL, 'Unknown', 0.00, 0.00, '2025-04-22 02:17:03', '2025-04-21 22:17:08', '', NULL, 'Pendiente'),
(136, 'final_produccion', 0, 1, 1, 'V', 'V', NULL, NULL, 'Unknown', 0.00, 0.00, '2025-04-22 02:17:08', '2025-04-21 22:17:08', '', NULL, 'Pendiente'),
(137, 'final_produccion', 0, 1, 1, 'rfgvb ', 'fdcsx', NULL, NULL, 'Unknown', 0.00, 3.00, '2025-04-22 02:17:19', '2025-04-21 22:17:19', '', NULL, 'Correccion'),
(138, 'final_produccion', 0, 1, 1, 'rfgvb ', 'fdcsx', NULL, NULL, 'Unknown', 44.00, 0.00, '2025-04-22 02:17:19', '2025-04-21 22:17:19', '', NULL, 'Correccion'),
(139, 'Producción', 0, 1, 1, 'gfvb ', 'frv', NULL, NULL, 'Parcial', 0.00, 34.00, '2025-04-22 02:17:31', '2025-04-22 12:00:53', '', NULL, 'Correccion'),
(140, 'Producción', 0, 1, 1, 'gfvb ', 'frv', NULL, NULL, 'Parcial', 33.00, 0.00, '2025-04-22 02:17:31', '2025-04-21 22:40:34', '', NULL, 'Correccion'),
(141, 'Producción', 0, 1, 1, 'gfvb ', 'frv', NULL, NULL, 'Parcial', 0.00, 555.00, '2025-04-22 02:40:34', '2025-04-21 22:40:46', '', NULL, 'Pendiente'),
(142, 'Producción', 0, 1, 1, 'gfvb ', 'frv', NULL, NULL, 'Parcial', 555555.00, 0.00, '2025-04-22 02:40:46', '2025-04-21 22:45:48', '', NULL, 'Pendiente'),
(143, 'Producción', 0, 1, 1, 'gfvb ', 'frv', NULL, NULL, 'Parcial', 0.00, 55.00, '2025-04-22 02:45:48', '2025-04-22 12:00:47', '', NULL, 'Pendiente'),
(144, 'Producción', 0, 1, 1, 'gfvb ', 'frv', NULL, NULL, 'Parcial', 669.00, 0.00, '2025-04-22 02:45:48', '2025-04-21 22:46:03', '', NULL, 'Pendiente'),
(145, 'Producción', 0, 1, 1, 'gfvb ', 'frv', NULL, NULL, 'Parcial', 0.00, 98855.00, '2025-04-22 02:46:03', '2025-04-22 12:00:37', '', NULL, 'Pendiente'),
(146, 'Producción', 0, 1, 1, 'gfvb ', 'frv', NULL, NULL, 'Parcial', 555.00, 0.00, '2025-04-22 02:46:03', '2025-04-22 11:47:02', '', NULL, 'Pendiente'),
(147, 'final_produccion', 0, 1, 1, 'gfvb ', 'frv', NULL, NULL, 'Unknown', 0.00, 0.00, '2025-04-22 15:47:02', '2025-04-22 11:47:02', '', NULL, 'Pendiente'),
(148, 'Preparación', 0, 1, 1, 'dfgvb', 'dfgb', NULL, NULL, 'Unknown', 0.00, 0.00, '2025-04-22 16:00:53', '2025-04-22 12:01:00', '', NULL, 'Pendiente'),
(149, 'final_produccion', 0, 1, 1, 'dfgvb', 'dfgb', NULL, NULL, 'Unknown', 0.00, 0.00, '2025-04-22 16:06:28', '2025-04-22 12:06:28', '', NULL, 'Pendiente'),
(150, 'Espera_trabajo', 0, 1, 1, '', '', NULL, NULL, '', 0.00, 0.00, '2025-04-22 16:06:31', '2025-04-22 12:09:21', '', NULL, 'Pendiente'),
(151, 'final_produccion', 0, 1, 1, 'gf', 'grf', NULL, NULL, 'Unknown', 0.00, 0.00, '2025-04-22 16:09:24', '2025-04-22 12:09:24', '', NULL, 'Pendiente'),
(152, 'Espera_trabajo', 0, 1, 1, '', '', NULL, NULL, '', 0.00, 0.00, '2025-04-22 16:09:27', '2025-04-22 13:57:06', '', NULL, 'Pendiente');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `retenciones`
--

CREATE TABLE `retenciones` (
  `id` int(11) NOT NULL,
  `registro_id` int(11) NOT NULL,
  `cantidad_total` decimal(10,2) NOT NULL,
  `cantidad_disponible` decimal(10,2) NOT NULL,
  `motivo` text NOT NULL,
  `estado` enum('activa','cerrada') NOT NULL DEFAULT 'activa',
  `usuario_id` int(11) NOT NULL,
  `fecha_creacion` datetime NOT NULL,
  `fecha_cierre` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `retencion_destinos`
--

CREATE TABLE `retencion_destinos` (
  `id` int(11) NOT NULL,
  `retencion_id` int(11) NOT NULL,
  `tipo_destino` enum('destruccion','retrabajo','produccion_final') NOT NULL,
  `cantidad` decimal(10,2) NOT NULL,
  `motivo` text DEFAULT NULL,
  `usuario_id` int(11) NOT NULL,
  `fecha_registro` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `scrap_final`
--

CREATE TABLE `scrap_final` (
  `id` int(11) NOT NULL,
  `registro_id` int(11) NOT NULL,
  `cantidad` decimal(10,2) NOT NULL,
  `observaciones` text DEFAULT NULL,
  `usuario_qa_id` int(11) NOT NULL,
  `fecha_validacion` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `solicitudes_correccion`
--

CREATE TABLE `solicitudes_correccion` (
  `id` int(11) NOT NULL,
  `registro_id` int(11) NOT NULL,
  `tipo_cantidad` enum('produccion','scrap') NOT NULL,
  `motivo` text NOT NULL,
  `estado` enum('Pendiente','Procesada','Cancelada') DEFAULT 'Pendiente',
  `qa_solicita_id` int(11) NOT NULL,
  `fecha_solicitud` datetime NOT NULL,
  `fecha_resolucion` datetime DEFAULT NULL,
  `cantidad_corregida` decimal(10,2) DEFAULT NULL,
  `comentario_operador` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `solicitudes_correccion`
--

INSERT INTO `solicitudes_correccion` (`id`, `registro_id`, `tipo_cantidad`, `motivo`, `estado`, `qa_solicita_id`, `fecha_solicitud`, `fecha_resolucion`, `cantidad_corregida`, `comentario_operador`) VALUES
(1, 140, 'scrap', '', 'Procesada', 1212, '2025-04-21 22:24:37', '2025-04-21 22:25:39', 33.00, ''),
(2, 139, 'produccion', '', 'Cancelada', 1212, '2025-04-21 22:24:40', '2025-04-21 22:25:12', NULL, NULL),
(3, 138, 'scrap', '', 'Procesada', 1212, '2025-04-21 22:24:54', '2025-04-21 22:25:23', 44.00, ''),
(4, 137, 'produccion', '', 'Cancelada', 1212, '2025-04-21 22:24:55', '2025-04-21 22:25:05', NULL, NULL),
(5, 140, 'scrap', '', 'Pendiente', 1212, '2025-04-21 22:25:51', NULL, NULL, NULL),
(6, 138, 'scrap', '', 'Pendiente', 1212, '2025-04-21 22:25:54', NULL, NULL, NULL),
(7, 134, 'scrap', '', 'Pendiente', 1212, '2025-04-21 22:25:56', NULL, NULL, NULL),
(8, 137, 'produccion', '', 'Cancelada', 33, '2025-04-21 22:25:58', '2025-04-21 22:26:18', NULL, NULL),
(9, 139, 'produccion', '', 'Pendiente', 1212, '2025-04-21 22:26:00', NULL, NULL, NULL),
(10, 137, 'produccion', '', 'Pendiente', 1212, '2025-04-21 22:28:04', NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `nombre` varchar(200) NOT NULL,
  `codigo_empleado` int(11) NOT NULL,
  `password` varchar(255) NOT NULL,
  `tipo_usuario` enum('operador','supervisor','qa') NOT NULL,
  `area_id` int(11) DEFAULT NULL,
  `maquina_id` int(11) DEFAULT NULL,
  `jtWo` varchar(255) DEFAULT NULL,
  `item` varchar(255) DEFAULT NULL,
  `active_button_id` varchar(50) DEFAULT 'defaultButtonId',
  `po` varchar(255) DEFAULT NULL,
  `cliente` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `users`
--

INSERT INTO `users` (`id`, `nombre`, `codigo_empleado`, `password`, `tipo_usuario`, `area_id`, `maquina_id`, `jtWo`, `item`, `active_button_id`, `po`, `cliente`) VALUES
(1, '00', 0, '$2y$10$h71TJLs7aNSHhIY5JGy/VOZigZ6OooeXpzkArNteQTQ7mBaIs0Ihy', 'operador', 1, 1, 'ddf', 'ff', 'Espera_trabajo', 'ff', 'ff'),
(2, '1212', 1212, '$2y$10$2xH3c2gSvckNjePuUWpaieD3s8TJPhFDt4QFc6riWkYK6IjVzSr2S', 'qa', 1, NULL, NULL, NULL, 'defaultButtonId', NULL, NULL),
(3, '33', 33, '$2y$10$nr/6WGDmqOuZs.CXXQyw..iDmbnQ.8sItYPGLq/MPZKHMiePFKOu2', 'qa', 1, NULL, NULL, NULL, 'defaultButtonId', NULL, NULL),
(4, '44', 44, '$2y$10$HU7rzmscWjWUFuq0QIhxCOQSqldY3s7rlC3L/BpTmRoq2aRKT.lJC', 'qa', 1, NULL, NULL, NULL, 'defaultButtonId', NULL, NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `velocidad`
--

CREATE TABLE `velocidad` (
  `id` int(11) NOT NULL,
  `maquina` int(11) NOT NULL,
  `jtWo` varchar(50) NOT NULL,
  `item` varchar(255) NOT NULL,
  `velocidad_produccion` decimal(10,2) NOT NULL,
  `fecha_registro` timestamp NOT NULL DEFAULT current_timestamp(),
  `area_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `area`
--
ALTER TABLE `area`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `maquinas`
--
ALTER TABLE `maquinas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_maquinas_area` (`area_id`);

--
-- Indices de la tabla `notificaciones`
--
ALTER TABLE `notificaciones`
  ADD PRIMARY KEY (`id`),
  ADD KEY `area` (`area_id`);

--
-- Indices de la tabla `operacion`
--
ALTER TABLE `operacion`
  ADD PRIMARY KEY (`id`),
  ADD KEY `maquina_id` (`maquina_id`);

--
-- Indices de la tabla `produccion_final`
--
ALTER TABLE `produccion_final`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_produccion_registro` (`registro_id`);

--
-- Indices de la tabla `registro`
--
ALTER TABLE `registro`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_registro_area` (`area_id`),
  ADD KEY `codigo_empleado` (`codigo_empleado`),
  ADD KEY `maquina` (`maquina`),
  ADD KEY `idx_empleado_maquina_item_jtwo` (`codigo_empleado`,`maquina`,`item`,`jtWo`),
  ADD KEY `idx_registro_item_jtwo` (`item`,`jtWo`),
  ADD KEY `idx_registro_tipo_description` (`tipo_boton`(100),`descripcion`(100)),
  ADD KEY `idx_registro_employee_machine` (`codigo_empleado`,`maquina`,`fecha_registro`),
  ADD KEY `validado_por` (`validado_por`),
  ADD KEY `validado_por_2` (`validado_por`);
ALTER TABLE `registro` ADD FULLTEXT KEY `ft_registro_comentario` (`comentario`);

--
-- Indices de la tabla `retenciones`
--
ALTER TABLE `retenciones`
  ADD PRIMARY KEY (`id`),
  ADD KEY `registro_id` (`registro_id`),
  ADD KEY `usuario_id` (`usuario_id`);

--
-- Indices de la tabla `retencion_destinos`
--
ALTER TABLE `retencion_destinos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `retencion_id` (`retencion_id`),
  ADD KEY `usuario_id` (`usuario_id`);

--
-- Indices de la tabla `scrap_final`
--
ALTER TABLE `scrap_final`
  ADD PRIMARY KEY (`id`),
  ADD KEY `registro_id` (`registro_id`),
  ADD KEY `usuario_qa_id` (`usuario_qa_id`);

--
-- Indices de la tabla `solicitudes_correccion`
--
ALTER TABLE `solicitudes_correccion`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_solicitud_registro` (`registro_id`),
  ADD KEY `fk_solicitud_qa` (`qa_solicita_id`);

--
-- Indices de la tabla `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `codigo_empleado` (`codigo_empleado`),
  ADD KEY `maquina` (`maquina_id`),
  ADD KEY `area_id` (`area_id`),
  ADD KEY `idx_users_jtwo` (`jtWo`);

--
-- Indices de la tabla `velocidad`
--
ALTER TABLE `velocidad`
  ADD PRIMARY KEY (`id`),
  ADD KEY `area_id` (`area_id`),
  ADD KEY `maquina` (`maquina`),
  ADD KEY `idx_velocidad_jtwo` (`jtWo`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `area`
--
ALTER TABLE `area`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de la tabla `maquinas`
--
ALTER TABLE `maquinas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=55;

--
-- AUTO_INCREMENT de la tabla `notificaciones`
--
ALTER TABLE `notificaciones`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT de la tabla `operacion`
--
ALTER TABLE `operacion`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=601;

--
-- AUTO_INCREMENT de la tabla `produccion_final`
--
ALTER TABLE `produccion_final`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `registro`
--
ALTER TABLE `registro`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=153;

--
-- AUTO_INCREMENT de la tabla `retenciones`
--
ALTER TABLE `retenciones`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT de la tabla `retencion_destinos`
--
ALTER TABLE `retencion_destinos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `scrap_final`
--
ALTER TABLE `scrap_final`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `solicitudes_correccion`
--
ALTER TABLE `solicitudes_correccion`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT de la tabla `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `velocidad`
--
ALTER TABLE `velocidad`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `maquinas`
--
ALTER TABLE `maquinas`
  ADD CONSTRAINT `fk_maquinas_area` FOREIGN KEY (`area_id`) REFERENCES `area` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `notificaciones`
--
ALTER TABLE `notificaciones`
  ADD CONSTRAINT `notificaciones_ibfk_1` FOREIGN KEY (`area_id`) REFERENCES `area` (`id`);

--
-- Filtros para la tabla `operacion`
--
ALTER TABLE `operacion`
  ADD CONSTRAINT `operacion_ibfk_1` FOREIGN KEY (`maquina_id`) REFERENCES `maquinas` (`id`);

--
-- Filtros para la tabla `produccion_final`
--
ALTER TABLE `produccion_final`
  ADD CONSTRAINT `fk_produccion_registro` FOREIGN KEY (`registro_id`) REFERENCES `registro` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `registro`
--
ALTER TABLE `registro`
  ADD CONSTRAINT `fk_registro_area` FOREIGN KEY (`area_id`) REFERENCES `area` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_registro_codigo_empleado` FOREIGN KEY (`codigo_empleado`) REFERENCES `users` (`codigo_empleado`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_registro_maquina` FOREIGN KEY (`maquina`) REFERENCES `maquinas` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `registro_ibfk_1` FOREIGN KEY (`validado_por`) REFERENCES `users` (`codigo_empleado`);

--
-- Filtros para la tabla `retenciones`
--
ALTER TABLE `retenciones`
  ADD CONSTRAINT `retenciones_ibfk_1` FOREIGN KEY (`registro_id`) REFERENCES `registro` (`id`),
  ADD CONSTRAINT `retenciones_ibfk_2` FOREIGN KEY (`usuario_id`) REFERENCES `users` (`codigo_empleado`);

--
-- Filtros para la tabla `retencion_destinos`
--
ALTER TABLE `retencion_destinos`
  ADD CONSTRAINT `retencion_destinos_ibfk_1` FOREIGN KEY (`retencion_id`) REFERENCES `retenciones` (`id`),
  ADD CONSTRAINT `retencion_destinos_ibfk_2` FOREIGN KEY (`usuario_id`) REFERENCES `users` (`codigo_empleado`);

--
-- Filtros para la tabla `scrap_final`
--
ALTER TABLE `scrap_final`
  ADD CONSTRAINT `scrap_final_ibfk_1` FOREIGN KEY (`registro_id`) REFERENCES `registro` (`id`),
  ADD CONSTRAINT `scrap_final_ibfk_2` FOREIGN KEY (`usuario_qa_id`) REFERENCES `users` (`codigo_empleado`);

--
-- Filtros para la tabla `solicitudes_correccion`
--
ALTER TABLE `solicitudes_correccion`
  ADD CONSTRAINT `fk_solicitud_qa` FOREIGN KEY (`qa_solicita_id`) REFERENCES `users` (`codigo_empleado`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_solicitud_registro` FOREIGN KEY (`registro_id`) REFERENCES `registro` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `fk_users_maquina` FOREIGN KEY (`maquina_id`) REFERENCES `maquinas` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `users_ibfk_1` FOREIGN KEY (`area_id`) REFERENCES `area` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `velocidad`
--
ALTER TABLE `velocidad`
  ADD CONSTRAINT `velocidad_ibfk_1` FOREIGN KEY (`area_id`) REFERENCES `area` (`id`),
  ADD CONSTRAINT `velocidad_ibfk_2` FOREIGN KEY (`maquina`) REFERENCES `maquinas` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
