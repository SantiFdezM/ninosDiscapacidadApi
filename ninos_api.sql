-- phpMyAdmin SQL Dump
-- version 4.5.0.2
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Apr 28, 2016 at 08:04 AM
-- Server version: 10.0.17-MariaDB
-- PHP Version: 5.6.14

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `ninos_api`
--

-- --------------------------------------------------------

--
-- Table structure for table `applications`
--

CREATE TABLE `applications` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `developer` varchar(100) NOT NULL,
  `mail` varchar(100) NOT NULL,
  `token` varchar(40) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `applications`
--

INSERT INTO `applications` (`id`, `name`, `developer`, `mail`, `token`) VALUES
(1, 'aplicacion para ninos', 'santiago', 'santiatlas11@hotmail.com', 'e0d347d31803c65680ed00a4470ed6e4591069f9');

-- --------------------------------------------------------

--
-- Table structure for table `games`
--

CREATE TABLE `games` (
  `id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `developer` varchar(50) NOT NULL,
  `mail` varchar(50) NOT NULL,
  `token` varchar(40) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `games`
--

INSERT INTO `games` (`id`, `name`, `developer`, `mail`, `token`) VALUES
(1, 'rompecabezas', 'Santiago', 'santiatlas11@hotmail.com', 'f931533a29ae3e6446e9dc9dd662cd36f5ea3856'),
(2, 'Sudoku', 'Estefania', 'estefi_ma94@hotmail.com', '16516dc3709fb21a2a4d4fa37734da5f939ce75c');

-- --------------------------------------------------------

--
-- Table structure for table `game_metrics`
--

CREATE TABLE `game_metrics` (
  `id` int(11) NOT NULL,
  `metric` varchar(100) DEFAULT NULL,
  `value` varchar(100) DEFAULT NULL,
  `id_patient` int(11) DEFAULT NULL,
  `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `id_game` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `game_metrics`
--

INSERT INTO `game_metrics` (`id`, `metric`, `value`, `id_patient`, `date`, `id_game`) VALUES
(1, 'SCORE', '28', 3, '2016-04-28 00:44:38', 1),
(2, 'TIME', '122', 3, '2016-04-28 00:44:38', 1),
(3, 'SCORE', '98', 3, '2016-04-28 00:44:38', 1),
(4, 'SCORE', '98', 3, '2016-04-28 00:44:38', 2);

-- --------------------------------------------------------

--
-- Table structure for table `patient`
--

CREATE TABLE `patient` (
  `id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `username` varchar(20) NOT NULL,
  `password` varchar(40) NOT NULL,
  `active` int(11) NOT NULL,
  `token` varchar(40) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `patient`
--

INSERT INTO `patient` (`id`, `name`, `username`, `password`, `active`, `token`) VALUES
(3, 'Santiago Paciente Actualizado', 'santifdezm', '711383a59fda05336fd2ccf70c8059d1523eb41a', 1, '1bb3120139b9e05edac6e8980acd47f444878eb0'),
(4, 'Roberto Ferrer', 'rferrer', '711383a59fda05336fd2ccf70c8059d1523eb41a', 1, ''),
(5, 'Carlos BaÃ±os', 'cbaÃ±os', '711383a59fda05336fd2ccf70c8059d1523eb41a', 1, '');

-- --------------------------------------------------------

--
-- Table structure for table `patient_doctor`
--

CREATE TABLE `patient_doctor` (
  `id` int(11) NOT NULL,
  `id_patient` int(11) DEFAULT NULL,
  `id_doctor` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `patient_doctor`
--

INSERT INTO `patient_doctor` (`id`, `id_patient`, `id_doctor`) VALUES
(4, 3, 1),
(5, 3, 5),
(6, 4, 1),
(7, 5, 5);

-- --------------------------------------------------------

--
-- Table structure for table `patient_parent`
--

CREATE TABLE `patient_parent` (
  `id` int(11) NOT NULL,
  `id_patient` int(11) DEFAULT NULL,
  `id_parent` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `patient_parent`
--

INSERT INTO `patient_parent` (`id`, `id_patient`, `id_parent`) VALUES
(3, 3, 2),
(4, 3, 6),
(5, 4, 6);

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE `user` (
  `id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `username` varchar(20) NOT NULL,
  `mail` varchar(50) NOT NULL,
  `cellphone` varchar(10) NOT NULL,
  `password` varchar(40) NOT NULL,
  `active` int(11) NOT NULL,
  `token` varchar(40) NOT NULL,
  `kind` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`id`, `name`, `username`, `mail`, `cellphone`, `password`, `active`, `token`, `kind`) VALUES
(1, 'Santiago Fernandez', 'santifdezm', 'santiatlas11@hotmail.com', '5549808540', '711383a59fda05336fd2ccf70c8059d1523eb41a', 1, '8eb54b89e4b97ed55c85486aa474916cf67cfe64', 1),
(2, 'Santiago Papa', 'santifdezmparent', 'santiatlas11@hotmail.com', '5549808540', '711383a59fda05336fd2ccf70c8059d1523eb41a', 1, '', 2),
(3, 'Estefania Morales', 'estefima', 'estefi_ma94@hotmail.com', '2222223403', '99800b85d3383e3a2fb45eb7d0066a4879a9dad0', 1, '', 1),
(4, 'Leticia Madero', 'letosh', 'letosh@hotmail.com', '2222223403', '99800b85d3383e3a2fb45eb7d0066a4879a9dad0', 1, '', 1),
(5, 'Pedro Fernandez', 'pedrof', 'pedro@hotmail.com', '2222223403', '99800b85d3383e3a2fb45eb7d0066a4879a9dad0', 1, '', 1),
(6, 'Arantxa Fernandez', 'arantxafm', 'arantxa@hotmail.com', '2222223403', '99800b85d3383e3a2fb45eb7d0066a4879a9dad0', 1, '', 2);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `applications`
--
ALTER TABLE `applications`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `games`
--
ALTER TABLE `games`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `game_metrics`
--
ALTER TABLE `game_metrics`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_patient` (`id_patient`),
  ADD KEY `id_game` (`id_game`);

--
-- Indexes for table `patient`
--
ALTER TABLE `patient`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `patient_doctor`
--
ALTER TABLE `patient_doctor`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_patient` (`id_patient`),
  ADD KEY `id_doctor` (`id_doctor`);

--
-- Indexes for table `patient_parent`
--
ALTER TABLE `patient_parent`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_patient` (`id_patient`),
  ADD KEY `id_parent` (`id_parent`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `applications`
--
ALTER TABLE `applications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT for table `games`
--
ALTER TABLE `games`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
--
-- AUTO_INCREMENT for table `game_metrics`
--
ALTER TABLE `game_metrics`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;
--
-- AUTO_INCREMENT for table `patient`
--
ALTER TABLE `patient`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;
--
-- AUTO_INCREMENT for table `patient_doctor`
--
ALTER TABLE `patient_doctor`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;
--
-- AUTO_INCREMENT for table `patient_parent`
--
ALTER TABLE `patient_parent`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;
--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;
--
-- Constraints for dumped tables
--

--
-- Constraints for table `game_metrics`
--
ALTER TABLE `game_metrics`
  ADD CONSTRAINT `game_metrics_ibfk_1` FOREIGN KEY (`id_patient`) REFERENCES `patient` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `game_metrics_ibfk_2` FOREIGN KEY (`id_game`) REFERENCES `games` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `patient_doctor`
--
ALTER TABLE `patient_doctor`
  ADD CONSTRAINT `patient_doctor_ibfk_1` FOREIGN KEY (`id_patient`) REFERENCES `patient` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `patient_doctor_ibfk_2` FOREIGN KEY (`id_doctor`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `patient_parent`
--
ALTER TABLE `patient_parent`
  ADD CONSTRAINT `patient_parent_ibfk_1` FOREIGN KEY (`id_patient`) REFERENCES `patient` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `patient_parent_ibfk_2` FOREIGN KEY (`id_parent`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
