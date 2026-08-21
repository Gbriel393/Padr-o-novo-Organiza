CREATE TABLE IF NOT EXISTS transacoes (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT NOT NULL,
    tipo ENUM('entrada', 'saida') NOT NULL,
    natureza ENUM('fixo', 'variavel') NULL,
    categoria VARCHAR(80) NOT NULL,
    valor DECIMAL(12,2) UNSIGNED NOT NULL,
    data_transacao DATE NOT NULL,
    status ENUM('recebido', 'pago', 'pendente') NOT NULL DEFAULT 'pendente',
    descricao VARCHAR(500) NULL,
    criado_em TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_transacoes_usuario_data (usuario_id, data_transacao),
    INDEX idx_transacoes_filtros (usuario_id, tipo, natureza, status),
    CONSTRAINT fk_transacoes_usuario FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
