<?php

class fe{
	
    public $banco;

    public function __construct(){
            $this->banco = banco::instanciar();
            return NULL;
    }
    
    public function showRanking($categoria){
        $ranking = new ranking();
        return $ranking->listar($categoria);
    }
        
}