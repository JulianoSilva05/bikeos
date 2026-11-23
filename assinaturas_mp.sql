-- Tabela de assinaturas do Mercado Pago (rodar uma vez no banco do servidor e no local)
CREATE TABLE IF NOT EXISTS `assinaturas_mp` (
  `id` int NOT NULL AUTO_INCREMENT,
  `usuario_id` int NOT NULL,
  `oferta` varchar(50) NOT NULL,
  `external_reference` varchar(64) NOT NULL,
  `preapproval_id` varchar(64) DEFAULT NULL,
  `payer_email` varchar(255) NOT NULL,
  `status` enum('creating','pending','authorized','paused','canceled','error') NOT NULL DEFAULT 'creating',
  `init_point` varchar(500) DEFAULT NULL,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `idx_external_reference` (`external_reference`),
  UNIQUE KEY `idx_preapproval_id` (`preapproval_id`),
  KEY `idx_usuario` (`usuario_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
