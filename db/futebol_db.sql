-- =============================
-- Database: futebol_db
-- =============================
CREATE DATABASE IF NOT EXISTS futebol_db;
USE futebol_db;

-- Tabela de times
CREATE TABLE times (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    cidade VARCHAR(100) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Tabela de jogadores
CREATE TABLE jogadores (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    posicao ENUM('GOL', 'ZAG', 'LD', 'LE', 'VOL', 'MEI', 'ATA') NOT NULL,
    numero_camisa INT NOT NULL,
    time_id INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (time_id) REFERENCES times(id) ON DELETE CASCADE,
    UNIQUE KEY unique_jogador_time (time_id, numero_camisa)
);

-- Tabela de partidas
CREATE TABLE partidas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    time_casa_id INT NOT NULL,
    time_fora_id INT NOT NULL,
    data_jogo DATETIME NOT NULL,
    gols_casa INT DEFAULT 0,
    gols_fora INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (time_casa_id) REFERENCES times(id) ON DELETE CASCADE,
    FOREIGN KEY (time_fora_id) REFERENCES times(id) ON DELETE CASCADE,
    CHECK (time_casa_id != time_fora_id),
    CHECK (gols_casa >= 0),
    CHECK (gols_fora >= 0)
);

-- =============================
-- Inserir os 20 clubes da Série A 2025
-- =============================
INSERT INTO times (nome, cidade) VALUES
('Atlético Mineiro', 'Belo Horizonte'),
('Bahia', 'Salvador'),
('Botafogo', 'Rio de Janeiro'),
('Red Bull Bragantino', 'Bragança Paulista'),
('Ceará', 'Fortaleza'),
('Corinthians', 'São Paulo'),
('Cruzeiro', 'Belo Horizonte'),
('Flamengo', 'Rio de Janeiro'),
('Fluminense', 'Rio de Janeiro'),
('Fortaleza', 'Fortaleza'),
('Grêmio', 'Porto Alegre'),
('Internacional', 'Porto Alegre'),
('Juventude', 'Caxias do Sul'),
('Mirassol', 'Mirassol'),
('Palmeiras', 'São Paulo'),
('Santos', 'Santos'),
('São Paulo', 'São Paulo'),
('Sport Recife', 'Recife'),
('Vasco da Gama', 'Rio de Janeiro'),
('Vitória', 'Salvador');

-- =============================
-- Jogadores titulares (genéricos, exemplo fictício)
-- =============================
INSERT INTO jogadores (nome, posicao, numero_camisa, time_id) VALUES
-- Atlético Mineiro
('Goleiro Atlético', 'GOL', 1, (SELECT id FROM times WHERE nome = 'Atlético Mineiro')),
('Zagueiro Atlético', 'ZAG', 4, (SELECT id FROM times WHERE nome = 'Atlético Mineiro')),
('Meia Atlético', 'MEI', 8, (SELECT id FROM times WHERE nome = 'Atlético Mineiro')),
('Atacante Atlético', 'ATA', 9, (SELECT id FROM times WHERE nome = 'Atlético Mineiro')),

-- Bahia
('Goleiro Bahia', 'GOL', 1, (SELECT id FROM times WHERE nome = 'Bahia')),
('Zagueiro Bahia', 'ZAG', 3, (SELECT id FROM times WHERE nome = 'Bahia')),
('Meia Bahia', 'MEI', 10, (SELECT id FROM times WHERE nome = 'Bahia')),
('Atacante Bahia', 'ATA', 9, (SELECT id FROM times WHERE nome = 'Bahia')),

-- Botafogo
('Goleiro Botafogo', 'GOL', 1, (SELECT id FROM times WHERE nome = 'Botafogo')),
('Zagueiro Botafogo', 'ZAG', 4, (SELECT id FROM times WHERE nome = 'Botafogo')),
('Meia Botafogo', 'MEI', 8, (SELECT id FROM times WHERE nome = 'Botafogo')),
('Atacante Botafogo', 'ATA', 9, (SELECT id FROM times WHERE nome = 'Botafogo')),

