<?php

require_once('jogo.php');

class jogos extends jogo {
	
	public $rodada;
	public $jogo = array(); // todos os jogos da rodada - objeto jogo
	
	public function __construct($rodada = 'rodada_atual',$categoria = false){
		$this->rodada = $rodada;
		$this->banco = banco::instanciar();
		if ($rodada == 'rodada_atual'){
                    $where = '';
                    if ($categoria){
                        $where = "where ranking='$categoria'";
                    }
			$q = "select id from $rodada $where order by confirmado desc, quadra, desafiante_posicao";
			$lista = $this->banco->consultar($q); // obtem todos os jogos da rodada
			foreach ($lista as $j){           
                                $id = $j['id'];
				$this->jogo[$id] = new jogo();
				$this->jogo[$id]->get($id,$rodada);
			}
		}
		return true;
	}
        
        /*
         * Obtem ids dos jogos
         */
        public function get_ids($rodada,$categoria=null){
            $where = '';
            if ($categoria){
                $where = "where ranking='$categoria'";
            }
            $q = "select id from $rodada $where order by confirmado desc, quadra, ranking, desafiante_posicao";
            $lista = $this->banco->consultar($q);
            foreach ($lista as $par){
                $ids[] = $par['id'];
            }
            return $ids;
        }

}

