<?php

class admin3 {

    public $banco;

    function __construct() {
        $this->banco = banco::instanciar();
        return NULL;
    }

    private function infosEDesign() {
        $this->quadras = [
            "USP SLAM" => 1,
            "USP MASTERS" => 2,
            "USP 500" => 3,
            "WTA" => 4,
            "USP 250" => 5
        ];

        $this->categoriasCores = [
            "USP SLAM" => "bg-danger text-white",
            "USP MASTERS" => "bg-warning text-dark",
            "USP 500" => "bg-success text-white",
            "USP 250" => "bg-primary text-white",
            "WTA" => "bg-info text-white"
        ];

        $this->barragemCores = [
            1 => "bg-dark text-white",
            2 => "bg-secondary text-white"
        ];

        $this->horarios = [
            1 => "Sábado às 13h00",
            2 => "Sábado às 14h30",
            3 => "Sábado às 16h00",
            4 => "Domingo às 8h00",
            5 => "Domingo às 10h00"
        ];

        return true;
    }

    public function proporJogos() {

        $this->infosEDesign();
        $quadras = $this->quadras;
        $categoriasCores = $this->categoriasCores;
        $barragemCores = $this->barragemCores;
        $horarios = $this->horarios;
        $bola = "&#129358;";

        $listaDeJogos = $this->gerarJogosAgendaveis();
        //die(print_r($listaDeJogos));
        return $this->gerarTabelaJogos($listaDeJogos);
    }

    public function showRanking() {
        $ranking = $_GET['id'];
        return $this->gerarTabelaRanking($ranking);
    }

    private function gerarTabelaRanking($sufixo) {
        // Monta a consulta SQL
        $sql = "SELECT id, posicao_$sufixo, nome_completo, telefone_celular, pontuacao_$sufixo, 
                   pontuacao_inicial_$sufixo, vitorias_$sufixo, jogos_$sufixo, rcsa_$sufixo, jat_$sufixo, 
                   tft_$sufixo, fav_$sufixo, wo_$sufixo, categoria_$sufixo, barragem_$sufixo
            FROM jogador 
            WHERE ranking_$sufixo = 1
            ORDER BY posicao_$sufixo ASC";

        $jogadores = $this->banco->consultar($sql);

        if (empty($jogadores)) {
            return "<p class='text-center'>Nenhum jogador encontrado.</p>";
        }

        $mostrarTelefone = isset($_SESSION['acesso_autorizado']) && $_SESSION['acesso_autorizado'] == 1;

        // Inicia a tabela
        $tabela = "<table class='table table-striped table-bordered text-center'>";
        $tabela .= "<thead class='thead-dark'><tr>
                    <th>Posição</th>
                    <th>Nome</th>";

        if ($mostrarTelefone) {
            $tabela .= "<th>Telefone</th>";
        }

        $tabela .= "<th>Pontos</th>
                <th>Vitórias/Jogos</th>
                <th>RCSA</th>
                <th>JAT</th>
                <th>TFT</th>
                <th>FAV</th>
                <th>WO</th>
            </tr></thead><tbody>";

        $categoriaAtual = null;
        $contador = 0;

        foreach ($jogadores as $jogador) {
            $contador++;

            // Se a categoria mudar, adiciona um separador
            if ($categoriaAtual !== $jogador["categoria_$sufixo"]) {
                $categoriaAtual = $jogador["categoria_$sufixo"];
                $tabela .= "<tr class='table-primary text-white'>
                            <td colspan='10'><strong>$categoriaAtual</strong></td>
                        </tr>";
            }

            // Badge de posição
            $posicaoBadge = "<span class='badge bg-success text-white'>{$jogador["posicao_$sufixo"]}</span>";

            // Badge de categoria
            if ($sufixo == "misto") {
                $categoriaBadge = "<span class='badge bg-danger text-white'>{$jogador["categoria_$sufixo"]}</span>";
            } else {
                $categoriaBadge = "<span class='badge bg-primary text-white'>WTA</span>";
            }

            // Badge de barragem
            $barragemBadge = "<span class='badge bg-dark text-white'>{$jogador["barragem_$sufixo"]}</span>";

            // Adiciona telefone com ícone do WhatsApp
            $telefoneColuna = "";
            if ($mostrarTelefone) {
                if (!empty($jogador['telefone_celular'])) {
                    // Remove caracteres não numéricos
                    $numeroLimpo = preg_replace('/\D/', '', $jogador['telefone_celular']);

                    // Adiciona DDI +55 se necessário
                    if (strlen($numeroLimpo) == 10 || strlen($numeroLimpo) == 11) {
                        $numeroLimpo = "+55" . $numeroLimpo;
                    } elseif (strlen($numeroLimpo) > 2 && substr($numeroLimpo, 0, 2) != "+55") {
                        $numeroLimpo = "+" . $numeroLimpo; // Mantém DDI existente
                    }

                    $telefoneColuna = "<td>
                    {$jogador['telefone_celular']}
                    <a href='https://wa.me/{$numeroLimpo}' target='_blank' class='ms-2'>
                        <i class='fab fa-whatsapp text-success'></i>
                    </a>
                </td>";
                } else {
                    $telefoneColuna = "<td><span class='text-muted'>Sem número</span></td>";
                }
            }

            // Pontuação (atual e inicial entre parênteses)
            $pontuacaoAtual = $jogador["pontuacao_$sufixo"];
            $pontuacaoInicial = $jogador["pontuacao_inicial_$sufixo"];
            $pontuacaoFormatada = "$pontuacaoAtual <span class='text-muted'>($pontuacaoInicial)</span>";

            // Badges para FAV
            $fav = $jogador["fav_$sufixo"];
            if ($fav == 0) {
                $favBadge = "<span class='badge bg-success'>$fav</span>"; // Verde 🟢
            } elseif ($fav == 1) {
                $favBadge = "<span class='badge bg-warning text-dark'>$fav</span>"; // Amarelo 🟡
            } else {
                $favBadge = "<span class='badge bg-danger'>$fav</span>"; // Vermelho 🔴
            }

            // Badge para WO
            $wo = $jogador["wo_$sufixo"];
            $woBadge = $wo > 0 ? "<span class='badge bg-danger'>$wo</span>" : "<span class='badge bg-success'>$wo</span>";

            // Monta a linha da tabela
            $tabela .= "<tr>
                        <td>$posicaoBadge</td>
                        <td>$barragemBadge $categoriaBadge {$jogador['nome_completo']}</td>
                        $telefoneColuna
                        <td>$pontuacaoFormatada</td>
                        <td>{$jogador["vitorias_$sufixo"]}/{$jogador["jogos_$sufixo"]}</td>
                        <td>{$jogador["rcsa_$sufixo"]}</td>
                        <td>{$jogador["jat_$sufixo"]}</td>
                        <td>{$jogador["tft_$sufixo"]}</td>
                        <td>$favBadge</td>
                        <td>$woBadge</td>
                    </tr>";
        }

        $tabela .= "</tbody></table>";

        return $tabela;
    }

