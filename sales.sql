-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 31-07-2025 a las 05:11:17
-- Versión del servidor: 10.4.32-MariaDB
-- Versión de PHP: 8.1.25

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `sv_facturacion_plan_02`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sales`
--

CREATE TABLE `sales` (
  `id` bigint(20) NOT NULL,
  `serie` varchar(50) DEFAULT NULL,
  `correlativo` bigint(20) UNSIGNED DEFAULT NULL,
  `n_operacion` varchar(125) DEFAULT NULL,
  `user_id` bigint(20) NOT NULL,
  `client_id` bigint(20) NOT NULL,
  `type_client` smallint(6) NOT NULL DEFAULT 1 COMMENT '1 es cliente final, 2 es cliente empresa',
  `currency` varchar(15) NOT NULL DEFAULT '''S/.''',
  `subtotal` double NOT NULL DEFAULT 0,
  `total` double NOT NULL DEFAULT 0,
  `is_exportacion` tinyint(1) UNSIGNED NOT NULL DEFAULT 0,
  `discount` double DEFAULT 0,
  `discount_global` double NOT NULL DEFAULT 0,
  `n_comprobante_anticipo` varchar(150) DEFAULT NULL,
  `amount_anticipo` double DEFAULT NULL,
  `igv` double NOT NULL,
  `igv_discount_general` double UNSIGNED DEFAULT 0,
  `type_payment` tinyint(1) UNSIGNED NOT NULL DEFAULT 1 COMMENT '1 es al contado, 2 credito',
  `state_sale` smallint(6) NOT NULL DEFAULT 1 COMMENT '1 es venta, 2 es cotizacion',
  `state_payment` smallint(6) NOT NULL DEFAULT 1 COMMENT '1 es pendiente, 2 es parcial, 3 es completo',
  `debt` double NOT NULL DEFAULT 0 COMMENT 'deuda',
  `paid_out` double NOT NULL DEFAULT 0 COMMENT 'pagado o cancelado',
  `retencion_igv` tinyint(1) UNSIGNED NOT NULL DEFAULT 0 COMMENT '1 Retención , 2 Detracción , 3 Percepción',
  `description` text DEFAULT NULL,
  `cdr` varchar(250) DEFAULT NULL,
  `xml` varchar(250) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `sales`
--
ALTER TABLE `sales`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `noperacion_sales` (`n_operacion`),
  ADD UNIQUE KEY `sale_serie_correlativo` (`serie`,`correlativo`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `sales`
--
ALTER TABLE `sales`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1000;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
