<?php

class admin {
	
	public $banco;
	public $confirma_todos_possiveis = false;
	
	function __construct(){
// comentei pois não estava listando em celular
//		if (!$_SESSION['jogador']['admin']){ // se não é admin, mas tentou acessar admin, retorna a página de jogadores
//			global $url;
//			$url .= "?module=jogador&action=listar";
//			header("Refresh: 0; URL=$url");
//		}
            
		$menu = new menu;
		echo $menu->imprimir();
            
		$this->banco = banco::instanciar();
		return NULL;
	}
	
	function listagem() {
		$ranking = array('misto','feminino');
		$str = '';
		// se a rodada está em andamento, não libera as funções que alteram o status do jogador
		$rodada = new rodada();
		if ($rodada->nova_rodada == 0){
			$altera_status = false;
		}
		else {
			$altera_status = true;
		}
		foreach ($ranking as $r){
			$q = "select id from jogador where ranking='$r' and ativo=1 order by posicao";
			$jogadores_ids = $this->banco->consultar($q);
			$str.= "<div id=$r>\n<h2>ranking $r</h2><table width=780>
						<tr>
							<th>Posição</th>
							<th>Nome</th>
							<th>Sexo</th>
							<th>Jogos</th>
							<th>Vitórias</th>
							<th>Derrotas</th>
							<th>Vit Consecutivas</th>
							<th>Der Consecutivas</th>
							<th>WxO</th>
							<th>Informações</th>";
			if ($altera_status){
				$str.= "<th>Sobe</th>
						<th>Desce</th>
						";
			}
			$str.= "</tr>\n";
		
							
			foreach ($jogadores_ids as $jogador_id){
				$j = new jogador($jogador_id['id']);
				$wo = "&nbsp;";
				if ($j->wo > 0) 
					$wo = $j->wo;
				$str.="<tr onMouseOver=\"javascript:this.style.backgroundColor='#AFEEEE'\"
							onMouseOut=\"javascript:this.style.backgroundColor=''\">
							<a name=" . $j->id . " /><td class=jogador_" . $j->cor . ">" . $j->posicao . "</td>
							<td class=nome><a href=?module=admin&action=editar_jogador&id=" . $j->id . " />" . $j->nome_completo . "</a></td>
							<td>" . $j->sexo . "</td>
							<td>" . $j->jogos . "</td>
							<td>" . $j->vitorias_total . "</td>
							<td>" . $j->derrotas_total . "</td>
							<td>" . $j->vitorias_consecutivas . "</td>
							<td>" . $j->derrotas_consecutivas . "</td>
							<td class=wo>" . $wo . "</td>
							<td>" . $j->info . "</td>";
				if ($altera_status){
					$str.= "<td><a href=?module=admin&action=jogador_sobe&id=" . $j->id . " />Sobe</a></td>
							<td><a href=?module=admin&action=jogador_desce&id=" . $j->id . " />Desce</a></td>
							";
				}
				$str.= "</tr>";
			}
			$str.= "</table></div><br />";
		}
		return $str;
	}
	
/* 
Exibe os jogos que podem acontecer na próxima rodada 
*/	
	public function nova_rodada(){
		// verifica se já tem nova_rodada em andamento para confirmacao
		//$count = $this->banco->contar(nova_rodada);
		return $this->nova_rodada_criacao();
	}
	
