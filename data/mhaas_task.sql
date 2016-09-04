-- phpMyAdmin SQL Dump
-- version 4.2.11
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Generation Time: 04-Set-2016 às 21:36
-- Versão do servidor: 5.6.21
-- PHP Version: 5.6.3

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `mhaas_task`
--
CREATE DATABASE IF NOT EXISTS `mhaas_task` DEFAULT CHARACTER SET latin1 COLLATE latin1_swedish_ci;
USE `mhaas_task`;

-- --------------------------------------------------------

--
-- Estrutura da tabela `lembrete`
--

CREATE TABLE IF NOT EXISTS `lembrete` (
`id` int(11) NOT NULL,
  `id_usuario` int(11) NOT NULL,
  `titulo` varchar(128) NOT NULL,
  `descricao` text NOT NULL,
  `prioridade` int(11) NOT NULL,
  `data` datetime NOT NULL,
  `status` int(1) NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=latin1;

--
-- Extraindo dados da tabela `lembrete`
--

INSERT INTO `lembrete` (`id`, `id_usuario`, `titulo`, `descricao`, `prioridade`, `data`, `status`) VALUES
(1, 1, 'Teste Lembrete', 'abcéóúíá´l ççç\r\nasas\r\nqwqwd\r\ngf\r\ngh\r\nh\r\n', 1, '2016-08-28 14:04:00', 0),
(2, 2, 'Teste Lembrete 2', 'abcéóúíá´l ççç\r\nasas\r\nqwqwd\r\ngf\r\ngh\r\nh\r\n', 3, '2016-08-28 00:00:00', 1),
(3, 1, 'teste2', 'a\r\nb\r\n\r\n\r\nc\r\nas', 3, '2016-09-04 00:00:00', 0),
(4, 1, 'Lembrete novo', 'a\r\nk\r\nl\r\noo', 2, '2016-09-02 00:00:00', 0),
(5, 1, 'Com reload agora', 'penis', 0, '2016-09-29 00:00:00', 0),
(6, 1, 'Mais um teste agora vai', 'abc', 1, '2016-09-05 00:00:00', 0);

-- --------------------------------------------------------

--
-- Estrutura da tabela `trofeu`
--

CREATE TABLE IF NOT EXISTS `trofeu` (
`id` int(11) NOT NULL,
  `nome` varchar(64) NOT NULL,
  `descricao` varchar(128) NOT NULL,
  `categoria` int(1) NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;

--
-- Extraindo dados da tabela `trofeu`
--

INSERT INTO `trofeu` (`id`, `nome`, `descricao`, `categoria`) VALUES
(1, 'user++', 'Mais um usuário utilizando o sistema. Seja bem-vindo e mãos à obra!', 1);

-- --------------------------------------------------------

--
-- Estrutura da tabela `trofeu_usuario`
--

CREATE TABLE IF NOT EXISTS `trofeu_usuario` (
`id` int(11) NOT NULL,
  `id_usuario` int(11) NOT NULL,
  `id_trofeu` int(11) NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;

--
-- Extraindo dados da tabela `trofeu_usuario`
--

INSERT INTO `trofeu_usuario` (`id`, `id_usuario`, `id_trofeu`) VALUES
(1, 1, 1);

-- --------------------------------------------------------

--
-- Estrutura da tabela `usuario`
--

CREATE TABLE IF NOT EXISTS `usuario` (
`id` int(11) NOT NULL,
  `nome` varchar(64) NOT NULL,
  `login` varchar(64) NOT NULL,
  `senha` varchar(32) NOT NULL,
  `nivel_privilegios` int(1) NOT NULL,
  `email` varchar(32) NOT NULL,
  `tarefas_completas` int(11) NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;

--
-- Extraindo dados da tabela `usuario`
--

INSERT INTO `usuario` (`id`, `nome`, `login`, `senha`, `nivel_privilegios`, `email`, `tarefas_completas`) VALUES
(1, 'Jean Flesch', 'Jean', 'b71985397688d6f1820685dde534981b', 0, 'jean@mhaas.com.br', 0);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `lembrete`
--
ALTER TABLE `lembrete`
 ADD PRIMARY KEY (`id`);

--
-- Indexes for table `trofeu`
--
ALTER TABLE `trofeu`
 ADD PRIMARY KEY (`id`);

--
-- Indexes for table `trofeu_usuario`
--
ALTER TABLE `trofeu_usuario`
 ADD PRIMARY KEY (`id`);

--
-- Indexes for table `usuario`
--
ALTER TABLE `usuario`
 ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `lembrete`
--
ALTER TABLE `lembrete`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=7;
--
-- AUTO_INCREMENT for table `trofeu`
--
ALTER TABLE `trofeu`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT for table `trofeu_usuario`
--
ALTER TABLE `trofeu_usuario`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT for table `usuario`
--
ALTER TABLE `usuario`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=2;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
