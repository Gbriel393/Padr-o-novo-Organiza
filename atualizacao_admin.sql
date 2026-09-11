-- ================================================================
-- ATUALIZAÇÃO: adiciona tabelas de Logs do Sistema e de
-- Redefinição de Senha ao banco organiza já existente.
-- Importe isso no phpMyAdmin (mesmo jeito que importou o banco
-- original). Não apaga nada que já existe.
-- ================================================================

CREATE TABLE IF NOT EXISTS `logs_sistema` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nivel` enum('INFO','DEBUG','WARN','ERROR') NOT NULL DEFAULT 'INFO',
  `mensagem` varchar(255) NOT NULL,
  `criado_em` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE IF NOT EXISTS `password_resets` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `usuario_id` int(11) NOT NULL,
  `token` varchar(64) NOT NULL,
  `expira_em` datetime NOT NULL,
  `usado` tinyint(1) NOT NULL DEFAULT 0,
  `criado_em` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `usuario_id` (`usuario_id`),
  KEY `token` (`token`),
  CONSTRAINT `password_resets_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `logs_sistema` (`nivel`, `mensagem`) VALUES
('INFO', 'Sistema iniciado e conectado ao banco organiza'),
('INFO', 'Tabela de logs criada com sucesso');
