-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 05-07-2024 a las 23:46:02
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
-- Base de datos: `dbasist`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `asignaturas`
--

CREATE TABLE `asignaturas` (
  `id_asignatura` int(11) NOT NULL,
  `asignatura_nombre` varchar(250) NOT NULL,
  `fechaYhora_actualizacion` datetime NOT NULL,
  `fechaYhora_creacion` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `horarios`
--

CREATE TABLE `horarios` (
  `id_horario` int(11) NOT NULL,
  `horario_profesor` int(11) DEFAULT NULL,
  `horario_asignatura` int(11) NOT NULL,
  `horario_hora` time NOT NULL,
  `fechaYhora_actualizacion` datetime NOT NULL,
  `fechaYhora_creacion` datetime NOT NULL,
  `horario_usuario` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `pagos`
--

CREATE TABLE `pagos` (
  `id_pago` int(11) NOT NULL,
  `pago_profesor` int(11) NOT NULL,
  `pago_asignatura` int(11) NOT NULL,
  `pago_horas` varchar(255) NOT NULL,
  `pago_valorHora` varchar(255) NOT NULL,
  `pago_monto` varchar(255) NOT NULL,
  `pago_periodo` varchar(255) NOT NULL,
  `fechaYhora_actualizacion` datetime NOT NULL,
  `fechaYhora_creacion` datetime NOT NULL,
  `horario_usuario` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `planes`
--

CREATE TABLE `planes` (
  `id_plan` int(11) NOT NULL,
  `plan_nombre` varchar(255) NOT NULL,
  `fechaYhora_actualizacion` datetime NOT NULL,
  `fechaYhora_cracion` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `profesores`
--

CREATE TABLE `profesores` (
  `id_profesor` int(11) NOT NULL,
  `profesor_nombre` varchar(255) NOT NULL,
  `profesor_apellido` varchar(255) NOT NULL,
  `profesor_email` varchar(255) NOT NULL,
  `profesor_documento` int(11) NOT NULL,
  `profesor_cede` int(11) NOT NULL,
  `profesor_plan` int(11) NOT NULL,
  `profesor_programa` int(11) NOT NULL,
  `profesor_saldo` varchar(255) NOT NULL,
  `fechaYhora_creacion` datetime NOT NULL,
  `fechaYhora_actualizacion` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `programas`
--

CREATE TABLE `programas` (
  `id_programa` int(11) NOT NULL,
  `programa_nombre` varchar(250) NOT NULL,
  `fechaYhora_actualizacion` datetime NOT NULL,
  `fechaYhora_creacion` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sedes`
--

CREATE TABLE `sedes` (
  `id_sede` int(11) NOT NULL,
  `sede_nombre` varchar(250) NOT NULL,
  `fechaYhora_actualizacion` datetime NOT NULL,
  `fechaYhora_creacion` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `id_usuario` int(11) NOT NULL,
  `usuario_nombre` varchar(2555) NOT NULL,
  `usuario_apellido` varchar(255) NOT NULL,
  `usuario_email` varchar(255) NOT NULL,
  `usuario_documento` varchar(255) NOT NULL,
  `usuario_rol` varchar(255) NOT NULL,
  `usuario_login` varchar(255) NOT NULL,
  `usuario_contraseña` varchar(255) NOT NULL,
  `usuario_fechaYhora_creacion` datetime NOT NULL,
  `usuario_fechaYhora_actualizacion` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `valorhora`
--

CREATE TABLE `valorhora` (
  `id_valorHora` int(11) NOT NULL,
  `valorHora_monto` varchar(250) NOT NULL,
  `fechaYhora_actualizacion` datetime NOT NULL,
  `fechaYhora_creacion` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `asignaturas`
--
ALTER TABLE `asignaturas`
  ADD PRIMARY KEY (`id_asignatura`);

--
-- Indices de la tabla `horarios`
--
ALTER TABLE `horarios`
  ADD PRIMARY KEY (`id_horario`),
  ADD KEY `horario_profesor` (`horario_profesor`),
  ADD KEY `horario_asignatura` (`horario_asignatura`),
  ADD KEY `horario_usuario` (`horario_usuario`);

--
-- Indices de la tabla `pagos`
--
ALTER TABLE `pagos`
  ADD PRIMARY KEY (`id_pago`),
  ADD KEY `pago_profesor` (`pago_profesor`),
  ADD KEY `pago_asignatura` (`pago_asignatura`);

--
-- Indices de la tabla `planes`
--
ALTER TABLE `planes`
  ADD PRIMARY KEY (`id_plan`);

--
-- Indices de la tabla `profesores`
--
ALTER TABLE `profesores`
  ADD PRIMARY KEY (`id_profesor`),
  ADD KEY `profesor_programa` (`profesor_programa`),
  ADD KEY `profesor_plan` (`profesor_plan`),
  ADD KEY `profesor_cede` (`profesor_cede`);

--
-- Indices de la tabla `programas`
--
ALTER TABLE `programas`
  ADD PRIMARY KEY (`id_programa`);

--
-- Indices de la tabla `sedes`
--
ALTER TABLE `sedes`
  ADD PRIMARY KEY (`id_sede`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id_usuario`);

--
-- Indices de la tabla `valorhora`
--
ALTER TABLE `valorhora`
  ADD PRIMARY KEY (`id_valorHora`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `asignaturas`
--
ALTER TABLE `asignaturas`
  MODIFY `id_asignatura` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `horarios`
--
ALTER TABLE `horarios`
  MODIFY `id_horario` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `pagos`
--
ALTER TABLE `pagos`
  MODIFY `id_pago` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `planes`
--
ALTER TABLE `planes`
  MODIFY `id_plan` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `profesores`
--
ALTER TABLE `profesores`
  MODIFY `id_profesor` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `programas`
--
ALTER TABLE `programas`
  MODIFY `id_programa` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `sedes`
--
ALTER TABLE `sedes`
  MODIFY `id_sede` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id_usuario` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `valorhora`
--
ALTER TABLE `valorhora`
  MODIFY `id_valorHora` int(11) NOT NULL AUTO_INCREMENT;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `horarios`
--
ALTER TABLE `horarios`
  ADD CONSTRAINT `horarios_ibfk_1` FOREIGN KEY (`horario_asignatura`) REFERENCES `asignaturas` (`id_asignatura`) ON DELETE NO ACTION ON UPDATE CASCADE,
  ADD CONSTRAINT `horarios_ibfk_2` FOREIGN KEY (`horario_usuario`) REFERENCES `usuarios` (`id_usuario`) ON DELETE NO ACTION ON UPDATE CASCADE;

--
-- Filtros para la tabla `pagos`
--
ALTER TABLE `pagos`
  ADD CONSTRAINT `pagos_ibfk_1` FOREIGN KEY (`pago_profesor`) REFERENCES `profesores` (`id_profesor`) ON DELETE NO ACTION ON UPDATE CASCADE,
  ADD CONSTRAINT `pagos_ibfk_2` FOREIGN KEY (`pago_asignatura`) REFERENCES `asignaturas` (`id_asignatura`) ON DELETE NO ACTION ON UPDATE CASCADE;

--
-- Filtros para la tabla `profesores`
--
ALTER TABLE `profesores`
  ADD CONSTRAINT `profesores_ibfk_1` FOREIGN KEY (`profesor_plan`) REFERENCES `planes` (`id_plan`) ON DELETE NO ACTION ON UPDATE CASCADE,
  ADD CONSTRAINT `profesores_ibfk_2` FOREIGN KEY (`profesor_cede`) REFERENCES `sedes` (`id_sede`) ON DELETE NO ACTION ON UPDATE CASCADE,
  ADD CONSTRAINT `profesores_ibfk_3` FOREIGN KEY (`profesor_programa`) REFERENCES `programas` (`id_programa`) ON DELETE NO ACTION ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
