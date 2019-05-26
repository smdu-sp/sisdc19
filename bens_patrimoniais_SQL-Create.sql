/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;


-- Copiando estrutura do banco de dados para sicabe
CREATE DATABASE IF NOT EXISTS `sicabe` /*!40100 DEFAULT CHARACTER SET utf8 */;
USE `sicabe`;

-- Copiando estrutura para tabela sicabe.bens_patrimoniais
CREATE TABLE IF NOT EXISTS `bens_patrimoniais` (
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
  `dataInclusao` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Exportação de dados foi desmarcado.
-- Copiando estrutura para tabela sicabe.fiscais
CREATE TABLE IF NOT EXISTS `fiscais` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nomeServidor` varchar(50) DEFAULT NULL,
  `rf` varchar(50) DEFAULT NULL,
  `setor` varchar(50) DEFAULT NULL,
  `divisao` varchar(50) DEFAULT NULL,
  `dataCadastro` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Exportação de dados foi desmarcado.
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IF(@OLD_FOREIGN_KEY_CHECKS IS NULL, 1, @OLD_FOREIGN_KEY_CHECKS) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
