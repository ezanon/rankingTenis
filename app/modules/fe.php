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

}