	public function nova_rodada_criacao(){
		$ranking = array('misto','feminino');
		$str = '';
		$confirmar = 0;
		$btn_conf_todos = 0;
		foreach ($ranking as $r){
			$str.= "<div id=$r><a name=$r><a name=$r/></a><h2>ranking $r</h2>";
			$desafios = $this->lista_pode_desafiar($r);
			$possiveis = false;
			if (!$desafios) // desafios e bonus já decididos, aí mostra jogos possiveis
				$possiveis = $this->jogos_possiveis($r);
			if ($desafios) 
				$str.= $desafios;
			if ($possiveis)
				$str.= $possiveis;
			if ((!$desafios) && (!$possiveis)) 
				$confirmar++; // soma 1 a cada ranking
			$str.= $this->jogos_confirmados($r,1); // jogos agendados 
			$str.= $this->jogos_confirmados($r,0); // jogos possíveis 
                        if ($confirmar>=1){ // o ranking que já tiver todos os jogos confirmados, exibe os jogos entre vizinhos
                            $this->jogos_vizinhos($r);
                            $str.= $this->jogos_confirmados($r,-1); // jogos entre vizinhos
                        }    
                        //$str.= "<p>jogos vizinhos:<br><br>" . $this->jogos_vizinhos($r) . "</p>";
			if (($possiveis) && (!$desafios)) $btn_conf_todos++;  // só há possiveis, incrementa btão... se btão 2 então será exibido, pois em misto e feminino só há possíveis
			$str.= "</div>\n";
		}
		if ($btn_conf_todos == 2){ // se há jogos possíveis, exibe botão que permite confirmar todos como jogos de uma vez, sem agendamento de quadra
			$str = '<form action=?module=admin&action=confirmar_tudo method=post>
						<p><br>Confirmar TODOS os jogos restantes como possíveis? <input type=submit value="SIM" /></p>
						<input type=hidden name=confirma_tudo value=1 />
					</form>' . "\n" . $str;
		}
		if ($confirmar==2)  // se 2, não há jogos a serem agendados, nem do misto nem do feminino
			$str2 = $this->confirmar_rodada(false);
		else 
			$str2 = '';
		$str = $str2 . $str;
		return $str;	
	}
	
/*
Confirma todos os jogso como possíveis
*/
	public function confirmar_tudo(){
		$this->confirma_todos_possiveis = true;
		$ranking = array('misto','feminino');
		$str = '';
		foreach ($ranking as $r){
			$this->jogos_possiveis($r);
		}
		$str = $this->nova_rodada();
		return $str;
	}
	
/*
Exibe lista e opções dos jogadores que podem desafiar ou receber bonus
*/
	public function lista_pode_desafiar($ranking) {
		global $url;
		$str = '';
		$q = "select id from jogador where ativo=1 and pode_desafiar=1 and ranking='$ranking' and jogo_agendado=0 order by posicao";
		$jogadores = $this->banco->consultar($q);
		$str.= "<h3>Desafio ou Bônus?</h3><table>
						<tr>
							<th>&nbsp;</th>
							<th>Jogador</th>
							<th>Opção</th>
							<th>Agendar</th>
							<th>&nbsp;</th>
						</tr>
					";
		$k = 0;
		foreach ($jogadores as $j){
			$id = $j['id'];
			$k++;
			// define quantidade de posições para bonus e 
			$desafiante = new jogador($id);
			$p_bonus = $desafiante->ver_bonus();
			// define desafiado
    		unset($desafiaveis);
			$desafiaveis = array();
			$desafiaveis = $desafiante->desafiaveis(); // lista de id dos desafiaveis
			$desafio = ''; // string da tag select dos desafiaveis
			$num_desafiaveis = count($desafiaveis);
			if ($num_desafiaveis>=1){
				foreach ($desafiaveis as $d){
					$desafiavel = new jogador($d);
					$desafio.= "<option value=" . $desafiavel->id . ">Desafiar ". $desafiavel->nome_pos . "</option>";
					$desafiavel = NULL;
				}
			}
			$quadras = new quadra();
			$str.= " <form id=desafio$ranking$k action=$url?module=admin&action=desafiar method=post>
					  <tr>
						<td>$k</td>
						<td>" . $desafiante->nome_pos . " #" . $desafiante->vitorias_consecutivas . "</td>
						<td>
								<select name=opcao>
								  <option value=0 selected>A definir</option>
								  <option value=bonus>Bônus de $p_bonus posições</option>
								  $desafio
								</select>
						</td>
						<td>" . $quadras->disponiveis() . "</td>
						<td>
							<input type=submit value=Confirmar />
							<input type=hidden name=bonus value=$p_bonus />
							<input type=hidden name=id value=" . $desafiante->id ." />
							<input type=hidden value=\"" . $desafiante->ranking . "\"  name=ranking />
						</td>
					 </tr>
					</form>";
		}
		$str.= "</table>";
		if ($k==0) // não há jogadores com bonus ou desafios a serem confirmados
			return false;
		else
			return $str;
	} 

/*
Exibe lista dos jogos possíveis para a rodada baseado nas cores branco e verde
*/	
	function jogos_possiveis($ranking){
		global $url;
		$i = 1; // numeracao dos jogos
		$str = '';
		$str.= "<h3>Jogos Possíveis</h3>";
		$str.= "<table>
					<tr>
						<th>Jogo</th>
						<th>Desafiante</th>
						<th>&nbsp;</th>
						<th>Desafiado</th>
						<th>&nbsp;</th>
						<th>Agendar</th>
						<th>Confirmar?</th>
					</tr>
				";
		$q = "select id from jogador where ranking='$ranking' and ativo=1 and jogo_agendado=0 and cor='VERDE' order by posicao";
		$jogadores = $this->banco->consultar($q);
		$desafiados = array();
		foreach ($jogadores as $j){
			$desafiante = new jogador($j['id']);
			// procura possivel desafiado BRANCO
			$q = "select id from jogador where ativo=1 and ranking='$ranking' and cor='BRANCO' and posicao<" . $desafiante->posicao . " order by posicao desc limit 1";
			$desafiaveis = $this->banco->consultar($q);
			foreach ($desafiaveis as $d){
				//echo "# " . $desafiante->id . $desafiante->nome_pos . " X " . $d['id'] . "#<br>";
				$nao_marcar = false;
				if (in_array($d['id'],$desafiados)) {// verifica se o desafiado já foi desafiado
					$nao_marcar = true;
					break;
				}
				$desafiado = new jogador($d['id']);
				if ($desafiado->jogo_agendado == 1) {// verifica se o desafiado já tem jogo marcado
					$nao_marcar = true;
					break;
				}
				$desafiados[] = $desafiado->id; // para verificar se o jogador já foi desafiado
				break;
			}
			if ($nao_marcar)
				continue;
			
			// se é para confirmar todos os jogos como possíveis, confirma este, e já passa pro próximo sem montar html
			if ($this->confirma_todos_possiveis){
				$this->marcar_jogo($desafiante->id, $desafiado->id, 0);
				continue;
			}
			
			$quadras = new quadra();
			// exibe jogo
			$str.= "<form id=jogo$ranking$i action=$url?module=admin&action=marcar_jogo method=post>
					<tr>
					   <td>$i</td>
					   <td>" . $desafiante->nome_pos . "</td>
					   <td> X </td>
					   <td>" . $desafiado->nome_pos . "</td>
					   <td>&nbsp;</td>
					   <td>" . $quadras->disponiveis() . "</td>
					   <td>
						 <input type=submit value=Confirmar />
						 <input type=hidden name=desafiante value=" . $desafiante->id . " />
						 <input type=hidden name=desafiado value=" . $desafiado->id . " />
						 <input type=hidden name=ranking value=" . $desafiante->ranking . "/>
					   </td>
					 </tr>
					 </form>";
			$i++;
			$dados['id'] = $i;
			$dados['desafiante'] = $desafiante->id;
			$dados['desafiado'] = $desafiado->id;
			//$this->banco->inserir('nova_rodada',$dados);			
		}
		$str.="</table>";
		if ($i==1) 
			return false;
		else 
			return $str;	
	}
	
/*
Exibe lista dos jogos possíveis para a rodada baseado nas cores branco e verde
*/	
	function jogos_possiveis_old($ranking){
		global $url;
		$i = 0; // numeracao dos jogos
		$str = '';
		$str.= "<h3>Jogos Possíveis</h3>";
		$str.= "<table>
					<tr>
						<th>Jogo</th>
						<th>Desafiante</th>
						<th>&nbsp;</th>
						<th>Desafiado</th>
						<th>&nbsp;</th>
						<th>Agendar</th>
						<th>Confirmar?</th>
					</tr>
				";
		$q = "select id,nome_completo,posicao,cor from jogador where ranking='$ranking' and ativo=1 and jogo_agendado=0 order by posicao";
		$jogadores = $this->banco->consultar($q);
		foreach ($jogadores as $j){
			if ($j['cor']=='BRANCO') {
				$desafiado['livre'] = true;  // se já tem jogo, marcado num ciclo antes deste foreach, este valor será false
				$desafiado['id'] = $j['id']; // armazeno o id do possível desafiado
				$desafiado['nome_completo'] = $j['nome_completo'];
				$desafiado['posicao'] = $j['posicao'];
			}
			if ($j['cor']!='VERDE') continue; // marcamos somente se é desafiante, ou seja, verde
			if (($j['cor']=='VERDE') && ($desafiado['livre'])) {
				$i++;
				$quadras = $this->quadras();
				$str.= "<form id=jogo$ranking$i action=$url?module=admin&action=marcar_jogo method=post>
						<tr>
						   <td>$i</td>
						   <td>" . $j['nome_completo'] . " (" . $j['posicao'] . ")</td>
						   <td> X </td>
						   <td>" . $desafiado['nome_completo'] . " (" . $desafiado['posicao'] . ")</td>
						   <td>&nbsp;</td>
						   <td>$quadras</td>
						   <td>
						 	 <input type=submit value=Confirmar />
							 <input type=hidden name=desafiante value=" . $j['id'] . " />
							 <input type=hidden name=desafiado value=" . $desafiado['id'] . " />
						   </td>
						 </tr>
						 </form>";
				$dados['id'] = $i;
				$dados['desafiante'] = $j['id'];
				$dados['desafiado'] = $desafiado['id'];
				//$this->banco->inserir('nova_rodada',$dados);
				$desafiado['livre'] = false;
			}
		}
		$str.="</table>";
		if ($i==0) $str = '';
		return $str;	
	}
        
