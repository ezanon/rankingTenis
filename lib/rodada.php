<?php

class rodada {
	
	public $banco;
	
	public $nova_rodada;
	public $rodada_atual;
	public $numero;
	public $ano;
	public $data;
	public $nome;
	public $confirmada;

	public function __construct($id = 1) {
            $this->banco = banco::instanciar();
            $q = "select * from rodada_controle where id=$id limit 1";
            $res = $this->banco->consultar($q);
            foreach ($res as $r){
                    $this->nova_rodada = $r['nova_rodada'];
                    $this->rodada_atual = $r['rodada_atual'];
                    $this->numero = $r['numero'];
                    $this->ano = $r['ano'];
                    $this->data = $r['data'];
                    $this->nome = $r['nome'];
                    $this->confirmada = $r['confirmada'];
            }
            return true;
	}

	/*
	 * Atualiza gerenciamento da rodada
	 */
	public function nova(){
		// atualiza gerenciamento de rodada, atual ou nova
		if ($this->confirmada==0)
			return false;
		/*if ($this->nova_rodada==1){
			$this->nova_rodada = 0;
			$this->rodada_atual = 1;
		}
		else {
			$this->nova_rodada = 1;
			$this->rodada_atual = 0;
		}*/
		// atualiza rodada, nome e data
		$ano = date('Y');
		if ($ano > $this->ano){
			$this->ano = $ano;
			$this->numero = 1;
			$this->nome = 'RODADA ' . '1' . '/' . $ano;
		}
		else {
			$this->numero++;
			$this->nome = 'RODADA ' . $this->numero . '/' . $this->ano;
		}
		$q = "update rodada_controle set
				nova_rodada=1 ,
				rodada_atual=0 ,
				ano=" . $this->ano . " ,
				numero=" . $this->numero . " ,
				nome='" . $this->nome . "' ,
				data='' ,
				confirmada=0 where id=1";
		$this->banco->executar($q);
		
		return true;		
	}
	
/*
 * Confirma Rodada 
 * */

	public function confirmar(){
		$q = "update rodada_controle set confirmada=1 , nova_rodada=0, rodada_atual=1, data='" . $_POST['data'] . "' where id=1";
		$this->banco->executar($q);
		return true;
	}
	
	/*
	 * Finaliza rodada atual, computa resultados, rankeamento
	*/
	public function finalizar(){
		// computa resultados e posicoes
		$str = $this->computar_jogos();
		// apaga registros atuais da tabela ultima_rodada
		$q = "delete from ultima_rodada";
		$this->banco->executar($q);@mkdir("jogadores/" . $id);
		// copia rodada_atual, que está sendo finalizada, para ultima_rodada
		$q = "insert into ultima_rodada select * from rodada_atual";
		$this->banco->executar($q);
		// altera valores da rodada_controle
		$q = "update rodada_controle set nova_rodada=1,rodada_atual=0 where id=1";
		$this->banco->executar($q);
		// altera info da ultima rodada na tabela rodada_controle (id=2)
		$q = "update rodada_controle set nome='" . $this->nome . "', data='" . $this->data . "', numero=" . $this->numero . ", ano=" . $this->ano . " where id=2";
		$this->banco->executar($q);
		// apaga nova_rodada para q seja criada a nova
		$q = "delete from nova_rodada";
		$this->banco->executar($q);
		// libera quadras
		$quadras = new quadra();
		$quadras->desocupar_todas();
		$quadras = NULL;
                // limpa informações antigas sobre bonus e desbonux
                $js = new jogadores();
                $js->limpar_info();
                $js = NULL;
		return true;
	}

