<?php

class jogo{

	public $banco;
	
	function __construct(){
		$this->banco = banco::instanciar();
		return NULL;
	}
	
	/* 
	* obtem dados de um jogo, seja da rodada atual quanto da nova rodada
	*/
	public function get($id,$rodada = 'rodada_atual'){
		$q = "select * from $rodada where id=$id limit 1";
		$jogo = $this->banco->consultar($q);
		foreach ($jogo as $j){
			foreach ($j as $key => $value){
				$this->$key = $value;
			}
			break;
		}
		$this->rodada = $rodada;
		return true;
	}
	
	function info($id,$rodada = false){
                if (!$rodada) {
                    $rodada = 'rodada_atual';
                }
		$q = "select * from $rodada where id=$id limit 1";
		$jogo = $this->banco->consultar($q);
		foreach ($jogo as $j){
			$this->id = $j['id'];
			$this->confirmado = $j['confirmado'];
			$this->desafio = $j['desafio'];
			$this->ranking = $j['ranking'];
			$this->quadra = $j['quadra'];
			$this->desafiante = $j['desafiante']; // registro
			$this->desafiado = $j['desafiado'];  // registro
			$this->desafiante_posicao = $j['desafiante_posicao'];
			$this->desafiado_posicao = $j['desafiado_posicao'];
			$this->desafiante_sets = $j['desafiante_sets'];
			$this->desafiado_sets = $j['desafiado_sets'];
			$this->vencedor = $j['vencedor'];
			$this->parciais = $j['parciais'];
			$this->ocorrido = $j['ocorrido'];
			$this->cancelado = $j['cancelado'];
			$this->woduplo = $j['woduplo'];
		}
		return true;
	}
	
/*
 * Cancela o jogo
 */
	function cancelar($id = NULL){
		if ($id==NULL)
			$id = $this->id;
		$q = "update rodada_atual set cancelado=1, ocorrido=0, woduplo=0 where id=$id";
		$this->banco->executar($q);
		return true;
	}
	
	/*
	 * registra wo duplo
	*/
	function woduplo($id = NULL){
		if ($id==NULL)
			$id = $this->id;
		$q = "update rodada_atual set woduplo=1, ocorrido=1,cancelado=0 where id=$id limit 1";
		$this->banco->executar($q);
		return true;
	}
	
	/*
	* apaga jogo que está sendo marcado
	* libera jogadores envolvido no jogo apagado
	*/
	public function apagar(){
		if ($this->rodada != 'nova_rodada')
			return 'Não é possível apagar este jogo';
		$desafiante = new jogador($this->desafiante);
		$desafiante->jogo_apagado($this->desafio);
		$desafiado = new jogador($this->desafiado);
		$desafiado->jogo_apagado(0);
		if ($this->quadra != 0){
			$quadra = new quadra($this->quadra);
			$quadra->desocupar();
		}
		$q = "delete from nova_rodada where id=" . $this->id;
		$this->banco->executar($q);
		return 'Jogo apagado';
	}
	
}

?>