-- Red Bull Bragantino
('Goleiro Bragantino', 'GOL', 1, (SELECT id FROM times WHERE nome = 'Red Bull Bragantino')),
('Zagueiro Bragantino', 'ZAG', 5, (SELECT id FROM times WHERE nome = 'Red Bull Bragantino')),
('Meia Bragantino', 'MEI', 7, (SELECT id FROM times WHERE nome = 'Red Bull Bragantino')),
('Atacante Bragantino', 'ATA', 9, (SELECT id FROM times WHERE nome = 'Red Bull Bragantino')),

-- Ceará
('Goleiro Ceará', 'GOL', 1, (SELECT id FROM times WHERE nome = 'Ceará')),
('Zagueiro Ceará', 'ZAG', 3, (SELECT id FROM times WHERE nome = 'Ceará')),
('Meia Ceará', 'MEI', 8, (SELECT id FROM times WHERE nome = 'Ceará')),
('Atacante Ceará', 'ATA', 11, (SELECT id FROM times WHERE nome = 'Ceará')),

-- Corinthians
('Goleiro Corinthians', 'GOL', 1, (SELECT id FROM times WHERE nome = 'Corinthians')),
('Zagueiro Corinthians', 'ZAG', 4, (SELECT id FROM times WHERE nome = 'Corinthians')),
('Meia Corinthians', 'MEI', 8, (SELECT id FROM times WHERE nome = 'Corinthians')),
('Atacante Corinthians', 'ATA', 9, (SELECT id FROM times WHERE nome = 'Corinthians')),

-- Cruzeiro
('Goleiro Cruzeiro', 'GOL', 1, (SELECT id FROM times WHERE nome = 'Cruzeiro')),
('Zagueiro Cruzeiro', 'ZAG', 3, (SELECT id FROM times WHERE nome = 'Cruzeiro')),
('Meia Cruzeiro', 'MEI', 10, (SELECT id FROM times WHERE nome = 'Cruzeiro')),
('Atacante Cruzeiro', 'ATA', 9, (SELECT id FROM times WHERE nome = 'Cruzeiro')),

-- Flamengo
('Goleiro Flamengo', 'GOL', 1, (SELECT id FROM times WHERE nome = 'Flamengo')),
('Zagueiro Flamengo', 'ZAG', 4, (SELECT id FROM times WHERE nome = 'Flamengo')),
('Meia Flamengo', 'MEI', 10, (SELECT id FROM times WHERE nome = 'Flamengo')),
('Atacante Flamengo', 'ATA', 9, (SELECT id FROM times WHERE nome = 'Flamengo')),

-- Fluminense
('Goleiro Fluminense', 'GOL', 1, (SELECT id FROM times WHERE nome = 'Fluminense')),
('Zagueiro Fluminense', 'ZAG', 3, (SELECT id FROM times WHERE nome = 'Fluminense')),
('Meia Fluminense', 'MEI', 8, (SELECT id FROM times WHERE nome = 'Fluminense')),
('Atacante Fluminense', 'ATA', 11, (SELECT id FROM times WHERE nome = 'Fluminense')),

-- Fortaleza
('Goleiro Fortaleza', 'GOL', 1, (SELECT id FROM times WHERE nome = 'Fortaleza')),
('Zagueiro Fortaleza', 'ZAG', 4, (SELECT id FROM times WHERE nome = 'Fortaleza')),
('Meia Fortaleza', 'MEI', 7, (SELECT id FROM times WHERE nome = 'Fortaleza')),
('Atacante Fortaleza', 'ATA', 9, (SELECT id FROM times WHERE nome = 'Fortaleza')),

-- Grêmio
('Goleiro Grêmio', 'GOL', 1, (SELECT id FROM times WHERE nome = 'Grêmio')),
('Zagueiro Grêmio', 'ZAG', 3, (SELECT id FROM times WHERE nome = 'Grêmio')),
('Meia Grêmio', 'MEI', 10, (SELECT id FROM times WHERE nome = 'Grêmio')),
('Atacante Grêmio', 'ATA', 9, (SELECT id FROM times WHERE nome = 'Grêmio')),

