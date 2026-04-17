-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 17-04-2026 a las 20:45:16
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
-- Base de datos: `somos_propiedad`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `banner`
--

CREATE TABLE `banner` (
  `id` int(11) NOT NULL,
  `titulo` varchar(50) NOT NULL,
  `descripcion` varchar(255) NOT NULL,
  `url_image` varchar(255) NOT NULL,
  `estado` int(11) NOT NULL,
  `orden` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `barrios`
--

CREATE TABLE `barrios` (
  `id` int(11) NOT NULL,
  `barrio` text NOT NULL,
  `codigo` varchar(20) NOT NULL,
  `codigo_municipio` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `barrios`
--

INSERT INTO `barrios` (`id`, `barrio`, `codigo`, `codigo_municipio`) VALUES
(7, 'Barrio Santa Monica', 'eiygklbxegckhck', 81),
(9, 'Sector Calle Nueva', 'quofkvhixbfzyag', 81),
(10, 'Vereda Jamundi', 'aoauvicvrnnynbg', 373),
(12, 'Pepe Sierra I', 'dhljosvzñnforsn', 81),
(13, 'Buenos aires', 'iiwhiqquuappmmx', 81),
(15, 'Leticia', 'mñturtuobssdtgq', 81),
(16, 'Treinta de mayo', 'mavjhkfvtqvgqmg', 81),
(18, 'El portón', 'qmrvvfwhwmazxsy', 81),
(19, 'Santiago de lo Caballeros', 'htñmcncncfvmcbx', 81),
(20, 'Aguas calientes', 'byñenodsrhkwtsc', 81),
(21, 'El progreso', 'suhxwdyipnxñnpl', 81),
(22, 'Morro de la virgen', 'vgiiipqcagbclfa', 81),
(23, 'Pepe Sierra II', 'cfseijkeojnqldn', 81),
(24, 'Cecilia Caballero', 'tioqbpbfnrmwliq', 81),
(27, 'Calle Bolivar ', 'cbgilobxnbodxwe', 81),
(28, 'verda la cuesta', 'tylqnolmhvperxr', 81),
(29, 'manantiales', 'oycefxpzgtnggoi', 81),
(30, 'UNIDAD CAMPESTRE LLANO AZUL', 'tgdkfzceyizvidn', 373),
(31, 'UNIDAD CAMPESTRE EL PARAISO', 'itmervwgupkktcx', 373),
(32, 'SECTOR LA BOMBA VIEJA', 'sñhhpduspdtwgcr', 81),
(34, 'SECTOR SANTA MONICA', 'unsydwsmlzeerjr', 81),
(35, 'SECTOR EL PROGRESO', 'akdvhntwmoogwwy', 81),
(36, 'SECTOR BUENOS AIRES', 'hipqihzmuqfdydt', 81),
(37, 'SECTOR LOS ABUELOS', 'zhgxmgimlxrhoqd', 81),
(38, 'SECTOR EL AURELIO MEJIA', 'xryetñgicfnmgmi', 373),
(39, 'SECTOR LA CALLE CALDA', 'yjbgiryuymmewhe', 81),
(40, 'SECTOR SANTA ANA', 'sokqlidnrdncnwñ', 373),
(41, 'SECTOR LA CEIBA', 'vmtldoscnyobvdt', 373),
(42, 'SECTOR LA FERRERIA', 'rcyhñejkqbqvcpz', 373),
(45, 'SECTOR EL PARAISO', 'ilcuqevkmrbgqcn', 373),
(47, 'NIQUIA UNIDAD RESIDENCIAL PORTAL DEL NORTE', 'wfsnkoxhkydysou', 91),
(49, 'Calle del Comercio', 'ruueiñngopckñum', 81),
(50, 'SECTOR EL CEMENTERIO', 'mbnervfouzujrpt', 373),
(51, 'LA 12', 'imvxqyjpñgizzuv', 81),
(52, 'PILARICA', 'rififvbqnxteqzb', 547),
(53, 'la esmeralda', 'junnhwounaqmgin', 81),
(54, 'SECTOR EL 30 DE MAYO', 'edogobivcgugejh', 81),
(56, 'EL CRISTO', 'bngembqxblgczpl', 81),
(57, 'VEREDA EL GUAYABO', 'xhñuhxebptqmxqk', 81),
(58, 'calle 13', 'dqbmgñwjeoñfvua', 81),
(60, 'SECTOR GUAYACANES', 'gztiygiwixzpnnd', 373),
(61, 'sector el guayabal', 'htnsfmuhxzghrpj', 81),
(62, 'sector segundo parque', 'jajcelesyvxjdyd', 81),
(63, 'sector pilarica', 'xkcwñokgnnegbdm', 547),
(64, 'sector la 17', 'kpvsephayuwhbyh', 81),
(66, 'sector los angelitos', 'kozyrvoncnhbññi', 81),
(67, 'sector barrio de Jesús', 'zhyytgpgphgvkbx', 81),
(68, 'sector el talego', 'tmkxhyobqfjvvim', 81),
(69, 'caribe', 'ymutuzbbwpshyuc', 547),
(71, 'CALLE 16 EL COOPERATIVO', 'vzhhañonqfgijux', 81),
(72, 'SECTOR LA FLORIDA', 'yeovqgoñktjeguk', 81),
(73, 'la calle colombia', 'vhsfptphsdmrwuj', 373),
(74, 'VEREDA FILOVERDE', 'ñbjcidbwymqkdtm', 81),
(75, 'TRAPICHE', 'hzscdeyxcrxihde', 91),
(76, 'vereda tamborcito', 'zomvffqkdasjfxw', 81),
(77, 'vereda el vallo', '00', 81),
(78, 'la colombia', 'yazojbrnfñeyswi', 373),
(79, 'vereda manga arriba', 'qfehgcbwdglpbñv', 373),
(80, 'VEREDAD ISAZA', 'njgeffzipfwulru', 81),
(81, 'VEREDA ISAZA', 'ekkhmkppygmñvzn', 81),
(82, 'palmas del Llano', 'hohueldjisdrdrj', 373),
(84, 'ALTO DE LA VIRGEN ', 'lpñqxdidffvijqx', 373),
(85, 'vereda potrerito', 'npicxhtjñibjsñr', 81),
(86, 'CENTRO', 'tpepygarwkxthyg', 239),
(88, 'los lavaderos (Tablemac)', 'oñkbhfubmfpwbgñ', 81),
(90, 'sector la 14', 'jbzwyttñnehuubt', 81),
(91, 'calle la 14', 'engwztiisthulcp', 373),
(92, 'Urbanización Elvia Gutierrez', 'liyjkmumvwqmkrq', 81),
(93, 'Sector El Cementerio', 'wrutuñolvznstbu', 81),
(94, 'PRADO', 'ejqnxpgihbwqsji', 91),
(95, 'Vereda la Herradura', 'boñftsbpnnñpgeq', 81),
(96, 'Sector la Pinera', 'woldlirxsqtwgpi', 81),
(97, 'Santo Domingo ', 'thkuvjlmwqdkgot', 892),
(98, 'Calle Robles', 'muhukctcmñrkhef', 81),
(99, 'vereda Montañita', 'fdjngbruumbodtv', 81),
(100, 'Vereda Cabildo', 'garwgcporñbviva', 373),
(101, 'Montecarlo', 'wxjxwoodtmrdpwo', 373),
(102, ' Vereda la Herradura', 'uocfqetlñnecwde', 81),
(103, 'Urbanización Altos de Mayorca', 'nymijjhkorpkjdo', 772),
(104, 'VEREDA BUGA ', 'ddexgwpldmztlyl', 81),
(105, 'BARRIO EL SALADO', 'elbekalrqnpcñbn', 373),
(106, 'LA BICENTENARIA', 'ulkdcnjltyjeqat', 81),
(107, 'SAN RAFAEL', 'mhgopsnhkknqñtk', 81),
(109, 'VEREDA SAN ANDRES', 'ovowsycwdvwtvig', 373),
(110, 'CALLE NARIÑO', '0', 373),
(111, 'Vereda la playa', 'hsñrqbwjppjeqgt', 81),
(112, 'Sector la cortada', 'fñvqhicfjrpcqmd', 1083),
(113, 'Barrio de Jesus', 'knsñqcbfrbqmññc', 81),
(114, 'Calle Cordoba', 'pjrwtqitkscxnxs', 81),
(115, 'Calle Cordoba', 'fkirrñthxfauoñh', 373),
(116, 'Vereda la Calda', 'ikñnpwqcequjsuc', 81),
(117, 'Vereda la M', 'jmddkkdmpdtyftp', 81),
(118, 'Vereda el Barrio', 'uiezwqiklriorsu', 373),
(119, 'Vereda Limonar', 'ovilsbswrsuyduo', 373),
(120, 'Vereda el Paraíso', 'jkkohñkriugyepu', 81),
(121, 'Vereda la Meseta', 'mxcxhgchqjjtbgo', 373),
(122, 'Sector la Pradera', 'eetmxyfdyrkcfsg', 81),
(123, 'CAMPO NUEVO', 'vgutañhcgdpxhmx', 373),
(125, 'CALLE RHIN', 'zggywogcxruxgms', 373),
(126, 'BARRIO EL PARAISO', 'psñuytlgsxhhhkñ', 373),
(129, 'Guayabal', 'cfmeuyqdyraxrvd', 81),
(130, 'Sector Hato Grande', 'akvkcypnjmcivrc', 373),
(131, 'Zona Rosa', 'dwhjkikkñjzjopñ', 81),
(132, 'Sector de Manantiales', 'plixbdxjrdqxdcw', 81),
(134, 'Vereda el Totumo', 'orgykzgrlagsefz', 373),
(135, 'Vereda Juan Cojo', 'ehlcjvsñlmhptuj', 373),
(136, 'Zona Hospital', 'frjtvkmpjayqñby', 81),
(137, 'Paraíso', 'lelerjywnkojuxq', 373),
(138, 'Urbanización Martinez Montiel', 'qnowhquykuqrheq', 373),
(139, 'Vía la quiebra', 'fqucneeñevgqaix', 892),
(140, 'Vereda Platanito parte baja', 'urmvlbnpidhdpkc', 81),
(141, 'Calle San Rafael ', 'ouffgñiemqixrñg', 373),
(142, 'Vereda Vallecitos', 'cyggrnhtmhjqqot', 81),
(144, 'Vereda la Raya', 'toiksojoxfsufql', 81),
(145, 'Vereda las Lajas', 'ecguññwexlhyuxs', 81),
(146, 'Filo verde', 'jevyrkqbwvhjqdh', 81),
(148, 'Santa Fé de antioquia ', 'ldviyigbhqiksvc', 872),
(149, 'CORREGIMIENTO EL HATILLO', 'ecpsgobhxdzbevl', 81),
(150, 'HATO GRANDE', 'bogkwooñgndhwkw', 373),
(151, 'CENTRO', 'clbdlltlcetathk', 81),
(152, 'PARQUE GIRARDOTA', 'qctdyniyptdcntt', 373),
(153, 'LA FLORIDA', 'lñmxcrghñithrsu', 373),
(154, 'PLAZA MINORISTA', 'itylnifcutrdbyi', 547),
(155, 'cALLE SAN RAFAEL', 'ibxffvfvglkqdbp', 373),
(156, 'cALLE SAN RAFAEL', 'tmtzzwrñhuanxwl', 373),
(157, 'CALLE SAN RAFAEL', 'enqawuuxxñltmxr', 373),
(158, 'CALLEJON DE LOS CARMONAS ', 'velkisfnflytefj', 373),
(159, 'VEREDA CORRIENTES', 'ambrqmtfñubsñjz', 81),
(160, 'PALMAS DEL LLANO', 'fyvdmcjwpskxñxl', 373),
(161, 'RIO NEGRO', 'lpmktrqtxxqrgcv', 547),
(163, 'Estadio de medellín', 'wqrislectxñhorc', 547),
(164, 'LA NUEVA', 'ypisiywerctosdj', 373),
(165, 'Machado', 'lxvoliicqjkdptd', 239),
(166, 'Sector el hormiguero', 'cmrorawlwntvwsn', 1083),
(168, 'LLANO GRANDE', 'bubgñhvbgcuidwq', 764),
(169, 'RIONEGRO', 'iopslsdinkitltk', 764),
(170, 'VEREDA FILO VERDE ', 'denijeñfjrgsiix', 373),
(171, 'TAMBORCITO 1', 'ogeeuwrljnsqrjw', 81),
(172, 'VEREDA EL CORTADO', 'peqvbññwpvnjxwr', 81),
(173, 'sector  Nuevo Horizonte', 'mrveewuñwoylivñ', 373),
(174, 'VEREDA LA CHORRERA', 'iymylttpqywuklo', 81),
(175, 'SAN FRANCISCO', '000', 239),
(176, 'MACHADO', 'yispvyjtmcsmrnd', 91),
(177, 'Barrio San Rafael', 'ppyñlluhrowicrc', 373),
(178, 'Callejon San Rafael', 'qsjcaohhoññkgiv', 373),
(179, 'molino viejo', 'egegwhgbrugxzgk', 81),
(180, 'Porce', 'jviljgcocfqromd', 892),
(181, 'MADERA', 'gxfdtvlunehimrh', 91),
(182, 'VEREDA SAN DIEGO', 'nrrrupbjfchjduo', 373),
(183, 'LOS ALMENDROS', 'frñfjxjipoveskn', 81),
(184, 'SAN GERONIMO', 'eivdynxyñegsfww', 0),
(185, 'Corregimiento San Pablo', 'dvptshonscmdñms', 883),
(186, 'Paraíso de Sol', 'qhubbbbhlpwoqmd', 816),
(187, 'HATANILLO', 'ehrsorfyñlxñiyq', 81),
(188, 'VEREDA HANTANILLO', 'xwrlihñitjwedjx', 81),
(189, 'VEREDA PANTANILLO', 'fmiñfewdzflohhg', 81),
(190, 'Centro', 'gnbjcxsfodfpccn', 373),
(191, 'Barrio Belén Malubu', 'myighñqydpnwdxb', 547),
(192, 'Belén Malibú', 'zxjptragqyhmhxs', 547),
(193, 'calle San Miguel', 'fhitdisjykutmgu', 892);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `campo_atributos`
--

CREATE TABLE `campo_atributos` (
  `id` int(11) NOT NULL,
  `id_campo` varchar(255) NOT NULL,
  `nombre_campo` varchar(255) NOT NULL,
  `tipo` varchar(50) NOT NULL,
  `label` varchar(255) DEFAULT NULL,
  `placeholder` varchar(255) DEFAULT NULL,
  `requerido` tinyint(1) DEFAULT 0,
  `readonly` tinyint(1) DEFAULT 0,
  `max_length` int(11) DEFAULT NULL,
  `min_length` int(11) DEFAULT NULL,
  `pattern` varchar(255) DEFAULT NULL,
  `opciones` text DEFAULT NULL,
  `default_value` varchar(255) DEFAULT NULL,
  `clase_css` varchar(255) DEFAULT NULL,
  `evento_onchange` varchar(255) DEFAULT NULL,
  `evento_onclick` varchar(255) DEFAULT NULL,
  `evento_onfocus` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `campo_atributos`
--

INSERT INTO `campo_atributos` (`id`, `id_campo`, `nombre_campo`, `tipo`, `label`, `placeholder`, `requerido`, `readonly`, `max_length`, `min_length`, `pattern`, `opciones`, `default_value`, `clase_css`, `evento_onchange`, `evento_onclick`, `evento_onfocus`) VALUES
(1, '1', 'codigo', 'text', 'Códio propiedad', 'Códio propiedad', 1, 0, 50, NULL, NULL, NULL, NULL, 'form-control', NULL, NULL, NULL),
(2, '', 'tipoInmueble', 'select', 'Tipo Inmueble', NULL, 1, 0, 100, NULL, 'NULL', NULL, NULL, 'form-control', NULL, NULL, NULL),
(3, '', 'nivel_piso', 'radio', 'Nivel de piso', 'NULL', 1, 0, 20, 8, NULL, NULL, NULL, 'form-control', NULL, NULL, NULL),
(4, '', 'ciudad', 'select', 'Ciudad', NULL, 1, 0, NULL, NULL, NULL, 'Bogotá,Medellín,Cali', 'Medellín', 'form-select', NULL, NULL, NULL),
(5, '', 'hobbies', 'checkbox', 'Hobbies', NULL, 0, 0, NULL, NULL, NULL, 'Leer,Viajar,Deportes', NULL, 'form-check-input', NULL, NULL, NULL),
(6, '', 'genero', 'radio', 'Género', NULL, 1, 0, NULL, NULL, NULL, 'Masculino,Femenino,Otro', NULL, 'form-check-input', NULL, NULL, NULL),
(7, '', 'foto', 'file', 'Foto de Perfil', NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, 'form-control-file', NULL, NULL, NULL),
(8, '', 'fecha_nacimiento', 'date', 'Fecha de Nacimiento', NULL, 1, 0, NULL, NULL, NULL, NULL, NULL, 'form-control', NULL, NULL, NULL),
(9, '', 'edad', 'number', 'Edad', 'Ingrese su edad', 1, 0, 3, 1, NULL, NULL, NULL, 'form-control', NULL, NULL, NULL),
(10, '', 'comentarios', 'textarea', 'Comentarios', 'Ingrese sus comentarios aquí', 0, 0, 500, NULL, NULL, NULL, NULL, 'form-control', NULL, NULL, NULL),
(11, '', 'calificacion', 'range', 'Calificación', NULL, 0, 0, NULL, NULL, NULL, NULL, '5', 'form-range', NULL, NULL, NULL),
(12, '', 'token', 'hidden', NULL, NULL, 0, 1, NULL, NULL, NULL, NULL, '12345', 'form-hidden', NULL, NULL, NULL),
(13, '', 'color_favorito', 'color', 'Color Favorito', NULL, 0, 0, NULL, NULL, NULL, NULL, '#ffffff', 'form-control', NULL, NULL, NULL),
(14, '', 'busqueda', 'search', 'Buscar', 'Ingrese palabras clave', 0, 0, 50, NULL, NULL, NULL, NULL, 'form-control', NULL, NULL, NULL),
(15, '', 'telefono', 'tel', 'Teléfono', 'Ingrese su número de teléfono', 1, 0, 15, 10, '[0-9]{10,15}', NULL, NULL, 'form-control', NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `certificados_residencia`
--

CREATE TABLE `certificados_residencia` (
  `id` int(11) NOT NULL,
  `codigo_propiedad` int(11) NOT NULL,
  `doc_inquilino` varchar(50) NOT NULL,
  `fecha_generacion` timestamp NOT NULL DEFAULT current_timestamp(),
  `consecutivo` varchar(20) NOT NULL,
  `ruta_archivo` varchar(500) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `certificados_residencia`
--

INSERT INTO `certificados_residencia` (`id`, `codigo_propiedad`, `doc_inquilino`, `fecha_generacion`, `consecutivo`, `ruta_archivo`) VALUES
(4, 1363915, '1047996089', '2026-03-09 19:02:43', 'CR8FTZW4', 'certficados/residencia/CR8FTZW4.pdf');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `citas`
--

CREATE TABLE `citas` (
  `id` int(11) NOT NULL,
  `tipoCita` varchar(50) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `codigoPropiedad` varchar(50) NOT NULL,
  `telefono` varchar(20) NOT NULL,
  `fecha` date NOT NULL,
  `hora` time NOT NULL,
  `estado` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `ciudades`
--

CREATE TABLE `ciudades` (
  `id` int(11) NOT NULL,
  `id_pais` int(11) NOT NULL,
  `nombre_ciudad` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `ciudades`
--

INSERT INTO `ciudades` (`id`, `id_pais`, `nombre_ciudad`) VALUES
(1, 1, 'Medellín');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `company`
--

CREATE TABLE `company` (
  `id` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `nit` varchar(15) NOT NULL,
  `direccion` varchar(255) NOT NULL,
  `telefono` varchar(15) NOT NULL,
  `logo` varchar(255) NOT NULL,
  `email` varchar(266) NOT NULL,
  `ciudad` varchar(255) NOT NULL,
  `web` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Volcado de datos para la tabla `company`
--

INSERT INTO `company` (`id`, `nombre`, `nit`, `direccion`, `telefono`, `logo`, `email`, `ciudad`, `web`) VALUES
(1, 'Somos Propiedad Inmobiliaria', '811.008.756-8', 'Calle 73 Sur No. 45 A - 60 ', '000000', 'logo_SomosPropiedad.png', 'servicioalcliente@somospropiedad.com', 'Sabaneta, Antioquia', 'www.hablemosdenegocios.com.co');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `departamentos`
--

CREATE TABLE `departamentos` (
  `id_departamento` int(10) UNSIGNED NOT NULL,
  `departamento` varchar(255) NOT NULL DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `departamentos`
--

INSERT INTO `departamentos` (`id_departamento`, `departamento`) VALUES
(5, 'ANTIOQUIA'),
(8, 'ATLÁNTICO'),
(11, 'BOGOTÁ, D.C.'),
(13, 'BOLÍVAR'),
(15, 'BOYACÁ'),
(17, 'CALDAS'),
(18, 'CAQUETÁ'),
(19, 'CAUCA'),
(20, 'CESAR'),
(23, 'CÓRDOBA'),
(25, 'CUNDINAMARCA'),
(27, 'CHOCÓ'),
(41, 'HUILA'),
(44, 'LA GUAJIRA'),
(47, 'MAGDALENA'),
(50, 'META'),
(52, 'NARIÑO'),
(54, 'NORTE DE SANTANDER'),
(63, 'QUINDIO'),
(66, 'RISARALDA'),
(68, 'SANTANDER'),
(70, 'SUCRE'),
(73, 'TOLIMA'),
(76, 'VALLE DEL CAUCA'),
(81, 'ARAUCA'),
(85, 'CASANARE'),
(86, 'PUTUMAYO'),
(88, 'ARCHIPIÉLAGO DE SAN ANDRÉS, PROVIDENCIA Y SANTA CATALINA'),
(91, 'AMAZONAS'),
(94, 'GUAINÍA'),
(95, 'GUAVIARE'),
(97, 'VAUPÉS'),
(99, 'VICHADA');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `fotos`
--

CREATE TABLE `fotos` (
  `id` int(11) NOT NULL,
  `codigoPropiedad` varchar(11) NOT NULL,
  `nombre_foto` varchar(200) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `municipios`
--

CREATE TABLE `municipios` (
  `id_municipio` int(10) UNSIGNED NOT NULL,
  `municipio` varchar(255) NOT NULL DEFAULT '',
  `estado` int(10) UNSIGNED NOT NULL,
  `departamento_id` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `municipios`
--

INSERT INTO `municipios` (`id_municipio`, `municipio`, `estado`, `departamento_id`) VALUES
(1, 'Abriaquí', 1, 5),
(2, 'Acacías', 1, 50),
(3, 'Acandí', 1, 27),
(4, 'Acevedo', 1, 41),
(5, 'Achí', 1, 13),
(6, 'Agrado', 1, 41),
(7, 'Agua de Dios', 1, 25),
(8, 'Aguachica', 1, 20),
(9, 'Aguada', 1, 68),
(10, 'Aguadas', 1, 17),
(11, 'Aguazul', 1, 85),
(12, 'Agustín Codazzi', 1, 20),
(13, 'Aipe', 1, 41),
(14, 'Albania', 1, 18),
(15, 'Albania', 1, 44),
(16, 'Albania', 1, 68),
(17, 'Albán', 1, 25),
(18, 'Albán (San José)', 1, 52),
(19, 'Alcalá', 1, 76),
(20, 'Alejandria', 1, 5),
(21, 'Algarrobo', 1, 47),
(22, 'Algeciras', 1, 41),
(23, 'Almaguer', 1, 19),
(24, 'Almeida', 1, 15),
(25, 'Alpujarra', 1, 73),
(26, 'Altamira', 1, 41),
(27, 'Alto Baudó (Pie de Pato)', 1, 27),
(28, 'Altos del Rosario', 1, 13),
(29, 'Alvarado', 1, 73),
(30, 'Amagá', 1, 5),
(31, 'Amalfi', 1, 5),
(32, 'Ambalema', 1, 73),
(33, 'Anapoima', 1, 25),
(34, 'Ancuya', 1, 52),
(35, 'Andalucía', 1, 76),
(36, 'Andes', 1, 5),
(37, 'Angelópolis', 1, 5),
(38, 'Angostura', 1, 5),
(39, 'Anolaima', 1, 25),
(40, 'Anorí', 1, 5),
(41, 'Anserma', 1, 17),
(42, 'Ansermanuevo', 1, 76),
(43, 'Anzoátegui', 1, 73),
(44, 'Anzá', 1, 5),
(45, 'Apartadó', 1, 5),
(46, 'Apulo', 1, 25),
(47, 'Apía', 1, 66),
(48, 'Aquitania', 1, 15),
(49, 'Aracataca', 1, 47),
(50, 'Aranzazu', 1, 17),
(51, 'Aratoca', 1, 68),
(52, 'Arauca', 1, 81),
(53, 'Arauquita', 1, 81),
(54, 'Arbeláez', 1, 25),
(55, 'Arboleda (Berruecos)', 1, 52),
(56, 'Arboledas', 1, 54),
(57, 'Arboletes', 1, 5),
(58, 'Arcabuco', 1, 15),
(59, 'Arenal', 1, 13),
(60, 'Argelia', 1, 5),
(61, 'Argelia', 1, 19),
(62, 'Argelia', 1, 76),
(63, 'Ariguaní (El Difícil)', 1, 47),
(64, 'Arjona', 1, 13),
(65, 'Armenia', 1, 5),
(66, 'Armenia', 1, 63),
(67, 'Armero (Guayabal)', 1, 73),
(68, 'Arroyohondo', 1, 13),
(69, 'Astrea', 1, 20),
(70, 'Ataco', 1, 73),
(71, 'Atrato (Yuto)', 1, 27),
(72, 'Ayapel', 1, 23),
(73, 'Bagadó', 1, 27),
(74, 'Bahía Solano (Mútis)', 1, 27),
(75, 'Bajo Baudó (Pizarro)', 1, 27),
(76, 'Balboa', 1, 19),
(77, 'Balboa', 1, 66),
(78, 'Baranoa', 1, 8),
(79, 'Baraya', 1, 41),
(80, 'Barbacoas', 1, 52),
(81, 'Barbosa', 1, 5),
(82, 'Barbosa', 1, 68),
(83, 'Barichara', 1, 68),
(84, 'Barranca de Upía', 1, 50),
(85, 'Barrancabermeja', 1, 68),
(86, 'Barrancas', 1, 44),
(87, 'Barranco de Loba', 1, 13),
(88, 'Barranquilla', 1, 8),
(89, 'Becerríl', 1, 20),
(90, 'Belalcázar', 1, 17),
(91, 'Bello', 1, 5),
(92, 'Belmira', 1, 5),
(93, 'Beltrán', 1, 25),
(94, 'Belén', 1, 15),
(95, 'Belén', 1, 52),
(96, 'Belén de Bajirá', 1, 27),
(97, 'Belén de Umbría', 1, 66),
(98, 'Belén de los Andaquíes', 1, 18),
(99, 'Berbeo', 1, 15),
(100, 'Betania', 1, 5),
(101, 'Beteitiva', 1, 15),
(102, 'Betulia', 1, 5),
(103, 'Betulia', 1, 68),
(104, 'Bituima', 1, 25),
(105, 'Boavita', 1, 15),
(106, 'Bochalema', 1, 54),
(107, 'Bogotá D.C.', 1, 11),
(108, 'Bojacá', 1, 25),
(109, 'Bojayá (Bellavista)', 1, 27),
(110, 'Bolívar', 1, 5),
(111, 'Bolívar', 1, 19),
(112, 'Bolívar', 1, 68),
(113, 'Bolívar', 1, 76),
(114, 'Bosconia', 1, 20),
(115, 'Boyacá', 1, 15),
(116, 'Briceño', 1, 5),
(117, 'Briceño', 1, 15),
(118, 'Bucaramanga', 1, 68),
(119, 'Bucarasica', 1, 54),
(120, 'Buenaventura', 1, 76),
(121, 'Buenavista', 1, 15),
(122, 'Buenavista', 1, 23),
(123, 'Buenavista', 1, 63),
(124, 'Buenavista', 1, 70),
(125, 'Buenos Aires', 1, 19),
(126, 'Buesaco', 1, 52),
(127, 'Buga', 1, 76),
(128, 'Bugalagrande', 1, 76),
(129, 'Burítica', 1, 5),
(130, 'Busbanza', 1, 15),
(131, 'Cabrera', 1, 25),
(132, 'Cabrera', 1, 68),
(133, 'Cabuyaro', 1, 50),
(134, 'Cachipay', 1, 25),
(135, 'Caicedo', 1, 5),
(136, 'Caicedonia', 1, 76),
(137, 'Caimito', 1, 70),
(138, 'Cajamarca', 1, 73),
(139, 'Cajibío', 1, 19),
(140, 'Cajicá', 1, 25),
(141, 'Calamar', 1, 13),
(142, 'Calamar', 1, 95),
(143, 'Calarcá', 1, 63),
(144, 'Caldas', 1, 5),
(145, 'Caldas', 1, 15),
(146, 'Caldono', 1, 19),
(147, 'California', 1, 68),
(148, 'Calima (Darién)', 1, 76),
(149, 'Caloto', 1, 19),
(150, 'Calí', 1, 76),
(151, 'Campamento', 1, 5),
(152, 'Campo de la Cruz', 1, 8),
(153, 'Campoalegre', 1, 41),
(154, 'Campohermoso', 1, 15),
(155, 'Canalete', 1, 23),
(156, 'Candelaria', 1, 8),
(157, 'Candelaria', 1, 76),
(158, 'Cantagallo', 1, 13),
(159, 'Cantón de San Pablo', 1, 27),
(160, 'Caparrapí', 1, 25),
(161, 'Capitanejo', 1, 68),
(162, 'Caracolí', 1, 5),
(163, 'Caramanta', 1, 5),
(164, 'Carcasí', 1, 68),
(165, 'Carepa', 1, 5),
(166, 'Carmen de Apicalá', 1, 73),
(167, 'Carmen de Carupa', 1, 25),
(168, 'Carmen de Viboral', 1, 5),
(169, 'Carmen del Darién (CURBARADÓ)', 1, 27),
(170, 'Carolina', 1, 5),
(171, 'Cartagena', 1, 13),
(172, 'Cartagena del Chairá', 1, 18),
(173, 'Cartago', 1, 76),
(174, 'Carurú', 1, 97),
(175, 'Casabianca', 1, 73),
(176, 'Castilla la Nueva', 1, 50),
(177, 'Caucasia', 1, 5),
(178, 'Cañasgordas', 1, 5),
(179, 'Cepita', 1, 68),
(180, 'Cereté', 1, 23),
(181, 'Cerinza', 1, 15),
(182, 'Cerrito', 1, 68),
(183, 'Cerro San Antonio', 1, 47),
(184, 'Chachaguí', 1, 52),
(185, 'Chaguaní', 1, 25),
(186, 'Chalán', 1, 70),
(187, 'Chaparral', 1, 73),
(188, 'Charalá', 1, 68),
(189, 'Charta', 1, 68),
(190, 'Chigorodó', 1, 5),
(191, 'Chima', 1, 68),
(192, 'Chimichagua', 1, 20),
(193, 'Chimá', 1, 23),
(194, 'Chinavita', 1, 15),
(195, 'Chinchiná', 1, 17),
(196, 'Chinácota', 1, 54),
(197, 'Chinú', 1, 23),
(198, 'Chipaque', 1, 25),
(199, 'Chipatá', 1, 68),
(200, 'Chiquinquirá', 1, 15),
(201, 'Chiriguaná', 1, 20),
(202, 'Chiscas', 1, 15),
(203, 'Chita', 1, 15),
(204, 'Chitagá', 1, 54),
(205, 'Chitaraque', 1, 15),
(206, 'Chivatá', 1, 15),
(207, 'Chivolo', 1, 47),
(208, 'Choachí', 1, 25),
(209, 'Chocontá', 1, 25),
(210, 'Chámeza', 1, 85),
(211, 'Chía', 1, 25),
(212, 'Chíquiza', 1, 15),
(213, 'Chívor', 1, 15),
(214, 'Cicuco', 1, 13),
(215, 'Cimitarra', 1, 68),
(216, 'Circasia', 1, 63),
(217, 'Cisneros', 1, 5),
(218, 'Ciénaga', 1, 15),
(219, 'Ciénaga', 1, 47),
(220, 'Ciénaga de Oro', 1, 23),
(221, 'Clemencia', 1, 13),
(222, 'Cocorná', 1, 5),
(223, 'Coello', 1, 73),
(224, 'Cogua', 1, 25),
(225, 'Colombia', 1, 41),
(226, 'Colosó (Ricaurte)', 1, 70),
(227, 'Colón', 1, 86),
(228, 'Colón (Génova)', 1, 52),
(229, 'Concepción', 1, 5),
(230, 'Concepción', 1, 68),
(231, 'Concordia', 1, 5),
(232, 'Concordia', 1, 47),
(233, 'Condoto', 1, 27),
(234, 'Confines', 1, 68),
(235, 'Consaca', 1, 52),
(236, 'Contadero', 1, 52),
(237, 'Contratación', 1, 68),
(238, 'Convención', 1, 54),
(239, 'Copacabana', 1, 5),
(240, 'Coper', 1, 15),
(241, 'Cordobá', 1, 63),
(242, 'Corinto', 1, 19),
(243, 'Coromoro', 1, 68),
(244, 'Corozal', 1, 70),
(245, 'Corrales', 1, 15),
(246, 'Cota', 1, 25),
(247, 'Cotorra', 1, 23),
(248, 'Covarachía', 1, 15),
(249, 'Coveñas', 1, 70),
(250, 'Coyaima', 1, 73),
(251, 'Cravo Norte', 1, 81),
(252, 'Cuaspud (Carlosama)', 1, 52),
(253, 'Cubarral', 1, 50),
(254, 'Cubará', 1, 15),
(255, 'Cucaita', 1, 15),
(256, 'Cucunubá', 1, 25),
(257, 'Cucutilla', 1, 54),
(258, 'Cuitiva', 1, 15),
(259, 'Cumaral', 1, 50),
(260, 'Cumaribo', 1, 99),
(261, 'Cumbal', 1, 52),
(262, 'Cumbitara', 1, 52),
(263, 'Cunday', 1, 73),
(264, 'Curillo', 1, 18),
(265, 'Curití', 1, 68),
(266, 'Curumaní', 1, 20),
(267, 'Cáceres', 1, 5),
(268, 'Cáchira', 1, 54),
(269, 'Cácota', 1, 54),
(270, 'Cáqueza', 1, 25),
(271, 'Cértegui', 1, 27),
(272, 'Cómbita', 1, 15),
(273, 'Córdoba', 1, 13),
(274, 'Córdoba', 1, 52),
(275, 'Cúcuta', 1, 54),
(276, 'Dabeiba', 1, 5),
(277, 'Dagua', 1, 76),
(278, 'Dibulla', 1, 44),
(279, 'Distracción', 1, 44),
(280, 'Dolores', 1, 73),
(281, 'Don Matías', 1, 5),
(282, 'Dos Quebradas', 1, 66),
(283, 'Duitama', 1, 15),
(284, 'Durania', 1, 54),
(285, 'Ebéjico', 1, 5),
(286, 'El Bagre', 1, 5),
(287, 'El Banco', 1, 47),
(288, 'El Cairo', 1, 76),
(289, 'El Calvario', 1, 50),
(290, 'El Carmen', 1, 54),
(291, 'El Carmen', 1, 68),
(292, 'El Carmen de Atrato', 1, 27),
(293, 'El Carmen de Bolívar', 1, 13),
(294, 'El Castillo', 1, 50),
(295, 'El Cerrito', 1, 76),
(296, 'El Charco', 1, 52),
(297, 'El Cocuy', 1, 15),
(298, 'El Colegio', 1, 25),
(299, 'El Copey', 1, 20),
(300, 'El Doncello', 1, 18),
(301, 'El Dorado', 1, 50),
(302, 'El Dovio', 1, 76),
(303, 'El Espino', 1, 15),
(304, 'El Guacamayo', 1, 68),
(305, 'El Guamo', 1, 13),
(306, 'El Molino', 1, 44),
(307, 'El Paso', 1, 20),
(308, 'El Paujil', 1, 18),
(309, 'El Peñol', 1, 52),
(310, 'El Peñon', 1, 13),
(311, 'El Peñon', 1, 68),
(312, 'El Peñón', 1, 25),
(313, 'El Piñon', 1, 47),
(314, 'El Playón', 1, 68),
(315, 'El Retorno', 1, 95),
(316, 'El Retén', 1, 47),
(317, 'El Roble', 1, 70),
(318, 'El Rosal', 1, 25),
(319, 'El Rosario', 1, 52),
(320, 'El Tablón de Gómez', 1, 52),
(321, 'El Tambo', 1, 19),
(322, 'El Tambo', 1, 52),
(323, 'El Tarra', 1, 54),
(324, 'El Zulia', 1, 54),
(325, 'El Águila', 1, 76),
(326, 'Elías', 1, 41),
(327, 'Encino', 1, 68),
(328, 'Enciso', 1, 68),
(329, 'Entrerríos', 1, 5),
(330, 'Envigado', 1, 5),
(331, 'Espinal', 1, 73),
(332, 'Facatativá', 1, 25),
(333, 'Falan', 1, 73),
(334, 'Filadelfia', 1, 17),
(335, 'Filandia', 1, 63),
(336, 'Firavitoba', 1, 15),
(337, 'Flandes', 1, 73),
(338, 'Florencia', 1, 18),
(339, 'Florencia', 1, 19),
(340, 'Floresta', 1, 15),
(341, 'Florida', 1, 76),
(342, 'Floridablanca', 1, 68),
(343, 'Florián', 1, 68),
(344, 'Fonseca', 1, 44),
(345, 'Fortúl', 1, 81),
(346, 'Fosca', 1, 25),
(347, 'Francisco Pizarro', 1, 52),
(348, 'Fredonia', 1, 5),
(349, 'Fresno', 1, 73),
(350, 'Frontino', 1, 5),
(351, 'Fuente de Oro', 1, 50),
(352, 'Fundación', 1, 47),
(353, 'Funes', 1, 52),
(354, 'Funza', 1, 25),
(355, 'Fusagasugá', 1, 25),
(356, 'Fómeque', 1, 25),
(357, 'Fúquene', 1, 25),
(358, 'Gachalá', 1, 25),
(359, 'Gachancipá', 1, 25),
(360, 'Gachantivá', 1, 15),
(361, 'Gachetá', 1, 25),
(362, 'Galapa', 1, 8),
(363, 'Galeras (Nueva Granada)', 1, 70),
(364, 'Galán', 1, 68),
(365, 'Gama', 1, 25),
(366, 'Gamarra', 1, 20),
(367, 'Garagoa', 1, 15),
(368, 'Garzón', 1, 41),
(369, 'Gigante', 1, 41),
(370, 'Ginebra', 1, 76),
(371, 'Giraldo', 1, 5),
(372, 'Girardot', 1, 25),
(373, 'Girardota', 1, 5),
(374, 'Girón', 1, 68),
(375, 'Gonzalez', 1, 20),
(376, 'Gramalote', 1, 54),
(377, 'Granada', 1, 5),
(378, 'Granada', 1, 25),
(379, 'Granada', 1, 50),
(380, 'Guaca', 1, 68),
(381, 'Guacamayas', 1, 15),
(382, 'Guacarí', 1, 76),
(383, 'Guachavés', 1, 52),
(384, 'Guachené', 1, 19),
(385, 'Guachetá', 1, 25),
(386, 'Guachucal', 1, 52),
(387, 'Guadalupe', 1, 5),
(388, 'Guadalupe', 1, 41),
(389, 'Guadalupe', 1, 68),
(390, 'Guaduas', 1, 25),
(391, 'Guaitarilla', 1, 52),
(392, 'Gualmatán', 1, 52),
(393, 'Guamal', 1, 47),
(394, 'Guamal', 1, 50),
(395, 'Guamo', 1, 73),
(396, 'Guapota', 1, 68),
(397, 'Guapí', 1, 19),
(398, 'Guaranda', 1, 70),
(399, 'Guarne', 1, 5),
(400, 'Guasca', 1, 25),
(401, 'Guatapé', 1, 5),
(402, 'Guataquí', 1, 25),
(403, 'Guatavita', 1, 25),
(404, 'Guateque', 1, 15),
(405, 'Guavatá', 1, 68),
(406, 'Guayabal de Siquima', 1, 25),
(407, 'Guayabetal', 1, 25),
(408, 'Guayatá', 1, 15),
(409, 'Guepsa', 1, 68),
(410, 'Guicán', 1, 15),
(411, 'Gutiérrez', 1, 25),
(412, 'Guática', 1, 66),
(413, 'Gámbita', 1, 68),
(414, 'Gámeza', 1, 15),
(415, 'Génova', 1, 63),
(416, 'Gómez Plata', 1, 5),
(417, 'Hacarí', 1, 54),
(418, 'Hatillo de Loba', 1, 13),
(419, 'Hato', 1, 68),
(420, 'Hato Corozal', 1, 85),
(421, 'Hatonuevo', 1, 44),
(422, 'Heliconia', 1, 5),
(423, 'Herrán', 1, 54),
(424, 'Herveo', 1, 73),
(425, 'Hispania', 1, 5),
(426, 'Hobo', 1, 41),
(427, 'Honda', 1, 73),
(428, 'Ibagué', 1, 73),
(429, 'Icononzo', 1, 73),
(430, 'Iles', 1, 52),
(431, 'Imúes', 1, 52),
(432, 'Inzá', 1, 19),
(433, 'Inírida', 1, 94),
(434, 'Ipiales', 1, 52),
(435, 'Isnos', 1, 41),
(436, 'Istmina', 1, 27),
(437, 'Itagüí', 1, 5),
(438, 'Ituango', 1, 5),
(439, 'Izá', 1, 15),
(440, 'Jambaló', 1, 19),
(441, 'Jamundí', 1, 76),
(442, 'Jardín', 1, 5),
(443, 'Jenesano', 1, 15),
(444, 'Jericó', 1, 5),
(445, 'Jericó', 1, 15),
(446, 'Jerusalén', 1, 25),
(447, 'Jesús María', 1, 68),
(448, 'Jordán', 1, 68),
(449, 'Juan de Acosta', 1, 8),
(450, 'Junín', 1, 25),
(451, 'Juradó', 1, 27),
(452, 'La Apartada y La Frontera', 1, 23),
(453, 'La Argentina', 1, 41),
(454, 'La Belleza', 1, 68),
(455, 'La Calera', 1, 25),
(456, 'La Capilla', 1, 15),
(457, 'La Ceja', 1, 5),
(458, 'La Celia', 1, 66),
(459, 'La Cruz', 1, 52),
(460, 'La Cumbre', 1, 76),
(461, 'La Dorada', 1, 17),
(462, 'La Esperanza', 1, 54),
(463, 'La Estrella', 1, 5),
(464, 'La Florida', 1, 52),
(465, 'La Gloria', 1, 20),
(466, 'La Jagua de Ibirico', 1, 20),
(467, 'La Jagua del Pilar', 1, 44),
(468, 'La Llanada', 1, 52),
(469, 'La Macarena', 1, 50),
(470, 'La Merced', 1, 17),
(471, 'La Mesa', 1, 25),
(472, 'La Montañita', 1, 18),
(473, 'La Palma', 1, 25),
(474, 'La Paz', 1, 68),
(475, 'La Paz (Robles)', 1, 20),
(476, 'La Peña', 1, 25),
(477, 'La Pintada', 1, 5),
(478, 'La Plata', 1, 41),
(479, 'La Playa', 1, 54),
(480, 'La Primavera', 1, 99),
(481, 'La Salina', 1, 85),
(482, 'La Sierra', 1, 19),
(483, 'La Tebaida', 1, 63),
(484, 'La Tola', 1, 52),
(485, 'La Unión', 1, 5),
(486, 'La Unión', 1, 52),
(487, 'La Unión', 1, 70),
(488, 'La Unión', 1, 76),
(489, 'La Uvita', 1, 15),
(490, 'La Vega', 1, 19),
(491, 'La Vega', 1, 25),
(492, 'La Victoria', 1, 15),
(493, 'La Victoria', 1, 17),
(494, 'La Victoria', 1, 76),
(495, 'La Virginia', 1, 66),
(496, 'Labateca', 1, 54),
(497, 'Labranzagrande', 1, 15),
(498, 'Landázuri', 1, 68),
(499, 'Lebrija', 1, 68),
(500, 'Leiva', 1, 52),
(501, 'Lejanías', 1, 50),
(502, 'Lenguazaque', 1, 25),
(503, 'Leticia', 1, 91),
(504, 'Liborina', 1, 5),
(505, 'Linares', 1, 52),
(506, 'Lloró', 1, 27),
(507, 'Lorica', 1, 23),
(508, 'Los Córdobas', 1, 23),
(509, 'Los Palmitos', 1, 70),
(510, 'Los Patios', 1, 54),
(511, 'Los Santos', 1, 68),
(512, 'Lourdes', 1, 54),
(513, 'Luruaco', 1, 8),
(514, 'Lérida', 1, 73),
(515, 'Líbano', 1, 73),
(516, 'López (Micay)', 1, 19),
(517, 'Macanal', 1, 15),
(518, 'Macaravita', 1, 68),
(519, 'Maceo', 1, 5),
(520, 'Machetá', 1, 25),
(521, 'Madrid', 1, 25),
(522, 'Magangué', 1, 13),
(523, 'Magüi (Payán)', 1, 52),
(524, 'Mahates', 1, 13),
(525, 'Maicao', 1, 44),
(526, 'Majagual', 1, 70),
(527, 'Malambo', 1, 8),
(528, 'Mallama (Piedrancha)', 1, 52),
(529, 'Manatí', 1, 8),
(530, 'Manaure', 1, 44),
(531, 'Manaure Balcón del Cesar', 1, 20),
(532, 'Manizales', 1, 17),
(533, 'Manta', 1, 25),
(534, 'Manzanares', 1, 17),
(535, 'Maní', 1, 85),
(536, 'Mapiripan', 1, 50),
(537, 'Margarita', 1, 13),
(538, 'Marinilla', 1, 5),
(539, 'Maripí', 1, 15),
(540, 'Mariquita', 1, 73),
(541, 'Marmato', 1, 17),
(542, 'Marquetalia', 1, 17),
(543, 'Marsella', 1, 66),
(544, 'Marulanda', 1, 17),
(545, 'María la Baja', 1, 13),
(546, 'Matanza', 1, 68),
(547, 'Medellín', 1, 5),
(548, 'Medina', 1, 25),
(549, 'Medio Atrato', 1, 27),
(550, 'Medio Baudó', 1, 27),
(551, 'Medio San Juan (ANDAGOYA)', 1, 27),
(552, 'Melgar', 1, 73),
(553, 'Mercaderes', 1, 19),
(554, 'Mesetas', 1, 50),
(555, 'Milán', 1, 18),
(556, 'Miraflores', 1, 15),
(557, 'Miraflores', 1, 95),
(558, 'Miranda', 1, 19),
(559, 'Mistrató', 1, 66),
(560, 'Mitú', 1, 97),
(561, 'Mocoa', 1, 86),
(562, 'Mogotes', 1, 68),
(563, 'Molagavita', 1, 68),
(564, 'Momil', 1, 23),
(565, 'Mompós', 1, 13),
(566, 'Mongua', 1, 15),
(567, 'Monguí', 1, 15),
(568, 'Moniquirá', 1, 15),
(569, 'Montebello', 1, 5),
(570, 'Montecristo', 1, 13),
(571, 'Montelíbano', 1, 23),
(572, 'Montenegro', 1, 63),
(573, 'Monteria', 1, 23),
(574, 'Monterrey', 1, 85),
(575, 'Morales', 1, 13),
(576, 'Morales', 1, 19),
(577, 'Morelia', 1, 18),
(578, 'Morroa', 1, 70),
(579, 'Mosquera', 1, 25),
(580, 'Mosquera', 1, 52),
(581, 'Motavita', 1, 15),
(582, 'Moñitos', 1, 23),
(583, 'Murillo', 1, 73),
(584, 'Murindó', 1, 5),
(585, 'Mutatá', 1, 5),
(586, 'Mutiscua', 1, 54),
(587, 'Muzo', 1, 15),
(588, 'Málaga', 1, 68),
(589, 'Nariño', 1, 5),
(590, 'Nariño', 1, 25),
(591, 'Nariño', 1, 52),
(592, 'Natagaima', 1, 73),
(593, 'Nechí', 1, 5),
(594, 'Necoclí', 1, 5),
(595, 'Neira', 1, 17),
(596, 'Neiva', 1, 41),
(597, 'Nemocón', 1, 25),
(598, 'Nilo', 1, 25),
(599, 'Nimaima', 1, 25),
(600, 'Nobsa', 1, 15),
(601, 'Nocaima', 1, 25),
(602, 'Norcasia', 1, 17),
(603, 'Norosí', 1, 13),
(604, 'Novita', 1, 27),
(605, 'Nueva Granada', 1, 47),
(606, 'Nuevo Colón', 1, 15),
(607, 'Nunchía', 1, 85),
(608, 'Nuquí', 1, 27),
(609, 'Nátaga', 1, 41),
(610, 'Obando', 1, 76),
(611, 'Ocamonte', 1, 68),
(612, 'Ocaña', 1, 54),
(613, 'Oiba', 1, 68),
(614, 'Oicatá', 1, 15),
(615, 'Olaya', 1, 5),
(616, 'Olaya Herrera', 1, 52),
(617, 'Onzaga', 1, 68),
(618, 'Oporapa', 1, 41),
(619, 'Orito', 1, 86),
(620, 'Orocué', 1, 85),
(621, 'Ortega', 1, 73),
(622, 'Ospina', 1, 52),
(623, 'Otanche', 1, 15),
(624, 'Ovejas', 1, 70),
(625, 'Pachavita', 1, 15),
(626, 'Pacho', 1, 25),
(627, 'Padilla', 1, 19),
(628, 'Paicol', 1, 41),
(629, 'Pailitas', 1, 20),
(630, 'Paime', 1, 25),
(631, 'Paipa', 1, 15),
(632, 'Pajarito', 1, 15),
(633, 'Palermo', 1, 41),
(634, 'Palestina', 1, 17),
(635, 'Palestina', 1, 41),
(636, 'Palmar', 1, 68),
(637, 'Palmar de Varela', 1, 8),
(638, 'Palmas del Socorro', 1, 68),
(639, 'Palmira', 1, 76),
(640, 'Palmito', 1, 70),
(641, 'Palocabildo', 1, 73),
(642, 'Pamplona', 1, 54),
(643, 'Pamplonita', 1, 54),
(644, 'Pandi', 1, 25),
(645, 'Panqueba', 1, 15),
(646, 'Paratebueno', 1, 25),
(647, 'Pasca', 1, 25),
(648, 'Patía (El Bordo)', 1, 19),
(649, 'Pauna', 1, 15),
(650, 'Paya', 1, 15),
(651, 'Paz de Ariporo', 1, 85),
(652, 'Paz de Río', 1, 15),
(653, 'Pedraza', 1, 47),
(654, 'Pelaya', 1, 20),
(655, 'Pensilvania', 1, 17),
(656, 'Peque', 1, 5),
(657, 'Pereira', 1, 66),
(658, 'Pesca', 1, 15),
(659, 'Peñol', 1, 5),
(660, 'Piamonte', 1, 19),
(661, 'Pie de Cuesta', 1, 68),
(662, 'Piedras', 1, 73),
(663, 'Piendamó', 1, 19),
(664, 'Pijao', 1, 63),
(665, 'Pijiño', 1, 47),
(666, 'Pinchote', 1, 68),
(667, 'Pinillos', 1, 13),
(668, 'Piojo', 1, 8),
(669, 'Pisva', 1, 15),
(670, 'Pital', 1, 41),
(671, 'Pitalito', 1, 41),
(672, 'Pivijay', 1, 47),
(673, 'Planadas', 1, 73),
(674, 'Planeta Rica', 1, 23),
(675, 'Plato', 1, 47),
(676, 'Policarpa', 1, 52),
(677, 'Polonuevo', 1, 8),
(678, 'Ponedera', 1, 8),
(679, 'Popayán', 1, 19),
(680, 'Pore', 1, 85),
(681, 'Potosí', 1, 52),
(682, 'Pradera', 1, 76),
(683, 'Prado', 1, 73),
(684, 'Providencia', 1, 52),
(685, 'Providencia', 1, 88),
(686, 'Pueblo Bello', 1, 20),
(687, 'Pueblo Nuevo', 1, 23),
(688, 'Pueblo Rico', 1, 66),
(689, 'Pueblorrico', 1, 5),
(690, 'Puebloviejo', 1, 47),
(691, 'Puente Nacional', 1, 68),
(692, 'Puerres', 1, 52),
(693, 'Puerto Asís', 1, 86),
(694, 'Puerto Berrío', 1, 5),
(695, 'Puerto Boyacá', 1, 15),
(696, 'Puerto Caicedo', 1, 86),
(697, 'Puerto Carreño', 1, 99),
(698, 'Puerto Colombia', 1, 8),
(699, 'Puerto Concordia', 1, 50),
(700, 'Puerto Escondido', 1, 23),
(701, 'Puerto Gaitán', 1, 50),
(702, 'Puerto Guzmán', 1, 86),
(703, 'Puerto Leguízamo', 1, 86),
(704, 'Puerto Libertador', 1, 23),
(705, 'Puerto Lleras', 1, 50),
(706, 'Puerto López', 1, 50),
(707, 'Puerto Nare', 1, 5),
(708, 'Puerto Nariño', 1, 91),
(709, 'Puerto Parra', 1, 68),
(710, 'Puerto Rico', 1, 18),
(711, 'Puerto Rico', 1, 50),
(712, 'Puerto Rondón', 1, 81),
(713, 'Puerto Salgar', 1, 25),
(714, 'Puerto Santander', 1, 54),
(715, 'Puerto Tejada', 1, 19),
(716, 'Puerto Triunfo', 1, 5),
(717, 'Puerto Wilches', 1, 68),
(718, 'Pulí', 1, 25),
(719, 'Pupiales', 1, 52),
(720, 'Puracé (Coconuco)', 1, 19),
(721, 'Purificación', 1, 73),
(722, 'Purísima', 1, 23),
(723, 'Pácora', 1, 17),
(724, 'Páez', 1, 15),
(725, 'Páez (Belalcazar)', 1, 19),
(726, 'Páramo', 1, 68),
(727, 'Quebradanegra', 1, 25),
(728, 'Quetame', 1, 25),
(729, 'Quibdó', 1, 27),
(730, 'Quimbaya', 1, 63),
(731, 'Quinchía', 1, 66),
(732, 'Quipama', 1, 15),
(733, 'Quipile', 1, 25),
(734, 'Ragonvalia', 1, 54),
(735, 'Ramiriquí', 1, 15),
(736, 'Recetor', 1, 85),
(737, 'Regidor', 1, 13),
(738, 'Remedios', 1, 5),
(739, 'Remolino', 1, 47),
(740, 'Repelón', 1, 8),
(741, 'Restrepo', 1, 50),
(742, 'Restrepo', 1, 76),
(743, 'Retiro', 1, 5),
(744, 'Ricaurte', 1, 25),
(745, 'Ricaurte', 1, 52),
(746, 'Rio Negro', 1, 68),
(747, 'Rioblanco', 1, 73),
(748, 'Riofrío', 1, 76),
(749, 'Riohacha', 1, 44),
(750, 'Risaralda', 1, 17),
(751, 'Rivera', 1, 41),
(752, 'Roberto Payán (San José)', 1, 52),
(753, 'Roldanillo', 1, 76),
(754, 'Roncesvalles', 1, 73),
(755, 'Rondón', 1, 15),
(756, 'Rosas', 1, 19),
(757, 'Rovira', 1, 73),
(758, 'Ráquira', 1, 15),
(759, 'Río Iró', 1, 27),
(760, 'Río Quito', 1, 27),
(761, 'Río Sucio', 1, 17),
(762, 'Río Viejo', 1, 13),
(763, 'Río de oro', 1, 20),
(764, 'Ríonegro', 1, 5),
(765, 'Ríosucio', 1, 27),
(766, 'Sabana de Torres', 1, 68),
(767, 'Sabanagrande', 1, 8),
(768, 'Sabanalarga', 1, 5),
(769, 'Sabanalarga', 1, 8),
(770, 'Sabanalarga', 1, 85),
(771, 'Sabanas de San Angel (SAN ANGEL)', 1, 47),
(772, 'Sabaneta', 1, 5),
(773, 'Saboyá', 1, 15),
(774, 'Sahagún', 1, 23),
(775, 'Saladoblanco', 1, 41),
(776, 'Salamina', 1, 17),
(777, 'Salamina', 1, 47),
(778, 'Salazar', 1, 54),
(779, 'Saldaña', 1, 73),
(780, 'Salento', 1, 63),
(781, 'Salgar', 1, 5),
(782, 'Samacá', 1, 15),
(783, 'Samaniego', 1, 52),
(784, 'Samaná', 1, 17),
(785, 'Sampués', 1, 70),
(786, 'San Agustín', 1, 41),
(787, 'San Alberto', 1, 20),
(788, 'San Andrés', 1, 68),
(789, 'San Andrés Sotavento', 1, 23),
(790, 'San Andrés de Cuerquía', 1, 5),
(791, 'San Antero', 1, 23),
(792, 'San Antonio', 1, 73),
(793, 'San Antonio de Tequendama', 1, 25),
(794, 'San Benito', 1, 68),
(795, 'San Benito Abad', 1, 70),
(796, 'San Bernardo', 1, 25),
(797, 'San Bernardo', 1, 52),
(798, 'San Bernardo del Viento', 1, 23),
(799, 'San Calixto', 1, 54),
(800, 'San Carlos', 1, 5),
(801, 'San Carlos', 1, 23),
(802, 'San Carlos de Guaroa', 1, 50),
(803, 'San Cayetano', 1, 25),
(804, 'San Cayetano', 1, 54),
(805, 'San Cristobal', 1, 13),
(806, 'San Diego', 1, 20),
(807, 'San Eduardo', 1, 15),
(808, 'San Estanislao', 1, 13),
(809, 'San Fernando', 1, 13),
(810, 'San Francisco', 1, 5),
(811, 'San Francisco', 1, 25),
(812, 'San Francisco', 1, 86),
(813, 'San Gíl', 1, 68),
(814, 'San Jacinto', 1, 13),
(815, 'San Jacinto del Cauca', 1, 13),
(816, 'San Jerónimo', 1, 5),
(817, 'San Joaquín', 1, 68),
(818, 'San José', 1, 17),
(819, 'San José de Miranda', 1, 68),
(820, 'San José de Montaña', 1, 5),
(821, 'San José de Pare', 1, 15),
(822, 'San José de Uré', 1, 23),
(823, 'San José del Fragua', 1, 18),
(824, 'San José del Guaviare', 1, 95),
(825, 'San José del Palmar', 1, 27),
(826, 'San Juan de Arama', 1, 50),
(827, 'San Juan de Betulia', 1, 70),
(828, 'San Juan de Nepomuceno', 1, 13),
(829, 'San Juan de Pasto', 1, 52),
(830, 'San Juan de Río Seco', 1, 25),
(831, 'San Juan de Urabá', 1, 5),
(832, 'San Juan del Cesar', 1, 44),
(833, 'San Juanito', 1, 50),
(834, 'San Lorenzo', 1, 52),
(835, 'San Luis', 1, 73),
(836, 'San Luís', 1, 5),
(837, 'San Luís de Gaceno', 1, 15),
(838, 'San Luís de Palenque', 1, 85),
(839, 'San Marcos', 1, 70),
(840, 'San Martín', 1, 20),
(841, 'San Martín', 1, 50),
(842, 'San Martín de Loba', 1, 13),
(843, 'San Mateo', 1, 15),
(844, 'San Miguel', 1, 68),
(845, 'San Miguel', 1, 86),
(846, 'San Miguel de Sema', 1, 15),
(847, 'San Onofre', 1, 70),
(848, 'San Pablo', 1, 13),
(849, 'San Pablo', 1, 52),
(850, 'San Pablo de Borbur', 1, 15),
(851, 'San Pedro', 1, 5),
(852, 'San Pedro', 1, 70),
(853, 'San Pedro', 1, 76),
(854, 'San Pedro de Cartago', 1, 52),
(855, 'San Pedro de Urabá', 1, 5),
(856, 'San Pelayo', 1, 23),
(857, 'San Rafael', 1, 5),
(858, 'San Roque', 1, 5),
(859, 'San Sebastián', 1, 19),
(860, 'San Sebastián de Buenavista', 1, 47),
(861, 'San Vicente', 1, 5),
(862, 'San Vicente del Caguán', 1, 18),
(863, 'San Vicente del Chucurí', 1, 68),
(864, 'San Zenón', 1, 47),
(865, 'Sandoná', 1, 52),
(866, 'Santa Ana', 1, 47),
(867, 'Santa Bárbara', 1, 5),
(868, 'Santa Bárbara', 1, 68),
(869, 'Santa Bárbara (Iscuandé)', 1, 52),
(870, 'Santa Bárbara de Pinto', 1, 47),
(871, 'Santa Catalina', 1, 13),
(872, 'Santa Fé de Antioquia', 1, 5),
(873, 'Santa Genoveva de Docorodó', 1, 27),
(874, 'Santa Helena del Opón', 1, 68),
(875, 'Santa Isabel', 1, 73),
(876, 'Santa Lucía', 1, 8),
(877, 'Santa Marta', 1, 47),
(878, 'Santa María', 1, 15),
(879, 'Santa María', 1, 41),
(880, 'Santa Rosa', 1, 13),
(881, 'Santa Rosa', 1, 19),
(882, 'Santa Rosa de Cabal', 1, 66),
(883, 'Santa Rosa de Osos', 1, 5),
(884, 'Santa Rosa de Viterbo', 1, 15),
(885, 'Santa Rosa del Sur', 1, 13),
(886, 'Santa Rosalía', 1, 99),
(887, 'Santa Sofía', 1, 15),
(888, 'Santana', 1, 15),
(889, 'Santander de Quilichao', 1, 19),
(890, 'Santiago', 1, 54),
(891, 'Santiago', 1, 86),
(892, 'Santo Domingo', 1, 5),
(893, 'Santo Tomás', 1, 8),
(894, 'Santuario', 1, 5),
(895, 'Santuario', 1, 66),
(896, 'Sapuyes', 1, 52),
(897, 'Saravena', 1, 81),
(898, 'Sardinata', 1, 54),
(899, 'Sasaima', 1, 25),
(900, 'Sativanorte', 1, 15),
(901, 'Sativasur', 1, 15),
(902, 'Segovia', 1, 5),
(903, 'Sesquilé', 1, 25),
(904, 'Sevilla', 1, 76),
(905, 'Siachoque', 1, 15),
(906, 'Sibaté', 1, 25),
(907, 'Sibundoy', 1, 86),
(908, 'Silos', 1, 54),
(909, 'Silvania', 1, 25),
(910, 'Silvia', 1, 19),
(911, 'Simacota', 1, 68),
(912, 'Simijaca', 1, 25),
(913, 'Simití', 1, 13),
(914, 'Sincelejo', 1, 70),
(915, 'Sincé', 1, 70),
(916, 'Sipí', 1, 27),
(917, 'Sitionuevo', 1, 47),
(918, 'Soacha', 1, 25),
(919, 'Soatá', 1, 15),
(920, 'Socha', 1, 15),
(921, 'Socorro', 1, 68),
(922, 'Socotá', 1, 15),
(923, 'Sogamoso', 1, 15),
(924, 'Solano', 1, 18),
(925, 'Soledad', 1, 8),
(926, 'Solita', 1, 18),
(927, 'Somondoco', 1, 15),
(928, 'Sonsón', 1, 5),
(929, 'Sopetrán', 1, 5),
(930, 'Soplaviento', 1, 13),
(931, 'Sopó', 1, 25),
(932, 'Sora', 1, 15),
(933, 'Soracá', 1, 15),
(934, 'Sotaquirá', 1, 15),
(935, 'Sotara (Paispamba)', 1, 19),
(936, 'Sotomayor (Los Andes)', 1, 52),
(937, 'Suaita', 1, 68),
(938, 'Suan', 1, 8),
(939, 'Suaza', 1, 41),
(940, 'Subachoque', 1, 25),
(941, 'Sucre', 1, 19),
(942, 'Sucre', 1, 68),
(943, 'Sucre', 1, 70),
(944, 'Suesca', 1, 25),
(945, 'Supatá', 1, 25),
(946, 'Supía', 1, 17),
(947, 'Suratá', 1, 68),
(948, 'Susa', 1, 25),
(949, 'Susacón', 1, 15),
(950, 'Sutamarchán', 1, 15),
(951, 'Sutatausa', 1, 25),
(952, 'Sutatenza', 1, 15),
(953, 'Suárez', 1, 19),
(954, 'Suárez', 1, 73),
(955, 'Sácama', 1, 85),
(956, 'Sáchica', 1, 15),
(957, 'Tabio', 1, 25),
(958, 'Tadó', 1, 27),
(959, 'Talaigua Nuevo', 1, 13),
(960, 'Tamalameque', 1, 20),
(961, 'Tame', 1, 81),
(962, 'Taminango', 1, 52),
(963, 'Tangua', 1, 52),
(964, 'Taraira', 1, 97),
(965, 'Tarazá', 1, 5),
(966, 'Tarqui', 1, 41),
(967, 'Tarso', 1, 5),
(968, 'Tasco', 1, 15),
(969, 'Tauramena', 1, 85),
(970, 'Tausa', 1, 25),
(971, 'Tello', 1, 41),
(972, 'Tena', 1, 25),
(973, 'Tenerife', 1, 47),
(974, 'Tenjo', 1, 25),
(975, 'Tenza', 1, 15),
(976, 'Teorama', 1, 54),
(977, 'Teruel', 1, 41),
(978, 'Tesalia', 1, 41),
(979, 'Tibacuy', 1, 25),
(980, 'Tibaná', 1, 15),
(981, 'Tibasosa', 1, 15),
(982, 'Tibirita', 1, 25),
(983, 'Tibú', 1, 54),
(984, 'Tierralta', 1, 23),
(985, 'Timaná', 1, 41),
(986, 'Timbiquí', 1, 19),
(987, 'Timbío', 1, 19),
(988, 'Tinjacá', 1, 15),
(989, 'Tipacoque', 1, 15),
(990, 'Tiquisio (Puerto Rico)', 1, 13),
(991, 'Titiribí', 1, 5),
(992, 'Toca', 1, 15),
(993, 'Tocaima', 1, 25),
(994, 'Tocancipá', 1, 25),
(995, 'Toguí', 1, 15),
(996, 'Toledo', 1, 5),
(997, 'Toledo', 1, 54),
(998, 'Tolú', 1, 70),
(999, 'Tolú Viejo', 1, 70),
(1000, 'Tona', 1, 68),
(1001, 'Topagá', 1, 15),
(1002, 'Topaipí', 1, 25),
(1003, 'Toribío', 1, 19),
(1004, 'Toro', 1, 76),
(1005, 'Tota', 1, 15),
(1006, 'Totoró', 1, 19),
(1007, 'Trinidad', 1, 85),
(1008, 'Trujillo', 1, 76),
(1009, 'Tubará', 1, 8),
(1010, 'Tuchín', 1, 23),
(1011, 'Tulúa', 1, 76),
(1012, 'Tumaco', 1, 52),
(1013, 'Tunja', 1, 15),
(1014, 'Tunungua', 1, 15),
(1015, 'Turbaco', 1, 13),
(1016, 'Turbaná', 1, 13),
(1017, 'Turbo', 1, 5),
(1018, 'Turmequé', 1, 15),
(1019, 'Tuta', 1, 15),
(1020, 'Tutasá', 1, 15),
(1021, 'Támara', 1, 85),
(1022, 'Támesis', 1, 5),
(1023, 'Túquerres', 1, 52),
(1024, 'Ubalá', 1, 25),
(1025, 'Ubaque', 1, 25),
(1026, 'Ubaté', 1, 25),
(1027, 'Ulloa', 1, 76),
(1028, 'Une', 1, 25),
(1029, 'Unguía', 1, 27),
(1030, 'Unión Panamericana (ÁNIMAS)', 1, 27),
(1031, 'Uramita', 1, 5),
(1032, 'Uribe', 1, 50),
(1033, 'Uribia', 1, 44),
(1034, 'Urrao', 1, 5),
(1035, 'Urumita', 1, 44),
(1036, 'Usiacuri', 1, 8),
(1037, 'Valdivia', 1, 5),
(1038, 'Valencia', 1, 23),
(1039, 'Valle de San José', 1, 68),
(1040, 'Valle de San Juan', 1, 73),
(1041, 'Valle del Guamuez', 1, 86),
(1042, 'Valledupar', 1, 20),
(1043, 'Valparaiso', 1, 5),
(1044, 'Valparaiso', 1, 18),
(1045, 'Vegachí', 1, 5),
(1046, 'Venadillo', 1, 73),
(1047, 'Venecia', 1, 5),
(1048, 'Venecia (Ospina Pérez)', 1, 25),
(1049, 'Ventaquemada', 1, 15),
(1050, 'Vergara', 1, 25),
(1051, 'Versalles', 1, 76),
(1052, 'Vetas', 1, 68),
(1053, 'Viani', 1, 25),
(1054, 'Vigía del Fuerte', 1, 5),
(1055, 'Vijes', 1, 76),
(1056, 'Villa Caro', 1, 54),
(1057, 'Villa Rica', 1, 19),
(1058, 'Villa de Leiva', 1, 15),
(1059, 'Villa del Rosario', 1, 54),
(1060, 'Villagarzón', 1, 86),
(1061, 'Villagómez', 1, 25),
(1062, 'Villahermosa', 1, 73),
(1063, 'Villamaría', 1, 17),
(1064, 'Villanueva', 1, 13),
(1065, 'Villanueva', 1, 44),
(1066, 'Villanueva', 1, 68),
(1067, 'Villanueva', 1, 85),
(1068, 'Villapinzón', 1, 25),
(1069, 'Villarrica', 1, 73),
(1070, 'Villavicencio', 1, 50),
(1071, 'Villavieja', 1, 41),
(1072, 'Villeta', 1, 25),
(1073, 'Viotá', 1, 25),
(1074, 'Viracachá', 1, 15),
(1075, 'Vista Hermosa', 1, 50),
(1076, 'Viterbo', 1, 17),
(1077, 'Vélez', 1, 68),
(1078, 'Yacopí', 1, 25),
(1079, 'Yacuanquer', 1, 52),
(1080, 'Yaguará', 1, 41),
(1081, 'Yalí', 1, 5),
(1082, 'Yarumal', 1, 5),
(1083, 'Yolombó', 1, 5),
(1084, 'Yondó (Casabe)', 1, 5),
(1085, 'Yopal', 1, 85),
(1086, 'Yotoco', 1, 76),
(1087, 'Yumbo', 1, 76),
(1088, 'Zambrano', 1, 13),
(1089, 'Zapatoca', 1, 68),
(1090, 'Zapayán (PUNTA DE PIEDRAS)', 1, 47),
(1091, 'Zaragoza', 1, 5),
(1092, 'Zarzal', 1, 76),
(1093, 'Zetaquirá', 1, 15),
(1094, 'Zipacón', 1, 25),
(1095, 'Zipaquirá', 1, 25),
(1096, 'Zona Bananera (PRADO - SEVILLA)', 1, 47),
(1097, 'Ábrego', 1, 54),
(1098, 'Íquira', 1, 41),
(1099, 'Úmbita', 1, 15),
(1100, 'Útica', 1, 25);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `paises`
--

CREATE TABLE `paises` (
  `id` int(11) NOT NULL,
  `nombre_pais` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `paises`
--

INSERT INTO `paises` (`id`, `nombre_pais`) VALUES
(1, 'Colombia');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `porcentajeaumento`
--

CREATE TABLE `porcentajeaumento` (
  `id` int(11) NOT NULL,
  `porcentaje` float(5,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `proprieter`
--

CREATE TABLE `proprieter` (
  `id` int(11) NOT NULL,
  `codigo` int(11) NOT NULL,
  `tipoInmueble` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `nivel_piso` int(11) NOT NULL,
  `area` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `estrato` int(11) NOT NULL,
  `departamento` text NOT NULL,
  `Municipio` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `terraza` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `ascensor` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `patio` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `parqueadero` text NOT NULL,
  `cuarto_util` text NOT NULL,
  `alcobas` int(11) NOT NULL,
  `closet` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `sala` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `sala_comedor` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `comedor` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `cocina` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `servicios` int(11) NOT NULL,
  `CuartoServicios` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `ZonaRopa` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `vista` text NOT NULL,
  `servicios_publicos` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `otras_caracteristicas` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `direccion` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `latitud` varchar(50) NOT NULL,
  `longitud` varchar(50) NOT NULL,
  `TelefonoInmueble` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `valor_canon` varchar(30) NOT NULL,
  `doc_propietario` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `nombre_propietario` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `telefono_propietario` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `email_propietario` varchar(255) NOT NULL,
  `banco` text NOT NULL,
  `tipoCuenta` text NOT NULL,
  `numeroCuenta` varchar(30) NOT NULL,
  `diaPago` int(11) NOT NULL,
  `doc_inquilino` varchar(15) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `nombre_inquilino` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `telefono_inquilino` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `email_inquilino` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `vigenciaContrato` varchar(40) NOT NULL,
  `fecha` date NOT NULL,
  `descuento` int(11) NOT NULL,
  `iva` decimal(5,0) NOT NULL,
  `contrato_EPM` varchar(25) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `comision` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `aval_catastro` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `asistencia` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `cc_codeudor_uno` varchar(12) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `nombre_codeudor_uno` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `email_codeudor_uno` varchar(255) NOT NULL,
  `telefono_codeudor_uno` varchar(25) NOT NULL,
  `direccion_codeudor_uno` varchar(255) NOT NULL,
  `cc_codeudor_dos` varchar(12) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `nombre_codeudor_dos` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `email_codeudor_dos` varchar(255) NOT NULL,
  `telefono_codeudor_dos` varchar(25) NOT NULL,
  `direccion_codeudor_dos` varchar(255) NOT NULL,
  `cc_codeudor_tres` varchar(12) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `nombre_codeudor_tres` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `email_codeudor_tres` varchar(255) NOT NULL,
  `telefono_codeudor_tres` varchar(25) NOT NULL,
  `direccion_codeudor_tres` varchar(255) NOT NULL,
  `estadoPropietario` varchar(15) NOT NULL,
  `url_foto_principal` varchar(255) NOT NULL,
  `condicion` varchar(50) NOT NULL,
  `ipc` float(5,2) NOT NULL,
  `fecha_creacion` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `repairmen`
--

CREATE TABLE `repairmen` (
  `id` int(11) NOT NULL,
  `identificacion` varchar(15) NOT NULL,
  `nombre` varchar(50) NOT NULL,
  `telefono` varchar(12) NOT NULL,
  `email` varchar(255) NOT NULL,
  `profesion` text NOT NULL,
  `estado` text NOT NULL,
  `fecha_creacion` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `report`
--

CREATE TABLE `report` (
  `id` int(11) NOT NULL,
  `codigoReporte` varchar(15) NOT NULL,
  `codigo_propietario` int(11) NOT NULL,
  `valorFactura` int(11) NOT NULL,
  `valorServicio` int(11) NOT NULL,
  `totalPagar` int(11) NOT NULL,
  `pagado` tinyint(4) NOT NULL,
  `situacionReportada` varchar(255) NOT NULL,
  `fotoReporte` varchar(255) NOT NULL,
  `solucion` varchar(255) NOT NULL,
  `fotoSolucion` varchar(255) NOT NULL,
  `EstadoReporte` text NOT NULL,
  `id_reparador` varchar(15) NOT NULL,
  `fechaCreacion` date NOT NULL,
  `fechaActualizacion` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `retiredtenants`
--

CREATE TABLE `retiredtenants` (
  `idRetired` int(11) NOT NULL,
  `codigoPropiedad` varchar(12) CHARACTER SET utf8mb4 COLLATE utf8mb4_spanish_ci NOT NULL,
  `IdInquilino` varchar(25) CHARACTER SET utf8mb4 COLLATE utf8mb4_spanish_ci NOT NULL,
  `NombreInquilino` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_spanish_ci NOT NULL,
  `telefonoInquilino` varchar(12) NOT NULL,
  `emailInquilino` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_spanish_ci NOT NULL,
  `fechaRetiro` date NOT NULL,
  `fechaRegistro` date NOT NULL,
  `registro` varchar(12) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `slider`
--

CREATE TABLE `slider` (
  `id` int(11) NOT NULL,
  `titulo` varchar(50) NOT NULL,
  `descripcion` varchar(100) NOT NULL,
  `url_image` varchar(255) NOT NULL,
  `texto_boton` varchar(50) NOT NULL,
  `url_boton` text NOT NULL,
  `estilo_boton` varchar(30) NOT NULL,
  `estado` int(11) NOT NULL,
  `orden` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `smtpconfig`
--

CREATE TABLE `smtpconfig` (
  `id` int(11) NOT NULL,
  `host` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `port` int(11) NOT NULL,
  `nameBody` text NOT NULL,
  `Subject` varchar(255) NOT NULL,
  `body` varchar(255) NOT NULL,
  `urlpicture` varchar(255) NOT NULL,
  `logoEncabezado` varchar(255) NOT NULL,
  `fondo` varchar(255) NOT NULL,
  `firma` varchar(255) NOT NULL,
  `nameFile` varchar(255) NOT NULL,
  `emailCompany` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Volcado de datos para la tabla `smtpconfig`
--

INSERT INTO `smtpconfig` (`id`, `host`, `email`, `password`, `port`, `nameBody`, `Subject`, `body`, `urlpicture`, `logoEncabezado`, `fondo`, `firma`, `nameFile`, `emailCompany`) VALUES
(1, 'ejemplos', 'jd123am@gmail.com', '1', 2, 'Somos Propiedad Inmobilaria', 'ejemplo', 'ejemplo', 'img/empresa/casas.jpg', 'img/empresa/eagle.png', 'images/imagenesSubidas/fondo.png', 'images/imagenesSubidas/firma.png', 'ejemplo', 'inmobiliariahdn@gmail.com');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tenant`
--

CREATE TABLE `tenant` (
  `id` int(11) NOT NULL,
  `codigoPropiedad` int(11) NOT NULL,
  `identificacionInquilino` varchar(25) NOT NULL,
  `nombreInquilino` varchar(255) NOT NULL,
  `telefonoInquilino` varchar(15) NOT NULL,
  `email` varchar(255) NOT NULL,
  `facturacion` varchar(50) NOT NULL,
  `fechaIngreso` date NOT NULL,
  `fechaRetiro` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tipos`
--

CREATE TABLE `tipos` (
  `id` int(11) NOT NULL,
  `nombre_tipo` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `tipos`
--

INSERT INTO `tipos` (`id`, `nombre_tipo`) VALUES
(1, 'Casa'),
(2, 'Apartamento'),
(3, 'Finca'),
(4, 'Local'),
(5, 'Lote'),
(6, 'Apartaestudio'),
(7, 'Penthouse'),
(8, 'Penthouse'),
(9, 'Penthouse'),
(10, 'Casa con local');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` int(11) NOT NULL,
  `password` varchar(255) NOT NULL,
  `nombre` text NOT NULL,
  `rol` int(1) NOT NULL,
  `foto` varchar(255) NOT NULL,
  `orden` int(11) NOT NULL,
  `fechaCreacionUser` varchar(15) NOT NULL,
  `email` varchar(255) NOT NULL,
  `genero` text NOT NULL,
  `telefono` varchar(20) NOT NULL,
  `direccion` varchar(255) NOT NULL,
  `edad` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Volcado de datos para la tabla `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `nombre`, `rol`, `foto`, `orden`, `fechaCreacionUser`, `email`, `genero`, `telefono`, `direccion`, `edad`) VALUES
(32, 39385959, '$2y$10$8vgYFsDyRFwJs5.qo097AOh7WpiMLzNxTIbMBnDcMfDQT28Hch2Ca', 'DIANA ALEXANDRA AGUDELO GIL', 2, 'img/fotosUsuarios/1736546236_Diana Alexandra Agudelo.JPG', 0, '', 'servicios@somospropiedad.com', 'Femenino', '320 6716990 ', '', 0),
(27, 42799701, '$2y$10$bBObqpttLJ0jcXzMrwbnTu0F51wL79DfPWLNXaBaw8xpMAn/ezfMO', 'CAROL RIOS QUICENO', 2, 'img/fotosUsuarios/1736546046_Carol Quiceno.JPG', 0, '', 'asesor2@somospropiedad.com', 'Femenino', '304 4459737', '', 0),
(33, 43192071, '$2y$10$RRMey5.WdwIZFz5AgjYe7utecoXZ9dp3uBt4c1mlrS9.GgBD3KqJ6', 'JESSICA LILIANA MONTOYA COLORADO', 3, '', 0, '', 'servicioalcliente@somospropiedad.com', 'Femenino', '3168757733', '', 0),
(29, 43753523, '$2y$10$VeX23cHNE6T0BMf8fgkW.uDMMrlBz50f/iThTuxpkkX7YiW833k9u', 'VERONICA VALENCIA HERNANDEZ', 2, 'img/fotosUsuarios/1736546114_Veronica Valencia.JPG', 0, '', 'reparaciones@somospropiedad.com', 'Femenino', '300 6662367', '', 0),
(26, 43838329, '$2y$10$yhJNykp1i5bjWGqDs9Nxi.Zugucm1Z/dl4jEJmYml6THVUqNpicZi', 'SANDRA PATRICIA MONTOYA COLORADO', 2, 'img/fotosUsuarios/1736546015_Sandra Patricia Montoya.JPG', 0, '', 'liderarrendamientos@somospropiedad.com', 'Femenino', '312 2933978', '', 0),
(31, 70565868, '$2y$10$1RlsaoSawH7L6pwZYBToLetRuNI1ooKfZgPNwNmrqjU/xolQAzxyq', 'JAIME ALBERTO ZULUAGA CARDONA', 3, 'img/fotosUsuarios/1736546184_jaime Alberto Zuluaga.JPG', 0, '', 'administracion@somospropiedad.com', 'Masculino', '3128330857', '', 0),
(4, 71772539, '$2y$10$SpT8LriD4ynC7Dre0w57LujZls0pECBtYn57iCV/qRKLSGNpFCX92', 'Ignacio Gomez', 1, 'img/fotosUsuarios/1731979802_eagle.png', 0, '', 'iggomez@extein.co', 'Masculino', '3127270287', '', 49),
(34, 1001142067, '$2y$10$srPPOfJIBCWpPi8nplHRa.90I8ulxicNGRjobRItwvX4VUkxBQuL6', 'Juan Zapata', 5, 'img/fotosUsuarios/arrendatarios/1001142067_arrendatario.png', 0, '2026-02-07', 'juandidoc11@gmail.com', 'Masculino', '3508284544', 'Diagonal 57 # 47 a 28', 22),
(30, 1017128026, '$2y$10$SgjM6iLHnHURimd7I.1QNOsoRpYNo7BXM4BaQacmxq6SpooE1UKgC', 'CARLOS ANDRES VELEZ GOMEZ', 2, 'img/fotosUsuarios/1736546150_Carlos Andres velez.JPG', 0, '', 'tesoreria@somospropiedad.com', 'Masculino', '311 7995949', '', 0),
(24, 1023624810, '$2y$10$F4Qw5y2vaFpDkt2B1gnkxusihMwETSp0UcyJ/ZXLo73QxA2FiV08a', 'SANTIAGO ÁRIAS HINCAPIE', 2, 'img/fotosUsuarios/1736545733_Santiago Hincapie.JPG', 0, '', 'asesor2@somospropiedad.com', 'Masculino', '316 8757733', '', 0),
(25, 1036652020, '$2y$10$k68jpJalIcQSWIGtlVa9C.8szaSlqNQ2J5/fElHaQmzaiIC.ZqiEa', 'MARIA DANIELA MOLINA MONTOYA', 2, 'img/fotosUsuarios/1736545976_Daniela Molina.JPG', 0, '', 'cartera@somospropiedad.com', 'Femenino', '319 4534526', '', 0),
(17, 1047996089, '$2y$10$BQeZOLqLwgYcBSo7SAZytOdNroSY7S/MXIFzQcg2iSy7xhqBC.jyq', 'Jhon Darwin Acevedo', 6, 'img/fotosUsuarios/1735942889_JHON-D.jpeg', 0, '', 'info@agenciaeaglesoftware.com', 'Masculino', '3015606006', 'carrera 40#98b35', 30);

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `banner`
--
ALTER TABLE `banner`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `barrios`
--
ALTER TABLE `barrios`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `campo_atributos`
--
ALTER TABLE `campo_atributos`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `certificados_residencia`
--
ALTER TABLE `certificados_residencia`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `consecutivo` (`consecutivo`),
  ADD KEY `idx_codigo_propiedad` (`codigo_propiedad`),
  ADD KEY `idx_consecutivo` (`consecutivo`);

--
-- Indices de la tabla `citas`
--
ALTER TABLE `citas`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `ciudades`
--
ALTER TABLE `ciudades`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `company`
--
ALTER TABLE `company`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `departamentos`
--
ALTER TABLE `departamentos`
  ADD PRIMARY KEY (`id_departamento`);

--
-- Indices de la tabla `fotos`
--
ALTER TABLE `fotos`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `municipios`
--
ALTER TABLE `municipios`
  ADD PRIMARY KEY (`id_municipio`),
  ADD KEY `departamento_id` (`departamento_id`);

--
-- Indices de la tabla `paises`
--
ALTER TABLE `paises`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `porcentajeaumento`
--
ALTER TABLE `porcentajeaumento`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `porcentaje` (`porcentaje`);

--
-- Indices de la tabla `proprieter`
--
ALTER TABLE `proprieter`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `codigo` (`codigo`);

--
-- Indices de la tabla `repairmen`
--
ALTER TABLE `repairmen`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `identificacion` (`identificacion`);

--
-- Indices de la tabla `report`
--
ALTER TABLE `report`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `codigoReporte` (`codigoReporte`),
  ADD KEY `codigo_propietario` (`codigo_propietario`),
  ADD KEY `id_reparador` (`id_reparador`);

--
-- Indices de la tabla `retiredtenants`
--
ALTER TABLE `retiredtenants`
  ADD PRIMARY KEY (`idRetired`),
  ADD UNIQUE KEY `registro` (`registro`),
  ADD KEY `codigoPropiedad` (`codigoPropiedad`);

--
-- Indices de la tabla `slider`
--
ALTER TABLE `slider`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `smtpconfig`
--
ALTER TABLE `smtpconfig`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `tenant`
--
ALTER TABLE `tenant`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `tipos`
--
ALTER TABLE `tipos`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`username`),
  ADD KEY `id` (`id`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `banner`
--
ALTER TABLE `banner`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `barrios`
--
ALTER TABLE `barrios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=194;

--
-- AUTO_INCREMENT de la tabla `campo_atributos`
--
ALTER TABLE `campo_atributos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT de la tabla `certificados_residencia`
--
ALTER TABLE `certificados_residencia`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `citas`
--
ALTER TABLE `citas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `ciudades`
--
ALTER TABLE `ciudades`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `company`
--
ALTER TABLE `company`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `departamentos`
--
ALTER TABLE `departamentos`
  MODIFY `id_departamento` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=100;

--
-- AUTO_INCREMENT de la tabla `fotos`
--
ALTER TABLE `fotos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `municipios`
--
ALTER TABLE `municipios`
  MODIFY `id_municipio` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1101;

--
-- AUTO_INCREMENT de la tabla `paises`
--
ALTER TABLE `paises`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `porcentajeaumento`
--
ALTER TABLE `porcentajeaumento`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `proprieter`
--
ALTER TABLE `proprieter`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `repairmen`
--
ALTER TABLE `repairmen`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `report`
--
ALTER TABLE `report`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `retiredtenants`
--
ALTER TABLE `retiredtenants`
  MODIFY `idRetired` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `slider`
--
ALTER TABLE `slider`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `smtpconfig`
--
ALTER TABLE `smtpconfig`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `tenant`
--
ALTER TABLE `tenant`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=390;

--
-- AUTO_INCREMENT de la tabla `tipos`
--
ALTER TABLE `tipos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT de la tabla `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=35;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `report`
--
ALTER TABLE `report`
  ADD CONSTRAINT `report_ibfk_1` FOREIGN KEY (`codigo_propietario`) REFERENCES `proprieter` (`codigo`) ON UPDATE CASCADE,
  ADD CONSTRAINT `report_ibfk_2` FOREIGN KEY (`id_reparador`) REFERENCES `repairmen` (`identificacion`) ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
