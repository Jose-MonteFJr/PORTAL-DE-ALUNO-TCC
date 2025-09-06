-- Criação do banco de dados e das tabelas

CREATE DATABASE IF NOT EXISTS portal_do_aluno_tcc CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE portal_do_aluno_tcc;

CREATE TABLE IF NOT EXISTS pessoa (
  id_pessoa INT AUTO_INCREMENT PRIMARY KEY,
  nome VARCHAR(150) NOT NULL,
  cpf VARCHAR(11) NOT NULL UNIQUE,
  data_nascimento DATE NOT NULL,
  password_hash VARCHAR(255) NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
-- Colocar na parte da senha
-- password_hash VARCHAR(255) NOT NULL,