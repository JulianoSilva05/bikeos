-- =====================================================
-- SCRIPT DE DEPLOY PARA HOSTGATOR - BIKEOS
-- =====================================================
-- Database: jul33718_bikeos
-- User: jul33718_juliano
-- Password: Jjmgmz56@
-- =====================================================

-- Configurações iniciais
SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

-- =====================================================
-- ESTRUTURA DAS TABELAS
-- =====================================================

-- Tabela: bicicletas
CREATE TABLE IF NOT EXISTS `bicicletas` (
  `id` int NOT NULL AUTO_INCREMENT,
  `cliente_id` int NOT NULL,
  `marca` varchar(100) DEFAULT NULL,
  `modelo` varchar(100) NOT NULL,
  `cor` varchar(50) DEFAULT NULL,
  `aro` varchar(10) DEFAULT NULL,
  `numero_serie` varchar(100) DEFAULT NULL,
  `observacoes` text,
  `imagem_url` varchar(255) DEFAULT NULL,
  `avarias` text,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_cliente_id` (`cliente_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabela: clientes
CREATE TABLE IF NOT EXISTS `clientes` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nome` varchar(255) NOT NULL,
  `email` varchar(255) DEFAULT NULL,
  `telefone` varchar(20) DEFAULT NULL,
  `cpf` varchar(14) DEFAULT NULL,
  `endereco` text,
  `cidade` varchar(100) DEFAULT NULL,
  `estado` varchar(2) DEFAULT NULL,
  `cep` varchar(10) DEFAULT NULL,
  `data_nascimento` date DEFAULT NULL,
  `observacoes` text,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `idx_email` (`email`),
  KEY `idx_cpf` (`cpf`),
  KEY `idx_nome` (`nome`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabela: produtos
CREATE TABLE IF NOT EXISTS `produtos` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nome` varchar(255) NOT NULL,
  `descricao` text,
  `categoria` varchar(100) DEFAULT NULL,
  `marca` varchar(100) DEFAULT NULL,
  `modelo` varchar(100) DEFAULT NULL,
  `codigo_barras` varchar(50) DEFAULT NULL,
  `preco_custo` decimal(10,2) DEFAULT '0.00',
  `preco_venda` decimal(10,2) NOT NULL DEFAULT '0.00',
  `estoque_atual` int DEFAULT '0',
  `estoque_minimo` int DEFAULT '0',
  `unidade` varchar(10) DEFAULT 'UN',
  `ativo` tinyint(1) DEFAULT '1',
  `imagem_url` varchar(255) DEFAULT NULL,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_categoria` (`categoria`),
  KEY `idx_codigo_barras` (`codigo_barras`),
  KEY `idx_nome` (`nome`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabela: servicos
CREATE TABLE IF NOT EXISTS `servicos` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nome` varchar(255) NOT NULL,
  `descricao` text,
  `categoria` varchar(100) DEFAULT NULL,
  `preco` decimal(10,2) NOT NULL DEFAULT '0.00',
  `tempo_estimado` int DEFAULT NULL COMMENT 'Tempo em minutos',
  `ativo` tinyint(1) DEFAULT '1',
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_categoria` (`categoria`),
  KEY `idx_nome` (`nome`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabela: ordens_servico
CREATE TABLE IF NOT EXISTS `ordens_servico` (
  `id` int NOT NULL AUTO_INCREMENT,
  `numero_os` varchar(20) NOT NULL,
  `cliente_id` int NOT NULL,
  `bicicleta_id` int DEFAULT NULL,
  `data_entrada` datetime DEFAULT CURRENT_TIMESTAMP,
  `data_prevista` datetime DEFAULT NULL,
  `data_conclusao` datetime DEFAULT NULL,
  `status` enum('aberta','em_andamento','aguardando_peca','concluida','entregue','cancelada') DEFAULT 'aberta',
  `descricao_problema` text,
  `observacoes` text,
  `valor_total` decimal(10,2) DEFAULT '0.00',
  `valor_pago` decimal(10,2) DEFAULT '0.00',
  `forma_pagamento` varchar(50) DEFAULT NULL,
  `tecnico_responsavel` varchar(255) DEFAULT NULL,
  `prioridade` enum('baixa','normal','alta','urgente') DEFAULT 'normal',
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `idx_numero_os` (`numero_os`),
  KEY `idx_cliente_id` (`cliente_id`),
  KEY `idx_bicicleta_id` (`bicicleta_id`),
  KEY `idx_status` (`status`),
  KEY `idx_data_entrada` (`data_entrada`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabela: os_itens
CREATE TABLE IF NOT EXISTS `os_itens` (
  `id` int NOT NULL AUTO_INCREMENT,
  `os_id` int NOT NULL,
  `tipo` enum('produto','servico') NOT NULL,
  `item_id` int NOT NULL,
  `descricao` varchar(255) NOT NULL,
  `quantidade` decimal(10,3) DEFAULT '1.000',
  `preco_unitario` decimal(10,2) NOT NULL,
  `preco_total` decimal(10,2) NOT NULL,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_os_id` (`os_id`),
  KEY `idx_tipo_item` (`tipo`, `item_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabela: vendas
CREATE TABLE IF NOT EXISTS `vendas` (
  `id` int NOT NULL AUTO_INCREMENT,
  `numero_venda` varchar(20) NOT NULL,
  `cliente_id` int DEFAULT NULL,
  `data_venda` datetime DEFAULT CURRENT_TIMESTAMP,
  `valor_total` decimal(10,2) NOT NULL DEFAULT '0.00',
  `desconto` decimal(10,2) DEFAULT '0.00',
  `valor_final` decimal(10,2) NOT NULL DEFAULT '0.00',
  `forma_pagamento` varchar(50) DEFAULT NULL,
  `status` enum('pendente','pago','cancelado') DEFAULT 'pendente',
  `observacoes` text,
  `vendedor` varchar(255) DEFAULT NULL,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `idx_numero_venda` (`numero_venda`),
  KEY `idx_cliente_id` (`cliente_id`),
  KEY `idx_data_venda` (`data_venda`),
  KEY `idx_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabela: venda_itens
CREATE TABLE IF NOT EXISTS `venda_itens` (
  `id` int NOT NULL AUTO_INCREMENT,
  `venda_id` int NOT NULL,
  `produto_id` int NOT NULL,
  `descricao` varchar(255) NOT NULL,
  `quantidade` decimal(10,3) NOT NULL DEFAULT '1.000',
  `preco_unitario` decimal(10,2) NOT NULL,
  `preco_total` decimal(10,2) NOT NULL,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_venda_id` (`venda_id`),
  KEY `idx_produto_id` (`produto_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabela: usuarios (para login do sistema)
CREATE TABLE IF NOT EXISTS `usuarios` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nome` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `senha` varchar(255) NOT NULL,
  `nivel` enum('admin','funcionario','visualizador') DEFAULT 'funcionario',
  `ativo` tinyint(1) DEFAULT '1',
  `ultimo_login` datetime DEFAULT NULL,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `idx_email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabela: configuracoes
CREATE TABLE IF NOT EXISTS `configuracoes` (
  `id` int NOT NULL AUTO_INCREMENT,
  `chave` varchar(100) NOT NULL,
  `valor` text,
  `descricao` varchar(255) DEFAULT NULL,
  `tipo` enum('string','number','boolean','json') DEFAULT 'string',
  `updated_at` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `idx_chave` (`chave`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- FOREIGN KEYS
-- =====================================================

ALTER TABLE `bicicletas`
  ADD CONSTRAINT `fk_bicicletas_cliente` FOREIGN KEY (`cliente_id`) REFERENCES `clientes` (`id`) ON DELETE CASCADE;

ALTER TABLE `ordens_servico`
  ADD CONSTRAINT `fk_os_cliente` FOREIGN KEY (`cliente_id`) REFERENCES `clientes` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_os_bicicleta` FOREIGN KEY (`bicicleta_id`) REFERENCES `bicicletas` (`id`) ON DELETE SET NULL;

ALTER TABLE `os_itens`
  ADD CONSTRAINT `fk_os_itens_os` FOREIGN KEY (`os_id`) REFERENCES `ordens_servico` (`id`) ON DELETE CASCADE;

ALTER TABLE `vendas`
  ADD CONSTRAINT `fk_vendas_cliente` FOREIGN KEY (`cliente_id`) REFERENCES `clientes` (`id`) ON DELETE SET NULL;

ALTER TABLE `venda_itens`
  ADD CONSTRAINT `fk_venda_itens_venda` FOREIGN KEY (`venda_id`) REFERENCES `vendas` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_venda_itens_produto` FOREIGN KEY (`produto_id`) REFERENCES `produtos` (`id`) ON DELETE CASCADE;

-- =====================================================
-- DADOS INICIAIS
-- =====================================================

-- Usuário administrador padrão
INSERT INTO `usuarios` (`nome`, `email`, `senha`, `nivel`) VALUES
('Administrador', 'admin@bikeos.com.br', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin');
-- Senha padrão: password (altere após o primeiro login)

-- Configurações básicas do sistema
INSERT INTO `configuracoes` (`chave`, `valor`, `descricao`, `tipo`) VALUES
('empresa_nome', 'BikeOS', 'Nome da empresa', 'string'),
('empresa_cnpj', '', 'CNPJ da empresa', 'string'),
('empresa_endereco', '', 'Endereço da empresa', 'string'),
('empresa_telefone', '', 'Telefone da empresa', 'string'),
('empresa_email', 'contato@bikeos.com.br', 'Email da empresa', 'string'),
('sistema_versao', '1.0.0', 'Versão do sistema', 'string'),
('backup_automatico', 'true', 'Backup automático habilitado', 'boolean'),
('manutencao_modo', 'false', 'Modo manutenção', 'boolean');

-- Categorias padrão de produtos
INSERT INTO `produtos` (`nome`, `descricao`, `categoria`, `preco_venda`, `estoque_atual`) VALUES
('Câmara de Ar 26"', 'Câmara de ar para bicicleta aro 26', 'Acessórios', 15.00, 50),
('Corrente KMC', 'Corrente para bicicleta', 'Transmissão', 45.00, 20),
('Pneu 26" x 1.95', 'Pneu para mountain bike', 'Pneus', 35.00, 30);

-- Serviços padrão
INSERT INTO `servicos` (`nome`, `descricao`, `categoria`, `preco`, `tempo_estimado`) VALUES
('Revisão Geral', 'Revisão completa da bicicleta', 'Manutenção', 80.00, 120),
('Troca de Câmara', 'Substituição de câmara de ar', 'Reparo', 25.00, 30),
('Regulagem de Freios', 'Ajuste e regulagem do sistema de freios', 'Ajuste', 35.00, 45),
('Troca de Corrente', 'Substituição da corrente', 'Transmissão', 50.00, 60);

COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

-- =====================================================
-- FIM DO SCRIPT DE DEPLOY
-- =====================================================