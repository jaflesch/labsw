-- phpMyAdmin SQL Dump
-- version 4.2.11
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Generation Time: 14-Out-2016 às 06:00
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
-- Estrutura da tabela `equipe`
--

CREATE TABLE IF NOT EXISTS `equipe` (
`id` int(11) NOT NULL,
  `id_projeto` int(11) NOT NULL,
  `id_usuario` int(11) NOT NULL,
  `funcao` int(11) NOT NULL,
  `admin` int(1) NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=latin1;

--
-- Extraindo dados da tabela `equipe`
--

INSERT INTO `equipe` (`id`, `id_projeto`, `id_usuario`, `funcao`, `admin`) VALUES
(1, 1, 1, 1, 1),
(2, 2, 1, 1, 1),
(3, 3, 1, 1, 1),
(4, 4, 1, 1, 1),
(5, 5, 1, 1, 1),
(6, 1, 2, 1, 1);

-- --------------------------------------------------------

--
-- Estrutura da tabela `funcao`
--

CREATE TABLE IF NOT EXISTS `funcao` (
`id` int(11) NOT NULL,
  `descricao` varchar(32) NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=latin1;

--
-- Extraindo dados da tabela `funcao`
--

INSERT INTO `funcao` (`id`, `descricao`) VALUES
(1, 'Backend'),
(2, 'Frontend'),
(3, 'Tester'),
(4, 'Product Owner'),
(5, 'Designer');

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
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=latin1;

--
-- Extraindo dados da tabela `lembrete`
--

INSERT INTO `lembrete` (`id`, `id_usuario`, `titulo`, `descricao`, `prioridade`, `data`, `status`) VALUES
(1, 1, 'Teste Lembrete', 'abcéóúíá´l ççç\r\nasas\r\nqwqwd\r\ngf\r\ngh\r\nh\r\n', 1, '2016-08-28 14:04:00', 1),
(2, 2, 'Teste Lembrete 2', 'abcéóúíá´l ççç\r\nasas\r\nqwqwd\r\ngf\r\ngh\r\nh\r\n', 3, '2016-08-28 00:00:00', 1),
(3, 1, 'teste2', 'a\r\nb\r\n\r\n\r\nc\r\nas', 3, '2016-09-04 00:00:00', 0),
(4, 1, 'Lembrete novo', 'a\r\nk\r\nl\r\noo', 2, '2016-09-02 00:00:00', 0),
(9, 1, 'asasqawqwqw', 'asasas', 0, '2016-11-28 00:00:00', 1),
(12, 1, 'qiwjqwjqojw', '', 3, '0000-00-00 00:00:00', 0);

-- --------------------------------------------------------

--
-- Estrutura da tabela `log`
--

CREATE TABLE IF NOT EXISTS `log` (
`id` int(11) NOT NULL,
  `id_autor` int(11) NOT NULL,
  `id_tarefa` int(11) NOT NULL,
  `acao` varchar(128) NOT NULL,
  `data` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Estrutura da tabela `projeto`
--

CREATE TABLE IF NOT EXISTS `projeto` (
`id` int(11) NOT NULL,
  `id_admin` int(11) NOT NULL,
  `nome` varchar(32) NOT NULL,
  `imagem` varchar(40) NOT NULL,
  `identidade_visual` varchar(8) NOT NULL,
  `url` varchar(40) NOT NULL,
  `privacidade` int(1) NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=latin1;

--
-- Extraindo dados da tabela `projeto`
--

INSERT INTO `projeto` (`id`, `id_admin`, `nome`, `imagem`, `identidade_visual`, `url`, `privacidade`) VALUES
(1, 1, 'MHAAS', '', '#0eabbc', 'http://mhaas.com.br', 0),
(2, 1, 'Linkker', '', '#4E5A62', 'http://linkker.com.br', 1),
(3, 1, 'Contato Seguro', '', '#283c54', 'https://contatoseguro.com.br', 1),
(4, 1, 'Share Hunter', '', '#05a4e1', 'http://sharehunter.com.br', 1),
(5, 1, 'QualiStatus', '', '#a6ce42', 'http://www.qualistatus.com.br/', 1),
(6, 1, 'Projeto teste', '', '#f00', 'ashuahus', 1),
(7, 1, 'Fake', '', '#0ff', 'lÃ§lÃ§lÃ§llÃ§qwqwqw', 0),
(8, 1, 'wqwjksk', '', '#554647', '464as6as5', 1),
(9, 1, 'wqwjksk', '', '#554647', '464as6as5', 1),
(11, 1, 'kowqkowp', '', '#454545', '4644646', 1);

-- --------------------------------------------------------

--
-- Estrutura da tabela `tarefa`
--

CREATE TABLE IF NOT EXISTS `tarefa` (
`id` int(11) NOT NULL,
  `id_autor` int(11) NOT NULL,
  `id_usuarios_tarefa` int(11) NOT NULL,
  `id_projeto` int(11) NOT NULL,
  `titulo` varchar(128) NOT NULL,
  `id_categoria` int(11) NOT NULL,
  `prioridade` int(11) NOT NULL,
  `descricao_formal` text NOT NULL,
  `descricao_tecnica` text NOT NULL,
  `solucao` text NOT NULL,
  `resultados` text NOT NULL,
  `status_erro` int(11) NOT NULL,
  `tempo_previsto` varchar(10) NOT NULL,
  `status` int(11) NOT NULL,
  `data_criacao` datetime NOT NULL,
  `data_entrega` datetime NOT NULL,
  `data_fim` datetime NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;

--
-- Extraindo dados da tabela `tarefa`
--

INSERT INTO `tarefa` (`id`, `id_autor`, `id_usuarios_tarefa`, `id_projeto`, `titulo`, `id_categoria`, `prioridade`, `descricao_formal`, `descricao_tecnica`, `solucao`, `resultados`, `status_erro`, `tempo_previsto`, `status`, `data_criacao`, `data_entrega`, `data_fim`) VALUES
(1, 1, 0, 3, 'titulo', 1, 1, 'aksas', 'wpoqpwoqpo', 'jddjdjkxkjc', '', 0, '12h00min', 0, '2016-10-14 00:49:12', '2016-10-28 12:00:00', '0000-00-00 00:00:00');

-- --------------------------------------------------------

--
-- Estrutura da tabela `trofeu`
--

CREATE TABLE IF NOT EXISTS `trofeu` (
`id` int(11) NOT NULL,
  `nome` varchar(64) NOT NULL,
  `descricao` varchar(128) NOT NULL,
  `categoria` int(1) NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=26 DEFAULT CHARSET=latin1;

--
-- Extraindo dados da tabela `trofeu`
--

INSERT INTO `trofeu` (`id`, `nome`, `descricao`, `categoria`) VALUES
(1, 'user++', 'Mais um usuário utilizando o sistema. Seja bem-vindo e mãos à obra!', 1),
(2, 'Novato', 'A primeira de muitas!', 1),
(3, 'Assembler Guy', 'Para de falar só com as máquinas. Interaja com seus colegas de trabalho também!', 1),
(4, 'Programador C', 'Sua velocidade está boa. Porém, seu nível está muito baixo...', 1),
(5, 'Amador', 'Não vá se achando, você é junevil ainda, mas está no caminho certo. Continue assim!', 1),
(6, 'Réptil', 'Tenha paciência: um dia seu esforço será reconhecido e lembrarão de você.', 1),
(7, 'Javeiro', 'Embora exiba progresso, você ainda dá umas travadas', 1),
(8, 'Mito', 'Quem é esse cara? esse cara é um mito!', 1),
(9, 'Javascripter', 'Tenha mais segurança com suas obrigações:void(0);', 1),
(10, 'Experiente', 'Agora já são várias tarefas completas. Parabéns!', 1),
(11, 'Conhecedor do Universo', 'Você fez tantas tarefas que enxergou a resposta para todas as dúvidas.', 1),
(12, 'Hill Climber', 'A primeira montanha foi escalada, mas a jornada ainda continua (não fique preso a um ótimo local)!', 2),
(13, 'Programador C++', 'Espero que a maioria de suas tarefas tenham sido úteis.', 2),
(14, 'Polimorfo', 'Sua quantidade de tarefas está crescendo e, veja só, você está adquirindo classe no que faz!', 2),
(15, '3 Dígitos', 'Nesse ritmo, logo precisaremos de 7 bits para a contagem.', 3),
(16, 'PHP Dev', 'Deus tá vendo essa gambiarra.', 2),
(17, 'Recursivo', 'Você está craque no paradigma funcional: funcionou? então tá pronto!', 2),
(18, 'Ray Tracer', 'Seu desempenho está mais lindo que um algoritmo de computação gráfica.', 2),
(19, 'Escalonador Humano', 'Preempção não existe no seu dicionário.', 2),
(20, 'Fortran Man', 'Você já está na história, mas ainda é cedo para parar.', 2),
(21, 'Patriarca', 'Respeita o pai.', 3),
(22, 'COBOL-Rex', 'Após tantas tarefas, você começa a se sentir na Era Jurássica.', 3),
(23, 'Tautólogo', 'if(tarefa.done()) return TRUE;', 3),
(24, 'Berserker', 'Suas habilidades estão tão altas que você pode ser considerado um monstro. BIRL!', 3),
(25, 'Overflower', 'Não culpe o projetista do sistema por não prever que você realizaria tantas tarefas!', 4);

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
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=latin1;

--
-- Extraindo dados da tabela `usuario`
--

INSERT INTO `usuario` (`id`, `nome`, `login`, `senha`, `nivel_privilegios`, `email`, `tarefas_completas`) VALUES
(1, 'Jean Flesch', 'Jean', 'b71985397688d6f1820685dde534981b', 0, 'jean@mhaas.com.br', 0),
(2, 'Alberto Pena Neto', 'Alberto', '177dacb14b34103960ec27ba29bd686b', 1, 'alberto@mhaas.com.br', 0);

-- --------------------------------------------------------

--
-- Estrutura da tabela `usuarios_tarefa`
--

CREATE TABLE IF NOT EXISTS `usuarios_tarefa` (
`id` int(11) NOT NULL,
  `id_tarefa` int(11) NOT NULL,
  `id_usuario` int(11) NOT NULL,
  `tempo_total` float NOT NULL,
  `tempo_sessao` float NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `equipe`
--
ALTER TABLE `equipe`
 ADD PRIMARY KEY (`id`);

--
-- Indexes for table `funcao`
--
ALTER TABLE `funcao`
 ADD PRIMARY KEY (`id`);

--
-- Indexes for table `lembrete`
--
ALTER TABLE `lembrete`
 ADD PRIMARY KEY (`id`);

--
-- Indexes for table `log`
--
ALTER TABLE `log`
 ADD PRIMARY KEY (`id`);

--
-- Indexes for table `projeto`
--
ALTER TABLE `projeto`
 ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tarefa`
--
ALTER TABLE `tarefa`
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
-- Indexes for table `usuarios_tarefa`
--
ALTER TABLE `usuarios_tarefa`
 ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `equipe`
--
ALTER TABLE `equipe`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=7;
--
-- AUTO_INCREMENT for table `funcao`
--
ALTER TABLE `funcao`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=6;
--
-- AUTO_INCREMENT for table `lembrete`
--
ALTER TABLE `lembrete`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=13;
--
-- AUTO_INCREMENT for table `log`
--
ALTER TABLE `log`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `projeto`
--
ALTER TABLE `projeto`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=14;
--
-- AUTO_INCREMENT for table `tarefa`
--
ALTER TABLE `tarefa`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT for table `trofeu`
--
ALTER TABLE `trofeu`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=26;
--
-- AUTO_INCREMENT for table `trofeu_usuario`
--
ALTER TABLE `trofeu_usuario`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT for table `usuario`
--
ALTER TABLE `usuario`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=3;
--
-- AUTO_INCREMENT for table `usuarios_tarefa`
--
ALTER TABLE `usuarios_tarefa`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
