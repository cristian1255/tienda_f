-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 28-10-2024 a las 02:09:46
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
-- Base de datos: `tienda-f`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `almacen`
--

CREATE TABLE `almacen` (
  `id` int(11) NOT NULL,
  `producto_id` int(11) DEFAULT NULL,
  `cantidad` int(11) DEFAULT NULL,
  `fecha_actualizacion` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `almacen`
--

INSERT INTO `almacen` (`id`, `producto_id`, `cantidad`, `fecha_actualizacion`) VALUES
(58, 6, 100, '2024-10-23'),
(59, 7, 150, '2024-10-23'),
(60, 8, 200, '2024-10-23'),
(61, 9, 50, '2024-10-23'),
(62, 10, 75, '2024-10-23'),
(63, 11, 120, '2024-10-23'),
(64, 12, 90, '2024-10-23'),
(65, 13, 250, '2024-10-23'),
(66, 14, 60, '2024-10-23'),
(67, 15, 80, '2024-10-23'),
(68, 16, 110, '2024-10-23'),
(69, 16, 95, '2024-10-23'),
(70, 17, 230, '2024-10-23'),
(71, 18, 55, '2024-10-23'),
(72, 19, 85, '2024-10-23'),
(73, 20, 130, '2024-10-23');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `categorias`
--

CREATE TABLE `categorias` (
  `id` int(11) NOT NULL,
  `nombre_categoria` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `categorias`
--

INSERT INTO `categorias` (`id`, `nombre_categoria`) VALUES
(1, 'Herramientas'),
(2, 'Accesorios'),
(3, 'Bicicletas'),
(4, 'Pinturas');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `clientes`
--

CREATE TABLE `clientes` (
  `id` int(11) NOT NULL,
  `nombre_cliente` varchar(100) DEFAULT NULL,
  `telefono` varchar(15) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `clientes`
--

INSERT INTO `clientes` (`id`, `nombre_cliente`, `telefono`, `email`) VALUES
(1, 'Juan Pérez', '555-1234', 'juan.perez@example.com'),
(2, 'María López', '555-5678', 'maria.lopez@example.com'),
(3, 'Carlos Ruiz', '555-8765', 'carlos.ruiz@example.com'),
(4, 'Ana Gómez', '555-4321', 'ana.gomez@example.com'),
(5, 'Luis Torres', '555-1357', 'luis.torres@example.com');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `detalles_venta`
--

CREATE TABLE `detalles_venta` (
  `id` int(11) NOT NULL,
  `venta_id` int(11) DEFAULT NULL,
  `producto_id` int(11) DEFAULT NULL,
  `cantidad` int(11) DEFAULT NULL,
  `precio_unitario` decimal(10,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `detalles_venta`
--

INSERT INTO `detalles_venta` (`id`, `venta_id`, `producto_id`, `cantidad`, `precio_unitario`) VALUES
(11, 1, 6, 3, 100.00),
(12, 1, 7, 2, 150.00),
(13, 2, 8, 1, 200.00),
(14, 2, 9, 5, 50.00),
(15, 3, 10, 7, 75.00);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `productos`
--

CREATE TABLE `productos` (
  `id` int(11) NOT NULL,
  `nombre_producto` varchar(50) DEFAULT NULL,
  `categoria_id` int(11) DEFAULT NULL,
  `descripcion` text DEFAULT NULL,
  `proveedor_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `productos`
--

INSERT INTO `productos` (`id`, `nombre_producto`, `categoria_id`, `descripcion`, `proveedor_id`) VALUES
(6, 'Martillo', 1, 'Martillo de acero para construcción', 3),
(7, 'Taladro', 1, 'Taladro eléctrico de 500W', 3),
(8, 'Tornillos', 2, 'Paquete de tornillos de acero', 3),
(9, 'Bicicleta Urbana', 3, 'Bicicleta para uso en la ciudad', 3),
(10, 'Pintura Acrílica', 4, 'Pintura acrílica para interiores', 4),
(11, 'Destornillador', 1, 'Destornillador de precisión', 2),
(12, 'Sierra', 1, 'Sierra circular para madera', 3),
(13, 'Cinta métrica', 1, 'Cinta métrica de 5 metros', 4),
(14, 'Bicicleta de montaña', 3, 'Bicicleta para caminos difíciles', 3),
(15, 'Rodillo de pintura', 4, 'Rodillo de pintura para grandes superficies', 5),
(16, 'Tijeras', 1, 'Tijeras de acero inoxidable', 4),
(17, 'Pincel', 4, 'Pincel para detalles en pintura', 5),
(18, 'Guantes de trabajo', 1, 'Guantes resistentes para trabajo pesado', 4),
(19, 'Lijadora', 1, 'Lijadora eléctrica para acabados', 4),
(20, 'Cuchillo de uso general', 1, 'Cuchillo versátil para todo uso', 1),
(21, 'martillo', 1, 'martillo', 2),
(22, 'martillo', 1, 'martillo', 2),
(23, 'martillo', 1, 'martillo', 2);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `proveedores`
--

CREATE TABLE `proveedores` (
  `id` int(11) NOT NULL,
  `nombre_proveedor` varchar(100) DEFAULT NULL,
  `contacto` varchar(100) DEFAULT NULL,
  `telefono` varchar(15) DEFAULT NULL,
  `direccion` text DEFAULT NULL,
  `created_at` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `proveedores`
--

INSERT INTO `proveedores` (`id`, `nombre_proveedor`, `contacto`, `telefono`, `direccion`, `created_at`) VALUES
(1, 'Proveedor 1', 'Jorge Pérez', '555-1234', 'Calle 123, Ciudad', '2024-10-24'),
(2, 'Proveedor 2', 'Mariana Gómez', '555-5678', 'Calle 456, Ciudad', '2024-10-24'),
(3, 'Proveedor 3', 'Roberto Ruiz', '555-8765', 'Avenida 789, Ciudad', '2024-10-24'),
(4, 'Proveedor 4', 'Ariani Torres', '555-4321', 'Boulevard 321, Ciudad', '2024-10-24'),
(5, 'Proveedor 5', 'Jesus Sánchez', '555-1357', 'Calle 987, Ciudad', '2024-10-24');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuario`
--

CREATE TABLE `usuario` (
  `id` int(11) NOT NULL,
  `nombre_usuario` varchar(100) DEFAULT NULL,
  `contraseña` varchar(100) DEFAULT NULL,
  `perfil` varchar(50) DEFAULT NULL,
  `created_at` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `usuario`
--

INSERT INTO `usuario` (`id`, `nombre_usuario`, `contraseña`, `perfil`, `created_at`) VALUES
(1, 'root', 'root', 'root', '2024-10-23'),
(2, 'lorena', 'lore12', 'secretaria', '2024-10-23'),
(3, 'cristian', 'cris12', 'gerente', '2024-10-23'),
(4, 'jeremy', 'jere12', 'empleado', '2024-10-23');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `ventas`
--

CREATE TABLE `ventas` (
  `id` int(11) NOT NULL,
  `fecha_venta` date DEFAULT NULL,
  `total` decimal(10,2) DEFAULT NULL,
  `cliente_id` int(11) DEFAULT NULL,
  `usuario_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `ventas`
--

INSERT INTO `ventas` (`id`, `fecha_venta`, `total`, `cliente_id`, `usuario_id`) VALUES
(1, '2024-01-01', 100.00, 1, 4),
(2, '2024-01-02', 150.00, 5, 4),
(3, '2024-01-03', 200.00, 3, 4),
(4, '2024-01-04', 75.00, 4, 4),
(5, '2024-01-05', 250.00, 2, 2);

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `almacen`
--
ALTER TABLE `almacen`
  ADD PRIMARY KEY (`id`),
  ADD KEY `producto_id` (`producto_id`);

--
-- Indices de la tabla `categorias`
--
ALTER TABLE `categorias`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `clientes`
--
ALTER TABLE `clientes`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `detalles_venta`
--
ALTER TABLE `detalles_venta`
  ADD PRIMARY KEY (`id`),
  ADD KEY `venta_id` (`venta_id`),
  ADD KEY `producto_id` (`producto_id`);

--
-- Indices de la tabla `productos`
--
ALTER TABLE `productos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `categoria_id` (`categoria_id`),
  ADD KEY `fk_proveedor` (`proveedor_id`);

--
-- Indices de la tabla `proveedores`
--
ALTER TABLE `proveedores`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `usuario`
--
ALTER TABLE `usuario`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `ventas`
--
ALTER TABLE `ventas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `cliente_id` (`cliente_id`),
  ADD KEY `fk_usuario` (`usuario_id`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `almacen`
--
ALTER TABLE `almacen`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=78;

--
-- AUTO_INCREMENT de la tabla `categorias`
--
ALTER TABLE `categorias`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de la tabla `clientes`
--
ALTER TABLE `clientes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de la tabla `detalles_venta`
--
ALTER TABLE `detalles_venta`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT de la tabla `productos`
--
ALTER TABLE `productos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT de la tabla `proveedores`
--
ALTER TABLE `proveedores`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de la tabla `usuario`
--
ALTER TABLE `usuario`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de la tabla `ventas`
--
ALTER TABLE `ventas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `almacen`
--
ALTER TABLE `almacen`
  ADD CONSTRAINT `almacen_ibfk_1` FOREIGN KEY (`producto_id`) REFERENCES `productos` (`id`);

--
-- Filtros para la tabla `detalles_venta`
--
ALTER TABLE `detalles_venta`
  ADD CONSTRAINT `detalles_venta_ibfk_1` FOREIGN KEY (`venta_id`) REFERENCES `ventas` (`id`),
  ADD CONSTRAINT `detalles_venta_ibfk_2` FOREIGN KEY (`producto_id`) REFERENCES `productos` (`id`);

--
-- Filtros para la tabla `productos`
--
ALTER TABLE `productos`
  ADD CONSTRAINT `fk_proveedor` FOREIGN KEY (`proveedor_id`) REFERENCES `proveedores` (`id`),
  ADD CONSTRAINT `productos_ibfk_1` FOREIGN KEY (`categoria_id`) REFERENCES `categorias` (`id`);

--
-- Filtros para la tabla `ventas`
--
ALTER TABLE `ventas`
  ADD CONSTRAINT `fk_usuario` FOREIGN KEY (`usuario_id`) REFERENCES `usuario` (`id`),
  ADD CONSTRAINT `ventas_ibfk_1` FOREIGN KEY (`cliente_id`) REFERENCES `clientes` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
