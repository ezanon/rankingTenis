<?php

class rodada3 {
    
    public function __construct($rodada = 'rodada_atual',$categoria = false){
        $this->banco = banco::instanciar();
        return true;
    }
    
    public function em_andamento() {

        $sql = "SELECT rodada_em_andamento FROM rodada_controle WHERE rodada_em_andamento = 1";
        $res = $this->banco->consultar($sql);

        return !empty($res); // Retorna true se houver resultados, false caso contrário
    }

}