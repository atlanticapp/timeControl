-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 02-04-2025 a las 23:53:48
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
-- Base de datos: `contro`
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
-- Estructura de tabla para la tabla `confirmaciones_operacion`
--

CREATE TABLE `confirmaciones_operacion` (
  `item` varchar(200) NOT NULL,
  `jtWo` varchar(200) NOT NULL,
  `scraptConfirm` varchar(200) NOT NULL,
  `produccion_confirm` varchar(200) NOT NULL,
  `diferencia` varchar(200) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
(1, 'info', 'Se ha registrado una nueva cantidad de producción o scrap en la máquina 1.', 1, '2025-03-31 16:57:14', 'leído'),
(2, 'info', 'Se ha registrado una nueva cantidad de producción o scrap en la máquina 1.', 1, '2025-03-31 16:59:30', 'leído'),
(3, 'info', 'Se ha registrado una nueva cantidad de producción o scrap en la máquina 1.', 1, '2025-03-31 16:59:57', 'leído'),
(4, 'info', 'Se ha registrado una nueva cantidad de producción o scrap en la máquina 1.', 1, '2025-03-31 17:00:45', 'leído'),
(5, 'info', 'Se ha registrado una nueva cantidad de producción o scrap en la máquina 1.', 1, '2025-03-31 17:09:54', 'leído'),
(6, 'info', 'Se ha registrado una nueva cantidad de producción o scrap en la máquina 1.', 1, '2025-03-31 17:11:47', 'leído'),
(7, 'info', 'Se ha registrado una nueva cantidad de producción o scrap en la máquina 1.', 1, '2025-03-31 17:26:03', 'leído'),
(8, 'info', 'Se ha registrado una nueva cantidad de producción o scrap en la máquina 1.', 1, '2025-03-31 17:40:51', 'leído'),
(9, 'info', 'Se ha registrado una nueva cantidad de producción o scrap en la máquina 1.', 1, '2025-03-31 17:40:57', 'leído'),
(10, 'info', 'Se ha registrado una nueva cantidad de producción o scrap en la máquina 1.', 1, '2025-03-31 17:45:33', 'leído'),
(11, 'info', 'Se ha registrado una nueva cantidad de producción o scrap en la máquina 1.', 1, '2025-03-31 17:52:17', 'leído'),
(12, 'info', 'Se ha registrado una nueva cantidad de producción o scrap en la máquina 1.', 1, '2025-03-31 17:52:31', 'leído'),
(13, 'info', 'Se ha registrado una nueva cantidad de producción o scrap en la máquina 1.', 1, '2025-03-31 17:56:42', 'leído'),
(14, 'info', 'Se ha registrado una nueva cantidad de producción o scrap en la máquina 1.', 1, '2025-03-31 17:57:53', 'leído'),
(15, 'info', 'Se ha registrado una nueva cantidad de producción o scrap en la máquina 1.', 1, '2025-03-31 17:58:39', 'leído'),
(16, 'info', 'Se ha registrado una nueva cantidad de producción o scrap en la máquina 1.', 1, '2025-03-31 17:59:49', 'leído'),
(17, 'info', 'Se ha registrado una nueva cantidad de producción o scrap en la máquina 1.', 1, '2025-03-31 18:05:32', 'leído'),
(18, 'info', 'Se ha registrado una nueva cantidad de producción o scrap en la máquina 1.', 1, '2025-03-31 18:05:39', 'leído'),
(19, 'info', 'Se ha registrado una nueva cantidad de producción o scrap en la máquina 1.', 1, '2025-03-31 18:11:00', 'leído'),
(20, 'info', 'Se ha registrado una nueva cantidad de producción o scrap en la máquina 1.', 1, '2025-03-31 18:11:07', 'leído'),
(21, 'info', 'Se ha registrado una nueva cantidad de producción o scrap en la máquina 1.', 1, '2025-03-31 18:11:16', 'leído'),
(22, 'info', 'Se ha registrado una nueva cantidad de producción o scrap en la máquina 1.', 1, '2025-03-31 18:11:20', 'leído'),
(23, 'info', 'Se ha registrado una nueva cantidad de producción o scrap en la máquina 1.', 1, '2025-03-31 18:11:25', 'leído'),
(24, 'info', 'Se ha registrado una nueva cantidad de producción o scrap en la máquina 1.', 1, '2025-03-31 18:11:33', 'leído'),
(25, 'info', 'Se ha registrado una nueva cantidad de producción o scrap en la máquina 1.', 1, '2025-03-31 18:11:39', 'leído'),
(26, 'info', 'Se ha registrado una nueva cantidad de producción o scrap en la máquina 1.', 1, '2025-03-31 18:11:47', 'leído'),
(27, 'info', 'Se ha registrado una nueva cantidad de producción o scrap en la máquina 1.', 1, '2025-03-31 18:18:29', 'leído'),
(28, 'info', 'Se ha registrado una nueva cantidad de producción o scrap en la máquina 1.', 1, '2025-03-31 18:19:11', 'leído'),
(29, 'info', 'Se ha registrado una nueva cantidad de producción o scrap en la máquina 1.', 1, '2025-03-31 18:19:21', 'leído'),
(30, 'info', 'Se ha registrado una nueva cantidad de producción o scrap en la máquina 1.', 1, '2025-03-31 18:22:25', 'leído'),
(31, 'info', 'Se ha registrado una nueva cantidad de producción o scrap en la máquina 1.', 1, '2025-03-31 18:22:37', 'leído'),
(32, 'info', 'Se ha registrado una nueva cantidad de producción o scrap en la máquina 1.', 1, '2025-03-31 18:25:46', 'leído'),
(33, 'info', 'Se ha registrado una nueva cantidad de producción o scrap en la máquina 1.', 1, '2025-03-31 18:28:25', 'leído'),
(34, 'info', 'Se ha registrado una nueva cantidad de producción o scrap en la máquina 1.', 1, '2025-03-31 18:28:32', 'leído'),
(35, 'info', 'Se ha registrado una nueva cantidad de producción o scrap en la máquina 1.', 1, '2025-03-31 18:29:17', 'leído'),
(36, 'info', 'Se ha registrado una nueva cantidad de producción o scrap en la máquina 1.', 1, '2025-03-31 18:30:25', 'leído'),
(37, 'info', 'Se ha registrado una nueva cantidad de producción o scrap en la máquina 1.', 1, '2025-03-31 18:30:46', 'leído'),
(38, 'info', 'Se ha registrado una nueva cantidad de producción o scrap en la máquina 1.', 1, '2025-03-31 18:30:58', 'leído'),
(39, 'info', 'Se ha registrado una nueva cantidad de producción o scrap en la máquina 1.', 1, '2025-03-31 18:31:34', 'leído'),
(40, 'info', 'Se ha registrado una nueva cantidad de producción o scrap en la máquina 1.', 1, '2025-03-31 18:32:09', 'leído'),
(41, 'info', 'Se ha registrado una nueva cantidad de producción o scrap en la máquina 1.', 1, '2025-03-31 18:32:14', 'leído'),
(42, 'info', 'Se ha registrado una nueva cantidad de producción o scrap en la máquina 1.', 1, '2025-03-31 18:32:19', 'leído');

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
-- Estructura de tabla para la tabla `registro`
--

CREATE TABLE `registro` (
  `id` int(11) NOT NULL,
  `tipo_boton` varchar(255) NOT NULL,
  `codigo_empleado` int(11) NOT NULL,
  `item` varchar(255) NOT NULL,
  `maquina` int(11) NOT NULL,
  `area_id` int(11) DEFAULT NULL,
  `descripcion` varchar(255) NOT NULL,
  `jtWo` varchar(255) NOT NULL,
  `cantidad_scrapt` decimal(10,2) NOT NULL,
  `cantidad_validada_scrapt` decimal(10,2) NOT NULL DEFAULT 0.00,
  `cantidad_produccion` decimal(10,2) NOT NULL,
  `cantidad_validada_produccion` decimal(10,2) NOT NULL DEFAULT 0.00,
  `fecha_registro` timestamp NOT NULL DEFAULT current_timestamp(),
  `fecha_fin` datetime DEFAULT NULL,
  `comentario` varchar(255) NOT NULL,
  `estado_validacion` enum('Pendiente','Validado','Corregir') DEFAULT NULL,
  `validado_por` int(11) DEFAULT NULL,
  `fecha_validacion` datetime DEFAULT NULL,
  `comentario_qa` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `registro`
--

INSERT INTO `registro` (`id`, `tipo_boton`, `codigo_empleado`, `item`, `maquina`, `area_id`, `descripcion`, `jtWo`, `cantidad_scrapt`, `cantidad_validada_scrapt`, `cantidad_produccion`, `cantidad_validada_produccion`, `fecha_registro`, `fecha_fin`, `comentario`, `estado_validacion`, `validado_por`, `fecha_validacion`, `comentario_qa`) VALUES
(1, 'Producción', 0, 'gjhj', 1, 1, 'Parcial', 'ytujh', 0.00, 0.00, 40000.00, 0.00, '2025-03-28 22:58:02', NULL, '', 'Pendiente', NULL, NULL, NULL),
(2, 'Producción', 0, 'gjhj', 1, 1, 'Parcial', 'ytujh', 55.00, 0.00, 0.00, 0.00, '2025-03-28 22:58:02', '2025-03-28 18:58:43', '', 'Pendiente', NULL, NULL, NULL),
(3, 'Producción', 0, 'gjhj', 1, 1, 'Parcial', 'ytujh', 0.00, 0.00, 445.00, 0.00, '2025-03-28 22:58:43', NULL, '', 'Validado', 1212, '2025-03-31 15:24:39', NULL),
(4, 'Producción', 0, 'gjhj', 1, 1, 'Parcial', 'ytujh', 55.00, 0.00, 0.00, 0.00, '2025-03-28 22:58:43', '2025-03-31 15:46:11', '', 'Validado', 1212, '2025-03-31 15:24:43', NULL),
(5, 'Producción', 0, 'gjhj', 1, 1, 'Parcial', 'ytujh', 0.00, 0.00, 33.00, 0.00, '2025-03-31 19:46:11', NULL, '', 'Pendiente', NULL, NULL, NULL),
(6, 'Producción', 0, 'gjhj', 1, 1, 'Parcial', 'ytujh', 33.00, 0.00, 0.00, 0.00, '2025-03-31 19:46:11', '2025-03-31 16:57:13', '', 'Pendiente', NULL, NULL, NULL),
(7, 'Producción', 0, 'gjhj', 1, 1, 'Parcial', 'ytujh', 0.00, 0.00, 33.00, 0.00, '2025-03-31 20:57:13', NULL, '', 'Validado', 1212, '2025-03-31 16:57:52', NULL),
(8, 'Producción', 0, 'gjhj', 1, 1, 'Parcial', 'ytujh', 33.00, 0.00, 0.00, 0.00, '2025-03-31 20:57:13', '2025-03-31 16:59:30', '', 'Validado', 1212, '2025-03-31 16:57:56', NULL),
(9, 'Producción', 0, 'gjhj', 1, 1, 'Parcial', 'ytujh', 0.00, 0.00, 33.00, 0.00, '2025-03-31 20:59:30', NULL, '', 'Pendiente', NULL, NULL, NULL),
(10, 'Producción', 0, 'gjhj', 1, 1, 'Parcial', 'ytujh', 3344.00, 0.00, 0.00, 0.00, '2025-03-31 20:59:30', '2025-03-31 16:59:57', '', 'Pendiente', NULL, NULL, NULL),
(11, 'Producción', 0, 'gjhj', 1, 1, 'Parcial', 'ytujh', 0.00, 0.00, 33.00, 0.00, '2025-03-31 20:59:57', NULL, '', 'Pendiente', NULL, NULL, NULL),
(12, 'Producción', 0, 'gjhj', 1, 1, 'Parcial', 'ytujh', 33.00, 0.00, 0.00, 0.00, '2025-03-31 20:59:57', '2025-03-31 17:00:45', '', 'Pendiente', NULL, NULL, NULL),
(13, 'Producción', 0, 'gjhj', 1, 1, 'Parcial', 'ytujh', 0.00, 0.00, 33.00, 0.00, '2025-03-31 21:00:45', NULL, '', 'Pendiente', NULL, NULL, NULL),
(14, 'Producción', 0, 'gjhj', 1, 1, 'Parcial', 'ytujh', 33.00, 0.00, 0.00, 0.00, '2025-03-31 21:00:45', '2025-03-31 17:09:54', '', 'Pendiente', NULL, NULL, NULL),
(15, 'Producción', 0, 'gjhj', 1, 1, 'Parcial', 'ytujh', 0.00, 0.00, 88.00, 0.00, '2025-03-31 21:09:54', NULL, '', 'Pendiente', NULL, NULL, NULL),
(16, 'Producción', 0, 'gjhj', 1, 1, 'Parcial', 'ytujh', 88.00, 0.00, 0.00, 0.00, '2025-03-31 21:09:54', '2025-03-31 17:11:47', '', 'Pendiente', NULL, NULL, NULL),
(17, 'Producción', 0, 'gjhj', 1, 1, 'Parcial', 'ytujh', 0.00, 0.00, 88.00, 0.00, '2025-03-31 21:11:47', '2025-03-31 17:26:03', '', 'Pendiente', NULL, NULL, NULL),
(18, 'Producción', 0, 'gjhj', 1, 1, 'Parcial', 'ytujh', 0.00, 0.00, 85.00, 0.00, '2025-03-31 21:26:03', NULL, '', 'Pendiente', NULL, NULL, NULL),
(19, 'Producción', 0, 'gjhj', 1, 1, 'Parcial', 'ytujh', 85.00, 0.00, 0.00, 0.00, '2025-03-31 21:26:03', '2025-03-31 17:40:51', '', 'Pendiente', NULL, NULL, NULL),
(20, 'Preparación', 0, 'gjhj', 1, 1, 'Unknown', 'ytujh', 0.00, 0.00, 0.00, 0.00, '2025-03-31 21:40:51', '2025-03-31 17:40:57', '', NULL, NULL, NULL, NULL),
(21, 'Producción', 0, 'gjhj', 1, 1, 'Parcial', 'ytujh', 0.00, 0.00, 88.00, 0.00, '2025-03-31 21:40:57', '2025-03-31 17:45:33', '', 'Pendiente', NULL, NULL, NULL),
(22, 'Producción', 0, 'gjhj', 1, 1, 'Parcial', 'ytujh', 0.00, 0.00, 88.00, 0.00, '2025-03-31 21:45:33', NULL, '', 'Pendiente', NULL, NULL, NULL),
(23, 'Producción', 0, 'gjhj', 1, 1, 'Parcial', 'ytujh', 88.00, 0.00, 0.00, 0.00, '2025-03-31 21:45:33', '2025-03-31 17:52:17', '', 'Pendiente', NULL, NULL, NULL),
(24, 'Producción', 0, 'gjhj', 1, 1, 'Parcial', 'ytujh', 0.00, 0.00, 88.00, 0.00, '2025-03-31 21:52:17', '2025-03-31 17:52:31', '', 'Pendiente', NULL, NULL, NULL),
(25, 'Producción', 0, 'gjhj', 1, 1, 'Parcial', 'ytujh', 0.00, 0.00, 8585.00, 0.00, '2025-03-31 21:52:31', NULL, '', 'Pendiente', NULL, NULL, NULL),
(26, 'Producción', 0, 'gjhj', 1, 1, 'Parcial', 'ytujh', 88.00, 0.00, 0.00, 0.00, '2025-03-31 21:52:31', '2025-03-31 17:56:42', '', 'Pendiente', NULL, NULL, NULL),
(27, 'Producción', 0, 'gjhj', 1, 1, 'Parcial', 'ytujh', 0.00, 0.00, 88.00, 0.00, '2025-03-31 21:56:42', NULL, '', 'Pendiente', NULL, NULL, NULL),
(28, 'Producción', 0, 'gjhj', 1, 1, 'Parcial', 'ytujh', 8.00, 0.00, 0.00, 0.00, '2025-03-31 21:56:42', '2025-03-31 17:57:53', '', 'Pendiente', NULL, NULL, NULL),
(29, 'Producción', 0, 'gjhj', 1, 1, 'Parcial', 'ytujh', 0.00, 0.00, 88.00, 0.00, '2025-03-31 21:57:53', '2025-03-31 17:58:39', '', 'Pendiente', NULL, NULL, NULL),
(30, 'Producción', 0, 'gjhj', 1, 1, 'Parcial', 'ytujh', 0.00, 0.00, 85.00, 0.00, '2025-03-31 21:58:39', NULL, '', 'Pendiente', NULL, NULL, NULL),
(31, 'Producción', 0, 'gjhj', 1, 1, 'Parcial', 'ytujh', 88.00, 0.00, 0.00, 0.00, '2025-03-31 21:58:39', '2025-03-31 17:59:49', '', 'Pendiente', NULL, NULL, NULL),
(32, 'Producción', 0, 'gjhj', 1, 1, 'Parcial', 'ytujh', 0.00, 0.00, 88.00, 0.00, '2025-03-31 21:59:49', NULL, '', 'Pendiente', NULL, NULL, NULL),
(33, 'Producción', 0, 'gjhj', 1, 1, 'Parcial', 'ytujh', 8.00, 0.00, 0.00, 0.00, '2025-03-31 21:59:49', '2025-03-31 18:05:32', '', 'Pendiente', NULL, NULL, NULL),
(34, 'Producción', 0, 'gjhj', 1, 1, 'Parcial', 'ytujh', 0.00, 0.00, 88.00, 0.00, '2025-03-31 22:05:32', '2025-03-31 18:05:39', '', 'Pendiente', NULL, NULL, NULL),
(35, 'Producción', 0, 'gjhj', 1, 1, 'Parcial', 'ytujh', 0.00, 0.00, 88.00, 0.00, '2025-03-31 22:05:39', NULL, '', 'Pendiente', NULL, NULL, NULL),
(36, 'Producción', 0, 'gjhj', 1, 1, 'Parcial', 'ytujh', 8.00, 0.00, 0.00, 0.00, '2025-03-31 22:05:39', '2025-03-31 18:11:00', '', 'Pendiente', NULL, NULL, NULL),
(37, 'Producción', 0, 'gjhj', 1, 1, 'Parcial', 'ytujh', 0.00, 0.00, 8.00, 0.00, '2025-03-31 22:11:00', NULL, '', 'Pendiente', NULL, NULL, NULL),
(38, 'Producción', 0, 'gjhj', 1, 1, 'Parcial', 'ytujh', 8.00, 0.00, 0.00, 0.00, '2025-03-31 22:11:00', '2025-03-31 18:11:07', '', 'Pendiente', NULL, NULL, NULL),
(39, 'Producción', 0, 'gjhj', 1, 1, 'Parcial', 'ytujh', 0.00, 0.00, 99.00, 0.00, '2025-03-31 22:11:07', NULL, '', 'Pendiente', NULL, NULL, NULL),
(40, 'Producción', 0, 'gjhj', 1, 1, 'Parcial', 'ytujh', 88.00, 0.00, 0.00, 0.00, '2025-03-31 22:11:07', '2025-03-31 18:11:16', '', 'Pendiente', NULL, NULL, NULL),
(41, 'Producción', 0, 'gjhj', 1, 1, 'Parcial', 'ytujh', 0.00, 0.00, 8.00, 0.00, '2025-03-31 22:11:16', NULL, '', 'Pendiente', NULL, NULL, NULL),
(42, 'Producción', 0, 'gjhj', 1, 1, 'Parcial', 'ytujh', 5.00, 0.00, 0.00, 0.00, '2025-03-31 22:11:16', '2025-03-31 18:11:20', '', 'Pendiente', NULL, NULL, NULL),
(43, 'Producción', 0, 'gjhj', 1, 1, 'Parcial', 'ytujh', 8.00, 0.00, 0.00, 0.00, '2025-03-31 22:11:20', '2025-03-31 18:11:25', '', 'Pendiente', NULL, NULL, NULL),
(44, 'Preparación', 0, 'gjhj', 1, 1, 'Unknown', 'ytujh', 0.00, 0.00, 0.00, 0.00, '2025-03-31 22:11:25', '2025-03-31 18:11:33', '', NULL, NULL, NULL, NULL),
(45, 'Producción', 0, 'gjhj', 1, 1, 'Parcial', 'ytujh', 0.00, 0.00, 8.00, 0.00, '2025-03-31 22:11:33', NULL, '', 'Pendiente', NULL, NULL, NULL),
(46, 'Producción', 0, 'gjhj', 1, 1, 'Parcial', 'ytujh', 8.00, 0.00, 0.00, 0.00, '2025-03-31 22:11:33', '2025-03-31 18:11:39', '', 'Pendiente', NULL, NULL, NULL),
(47, 'Contratiempos', 0, 'gjhj', 1, 1, 'ESPERA DE TINTAS EN PRENSA', 'ytujh', 0.00, 0.00, 0.00, 0.00, '2025-03-31 22:11:39', '2025-03-31 18:11:47', '', NULL, NULL, NULL, NULL),
(48, 'Producción', 0, 'gjhj', 1, 1, 'Parcial', 'ytujh', 0.00, 0.00, 8.00, 0.00, '2025-03-31 22:11:47', '2025-03-31 18:18:29', '', 'Pendiente', NULL, NULL, NULL),
(49, 'Producción', 0, 'gjhj', 1, 1, 'Parcial', 'ytujh', 0.00, 0.00, 88.00, 0.00, '2025-03-31 22:18:29', NULL, '', 'Pendiente', NULL, NULL, NULL),
(50, 'Producción', 0, 'gjhj', 1, 1, 'Parcial', 'ytujh', 8.00, 0.00, 0.00, 0.00, '2025-03-31 22:18:29', '2025-03-31 18:19:11', '', 'Pendiente', NULL, NULL, NULL),
(51, 'Producción', 0, 'gjhj', 1, 1, 'Parcial', 'ytujh', 0.00, 0.00, 88.00, 0.00, '2025-03-31 22:19:11', NULL, '', 'Pendiente', NULL, NULL, NULL),
(52, 'Producción', 0, 'gjhj', 1, 1, 'Parcial', 'ytujh', 8.00, 0.00, 0.00, 0.00, '2025-03-31 22:19:11', '2025-03-31 18:19:21', '', 'Pendiente', NULL, NULL, NULL),
(53, 'Producción', 0, 'gjhj', 1, 1, 'Parcial', 'ytujh', 0.00, 0.00, 8.00, 0.00, '2025-03-31 22:19:21', NULL, '', 'Pendiente', NULL, NULL, NULL),
(54, 'Producción', 0, 'gjhj', 1, 1, 'Parcial', 'ytujh', 9.00, 0.00, 0.00, 0.00, '2025-03-31 22:19:21', '2025-03-31 18:22:25', '', 'Pendiente', NULL, NULL, NULL),
(55, 'Producción', 0, 'gjhj', 1, 1, 'Parcial', 'ytujh', 0.00, 0.00, 8.00, 0.00, '2025-03-31 22:22:25', NULL, '', 'Pendiente', NULL, NULL, NULL),
(56, 'Producción', 0, 'gjhj', 1, 1, 'Parcial', 'ytujh', 8.00, 0.00, 0.00, 0.00, '2025-03-31 22:22:25', '2025-03-31 18:22:37', '', 'Pendiente', NULL, NULL, NULL),
(57, 'Producción', 0, 'gjhj', 1, 1, 'Parcial', 'ytujh', 0.00, 0.00, 8.00, 0.00, '2025-03-31 22:22:37', NULL, '', 'Pendiente', NULL, NULL, NULL),
(58, 'Producción', 0, 'gjhj', 1, 1, 'Parcial', 'ytujh', 8.00, 0.00, 0.00, 0.00, '2025-03-31 22:22:37', '2025-03-31 18:25:46', '', 'Pendiente', NULL, NULL, NULL),
(59, 'Producción', 0, 'gjhj', 1, 1, 'Parcial', 'ytujh', 0.00, 0.00, 88.00, 0.00, '2025-03-31 22:25:46', NULL, '', 'Pendiente', NULL, NULL, NULL),
(60, 'Producción', 0, 'gjhj', 1, 1, 'Parcial', 'ytujh', 8.00, 0.00, 0.00, 0.00, '2025-03-31 22:25:46', '2025-03-31 18:28:25', '', 'Pendiente', NULL, NULL, NULL),
(61, 'Producción', 0, 'gjhj', 1, 1, 'Parcial', 'ytujh', 0.00, 0.00, 88.00, 0.00, '2025-03-31 22:28:25', NULL, '', 'Pendiente', NULL, NULL, NULL),
(62, 'Producción', 0, 'gjhj', 1, 1, 'Parcial', 'ytujh', 8.00, 0.00, 0.00, 0.00, '2025-03-31 22:28:25', '2025-03-31 18:28:32', '', 'Pendiente', NULL, NULL, NULL),
(63, 'Producción', 0, 'gjhj', 1, 1, 'Parcial', 'ytujh', 0.00, 0.00, 8.00, 0.00, '2025-03-31 22:28:32', NULL, '', 'Pendiente', NULL, NULL, NULL),
(64, 'Producción', 0, 'gjhj', 1, 1, 'Parcial', 'ytujh', 5.00, 0.00, 0.00, 0.00, '2025-03-31 22:28:32', '2025-03-31 18:29:17', '', 'Pendiente', NULL, NULL, NULL),
(65, 'Producción', 0, 'gjhj', 1, 1, 'Parcial', 'ytujh', 0.00, 0.00, 8.00, 0.00, '2025-03-31 22:29:17', NULL, '', 'Pendiente', NULL, NULL, NULL),
(66, 'Producción', 0, 'gjhj', 1, 1, 'Parcial', 'ytujh', 8.00, 0.00, 0.00, 0.00, '2025-03-31 22:29:17', '2025-03-31 18:30:25', '', 'Pendiente', NULL, NULL, NULL),
(67, 'Producción', 0, 'gjhj', 1, 1, 'Parcial', 'ytujh', 0.00, 0.00, 88.00, 0.00, '2025-03-31 22:30:25', NULL, '', 'Pendiente', NULL, NULL, NULL),
(68, 'Producción', 0, 'gjhj', 1, 1, 'Parcial', 'ytujh', 8.00, 0.00, 0.00, 0.00, '2025-03-31 22:30:25', '2025-03-31 18:30:46', '', 'Pendiente', NULL, NULL, NULL),
(69, 'Producción', 0, 'gjhj', 1, 1, 'Parcial', 'ytujh', 8.00, 0.00, 0.00, 0.00, '2025-03-31 22:30:46', '2025-03-31 18:30:58', '', 'Pendiente', NULL, NULL, NULL),
(70, 'Producción', 0, 'gjhj', 1, 1, 'Parcial', 'ytujh', 0.00, 0.00, 8.00, 0.00, '2025-03-31 22:30:58', NULL, '', 'Pendiente', NULL, NULL, NULL),
(71, 'Producción', 0, 'gjhj', 1, 1, 'Parcial', 'ytujh', 8.00, 0.00, 0.00, 0.00, '2025-03-31 22:30:58', '2025-03-31 18:31:34', '', 'Pendiente', NULL, NULL, NULL),
(72, 'Producción', 0, 'gjhj', 1, 1, 'Parcial', 'ytujh', 0.00, 0.00, 5.00, 0.00, '2025-03-31 22:31:34', NULL, '', 'Pendiente', NULL, NULL, NULL),
(73, 'Producción', 0, 'gjhj', 1, 1, 'Parcial', 'ytujh', 3.00, 0.00, 0.00, 0.00, '2025-03-31 22:31:34', '2025-03-31 18:32:09', '', 'Pendiente', NULL, NULL, NULL),
(74, 'Producción', 0, 'gjhj', 1, 1, 'Parcial', 'ytujh', 0.00, 0.00, 858.00, 0.00, '2025-03-31 22:32:09', NULL, '', 'Pendiente', NULL, NULL, NULL),
(75, 'Producción', 0, 'gjhj', 1, 1, 'Parcial', 'ytujh', 88.00, 0.00, 0.00, 0.00, '2025-03-31 22:32:09', '2025-03-31 18:32:14', '', 'Pendiente', NULL, NULL, NULL),
(76, 'Producción', 0, 'gjhj', 1, 1, 'Parcial', 'ytujh', 0.00, 0.00, 848.00, 0.00, '2025-03-31 22:32:14', NULL, '', 'Pendiente', NULL, NULL, NULL),
(77, 'Producción', 0, 'gjhj', 1, 1, 'Parcial', 'ytujh', 55.00, 0.00, 0.00, 0.00, '2025-03-31 22:32:14', '2025-03-31 18:32:19', '', 'Pendiente', NULL, NULL, NULL),
(78, 'Producción', 0, 'gjhj', 1, 1, 'Parcial', 'ytujh', 0.00, 0.00, 88.00, 0.00, '2025-03-31 22:32:19', NULL, '', 'Pendiente', NULL, NULL, NULL),
(79, 'Producción', 0, 'gjhj', 1, 1, 'Parcial', 'ytujh', 6.00, 0.00, 0.00, 0.00, '2025-03-31 22:32:19', NULL, '', 'Pendiente', NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `scrap_final`
--

CREATE TABLE `scrap_final` (
  `id` int(11) NOT NULL,
  `codigo_empleado` int(11) NOT NULL,
  `maquina_id` int(11) NOT NULL,
  `item` varchar(255) NOT NULL,
  `jtwo` varchar(255) NOT NULL,
  `cantidad` decimal(10,2) NOT NULL,
  `aprobado_por` int(11) NOT NULL,
  `fecha_aprobacion` datetime NOT NULL,
  `comentario` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `scrap_final`
--

INSERT INTO `scrap_final` (`id`, `codigo_empleado`, `maquina_id`, `item`, `jtwo`, `cantidad`, `aprobado_por`, `fecha_aprobacion`, `comentario`) VALUES
(13, 0, 1, 'dfgg', '54tg', 8.00, 1212, '2025-03-27 16:20:11', 'f'),
(14, 0, 1, 'dfgg', '54tg', 33.00, 1212, '2025-03-27 16:53:15', ''),
(15, 0, 1, 'dfgg', '54tg', 45.00, 1212, '2025-03-27 16:53:19', ''),
(16, 0, 1, 'dfgg', '54tg', 33.00, 1212, '2025-03-27 16:53:58', 'Nitido'),
(17, 0, 1, 'dfgg', '54tg', 500.00, 1212, '2025-03-27 16:56:59', ''),
(18, 0, 1, 'dfgg', '54tg', 33.00, 1212, '2025-03-27 17:03:14', ''),
(19, 0, 1, 'dfgg', '54tg', 5.00, 1212, '2025-03-27 17:03:21', ''),
(20, 0, 1, 'dfgg', '54tg', 11.00, 1212, '2025-03-28 15:19:27', 'ENviado a B1'),
(21, 0, 1, 'dfgg', '54tg', 3.00, 1212, '2025-03-28 15:32:48', 'Enviado a B2\r\n'),
(22, 0, 1, 'dfgg', '54tg', 55.00, 1212, '2025-03-28 17:39:14', ''),
(23, 0, 1, 'dfgg', '54tg', 55.00, 1212, '2025-03-28 18:05:27', ''),
(24, 0, 1, 'gjhj', 'ytujh', 55.00, 1212, '2025-03-31 15:24:43', ''),
(25, 0, 1, 'gjhj', 'ytujh', 33.00, 1212, '2025-03-31 16:57:56', '');

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
  `active_button_id` varchar(50) DEFAULT 'defaultButtonId'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `users`
--

INSERT INTO `users` (`id`, `nombre`, `codigo_empleado`, `password`, `tipo_usuario`, `area_id`, `maquina_id`, `jtWo`, `item`, `active_button_id`) VALUES
(22, 'Keíly de la cruz', 1194, '$2y$10$oiDDoCuyIg3eiLhE/g4fT.YsJcCzqcB.iXdKiaj6fK5rOIyoedO6G', 'operador', 1, NULL, NULL, NULL, 'defaultButtonId'),
(23, 'Miguel Brache', 783, '$2y$10$u5/aog0LtmNQ6h8Pay8LLuwXHm06y3J6YJbjDAdYnOLH3A13nSalK', 'operador', 1, 8, '306039', '3419402', 'Producción'),
(24, 'Cirilo de jesus', 453, '$2y$10$tP43kN6lSyguIkPDABrUbe.ZioovNGLqhLeFq9KGHGMii4edh7nYm', 'operador', 1, NULL, NULL, NULL, 'defaultButtonId'),
(25, 'Gustado mojica', 79852, '$2y$10$igM9FrNgOIJ5Exliqawa.eCQqDHj876vdS3nMVtyObTrLOvv60T1a', 'operador', 1, NULL, NULL, NULL, 'defaultButtonId'),
(26, 'Daniel Valdez', 71351, '$2y$10$Y9UmF.L6mQmJT6liRDAiMORrFwtElcwJEpwltKGcHWeLASnH4Xn4a', 'operador', 1, NULL, NULL, NULL, 'defaultButtonId'),
(27, 'Davor', 101, '$2y$10$VuxRgoJ8KtDZ3cemRg4OIekzZra1nhPSdffB.bMUin0EgQsGiuIg.', 'supervisor', 1, 24, '', '', 'defaultButtonId'),
(28, 'Reynaldo Reyes', 784, '$2y$10$AqleZBoRBx0fCao5N58xxezEtqym2nMge4b23ADMRxyLlpAsjuSqS', 'operador', 1, 8, NULL, NULL, 'Espera_trabajo'),
(29, 'Welington Seberino', 7411, '$2y$10$G1MqDC2zsZIqNDGaZUuVsOmbJmlpf6UQ.3nBRqX/d0/wWOsnty4q.', 'operador', 1, NULL, NULL, NULL, 'defaultButtonId'),
(30, 'Julio Rodriguez', 1363, '$2y$10$AMLZyjP204GQnJ3rPW/nJux.ARTnNbCVQKRIH6NGCf7bJx7tCqcWe', 'operador', 1, NULL, NULL, NULL, 'defaultButtonId'),
(31, 'Wilson mercedes', 373, '$2y$10$PuacuxoLajgnJq9R2KhxOeqrFvl7yvug1WTWbWxYAufVmeDUOvE0K', 'operador', 1, NULL, NULL, NULL, 'defaultButtonId'),
(32, 'Francis', 71829, '$2y$10$oakpVn4X8m6yoCUnPFYSBOySCarPfzAZ4qW/HwN1Ev6zZfdz4qM.C', 'operador', 1, NULL, NULL, NULL, 'defaultButtonId'),
(33, 'Wilder guerrero vargas', 72158, '$2y$10$IlpmA9Eu7UsIG.jr3ayyaOT.gYL/NAfM3li6KUtDU10RSa20aE0Q.', 'operador', 1, NULL, NULL, NULL, 'defaultButtonId'),
(34, 'Joel Joseph ', 73899, '$2y$10$VJ/xrFiEHoMXdqlPsZStTu34s0PpEXE9pfI.Ua9dy9zDmhD0TBfty', 'operador', 1, NULL, NULL, NULL, 'defaultButtonId'),
(35, 'Fernándo Gutierrez', 76998, '$2y$10$CDE4hsCsdZY3u1Q7/tGLSOZHJGR73Axkr22xoRV6L0jdZ/aUQnqZW', 'operador', 1, 8, '305891', '3420089', 'Producción'),
(36, 'Nelson pena', 481, '$2y$10$dzy6kt7bd9.nfUT4w3ltbe7zraAq6NZRlrzZciWRili5RHjHYeWLO', 'operador', 1, NULL, NULL, NULL, 'defaultButtonId'),
(37, 'Jorge luis', 436, '$2y$10$RYCXv3NFe2st0zjoi0FfwellnDi6emGIP9Fn7pOqbTPa4e//umbvC', 'operador', 1, NULL, NULL, NULL, 'defaultButtonId'),
(38, 'Maximo Matos ', 312, '$2y$10$58n5glwk5M7iGxm.XxyziuAGIOU8Qc520eN.TWAuGOIUXM2nnUf22', 'operador', 1, NULL, NULL, NULL, 'defaultButtonId'),
(40, 'Luis Antonio Valdez Mateo', 9069, '$2y$10$9FA1C48fpGate9q/CH4bO.N0XZnP8O1wqUr76A84j0pN3UpMznCAG', 'operador', 1, 8, NULL, NULL, 'final_produccion'),
(86, 'Andres Martinez', 79195, '$2y$10$Nele2BsV4WoivY7.r/Te9.tNmLRdv/jopnXvDuTpfOGpI5B8pqcBy', 'supervisor', 3, NULL, NULL, NULL, 'Producción'),
(90, 'Domingo Alexis mejia', 1217, '$2y$10$ED/Fk1I9TLeGkvlL/.TTnetcuG8DPD4lY11lp5x.eOxSbRRDm9t5y', 'operador', 3, 24, NULL, NULL, 'final_produccion'),
(91, 'Jahiron eli pinales amador', 88092, '$2y$10$jnAMdVMrFpYSep/ZNFQYGu1VyG8b6n3whVC7fMNLGLeWFRKHpsPXa', 'operador', 3, 25, '306017 ', '90346565', 'Preparación'),
(92, 'Raul Aguilar ', 77932, '$2y$10$XfrVfnIALXAdSgmxbNPnQ.//l1Gr.qFIYGZR4tPiOIlrCVtHXTvji', 'operador', 3, 24, '306017', '90346565', 'Contratiempos'),
(93, 'José Batista ', 1356, '$2y$10$IDTDdVgc8WmC1lIQR6f3teTbjhJZtVFb0PpWFz8lHPIy80dq6Oz3a', 'supervisor', 3, NULL, NULL, NULL, 'defaultButtonId'),
(94, 'Edward perez', 386, '$2y$10$/zUnvA6NErAkxKpHeWhyAuWAJd6nYn3l.We.Z/hDufRQaTybJQbFK', 'supervisor', 3, NULL, NULL, NULL, 'defaultButtonId'),
(99, 'Saul Santana ', 72479, '$2y$10$WnYpwZGgi03kCL3b.Xc0k.wj3AZzqU/dWOrPHXBMEysymCvA3pY.a', 'operador', 3, 34, NULL, NULL, 'final_produccion'),
(100, 'Jeison Dominguez ', 85300, '$2y$10$AciKBOKfefZpS0OgzpwiY.OJAZRTbIgTSIGBB9X1rMRBtOm4z8hwS', 'operador', 3, 26, NULL, NULL, 'final_produccion'),
(104, 'Yan Carlos Flores ', 409, '$2y$10$0xKquv6yf7FAVRfj7a1dK.PkTZjCeWd1PR7wb4E1yi9k.USQhGVpG', 'operador', 3, 34, '306013', '90346566', 'Producción'),
(107, 'Franyeli Delgado', 1051, '$2y$10$NtYAOmaCNm0Agyf21tZVw.ZUoL0nPbsTfDFX7BBhOaSUi8HKHkhoO', 'operador', 3, 27, '306013', 'PG90346566', 'Contratiempos'),
(108, 'Miguel novas', 1359, '$2y$10$MbTi8/UVY334TIjSVC6xceHIgtUpjXG/kGqhg1J49v8XxHWEl6dP2', 'operador', 3, 27, NULL, NULL, 'final_produccion'),
(109, 'Wilmer Gonzalez', 74163, '$2y$10$xxb9SfR3TUZlXXjhhZmRxex4W.F6Ml5MDMVGVp3UOp5qUJyH.OHEi', 'supervisor', 3, NULL, NULL, NULL, 'defaultButtonId'),
(110, 'Omar', 1120, '$2y$10$kD8sOL7RNI/yilwGkJmiDeBZpqB1Ixd12aaPG/zF/WWVhovDCR.EK', 'supervisor', 3, NULL, NULL, NULL, 'defaultButtonId'),
(113, '00', 0, '$2y$10$hpRygAe65JbBmvjw8mdfmuf2qy7InTtVJxtKL/j6eIGbaLMuyignO', 'operador', 1, 1, 'ytujh', 'gjhj', 'Producción'),
(114, 'Estalin', 1314, '$2y$10$cTSOXQ8FzfjG.kmc3kYxXelZmkFPWCch5aJ.jacL3OL2Zbzpd5lzy', 'operador', 3, 25, NULL, NULL, 'final_produccion'),
(115, 'Joji', 1335, '$2y$10$y3p0yasQAYApHL2ibsCKie1pC8E/axWCU9foc73gsjwPMK5rNkHea', 'operador', 3, NULL, NULL, NULL, 'defaultButtonId'),
(116, 'Raúl', 73600, '$2y$10$0w/3ViAM6qGsHo3hzSUYA.CVp29xDe5DD.kLAnvGdSvxAKiS0fetK', 'operador', 3, 26, '306013', '90346566', 'Contratiempos'),
(117, 'Wilmer Gonzalez', 2327238, '$2y$10$bmwHYusgnf9bKBnu84KenuKgISnHhrM5l44ZBfxpLY9XnYQDT9Ana', 'supervisor', 3, NULL, NULL, NULL, 'defaultButtonId'),
(118, 'Juan jose arias ', 296, '$2y$10$M9yYLxHc2yvXNvUR7dTVDOrbSl./iIzPxDyPUixEw1kO/qMI9fLDm', 'operador', 3, 26, NULL, NULL, 'final_produccion'),
(119, 'Wilson montero ', 83879, '$2y$10$o1zfgUo2pE301rA8kdYJ7ObBQeZiRChoQbfSy/SC/hCkzzm3IBPlG', 'operador', 3, 26, NULL, NULL, 'final_produccion'),
(120, 'Estalin pineda', 72249, '$2y$10$qw2/qTqpUrDdvTDvLWQcquTHF70BRqJNhWwbVGo8cCvwARwaWmzIW', 'operador', 3, 25, NULL, NULL, 'final_produccion'),
(121, 'Luis Abad', 321, '$2y$10$7deyxdeyKvgoPmDdtlLvBeIIkiWz2bd3lwmSsXSx6DZ3HwzUgWPz6', 'operador', 2, 36, 'Z0980', '6209223', 'Producción'),
(122, 'Robert feliz', 88283, '$2y$10$7KQ84C7YoEMypbMtZyiLuO9qHMShq1Z5ar3wUygbOlgtB6NDKE2AW', 'operador', 2, 37, '25732-z2214', '3403049', 'Producción'),
(123, 'Santo vasquez', 31, '$2y$10$FT.JnmGF0iNgEP5sfmJqm.w2DAN.oOlSh4OD/pOQErHGRP.Q6ix7a', 'operador', 2, 35, NULL, NULL, 'final_produccion'),
(124, 'Eric Fernandez', 923, '$2y$10$i.soEHmEfxjij3AumpVYbeQMj6S3kaWc99tE5t.QEDXpSPh.D4wTK', 'supervisor', 2, NULL, NULL, NULL, 'defaultButtonId'),
(125, 'Fraklin silverio', 314, '$2y$10$p7YMYlDQy8S6o1BkkcaaiezNSEHFPzl0DYBTOf1cOfauDB2.QG/5e', 'supervisor', 2, NULL, NULL, NULL, 'defaultButtonId'),
(126, 'Euddy casanova', 318, '$2y$10$3/8YZPTV8Qpv.uekLKSJBe8d0Eevdd//QktHGft1KgpGIOg9FC/WW', 'operador', 1, 8, '305955', '3417058', 'Producción'),
(128, 'Anderson mercedes', 1278, '$2y$10$RLFo9xEyW70qIWIAu1/zUemDA0jL04GroEWz5MOnwxQuSnvJyhGm2', 'operador', 1, 9, NULL, NULL, 'final_produccion'),
(129, 'Rafael de jesus', 172, '$2y$10$KCSmj2XeG0kGW8PaQgtF7.lMZTnNlL8Vk1DbAyzUkBmwy4ywYcyjy', 'supervisor', 4, NULL, NULL, NULL, 'defaultButtonId'),
(130, 'Jansel tavarez ', 524, '$2y$10$Vlp550mzQDvOwQmxhydvmeVAN.fHLG9hHgKfgOWtaO2pAgxhtgdFm', 'operador', 4, 19, 'Z2139', '3418197', 'Preparación'),
(131, 'RICHARD ', 239, '$2y$10$Adfx5SE9hBO47M26a/LXb.J84wmaqz05vZIBUIB10G8dDC4UQ5oT2', 'operador', 4, 19, NULL, NULL, 'final_produccion'),
(132, 'Nelson sanchez', 1211, '$2y$10$7uzqkw2hpF0ABLmjeZAE2OXUjiWTxjoakJ6Vm/frZK0h3oBIHHHfC', 'supervisor', 1, NULL, NULL, NULL, 'defaultButtonId'),
(133, 'Luis Antonio Valdez Mateo', 90583, '$2y$10$ftoud6HrQh/p86KwJvCAju8vPr7sG/b2Lj.Q5m9NAESFpuEJJ5nla', 'operador', 1, 9, NULL, NULL, 'final_produccion'),
(134, 'AYENDY TRINIDAD ', 444, '$2y$10$fRqe4fqRnaSOKNWEyduhMehDi4SnXml33n.w/dAp8iHjxGQeuEbMW', 'operador', 4, 19, NULL, NULL, 'defaultButtonId'),
(135, 'Edu', 1266, '$2y$10$hn2MIeOoqPJg0pYeQqdKouZqBvStDT3Lz/dlUOrCPL60w3YbglT.O', 'supervisor', 4, NULL, NULL, NULL, 'defaultButtonId'),
(136, 'fhgfhgf', 9088, '$2y$10$Z5RO2r6Qb.1tnJIvAgAcO.TXAZxxqyy0LAgxVMzRDbMqAjCMYbsum', 'supervisor', 1, NULL, NULL, NULL, 'defaultButtonId'),
(138, 'sdfsfdfsd', 2147483647, '$2y$10$Ua4yPLVUD.B6Du0Ux8dEu.d6RBLNENdx5/0cQwFIwkz0aj9bbBBXu', 'operador', 1, NULL, NULL, NULL, 'defaultButtonId'),
(141, 'johan', 8978, '$2y$10$uBpaukLumSbDrbkn8rr/O.r7z6bYQ0yUlE.iEfNc3wxIoP0ZNiugq', 'operador', 1, NULL, NULL, NULL, 'defaultButtonId'),
(142, 'dfghfdhgfh', 35544353, '$2y$10$S25HHNFyLV1GIB1hqi4rYOkGpPey/ZBaKgScOtZQqZsfyMZPoAmGO', 'operador', 1, NULL, NULL, NULL, 'defaultButtonId'),
(146, 'gaby', 230988, '$2y$10$vgWPpNuS80ns0bRjBFdez.vgQRTL3KSEdQkGS1e3eWOrh0RzIvYCW', 'operador', 2, NULL, NULL, NULL, 'defaultButtonId'),
(147, '\"\"', 232333, '$2y$10$Fb5XVAkCGNPMXlz8Hk/UWe3YldfzsTPPcp4R9tZWcJgVVBiQ5wqDK', 'operador', 1, NULL, NULL, NULL, 'defaultButtonId'),
(149, 'eduuu', 325433545, '$2y$10$80z9lzrBS5i4kyV6v1lvu.8msOKb22CI44WFqoh9AYSQWi3NBjot6', 'operador', 2, NULL, NULL, NULL, 'defaultButtonId'),
(151, 'eduy', 98788890, '$2y$10$F3U/KoLwFfmOlgJk1aTk/uL4ZTVlzlA6T89hiYNKAyS8E2hl1NXgG', 'operador', 1, NULL, NULL, NULL, 'defaultButtonId'),
(152, 'Nicolas Fersola', 3370, '$2y$10$A8WOumXs45Is53DYpiLzq.BeUsRT0R1dr/ldbzDrUYjPNTubh31Mq', 'operador', 3, 24, NULL, NULL, 'final_produccion'),
(153, 'Carlos', 2323, '$2y$10$OhPpCkyBH1eIgVUjnZnFgOQXeaG4WosTO/bejyqdQZ2ogf9JZ.Lte', 'qa', 1, NULL, NULL, NULL, 'defaultButtonId'),
(163, 'Raul', 456757, '$2y$10$g5iyubBkKaIXLJKcURvWe.sjhO4LfdVKBEawei0xZBOOSIjxnnqqG', 'operador', 1, NULL, NULL, NULL, 'defaultButtonId'),
(164, 'Nicol', 12123, '$2y$10$zx0h5x/R0sxAx4RMpj0Cku6j5/4.XngQv5aR586yTz91Cl3AyIRxS', 'qa', 2, NULL, NULL, NULL, 'defaultButtonId'),
(165, 'eh', 1209888, '$2y$10$l4yEggA.RaIgM038kICFLeCKmlJqvCoS4jDCnZ430SBwDSytFsnhK', 'qa', 3, NULL, NULL, NULL, 'defaultButtonId'),
(166, 'Anuel', 666784, '$2y$10$Um4CqQ.NmV52L4ZlD5GDV.J/ey1A9FNWYNmHdCo61x7m1uHodQ5Su', 'qa', 1, NULL, NULL, NULL, 'defaultButtonId'),
(167, 'Quie', 1213, '$2y$10$jbZq6aWLfBa3RsLsNXWv1OfDfkT.rsUMYJAOycXvM3HyJh5Vzgq6y', 'qa', 3, NULL, NULL, NULL, 'defaultButtonId'),
(168, 'Fersola', 1212, '$2y$10$32LbBJyQM/bGMKx7LKq.6evcVbvOh1C/.VwK/uqVy979ENCtLHWkq', 'qa', 1, NULL, NULL, NULL, 'defaultButtonId'),
(169, 'Nicolasssss', 334467, '$2y$10$0x5UEGNqiD2LGFAYRwDYtep4j04/rurCqBE4ArLOmuJBqXRrH7Mfa', 'supervisor', 2, NULL, NULL, NULL, 'defaultButtonId'),
(170, 'Nicolasssss', 6677667, '$2y$10$UOQOMQ7b..VtJ4tFy3A.4OtbkarPEO5KXFgsyGb.ZLIr.HB.Vs23a', 'operador', 1, NULL, NULL, NULL, 'defaultButtonId'),
(171, 'gdsfdsf', 655676, '$2y$10$BuZAw3ELqQgHi7xS..HXMuZGyB4jeevgPLBW2xNQ839pQHv5h4iru', 'supervisor', 1, NULL, NULL, NULL, 'defaultButtonId');

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
-- Volcado de datos para la tabla `velocidad`
--

INSERT INTO `velocidad` (`id`, `maquina`, `jtWo`, `item`, `velocidad_produccion`, `fecha_registro`, `area_id`) VALUES
(1, 4, '305427', '3419800', 13000.00, '2024-05-01 15:34:47', 1),
(2, 12, '305427', '3419800', 6000.00, '2024-05-03 18:33:15', 1),
(3, 6, '305428', '3419939', 4500.00, '2024-05-03 22:00:00', 1),
(4, 6, '305531', '3412400', 4000.00, '2024-05-04 01:56:59', 1),
(5, 8, '305490', '1050893G3', 10000.00, '2024-05-08 01:03:49', 1),
(6, 8, '305488', '1026410V1', 10000.00, '2024-05-08 01:58:06', 1),
(7, 8, '305519', '1026395V1', 8000.00, '2024-05-08 02:51:08', 1),
(8, 2, '305509', '1050692c2', 6000.00, '2024-05-09 02:55:04', 1),
(9, 2, '305504', '1733220a1', 7000.00, '2024-05-09 03:44:15', 1),
(10, 2, '305523', '1001107G2', 7000.00, '2024-05-09 08:12:45', 1),
(13, 8, '305475', '1053567G1', 8500.00, '2024-05-16 00:31:45', 1),
(14, 8, '305477', '1256385V2', 6000.00, '2024-05-16 04:53:16', 1),
(16, 8, '305498', '1095447V2', 6500.00, '2024-05-18 00:10:40', 1),
(17, 12, '305047', '3410552', 5000.00, '2024-05-20 16:23:52', 1),
(18, 14, '305567', '3419938', 9000.00, '2024-05-21 16:33:25', 1),
(19, 14, '305567', '3419938', 10000.00, '2024-05-21 16:33:41', 1),
(20, 6, '305549', '3419562', 6500.00, '2024-05-21 17:04:59', 1),
(21, 24, '305621', '14006', 5000.00, '2024-06-20 22:27:34', 3),
(22, 24, '305621', '14006', 5000.00, '2024-06-20 22:27:43', 3),
(23, 24, '305621', '14006', 10000.00, '2024-06-20 22:28:06', 3),
(32, 26, '345678', '456743', 300.00, '2024-06-28 22:09:49', 3),
(34, 25, '305731', '90327150', 200.00, '2024-07-01 14:07:36', 3),
(35, 25, '305731', '90327150', 200.00, '2024-07-01 14:09:12', 3),
(36, 25, '305732 ', 'Pg-90346566', 200.00, '2024-07-01 18:18:20', 3),
(37, 26, '305731', '90327150', 250.00, '2024-07-02 11:41:37', 3),
(38, 26, '305731', '90327150', 250.00, '2024-07-02 11:41:52', 3),
(39, 24, '305911', 'PG=21088201', 400.00, '2024-07-02 11:59:49', 3),
(40, 25, '305732 ', '90346566 ', 200.00, '2024-07-02 12:03:32', 3),
(41, 26, '305731', '90327150', 250.00, '2024-07-02 12:06:43', 3),
(42, 26, '305731', '90327150', 250.00, '2024-07-02 12:12:12', 3),
(43, 27, '305774', '90228981', 130.00, '2024-07-02 13:13:31', 3),
(44, 26, '305731', '90327150', 100.00, '2024-07-02 13:28:09', 3),
(45, 26, '305731', '90327150', 100.00, '2024-07-02 13:28:17', 3),
(46, 26, '305731', '90327150', 100.00, '2024-07-02 14:11:32', 3),
(48, 26, '305731', '90327150', 100.00, '2024-07-02 15:24:52', 3),
(49, 26, '305731', '90327150', 100.00, '2024-07-02 15:27:50', 3),
(50, 27, '305731', '90327150', 130.00, '2024-07-02 15:46:33', 3),
(51, 25, 'X0028', 'Prueba ', 150.00, '2024-07-02 15:49:15', 3),
(52, 27, '305731', '90327150', 130.00, '2024-07-02 16:40:25', 3),
(53, 26, '305731', '90327150', 100.00, '2024-07-02 17:16:59', 3),
(54, 26, '305731', '90327150', 150.00, '2024-07-02 17:32:35', 3),
(55, 26, '305731', '90327150', 200.00, '2024-07-02 19:35:43', 3),
(56, 27, '305731', '90327150', 130.00, '2024-07-02 23:16:50', 3),
(57, 27, '305731', '90327150', 130.00, '2024-07-02 23:17:11', 3),
(58, 27, '305731', '90327150', 130.00, '2024-07-03 11:24:03', 3),
(59, 24, '305731', '90327150', 200.00, '2024-07-03 11:41:46', 3),
(60, 24, '305731', '90327150', 150.00, '2024-07-03 11:58:49', 3),
(61, 25, '305732 ', '90346566 ', 200.00, '2024-07-03 12:08:08', 3),
(62, 24, '305731', '90327150', 200.00, '2024-07-03 12:13:57', 3),
(63, 24, '305731', '90327150', 200.00, '2024-07-03 12:14:01', 3),
(64, 24, '305731', '90327150', 200.00, '2024-07-03 13:18:22', 3),
(65, 24, '305731', '90327150', 200.00, '2024-07-03 13:56:06', 3),
(66, 24, '305731', '90327150', 65.00, '2024-07-03 15:30:59', 3),
(67, 24, '305731', '90327150', 65.00, '2024-07-03 15:31:05', 3),
(68, 24, '305731', '90327150', 65.00, '2024-07-03 15:48:36', 3),
(69, 24, '305731', '90327150', 65.00, '2024-07-03 15:48:40', 3),
(70, 24, '305731', '90327150', 200.00, '2024-07-03 16:03:02', 3),
(71, 24, '305731', '90327150', 50.00, '2024-07-03 17:51:42', 3),
(72, 24, '305731', '90327150', 50.00, '2024-07-03 17:51:48', 3),
(73, 24, '305731', '90327150', 200.00, '2024-07-03 18:28:58', 3),
(74, 24, '305731', '90327150', 370.00, '2024-07-03 19:15:06', 3),
(75, 24, '305731', '90327150', 200.00, '2024-07-03 19:16:52', 3),
(76, 24, '305731', '90327150', 200.00, '2024-07-03 19:16:59', 3),
(77, 24, '305731', '90327150', 65.00, '2024-07-03 19:46:09', 3),
(78, 24, '305731', '90327150', 65.00, '2024-07-03 19:46:14', 3),
(79, 27, '305731', '90327150', 130.00, '2024-07-03 20:12:56', 3),
(80, 27, '305731', '90327150', 130.00, '2024-07-03 20:13:03', 3),
(81, 26, '305732', '90346566', 150.00, '2024-07-03 21:33:24', 3),
(82, 26, '305732', '90346566', 150.00, '2024-07-03 21:33:29', 3),
(83, 27, '305731', 'Pg90327150', 130.00, '2024-07-03 23:31:46', 3),
(84, 26, '305732', '90346566', 250.00, '2024-07-04 11:24:18', 3),
(85, 26, '305732', '90346566', 250.00, '2024-07-04 11:24:22', 3),
(86, 26, '305732', '90346566', 150.00, '2024-07-04 11:42:18', 3),
(87, 27, '305731', '90327150', 130.00, '2024-07-04 11:43:57', 3),
(88, 26, '305732', '90346566', 150.00, '2024-07-04 12:03:07', 3),
(89, 26, '305732', '90346566', 250.00, '2024-07-04 12:48:25', 3),
(90, 26, '305732', '90346566', 300.00, '2024-07-04 13:05:06', 3),
(91, 27, '305731', '90327150', 130.00, '2024-07-04 13:51:15', 3),
(92, 27, '305731', '90327150', 130.00, '2024-07-04 18:12:39', 3),
(93, 19, 'Z1166', '3409500', 2164.00, '2024-07-04 18:31:06', 4),
(94, 19, 'Z1166', '3409500', 50.00, '2024-07-04 18:31:34', 4),
(95, 25, '305900 ', '21072712', 150.00, '2024-07-04 19:10:26', 3),
(96, 27, '305732', '90346566', 135.00, '2024-07-05 11:21:42', 3),
(97, 25, '305910', '21084393', 150.00, '2024-07-05 12:14:58', 3),
(98, 24, '305910', 'PG=21084393', 450.00, '2024-07-05 12:37:29', 3),
(99, 27, '305732', '90346566', 130.00, '2024-07-05 17:32:40', 3),
(100, 26, '305732', '90346566', 150.00, '2024-07-05 19:31:52', 3),
(101, 26, '305732', '90346566', 150.00, '2024-07-06 12:10:55', 3),
(102, 27, '305732', '90346566', 130.00, '2024-07-06 13:26:16', 3),
(103, 26, '305732', '90346566', 200.00, '2024-07-06 13:39:27', 3),
(104, 26, '305732', '90346566', 200.00, '2024-07-06 13:39:38', 3),
(105, 27, '305732', '90346566', 130.00, '2024-07-06 16:17:43', 3),
(106, 27, '305732', '90346566', 150.00, '2024-07-07 04:09:02', 3),
(107, 26, '305732', '90346566', 150.00, '2024-07-07 12:24:50', 3),
(108, 26, '305732', '90346566', 250.00, '2024-07-07 13:39:36', 3),
(109, 26, '305732', '90346566', 250.00, '2024-07-08 11:40:25', 3),
(110, 27, '305732', '90346566', 150.00, '2024-07-08 11:54:36', 3),
(111, 25, '305910', '21084393', 160.00, '2024-07-08 13:53:45', 3),
(112, 26, '305900', '21072712', 300.00, '2024-07-08 15:33:24', 3),
(113, 26, '305900', '21072712', 300.00, '2024-07-08 15:33:32', 3),
(114, 26, '305900', '21072712', 200.00, '2024-07-08 16:07:39', 3),
(115, 26, '305900', '21072712', 200.00, '2024-07-08 16:07:43', 3),
(116, 27, '305732', 'PG90346566', 150.00, '2024-07-08 21:38:38', 3),
(117, 26, '305910', '21084393', 250.00, '2024-07-09 11:43:08', 3),
(118, 27, '305900', 'PG21072712', 130.00, '2024-07-09 14:18:27', 3),
(119, 25, '305911', '21082801 ', 150.00, '2024-07-09 18:32:53', 3),
(120, 26, '305910', '21084393', 250.00, '2024-07-10 12:29:34', 3),
(121, 26, '305910', '21084393', 250.00, '2024-07-10 12:29:39', 3),
(122, 27, '305910', 'PG21084393', 130.00, '2024-07-10 13:09:27', 3),
(123, 25, '305913', '21072720 ', 150.00, '2024-07-10 16:38:02', 3),
(124, 25, '305914 ', '21030209 ', 150.00, '2024-07-10 18:42:59', 3),
(125, 26, '305910', '21084393', 250.00, '2024-07-10 19:26:25', 3),
(129, 26, '305910', '21084393', 100.00, '2024-07-10 20:25:29', 3),
(130, 24, '305933', 'PG21116216', 400.00, '2024-07-10 22:33:13', 3),
(131, 25, '305911', '21082801', 200.00, '2024-07-11 13:24:38', 3),
(132, 24, '25626', 'PG=20173672', 400.00, '2024-07-11 15:07:21', 3),
(133, 25, '305911', '21082801', 50.00, '2024-07-11 16:02:17', 3),
(134, 25, '305911', '21082801', 50.00, '2024-07-11 16:02:29', 3),
(135, 26, '305933', '21116216', 300.00, '2024-07-11 19:37:06', 3),
(136, 27, '305910', 'PG=21084393', 130.00, '2024-07-12 11:54:21', 3),
(137, 26, '20173672', '25626', 100.00, '2024-07-12 14:21:24', 3),
(138, 26, '20173672', '25626', 100.00, '2024-07-12 14:47:24', 3),
(139, 26, '20173672', '25626', 100.00, '2024-07-12 14:52:57', 3),
(140, 34, '305910', '21084393', 10.00, '2024-07-13 14:04:02', 3),
(141, 34, '305910', '21084393', 130.00, '2024-07-13 14:04:09', 3),
(142, 26, '25626', '20173672', 60.00, '2024-07-13 20:33:53', 3),
(143, 26, '25629', '20182356', 50.00, '2024-07-15 12:31:00', 3),
(144, 26, '25629', '20182356', 150.00, '2024-07-15 13:30:54', 3),
(145, 26, '25629', '20182356', 30.00, '2024-07-15 15:33:02', 3),
(146, 26, '25629', '20182356', 100.00, '2024-07-15 15:49:15', 3),
(147, 26, '25627', '20193706', 150.00, '2024-07-15 17:43:23', 3),
(148, 26, '25627', '20193706', 60.00, '2024-07-15 19:16:38', 3),
(149, 27, '305910', 'PG=21084393', 130.00, '2024-07-16 11:49:49', 3),
(150, 26, '305732', '90346566', 300.00, '2024-07-16 15:27:17', 3),
(151, 26, '305732', '90346566', 200.00, '2024-07-16 15:27:55', 3),
(152, 25, '305931', '21244801 ', 110.00, '2024-07-16 17:38:42', 3),
(153, 27, '305911', '21082801', 130.00, '2024-07-16 21:42:56', 3),
(154, 27, '305911', '21082801', 130.00, '2024-07-17 12:15:44', 3),
(155, 27, '305911', '21082801', 120.00, '2024-07-17 12:51:49', 3),
(156, 24, '305732', '90346566', 150.00, '2024-07-17 20:48:29', 3),
(157, 24, '305924', '30051902', 400.00, '2024-07-17 21:01:45', 3),
(158, 27, '305732', '90346566', 150.00, '2024-07-17 22:51:27', 3),
(159, 27, '305732', 'PG90346566', 150.00, '2024-07-18 11:38:08', 3),
(160, 25, '305931', '21244801 ', 170.00, '2024-07-18 19:30:58', 3),
(161, 26, '305912', '21030210', 300.00, '2024-07-18 19:52:16', 3),
(162, 24, '305934', 'PG:90327150', 400.00, '2024-07-18 20:27:45', 3),
(163, 24, '305934', 'PG:90327150', 425.00, '2024-07-18 22:10:15', 3),
(164, 34, '305913', '21072720', 150.00, '2024-07-19 11:22:12', 3),
(165, 26, 'X0029', 'N/a', 200.00, '2024-07-19 12:19:02', 3),
(166, 26, '305913', '21072720', 350.00, '2024-07-19 13:51:24', 3),
(167, 24, '305934', 'PG:90327150', 450.00, '2024-07-19 15:04:35', 3),
(168, 27, '305914', 'PG21030209', 130.00, '2024-07-19 15:40:06', 3),
(169, 24, '305934', 'PG:90327150', 450.00, '2024-07-19 15:54:39', 3),
(170, 25, '305934 ', '90387150', 130.00, '2024-07-19 16:45:13', 3),
(171, 27, '305914', '21030209', 130.00, '2024-07-19 20:02:50', 3),
(172, 26, 'X0031', 'N/a', 70.00, '2024-07-19 21:06:29', 3),
(173, 24, '305931', '21144801', 250.00, '2024-07-20 01:32:47', 3),
(174, 26, '305931', '21144801', 250.00, '2024-07-20 01:59:05', 3),
(175, 26, '305931', '21144801', 300.00, '2024-07-22 11:36:11', 3),
(176, 25, '305934', '90327150', 180.00, '2024-07-22 15:59:17', 3),
(177, 34, '305910', '21084393', 130.00, '2024-07-22 16:44:40', 3),
(178, 24, '25629', '20182356', 350.00, '2024-07-22 18:36:39', 3),
(179, 27, '305931', '21144801', 130.00, '2024-07-22 20:20:31', 3),
(180, 27, '305931', 'PG21144801', 130.00, '2024-07-23 12:00:18', 3),
(181, 25, '305934', '90327150', 180.00, '2024-07-23 13:00:41', 3),
(182, 25, '305941', '90346565', 180.00, '2024-07-23 15:09:23', 3),
(183, 26, 'X0031', 'N/a', 65.00, '2024-07-23 21:19:24', 3),
(184, 26, '305934', '90327150', 300.00, '2024-07-24 14:12:06', 3),
(185, 24, '305934', '90327150', 450.00, '2024-07-24 14:33:04', 3),
(186, 24, '305934', '90327150', 450.00, '2024-07-24 15:01:48', 3),
(187, 24, '305934', '90327150', 450.00, '2024-07-24 17:30:35', 3),
(188, 27, '305934', '90327150', 130.00, '2024-07-24 20:13:24', 3),
(189, 26, '305941', '90346565', 375.00, '2024-07-25 11:52:38', 3),
(190, 26, 'X0032', 'N/A', 300.00, '2024-07-25 15:40:22', 3),
(191, 26, '305732', '90346566', 20.00, '2024-07-25 21:16:29', 3),
(192, 24, '305934', '90327150', 450.00, '2024-07-26 12:11:27', 3),
(193, 27, '305934', 'PG90327150', 130.00, '2024-07-26 15:01:32', 3),
(194, 27, '305934', '90327150', 130.00, '2024-07-26 19:18:18', 3),
(195, 27, '305491', 'PG90346565', 130.00, '2024-07-29 13:12:16', 3),
(196, 27, '305941', 'PG90346565', 130.00, '2024-07-29 15:39:04', 3),
(197, 27, '305941', '90346565', 130.00, '2024-07-29 20:21:54', 3),
(198, 27, '305941', 'PG90346565', 130.00, '2024-07-30 11:37:12', 3),
(199, 26, '305913', '21072720', 300.00, '2024-07-30 12:03:42', 3),
(200, 27, '305913', '21072720', 150.00, '2024-07-30 18:57:24', 3),
(201, 26, '305934', '90327150', 375.00, '2024-08-01 11:41:12', 3),
(202, 27, '305941', 'PG90346565', 130.00, '2024-08-01 12:33:14', 3),
(203, 27, '305563', 'PG90346565', 130.00, '2024-08-01 13:22:57', 3),
(204, 27, '305934', 'Pg90327150', 1300.00, '2024-08-01 16:05:35', 3),
(205, 27, '305934', 'Pg90327150', 130.00, '2024-08-01 16:05:38', 3),
(206, 26, '305934', '90327150', 300.00, '2024-08-01 21:27:16', 3),
(207, 26, '305986', '21116216', 200.00, '2024-08-02 11:58:10', 3),
(208, 27, '305934', 'Pg90327150', 130.00, '2024-08-02 12:03:13', 3),
(209, 24, '306010', '90055669', 450.00, '2024-08-02 12:22:53', 3),
(210, 24, '306010', '90055669', 450.00, '2024-08-02 18:45:09', 3),
(211, 26, '305934', '90327150', 200.00, '2024-08-05 12:27:50', 3),
(212, 27, '305934', 'PG90327150', 160.00, '2024-08-05 12:45:02', 3),
(213, 27, '305934', 'PG90327150', 130.00, '2024-08-05 12:45:06', 3),
(214, 24, '306013', '90346566', 600.00, '2024-08-05 17:12:32', 3),
(215, 26, '305934', '90327150', 375.00, '2024-08-05 18:15:53', 3),
(216, 27, '305934', 'PG90327150 ', 130.00, '2024-08-06 11:23:55', 3),
(217, 26, '305934 ', '90327150', 375.00, '2024-08-06 11:40:44', 3),
(218, 26, '305934 ', '90327150', 375.00, '2024-08-06 11:42:54', 3),
(219, 25, '306010', '90055669', 160.00, '2024-08-06 12:11:23', 3),
(220, 26, '306010', '90055669', 375.00, '2024-08-06 15:03:56', 3),
(221, 27, '305934', 'PG90327150', 130.00, '2024-08-07 11:41:18', 3),
(222, 26, '306010', '90055669', 375.00, '2024-08-07 11:50:52', 3),
(223, 26, 'Dd', 'Ff', 50.00, '2024-08-08 12:33:01', 3),
(224, 36, 'Z1278', '6209223', 250.00, '2024-08-08 14:22:46', 2),
(225, 36, 'Z1278', '6209223', 250.00, '2024-08-08 14:22:57', 2),
(226, 36, '1278', '6209223', 250.00, '2024-08-09 11:12:44', 2),
(227, 36, '1278', '6209223', 250.00, '2024-08-09 11:12:54', 2),
(228, 42, 'Y', '6', 50.00, '2024-08-09 12:26:44', 2),
(229, 1, '305955', '34170058', 4000.00, '2024-08-09 13:08:15', 1),
(230, 36, '1278', '6209223', 250.00, '2024-08-09 14:25:59', 2),
(231, 35, 'Z2145', '3407883', 50.00, '2024-08-09 14:30:58', 2),
(232, 26, '306013', '90346566', 375.00, '2024-08-09 14:42:44', 3),
(233, 35, 'Z2133', '3420087', 90.00, '2024-08-09 17:58:27', 2),
(234, 26, '306013', '90345666', 250.00, '2024-08-09 20:19:09', 3),
(235, 36, 'Z1278', '6209223', 250.00, '2024-08-12 11:17:04', 2),
(236, 27, '306013', 'PG90346566', 150.00, '2024-08-12 12:21:29', 3),
(237, 35, 'Z2133', '3420087', 80.00, '2024-08-12 12:24:47', 2),
(238, 20, '3418195', 'Z2024', 32.00, '2024-08-12 12:32:07', 4),
(239, 26, '306013', '90346566', 300.00, '2024-08-12 12:33:51', 3),
(240, 19, '3418190', 'Z2024', 32.00, '2024-08-12 13:13:18', 4),
(241, 19, 'Z2139', '3418197', 656.00, '2024-08-12 14:22:22', 4),
(242, 19, 'Z2139', '3418197', 656.00, '2024-08-12 14:22:32', 4),
(243, 8, '305955', '3417058', 6000.00, '2024-08-12 15:09:49', 1),
(244, 8, '305955', '3417058', 6000.00, '2024-08-12 15:09:53', 1),
(245, 36, 'Z2171', '3419370', 175.00, '2024-08-12 17:05:43', 2),
(246, 19, 'Z2139', '3418197', 650.00, '2024-08-12 17:16:53', 4),
(247, 8, '306018', '3419685', 7500.00, '2024-08-12 18:19:54', 1),
(248, 35, 'Z2172', '3413493', 150.00, '2024-08-12 18:45:06', 2),
(249, 26, '306013', '90346566', 250.00, '2024-08-13 02:25:57', 3),
(250, 26, '306013', '90346566', 250.00, '2024-08-13 02:26:03', 3),
(251, 26, '306013', '90346566', 250.00, '2024-08-13 02:26:11', 3),
(252, 27, '306013', 'PG90346566', 150.00, '2024-08-13 11:32:35', 3),
(253, 19, 'Z2139', '3418197', 534.00, '2024-08-13 11:41:45', 4),
(254, 26, '306013', '90346566', 375.00, '2024-08-13 12:02:53', 3),
(255, 24, '306016', '90327150', 500.00, '2024-08-13 12:54:38', 3),
(256, 19, 'Z2139', '3418197', 534.00, '2024-08-13 16:41:39', 4),
(257, 36, 'Z1278 ', '6209223', 250.00, '2024-08-13 16:43:02', 2),
(258, 19, 'Z2139', '3418197', 534.00, '2024-08-13 18:07:31', 4),
(259, 24, '306016', '90327150', 500.00, '2024-08-13 20:33:34', 3),
(260, 24, '306016', '90327150', 500.00, '2024-08-13 21:22:00', 3),
(261, 24, '306016', '90327150', 500.00, '2024-08-13 22:35:06', 3),
(262, 1, '305955', '34170058', 9000.00, '2024-08-14 11:14:55', 1),
(263, 9, '305978', '3419656', 9000.00, '2024-08-14 11:19:25', 1),
(264, 36, 'Z1278', '6209223', 250.00, '2024-08-14 11:26:15', 2),
(265, 26, '306013', '90346566', 350.00, '2024-08-14 11:49:35', 3),
(266, 35, 'Z2133', '3420087', 70.00, '2024-08-14 12:41:57', 2),
(267, 26, '306013', '90346566', 375.00, '2024-08-14 13:42:40', 3),
(268, 8, '305978', '3419655', 15000.00, '2024-08-14 13:43:10', 1),
(269, 24, '306016', '90327150', 500.00, '2024-08-14 15:29:58', 3),
(270, 24, '306016', '90327150', 500.00, '2024-08-14 17:32:55', 3),
(271, 27, '306013', 'PG90346566', 150.00, '2024-08-15 11:33:38', 3),
(272, 8, '306039', '3419402', 14700.00, '2024-08-15 11:56:01', 1),
(273, 1, '3466', 'dgfdB55', 33.00, '2025-03-11 19:59:34', 1),
(274, 1, '3466', 'dgfdB55', 5555.00, '2025-03-11 19:59:59', 1),
(275, 1, '	5454', '4545', 5.00, '2025-03-12 15:38:19', 1),
(276, 1, 'sgs', 'sgsggs', 4444.00, '2025-03-12 17:26:09', 1),
(277, 1, '54tg', 'dfgg', 44.00, '2025-03-28 22:36:23', 1),
(278, 1, 'ytujh', 'gjhj', 55.00, '2025-03-31 21:40:47', 1);

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
-- Indices de la tabla `scrap_final`
--
ALTER TABLE `scrap_final`
  ADD PRIMARY KEY (`id`),
  ADD KEY `maquina_id` (`maquina_id`),
  ADD KEY `aprobado_por` (`aprobado_por`),
  ADD KEY `codigo_empleado` (`codigo_empleado`);

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=43;

--
-- AUTO_INCREMENT de la tabla `operacion`
--
ALTER TABLE `operacion`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=601;

--
-- AUTO_INCREMENT de la tabla `registro`
--
ALTER TABLE `registro`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=80;

--
-- AUTO_INCREMENT de la tabla `scrap_final`
--
ALTER TABLE `scrap_final`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT de la tabla `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=173;

--
-- AUTO_INCREMENT de la tabla `velocidad`
--
ALTER TABLE `velocidad`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=279;

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
-- Filtros para la tabla `registro`
--
ALTER TABLE `registro`
  ADD CONSTRAINT `fk_registro_area` FOREIGN KEY (`area_id`) REFERENCES `area` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_registro_codigo_empleado` FOREIGN KEY (`codigo_empleado`) REFERENCES `users` (`codigo_empleado`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_registro_maquina` FOREIGN KEY (`maquina`) REFERENCES `maquinas` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `registro_ibfk_1` FOREIGN KEY (`validado_por`) REFERENCES `users` (`codigo_empleado`);

--
-- Filtros para la tabla `scrap_final`
--
ALTER TABLE `scrap_final`
  ADD CONSTRAINT `scrap_final_ibfk_1` FOREIGN KEY (`codigo_empleado`) REFERENCES `users` (`codigo_empleado`),
  ADD CONSTRAINT `scrap_final_ibfk_2` FOREIGN KEY (`maquina_id`) REFERENCES `maquinas` (`id`),
  ADD CONSTRAINT `scrap_final_ibfk_3` FOREIGN KEY (`aprobado_por`) REFERENCES `users` (`codigo_empleado`);

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
