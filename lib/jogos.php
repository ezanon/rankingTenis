<?php

require_once('jogo.php');

class jogos extends jogo {
	
	public $rodada;
	public $jogo = array(); // todos os jogos da rodada - objeto jogo
	
	public function __construct($rodada = 'rodada_atual'){
		$this->rodada = $rodada;
		$this->banco = banco::instanciar();
		if ($rodada == 'rodada_atual'){
			$q = "select id from $rodada order by confirmado desc, quadra, desafiante_posicao";
			$lista = $this->banco->consultar($q); // obtem todos os jogos da rodada
			foreach ($lista as $id){
				$this->jogo[$id] = new jogo();
				$this->jogo[$id]->info($id,$rodada);
			}
		}
		return true;
	}
	
}

?>