        /*
         * Cria jogos entre vizinhos (mesma cor) que não têm jogos agendados ou possíveis
         */
        public function jogos_vizinhos($ranking) {
            // verifica se os jogos entre vizinhos já foram sugeridos
            $q = "select count(*) as num from nova_rodada where ranking='$ranking' and vizinho=-1";
            $numjogos = $this->banco->consultar($q);
            $saida = '';
            if ($numjogos[0]['num'] == 0){ // cria jogos
                // seleciona jogadores do ranking sem jogo na rodada
                $ids = $this->jogadores_sem_jogo($ranking);

                //echo "<p>ranking $ranking</p>\n";

                
                $i=0;
                foreach ($ids as $id){
                    $i++;
                    if ($i==1) { // o primeiro da lista nunca será o desafiante
                        $id_anterior = $id;
                        continue; 
                    }
                    $desafiado = new jogador($id_anterior);
                    $desafiante = new jogador($id);
                    
                    /*echo "<br>"
                            . "{$desafiado->id} P {$desafiado->posicao} ({$desafiado->cor})   x "
                            . "{$desafiante->id} P {$desafiante-> posicao} ({$desafiante->cor})    ";*/
                    
                    if ($desafiado->posicao==1){ //primeiro do ranking nunca será desafiado por vizinho
                        $id_anterior = $id;
                        $desafiado = null;
                        $desafiante = null;
                        continue;
                    }
                    // verifica se há jogadores com jogos agendados entre ambos
                    // não pode haver jogos entre vizinhos se há jogos entre ambos
                    if ($this->jogos_entre_posicoes($desafiado->posicao, $desafiante->posicao, $ranking) > 0){ 
                        $id_anterior = $id;
                        $desafiado = null;
                        $desafiante = null;
                        continue;
                    }
                    // confirma se desafiado não tem jogo marcado mesmo
                    // pode ser que já tenha entrado num jogo entre vizinhos
                    if ($desafiado->jogo_agendado==1){
                        $id_anterior = $id;
                        $desafiado = null;
                        $desafiante = null;
                        continue;
                    }
                    // verifica se são da mesma cor
                    // se for branco x verde não vai marcar 
                    // pra ativar branco x verde basta comentar essa condição
                    if ($desafiado->cor != $desafiante->cor){
                        $id_anterior = $id;
                        $desafiado = null;
                        $desafiante = null;
                        continue;
                    }                
                    // agenda o jogo
                    $this->marcar_jogo($desafiante->id, $desafiado->id,0,0,-1); // último parâmetro 1 é flag vizinho
                    
                    //echo 'MARCADO';
                    
                    //$saida.= "{$desafiante->posicao}[{$desafiante->id}]({$desafiante->cor}) x {$desafiado->posicao}[{$desafiado->id}]({$desafiado->cor}) <br>\n";  
                    $id_anterior = $id;
                }
            }    
            return $saida;
        }
        /*
         * Verifica se há jogadores com jogos agendados entre 2 jogadores no rankeamento para jogos entre vizinhos
         */
        public function jogos_entre_posicoes($p1,$p2,$r){
            $q = "select count(*) as contador from jogador where jogo_agendado=1 and (posicao between $p1 and $p2) and ranking='$r' and ativo=1";
            $res = $this->banco->consultar($q);
            return $res[0]['contador'];
        }
        
