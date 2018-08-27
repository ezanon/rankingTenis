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
        
}