    public function atualizarCategoriasEBarragens() {
        $rankings = ["misto", "feminino"];

        foreach ($rankings as $sufixo) {
            // Busca os jogadores ordenados pela posição
            $sql = "SELECT id, posicao_$sufixo FROM jogador WHERE ranking_$sufixo = 1 ORDER BY posicao_$sufixo";
            $jogadores = $this->banco->consultar($sql);

            if (empty($jogadores))
                continue;

            $updates = [];
            $categorias = ["USP SLAM", "USP MASTERS", "USP 500", "USP 250"];
            $limites = [40, 80, 120, PHP_INT_MAX]; // Máximo de jogadores por categoria

            $categoriaIndex = 0;
            $contadorCategoria = 0;

            foreach ($jogadores as $jogador) {
                $id = $jogador['id'];
                $posicao = $jogador["posicao_$sufixo"];

                // Se a categoria atingiu o limite, passa para a próxima
                if ($posicao > $limites[$categoriaIndex]) {
                    $categoriaIndex++;
                    $contadorCategoria = 0;
                }

                $categoria = $categorias[$categoriaIndex];
                $contadorCategoria++;

                // Define a barragem: 1 para os primeiros 20 da categoria, 2 para os seguintes
                $barragem = ($contadorCategoria <= 20) ? 1 : 2;

                // Adiciona a query para esse jogador
                $updates[] = "UPDATE jogador 
                          SET categoria_$sufixo = '$categoria', barragem_$sufixo = $barragem 
                          WHERE id = $id";
            }

            // Executa todas as queries
            foreach ($updates as $query) {
                $this->banco->executar($query);
            }
        }

        return "Categorias e Barragens atualizadas com sucesso!";
    }

