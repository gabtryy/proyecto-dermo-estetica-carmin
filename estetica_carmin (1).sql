-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 21-06-2026 a las 02:29:05
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
  `id_antecedente` int(11) NOT NULL,
  `cedulaCliente` int(11) NOT NULL,
  `id_tipo_antecedente` int(11) NOT NULL,
  `descripcion_antecedente` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `antecedentes`
--

INSERT INTO `antecedentes` (`id_antecedente`, `cedulaCliente`, `id_tipo_antecedente`, `descripcion_antecedente`) VALUES
(1, 23456789, 1, 'Alergia severa al ácido salicílico.'),
(2, 34567890, 2, 'Uso de isotretinoína oral finalizado hace 6 meses.');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `citas`
--

CREATE TABLE `citas` (
  `idCita` int(11) NOT NULL,
  `cedulaEsteticista` int(11) NOT NULL,
  `cedulaCliente` int(11) NOT NULL,
  `hora_cita` time NOT NULL,
  `fecha_cita` date NOT NULL,
  `estado_cita` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `citas`
--

INSERT INTO `citas` (`idCita`, `cedulaEsteticista`, `cedulaCliente`, `hora_cita`, `fecha_cita`, `estado_cita`) VALUES
(1, 87654321, 23456789, '10:00:00', '2026-06-22', 'Confirmada'),
(2, 98765432, 34567890, '14:30:00', '2026-06-23', 'Pendiente');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cliente`
--

CREATE TABLE `cliente` (
  `cedulaCliente` int(11) NOT NULL,
  `nombreCliente` varchar(100) NOT NULL,
  `fechaNacimiento` date DEFAULT NULL,
  `estadoDirCliente` varchar(100) DEFAULT NULL,
  `municipioDirCliente` varchar(100) DEFAULT NULL,
  `parroquiaDirCliente` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `cliente`
--

INSERT INTO `cliente` (`cedulaCliente`, `nombreCliente`, `fechaNacimiento`, `estadoDirCliente`, `municipioDirCliente`, `parroquiaDirCliente`) VALUES
(23456789, 'Laura Valentina Gómez', '1995-08-19', 'Distrito Capital', 'Libertador', 'Altagracia'),
(34567890, 'Andrés Ignacio Pérez', '1990-03-05', 'Miranda', 'Chacao', 'Chacao');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `detalleproductoservicio`
--

CREATE TABLE `detalleproductoservicio` (
  `idServicio` int(11) NOT NULL,
  `idProducto` int(11) NOT NULL,
  `cantidad` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `detalleproductoservicio`
--

INSERT INTO `detalleproductoservicio` (`idServicio`, `idProducto`, `cantidad`) VALUES
(1, 1, 1),
(2, 2, 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `detalle_citas`
--

CREATE TABLE `detalle_citas` (
  `idServicio` int(11) NOT NULL,
  `idCita` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `detalle_citas`
--

INSERT INTO `detalle_citas` (`idServicio`, `idCita`) VALUES
(1, 1),
(2, 2);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `diagnostico`
--

CREATE TABLE `diagnostico` (
  `idDiagnostico` int(11) NOT NULL,
  `cedulaCliente` int(11) NOT NULL,
  `idPiel` int(11) NOT NULL,
  `fecha_diagnostico` date NOT NULL,
  `descripcion_diagnostico` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `diagnostico`
--

INSERT INTO `diagnostico` (`idDiagnostico`, `cedulaCliente`, `idPiel`, `fecha_diagnostico`, `descripcion_diagnostico`) VALUES
(1, 23456789, 2, '2026-03-10', 'Presenta exceso de sebo en la zona T y poros abiertos.'),
(2, 34567890, 4, '2026-04-15', 'Piel reactiva con rojeces en mejillas.');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `especialidad`
--

CREATE TABLE `especialidad` (
  `idEspecialidad` int(11) NOT NULL,
  `nombreEspecialidad` varchar(100) NOT NULL,
  `descripcionEspecialidad` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `especialidad`
--

INSERT INTO `especialidad` (`idEspecialidad`, `nombreEspecialidad`, `descripcionEspecialidad`) VALUES
(1, 'Dermatología Cosmética', 'Cuidado estético y tratamientos de afecciones superficiales.'),
(2, 'Tratamientos Faciales Avanzados', 'Especialista en limpiezas profundas, peeling e hidratación.');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `esteticista`
--

CREATE TABLE `esteticista` (
  `cedulaEsteticista` int(11) NOT NULL,
  `nombreEsteticista` varchar(100) NOT NULL,
  `correoElectronico` varchar(100) DEFAULT NULL,
  `idEspecialidad` int(11) NOT NULL,
  `fechaNacimiento` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `esteticista`
--

INSERT INTO `esteticista` (`cedulaEsteticista`, `nombreEsteticista`, `correoElectronico`, `idEspecialidad`, `fechaNacimiento`) VALUES
(87654321, 'Dra. Elena Rostova', 'elena.rostova@carmin.com', 1, '1988-05-14'),
(98765432, 'Carlos Mendoza', 'carlos.mendoza@carmin.com', 2, '1992-11-22');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `metodo_pago`
--

CREATE TABLE `metodo_pago` (
  `idMetodoPago` int(11) NOT NULL,
  `nom_MetodoPago` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `metodo_pago`
--

INSERT INTO `metodo_pago` (`idMetodoPago`, `nom_MetodoPago`) VALUES
(1, 'Pago Móvil'),
(2, 'Efectivo (USD)'),
(3, 'Zelle');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `piel`
--

CREATE TABLE `piel` (
  `idPiel` int(11) NOT NULL,
  `nom_Piel` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `piel`
--

INSERT INTO `piel` (`idPiel`, `nom_Piel`) VALUES
(1, 'Seca'),
(2, 'Grasa'),
(3, 'Mixta'),
(4, 'Sensible');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `producto`
--

CREATE TABLE `producto` (
  `idProducto` int(11) NOT NULL,
  `nombreProducto` varchar(100) NOT NULL,
  `marca` varchar(50) DEFAULT NULL,
  `precioProducto` decimal(10,2) NOT NULL,
  `idProveedor` int(11) NOT NULL,
  `cantidadActual` int(11) NOT NULL,
  `tipoProducto` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `producto`
--

INSERT INTO `producto` (`idProducto`, `nombreProducto`, `marca`, `precioProducto`, `idProveedor`, `cantidadActual`, `tipoProducto`) VALUES
(1, 'Gel Limpiador Purificante 200ml', 'Bioderma', 25.50, 1, 40, 'Limpiador'),
(2, 'Sérum de Ácido Hialurónico', 'La Roche-Posay', 38.00, 1, 25, 'Suero');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `proveedor`
--

CREATE TABLE `proveedor` (
  `idProveedor` int(11) NOT NULL,
  `rif` varchar(50) NOT NULL,
  `nombreProveedor` varchar(100) NOT NULL,
  `estadoDirProveedor` varchar(100) DEFAULT NULL,
  `municipioDirProveedor` varchar(100) DEFAULT NULL,
  `parroquiaDirProveedor` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `proveedor`
--

INSERT INTO `proveedor` (`idProveedor`, `rif`, `nombreProveedor`, `estadoDirProveedor`, `municipioDirProveedor`, `parroquiaDirProveedor`) VALUES
(1, 'J-12345678-9', 'DermoCosméticos Avanzados C.A.', 'Aragua', 'Girardot', 'Joaquín Crespo'),
(2, 'J-98765432-1', 'Laboratorios Piel Sana S.A.', 'Distrito Capital', 'El Recreo', 'El Recreo');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `rol`
--

CREATE TABLE `rol` (
  `id_rol` int(11) NOT NULL,
  `tipo_usuario` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `rol`
--

INSERT INTO `rol` (`id_rol`, `tipo_usuario`) VALUES
(1, 'Administrador'),
(2, 'Esteticista'),
(3, 'Cliente');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `servicio`
--

CREATE TABLE `servicio` (
  `idServicio` int(11) NOT NULL,
  `nombreServicio` varchar(100) NOT NULL,
  `precio` decimal(10,2) NOT NULL,
  `descripcion` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `servicio`
--

INSERT INTO `servicio` (`idServicio`, `nombreServicio`, `precio`, `descripcion`) VALUES
(1, 'Limpieza Facial Profunda', 45.00, 'Extracción de impurezas y mascarilla hidratante.'),
(2, 'Peeling Químico Renovador', 65.00, 'Aplicación de ácidos controlados para renovación celular.');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `telefonocliente`
--

CREATE TABLE `telefonocliente` (
  `idTelefonoCliente` int(11) NOT NULL,
  `cedulaCliente` int(11) NOT NULL,
  `numTelefonoCliente` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `telefonocliente`
--

INSERT INTO `telefonocliente` (`idTelefonoCliente`, `cedulaCliente`, `numTelefonoCliente`) VALUES
(1, 23456789, '+584165554433'),
(2, 34567890, '+584241110022');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `telefonoesteticista`
--

CREATE TABLE `telefonoesteticista` (
  `idTelefonoEsteticista` int(11) NOT NULL,
  `cedulaEsteticista` int(11) NOT NULL,
  `numTelefonoEsteticista` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `telefonoesteticista`
--

INSERT INTO `telefonoesteticista` (`idTelefonoEsteticista`, `cedulaEsteticista`, `numTelefonoEsteticista`) VALUES
(1, 87654321, '+584121112233'),
(2, 98765432, '+584149998877');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `telefonoproveedor`
--

CREATE TABLE `telefonoproveedor` (
  `idTelefonoProveedor` int(11) NOT NULL,
  `idProveedor` int(11) NOT NULL,
  `numTelefonoProveedor` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `telefonoproveedor`
--

INSERT INTO `telefonoproveedor` (`idTelefonoProveedor`, `idProveedor`, `numTelefonoProveedor`) VALUES
(1, 1, '+582432223344'),
(2, 2, '+582129991122');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tipo_antecedente`
--

CREATE TABLE `tipo_antecedente` (
  `id_tipo_antecedente` int(11) NOT NULL,
  `nom_tipo_antecedente` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `tipo_antecedente`
--

INSERT INTO `tipo_antecedente` (`id_tipo_antecedente`, `nom_tipo_antecedente`) VALUES
(1, 'Alergias'),
(2, 'Tratamientos Previos');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuario`
--

CREATE TABLE `usuario` (
  `cedula` int(11) NOT NULL,
  `rol` int(11) NOT NULL,
  `clave` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `usuario`
--

INSERT INTO `usuario` (`cedula`, `rol`, `clave`) VALUES
(12345678, 1, 'admin123_secure'),
(23456789, 3, 'cliente_secret'),
(87654321, 2, 'esteticista_pass');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `venta`
--

CREATE TABLE `venta` (
  `cedulaCliente` int(11) NOT NULL,
  `idProducto` int(11) NOT NULL,
  `idMetodoPago` int(11) NOT NULL,
  `fechaCompra` date NOT NULL,
  `cantidad` int(11) NOT NULL,
  `totalVenta` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `venta`
--

INSERT INTO `venta` (`cedulaCliente`, `idProducto`, `idMetodoPago`, `fechaCompra`, `cantidad`, `totalVenta`) VALUES
(23456789, 1, 1, '2026-06-18', 1, 25.50),
(34567890, 2, 3, '2026-06-19', 1, 38.00);

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
-- Indices de la tabla `diagnostico`
--
ALTER TABLE `diagnostico`
  ADD PRIMARY KEY (`idDiagnostico`),
  ADD KEY `cedulaCliente` (`cedulaCliente`),
  ADD KEY `idPiel` (`idPiel`);

--
-- Indices de la tabla `especialidad`
--
ALTER TABLE `especialidad`
  ADD PRIMARY KEY (`idEspecialidad`);

--
-- Indices de la tabla `esteticista`
--
ALTER TABLE `esteticista`
  ADD PRIMARY KEY (`cedulaEsteticista`),
  ADD KEY `idEspecialidad` (`idEspecialidad`);

--
-- Indices de la tabla `metodo_pago`
--
ALTER TABLE `metodo_pago`
  ADD PRIMARY KEY (`idMetodoPago`);

--
-- Indices de la tabla `piel`
--
ALTER TABLE `piel`
  ADD PRIMARY KEY (`idPiel`);

--
-- Indices de la tabla `producto`
--
ALTER TABLE `producto`
  ADD PRIMARY KEY (`idProducto`),
  ADD KEY `idProveedor` (`idProveedor`);

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
  ADD KEY `cedulaEsteticista` (`cedulaEsteticista`);

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
-- Indices de la tabla `usuario`
--
ALTER TABLE `usuario`
  ADD PRIMARY KEY (`cedula`),
  ADD KEY `rol` (`rol`);

--
-- Indices de la tabla `venta`
--
ALTER TABLE `venta`
  ADD PRIMARY KEY (`cedulaCliente`,`idProducto`),
  ADD KEY `idProducto` (`idProducto`),
  ADD KEY `idMetodoPago` (`idMetodoPago`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `antecedentes`
--
ALTER TABLE `antecedentes`
  MODIFY `id_antecedente` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `citas`
--
ALTER TABLE `citas`
  MODIFY `idCita` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `diagnostico`
--
ALTER TABLE `diagnostico`
  MODIFY `idDiagnostico` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `especialidad`
--
ALTER TABLE `especialidad`
  MODIFY `idEspecialidad` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `metodo_pago`
--
ALTER TABLE `metodo_pago`
  MODIFY `idMetodoPago` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `piel`
--
ALTER TABLE `piel`
  MODIFY `idPiel` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `producto`
--
ALTER TABLE `producto`
  MODIFY `idProducto` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `proveedor`
--
ALTER TABLE `proveedor`
  MODIFY `idProveedor` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `rol`
--
ALTER TABLE `rol`
  MODIFY `id_rol` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `servicio`
--
ALTER TABLE `servicio`
  MODIFY `idServicio` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `telefonocliente`
--
ALTER TABLE `telefonocliente`
  MODIFY `idTelefonoCliente` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `telefonoesteticista`
--
ALTER TABLE `telefonoesteticista`
  MODIFY `idTelefonoEsteticista` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `telefonoproveedor`
--
ALTER TABLE `telefonoproveedor`
  MODIFY `idTelefonoProveedor` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `tipo_antecedente`
--
ALTER TABLE `tipo_antecedente`
  MODIFY `id_tipo_antecedente` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `antecedentes`
--
ALTER TABLE `antecedentes`
  ADD CONSTRAINT `antecedentes_ibfk_1` FOREIGN KEY (`cedulaCliente`) REFERENCES `cliente` (`cedulaCliente`) ON DELETE CASCADE,
  ADD CONSTRAINT `antecedentes_ibfk_2` FOREIGN KEY (`id_tipo_antecedente`) REFERENCES `tipo_antecedente` (`id_tipo_antecedente`) ON DELETE CASCADE;

--
-- Filtros para la tabla `citas`
--
ALTER TABLE `citas`
  ADD CONSTRAINT `citas_ibfk_1` FOREIGN KEY (`cedulaEsteticista`) REFERENCES `esteticista` (`cedulaEsteticista`) ON DELETE CASCADE,
  ADD CONSTRAINT `citas_ibfk_2` FOREIGN KEY (`cedulaCliente`) REFERENCES `cliente` (`cedulaCliente`) ON DELETE CASCADE;

--
-- Filtros para la tabla `detalleproductoservicio`
--
ALTER TABLE `detalleproductoservicio`
  ADD CONSTRAINT `detalleproductoservicio_ibfk_1` FOREIGN KEY (`idServicio`) REFERENCES `servicio` (`idServicio`) ON DELETE CASCADE,
  ADD CONSTRAINT `detalleproductoservicio_ibfk_2` FOREIGN KEY (`idProducto`) REFERENCES `producto` (`idProducto`) ON DELETE CASCADE;

--
-- Filtros para la tabla `detalle_citas`
--
ALTER TABLE `detalle_citas`
  ADD CONSTRAINT `detalle_citas_ibfk_1` FOREIGN KEY (`idServicio`) REFERENCES `servicio` (`idServicio`) ON DELETE CASCADE,
  ADD CONSTRAINT `detalle_citas_ibfk_2` FOREIGN KEY (`idCita`) REFERENCES `citas` (`idCita`) ON DELETE CASCADE;

--
-- Filtros para la tabla `diagnostico`
--
ALTER TABLE `diagnostico`
  ADD CONSTRAINT `diagnostico_ibfk_1` FOREIGN KEY (`cedulaCliente`) REFERENCES `cliente` (`cedulaCliente`) ON DELETE CASCADE,
  ADD CONSTRAINT `diagnostico_ibfk_2` FOREIGN KEY (`idPiel`) REFERENCES `piel` (`idPiel`) ON DELETE CASCADE;

--
-- Filtros para la tabla `esteticista`
--
ALTER TABLE `esteticista`
  ADD CONSTRAINT `esteticista_ibfk_1` FOREIGN KEY (`idEspecialidad`) REFERENCES `especialidad` (`idEspecialidad`) ON DELETE CASCADE;

--
-- Filtros para la tabla `producto`
--
ALTER TABLE `producto`
  ADD CONSTRAINT `producto_ibfk_1` FOREIGN KEY (`idProveedor`) REFERENCES `proveedor` (`idProveedor`) ON DELETE CASCADE;

--
-- Filtros para la tabla `telefonocliente`
--
ALTER TABLE `telefonocliente`
  ADD CONSTRAINT `telefonocliente_ibfk_1` FOREIGN KEY (`cedulaCliente`) REFERENCES `cliente` (`cedulaCliente`) ON DELETE CASCADE;

--
-- Filtros para la tabla `telefonoesteticista`
--
ALTER TABLE `telefonoesteticista`
  ADD CONSTRAINT `telefonoesteticista_ibfk_1` FOREIGN KEY (`cedulaEsteticista`) REFERENCES `esteticista` (`cedulaEsteticista`) ON DELETE CASCADE;

--
-- Filtros para la tabla `telefonoproveedor`
--
ALTER TABLE `telefonoproveedor`
  ADD CONSTRAINT `telefonoproveedor_ibfk_1` FOREIGN KEY (`idProveedor`) REFERENCES `proveedor` (`idProveedor`) ON DELETE CASCADE;

--
-- Filtros para la tabla `usuario`
--
ALTER TABLE `usuario`
  ADD CONSTRAINT `usuario_ibfk_1` FOREIGN KEY (`rol`) REFERENCES `rol` (`id_rol`) ON DELETE CASCADE;

--
-- Filtros para la tabla `venta`
--
ALTER TABLE `venta`
  ADD CONSTRAINT `venta_ibfk_1` FOREIGN KEY (`cedulaCliente`) REFERENCES `cliente` (`cedulaCliente`) ON DELETE CASCADE,
  ADD CONSTRAINT `venta_ibfk_2` FOREIGN KEY (`idProducto`) REFERENCES `producto` (`idProducto`) ON DELETE CASCADE,
  ADD CONSTRAINT `venta_ibfk_3` FOREIGN KEY (`idMetodoPago`) REFERENCES `metodo_pago` (`idMetodoPago`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
