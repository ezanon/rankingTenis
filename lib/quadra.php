<?php

class quadra {

	public $banco;

	function __construct($id = NULL){
		$this->banco = banco::instanciar();
		if ($id!=NULL)
			$this->info($id);
		return NULL;
	}
	
	function info($id){
		$q = "select * from quadras where id=$id";
		$infos = $this->banco->consultar($q);
		foreach ($infos as $info){
			$this->id = $info['id'];
			$this->quadra = $info['quadra'];
			$this->nome = $info['quadra'] . ' - ' . $info['horario'];
			$this->horario = $info['horario'];
			$this->ocupada = $info['ocupada'];
			$this->disponivel = $info['disponivel'];
		}
		return true;
	}
	
	function ocupar($id = NULL){
		if ($id == NULL)
			$id = $this->id;
		$q = "update quadras set ocupada=1 where id=$id";
		$this->banco->executar($q);
		return true;
	}
	
	function desocupar($id = NULL){
		if ($id == NULL)
			$id = $this->id;
		$q = "update quadras set ocupada=0 where id=$id";
		$this->banco->executar($q);
		return true;
	}
	

	function desocupar_todas(){
		$q = "update quadras set ocupada=0";
		$this->banco->executar($q);
		return true;
	}
	
/*
Exibe as quadras disponiveis em tag <select>
*/
	function disponiveis(){
		$q = "select * from quadras where ocupada=0 and disponivel=1 order by id";
		$quadras = $this->banco->consultar($q);
		$str = "<select name=quadra>";
		$str.= "<option value=0>LIVRE</option>";
		foreach ($quadras as $quadra){
			$str.= "<option value=" . $quadra['id'] . ">" . $quadra['quadra'] . " - " . $quadra['horario'] . "</option>\n";
		}
		//$str.= "<option value=0>LIVRE</option>";
		$str.= "</select>";
		return $str;
	}

}

?>