    public function verDisponibilidades() {
        $tabelas = "";
        $rankings = ["misto", "feminino"];

        foreach ($rankings as $sufixo) {
            $sql = "SELECT id, nome_completo, pontuacao_$sufixo, disponibilidade, 
                       rcsa_$sufixo, jat_$sufixo, categoria_$sufixo, barragem_$sufixo, posicao_$sufixo,
                       ranking_feminino, ultimojogo_feminino
                FROM jogador 
                WHERE ranking_$sufixo = 1
                ORDER BY 
                    FIELD(categoria_$sufixo, 'USP SLAM', 'USP MASTERS', 'USP 500', 'USP 250'),
                    priorizar DESC,
                    rcsa_$sufixo ASC, 
                    jat_$sufixo ASC, 
                    posicao_$sufixo ASC";

            $jogadores = $this->banco->consultar($sql);

            if (empty($jogadores)) {
                $tabelas .= "<p class='text-center'>Nenhum jogador encontrado no ranking $sufixo.</p>";
                continue;
            }

            $titulo = ($sufixo == "misto") ? "Ranking Misto" : "Ranking Feminino";
            $tabelas .= "<h3 class='text-center mt-4'>$titulo</h3>";

            $tabelas .= "<table class='table table-striped table-bordered text-center'>";
            $tabelas .= "<thead class='thead-dark'><tr>
                        <th>#</th>
                        <th>Nome</th>
                        <th>Pontos</th>
                        <th>Disponibilidade</th>
                        <th>RCSA</th>
                        <th>JAT</th>
                    </tr></thead><tbody>";

            $categoriaAtual = null;
            $contador = 0;

            foreach ($jogadores as $jogador) {
                $contador++;

                // Se a categoria mudar, adiciona um separador
                if ($categoriaAtual !== $jogador["categoria_$sufixo"]) {
                    $categoriaAtual = $jogador["categoria_$sufixo"];
                    $tabelas .= "<tr class='table-primary text-white'>
                                <td colspan='6'><strong>$categoriaAtual</strong></td>
                             </tr>";
                }

                // Badge de posição
                $posicaoBadge = "<span class='badge bg-success text-white'>{$jogador["posicao_$sufixo"]}</span>";

                // Badge de categoria (USP SLAM, MASTERS, 500, 250)
                if ($sufixo == "misto") {
                    $categoriaBadge = "<span class='badge bg-danger text-white'>{$jogador["categoria_$sufixo"]}</span>";
                } else {
                    $categoriaBadge = "<span class='badge bg-primary text-white'>WTA</span>";
                }

                // Badge de barragem
                $barragemBadge = "<span class='badge bg-dark text-white'>{$jogador["barragem_$sufixo"]}</span>";

                // Badge de último jogo (para mulheres)
                $ultimoJogoBadge = "";
                if ($sufixo == "feminino" || $jogador["ranking_feminino"] == 1) {
                    $ultimoJogoBadge = $jogador["ultimojogo_feminino"] == 1 ? "<span class='badge bg-info text-white'>last: femin</span>" : "<span class='badge bg-secondary text-white'>last: misto</span>";
                }

                // Disponibilidade (1 a 5 em verde ou vermelho)
                $disponiveis = explode(",", $jogador["disponibilidade"]);
                $disponibilidadeBadges = "";
                for ($i = 1; $i <= 5; $i++) {
                    $cor = in_array($i, $disponiveis) ? "bg-success text-white" : "bg-danger text-white";
                    $disponibilidadeBadges .= "<span class='badge $cor me-1'>$i</span>";
                }

                // Monta a linha da tabela
                $tabelas .= "<tr>
                            <td>$contador</td>
                            <td>$posicaoBadge $barragemBadge $categoriaBadge {$jogador['nome_completo']} $ultimoJogoBadge</td>
                            <td>{$jogador["pontuacao_$sufixo"]}</td>
                            <td>$disponibilidadeBadges</td>
                            <td>{$jogador["rcsa_$sufixo"]}</td>
                            <td>{$jogador["jat_$sufixo"]}</td>
                        </tr>";
            }

            $tabelas .= "</tbody></table>";
        }

        return $tabelas;
    }

