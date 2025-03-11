-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: localhost
-- Tiempo de generación: 08-03-2025 a las 04:26:38
-- Versión del servidor: 10.4.28-MariaDB
-- Versión de PHP: 8.0.28

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `pa12025`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tprivilegios`
--

CREATE TABLE `tprivilegios` (
  `id` int(11) NOT NULL,
  `nombre` varchar(50) NOT NULL,
  `url` varchar(100) NOT NULL,
  `icono` varchar(75) NOT NULL,
  `estado` int(1) NOT NULL COMMENT '1: activo 2: eliminado'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `tprivilegios`
--

INSERT INTO `tprivilegios` (`id`, `nombre`, `url`, `icono`, `estado`) VALUES
(1, 'Privilegios', 'modulos/configuracion/privilegios.php', 'fa-cog', 1),
(2, 'Usuarios', 'modulos/usuarios/usuarios.php', 'fa-user', 1),
(3, 'Proyectos', 'modulos/proyectos/proyectos.php', 'fa-list', 1),
(4, 'Tareas', 'modulos/proyectos/tareas.php', 'fa-bars', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tprivilegiosusuario`
--

CREATE TABLE `tprivilegiosusuario` (
  `idUsuario` int(11) NOT NULL,
  `idPrivilegio` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `tprivilegiosusuario`
--

INSERT INTO `tprivilegiosusuario` (`idUsuario`, `idPrivilegio`) VALUES
(1, 2),
(1, 3),
(1, 4),
(1, 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tusuarios`
--

CREATE TABLE `tusuarios` (
  `id` int(11) NOT NULL,
  `identificacion` varchar(20) NOT NULL,
  `nombre` varchar(25) NOT NULL,
  `apellidos` varchar(75) NOT NULL,
  `telefono` varchar(20) NOT NULL,
  `email` varchar(100) NOT NULL,
  `usuario` varchar(15) NOT NULL,
  `password` varchar(270) NOT NULL,
  `direccion` text NOT NULL,
  `fechaNac` varchar(20) NOT NULL,
  `token` int(10) NOT NULL,
  `lastIP` varchar(15) NOT NULL,
  `estado` int(1) NOT NULL COMMENT '1: activo 2: eliminado'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `tusuarios`
--

INSERT INTO `tusuarios` (`id`, `identificacion`, `nombre`, `apellidos`, `telefono`, `email`, `usuario`, `password`, `direccion`, `fechaNac`, `token`, `lastIP`, `estado`) VALUES
(1, '113060863', 'Guillermo', 'Mora Granados', '21341234', 'asfasdfasfa', 'gmora', '03ac674216f3e15c761ee1a5e255f067953623c8b388b4459e13f978d7c846f4', 'asdfasdf', '', 221667, '::1', 1),
(2, '115990930', 'David', 'Mora Granados', '88888888', 'david@gmail.com', 'dmora', '03ac674216f3e15c761ee1a5e255f067953623c8b388b4459e13f978d7c846f4', 'no se', '', 0, '', 1),
(3, '0000000', 'Elci', 'Garro Mata', '1345561', 'fasfasdf', 'asdfa', 'b55126a39f9b1170a32e6f61e4a694c45235e5ac11c05ecd6ff6395de6a11187', 'asdfas', '', 0, '', 2),
(4, '2316535135', 'Guillermo', 'Perez Oso ', '3135435', 'asdfasfas', 'memo', '03ac674216f3e15c761ee1a5e255f067953623c8b388b4459e13f978d7c846f4', 'fasfa', '', 188249, '::1', 1);

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `tprivilegios`
--
ALTER TABLE `tprivilegios`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `tusuarios`
--
ALTER TABLE `tusuarios`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `tprivilegios`
--
ALTER TABLE `tprivilegios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `tusuarios`
--
ALTER TABLE `tusuarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
