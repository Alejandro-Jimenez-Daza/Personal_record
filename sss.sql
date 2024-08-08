-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 08-08-2024 a las 17:57:10
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
-- Base de datos: `sss`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `espalda`
--

CREATE TABLE `espalda` (
  `id_espalda` int(11) NOT NULL,
  `usuario_id` int(11) NOT NULL,
  `fecha` date NOT NULL,
  `pull_ups` varchar(20) NOT NULL,
  `cable_row` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `espalda`
--

INSERT INTO `espalda` (`id_espalda`, `usuario_id`, `fecha`, `pull_ups`, `cable_row`) VALUES
(1, 1, '2024-08-01', '25', '63,5');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `pecho`
--

CREATE TABLE `pecho` (
  `id_pecho` int(11) NOT NULL,
  `usuario_id` int(11) NOT NULL,
  `fecha` date NOT NULL,
  `dips` varchar(20) NOT NULL,
  `incline_press` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `pecho`
--

INSERT INTO `pecho` (`id_pecho`, `usuario_id`, `fecha`, `dips`, `incline_press`) VALUES
(1, 1, '2024-08-01', '45', '22');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `persona`
--

CREATE TABLE `persona` (
  `id_persona` int(11) NOT NULL,
  `nombre_persona` varchar(100) DEFAULT NULL,
  `apellido_persona` varchar(100) DEFAULT NULL,
  `edad_persona` varchar(3) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `persona`
--

INSERT INTO `persona` (`id_persona`, `nombre_persona`, `apellido_persona`, `edad_persona`) VALUES
(1, 'Alejandro', 'Jiménez Daza', '22');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `pierna`
--

CREATE TABLE `pierna` (
  `id_pierna` int(11) NOT NULL,
  `usuario_id` int(11) NOT NULL,
  `fecha` date NOT NULL,
  `squat` varchar(20) NOT NULL,
  `hamstring` varchar(20) NOT NULL,
  `walking_lunges` varchar(20) NOT NULL,
  `calf_raises` varchar(20) NOT NULL,
  `other_calf_raises` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `pierna`
--

INSERT INTO `pierna` (`id_pierna`, `usuario_id`, `fecha`, `squat`, `hamstring`, `walking_lunges`, `calf_raises`, `other_calf_raises`) VALUES
(1, 1, '2024-08-01', '136', '108', '10', '99', '22');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `espalda`
--
ALTER TABLE `espalda`
  ADD PRIMARY KEY (`id_espalda`),
  ADD KEY `usuario_id` (`usuario_id`);

--
-- Indices de la tabla `pecho`
--
ALTER TABLE `pecho`
  ADD PRIMARY KEY (`id_pecho`),
  ADD KEY `usuario_id` (`usuario_id`);

--
-- Indices de la tabla `persona`
--
ALTER TABLE `persona`
  ADD PRIMARY KEY (`id_persona`);

--
-- Indices de la tabla `pierna`
--
ALTER TABLE `pierna`
  ADD PRIMARY KEY (`id_pierna`),
  ADD KEY `usuario_id` (`usuario_id`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `espalda`
--
ALTER TABLE `espalda`
  MODIFY `id_espalda` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `pecho`
--
ALTER TABLE `pecho`
  MODIFY `id_pecho` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `persona`
--
ALTER TABLE `persona`
  MODIFY `id_persona` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `pierna`
--
ALTER TABLE `pierna`
  MODIFY `id_pierna` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `espalda`
--
ALTER TABLE `espalda`
  ADD CONSTRAINT `espalda_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `persona` (`id_persona`);

--
-- Filtros para la tabla `pecho`
--
ALTER TABLE `pecho`
  ADD CONSTRAINT `pecho_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `persona` (`id_persona`);

--
-- Filtros para la tabla `pierna`
--
ALTER TABLE `pierna`
  ADD CONSTRAINT `pierna_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `persona` (`id_persona`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