        /*
         * Devolve ids dos jogadores sem jogo na rodada para gerar jogos entre vizinhos
         */
	public function jogadores_sem_jogo($ranking){
            $q = "select id from jogador where jogo_agendado=0 and ativo=1 and cor!='AMARELO' and ranking='$ranking' order by posicao";
            $jogadores_disponiveis = $this->banco->consultar($q); 
            if ($jogadores_disponiveis){ 
                $ids = array();
                foreach ($jogadores_disponiveis as $j){
                    $ids[] = $j['id'];
                }
                return $ids; // array com os ids dos jogadores disponíveis pra jogos entre vizinhos
            }
            else {
                return false;
            }
        }
/* 
	Função que dá o bônus ao jogador ou confirma desafio
*/
	public function desafiar($id){
		global $url;
		if ($_POST['opcao']=='bonus'){ // dá bonus ao jogador
			$jogador = new jogador($id);
			$bonus = $jogador->bonificar();
			return "Jogador(a) " . $jogador->nome_completo . " optou por bônus de " . $bonus . " indo para a posição " . $jogador->posicao;
		}
		elseif ((is_numeric($_POST['opcao'])) && ($_POST['opcao']!=0)) { // definiu desafio
			$jogador = new jogador($id);
			$desafiado = new jogador($_POST['opcao']);
			//$jogador->agendar_jogo($desafiado->id,1,$_POST['quadra']);
			$this->marcar_jogo($jogador->id,$desafiado->id,$_POST['quadra'],1);
			return "Jogador(a) " . $jogador->nome_completo . " optou por desafio contra " . $desafiado->nome_completo . " pela posição " . $desafiado->posicao;
		}
		else {
			$url .= "?module=admin&action=nova_rodada";
			header("Refresh: 0; URL=$url");
			return NULL;
		}
	}

/* 
	Função que marca jogo possível
*/
	public function marcar_jogo($desafiante_id = null, $desafiado_id = null, $quadra_id = 0, $desafio = 0, $vizinho = 0){
		
		if (!$desafiante_id) $desafiante_id = $_POST['desafiante'];
		if (!$desafiado_id) $desafiado_id = $_POST['desafiado'];
		if (@$_POST['quadra']) $quadra_id = $_POST['quadra'];
		
		$desafiante = new jogador($desafiante_id);
		$desafiado = new jogador($desafiado_id);
		$confirmado = 0; // se tem quadra, fica 1
		if ($quadra_id != 0){ // marca quadra escolhida como ocupada
			$quadra = new quadra($quadra_id);
			$quadra->ocupar();
			$confirmado = 1;		
		}
                if ($vizinho==-1){
                    $confirmado=-1;
                }
		$q = "insert into nova_rodada (desafiante,desafiado,desafio,confirmado,ranking,quadra,desafiante_pos,desafiado_pos,vizinho) 
			  values (" . $desafiante->id . "," . $desafiado->id . ",$desafio,$confirmado,'" . $desafiante->ranking . "'," . $quadra_id . "," . $desafiante->posicao . "," . $desafiado->posicao . "," . $vizinho . ")";
		$this->banco->executar($q);
		$q = "update jogador set jogo_agendado=1,pode_desafiar=0 where id=" . $desafiante->id . " or id=" . $desafiado->id; 
		$this->banco->executar($q);
		$str = "Jogo Confirmado: " . $desafiante->nome_pos . " X " . $desafiado->nome_pos;
		return $str;
	}


/* 
	Função que marca jogo possível
*/
	public function marcar_jogo_old(){
		$desafiante = new jogador($_POST['desafiante']);
		$desafiado = new jogador($_POST['desafiado']);
		$confirmado = 0; // se tem quadra, fica 1
		$q = 0;
		if ($_POST['quadra']!=0){ // marca quadra escolhida como ocupada
			$quadra = new quadra($_POST['quadra']);
			$quadra->ocupar();
			$confirmado = 1;
			$q = $quadra->id;		
		}
		$q = "insert into nova_rodada (desafiante,desafiado,desafio,confirmado,ranking,quadra) 
			  values (" . $desafiante->id . "," . $desafiado->id . ",0,$confirmado,'" . $desafiante->ranking . "'," . $q. ")";
		$this->banco->executar($q);
		$q = "update jogador set jogo_agendado=1,pode_desafiar=0 where id=" . $desafiante->id . " or id=" . $desafiado->id; 
		$this->banco->executar($q);
		$str = "Jogo Confirmado: " . $desafiante->nome_pos . " X " . $desafiado->nome_pos;
		return $str;
		
	}
	
/*
Jogos marcados
*/
	public function jogos_confirmados($ranking,$confirmado){
		$q = "select * from nova_rodada where ranking='$ranking' and confirmado=$confirmado order by quadra,desafiante_pos";
		$jogos = $this->banco->consultar($q);
		$str = "<h3>Jogos Agendados</h3>";
		if ($confirmado==0)
			$str = "<h3>Jogos Possíveis</h3>";
                elseif ($confirmado==-1)
                    $str = "<h3>Jogos entre Vizinhos</h3>";
		$str .= "<table>
					<tr>
						<th>&nbsp;</th>
						<th>Jogo</th>
						<th>Desafiante</th>
						<th>&nbsp;</th>
						<th>Desafiado</th>
						<th>Agenda</th>
						<th>Observações</th>
					<tr>";
		$i = 0;
		foreach ($jogos as $jogo){
			$i++;
			$desafiante = new jogador($jogo['desafiante']);
			$desafiado = new jogador($jogo['desafiado']);
			if ($jogo['quadra']!=0) {
				$quadra = new quadra($jogo['quadra']);
				$q = $quadra->quadra . "<br>" . $quadra->horario;
			}
			if ($confirmado==0)
				$q = "Jogo Possível";
                        if ($confirmado==-1)
                                $q = "Jogo Vizinhos";
			if ($jogo['desafio']==1)
				$obs = 'Desafio';
			else $obs = '';
			$id_jogo = $jogo['id'];
			$str.= "
					<tr>
						<td><a href=?module=admin&action=apaga_jogo&id=$id_jogo><img src=\"images/delete-icon-16.png\" alt=apagar /></a></td>
						<td>$i</td>
                                                <td>{$desafiante->nome_pos}</td>
						<td> X </td>
                                                <td>{$desafiado->nome_pos}</td>
						<td>$q</td>
						<td>$obs</td>
					</tr>
					";
		}
		$str.= "</table>";
		return $str;
	}
	
