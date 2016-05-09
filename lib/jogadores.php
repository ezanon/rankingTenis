<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of jogadores
 *
 * @author erickson
 */
class jogadores {
    
    public $banco;

    public function __construct(){
            $this->banco = banco::instanciar();
            return NULL;
    }
    
    /**
     * Limpa campo jogador->info do BD se tiver 2 rodadas de diferença da informação para a atual
     */
    public function limpar_info(){
        // obtém rodada atual : $rodada->rodada_atual
        // isola o número da rodada
        $rodada = new rodada();
        if ($rodada->numero <= 2) // se a rodada for a 1, início do ano, mantém as últimas do ano anterior
            return true;
        // obtem lista de ids de quem tem info com a palavra BONUS
        $q = "select id,info from jogador where info like '%BONUS-%'";
        $res = $this->banco->consultar($q);
        foreach ($res as $r){
            $id = $r['id'];
            $info = $r['info'];
            // obtém rodada do bonus ou desbonus
            $aux = explode('-', $info);
            $aux = explode(' ', $aux[0]);
            $rodada_bonus = $aux[1];
            if (($rodada->numero - $rodada_bonus > 3) or
                ($rodada->numero - $rodada_bonus < 0)){ // apaga a informação antiga do jogador
                $dados['info'] = '';
                $this->banco->alterar('jogador',$id,$dados);
            }
        }
        return true;
    }
    
    
        
}
