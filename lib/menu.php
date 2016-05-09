<?php

class menu {
	
	public $rodada;

	function imprimir(){

		$str = '';
		if (@$_SESSION['jogador']['jogador']){
			$str.= "<menu>
					  <strong>jogador: </strong>
					  <a href=?module=jogador&action=listar>classificação</a> |
					  <a href=?module=login&action=sair>sair</a>
					</menu>";
		}
		if (@$_SESSION['jogador']['admin']){
			$str.= "<menu>
					  <strong>admin:</strong> 
					  <a href=?module=admin&action=listagem>classificação</a> |";
			// rodadas
			$this->rodada = new rodada();
			if ($this->rodada->rodada_atual==1)	
				$str.= " <a href=?module=admin&action=rodada_atual>rodada atual</a> |";
			else
				$str.= " <a href=?module=admin&action=nova_rodada>nova rodada</a> |"; 
			//ultima rodada
			$str.= " <a href=?module=admin&action=ultima_rodada>ultima rodada</a> |";
			//arquivos
			$str.= " <a href=?module=admin&action=arquivos>obter arquivos</a> |";
			//novo jogador
			
				// se a rodada está em andamento, não libera a criação de novo jogador
				$rodada = new rodada();
				if ($rodada->nova_rodada == 1){
					$str.= " <a href=?module=admin&action=novo_jogador>novo jogador</a> |";
				}
				$rodada = NULL;

			//funcoes
			$str.= " <a href=?module=admin&action=funcoes>funcoes</a> |";
			// sair 
			$str.= "<a href=?module=login&action=sair>sair</a>
					</menu>";
		}
		return $str;
	}

}

?>