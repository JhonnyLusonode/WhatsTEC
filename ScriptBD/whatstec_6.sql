-- phpMyAdmin SQL Dump
-- version 5.0.2
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Tempo de geração: 22-Jun-2020 às 11:27
-- Versão do servidor: 10.4.11-MariaDB
-- versão do PHP: 7.4.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Banco de dados: `whatstec`
--

-- --------------------------------------------------------

--
-- Estrutura da tabela `agenda_contactos`
--

CREATE TABLE `agenda_contactos` (
  `username` varchar(10) NOT NULL,
  `contacto` varchar(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Estrutura da tabela `mensagem`
--

CREATE TABLE `mensagem` (
  `id_mensagem` int(11) NOT NULL,
  `Emissor_id` varchar(10) DEFAULT NULL,
  `Recetor_id` varchar(10) DEFAULT NULL,
  `Corpo` varchar(500) DEFAULT NULL,
  `Hora_envio` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `Online` tinyint(1) NOT NULL,
  `read` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Estrutura da tabela `utilizadores`
--

CREATE TABLE `utilizadores` (
  `username` varchar(10) NOT NULL,
  `password` varchar(500) DEFAULT NULL,
  `contacto` varchar(10) DEFAULT NULL,
  `email` varchar(10) DEFAULT NULL,
  `online` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Extraindo dados da tabela `utilizadores`
--

INSERT INTO `utilizadores` (`username`, `password`, `contacto`, `email`, `online`) VALUES
('admin', '$2y$10$r9wOpo.30oRfoyaxlyBZJuH/obCQ.5lrH7SQa7mEwviyX6K7HKO/W', '987654321', 'dvdbatista', 1),
('semnome', '$2y$10$8ZfFsOwXnwsMO3Ez5/Rv9.WXLNemXuCGPqRMpjsJPogJJJd1Lop4a', '123456789', 'semnome@ma', 0),
('Ze', '$2y$10$U3WJF9DDgXv2AFzDtdLPM.nkXVOphJ56WOEFVsUBvc.IiGwfbEzsq', '1234556789', 'Ze@mail.co', 1);

--
-- Índices para tabelas despejadas
--

--
-- Índices para tabela `agenda_contactos`
--
ALTER TABLE `agenda_contactos`
  ADD PRIMARY KEY (`username`,`contacto`),
  ADD KEY `contacto` (`contacto`);

--
-- Índices para tabela `mensagem`
--
ALTER TABLE `mensagem`
  ADD PRIMARY KEY (`id_mensagem`),
  ADD KEY `Emissor_id` (`Emissor_id`),
  ADD KEY `Recetor_id` (`Recetor_id`);

--
-- Índices para tabela `utilizadores`
--
ALTER TABLE `utilizadores`
  ADD PRIMARY KEY (`username`);

--
-- AUTO_INCREMENT de tabelas despejadas
--

--
-- AUTO_INCREMENT de tabela `mensagem`
--
ALTER TABLE `mensagem`
  MODIFY `id_mensagem` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=70;

--
-- Restrições para despejos de tabelas
--

--
-- Limitadores para a tabela `agenda_contactos`
--
ALTER TABLE `agenda_contactos`
  ADD CONSTRAINT `agenda_contactos_ibfk_1` FOREIGN KEY (`username`) REFERENCES `utilizadores` (`username`),
  ADD CONSTRAINT `agenda_contactos_ibfk_2` FOREIGN KEY (`contacto`) REFERENCES `utilizadores` (`username`),
  ADD CONSTRAINT `agenda_contactos_ibfk_3` FOREIGN KEY (`username`) REFERENCES `utilizadores` (`username`);

--
-- Limitadores para a tabela `mensagem`
--
ALTER TABLE `mensagem`
  ADD CONSTRAINT `mensagem_ibfk_1` FOREIGN KEY (`Emissor_id`) REFERENCES `utilizadores` (`username`),
  ADD CONSTRAINT `mensagem_ibfk_2` FOREIGN KEY (`Recetor_id`) REFERENCES `utilizadores` (`username`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
