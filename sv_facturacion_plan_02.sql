-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 11-08-2025 a las 02:00:14
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
-- Estructura de tabla para la tabla `cache`
--

CREATE TABLE `cache` (
  `key` varchar(255) NOT NULL,
  `value` mediumtext NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cache_locks`
--

CREATE TABLE `cache_locks` (
  `key` varchar(255) NOT NULL,
  `owner` varchar(255) NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `categories`
--

CREATE TABLE `categories` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `title` varchar(250) NOT NULL,
  `imagen` varchar(250) NOT NULL,
  `state` tinyint(1) UNSIGNED NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `categories`
--

INSERT INTO `categories` (`id`, `title`, `imagen`, `state`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'AUTOS 2025', 'categories/qGFgwayBNHJLjrOGDo1IUfteenndv5p2ENMkz62N.png', 1, '2025-07-17 02:25:22', '2025-07-17 07:29:12', NULL),
(2, 'SERVICIOS', 'categories/gkgCwaB6WtmjPRiHphAl2mZE6AabAfz0CDOd0i0G.png', 1, '2025-07-17 02:27:07', '2025-07-19 02:27:08', NULL),
(3, 'TECNOLOGIA', 'categories/EN1vhe2t8K2py6aK8LO8wBkDYRB6zsczjUJU3TV4.png', 1, '2025-07-19 02:26:58', '2025-07-19 02:26:58', NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `clients`
--

CREATE TABLE `clients` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(250) DEFAULT NULL,
  `surname` varchar(250) DEFAULT NULL,
  `full_name` varchar(250) NOT NULL,
  `phone` varchar(25) DEFAULT NULL,
  `email` varchar(250) DEFAULT NULL,
  `type_client` tinyint(1) UNSIGNED NOT NULL DEFAULT 1 COMMENT '1 es cliente normal y 2 es empresa',
  `type_document` varchar(150) DEFAULT NULL,
  `n_document` varchar(50) NOT NULL,
  `gender` varchar(1) DEFAULT NULL COMMENT 'M es masculino y F femenino',
  `birth_date` timestamp NULL DEFAULT NULL COMMENT 'fecha de cumple',
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `address` varchar(250) DEFAULT NULL,
  `ubigeo_distrito` varchar(25) DEFAULT NULL,
  `ubigeo_provincia` varchar(25) DEFAULT NULL,
  `ubigeo_region` varchar(25) DEFAULT NULL,
  `distrito` varchar(80) DEFAULT NULL,
  `provincia` varchar(80) DEFAULT NULL,
  `region` varchar(80) DEFAULT NULL,
  `state` tinyint(1) UNSIGNED NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `clients`
--

INSERT INTO `clients` (`id`, `name`, `surname`, `full_name`, `phone`, `email`, `type_client`, `type_document`, `n_document`, `gender`, `birth_date`, `user_id`, `address`, `ubigeo_distrito`, `ubigeo_provincia`, `ubigeo_region`, `distrito`, `provincia`, `region`, `state`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'Jose', 'Mendoza', 'Jose Mendoza', '999999999', 'josemendoza@gmail.com', 1, 'DNI', '74526325', 'M', '1999-06-30 05:00:00', 2, 'Peru', '120302', '1203', '12', 'Perene', 'Chanchamayo', 'Junín', 1, '2025-07-31 01:13:27', '2025-07-31 01:44:14', NULL),
(2, NULL, NULL, 'Distribuidora Surco EIRL', '987444444', 'distribuidorasurco@eirl.com', 2, 'RUC', '205263654', 'M', '2021-06-08 05:00:00', 1, 'Peru', '060702', '0607', '06', 'Chugur', 'Hualgayoc', 'Cajamarca', 1, '2025-07-31 01:22:15', '2025-07-31 01:22:15', NULL),
(3, 'Sofia', 'Gutierrez', 'Sofia Gutierrez', '98562277', 'gutierrezsofia@gmail.com', 1, 'DNI', '74526328', 'M', '1999-07-30 05:00:00', 1, 'Peru', '160302', '1603', '16', 'Parinari', 'Loreto', 'Loreto', 1, '2025-07-31 01:27:50', '2025-07-31 01:44:08', NULL),
(4, 'Enrique 2025', 'Guzman', 'Enrique 2025 Guzman', '94444545', 'enriqueguzman@gmail.com', 1, 'DNI', '41526328', 'M', '2004-06-30 05:00:00', 1, 'Peru', '050403', '0504', '05', 'Huamanguilla', 'Huanta', 'Ayacucho', 1, '2025-07-31 01:29:28', '2025-07-31 01:44:04', NULL),
(5, NULL, NULL, 'TECNOLOGIA SAC 2026', '99888987', 'tecno@gmail.com', 2, 'RUC', '99955555555', 'M', '2015-10-30 05:00:00', 1, 'Peru', '190108', '1901', '19', 'San Francisco de Asís de Yarusyacan', 'Pasco', 'Pasco', 1, '2025-07-31 01:34:10', '2025-07-31 06:45:02', NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `companies`
--

CREATE TABLE `companies` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `razon_social` varchar(250) NOT NULL,
  `razon_social_comercial` varchar(250) NOT NULL,
  `phone` varchar(25) DEFAULT NULL,
  `email` varchar(250) DEFAULT NULL,
  `n_document` varchar(50) NOT NULL,
  `birth_date` timestamp NULL DEFAULT NULL COMMENT 'fecha de cumple',
  `address` varchar(250) DEFAULT NULL,
  `urbanizacion` varchar(250) NOT NULL,
  `cod_local` varchar(150) NOT NULL,
  `ubigeo_distrito` varchar(25) DEFAULT NULL,
  `ubigeo_provincia` varchar(25) DEFAULT NULL,
  `ubigeo_region` varchar(25) DEFAULT NULL,
  `distrito` varchar(80) DEFAULT NULL,
  `provincia` varchar(80) DEFAULT NULL,
  `region` varchar(80) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `companies`
--

INSERT INTO `companies` (`id`, `razon_social`, `razon_social_comercial`, `phone`, `email`, `n_document`, `birth_date`, `address`, `urbanizacion`, `cod_local`, `ubigeo_distrito`, `ubigeo_provincia`, `ubigeo_region`, `distrito`, `provincia`, `region`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'Laravest Sac. 2025', 'Laravest', '958962565', 'echodeveloper960@gmail.com', '20161515648', '2025-04-29 05:00:00', 'Peru', 'Av. Primavera 2711, Santiago de Surco 25884', '0000', '150302', '1503', '15', 'Copa', 'Cajatambo', 'Lima', '2025-04-30 00:37:01', '2025-07-17 08:33:04', NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `uuid` varchar(255) NOT NULL,
  `connection` text NOT NULL,
  `queue` text NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `jobs`
--

CREATE TABLE `jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `queue` varchar(255) NOT NULL,
  `payload` longtext NOT NULL,
  `attempts` tinyint(3) UNSIGNED NOT NULL,
  `reserved_at` int(10) UNSIGNED DEFAULT NULL,
  `available_at` int(10) UNSIGNED NOT NULL,
  `created_at` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `job_batches`
--

CREATE TABLE `job_batches` (
  `id` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `total_jobs` int(11) NOT NULL,
  `pending_jobs` int(11) NOT NULL,
  `failed_jobs` int(11) NOT NULL,
  `failed_job_ids` longtext NOT NULL,
  `options` mediumtext DEFAULT NULL,
  `cancelled_at` int(11) DEFAULT NULL,
  `created_at` int(11) NOT NULL,
  `finished_at` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '0001_01_01_000000_create_users_table', 1),
(2, '0001_01_01_000001_create_cache_table', 1),
(3, '0001_01_01_000002_create_jobs_table', 1),
(4, '2025_07_05_005613_create_personal_access_tokens_table', 1),
(5, '2025_07_05_012507_create_permission_tables', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `model_has_permissions`
--

CREATE TABLE `model_has_permissions` (
  `permission_id` bigint(20) UNSIGNED NOT NULL,
  `model_type` varchar(255) NOT NULL,
  `model_id` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `model_has_roles`
--

CREATE TABLE `model_has_roles` (
  `role_id` bigint(20) UNSIGNED NOT NULL,
  `model_type` varchar(255) NOT NULL,
  `model_id` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `model_has_roles`
--

INSERT INTO `model_has_roles` (`role_id`, `model_type`, `model_id`) VALUES
(1, 'App\\Models\\User', 1),
(6, 'App\\Models\\User', 3);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `password_reset_tokens`
--

CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `permissions`
--

CREATE TABLE `permissions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `guard_name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `permissions`
--

INSERT INTO `permissions` (`id`, `name`, `guard_name`, `created_at`, `updated_at`) VALUES
(1, 'dashboard', 'api', '2025-07-10 05:53:42', '2025-07-10 05:53:42'),
(2, 'register_role', 'api', '2025-07-10 05:53:42', '2025-07-10 05:53:42'),
(3, 'list_role', 'api', '2025-07-10 05:53:42', '2025-07-10 05:53:42'),
(4, 'edit_role', 'api', '2025-07-10 05:53:42', '2025-07-10 05:53:42'),
(5, 'delete_role', 'api', '2025-07-10 05:53:42', '2025-07-10 05:53:42'),
(6, 'register_user', 'api', '2025-07-10 05:53:42', '2025-07-10 05:53:42'),
(7, 'list_user', 'api', '2025-07-10 05:53:42', '2025-07-10 05:53:42'),
(8, 'edit_user', 'api', '2025-07-10 05:53:42', '2025-07-10 05:53:42'),
(9, 'delete_user', 'api', '2025-07-10 05:53:42', '2025-07-10 05:53:42'),
(10, 'register_categorie', 'api', '2025-07-10 05:53:42', '2025-07-10 05:53:42'),
(11, 'edit_categorie', 'api', '2025-07-10 05:53:42', '2025-07-10 05:53:42'),
(12, 'delete_categorie', 'api', '2025-07-10 05:53:42', '2025-07-10 05:53:42'),
(13, 'list_categorie', 'api', '2025-07-10 05:53:42', '2025-07-10 05:53:42'),
(14, 'register_product', 'api', '2025-07-10 05:53:42', '2025-07-10 05:53:42'),
(15, 'list_product', 'api', '2025-07-10 05:53:42', '2025-07-10 05:53:42'),
(16, 'edit_product', 'api', '2025-07-10 05:53:42', '2025-07-10 05:53:42'),
(17, 'delete_product', 'api', '2025-07-10 05:53:42', '2025-07-10 05:53:42'),
(18, 'register_client', 'api', '2025-07-10 05:53:42', '2025-07-10 05:53:42'),
(19, 'list_client', 'api', '2025-07-10 05:53:42', '2025-07-10 05:53:42'),
(20, 'edit_client', 'api', '2025-07-10 05:53:42', '2025-07-10 05:53:42'),
(21, 'delete_client', 'api', '2025-07-10 05:53:42', '2025-07-10 05:53:42'),
(22, 'register_sale', 'api', '2025-07-10 05:53:42', '2025-07-10 05:53:42'),
(23, 'list_sale', 'api', '2025-07-10 05:53:42', '2025-07-10 05:53:42'),
(24, 'edit_sale', 'api', '2025-07-10 05:53:42', '2025-07-10 05:53:42'),
(25, 'delete_sale', 'api', '2025-07-10 05:53:42', '2025-07-10 05:53:42'),
(26, 'register_guia_remision', 'api', '2025-07-10 05:53:42', '2025-07-10 05:53:42'),
(27, 'list_guia_remision', 'api', '2025-07-10 05:53:42', '2025-07-10 05:53:42'),
(28, 'nota_electronica', 'api', '2025-07-10 05:53:42', '2025-07-10 05:53:42'),
(29, 'list_nota_electronica', 'api', '2025-07-10 05:53:42', '2025-07-10 05:53:42');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `personal_access_tokens`
--

CREATE TABLE `personal_access_tokens` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `tokenable_type` varchar(255) NOT NULL,
  `tokenable_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `token` varchar(64) NOT NULL,
  `abilities` text DEFAULT NULL,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `products`
--

CREATE TABLE `products` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `title` varchar(250) NOT NULL,
  `sku` varchar(50) NOT NULL,
  `categorie_id` bigint(20) UNSIGNED NOT NULL,
  `imagen` varchar(250) NOT NULL,
  `price_general` double NOT NULL,
  `price_company` double NOT NULL,
  `description` longtext DEFAULT NULL,
  `is_discount` tinyint(1) UNSIGNED NOT NULL DEFAULT 1 COMMENT '1 es inactivo y 2 activo',
  `max_discount` double NOT NULL DEFAULT 0,
  `disponiblidad` tinyint(1) UNSIGNED NOT NULL DEFAULT 1 COMMENT '1 es venta sin stock , 2 venta con stock',
  `state` tinyint(1) UNSIGNED NOT NULL DEFAULT 1 COMMENT '1 es activo y 2 inactivo',
  `unidad_medida` varchar(25) NOT NULL,
  `stock` double NOT NULL DEFAULT 0,
  `include_igv` tinyint(1) UNSIGNED NOT NULL DEFAULT 1 COMMENT '1 no 2 si',
  `is_icbper` tinyint(1) UNSIGNED NOT NULL DEFAULT 1 COMMENT '1 no y 2 si ',
  `is_ivap` tinyint(1) UNSIGNED NOT NULL DEFAULT 1 COMMENT '1 no y 2 si',
  `percentage_isc` double NOT NULL DEFAULT 0,
  `is_especial_nota` tinyint(1) UNSIGNED NOT NULL DEFAULT 1 COMMENT '1 es no y 2 si',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `products`
--

INSERT INTO `products` (`id`, `title`, `sku`, `categorie_id`, `imagen`, `price_general`, `price_company`, `description`, `is_discount`, `max_discount`, `disponiblidad`, `state`, `unidad_medida`, `stock`, `include_igv`, `is_icbper`, `is_ivap`, `percentage_isc`, `is_especial_nota`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'Placa Madre Asus ZT-124', 'MRAM16', 3, 'products/e3IEOdpDhTbvzP6cfMRmVbkVVlKPWzMdAMrVcgA4.png', 1600, 1500, 'FDSFDSFDS', 2, 45, 1, 1, 'NIU', 5, 2, 1, 1, 0, 0, '2025-07-19 02:29:10', '2025-07-19 02:29:10', NULL),
(3, 'Memoria Ram 16GB 2027', '25101503', 2, 'products/bjDBQ7pHkcCXskNdeT1ziuDmGeRgMmAisRk915WX.png', 150, 120, 'FSDFDS', 2, 20, 1, 1, 'NIU', 5, 1, 1, 1, 0, 0, '2025-07-19 02:32:44', '2025-08-02 02:21:37', NULL),
(4, 'Hyundai Tucson 2025', 'HT2025', 1, 'products/EkQ9zKukWQLpeUrSZNrzwxhzgkjyje4jjb2a1KaO.png', 45000, 43000, 'FDSFDS', 1, 0, 1, 1, 'NIU', 6, 2, 1, 1, 0, 0, '2025-07-19 02:35:34', '2025-08-07 00:58:44', NULL),
(6, 'Arroz Pilado - San Jose', 'ARP2025', 2, 'products/8JRIHRhpkyivpiQ0DpniwfQqYsc8t0czm3Tk8O67.png', 100, 100, 'FDSFDS', 1, 0, 1, 1, 'KGM', 100, 1, 0, 2, 0, 0, '2025-08-02 02:38:23', '2025-08-02 02:38:23', NULL),
(7, 'Bolsa de Plastico - Ben', 'MRAM167', 2, 'products/wYPCnWCFJZ9fK4WpD2vjYiAh3cVfuZqP6Ym4ZdwE.png', 10, 15, 'fdfds', 1, 0, 1, 1, 'NIU', 1, 1, 2, 1, 0, 0, '2025-08-07 01:25:28', '2025-08-07 01:25:28', NULL),
(8, 'Johnnie Walker 2025', 'ZTD23475', 2, 'products/TEdvu23GOmAts79DUvIzChGmYmiVOYzBZEEvxsSf.png', 1000, 1200, 'fdfdsfds', 1, 0, 1, 1, 'TU', 0, 2, 1, 1, 17, 0, '2025-08-07 02:01:29', '2025-08-09 01:25:18', NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `roles`
--

CREATE TABLE `roles` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `guard_name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `roles`
--

INSERT INTO `roles` (`id`, `name`, `guard_name`, `created_at`, `updated_at`) VALUES
(1, 'Super-Admin', 'api', '2025-07-10 05:53:42', '2025-07-10 05:53:42'),
(2, 'Contador', 'api', '2025-07-10 07:42:51', '2025-07-10 07:42:51'),
(3, 'Asesor Comercial', 'api', '2025-07-10 07:46:14', '2025-07-10 07:46:14'),
(5, 'Administrador de Sede', 'api', '2025-07-10 07:50:28', '2025-07-10 08:32:22'),
(6, 'Jefe de Sede', 'api', '2025-07-12 05:51:04', '2025-07-12 05:51:04'),
(7, 'Jefe de Almacen', 'api', '2025-07-12 05:51:29', '2025-07-12 05:51:29'),
(8, 'Cajero de Sede', 'api', '2025-07-12 05:51:56', '2025-07-12 05:51:56');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `role_has_permissions`
--

CREATE TABLE `role_has_permissions` (
  `permission_id` bigint(20) UNSIGNED NOT NULL,
  `role_id` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `role_has_permissions`
--

INSERT INTO `role_has_permissions` (`permission_id`, `role_id`) VALUES
(6, 5),
(6, 6),
(6, 8),
(7, 5),
(7, 6),
(7, 8),
(8, 5),
(8, 6),
(8, 8),
(9, 5),
(9, 6),
(9, 8),
(10, 5),
(10, 6),
(10, 7),
(10, 8),
(11, 5),
(11, 6),
(11, 7),
(11, 8),
(12, 5),
(12, 7),
(12, 8),
(13, 5),
(13, 6),
(13, 7),
(13, 8),
(14, 5),
(14, 7),
(14, 8),
(15, 5),
(15, 8),
(16, 5),
(16, 7),
(17, 5),
(17, 7),
(18, 3),
(18, 5),
(19, 3),
(19, 5),
(20, 3),
(20, 5),
(21, 3),
(21, 5),
(22, 2),
(22, 3),
(23, 2),
(23, 3),
(24, 2),
(24, 3),
(25, 2),
(25, 3),
(26, 2),
(27, 2),
(28, 2),
(29, 2);

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
-- Volcado de datos para la tabla `sales`
--

INSERT INTO `sales` (`id`, `serie`, `correlativo`, `n_operacion`, `user_id`, `client_id`, `type_client`, `currency`, `subtotal`, `total`, `is_exportacion`, `discount`, `discount_global`, `n_comprobante_anticipo`, `amount_anticipo`, `igv`, `igv_discount_general`, `type_payment`, `state_sale`, `state_payment`, `debt`, `paid_out`, `retencion_igv`, `description`, `cdr`, `xml`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1000, 'F001', NULL, NULL, 1, 2, 2, 'S/.', 36440.67797, 43000, 0, 0, 0, NULL, NULL, 6559.32203, 0, 1, 1, 3, 0, 43000, 0, 'FDSFDSF', NULL, NULL, '2025-08-08 21:32:07', '2025-08-08 21:32:07', NULL),
(1001, 'F001', NULL, NULL, 1, 5, 2, 'S/.', 50, 52, 0, 0, 0, NULL, NULL, 2, 0, 1, 1, 3, 0, 52, 0, 'FDSFDSF', NULL, NULL, '2025-08-08 21:33:38', '2025-08-08 21:33:38', NULL),
(1002, 'B001', NULL, NULL, 1, 4, 1, 'S/.', 39491.52542, 46600, 0, 0, 0, NULL, NULL, 7108.47458, 0, 1, 1, 3, 0, 46600, 0, 'fdsfds', NULL, NULL, '2025-08-08 21:36:06', '2025-08-08 21:36:06', NULL),
(1003, 'F001', NULL, NULL, 1, 2, 2, 'S/.', 72881.35593, 86000, 0, 0, 0, NULL, NULL, 13118.64407, 0, 1, 1, 3, 0, 83420, 1, 'dsdadsa', NULL, NULL, '2025-08-08 21:41:03', '2025-08-08 21:41:03', NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sale_details`
--

CREATE TABLE `sale_details` (
  `id` bigint(20) NOT NULL,
  `sale_id` bigint(20) NOT NULL,
  `product_id` bigint(20) NOT NULL,
  `product_categorie_id` bigint(20) NOT NULL,
  `tip_afe_igv` int(3) UNSIGNED NOT NULL DEFAULT 10,
  `per_icbper` double NOT NULL DEFAULT 0,
  `icbper` double NOT NULL DEFAULT 0,
  `percentage_isc` double UNSIGNED NOT NULL DEFAULT 0,
  `isc` double UNSIGNED NOT NULL DEFAULT 0,
  `unidad_medida` varchar(25) NOT NULL,
  `quantity` double NOT NULL DEFAULT 1,
  `price_base` double NOT NULL DEFAULT 0,
  `price_final` double NOT NULL DEFAULT 0,
  `discount` double DEFAULT 0,
  `subtotal` double NOT NULL DEFAULT 0 COMMENT 'Es el precio base * quantity - descuento',
  `igv` double DEFAULT 0,
  `description` text DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `sale_details`
--

INSERT INTO `sale_details` (`id`, `sale_id`, `product_id`, `product_categorie_id`, `tip_afe_igv`, `per_icbper`, `icbper`, `percentage_isc`, `isc`, `unidad_medida`, `quantity`, `price_base`, `price_final`, `discount`, `subtotal`, `igv`, `description`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 1000, 4, 1, 10, 0, 0, 0, 0, 'NIU', 1, 36440.677966, 43000, 0, 36440.677966, 6559.32203388, NULL, '2025-08-08 21:32:07', '2025-08-08 21:32:07', NULL),
(2, 1001, 6, 2, 17, 0, 0, 0, 0, 'KGM', 1, 50, 52, 0, 50, 2, NULL, '2025-08-08 21:33:38', '2025-08-08 21:33:38', NULL),
(3, 1002, 1, 3, 10, 0, 0, 0, 0, 'NIU', 1, 1355.932203, 1600, 0, 1355.932203, 244.06779654, NULL, '2025-08-08 21:36:06', '2025-08-08 21:36:06', NULL),
(4, 1002, 4, 1, 10, 0, 0, 0, 0, 'NIU', 1, 38135.59322, 45000, 0, 38135.59322, 6864.4067796, NULL, '2025-08-08 21:36:06', '2025-08-08 21:36:06', NULL),
(5, 1003, 4, 1, 10, 0, 0, 0, 0, 'NIU', 2, 36440.677966, 43000, 0, 72881.355932, 13118.64406776, NULL, '2025-08-08 21:41:03', '2025-08-08 21:41:03', NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sale_payments`
--

CREATE TABLE `sale_payments` (
  `id` bigint(20) NOT NULL,
  `sale_id` bigint(20) NOT NULL,
  `method_payment` varchar(100) NOT NULL,
  `amount` double NOT NULL,
  `date_payment` timestamp NULL DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `sale_payments`
--

INSERT INTO `sale_payments` (`id`, `sale_id`, `method_payment`, `amount`, `date_payment`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 1000, 'EFECTIVO', 43000, NULL, '2025-08-08 21:32:07', '2025-08-08 21:32:07', NULL),
(2, 1001, 'EFECTIVO', 52, NULL, '2025-08-08 21:33:38', '2025-08-08 21:33:38', NULL),
(3, 1002, 'TRANSFERENCIA', 46600, NULL, '2025-08-08 21:36:06', '2025-08-08 21:36:06', NULL),
(4, 1003, 'EFECTIVO', 83420, NULL, '2025-08-08 21:41:03', '2025-08-08 21:41:03', NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sessions`
--

CREATE TABLE `sessions` (
  `id` varchar(255) NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `payload` longtext NOT NULL,
  `last_activity` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `surname` varchar(250) DEFAULT NULL,
  `email` varchar(255) NOT NULL,
  `role_id` bigint(20) UNSIGNED NOT NULL DEFAULT 1,
  `phone` varchar(20) DEFAULT NULL,
  `avatar` varchar(250) DEFAULT NULL,
  `type_document` varchar(15) DEFAULT NULL,
  `n_document` varchar(15) DEFAULT NULL,
  `gender` varchar(1) NOT NULL DEFAULT 'M' COMMENT 'm o F',
  `state` tinyint(1) UNSIGNED NOT NULL DEFAULT 1 COMMENT '1 es activo y 2 inactivo',
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `users`
--

INSERT INTO `users` (`id`, `name`, `surname`, `email`, `role_id`, `phone`, `avatar`, `type_document`, `n_document`, `gender`, `state`, `email_verified_at`, `password`, `remember_token`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'Jose Jaico', NULL, 'laravest@gmail.com', 1, NULL, NULL, NULL, NULL, 'M', 1, '2025-07-10 05:53:42', '$2y$12$uK/KXIAz4fR6dhjRlZ4a4Oq00wFC1BDXyhT8xvW5xeoQ6x3Rm7UuW', '9StL2uCZ1q', '2025-07-10 05:53:43', '2025-07-10 05:53:43', NULL),
(2, 'Juan 2025', 'Lopez', 'juandiaz@gmail.com', 7, '936254411', 'users/Bz8ZuppDRRzhShaJ8i48iKcVPZCSLssVtqDMkY1K.png', 'DNI', '744444444', 'M', 1, NULL, '$2y$12$jNVLSc70szAlIVGq5NEK5.vlcxAxeOnhvJZyXoLhzrA7RkhlKhQhS', NULL, '2025-07-12 08:17:34', '2025-07-12 08:35:44', NULL),
(3, 'Sofia', 'Gutierrez', 'sofia@gmail.com', 6, '98594445', 'users/1RIYd3BPfDJp4iTF5KdZJNSxNKtxCsGHqCjP0zIp.png', 'DNI', '74828828', 'F', 2, NULL, '$2y$12$Pi1ksEdNP4WFyEh8Pq1FweyNICqRO0KtDP.BopB6TwYavOq2jgfSy', NULL, '2025-07-12 08:20:40', '2025-07-19 05:38:40', NULL);

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `cache`
--
ALTER TABLE `cache`
  ADD PRIMARY KEY (`key`);

--
-- Indices de la tabla `cache_locks`
--
ALTER TABLE `cache_locks`
  ADD PRIMARY KEY (`key`);

--
-- Indices de la tabla `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `clients`
--
ALTER TABLE `clients`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `full_name` (`full_name`),
  ADD UNIQUE KEY `n_document` (`n_document`);

--
-- Indices de la tabla `companies`
--
ALTER TABLE `companies`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- Indices de la tabla `jobs`
--
ALTER TABLE `jobs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `jobs_queue_index` (`queue`);

--
-- Indices de la tabla `job_batches`
--
ALTER TABLE `job_batches`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `model_has_permissions`
--
ALTER TABLE `model_has_permissions`
  ADD PRIMARY KEY (`permission_id`,`model_id`,`model_type`),
  ADD KEY `model_has_permissions_model_id_model_type_index` (`model_id`,`model_type`);

--
-- Indices de la tabla `model_has_roles`
--
ALTER TABLE `model_has_roles`
  ADD PRIMARY KEY (`role_id`,`model_id`,`model_type`),
  ADD KEY `model_has_roles_model_id_model_type_index` (`model_id`,`model_type`);

--
-- Indices de la tabla `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  ADD PRIMARY KEY (`email`);

--
-- Indices de la tabla `permissions`
--
ALTER TABLE `permissions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `permissions_name_guard_name_unique` (`name`,`guard_name`);

--
-- Indices de la tabla `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  ADD KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`);

--
-- Indices de la tabla `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `title` (`title`),
  ADD UNIQUE KEY `sku` (`sku`),
  ADD KEY `product_categories` (`categorie_id`);

--
-- Indices de la tabla `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `roles_name_guard_name_unique` (`name`,`guard_name`);

--
-- Indices de la tabla `role_has_permissions`
--
ALTER TABLE `role_has_permissions`
  ADD PRIMARY KEY (`permission_id`,`role_id`),
  ADD KEY `role_has_permissions_role_id_foreign` (`role_id`);

--
-- Indices de la tabla `sales`
--
ALTER TABLE `sales`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `noperacion_sales` (`n_operacion`),
  ADD UNIQUE KEY `sale_serie_correlativo` (`serie`,`correlativo`);

--
-- Indices de la tabla `sale_details`
--
ALTER TABLE `sale_details`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `sale_payments`
--
ALTER TABLE `sale_payments`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sessions_user_id_index` (`user_id`),
  ADD KEY `sessions_last_activity_index` (`last_activity`);

--
-- Indices de la tabla `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`),
  ADD KEY `restric_role_id` (`role_id`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `categories`
--
ALTER TABLE `categories`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `clients`
--
ALTER TABLE `clients`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de la tabla `companies`
--
ALTER TABLE `companies`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `jobs`
--
ALTER TABLE `jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de la tabla `permissions`
--
ALTER TABLE `permissions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=30;

--
-- AUTO_INCREMENT de la tabla `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `products`
--
ALTER TABLE `products`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT de la tabla `roles`
--
ALTER TABLE `roles`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT de la tabla `sales`
--
ALTER TABLE `sales`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1004;

--
-- AUTO_INCREMENT de la tabla `sale_details`
--
ALTER TABLE `sale_details`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de la tabla `sale_payments`
--
ALTER TABLE `sale_payments`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `model_has_permissions`
--
ALTER TABLE `model_has_permissions`
  ADD CONSTRAINT `model_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `model_has_roles`
--
ALTER TABLE `model_has_roles`
  ADD CONSTRAINT `model_has_roles_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `products`
--
ALTER TABLE `products`
  ADD CONSTRAINT `product_categories` FOREIGN KEY (`categorie_id`) REFERENCES `categories` (`id`);

--
-- Filtros para la tabla `role_has_permissions`
--
ALTER TABLE `role_has_permissions`
  ADD CONSTRAINT `role_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `role_has_permissions_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `restric_role_id` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
