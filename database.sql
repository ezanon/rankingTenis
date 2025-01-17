-- v. 3.0.0
CREATE TABLE categorias (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(20) NOT NULL
);

ALTER TABLE jogador
ADD COLUMN posicao_misto INT,
ADD COLUMN posicao_feminino INT,
ADD COLUMN posicao_anterior_misto INT,
ADD COLUMN posicao_anterior_feminino INT,
ADD COLUMN categoria_misto INT DEFAULT 0,
ADD COLUMN categoria_feminino INT DEFAULT 0,
ADD COLUMN rcsa_misto INT DEFAULT 0,
ADD COLUMN rcsa_feminino INT DEFAULT 0,
ADD COLUMN jat_misto INT DEFAULT 0,
ADD COLUMN jat_feminino INT DEFAULT 0,
ADD COLUMN prioridade_misto INT DEFAULT 999,
ADD COLUMN prioridade_feminino INT DEFAULT 999,
ADD COLUMN tft_misto INT DEFAULT 0,
ADD COLUMN tft_feminino INT DEFAULT 0,
ADD COLUMN wo_misto INT DEFAULT 0,
ADD COLUMN wo_feminino INT DEFAULT 0,
ADD COLUMN jogos_misto INT DEFAULT 0,
ADD COLUMN jogos_feminino INT DEFAULT 0,
ADD COLUMN vitorias_misto INT DEFAULT 0,
ADD COLUMN vitorias_feminino INT DEFAULT 0,
ADD CONSTRAINT fk_categoria_misto FOREIGN KEY (categoria_misto) REFERENCES categorias(id) ON DELETE SET NULL,
ADD CONSTRAINT fk_categoria_feminino FOREIGN KEY (categoria_feminino) REFERENCES categorias(id) ON DELETE SET NULL;