	/*
	Apaga jogo agendado ou possível durante a fase de construção da rodada
	*/
	public function apaga_jogo($id){
		$jogo = new jogo();
		$jogo->get($id,'nova_rodada');
		$str = $jogo->apagar();
		$jogo = NULL;
		return $str;
	}
	
	public function confirmar_rodada($confirma = false){
		$rodada = new rodada();
		if (!$confirma){ // mostra botão para confirmar a rodada
			$rodada->nova();
			$str = "<hr><form method=post action=?module=admin&action=confirmar_rodada&id=true>
					<blockquote>
					<h2>Confirmar Rodada?</h2>
					<blockquote><h3>" . $rodada->nome . "<h3>
					Data: <input type=text name=data size=60 /><br /><br />
					<input type=submit value='Confirmar Rodada' /></p>
					</blockquote></blockquote>
					</form><hr>";
		}
		else { // confirma jogo
			$q = "delete from rodada_atual"; // limpa tabela
			$this->banco->executar($q);
			$q = "insert into rodada_atual (id,desafiante,desafiado,confirmado,desafio,ranking,quadra,desafiante_posicao,desafiado_posicao,vizinho) select * from nova_rodada"; // copia todos os jogos
			$this->banco->executar($q);
			$rodada->confirmar();
			//$q = 'update rodada_atual,jogador set rodada_atual.desafiante_posicao=jogador.posicao where rodada_atual.desafiante=jogador.id'; // adiciona posicao do desafiante
			//$this->banco->executar($q);
			//$q = 'update rodada_atual,jogador set rodada_atual.desafiado_posicao=jogador.posicao where rodada_atual.desafiado=jogador.id'; // adiciona posicao do desafiado
			//$this->banco->executar($q);
			$str = "<h3>Rodada " . $rodada->nome . " confirmada.</h3>";
		}
		return $str;
	}
	