	/*
	 * Computa resultados dos jogos da rodada_atual sendo finalizada
	 */
	public function computar_jogos(){
		// cria xml do ranking atual
		$this->xml();
		// copia ranking atual pra rodada anterior
		$q = "delete from jogador_ultima_rodada";
		$this->banco->executar($q);
		$q = "insert into jogador_ultima_rodada select * from jogador";
		$this->banco->executar($q);
		// dados da rodada
		$ranking = array('misto','feminino');
		foreach ($ranking as $r){
			//$q = "select id from rodada_atual where (ocorrido=1 or woduplo=1) and ranking='$r' order by desafiado_posicao";
			$q = "select id from rodada_atual where ranking='$r' order by desafiado_posicao";
			$jogos = $this->banco->consultar($q); // obtem todos os jogos da rodada
			$str = '';
			$i = 0;
			foreach ($jogos as $j){
				$i++;
				$jogo = new jogo();
				$jogo->info($j['id'],'rodada_atual');
				$desafiante = new jogador($jogo->desafiante);
				$desafiado = new jogador($jogo->desafiado);
				// se não ocorreu, verifica se era desafio, e mantém status de pode desafiar do jogador desafiante
				if (($jogo->ocorrido==0) && ($jogo->woduplo==0)){
					if ($jogo->desafio==1){
						$desafiante->pode_desafiar();
					}
					continue;
				}
				$xml = "<jogo>
							<rodada_numero>" . $this->numero . "</rodada_numero>
							<rodada_ano>" . $this->ano . "</rodada_ano>
							<desafiante>" . $desafiante->nome_completo . "</desafiante>
							<desafiado>" . $desafiado->nome_completo . "</desafiado>
							<desafiante_id>" . $desafiante->id . "</desafiante>
							<desafiado_id>" . $desafiado->id . "</desafiado>
							<desafiante_posicao>" . $jogo->desafiante_posicao . "</desafiante_posicao>
							<desafiado_posicao>" . $jogo->desafiado_posicao . "</desafiado_posicao>
							<vencedor>" . $jogo->vencedor . "</vencedor>
							<desafiado_sets>" . $jogo->desafiante_sets . "</desafiado_sets>
							<desafiado_sets>" . $jogo->desafiado_sets . "</desafiado_sets>
							<parciais>" . $jogo->parciais . "</parciais>";
				// se foi woduplo, ambos caem 1 posicao
				if ($jogo->woduplo==1){
					$desafiante->nova_posicao($desafiante->posicao + 1);
					$desafiado->nova_posicao($desafiado->posicao + 1);
					$desafiante->perdeu();
					$desafiado->perdeu();
					$desafiante->wo();
					$desafiado->wo();
					$xml.= "\n<status_jogo>woduplo</status_jogo>";
					$xml.= "</jogo>\n";
					$desafiante->log_resultado($xml);
					$desafiado->log_resultado($xml);
					continue; // proximo jogo a ser computado
				}
				if (($jogo->ocorrido==1) && ($jogo->woduplo==0) && ($jogo->desafio==0)) { // jogo ocorreu e não é desafio
					if ($jogo->vencedor == $desafiado->id){ // desafiado vence, desafiante fica 1 posicao atras do desafiado
						$desafiante->nova_posicao($desafiado->posicao + 1);
						$desafiante->perdeu();
						$desafiado->ganhou();
						if ($jogo->desafiado_sets=='W') $desafiante->wo();
					}
					if ($jogo->vencedor == $desafiante->id){ // desafiante vence, desafiante fica na posicao do desafiado, q cai 1 posicao
						$desafiante->nova_posicao($desafiado->posicao);
						$desafiante->ganhou();
						$desafiado->perdeu();
						if ($jogo->desafiante_sets=='W') $desafiado->wo();
					}
					$xml.= "\n<status_jogo>ocorrido</status_jogo>
							</jogo>\n";
					$desafiante->log_resultado($xml);
					$desafiado->log_resultado($xml);
					continue;// proximo jogo a ser computado
				} 
				if (($jogo->ocorrido==1) && ($jogo->woduplo==0) && ($jogo->desafio==1)){ // jogo ocorreu e é desafio
					if ($jogo->vencedor == $desafiado->id){ // desafiado vence, nada acontece
						if ($jogo->desafiado_sets=='W') $desafiante->wo();
						$desafiante->perdeu();
						$desafiado->ganhou();
						continue; // nao muda posicoes // proximo jogo a ser computado
					}
					if ($jogo->vencedor == $desafiante->id){ // desafiante vence, desafiante fica na posicao do desafiado, q cai 1 posicao
						$desafiante->nova_posicao($desafiado->posicao);
						$desafiante->ganhou();
						$desafiado->perdeu();
						if ($jogo->desafiante_sets=='W') $desafiado->wo();
						continue;// proximo jogo a ser computado
					}
					$xml.= "\n<status_jogo>desafio</status_jogo>
								</jogo>\n";
					$desafiante->log_resultado($xml);
					$desafiado->log_resultado($xml);
				}
			}
		}
		$desafiado = NULL;
		$desafiante = NULL;
		$jogo = NULL;
		// coloca q nenhum jogador tem jogo marcado
		$q = "update jogador set jogo_agendado=0";
		$this->banco->executar($q);
		// corrige cores das posições 1
		$q = "update jogador set cor='BRANCO' where posicao=1";
		$this->banco->executar($q);
		// desliga quem teve 2 wo
		$q = "select id from jogador where wo=2";
		unset($jogadores);
		$jogadores = $this->banco->consultar($q);
		foreach ($jogadores as $j){
			$jogador = new jogador($j['id']);
			$jogador->eliminar('eliminado por 2 WOs');
		}
		return true;
	}
	
	/*
	 * Cria xml com as posições desta rodada
	 */
	public function xml(){
		@mkdir("files/");
		$arq = "files/" . $this->ano . "_" . $this->numero . "" . ".xml";
		$fp = fopen($arq, 'w');
		$xml = "<ranking>\n";
		$xml.= "<numero>" . $this->numero . "</numero>\n";
		$xml.= "<ano>" . $this->ano . "</numero>\n";
		$q = "select id,posicao,ranking from jogador order by ranking desc, posicao";
		$res = $this->banco->consultar($q);
		foreach ($res as $r){
			$xml.= "<jogador>\n";
			$xml.= "  <id>" . $r['id'] . "</id>\n";
			$xml.= "  <posicao>" . $r['posicao'] . "</posicao>\n";
			$xml.= "  <ranking>" . $r['ranking'] . "</ranking>\n";
			$xml.= "</jogador>\n";
		}
		$xml.= "</ranking>\n";
		fwrite($fp, $xml);
		fclose($fp);
		return true;
	}

}

?>