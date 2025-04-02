<?php

class fe {

    public $banco;

    public function __construct() {
        $this->banco = banco::instanciar();
        return NULL;
    }

    public function showHome() {
        return true;
    }

    public function showRanking($categoria) {
        $ranking = new ranking();
        return $ranking->listar($categoria);
    }

    public function showRodadaAtual($categoria = false) {
        return $categoria;
    }

    public function showUltimaRodada($categoria = false) {
        return $categoria;
    }

    public function showRegulamento() {
        return true;
    }

    public function showTorneio2022() {
        return true;
    }

    public function showTorneio() {
        return true;
    }

    /*
     * tela de login para o frontend
     */

    public function meuRanking() {
        return true;
    }

    /*
     * realiza o login no frontend
     */

    public function feLogin() {
        return true;
    }

    /*
     * opções para o jogador
     */

    public function meuRankingOpcoes() {
        return true;
    }

 function salvarResultados()
{
    $banco = banco::instanciar();

    // Captura os dados do formulário
    $vencedorId = $_POST['vencedor'] ?? null;
    $resultado = $_POST['resultado'] ?? null;
    $parciais = $_POST['parciais'] ?? null;
    $quemLevouBola = $_POST['quemLevouBola'] ?? null;
    $observacoes = $_POST['observacoes'] ?? null;
    //die(print_r($_POST));

    // Verifica se temos um jogo agendado para esse jogador
    $sqlJogo = "SELECT id FROM jogos_agendados WHERE jogador1_id = :jogador_id OR jogador2_id = :jogador_id";
    $dadosJogo = $banco->consultar($sqlJogo, ["jogador_id" => $_SESSION['jogador']['id']]);

    if (!$dadosJogo) {
        return "Nenhum jogo agendado encontrado.";
    }

    $jogoId = $dadosJogo[0]['id'];

    // Atualiza a tabela jogos_agendados
    $sqlUpdate = "UPDATE jogos_agendados 
                  SET vencedor_id = :vencedor, 
                      resultado = :resultado, 
                      parciais = :parciais, 
                      quem_levou_bola = :quem_levou_bola ,
                      observacoes = :observacoes
                  WHERE id = :jogo_id";

    $params = [
        "vencedor" => $vencedorId,
        "resultado" => $resultado,
        "parciais" => $parciais,
        "quem_levou_bola" => $quemLevouBola,
        "jogo_id" => $jogoId,
        "observacoes" => $observacoes
    ];

    $atualizado = $banco->executar($sqlUpdate, $params);

    if ($atualizado) {
        return "Resultado registrado com sucesso.";
    } else {
        return "Erro ao salvar o resultado.";
    }
}


public function registrarJogoPossivel() {
    
    // Verifica se os campos necessários foram enviados
    if (!isset($_POST['adversario'], $_POST['vencedor'], $_POST['resultado'], $_POST['bolas'], $_POST['categoria'], $_POST['barragem'])) {
        return $this->mensagem('Erro: Todos os campos são obrigatórios!', 'danger');
    }

    $jogador_id = $_SESSION['jogador']['id'];
    $adversario_id = $_POST['adversario'];
    $vencedor = $_POST['vencedor'];
    $resultado = $_POST['resultado'];
    $bolas = $_POST['bolas'];
    $observacoes = $_POST['observacoes'] ?? ''; // Pode ser opcional
    $categoria = $_POST['categoria'];
    $barragem = $_POST['barragem'];
    $parciais = $_POST['parciais'];

    // Se o vencedor for 0, significa que o adversário venceu
    if ($vencedor == 0) {
        $vencedor = $adversario_id;
    }
    if ($bolas == 0) {
        $bolas = $adversario_id;
    }

    // Verifica se já existe um jogo possível registrado
    $sqlVerificar = "SELECT id FROM jogos_agendados 
                     WHERE (jogador1_id = :jogador_id OR jogador2_id = :jogador_id) 
                     AND jogo_possivel = 1
                     LIMIT 1";
    
    $jogoExistente = $this->banco->consultar($sqlVerificar, ["jogador_id" => $jogador_id]);

    if ($jogoExistente) {
        // Atualiza o jogo existente
        $sqlAtualizar = "UPDATE jogos_agendados 
                         SET jogador1_id = :jogador1, jogador2_id = :jogador2, vencedor_id = :vencedor, 
                             resultado = :resultado, quem_levou_bola = :bolas, observacoes = :observacoes, 
                             categoria = :categoria, parciais = :parciais 
                         WHERE id = :jogo_id";
        
        $params = [
            "jogador1" => $jogador_id,
            "jogador2" => $adversario_id,
            "vencedor" => $vencedor,
            "resultado" => $resultado,
            "bolas" => $bolas,
            "categoria" => $categoria,
            "observacoes" => $observacoes,
            "parciais" => $parciais,
            "jogo_id" => $jogoExistente[0]['id']
        ];

        if ($this->banco->executar($sqlAtualizar, $params)) {
            return $this->mensagem('Jogo atualizado com sucesso!', 'success');
        } else {
            return $this->mensagem('Erro ao atualizar o jogo.', 'danger');
        }
    } else {
        // Inserir o jogo na tabela jogos_agendados
        $sqlInserir = "INSERT INTO jogos_agendados (jogador1_id, jogador2_id, vencedor_id, resultado, quem_levou_bola, observacoes, categoria, jogo_possivel) 
                       VALUES (:jogador1, :jogador2, :vencedor, :resultado, :bolas, :observacoes, :categoria, 1)";

        $params = [
            "jogador1" => $jogador_id,
            "jogador2" => $adversario_id,
            "vencedor" => $vencedor,
            "resultado" => $resultado,
            "bolas" => $bolas,
            "categoria" => $categoria,
            "observacoes" => $observacoes
        ];

        if ($this->banco->executar($sqlInserir, $params)) {
            return $this->mensagem('Jogo registrado com sucesso!', 'success');
        } else {
            return $this->mensagem('Erro ao registrar o jogo.', 'danger');
        }
    }
}


    private function mensagem($texto, $tipo) {
        return '<div class="alert alert-' . $tipo . ' p-3 text-center" role="alert">' . $texto . '</div>';
    }




}
