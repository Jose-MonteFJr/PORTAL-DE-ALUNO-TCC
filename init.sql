-- Criação do banco de dados e das tabelas

CREATE DATABASE IF NOT EXISTS portal_do_aluno_tcc CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE portal_do_aluno_tcc;

CREATE TABLE IF NOT EXISTS aluno (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nome VARCHAR(120) NOT NULL,
  email VARCHAR(160) NOT NULL UNIQUE,
  password_hash VARCHAR(255) NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
