-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 14-06-2026 a las 21:50:24
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
(1, 10000001, 1, 'Alergia al látex. Evitar guantes de látex en cualquier procedimiento.'),
(2, 10000002, 4, 'Toma anticoagulantes. No realizar procedimientos invasivos sin autorización médica.'),
(3, 10000003, 5, 'Sensibilidad al ácido glicólico. Reacciones de ardor intenso en pruebas previas.'),
(4, 10000004, 6, 'Rosácea diagnosticada. Evitar calor excesivo y vaporizaciones prolongadas.'),
(5, 10000005, 3, 'Rinoplastia en 2022. Sin restricciones actuales para tratamientos faciales.'),
(6, 10000006, 2, 'Diabetes tipo 2 controlada. Precaución con depilación y heridas.'),
(7, 10000007, 1, 'Alergia a la manzanilla. Revisar ingredientes de productos a utilizar.'),
(8, 10000008, 4, 'Isotretinoína (Roacután). No realizar peelings ni exfoliaciones agresivas.');

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
  `estado_cita` varchar(50) DEFAULT NULL,
  `total_Pagar` decimal(10,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `citas`
--

INSERT INTO `citas` (`idCita`, `cedulaEsteticista`, `cedulaCliente`, `hora_cita`, `fecha_cita`, `estado_cita`, `total_Pagar`) VALUES
(1, 22222222, 10000001, '09:00:00', '2024-05-06', 'Completada', 95.00),
(2, 33333333, 10000002, '10:30:00', '2024-05-07', 'Completada', 35.00),
(3, 55555555, 10000003, '11:00:00', '2024-05-08', 'Completada', 43.00),
(4, 66666666, 10000004, '14:00:00', '2024-05-09', 'Completada', 125.00),
(5, 22222222, 10000005, '09:30:00', '2024-05-13', 'Completada', 45.00),
(6, 33333333, 10000006, '15:00:00', '2024-05-14', 'Completada', 80.00),
(7, 55555555, 10000007, '10:00:00', '2024-05-15', 'Completada', 40.00),
(8, 66666666, 10000008, '16:00:00', '2024-05-16', 'Completada', 115.00),
(9, 22222222, 10000001, '09:00:00', '2024-06-03', 'Completada', 65.00),
(10, 33333333, 10000003, '11:30:00', '2024-06-10', 'Completada', 70.00),
(11, 22222222, 10000002, '10:00:00', '2024-06-17', 'Pendiente', 45.00),
(12, 55555555, 10000005, '13:00:00', '2024-06-20', 'Pendiente', 58.00);

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
(10000001, 'Andrea Daza', '1995-04-10', 'Miranda', 'Sucre', 'Petare'),
(10000002, 'Sofía Flore', '1988-09-23', 'Caracas', 'Libertador', 'El Paraíso'),
(10000003, 'Camila Zambrano', '2000-01-15', 'Miranda', 'Baruta', 'Las Minas'),
(10000004, 'Isabella Carrilo', '1993-06-30', 'Caracas', 'Libertador', 'La Candelaria'),
(10000005, 'Daniela Perez', '1997-12-05', 'Miranda', 'Chacao', 'Los Palos Grandes'),
(10000006, 'Valeria Morillo', '1985-08-18', 'Aragua', 'Girardot', 'El Limón'),
(10000007, 'Gabriela Garcia', '2001-03-27', 'Carabobo', 'Valencia', 'Naguanagua'),
(10000008, 'Natalia Mendoza', '1990-11-11', 'Miranda', 'Sucre', 'La Dolorita');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `detalleproductorservicio`
--

CREATE TABLE `detalleproductorservicio` (
  `idServicio` int(11) NOT NULL,
  `idProducto` int(11) NOT NULL,
  `cantidad` int(11) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `detalleproductorservicio`
--

INSERT INTO `detalleproductorservicio` (`idServicio`, `idProducto`, `cantidad`) VALUES
(1, 1, 1),
(1, 11, 1),
(2, 2, 1),
(2, 4, 1),
(3, 3, 1),
(4, 2, 1),
(4, 4, 1),
(5, 5, 1),
(6, 5, 2),
(7, 6, 1),
(8, 6, 1),
(8, 7, 1),
(9, 8, 1),
(10, 8, 1),
(10, 12, 1),
(11, 9, 1),
(12, 10, 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `detalle_citas`
--

CREATE TABLE `detalle_citas` (
  `idServicio` int(11) NOT NULL,
  `idCita` int(11) NOT NULL,
  `precioEstatico` decimal(10,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `detalle_citas`
--

INSERT INTO `detalle_citas` (`idServicio`, `idCita`, `precioEstatico`) VALUES
(1, 1, 45.00),
(1, 5, 45.00),
(1, 11, 45.00),
(2, 1, 50.00),
(2, 10, 50.00),
(3, 9, 65.00),
(4, 6, 80.00),
(5, 10, 20.00),
(6, 2, 35.00),
(7, 4, 55.00),
(7, 8, 55.00),
(8, 4, 70.00),
(9, 3, 18.00),
(9, 12, 18.00),
(10, 3, 25.00),
(11, 7, 40.00),
(11, 12, 40.00),
(12, 8, 60.00);

--
-- Disparadores `detalle_citas`
--
DELIMITER $$
CREATE TRIGGER `trg_detallecitas_after_delete` AFTER DELETE ON `detalle_citas` FOR EACH ROW BEGIN
    UPDATE Citas
    SET total_Pagar = (
        SELECT COALESCE(SUM(precioEstatico), 0)
        FROM detalle_citas
        WHERE idCita = OLD.idCita
    )
    WHERE idCita = OLD.idCita;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `trg_detallecitas_after_insert` AFTER INSERT ON `detalle_citas` FOR EACH ROW BEGIN
    UPDATE Citas
    SET total_Pagar = (
        SELECT COALESCE(SUM(precioEstatico), 0)
        FROM detalle_citas
        WHERE idCita = NEW.idCita
    )
    WHERE idCita = NEW.idCita;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `trg_detallecitas_after_update` AFTER UPDATE ON `detalle_citas` FOR EACH ROW BEGIN
    UPDATE Citas
    SET total_Pagar = (
        SELECT COALESCE(SUM(precioEstatico), 0)
        FROM detalle_citas
        WHERE idCita = NEW.idCita
    )
    WHERE idCita = NEW.idCita;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `diagnostico`
--

CREATE TABLE `diagnostico` (
  `IdDiagnostico` int(11) NOT NULL,
  `cedulaCliente` int(11) NOT NULL,
  `idPiel` int(11) NOT NULL,
  `fecha_diagnostico` date DEFAULT NULL,
  `Descripcion_diagnostico` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `diagnostico`
--

INSERT INTO `diagnostico` (`IdDiagnostico`, `cedulaCliente`, `idPiel`, `fecha_diagnostico`, `Descripcion_diagnostico`) VALUES
(1, 10000001, 3, '2024-01-10', 'Piel grasa con tendencia a brotes en zona T. Se recomienda limpieza profunda mensual.'),
(2, 10000002, 4, '2024-01-15', 'Piel mixta con sequedad en mejillas y exceso de grasa en nariz y frente.'),
(3, 10000003, 5, '2024-02-03', 'Piel sensible con rojeces frecuentes. Evitar productos con alcohol.'),
(4, 10000004, 2, '2024-02-20', 'Piel seca con descamación leve. Se recomienda hidratación intensiva.'),
(5, 10000005, 1, '2024-03-05', 'Piel normal en buen estado. Mantenimiento preventivo recomendado.'),
(6, 10000006, 6, '2024-03-18', 'Piel madura con líneas de expresión marcadas. Tratamiento antienvejecimiento sugerido.'),
(7, 10000007, 3, '2024-04-02', 'Piel grasa con comedones abiertos. Plan de tratamiento de 3 sesiones.'),
(8, 10000008, 4, '2024-04-15', 'Piel mixta con manchas solares leves en zona de mejillas.');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `esteticista`
--

CREATE TABLE `esteticista` (
  `cedulaEsteticista` int(11) NOT NULL,
  `nombreEsteticista` varchar(100) NOT NULL,
  `correoElectronico` varchar(100) DEFAULT NULL,
  `especialidad` varchar(100) DEFAULT NULL,
  `fechaNacimiento` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `esteticista`
--

INSERT INTO `esteticista` (`cedulaEsteticista`, `nombreEsteticista`, `correoElectronico`, `especialidad`, `fechaNacimiento`) VALUES
(22222222, 'Valentina Rojas', 'vrojas@carmin.com', 'Tratamientos Faciales', '1990-03-15'),
(33333333, 'Mariangel Pérez', 'mperez@carmin.com', 'Depilación y Corporales', '1988-07-22'),
(55555555, 'Luisana Castillo', 'lcastillo@carmin.com', 'Uñas y Nail Art', '1995-11-08'),
(66666666, 'Génesis Hernández', 'ghernandez@carmin.com', 'Masajes y Relajación', '1992-05-30');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `metod_pago`
--

CREATE TABLE `metod_pago` (
  `idMetodoPago` int(11) NOT NULL,
  `nom_MetodoPago` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `metod_pago`
--

INSERT INTO `metod_pago` (`idMetodoPago`, `nom_MetodoPago`) VALUES
(1, 'Efectivo'),
(2, 'Tarjeta de Débito'),
(3, 'Tarjeta de Crédito'),
(4, 'Transferencia Bancaria'),
(5, 'Pago Móvil');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `piel`
--

CREATE TABLE `piel` (
  `idPiel` int(11) NOT NULL,
  `nom_Piel` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `piel`
--

INSERT INTO `piel` (`idPiel`, `nom_Piel`) VALUES
(1, 'Piel Normal'),
(2, 'Piel Seca'),
(3, 'Piel Grasa'),
(4, 'Piel Mixta'),
(5, 'Piel Sensible'),
(6, 'Piel Madura');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `producto`
--

CREATE TABLE `producto` (
  `idProducto` int(11) NOT NULL,
  `nombreProducto` varchar(100) NOT NULL,
  `marca` varchar(100) DEFAULT NULL,
  `precioProducto` decimal(10,2) NOT NULL,
  `idProveedor` int(11) NOT NULL,
  `cantidadActual` int(11) DEFAULT 0,
  `tipoProducto` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `producto`
--

INSERT INTO `producto` (`idProducto`, `nombreProducto`, `marca`, `precioProducto`, `idProveedor`, `cantidadActual`, `tipoProducto`) VALUES
(1, 'Gel Limpiador Facial', 'DermoClean', 12.00, 1, 30, 'Limpiador'),
(2, 'Sérum de Ácido Hialurónico', 'HydraBoost', 25.00, 1, 20, 'Sérum'),
(3, 'Ácido Glicólico 30%', 'ChemPeel Pro', 18.00, 5, 15, 'Peeling'),
(4, 'Crema Antienvejecimiento SPF50', 'AgeDefyX', 30.00, 2, 25, 'Crema'),
(5, 'Cera Depilatoria Caliente', 'SmoothWax', 8.00, 2, 50, 'Depilación'),
(6, 'Aceite de Masaje Relajante', 'AromaWell', 15.00, 4, 40, 'Masaje'),
(7, 'Gel Reductor Anticelulítico', 'SlimShape', 22.00, 4, 18, 'Corporal'),
(8, 'Esmalte Tradicional', 'NailColor', 5.00, 3, 60, 'Uñas'),
(9, 'Gel UV para Uñas', 'NailPro Gel', 14.00, 3, 35, 'Uñas'),
(10, 'Mascarilla de Keratina', 'SilkHair Pro', 20.00, 1, 22, 'Capilar'),
(11, 'Tónico Facial Equilibrante', 'DermoClean', 10.00, 1, 28, 'Tónico'),
(12, 'Crema Hidratante Corporal', 'BellaSkin', 16.00, 2, 33, 'Hidratante');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `proveedor`
--

CREATE TABLE `proveedor` (
  `idProveedor` int(11) NOT NULL,
  `rif` varchar(20) NOT NULL,
  `nombreProveedor` varchar(100) NOT NULL,
  `estadoDirProvee` varchar(100) DEFAULT NULL,
  `municipioDirProvee` varchar(100) DEFAULT NULL,
  `parroquiaDirProvee` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `proveedor`
--

INSERT INTO `proveedor` (`idProveedor`, `rif`, `nombreProveedor`, `estadoDirProvee`, `municipioDirProvee`, `parroquiaDirProvee`) VALUES
(1, 'J-301234567', 'Dermocosméticos Elite C.A.', 'Caracas', 'Libertador', 'Altagracia'),
(2, 'J-402345678', 'BellaSkin Venezuela S.A.', 'Miranda', 'Baruta', 'Las Mercedes'),
(3, 'J-503456789', 'NailPro Distribuciones C.A.', 'Carabobo', 'Valencia', 'Centro'),
(4, 'J-604567890', 'AromaWell Spa Supplies S.R.L.', 'Aragua', 'Girardot', 'Cana de Azucar'),
(5, 'J-705678901', 'ProEstetica Internacional C.A.', 'Caracas', 'Chacao', 'Los Palos Grandes');

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
(3, 'Recepcionista');

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
(1, 'Limpieza Facial Profunda', 45.00, 'Limpieza completa del rostro con extracción de comedones y tonificación.'),
(2, 'Hidratación Facial Intensiva', 50.00, 'Tratamiento hidratante con ácido hialurónico y vitamina C.'),
(3, 'Peeling Químico', 65.00, 'Exfoliación química con ácido glicólico para renovar la piel.'),
(4, 'Tratamiento Antienvejecimiento', 80.00, 'Radiofrecuencia facial y aplicación de sérum antiedad.'),
(5, 'Depilación Facial con Hilo', 20.00, 'Depilación de cejas, labio superior y mentón con hilo.'),
(6, 'Depilación Corporal con Cera', 35.00, 'Depilación de piernas, axilas o bikini con cera caliente.'),
(7, 'Masaje Relajante', 55.00, 'Masaje corporal de 60 minutos con aceites esenciales.'),
(8, 'Masaje Reductor', 70.00, 'Masaje anticelulítico con técnica de drenaje linfático.'),
(9, 'Manicure Tradicional', 18.00, 'Limado, cutículas, exfoliación y esmaltado tradicional.'),
(10, 'Pedicure Completo', 25.00, 'Limpieza, exfoliación, hidratación y esmaltado de pies.'),
(11, 'Uñas en Gel', 40.00, 'Aplicación o reconstrucción de uñas en gel de larga duración.'),
(12, 'Tratamiento Capilar Hidratante', 60.00, 'Mascarilla nutritiva con keratina y sellado con plancha.');

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
(1, 10000001, '0414-6011111'),
(2, 10000002, '0416-6022222'),
(3, 10000002, '0212-5551234'),
(4, 10000003, '0424-6033333'),
(5, 10000004, '0412-6044444'),
(6, 10000005, '0414-6055555'),
(7, 10000006, '0426-6066666'),
(8, 10000007, '0416-6077777'),
(9, 10000008, '0424-6088888');

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
(1, 22222222, '0414-1234567'),
(2, 22222222, '0212-8765432'),
(3, 33333333, '0416-2345678'),
(4, 55555555, '0424-3456789'),
(5, 66666666, '0412-4567890');

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
(1, 1, '0212-4001111'),
(2, 1, '0414-7001111'),
(3, 2, '0212-4002222'),
(4, 3, '0241-4003333'),
(5, 4, '0243-4004444'),
(6, 5, '0212-4005555'),
(7, 5, '0416-8005555');

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
(1, 'Alergia'),
(2, 'Enfermedad Crónica'),
(3, 'Cirugía Estética'),
(4, 'Medicación Actual'),
(5, 'Sensibilidad Química'),
(6, 'Condición Dermatológica');

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
(11111111, 1, 'admin2024$'),
(22222222, 2, 'estetic123'),
(33333333, 2, 'pass_mar!'),
(44444444, 3, 'recep2024');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `venta`
--

CREATE TABLE `venta` (
  `cedulaCliente` int(11) NOT NULL,
  `idProducto` int(11) NOT NULL,
  `idMetodoPago` int(11) NOT NULL,
  `fechaCompra` date NOT NULL,
  `cantidad` int(11) NOT NULL DEFAULT 1,
  `totalVenta` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `venta`
--

INSERT INTO `venta` (`cedulaCliente`, `idProducto`, `idMetodoPago`, `fechaCompra`, `cantidad`, `totalVenta`) VALUES
(10000001, 1, 5, '2024-05-06', 1, 12.00),
(10000001, 2, 5, '2024-05-06', 1, 25.00),
(10000002, 4, 2, '2024-05-07', 1, 30.00),
(10000003, 8, 1, '2024-05-08', 2, 10.00),
(10000004, 6, 4, '2024-05-09', 1, 15.00),
(10000005, 11, 5, '2024-05-13', 1, 10.00),
(10000006, 4, 3, '2024-05-14', 1, 30.00),
(10000007, 9, 2, '2024-05-15', 1, 14.00),
(10000008, 12, 1, '2024-05-16', 1, 16.00);

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `antecedentes`
--
ALTER TABLE `antecedentes`
  ADD PRIMARY KEY (`id_antecedente`),
  ADD KEY `fk_antecedentes_cliente` (`cedulaCliente`),
  ADD KEY `fk_antecedentes_tipo` (`id_tipo_antecedente`);

--
-- Indices de la tabla `citas`
--
ALTER TABLE `citas`
  ADD PRIMARY KEY (`idCita`),
  ADD KEY `fk_citas_esteticista` (`cedulaEsteticista`),
  ADD KEY `fk_citas_cliente` (`cedulaCliente`);

--
-- Indices de la tabla `cliente`
--
ALTER TABLE `cliente`
  ADD PRIMARY KEY (`cedulaCliente`);

--
-- Indices de la tabla `detalleproductorservicio`
--
ALTER TABLE `detalleproductorservicio`
  ADD PRIMARY KEY (`idServicio`,`idProducto`),
  ADD KEY `fk_detprodserv_producto` (`idProducto`);

--
-- Indices de la tabla `detalle_citas`
--
ALTER TABLE `detalle_citas`
  ADD PRIMARY KEY (`idServicio`,`idCita`),
  ADD KEY `fk_detallecitas_cita` (`idCita`);

--
-- Indices de la tabla `diagnostico`
--
ALTER TABLE `diagnostico`
  ADD PRIMARY KEY (`IdDiagnostico`),
  ADD KEY `fk_diagnostico_cliente` (`cedulaCliente`),
  ADD KEY `fk_diagnostico_piel` (`idPiel`);

--
-- Indices de la tabla `esteticista`
--
ALTER TABLE `esteticista`
  ADD PRIMARY KEY (`cedulaEsteticista`);

--
-- Indices de la tabla `metod_pago`
--
ALTER TABLE `metod_pago`
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
  ADD KEY `fk_producto_proveedor` (`idProveedor`);

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
  ADD KEY `fk_telCliente_cliente` (`cedulaCliente`);

--
-- Indices de la tabla `telefonoesteticista`
--
ALTER TABLE `telefonoesteticista`
  ADD PRIMARY KEY (`idTelefonoEsteticista`),
  ADD KEY `fk_telEsteticista_esteticista` (`cedulaEsteticista`);

--
-- Indices de la tabla `telefonoproveedor`
--
ALTER TABLE `telefonoproveedor`
  ADD PRIMARY KEY (`idTelefonoProveedor`),
  ADD KEY `fk_telProveedor_proveedor` (`idProveedor`);

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
  ADD KEY `fk_usuario_rol` (`rol`);

--
-- Indices de la tabla `venta`
--
ALTER TABLE `venta`
  ADD PRIMARY KEY (`cedulaCliente`,`idProducto`),
  ADD KEY `fk_venta_producto` (`idProducto`),
  ADD KEY `fk_venta_metodo` (`idMetodoPago`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `antecedentes`
--
ALTER TABLE `antecedentes`
  MODIFY `id_antecedente` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT de la tabla `citas`
--
ALTER TABLE `citas`
  MODIFY `idCita` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT de la tabla `diagnostico`
--
ALTER TABLE `diagnostico`
  MODIFY `IdDiagnostico` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT de la tabla `metod_pago`
--
ALTER TABLE `metod_pago`
  MODIFY `idMetodoPago` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de la tabla `piel`
--
ALTER TABLE `piel`
  MODIFY `idPiel` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de la tabla `producto`
--
ALTER TABLE `producto`
  MODIFY `idProducto` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT de la tabla `proveedor`
--
ALTER TABLE `proveedor`
  MODIFY `idProveedor` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de la tabla `rol`
--
ALTER TABLE `rol`
  MODIFY `id_rol` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `servicio`
--
ALTER TABLE `servicio`
  MODIFY `idServicio` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT de la tabla `telefonocliente`
--
ALTER TABLE `telefonocliente`
  MODIFY `idTelefonoCliente` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT de la tabla `telefonoesteticista`
--
ALTER TABLE `telefonoesteticista`
  MODIFY `idTelefonoEsteticista` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de la tabla `telefonoproveedor`
--
ALTER TABLE `telefonoproveedor`
  MODIFY `idTelefonoProveedor` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT de la tabla `tipo_antecedente`
--
ALTER TABLE `tipo_antecedente`
  MODIFY `id_tipo_antecedente` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `antecedentes`
--
ALTER TABLE `antecedentes`
  ADD CONSTRAINT `fk_antecedentes_cliente` FOREIGN KEY (`cedulaCliente`) REFERENCES `cliente` (`cedulaCliente`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_antecedentes_tipo` FOREIGN KEY (`id_tipo_antecedente`) REFERENCES `tipo_antecedente` (`id_tipo_antecedente`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `citas`
--
ALTER TABLE `citas`
  ADD CONSTRAINT `fk_citas_cliente` FOREIGN KEY (`cedulaCliente`) REFERENCES `cliente` (`cedulaCliente`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_citas_esteticista` FOREIGN KEY (`cedulaEsteticista`) REFERENCES `esteticista` (`cedulaEsteticista`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `detalleproductorservicio`
--
ALTER TABLE `detalleproductorservicio`
  ADD CONSTRAINT `fk_detprodserv_producto` FOREIGN KEY (`idProducto`) REFERENCES `producto` (`idProducto`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_detprodserv_servicio` FOREIGN KEY (`idServicio`) REFERENCES `servicio` (`idServicio`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `detalle_citas`
--
ALTER TABLE `detalle_citas`
  ADD CONSTRAINT `fk_detallecitas_cita` FOREIGN KEY (`idCita`) REFERENCES `citas` (`idCita`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_detallecitas_servicio` FOREIGN KEY (`idServicio`) REFERENCES `servicio` (`idServicio`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `diagnostico`
--
ALTER TABLE `diagnostico`
  ADD CONSTRAINT `fk_diagnostico_cliente` FOREIGN KEY (`cedulaCliente`) REFERENCES `cliente` (`cedulaCliente`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_diagnostico_piel` FOREIGN KEY (`idPiel`) REFERENCES `piel` (`idPiel`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `producto`
--
ALTER TABLE `producto`
  ADD CONSTRAINT `fk_producto_proveedor` FOREIGN KEY (`idProveedor`) REFERENCES `proveedor` (`idProveedor`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `telefonocliente`
--
ALTER TABLE `telefonocliente`
  ADD CONSTRAINT `fk_telCliente_cliente` FOREIGN KEY (`cedulaCliente`) REFERENCES `cliente` (`cedulaCliente`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `telefonoesteticista`
--
ALTER TABLE `telefonoesteticista`
  ADD CONSTRAINT `fk_telEsteticista_esteticista` FOREIGN KEY (`cedulaEsteticista`) REFERENCES `esteticista` (`cedulaEsteticista`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `telefonoproveedor`
--
ALTER TABLE `telefonoproveedor`
  ADD CONSTRAINT `fk_telProveedor_proveedor` FOREIGN KEY (`idProveedor`) REFERENCES `proveedor` (`idProveedor`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `usuario`
--
ALTER TABLE `usuario`
  ADD CONSTRAINT `fk_usuario_rol` FOREIGN KEY (`rol`) REFERENCES `rol` (`id_rol`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `venta`
--
ALTER TABLE `venta`
  ADD CONSTRAINT `fk_venta_cliente` FOREIGN KEY (`cedulaCliente`) REFERENCES `cliente` (`cedulaCliente`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_venta_metodo` FOREIGN KEY (`idMetodoPago`) REFERENCES `metod_pago` (`idMetodoPago`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_venta_producto` FOREIGN KEY (`idProducto`) REFERENCES `producto` (`idProducto`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