-- Internacional
('Goleiro Internacional', 'GOL', 1, (SELECT id FROM times WHERE nome = 'Internacional')),
('Zagueiro Internacional', 'ZAG', 4, (SELECT id FROM times WHERE nome = 'Internacional')),
('Meia Internacional', 'MEI', 8, (SELECT id FROM times WHERE nome = 'Internacional')),
('Atacante Internacional', 'ATA', 9, (SELECT id FROM times WHERE nome = 'Internacional')),

-- Juventude
('Goleiro Juventude', 'GOL', 1, (SELECT id FROM times WHERE nome = 'Juventude')),
('Zagueiro Juventude', 'ZAG', 3, (SELECT id FROM times WHERE nome = 'Juventude')),
('Meia Juventude', 'MEI', 7, (SELECT id FROM times WHERE nome = 'Juventude')),
('Atacante Juventude', 'ATA', 9, (SELECT id FROM times WHERE nome = 'Juventude')),

-- Mirassol
('Goleiro Mirassol', 'GOL', 1, (SELECT id FROM times WHERE nome = 'Mirassol')),
('Zagueiro Mirassol', 'ZAG', 4, (SELECT id FROM times WHERE nome = 'Mirassol')),
('Meia Mirassol', 'MEI', 8, (SELECT id FROM times WHERE nome = 'Mirassol')),
('Atacante Mirassol', 'ATA', 9, (SELECT id FROM times WHERE nome = 'Mirassol')),

-- Palmeiras
('Goleiro Palmeiras', 'GOL', 1, (SELECT id FROM times WHERE nome = 'Palmeiras')),
('Zagueiro Palmeiras', 'ZAG', 4, (SELECT id FROM times WHERE nome = 'Palmeiras')),
('Meia Palmeiras', 'MEI', 8, (SELECT id FROM times WHERE nome = 'Palmeiras')),
('Atacante Palmeiras', 'ATA', 9, (SELECT id FROM times WHERE nome = 'Palmeiras')),

-- Santos
('Goleiro Santos', 'GOL', 1, (SELECT id FROM times WHERE nome = 'Santos')),
('Zagueiro Santos', 'ZAG', 3, (SELECT id FROM times WHERE nome = 'Santos')),
('Meia Santos', 'MEI', 10, (SELECT id FROM times WHERE nome = 'Santos')),
('Atacante Santos', 'ATA', 9, (SELECT id FROM times WHERE nome = 'Santos')),

-- São Paulo
('Goleiro São Paulo', 'GOL', 1, (SELECT id FROM times WHERE nome = 'São Paulo')),
('Zagueiro São Paulo', 'ZAG', 4, (SELECT id FROM times WHERE nome = 'São Paulo')),
('Meia São Paulo', 'MEI', 8, (SELECT id FROM times WHERE nome = 'São Paulo')),
('Atacante São Paulo', 'ATA', 9, (SELECT id FROM times WHERE nome = 'São Paulo')),

-- Sport Recife
('Goleiro Sport', 'GOL', 1, (SELECT id FROM times WHERE nome = 'Sport Recife')),
('Zagueiro Sport', 'ZAG', 3, (SELECT id FROM times WHERE nome = 'Sport Recife')),
('Meia Sport', 'MEI', 8, (SELECT id FROM times WHERE nome = 'Sport Recife')),
('Atacante Sport', 'ATA', 9, (SELECT id FROM times WHERE nome = 'Sport Recife')),

-- Vasco da Gama
('Goleiro Vasco', 'GOL', 1, (SELECT id FROM times WHERE nome = 'Vasco da Gama')),
('Zagueiro Vasco', 'ZAG', 4, (SELECT id FROM times WHERE nome = 'Vasco da Gama')),
('Meia Vasco', 'MEI', 10, (SELECT id FROM times WHERE nome = 'Vasco da Gama')),
('Atacante Vasco', 'ATA', 9, (SELECT id FROM times WHERE nome = 'Vasco da Gama')),

-- Vitória
('Goleiro Vitória', 'GOL', 1, (SELECT id FROM times WHERE nome = 'Vitória')),
('Zagueiro Vitória', 'ZAG', 3, (SELECT id FROM times WHERE nome = 'Vitória')),
('Meia Vitória', 'MEI', 8, (SELECT id FROM times WHERE nome = 'Vitória')),
('Atacante Vitória', 'ATA', 9, (SELECT id FROM times WHERE nome = 'Vitória'));
