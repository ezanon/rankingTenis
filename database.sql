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

ALTER TABLE jogador
ADD COLUMN pontuacao_misto INT,
ADD COLUMN pontuacao_feminino INT;

ALTER TABLE jogador
MODIFY COLUMN pontuacao_misto INT DEFAULT 0,
MODIFY COLUMN pontuacao_feminino INT DEFAULT 0;

SET @pontos = 0;
UPDATE jogador j
JOIN (
    SELECT id, (@pontos := @pontos + 5) AS nova_pontuacao
    FROM jogador
    WHERE ranking = 'misto'
    ORDER BY posicao DESC
) AS temp ON j.id = temp.id
SET j.pontuacao_misto = temp.nova_pontuacao;

SET @pontos = 0;
UPDATE jogador j
JOIN (
    SELECT id, (@pontos := @pontos + 5) AS nova_pontuacao
    FROM jogador
    WHERE ranking = 'feminino'
    ORDER BY posicao DESC
) AS temp ON j.id = temp.id
SET j.pontuacao_feminino = temp.nova_pontuacao;

-- Atualizar posicao_misto para jogadores do ranking misto
UPDATE jogador 
SET posicao_misto = posicao 
WHERE ranking = 'misto';

-- Atualizar posicao_feminino para jogadores do ranking feminino
UPDATE jogador 
SET posicao_feminino = posicao 
WHERE ranking = 'feminino';

-- Atualizar categoria_misto para jogadores do ranking misto
UPDATE jogador 
SET categoria_misto = 1 
WHERE ranking = 'misto';

-- Atualizar categoria_feminino para jogadores do ranking feminino
UPDATE jogador 
SET categoria_feminino = 1 
WHERE ranking = 'feminino';

-- Criar coluna sobre disponibilidade
ALTER TABLE jogador
ADD COLUMN disponibilidade VARCHAR(255) DEFAULT '0';

ALTER TABLE jogador
ADD COLUMN fav_misto INT DEFAULT 0,
ADD COLUMN fav_feminino INT DEFAULT 0;

ALTER TABLE jogador 
CHANGE COLUMN categoria_misto ranking_misto VARCHAR(20),
CHANGE COLUMN categoria_feminino ranking_feminino VARCHAR(20),
ADD COLUMN categoria_misto VARCHAR(20),
ADD COLUMN categoria_feminino VARCHAR(20),
ADD COLUMN barragem_misto INT DEFAULT 0,
ADD COLUMN barragem_feminino INT DEFAULT 0;