    private function criarJogos() {
        $sql = "SELECT id, nome_completo, posicao_misto, posicao_feminino, ranking_misto, ranking_feminino,
                   categoria_misto, categoria_feminino, barragem_misto, barragem_feminino, disponibilidade, 
                   rcsa_misto, jat_misto, rcsa_feminino, jat_feminino, priorizar, ultimojogo_feminino, 
                   ultimosjogos, fav_misto, fav_feminino
                FROM jogador
                WHERE ranking_misto = 1 OR ranking_feminino = 1
                ORDER BY priorizar DESC, rcsa_misto ASC, jat_misto ASC, rcsa_feminino ASC, jat_feminino ASC";

        $jogadores = $this->banco->consultar($sql);

        if (empty($jogadores)) {
            return "<p class='text-center'>Nenhum jogador disponível para agendamento.</p>";
        }

        $disponiveis = [];
        foreach ($jogadores as $jogador) {
            $categoria = ($jogador["ranking_feminino"] == 1) ? "WTA" : ($jogador["categoria_misto"] ?: $jogador["categoria_feminino"]);
            $barragem = $jogador["barragem_misto"] ?: $jogador["barragem_feminino"];
            $disponibilidade = explode(",", $jogador["disponibilidade"]);

            $disponiveis[$categoria][$barragem][] = [
                "id" => $jogador["id"],
                "nome" => $jogador["nome_completo"],
                "posicao" => $jogador["posicao_misto"] ?: $jogador["posicao_feminino"],
                "rcsa" => $jogador["rcsa_misto"] ?: $jogador["rcsa_feminino"],
                "jat" => $jogador["jat_misto"] ?: $jogador["jat_feminino"],
                "priorizar" => $jogador["priorizar"],
                "ultimojogo_feminino" => $jogador["ultimojogo_feminino"],
                "ranking_misto" => $jogador["ranking_misto"],
                "ranking_feminino" => $jogador["ranking_feminino"],
                "fav" => $jogador["fav_misto"] ?: $jogador["fav_feminino"],
                "ultimosjogos" => explode(",", $jogador["ultimosjogos"]),
                "disponibilidade" => $disponibilidade
            ];
        }
        return $disponiveis;
    }

    private function gerarJogosAgendaveis() {
        $this->infosEDesign();
        $categorias = ["USP SLAM", "USP MASTERS", "USP 500", "WTA", "USP 250"];
        $horarios = array_keys($this->horarios);
        $jogos = [];

        // Buscar ids de jogadores disponíveis
        $sqlJogadores = "SELECT id FROM jogador ORDER BY id";
        $jogadores = $this->banco->consultar($sqlJogadores);

        // Criar um array para marcar jogadores já agendados
        $agendamentos = [];
        foreach ($jogadores as $j) {
            $agendamentos[$j['id']] = false;
        }
        unset($jogadores);

        foreach ($categorias as $categoria) {
            $sufixo = ($categoria == 'WTA') ? "feminino" : "misto";
            $campoRanking = "ranking_" . $sufixo;
            $campoCategoria = "categoria_" . $sufixo;
            $campoBarragem = "barragem_" . $sufixo;
            $campoPosicao = "posicao_" . $sufixo;
            $campoRCsa = "rcsa_" . $sufixo;
            $campoJat = "jat_" . $sufixo;
            $campoFav = "fav_" . $sufixo;

            // Obter disponibilidade dos horários
            $sqlDisponibilidade = "SELECT
            SUM(CASE WHEN disponibilidade LIKE '%1%' THEN 1 ELSE 0 END) AS total_1,
            SUM(CASE WHEN disponibilidade LIKE '%2%' THEN 1 ELSE 0 END) AS total_2,
            SUM(CASE WHEN disponibilidade LIKE '%3%' THEN 1 ELSE 0 END) AS total_3,
            SUM(CASE WHEN disponibilidade LIKE '%4%' THEN 1 ELSE 0 END) AS total_4,
            SUM(CASE WHEN disponibilidade LIKE '%5%' THEN 1 ELSE 0 END) AS total_5
        FROM jogador
        WHERE $campoCategoria = :categoria";

            $disponibilidades = $this->banco->consultar($sqlDisponibilidade, ["categoria" => $categoria])[0];

            // Criar array com horários ordenados pelos menos procurados primeiro
            $horariosOrdenados = [
                1 => $disponibilidades["total_1"],
                2 => $disponibilidades["total_2"],
                3 => $disponibilidades["total_3"],
                4 => $disponibilidades["total_4"],
                5 => $disponibilidades["total_5"]
            ];
            asort($horariosOrdenados);
            $ordemHorarios = array_keys($horariosOrdenados);

            // Buscar jogadores disponíveis
            $sqlJogadores = "SELECT id, nome_completo, $campoPosicao AS posicao, 
                        $campoRanking AS ranking, $campoCategoria AS categoria, 
                        $campoBarragem AS barragem, disponibilidade, 
                        $campoRCsa AS rcsa, $campoJat AS jat, priorizar, 
                        ultimojogo_feminino, ultimosjogos, $campoFav AS fav
                        FROM jogador
                        WHERE $campoRanking = 1 AND $campoCategoria = :categoria 
                        AND disponibilidade != '0'
                        ORDER BY priorizar DESC, rcsa DESC, jat ASC, posicao ASC";

            $jogadores = $this->banco->consultar($sqlJogadores, ["categoria" => $categoria]);

            if (empty($jogadores))
                continue; // Se não há jogadores, pula para a próxima categoria

            // Criar cópias para comparar
            $jogadores1 = $jogadores;
            $jogadores2 = $jogadores;
            unset($jogadores);

            // Inicializa array de controle para evitar jogos repetidos no mesmo horário/quadra
            $controle = [];
            for ($q = 1; $q <= 5; $q++) {
                for ($h = 1; $h <= 5; $h++) {
                    $controle[$q][$h] = false;
                }
            }

            $tentativas = 0;
            while ($tentativas < 2) { // Primeira tentativa com barragens iguais, segunda sem restrição
                foreach ($jogadores1 as $j1) {
                    if ($agendamentos[$j1["id"]])
                        continue;

                    $disponibilidadeArray1 = explode(",", $j1['disponibilidade']);
                    $disponibilidadeArrayOrdenada1 = array_intersect($ordemHorarios, $disponibilidadeArray1);

                    foreach ($jogadores2 as $j2) {
                        if ($j1['id'] == $j2['id'])
                            continue;
                        if ($tentativas == 0 && $j2['barragem'] != $j1['barragem'])
                            continue;
                        if ($agendamentos[$j2["id"]])
                            continue;

                        $disponibilidadeArray2 = explode(",", $j2['disponibilidade']);

                        // Encontrar um horário comum disponível entre os jogadores
                        $horarioEscolhido = null;
                        foreach ($disponibilidadeArrayOrdenada1 as $horario1) {
                            if (in_array($horario1, $disponibilidadeArray2)) {
                                if ($controle[$this->quadras[$categoria]][$horario1])
                                    continue;
                                $horarioEscolhido = $horario1;
                                break;
                            }
                        }
                        if ($horarioEscolhido === null)
                            continue;

                        // Definir quem leva a bola 🥎
                        $levarBola = ($j1["fav"] < $j2["fav"]) ? $j1["id"] :
                                (($j1["fav"] > $j2["fav"]) ? $j2["id"] :
                                (($j1["posicao"] > $j2["posicao"]) ? $j1["id"] : $j2["id"]));

                        // Registrar jogo
                        $jogos[] = [
                            "jogador1" => $j1["id"],
                            "jogador2" => $j2["id"],
                            "horario" => $horarioEscolhido,
                            "quadra" => $this->quadras[$categoria],
                            "categoria" => $categoria,
                            "levarBola" => $levarBola
                        ];

                        // Marcar jogadores como agendados
                        $agendamentos[$j1["id"]] = true;
                        $agendamentos[$j2["id"]] = true;

                        // Registrar que a quadra/horário já tem um jogo agendado
                        $controle[$this->quadras[$categoria]][$horarioEscolhido] = true;

                        break;
                    }
                }
                $tentativas++;
            }
        }

        usort($jogos, fn($a, $b) => ($a['quadra'] <=> $b['quadra']) ?: ($a['horario'] <=> $b['horario']));
        return array_values($jogos);
    }

