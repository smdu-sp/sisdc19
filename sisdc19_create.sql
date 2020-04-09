-- --------------------------------------------------------
-- Servidor:                     127.0.0.1
-- Versão do servidor:           10.4.11-MariaDB - mariadb.org binary distribution
-- OS do Servidor:               Win64
-- HeidiSQL Versão:              10.3.0.5771
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;


-- Copiando estrutura do banco de dados para sisdc19
CREATE DATABASE IF NOT EXISTS `sisdc19` /*!40100 DEFAULT CHARACTER SET utf8 */;
USE `sisdc19`;

-- Copiando estrutura para tabela sisdc19.doacoes
CREATE TABLE IF NOT EXISTS `doacoes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `entrada` varchar(50) DEFAULT NULL,
  `data_entrada` datetime DEFAULT NULL,
  `id_responsavel` tinyint(4) DEFAULT NULL,
  `responsavel_atendimento` varchar(50) DEFAULT NULL,
  `doador` varchar(50) DEFAULT NULL,
  `status` varchar(50) DEFAULT NULL,
  `numero_sei` bigint(16) DEFAULT NULL,
  `observacao` text DEFAULT NULL,
  `comentario_sms` varchar(50) DEFAULT NULL,
  `relatorio_sei` text DEFAULT NULL,
  `itens_pendentes_sei` varchar(255) DEFAULT NULL,
  `monitoramento` varchar(255) DEFAULT NULL,
  `data_inclusao` timestamp NOT NULL DEFAULT current_timestamp(),
  `data_alteracao` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4;

-- Copiando dados para a tabela sisdc19.doacoes: ~1 rows (aproximadamente)
DELETE FROM `doacoes`;
/*!40000 ALTER TABLE `doacoes` DISABLE KEYS */;
INSERT INTO `doacoes` (`id`, `entrada`, `data_entrada`, `id_responsavel`, `responsavel_atendimento`, `doador`, `status`, `numero_sei`, `observacao`, `comentario_sms`, `relatorio_sei`, `itens_pendentes_sei`, `monitoramento`, `data_inclusao`, `data_alteracao`) VALUES
	(2, 'hoje', NULL, 5, 'Renan', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2020-04-09 16:19:00', '2020-04-09 16:19:12');
/*!40000 ALTER TABLE `doacoes` ENABLE KEYS */;

-- Copiando estrutura para tabela sisdc19.responsaveis
CREATE TABLE IF NOT EXISTS `responsaveis` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `rf` varchar(50) DEFAULT NULL,
  `nome` varchar(50) DEFAULT NULL,
  `data_inclusao` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4;

-- Copiando dados para a tabela sisdc19.responsaveis: ~0 rows (aproximadamente)
DELETE FROM `responsaveis`;
/*!40000 ALTER TABLE `responsaveis` DISABLE KEYS */;
INSERT INTO `responsaveis` (`id`, `rf`, `nome`, `data_inclusao`) VALUES
	(1, 'd851026', 'Renan', '2020-04-09 16:46:51');
/*!40000 ALTER TABLE `responsaveis` ENABLE KEYS */;

-- Copiando estrutura para tabela sisdc19._bens
CREATE TABLE IF NOT EXISTS `_bens` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nomeServidor` varchar(250) DEFAULT NULL,
  `rf` varchar(8) DEFAULT NULL,
  `orgao` varchar(50) DEFAULT NULL,
  `setor` varchar(50) DEFAULT NULL,
  `divisao` varchar(50) DEFAULT NULL,
  `sala` varchar(50) DEFAULT NULL,
  `andar` varchar(50) DEFAULT NULL,
  `chapa` varchar(50) DEFAULT NULL,
  `chapaOutraUnidade` varchar(50) DEFAULT NULL,
  `nomeOutraUnidade` varchar(50) DEFAULT NULL,
  `discriminacao` varchar(250) DEFAULT NULL,
  `descricaoPersonalizada` varchar(250) DEFAULT NULL,
  `servivel` varchar(10) DEFAULT NULL,
  `numSerie` varchar(50) DEFAULT NULL,
  `marca` varchar(50) DEFAULT NULL,
  `modelo` varchar(50) DEFAULT NULL,
  `cor` varchar(50) DEFAULT NULL,
  `comprimento` varchar(50) DEFAULT NULL,
  `profundidade` varchar(50) DEFAULT NULL,
  `altura` varchar(50) DEFAULT NULL,
  `conferido` varchar(50) DEFAULT NULL,
  `dataInclusao` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Copiando dados para a tabela sisdc19._bens: ~0 rows (aproximadamente)
DELETE FROM `_bens`;
/*!40000 ALTER TABLE `_bens` DISABLE KEYS */;
/*!40000 ALTER TABLE `_bens` ENABLE KEYS */;

-- Copiando estrutura para tabela sisdc19._fiscais
CREATE TABLE IF NOT EXISTS `_fiscais` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nomeServidor` varchar(50) DEFAULT NULL,
  `rf` varchar(50) DEFAULT NULL,
  `setor` varchar(50) DEFAULT NULL,
  `divisao` varchar(50) DEFAULT NULL,
  `dataCadastro` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Copiando dados para a tabela sisdc19._fiscais: ~0 rows (aproximadamente)
DELETE FROM `_fiscais`;
/*!40000 ALTER TABLE `_fiscais` DISABLE KEYS */;
/*!40000 ALTER TABLE `_fiscais` ENABLE KEYS */;

/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IF(@OLD_FOREIGN_KEY_CHECKS IS NULL, 1, @OLD_FOREIGN_KEY_CHECKS) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