	/*
	* exibe os jogos atuais
	*/
	public function rodada_atual(){
		$rodada = new rodada();
		$str = "<h2>" . $rodada->nome . "</h2><h3>" . $rodada->data . "</h3>";
		$str.= "<p><a href=download.php?id=9 target=_blank>Súmulas da Rodada Atual (Jogos agendados)</a></p>";
		$q = "select id from rodada_atual order by confirmado desc, quadra, ranking desc, desafiante_posicao";
		$jogos = $this->banco->consultar($q); // obtem todos os jogos da rodada
		// cabecalho da tabela de jogos"
		$str.= "<table>
					<tr>
					<th>Jogo</th>
					<th>&nbsp;</th>
					<th>Quadra</th>
					<th>Desafiante</th>
					<th>&nbsp;</th>
					<th>Desafiado</th>
					<th>Vencedor</th>
					<th>Desafiante SETs</th>
					<th>&nbsp;</th>
					<th>Desafiado SETs</th>
					<th>Parciais</th>
					<th>&nbsp;</th>
					</tr>
					";
		$i = 0;
		foreach ($jogos as $j){
			$i++;
			$jogo = new jogo();
			$jogo->info($j['id'],'rodada_atual');
			if ($jogo->quadra==0){
				$quadra = 'jogo possivel';
                                if ($jogo->vizinho==-1)
                                    $quadra = 'jogo vizinhos';
                        }
			else {
				$quadra = new quadra($jogo->quadra);
				$q = $quadra->nome;
				$quadra = NULL;
				$quadra = $q;
			}
			
			if ($jogo->ranking=='misto')
				$desafio = 'misto';
			else $desafio = 'feminino';
			if ($jogo->desafio==1)
				$desafio.= '<br />desafio';

			
			$desafiante = new jogador($jogo->desafiante);
			$desafiado = new jogador($jogo->desafiado);
			
			/*
			Definição dos selects
			*/
			$item1 = $desafiante->id;
			if ($desafiante->id==$jogo->vencedor)
				$item1.= ' selected="selected"';
			$item2 = $desafiado->id;
			if ($desafiado->id==$jogo->vencedor)
				$item2.= ' selected="selected"';
			$item3 = 'woduplo';
			if ($jogo->woduplo==1)
				$item3.= ' selected="selected"';
			$item4 = 'adiado';
			if ($jogo->cancelado==1)
				$item4.= ' selected="selected"';
			$vencedor = "<select name=vencedor>
							<option value='none'>-</option>\n
							<option value=$item1>" . $desafiante->nome_completo . "</option>\n
							<option value=$item2>" . $desafiado->nome_completo . "</option>\n
							<option value=$item3>Duplo WO</option>\n
							<option value=$item4>Adiar</option>\n
							</select>
							";
			$item1 = '0';
			if ($jogo->desafiante_sets==0)
				$item1.= ' selected="selected"';
			$item2 = '1';
			if ($jogo->desafiante_sets==1)
				$item2.= ' selected="selected"';
			$item3 = '2';
			if ($jogo->desafiante_sets==2)
				$item3.= ' selected="selected"';
			$item4 = "'W'";
			if ($jogo->desafiante_sets=='W')
				$item4.= ' selected="selected"';
			$item5 = "'O'";
			if ($jogo->desafiante_sets=='O')
				$item5.= ' selected="selected"';
			$desafiante_sets = "<select name=desafiante_sets>
							<option value=$item1>0</option>\n
							<option value=$item2>1</option>\n
							<option value=$item3>2</option>\n
							<option value=$item4>W</option>\n
							<option value=$item5>O</option>\n
							</select>
							";
			$item1 = '0';
			if ($jogo->desafiado_sets==0)
				$item1.= ' selected="selected"';
			$item2 = '1';
			if ($jogo->desafiado_sets==1)
				$item2.= ' selected="selected"';
			$item3 = '2';
			if ($jogo->desafiado_sets==2)
				$item3.= ' selected="selected"';
			$item4 = "'W'";
			if ($jogo->desafiado_sets=='W')
				$item4.= ' selected="selected"';
			$item5 = "'O'";
			if ($jogo->desafiado_sets=='O')
				$item5.= ' selected="selected"';
			$desafiado_sets = "<select name=desafiado_sets>
							<option value=$item1>0</option>\n
							<option value=$item2>1</option>\n
							<option value=$item3>2</option>\n
							<option value=$item4>W</option>\n
							<option value=$item5>O</option>\n
							</select>
							";
			$str.= "<form method=post action=?module=admin&action=registra_resultado&id=" . $jogo->id . ">
						<tr>
							<td>" . $i . "</td>
							<td>" . $desafio . "</td>
							<td>" . $quadra . "</td>
							<td>" . $desafiante->nome_pos . "</td>
							<td>X</td>
							<td>" . $desafiado->nome_pos . "</td>
							<td>" . $vencedor . "</td>
							<td>$desafiante_sets</td>
							<td>X</td>
							<td>$desafiado_sets</td>
							<td><input type=text name=parciais value=\"". $jogo->parciais . "\" /></td>
							<td>
								<input type=submit name=resultado value='Enviar Resultado' />
							</td>
						</tr>
					</form>
					";
			$jogo = NULL;
		}
		$str.= "</table>";
		// botao para terminar a rodada
		$str.= "<br><br><hr><p>
					Clique no botão abaixo para terminar a rodada. Todos os resultados serão computados e o ranking será calculado automaticamente.<br><br>
					<form method=post action=?module=admin&action=terminar_rodada_atual>
						<input type=submit name=botao value='Terminar Rodada' />
						<input type=hidden name=terminate value=1 />
					</form>
				</p><hr><br>";
		return $str;
	}
	
	/*
	 * Finaliza rodada atual, computa resultados, rankeamento
	 */
	public function terminar_rodada_atual(){
		$rodada = new rodada();
		$rodada->finalizar();
		return "A " . $rodada->nome . " foi finalizada e seus resultados computados.";
	}
	
