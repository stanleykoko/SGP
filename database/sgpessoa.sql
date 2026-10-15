-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Oct 15, 2026 at 10:53 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `sgpessoa`
--

-- --------------------------------------------------------

--
-- Table structure for table `cadastro`
--

CREATE TABLE `cadastro` (
  `CodigoUtilizador` int(11) NOT NULL,
  `nome` varchar(100) NOT NULL,
  `apelido` varchar(100) NOT NULL,
  `contacto` varchar(15) NOT NULL,
  `email` varchar(255) NOT NULL,
  `username` varchar(30) NOT NULL,
  `senha` varchar(255) NOT NULL,
  `nivelAcesso` enum('OPERADOR','SUPEROPERADOR','ADMINISTRADOR','AUDITOR') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `cadastro`
--

INSERT INTO `cadastro` (`CodigoUtilizador`, `nome`, `apelido`, `contacto`, `email`, `username`, `senha`, `nivelAcesso`) VALUES
(2, 'deisy', 'alfredo', '8477968635', 'deisyalfredo@gmail.com', 'deisy', 'dede123', 'OPERADOR'),
(7, 'deisy', 'muguambe', '854666999', 'deisymunguambe@gmail.com', 'dede', '$2y$10$rPXDIetH/2ixDHtl.fn6duoyyfSdzLsy.PDlm6oqjFDs5cQU5MU2e', 'ADMINISTRADOR'),
(9, 'Stanley', 'Koko', '842309109', 'kokito@gmail.com', 'Koko', 'Koko2007##', 'AUDITOR'),
(10, 'Paulo', 'Tembe', '877580325', 'paulo@gmail.com', 'paul', '$2y$10$EO9T5bWMfRjSQu8PSF2HzevWhAapgZaXd0YAUInuEll2L4RJbrsAS', 'SUPEROPERADOR'),
(11, 'Kelly', 'Sousa', '123456789', 'Kelly@gmail.com', 'keke', '$2y$10$YG.qaXUGHEf1bsVQRYNbX.kUJYlPHACYQXlJqSPbElNwXbAtfXsyq', 'OPERADOR'),
(12, 'Drake', 'OVO', '987654321', 'drake@gmail.com', 'Drizzy', '$2y$10$yfjTtk8oMU/1537p55m8/.OXBqi0SKqqjg9THrNyf3eyKUEwuG54e', 'OPERADOR'),
(13, 'Jorge', 'macamo', '65463322', 'Jorge@gmail.com', 'jojo', '$2y$10$f/WJPS.lR557RrXtcrNe4eIvSDp83QOrXMMdDs8kT6mqv2qbOKGWy', 'AUDITOR'),
(14, 'Epifania', 'Cuambe', '842507089', 'epifaniacuambe@gmail.com', 'Epi', '$2y$10$n3fdUnQQbP4cmOTUXD9SFOI9jHMvCDayO8oDUDrFqARHHDyR/Haau', 'AUDITOR');

-- --------------------------------------------------------

--
-- Table structure for table `continente`
--

CREATE TABLE `continente` (
  `codigoContinente` int(11) NOT NULL,
  `nomeContinente` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `continente`
--

INSERT INTO `continente` (`codigoContinente`, `nomeContinente`) VALUES
(1, 'Africa'),
(4, 'Asia'),
(5, 'Europa'),
(7, 'America');

-- --------------------------------------------------------

--
-- Table structure for table `log`
--

CREATE TABLE `log` (
  `codigoLog` int(11) NOT NULL,
  `usuario` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `perfil` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `accao` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `dataAccao` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `log`
--

INSERT INTO `log` (`codigoLog`, `usuario`, `perfil`, `accao`, `dataAccao`) VALUES
(1, 'dede', 'ADMINISTRADOR', 'Login efetuado', '2026-07-04 22:05:51'),
(2, 'dede', 'ADMINISTRADOR', 'Login efetuado', '2026-07-04 22:06:33'),
(3, 'dede', 'ADMINISTRADOR', 'Login efetuado', '2026-07-04 22:07:49'),
(4, 'dede', 'ADMINISTRADOR', 'Login efetuado', '2026-10-03 07:43:04'),
(5, 'dede', 'ADMINISTRADOR', 'Logout efetuado', '2026-10-03 07:43:23'),
(6, 'dede', 'ADMINISTRADOR', 'Login efetuado', '2026-10-03 07:43:45'),
(7, 'dede', 'ADMINISTRADOR', 'Criou Utilizador: Username Koko (AUDITOR)', '2026-10-03 07:45:23'),
(8, 'dede', 'ADMINISTRADOR', 'Logout efetuado', '2026-10-03 07:45:26'),
(9, 'Koko', 'AUDITOR', 'Login efetuado', '2026-10-03 07:45:36'),
(10, 'Koko', 'AUDITOR', 'Alterou a própria senha Utilizador: Senha alterada', '2026-10-03 07:46:38'),
(11, 'Koko', 'AUDITOR', 'Logout efetuado', '2026-10-03 08:02:50'),
(12, 'dede', 'ADMINISTRADOR', 'Login efetuado', '2026-10-03 10:40:02'),
(13, 'Koko', 'AUDITOR', 'Criou Nacionalidade: Mocambicana (Pessoa 4)', '2026-10-09 23:49:04'),
(14, 'deisy', 'ADMINISTRADOR', 'Iniciou sessão', '2026-10-10 11:35:56'),
(15, 'deisy', 'ADMINISTRADOR', 'Iniciou sessão', '2026-10-12 11:00:08'),
(16, 'Stanley', 'AUDITOR', 'Iniciou sessão', '2026-10-13 00:23:09'),
(17, 'deisy', 'OPERADOR', 'Iniciou sessão', '2026-10-13 00:30:06'),
(18, 'Paulo', 'SUPEROPERADOR', 'Iniciou sessão', '2026-10-13 00:31:13');

-- --------------------------------------------------------

--
-- Table structure for table `nacionalidade`
--

CREATE TABLE `nacionalidade` (
  `id_nacionalidade` int(11) NOT NULL,
  `Nome` varchar(100) NOT NULL,
  `dataRegisto` date NOT NULL,
  `id_pais` int(11) NOT NULL,
  `id_pessoa` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `nacionalidade`
--

INSERT INTO `nacionalidade` (`id_nacionalidade`, `Nome`, `dataRegisto`, `id_pais`, `id_pessoa`) VALUES
(1, 'angolana', '2000-03-20', 1, 1),
(3, 'Nigeriana', '2025-08-12', 4, 9),
(4, 'Chines', '2026-03-17', 3, 9),
(5, 'Nigeriana', '2026-10-03', 4, 5),
(6, 'Angolana', '2026-10-13', 8, 11),
(7, 'Mocambicana', '2026-10-01', 7, 4);

-- --------------------------------------------------------

--
-- Table structure for table `pais`
--

CREATE TABLE `pais` (
  `codigoPais` int(11) NOT NULL,
  `nomePais` varchar(100) NOT NULL,
  `idcontinente` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `pais`
--

INSERT INTO `pais` (`codigoPais`, `nomePais`, `idcontinente`) VALUES
(3, 'China', 4),
(4, 'Nigeria', 1),
(5, 'África do Sul', 1),
(7, 'Mocambique', 1),
(8, 'Angola', 1);

-- --------------------------------------------------------

--
-- Table structure for table `perfil`
--

CREATE TABLE `perfil` (
  `codigoPerfil` int(11) NOT NULL,
  `nomePerfil` varchar(30) NOT NULL,
  `descricao` varchar(150) NOT NULL DEFAULT '',
  `ver` tinyint(1) NOT NULL DEFAULT 0,
  `criar` tinyint(1) NOT NULL DEFAULT 0,
  `editar` tinyint(1) NOT NULL DEFAULT 0,
  `remover` tinyint(1) NOT NULL DEFAULT 0,
  `utilizadores` tinyint(1) NOT NULL DEFAULT 0,
  `logs` tinyint(1) NOT NULL DEFAULT 0,
  `perfis` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `perfil`
--

INSERT INTO `perfil` (`codigoPerfil`, `nomePerfil`, `descricao`, `ver`, `criar`, `editar`, `remover`, `utilizadores`, `logs`, `perfis`) VALUES
(1, 'OPERADOR', 'Lista e cria registos', 1, 1, 0, 0, 0, 0, 0),
(2, 'SUPEROPERADOR', 'CRUD completo', 1, 1, 1, 1, 0, 0, 0),
(3, 'ADMINISTRADOR', 'CRUD, utilizadores e perfis', 1, 1, 1, 1, 1, 0, 1),
(4, 'AUDITOR', 'CRUD e consulta de logs', 1, 1, 1, 1, 0, 1, 0);

-- --------------------------------------------------------

--
-- Table structure for table `pessoa`
--

CREATE TABLE `pessoa` (
  `codigo` int(11) NOT NULL,
  `nome` varchar(100) NOT NULL,
  `apelido` varchar(100) NOT NULL,
  `genero` varchar(100) NOT NULL,
  `estadoCivil` varchar(1000) NOT NULL,
  `anoNascimento` year(4) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `pessoa`
--

INSERT INTO `pessoa` (`codigo`, `nome`, `apelido`, `genero`, `estadoCivil`, `anoNascimento`) VALUES
(1, 'Sara', 'Cossa', 'Feminino', 'Casada', '2000'),
(4, 'Hiquima', 'Chomar', 'Feminino', 'Solteira', '2006'),
(5, 'Othane', 'Sitoe', 'Masculino', 'Solteiro', '2005'),
(9, 'Onana', 'Tembe', 'Masculino', 'Solteiro', '2000'),
(10, 'Gil', 'Tembe', 'Masculino', 'Casado', '2000'),
(11, 'Deisy', 'Alfredo', 'Feminino', 'Solteiro', '2007');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `cadastro`
--
ALTER TABLE `cadastro`
  ADD PRIMARY KEY (`CodigoUtilizador`);

--
-- Indexes for table `continente`
--
ALTER TABLE `continente`
  ADD PRIMARY KEY (`codigoContinente`);

--
-- Indexes for table `log`
--
ALTER TABLE `log`
  ADD PRIMARY KEY (`codigoLog`);

--
-- Indexes for table `nacionalidade`
--
ALTER TABLE `nacionalidade`
  ADD PRIMARY KEY (`id_nacionalidade`),
  ADD KEY `fk_pais` (`id_pais`),
  ADD KEY `id_pessoa` (`id_pessoa`);

--
-- Indexes for table `pais`
--
ALTER TABLE `pais`
  ADD PRIMARY KEY (`codigoPais`),
  ADD KEY `idcontinente` (`idcontinente`);

--
-- Indexes for table `perfil`
--
ALTER TABLE `perfil`
  ADD PRIMARY KEY (`codigoPerfil`),
  ADD UNIQUE KEY `nomePerfil` (`nomePerfil`);

--
-- Indexes for table `pessoa`
--
ALTER TABLE `pessoa`
  ADD PRIMARY KEY (`codigo`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `cadastro`
--
ALTER TABLE `cadastro`
  MODIFY `CodigoUtilizador` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `continente`
--
ALTER TABLE `continente`
  MODIFY `codigoContinente` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `log`
--
ALTER TABLE `log`
  MODIFY `codigoLog` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `nacionalidade`
--
ALTER TABLE `nacionalidade`
  MODIFY `id_nacionalidade` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `pais`
--
ALTER TABLE `pais`
  MODIFY `codigoPais` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `perfil`
--
ALTER TABLE `perfil`
  MODIFY `codigoPerfil` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `pessoa`
--
ALTER TABLE `pessoa`
  MODIFY `codigo` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `nacionalidade`
--
ALTER TABLE `nacionalidade`
  ADD CONSTRAINT `fk_pessoa` FOREIGN KEY (`id_pessoa`) REFERENCES `pessoa` (`codigo`);

--
-- Constraints for table `pais`
--
ALTER TABLE `pais`
  ADD CONSTRAINT `pais_ibfk_1` FOREIGN KEY (`idcontinente`) REFERENCES `continente` (`codigoContinente`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
