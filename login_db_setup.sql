CREATE DATABASE IF NOT EXISTS login_db;

USE login_db;

CREATE TABLE usuarios (
    pk INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(120) NOT NULL UNIQUE,
    senha VARCHAR(255) NOT NULL
);

INSERT INTO usuarios (username, senha) VALUES ('admin', '123');