	/*
	 * Exibe resultados da última rodada
	 */
	public function ultima_rodada(){
		$rodada = new rodada(2);
		$str = "<h2>" . $rodada->nome . "</h2><h3>" . $rodada->data . "</h3>";
		$q = "select id from ultima_rodada order by confirmado desc, quadra, ranking desc, desafiante_posicao";
		$jogos = $this->banco->consultar($q); // obtem todos os jogos da rodada
		// cabecalho da tabela de jogos"
		$str.= "<table>
				<tr>
					<th>Jogo</th>
					<th>Ranking</th>
					<th>Quadra</th>
					<th>Desafiante</th>
					<th>&nbsp;</th>
					<th>Desafiado</th>
					<th>Vencedor</th>
					<th>Desafiante SETs</th>
					<th>&nbsp;</th>
					<th>Desafiado SETs</th>
					<th>Parciais</th>
				</tr>
				";
		$i = 0;
		foreach ($jogos as $j){
			$i++;
			$jogo = new jogo();
			$jogo->info($j['id'],'ultima_rodada');
			if (($jogo->quadra==0) and ($jogo->vizinho==0))
				$quadra = 'jogo possivel';
                        elseif (($jogo->quadra==0) and ($jogo->vizinho==-1))
				$quadra = 'jogo vizinhos';
			else {
				$quadra = new quadra($jogo->quadra);
				$q = $quadra->nome;
				$quadra = NULL;
				$quadra = $q;
			}
				
			if ($jogo->ranking=='misto')
				$desafio = 'misto';
			else $desafio = 'feminino';
			if ($jogo->desafio==1)
				$desafio.= '<br />desafio';
		
				
			$desafiante = new jogador($jogo->desafiante);
			$desafiado = new jogador($jogo->desafiado);
			if ($jogo->vencedor > 0) {
				$vencedor = new jogador($jogo->vencedor);
				$winner = $vencedor->nome_completo;
			}
			else 
				$winner = '&nbsp;';
			/*
			 Definição dos selects
			*/
			
			$str.= "<tr>
						<td>" . $i . "</td>
						<td>" . $desafio . "</td>
						<td>" . $quadra . "</td>
						<td>" . $desafiante->nome_completo . "(" . $jogo->desafiante_posicao . ")</td>
						<td>X</td>
						<td>" . $desafiado->nome_completo . "(" . $jogo->desafiado_posicao . ")</td>
						<td>" . $winner . "</td>
						<td>". $jogo->desafiante_sets . "</td>
						<td>X</td>
						<td>". $jogo->desafiado_sets . "</td>
						<td>". $jogo->parciais . "</td>
					</tr>
					";
			$jogo = NULL;
			$desafiante = NULL;
			$desafiado = NULL;
			$vencedor = NULL;
		}
		$str.= "</table>";
		return $str;
	}
	
	
/*
 * Lista arquivos para download
 * É fornecida pela VIEW em HTML
 */	
	public function arquivos(){
		return NULL;
	}
	
/*
 * Registra o resultado de um jogo
 */
	public function registra_resultado($id){
		$vencedor = $_POST['vencedor'];
		if ($vencedor=='none'){
			$q = "update rodada_atual set vencedor=0,
				desafiante_sets='0',
				desafiado_sets='0',
				parciais='',
				ocorrido=0, woduplo=0, cancelado=0 where id=$id limit 1";
			$this->banco->executar($q);
			return "Não foi definido o vencedor deste jogo.";
		}
		$jogo = new jogo();
		$jogo->info($id);
		if ($vencedor=='adiado'){
			$jogo->cancelar();
			return "Jogo Adiado.";
		}
		$desafiante = new jogador($jogo->desafiante);
		$desafiado = new jogador($jogo->desafiado);
		if ($vencedor=='woduplo'){
			$jogo->woduplo();
			return "Os jogadores " . $desafiante->nome_completo . " e " . $desafiado->nome_completo . " perdem 1 posição cada confirmando-se o WO duplo.";
		}
		// registra sets e parciais
		$q = "update rodada_atual set vencedor=" . $_POST['vencedor'] . ",
									  desafiante_sets='" . $_POST['desafiante_sets'] . "', 
		                              desafiado_sets='" . $_POST['desafiado_sets'] . "',
		                              parciais='" . $_POST['parciais'] . "',
									  ocorrido=1, woduplo=0, cancelado=0 where id=" . $jogo->id . " limit 1"; 
		$this->banco->executar($q);
		$str = "<p>Resultado registrado: " . $desafiante->nome_completo . " <b>" . $_POST['desafiante_sets'] . "</b> x " . "
										  <b>" . $_POST['desafiado_sets'] . "</b> " . $desafiado->nome_completo . "
										  <i>   Parciais: " . $_POST['parciais'] . "</i></p>";
		
		return $str;	
	}
	
/*
 * Registrar novo jogador
 */
	public function novo_jogador(){
		// mostra tela de cadastro, com ultimas posicoes como sugestao
		$q = "select posicao from jogador where ranking='misto' order by posicao desc limit 1";
		$res = $this->banco->consultar($q);
		foreach ($res as $r)
			$p = $r['posicao'];
		$p++;
		$str = "$p misto - ";
		$q = "select posicao from jogador where ranking='feminino' order by posicao desc limit 1";
		$res = $this->banco->consultar($q);
		foreach ($res as $r)
			$p = $r['posicao'];
		$p++;
		$str.= "feminino $p";
		return $str;		
	}
	
