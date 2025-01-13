<?php

class fe{
	
    public $banco;

    public function __construct(){
            $this->banco = banco::instanciar();
            return NULL;
    }
    
    public function showHome(){
        return true;
    }
    
    public function showRanking($categoria){
        $ranking = new ranking();
        return $ranking->listar($categoria);
    }
    
    public function showRodadaAtual($categoria=false){
        return $categoria;
    }
    
    public function showUltimaRodada($categoria=false){
        return $categoria; 
    }
    
    public function showRegulamento(){
        return true;
    }
    
    public function showTorneio2022(){
        return true;
    }
    
    public function showTorneio(){
        return true;
    }
    
    /*
     * tela de login para o frontend
     */
    public function meuRanking(){
        return true;
    }
    
    /*
     * realiza o login no frontend
     */
    public function feLogin(){
        return true;
    }
    
     /*
     * opções para o jogador
     */
    public function meuRankingOpcoes(){
        return true;
    }
    
    
        
}
