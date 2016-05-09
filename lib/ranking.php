<?php

class ranking {
	
	public $banco;
	
	function __construct(){
		$this->banco = banco::instanciar();
		// obtem maximas posicoes dos rankings
		$this->misto_max = $this->max('misto');
		$this->feminino_max = $this->max('feminino');
		return NULL;
	}
	
	public function max($ranking){
		$q = "select posicao from jogador where ranking='$ranking' order by posicao desc limit 1";
		$res = $this->banco->consultar($q);
		foreach ($res as $r)
			$p = $r['posicao'];
		return $p;
	}
	
}