	/*
	 * Adiciona novo jogador
	 */
	public function add_jogador(){
		// verifica se posicao sugerida é válida
		$posicao = trim($_POST['posicao']);
		if ((!is_numeric($posicao)) || ($posicao < 1))
			return "Posição sugerida inválida: '" . $_POST['posicao'] . "'";
		// obtem ultima posicao do raking
		$ranking = new ranking();
		
		if ($_POST['ranking'] == 'misto')
			$max = $ranking->misto_max;
		if ($_POST['ranking']== 'feminino')
			$max = $ranking->feminino_max;
		if ($posicao > $max)
			$posicao = $max + 1;
		// corrige posicao dos que ficarão atrás deste jogador novo
		$q = "update jogador set posicao = posicao + 1 where posicao >= $posicao and ranking='" . $_POST['ranking'] . "'";
		$this->banco->executar($q);
		// insere na ultima posicao
		$nome = strtoupper($_POST['nome']);
		$q = "insert into jogador (nome_completo,unidade,categoria,sexo,email,
								   telefone_celular,telefone_residencial,telefone_comercial,
								   ranking,cor,posicao)
						  values  ('" . $nome . "', 
						  		   '" . $_POST['unidade'] . "',
						  		   '" . $_POST['categoria'] . "',
						  		   '" . $_POST['sexo'] . "',
						  		   '" . $_POST['email'] . "',
						  		   '" . $_POST['cel'] . "',
						  		   '" . $_POST['res'] . "',
						  		   '" . $_POST['cml'] . "',
						  		   '" . $_POST['ranking'] . "',
						  		   '" . $_POST['cor'] . "',
						  		   " . $posicao . ")";
		$this->banco->executar($q);
		// cria pasta
		$q = "select id from jogador where nome_completo='" . $nome . "' and ranking='" . $_POST['ranking'] . "' order by id desc limit 1";
		$res = $this->banco->consultar($q);
		foreach ($res as $r){
			$id = $r['id'];
			@mkdir("jogadores/" . $id);
		}
		return "Novo jogador inserido com sucesso.";
	}
	
	/*
	 * Altera jogador
	*/
	public function alterar_jogador($id){
		$q = "update jogador set 
				nome_completo='" . $_POST['nome'] . "',
				unidade='" . $_POST['unidade'] . "',
				categoria='" . $_POST['categoria'] . "',
				sexo='" . $_POST['sexo'] . "',
				email='" . $_POST['email'] . "',
				telefone_celular='" . $_POST['cel'] . "',
				telefone_residencial='" . $_POST['res'] . "',
				telefone_comercial='" . $_POST['cml'] . "',
				info='" . $_POST['info'] . "',
				observacoes='" . $_POST['observacoes'] . "',
				jogos=" . $_POST['jogos'] . ",
				vitorias_total=" . $_POST['vitorias_total'] . ",
				derrotas_total=" . $_POST['derrotas_total'] . ",
				vitorias_consecutivas=" . $_POST['vitorias_consecutivas'] . ",
				derrotas_consecutivas=" . $_POST['derrotas_consecutivas'] . ",
				pode_desafiar=" . $_POST['pode_desafiar'] . ",
				wo=" . $_POST['wo'] . "
			 where id=" . $id;
		$this->banco->executar($q);
		return true;
	}
	
	/*
	 * Sobe uma posicao
	 */
	public function jogador_sobe($id){
		$jogador = new jogador($id);
		$jogador->sobe();
		return $id;
	}
	
	/*
	 * Desce uma posicao
	*/
	public function jogador_desce($id){
		$jogador = new jogador($id);
		$jogador->desce();
		return $id;
	}
	
	/*
	 * tela para editar jogador
	 * será dada pela view
	 */
	public function editar_jogador($id){
		return NULL;		
	}
	
	/*
	 * tira ou coloca o jogador em licença
	 */
	public function licencia_jogador($id){
		$jogador = new jogador($id);
		$jogador->licenciar();
		return true;
	}
	
	/*
	 * altera cor do jogador
	 */
	public function jogador_cor($id){
		$jogador = new jogador($id);
		$jogador->nova_cor($_GET['cor']);
		return true;
	}
	
	/*
	 * altera posicao do jogador
	*/
	public function jogador_posicao($id){
		$jogador = new jogador($id);
		$jogador->nova_posicao($_POST['posicao']);
		return true;
	}
	
	/*
	 * Elimina jogador
	 */
	public function jogador_eliminar($id){
		$jogador = new jogador($id);
		$jogador->eliminar($_POST['motivo']);
		// TODO arrumar posicoes após eliminação
		return true;
	}
	
	/*
	 * Apaga jogador ... diferente de eliminar
	 */
	public function jogador_apagar($id){
		$jogador = new jogador($id);
		$jogador->apagar();
		// TODO arrumar posicoes após eliminação
		return true;
	}
	
	/*
	 * Exibe funções
	 * Será dada pela view
	*/
	public function funcoes(){
		return NULL;
	}
	
	/*
	 * zera WOs
	 */
	public function zerar_wos(){
		$q = "update jogador set wo=0";
		$this->banco->executar($q);
		return true;
	}
	
	/*
	 * reorganiza cores
	 */
	public function cores(){
		$ranking = array('misto','feminino');
		$rank = new ranking();
		foreach ($ranking as $r){
			$q = "select id from jogador where ranking='$r' order by posicao";
			$jogadores = $this->banco->consultar($q);
			$cor = 'BRANCO'; // cor inicial, posicao 1
			foreach ($jogadores as $j){
				$jogador = new jogador($j['id']);
				//echo "<br># " . $jogador->posicao . " " . $jogador->cor;
				if ($jogador->cor == 'AMARELO')
					continue;
				//echo " " . $jogador->cor;
				$jogador->nova_cor($cor);
				if ($cor=='BRANCO') // proxima cor a ser usada
					$cor = 'VERDE';
				else
					$cor = 'BRANCO';
				// se for última posicao, cor = VERDE
				if ($jogador->ranking == 'misto')
					if ($jogador->posicao == $rank->misto_max) 
						$jogador->nova_cor('VERDE');
				if ($jogador->ranking == 'feminino')
					if ($jogador->posicao == $rank->feminino_max)
						$jogador->nova_cor('VERDE');
			}	
			
		}
		return true;
	}
	
}

?>