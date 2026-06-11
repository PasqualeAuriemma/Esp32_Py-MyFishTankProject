-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Creato il: Lug 21, 2023 alle 16:36
-- Versione del server: 8.0.26
-- Versione PHP: 8.0.22

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `my_myfishtank`
--
CREATE DATABASE IF NOT EXISTS `my_myfishtank` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci;
USE `my_myfishtank`;

-- --------------------------------------------------------

--
-- Struttura della tabella `ec_tab`
--

CREATE TABLE `ec_tab` (
  `id` bigint NOT NULL,
  `ec` float DEFAULT NULL,
  `data_send` varchar(10) DEFAULT NULL,
  `data_arrive` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Struttura della tabella `fertilization_tab`
--

CREATE TABLE `fertilization_tab` (
  `id` int NOT NULL,
  `data` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `k` float DEFAULT NULL,
  `mg` float DEFAULT NULL,
  `fe` float DEFAULT NULL,
  `rinverdente` float DEFAULT NULL,
  `p` float DEFAULT NULL,
  `n` float DEFAULT NULL,
  `npk` float DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Struttura della tabella `fertilization_volumes`
--

CREATE TABLE `fertilization_volumes` (
  `id` int NOT NULL,
  `fertilizzante` varchar(50) NOT NULL,
  `qnt` float NOT NULL,
  `data_inizio` date DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Struttura della tabella `ph_tab`
--

CREATE TABLE `ph_tab` (
  `id` bigint NOT NULL,
  `ph` float NOT NULL,
  `data_send` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `data_arrive` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Struttura della tabella `tds_tab`
--

CREATE TABLE `tds_tab` (
  `id` bigint NOT NULL,
  `tds` float DEFAULT NULL,
  `data_send` varchar(10) DEFAULT NULL,
  `data_arrive` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Struttura della tabella `temp_tab`
--

CREATE TABLE `temp_tab` (
  `id` bigint NOT NULL,
  `temperature` float NOT NULL,
  `data_send` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `data_arrive` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Struttura della tabella `users`
--

CREATE TABLE `users` (
  `id` int NOT NULL,
  `firstName` varchar(50) NOT NULL,
  `email` varchar(40) NOT NULL,
  `password` varchar(50) NOT NULL,
  `reset_token` varchar(255) DEFAULT NULL,
  `reset_token_expiry` datetime DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Struttura della tabella `user_tab`
--

CREATE TABLE `user_tab` (
  `username` varchar(16) NOT NULL,
  `password` varchar(8) NOT NULL,
  `email` varchar(30) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Struttura della tabella `watervalues_table`
--

CREATE TABLE `watervalues_table` (
  `id` bigint NOT NULL,
  `data` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `EC_PRE` float DEFAULT NULL,
  `EC_AFT` float DEFAULT NULL,
  `PH` float DEFAULT NULL,
  `no2` float DEFAULT NULL,
  `no3` float DEFAULT NULL,
  `gh` float DEFAULT NULL,
  `kh` float DEFAULT NULL,
  `po4` float DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Indici per le tabelle scaricate
--

--
-- Indici per le tabelle `ec_tab`
--
ALTER TABLE `ec_tab`
  ADD PRIMARY KEY (`id`);

--
-- Indici per le tabelle `fertilization_tab`
--
ALTER TABLE `fertilization_tab`
  ADD PRIMARY KEY (`id`);

--
-- Indici per le tabelle `fertilization_volumes`
--
ALTER TABLE `fertilization_volumes`
  ADD PRIMARY KEY (`id`);

--
-- Indici per le tabelle `ph_tab`
--
ALTER TABLE `ph_tab`
  ADD PRIMARY KEY (`id`);

--
-- Indici per le tabelle `tds_tab`
--
ALTER TABLE `tds_tab`
  ADD PRIMARY KEY (`id`);

--
-- Indici per le tabelle `temp_tab`
--
ALTER TABLE `temp_tab`
  ADD PRIMARY KEY (`id`);

--
-- Indici per le tabelle `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- Indici per le tabelle `user_tab`
--
ALTER TABLE `user_tab`
  ADD PRIMARY KEY (`username`);

--
-- Indici per le tabelle `watervalues_table`
--
ALTER TABLE `watervalues_table`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT per le tabelle scaricate
--

--
-- AUTO_INCREMENT per la tabella `ec_tab`
--
ALTER TABLE `ec_tab`
  MODIFY `id` bigint NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;

--
-- AUTO_INCREMENT per la tabella `fertilization_tab`
--
ALTER TABLE `fertilization_tab`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;

--
-- AUTO_INCREMENT per la tabella `fertilization_volumes`
--
ALTER TABLE `fertilization_volumes`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;

--
-- AUTO_INCREMENT per la tabella `ph_tab`
--
ALTER TABLE `ph_tab`
  MODIFY `id` bigint NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;

--
-- AUTO_INCREMENT per la tabella `tds_tab`
--
ALTER TABLE `tds_tab`
  MODIFY `id` bigint NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;

--
-- AUTO_INCREMENT per la tabella `temp_tab`
--
ALTER TABLE `temp_tab`
  MODIFY `id` bigint NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;

--
-- AUTO_INCREMENT per la tabella `users`
--
ALTER TABLE `users`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;

--
-- AUTO_INCREMENT per la tabella `watervalues_table`
--
ALTER TABLE `watervalues_table`
  MODIFY `id` bigint NOT NULL AUTO_INCREMENT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
