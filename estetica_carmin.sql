-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 07-06-2026 a las 03:02:17
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
-- Base de datos: `estetica_carmin`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `antecedentes`
--

CREATE TABLE `antecedentes` (
  `id_antecedente` int(10) UNSIGNED NOT NULL,
  `cedulaCliente` varchar(20) NOT NULL,
  `id_tipo_antecedente` int(10) UNSIGNED NOT NULL,
  `descripcion_antecedente` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `citas`
--

CREATE TABLE `citas` (
  `idCita` int(10) UNSIGNED NOT NULL,
  `cedulaEsteticista` varchar(20) NOT NULL,
  `cedulaCliente` varchar(20) NOT NULL,
  `hora` time DEFAULT NULL,
  `fecha_cita` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cliente`
--

CREATE TABLE `cliente` (
  `cedulaCliente` varchar(20) NOT NULL,
  `nombreCliente` varchar(150) NOT NULL,
  `fechaNacimiento` date DEFAULT NULL,
  `estadoDirCliente` varchar(100) DEFAULT NULL,
  `municipioDirCliente` varchar(100) DEFAULT NULL,
  `parroquiaDirCliente` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `compra`
--

CREATE TABLE `compra` (
  `idCompra` int(10) UNSIGNED NOT NULL,
  `cedulaCliente` varchar(20) NOT NULL,
  `metodoPago` varchar(50) DEFAULT NULL,
  `fechaCompra` datetime DEFAULT current_timestamp(),
  `totalCompra` decimal(10,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `detalleproductoproveedor`
--

CREATE TABLE `detalleproductoproveedor` (
  `idProducto` int(10) UNSIGNED NOT NULL,
  `idProveedor` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `detalleproductoservicio`
--

CREATE TABLE `detalleproductoservicio` (
  `idServicio` int(10) UNSIGNED NOT NULL,
  `idProducto` int(10) UNSIGNED NOT NULL,
  `cantidad` int(10) UNSIGNED NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `detalle_citas`
--

CREATE TABLE `detalle_citas` (
  `idServicio` int(10) UNSIGNED NOT NULL,
  `idCita` int(10) UNSIGNED NOT NULL,
  `precioServicio` decimal(10,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `detalle_compra`
--

CREATE TABLE `detalle_compra` (
  `idDetalleCompra` int(10) UNSIGNED NOT NULL,
  `idCompra` int(10) UNSIGNED NOT NULL,
  `idProducto` int(10) UNSIGNED NOT NULL,
  `cantidad` int(10) UNSIGNED NOT NULL,
  `precioCompra` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `diagnostico`
--

CREATE TABLE `diagnostico` (
  `idDiagnostico` int(10) UNSIGNED NOT NULL,
  `cedulaCliente` varchar(20) NOT NULL,
  `idPiel` int(10) UNSIGNED DEFAULT NULL,
  `imagen` text DEFAULT NULL,
  `fecha` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `esteticista`
--

CREATE TABLE `esteticista` (
  `cedula` varchar(20) NOT NULL,
  `nombreEsteticista` varchar(150) NOT NULL,
  `correo` varchar(150) DEFAULT NULL,
  `especialidad` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `producto`
--

CREATE TABLE `producto` (
  `idProducto` int(10) UNSIGNED NOT NULL,
  `nombreProducto` varchar(150) NOT NULL,
  `marca` varchar(100) DEFAULT NULL,
  `precioCosto` decimal(10,2) DEFAULT NULL,
  `disponibilidad` int(11) DEFAULT 0,
  `tipoProducto` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `proveedor`
--

CREATE TABLE `proveedor` (
  `idProveedor` int(10) UNSIGNED NOT NULL,
  `nombreProveedor` varchar(150) NOT NULL,
  `rif` varchar(50) DEFAULT NULL,
  `precioCosto` decimal(10,2) DEFAULT NULL,
  `disponibilidad` int(11) DEFAULT 0,
  `estadoDirProvee` varchar(100) DEFAULT NULL,
  `municipioDirProvee` varchar(100) DEFAULT NULL,
  `parroquiaDirProvee` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `rol`
--

CREATE TABLE `rol` (
  `id_rol` int(10) UNSIGNED NOT NULL,
  `tipo_usuario` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `servicio`
--

CREATE TABLE `servicio` (
  `idServicio` int(10) UNSIGNED NOT NULL,
  `nombreServicio` varchar(150) NOT NULL,
  `precio` decimal(10,2) DEFAULT NULL,
  `descripcion` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `telefonocliente`
--

CREATE TABLE `telefonocliente` (
  `idTelefonoCliente` int(10) UNSIGNED NOT NULL,
  `cedulaCliente` varchar(20) NOT NULL,
  `numTelefono` varchar(30) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `telefonoesteticista`
--

CREATE TABLE `telefonoesteticista` (
  `idTelefonoEsteticista` int(10) UNSIGNED NOT NULL,
  `cedula` varchar(20) NOT NULL,
  `telefono` varchar(30) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `telefonoproveedor`
--

CREATE TABLE `telefonoproveedor` (
  `idTelefonoProveedor` int(10) UNSIGNED NOT NULL,
  `idProveedor` int(10) UNSIGNED NOT NULL,
  `telefono` varchar(30) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tipo_antecedente`
--

CREATE TABLE `tipo_antecedente` (
  `id_tipo_antecedente` int(10) UNSIGNED NOT NULL,
  `nom_tipo_antecedente` varchar(150) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tipo_piel`
--

CREATE TABLE `tipo_piel` (
  `id_tipo_piel` int(10) UNSIGNED NOT NULL,
  `nom_tipoPiel` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuario`
--

CREATE TABLE `usuario` (
  `cedula` varchar(20) NOT NULL,
  `id_rol` int(10) UNSIGNED NOT NULL,
  `clave` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `antecedentes`
--
ALTER TABLE `antecedentes`
  ADD PRIMARY KEY (`id_antecedente`),
  ADD KEY `cedulaCliente` (`cedulaCliente`),
  ADD KEY `id_tipo_antecedente` (`id_tipo_antecedente`);

--
-- Indices de la tabla `citas`
--
ALTER TABLE `citas`
  ADD PRIMARY KEY (`idCita`),
  ADD KEY `cedulaEsteticista` (`cedulaEsteticista`),
  ADD KEY `cedulaCliente` (`cedulaCliente`);

--
-- Indices de la tabla `cliente`
--
ALTER TABLE `cliente`
  ADD PRIMARY KEY (`cedulaCliente`);

--
-- Indices de la tabla `compra`
--
ALTER TABLE `compra`
  ADD PRIMARY KEY (`idCompra`),
  ADD KEY `cedulaCliente` (`cedulaCliente`);

--
-- Indices de la tabla `detalleproductoproveedor`
--
ALTER TABLE `detalleproductoproveedor`
  ADD PRIMARY KEY (`idProducto`,`idProveedor`),
  ADD KEY `idProveedor` (`idProveedor`);

--
-- Indices de la tabla `detalleproductoservicio`
--
ALTER TABLE `detalleproductoservicio`
  ADD PRIMARY KEY (`idServicio`,`idProducto`),
  ADD KEY `idProducto` (`idProducto`);

--
-- Indices de la tabla `detalle_citas`
--
ALTER TABLE `detalle_citas`
  ADD PRIMARY KEY (`idServicio`,`idCita`),
  ADD KEY `idCita` (`idCita`);

--
-- Indices de la tabla `detalle_compra`
--
ALTER TABLE `detalle_compra`
  ADD PRIMARY KEY (`idDetalleCompra`),
  ADD KEY `idCompra` (`idCompra`),
  ADD KEY `idProducto` (`idProducto`);

--
-- Indices de la tabla `diagnostico`
--
ALTER TABLE `diagnostico`
  ADD PRIMARY KEY (`idDiagnostico`),
  ADD KEY `cedulaCliente` (`cedulaCliente`),
  ADD KEY `idPiel` (`idPiel`);

--
-- Indices de la tabla `esteticista`
--
ALTER TABLE `esteticista`
  ADD PRIMARY KEY (`cedula`);

--
-- Indices de la tabla `producto`
--
ALTER TABLE `producto`
  ADD PRIMARY KEY (`idProducto`);

--
-- Indices de la tabla `proveedor`
--
ALTER TABLE `proveedor`
  ADD PRIMARY KEY (`idProveedor`),
  ADD UNIQUE KEY `rif` (`rif`);

--
-- Indices de la tabla `rol`
--
ALTER TABLE `rol`
  ADD PRIMARY KEY (`id_rol`);

--
-- Indices de la tabla `servicio`
--
ALTER TABLE `servicio`
  ADD PRIMARY KEY (`idServicio`);

--
-- Indices de la tabla `telefonocliente`
--
ALTER TABLE `telefonocliente`
  ADD PRIMARY KEY (`idTelefonoCliente`),
  ADD KEY `cedulaCliente` (`cedulaCliente`);

--
-- Indices de la tabla `telefonoesteticista`
--
ALTER TABLE `telefonoesteticista`
  ADD PRIMARY KEY (`idTelefonoEsteticista`),
  ADD KEY `cedula` (`cedula`);

--
-- Indices de la tabla `telefonoproveedor`
--
ALTER TABLE `telefonoproveedor`
  ADD PRIMARY KEY (`idTelefonoProveedor`),
  ADD KEY `idProveedor` (`idProveedor`);

--
-- Indices de la tabla `tipo_antecedente`
--
ALTER TABLE `tipo_antecedente`
  ADD PRIMARY KEY (`id_tipo_antecedente`);

--
-- Indices de la tabla `tipo_piel`
--
ALTER TABLE `tipo_piel`
  ADD PRIMARY KEY (`id_tipo_piel`);

--
-- Indices de la tabla `usuario`
--
ALTER TABLE `usuario`
  ADD PRIMARY KEY (`cedula`),
  ADD KEY `id_rol` (`id_rol`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `antecedentes`
--
ALTER TABLE `antecedentes`
  MODIFY `id_antecedente` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `citas`
--
ALTER TABLE `citas`
  MODIFY `idCita` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `compra`
--
ALTER TABLE `compra`
  MODIFY `idCompra` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `detalle_compra`
--
ALTER TABLE `detalle_compra`
  MODIFY `idDetalleCompra` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `diagnostico`
--
ALTER TABLE `diagnostico`
  MODIFY `idDiagnostico` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `producto`
--
ALTER TABLE `producto`
  MODIFY `idProducto` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `proveedor`
--
ALTER TABLE `proveedor`
  MODIFY `idProveedor` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `rol`
--
ALTER TABLE `rol`
  MODIFY `id_rol` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `servicio`
--
ALTER TABLE `servicio`
  MODIFY `idServicio` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `telefonocliente`
--
ALTER TABLE `telefonocliente`
  MODIFY `idTelefonoCliente` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `telefonoesteticista`
--
ALTER TABLE `telefonoesteticista`
  MODIFY `idTelefonoEsteticista` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `telefonoproveedor`
--
ALTER TABLE `telefonoproveedor`
  MODIFY `idTelefonoProveedor` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `tipo_antecedente`
--
ALTER TABLE `tipo_antecedente`
  MODIFY `id_tipo_antecedente` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `tipo_piel`
--
ALTER TABLE `tipo_piel`
  MODIFY `id_tipo_piel` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `antecedentes`
--
ALTER TABLE `antecedentes`
  ADD CONSTRAINT `antecedentes_ibfk_1` FOREIGN KEY (`cedulaCliente`) REFERENCES `cliente` (`cedulaCliente`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `antecedentes_ibfk_2` FOREIGN KEY (`id_tipo_antecedente`) REFERENCES `tipo_antecedente` (`id_tipo_antecedente`) ON UPDATE CASCADE;

--
-- Filtros para la tabla `citas`
--
ALTER TABLE `citas`
  ADD CONSTRAINT `citas_ibfk_1` FOREIGN KEY (`cedulaEsteticista`) REFERENCES `esteticista` (`cedula`) ON UPDATE CASCADE,
  ADD CONSTRAINT `citas_ibfk_2` FOREIGN KEY (`cedulaCliente`) REFERENCES `cliente` (`cedulaCliente`) ON UPDATE CASCADE;

--
-- Filtros para la tabla `compra`
--
ALTER TABLE `compra`
  ADD CONSTRAINT `compra_ibfk_1` FOREIGN KEY (`cedulaCliente`) REFERENCES `cliente` (`cedulaCliente`) ON UPDATE CASCADE;

--
-- Filtros para la tabla `detalleproductoproveedor`
--
ALTER TABLE `detalleproductoproveedor`
  ADD CONSTRAINT `detalleproductoproveedor_ibfk_1` FOREIGN KEY (`idProducto`) REFERENCES `producto` (`idProducto`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `detalleproductoproveedor_ibfk_2` FOREIGN KEY (`idProveedor`) REFERENCES `proveedor` (`idProveedor`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `detalleproductoservicio`
--
ALTER TABLE `detalleproductoservicio`
  ADD CONSTRAINT `detalleproductoservicio_ibfk_1` FOREIGN KEY (`idServicio`) REFERENCES `servicio` (`idServicio`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `detalleproductoservicio_ibfk_2` FOREIGN KEY (`idProducto`) REFERENCES `producto` (`idProducto`) ON UPDATE CASCADE;

--
-- Filtros para la tabla `detalle_citas`
--
ALTER TABLE `detalle_citas`
  ADD CONSTRAINT `detalle_citas_ibfk_1` FOREIGN KEY (`idServicio`) REFERENCES `servicio` (`idServicio`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `detalle_citas_ibfk_2` FOREIGN KEY (`idCita`) REFERENCES `citas` (`idCita`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `detalle_compra`
--
ALTER TABLE `detalle_compra`
  ADD CONSTRAINT `detalle_compra_ibfk_1` FOREIGN KEY (`idCompra`) REFERENCES `compra` (`idCompra`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `detalle_compra_ibfk_2` FOREIGN KEY (`idProducto`) REFERENCES `producto` (`idProducto`) ON UPDATE CASCADE;

--
-- Filtros para la tabla `diagnostico`
--
ALTER TABLE `diagnostico`
  ADD CONSTRAINT `diagnostico_ibfk_1` FOREIGN KEY (`cedulaCliente`) REFERENCES `cliente` (`cedulaCliente`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `diagnostico_ibfk_2` FOREIGN KEY (`idPiel`) REFERENCES `tipo_piel` (`id_tipo_piel`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Filtros para la tabla `telefonocliente`
--
ALTER TABLE `telefonocliente`
  ADD CONSTRAINT `telefonocliente_ibfk_1` FOREIGN KEY (`cedulaCliente`) REFERENCES `cliente` (`cedulaCliente`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `telefonoesteticista`
--
ALTER TABLE `telefonoesteticista`
  ADD CONSTRAINT `telefonoesteticista_ibfk_1` FOREIGN KEY (`cedula`) REFERENCES `esteticista` (`cedula`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `telefonoproveedor`
--
ALTER TABLE `telefonoproveedor`
  ADD CONSTRAINT `telefonoproveedor_ibfk_1` FOREIGN KEY (`idProveedor`) REFERENCES `proveedor` (`idProveedor`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `usuario`
--
ALTER TABLE `usuario`
  ADD CONSTRAINT `usuario_ibfk_1` FOREIGN KEY (`id_rol`) REFERENCES `rol` (`id_rol`) ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
