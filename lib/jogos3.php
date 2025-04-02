<?php

class jogos3 {
    
    public function __construct($rodada = 'rodada_atual',$categoria = false){
        $this->banco = banco::instanciar();
        return true;
    }
    
    public function tem_jogo_agendado($id) {

        $sql = "SELECT * FROM jogos_agendados 
                WHERE (jogador1_id = :jogador_id OR jogador2_id = :jogador_id) 
                AND jogo_possivel = 0
                LIMIT 1";

        $resultado = $this->banco->consultar($sql, ["jogador_id" => $id]);

        return $resultado ? $resultado : false;
    }
    
    public function tem_jogo_possivel($id) {

        $sql = "SELECT * FROM jogos_agendados 
                WHERE (jogador1_id = :jogador_id OR jogador2_id = :jogador_id) 
                AND jogo_possivel = 1
                LIMIT 1";

        $resultado = $this->banco->consultar($sql, ["jogador_id" => $id]);

        return $resultado ? $resultado : false;
    }


    
}