    private function gerarTabelaJogos($jogos) {
        if (empty($jogos)) {
            return "<p class='text-center'>Nenhum jogo disponível para exibição.</p>";
        }

        $this->infosEDesign(); // Carregar configurações de quadras e horários
        $tabela = "<h3 class='text-center mt-4'>Tabela de Jogos</h3>";
        $tabela .= "<table class='table table-striped table-bordered text-center'>";
        $tabela .= "<thead class='thead-dark'>
                    <tr>
                        <th>#</th>
                        <th>Jogador 1</th>
                        <th>Jogador 2</th>
                        <th>Horário</th>
                        <th>Quadra</th>
                    </tr>
                </thead><tbody>";

        $contador = 0;

        foreach ($jogos as $jogo) {
            $contador++;

            // Obter os dados formatados dos jogadores
            $jogador = new jogador3();
            $jogador1 = $jogador->exibirJogadorComBadges($jogo["jogador1"], $jogo["levarBola"], $jogo["categoria"], true, 2);
            $jogador2 = $jogador->exibirJogadorComBadges($jogo["jogador2"], $jogo["levarBola"], $jogo["categoria"]); // Inverter badges no jogador 2
            $horario = $this->horarios[$jogo["horario"]];
            $quadra = "Quadra " . $jogo["quadra"];

            // Montar a linha da tabela
            $tabela .= "<tr>
                        <td>$contador</td>
                        <td>$jogador1</td>
                        <td>$jogador2</td>
                        <td>$horario</td>
                        <td>$quadra</td>
                    </tr>";
        }

        $tabela .= "</tbody></table>";
        return $tabela;
    }
}
