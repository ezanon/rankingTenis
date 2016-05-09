<?php

class jogador {

	public $info;
	public $banco;

	function __construct($id = NULL){
		$this->banco = banco::instanciar();
		if ($id!=NULL)
			$this->info($id);
		return NULL;
	}
	
/*
Obtem informacoes do jogador e grava no objeto
*/
	public function info($id){
		if (!$id) return false;
                // verifica se jogar é ativo ou inativo, para buscá-lo
                $num_j = $this->banco->contar('jogador','id',$id);
                $num_i = $this->banco->contar('jogador_inativos','id',$id);
                if ($num_j > $num_i)
                    $q = "select * from jogador where id=$id";
                else
                    $q = "select * from jogador_inativos where id=$id";
		$res = $this->banco->consultar($q);
		foreach ($res as $r){
			$this->id = $r['id'];
			$this->nome_completo = $r['nome_completo'];
			$this->posicao = $r['posicao'];
			$this->cor = $r['cor'];
			$this->ranking = $r['ranking'];
			$this->nome_pos = $this->nome_completo . '(' . $this->posicao . ')';
			$this->nome_id = $this->nome_completo . '(id ' . $this->id . ' pos ' . $this->posicao . ')';
			$this->jogo_agendado = $r['jogo_agendado'];
			$this->sexo = $r['sexo'];
			$this->wo = $r['wo'];
			$this->email = $r['email'];
			$this->jogos = $r['jogos'];
			$this->vitorias_total = $r['vitorias_total'];
			$this->derrotas_total = $r['derrotas_total'];
			$this->vitorias_consecutivas = $r['vitorias_consecutivas'];
			$this->derrotas_consecutivas = $r['derrotas_consecutivas'];
			$this->unidade = $r['unidade'];
			$this->observacoes = $r['observacoes'];
			$this->telcel = $r['telefone_celular'];
			$this->telres = $r['telefone_residencial'];
			$this->telcml = $r['telefone_comercial'];
			$this->info = $r['info'];
			$this->categoria = $r['categoria'];
			$this->pode_desafiar = $r['pode_desafiar'];
		}
		return true;
	}

/*
Exibe lista dos jogadores	
*/

	public function listar() {
		$ranking = $_SESSION['jogador']['ranking'];
		$q = "select * from jogador where ranking='$ranking' and ativo=1 order by posicao";
		$jogadores = $this->banco->consultar($q);
		$str = '';
		$str.= "<h2>ranking $ranking</h2><table>
					<tr>
						<th>Posição</th>
						<th>Nome</th>
						<th>E-Mail</th>
						<th>Jogos</th>
						<th>Vitórias</th>
						<th>Derrotas</th>
						<th>Vit Consecutivas</th>
						<th>Der Consecutivas</th>
						<th>WxO</th>
						<th>Informações</th>
					</tr>
				";
		foreach ($jogadores as $j){
				$wo = "&nbsp;";
				if ($j['wo']>0) $wo = $j['wo'];
				if ($j['id'] == $_SESSION['jogador']['id'])
					$str.= "<tr class=jogador_online_" . $j['cor'] . ">";
				else $str.=" <tr>";
				$str.="     <td class=jogador_" . $j['cor'] . ">" . $j['posicao'] . "</td>
							<td class=nome>" . $j['nome_completo'] . "</td>
							<td>" . $j['email'] . "</td>
							<td>" . $j['jogos'] . "</td>
							<td>" . $j['vitorias_total'] . "</td>
							<td>" . $j['derrotas_total'] . "</td>
							<td>" . $j['vitorias_consecutivas'] . "</td>
							<td>" . $j['derrotas_consecutivas'] . "</td>
							<td class=wo>" . $wo . "</td>
							<td>" . $j['info'] . "</td>
						</tr>
						";
		}
		$str.= "</table>";
		return $str;
	}
	
/*
Obtem lista dos jogadores que podem ser desafiados pelo jogador $id
*/
	public function desafiaveis($id = NULL){
		if ($id==NULL)
			$id = $this->id;
		$p = $this->ver_posicao($id);// obtem posicao	
		$p_bonus = $this->ver_bonus($p); // obtem bonus
		$p_desafia = $this->ver_desafia($p); // obtem maximo desafiado
		$desafiaveis = array();
		$forte = $p-$p_desafia;
		$fraco = $p-$p_bonus-1;
		if ($fraco<=1)	// não há quem desafiar
			return $desafiaveis;
		if ($forte<=1)
			$forte = 2; // correção dos limites
		for ($i=$forte; $i<=$fraco; $i++) { // cria lista dos desafiaveis em ordem descrescente de posicoes, mais forte primeiro
			if ($i<=1)				
				break;
			$desafiavel_id = $this->jogador_na_posicao($i);
			$disponivel = $this->disponivel($desafiavel_id,true);
			if ($disponivel)
				$desafiaveis[] = $desafiavel_id;
		}
		return $desafiaveis;
	}
	
/*
Bonifica o jogador
*/
	public function bonificar(){
		$rodada = new rodada();
		$nome_rodada = 'R ' . $rodada->numero . '-' . $rodada->ano;
		$bonus = $this->ver_bonus();
		$nova_posicao = $this->posicao - $bonus;
		$this->add_info($nome_rodada . "<br>" . $this->posicao . '-BONUS-' . $nova_posicao);
		$this->nova_posicao($nova_posicao);
		/*// corrige posição dos que estarão atrás do jogador bonificado 
		$q = "update jogador set posicao = posicao + 1 where posicao >= $nova_posicao and posicao < " . $this->posicao;
		$this->banco->executar($q);
		// nova posicao do bonificado
		$q = "update jogador set posicao=$nova_posicao where id=" . $this->id;
		$this->banco->executar($q);*/
		// jogador nao desafia, então pode aparecer na lista para jogos possiveis
		$q = "update jogador set pode_desafiar=0 where id=" . $this->id; // para não aparecer na lista de desafiantes novamente
		$this->banco->executar($q);
		// define cor verde para o bonificado
		$q = "update jogador set cor='VERDE' where id=" . $this->id; // para não aparecer na lista de desafiantes novamente
		$this->banco->executar($q);
		return $bonus;
	}
	
/*
Agenda desafio
*/
	public function agendar_jogo($desafiado_id,$desafio,$quadra){
		$confirmado = 0; // se tem quadra, fica 1
		if ($quadra!=0){ // marca quadra escolhida como ocupada
			$qd = new quadra();
			$qd->ocupar($quadra);
			$qd = NULL;
			//$q = "update quadras set ocupada=1 where id=" . $quadra;
			//$this->banco->executar($q);
			$confirmado = 1;
		}
		$desafiado_pos = $this->ver_posicao($desafiado_id);
		$q = "insert into nova_rodada (desafiante,desafiado,desafio,confirmado,ranking,quadra,desafiante_posicao,desafiado_posicao) 
			  values (" . $this->id . ",$desafiado_id,$desafio,$confirmado,'" . $this->ranking . "',$quadra," . $this->posicao . ",$desafiado_pos)";
		$this->banco->executar($q);echo $q;
		$q = "update jogador set jogo_agendado=1,pode_desafiar=0 where id=" . $this->id . " or id=" . $desafiado; // para não aparecerem na lista de desafiantes/desafiados novamente
		$this->banco->executar($q);
		return true;
	}

/*********************************************************************************************************************

FUNÇÕES AUXILIARES

*********************************************************************************************************************/

/*
Verifica cor atual do jogador
*/
	public function verifica_cor($id = NULL){
		if ($id==NULL)
			$id = $this->id;
		$q = "select cor from jogador where id=$id limit 1";
		$res = $this->banco->consultar($q);
		foreach ($res as $r){
			$cor = $r['cor'];
		}
		return $cor;
	}
	
/*
Verifica posicao do jogador
*/
	public function ver_posicao($id = NULL){
		if ($id==NULL)
			$id = $this->id;
		$q = "select posicao from jogador where id=$id limit 1";
		$res = $this->banco->consultar($q);
		foreach ($res as $r){
			$pos = $r['posicao'];
		}
		return $pos;
	}

/*
Obtem jogador na posicao $p
*/
	public function jogador_na_posicao($p,$r = 'misto'){
		$q = "select id from jogador where posicao=$p and ranking='$r' limit 1";
		$res = $this->banco->consultar($q);
		foreach ($res as $r){
			$id = $r['id'];
		}
		return $id;
	}
	
/*
Verifica qual bonus possível para um jogador na posicao p
*/
	public function ver_bonus($p = NULL){
		if ($p==NULL)
			$p = $this->posicao;
		if ($p>=176)
			$p_bonus = 40;
		elseif (($p <= 175) && ($p >= 151))
			$p_bonus = 35;
		elseif (($p <= 150) && ($p >= 126))
			$p_bonus = 30;
		elseif (($p <= 125) && ($p >= 101))
			$p_bonus = 25;
		elseif (($p <= 100) && ($p >= 76))
			$p_bonus = 20;
		elseif (($p <= 75) && ($p >= 51))
			$p_bonus = 16;	
		elseif (($p <= 50) && ($p >= 26))
			$p_bonus = 10;
		elseif (($p <= 25) && ($p >= 11))
			$p_bonus = 6;
		elseif (($p <= 10) && ($p >= 6))
			$p_bonus = 4;
		elseif ($p == 5)
			$p_bonus = 3;
		elseif ($p == 4)
			$p_bonus = 2;
		elseif ($p == 3)
			$p_bonus = 1;
		elseif ($p <= 2)
			$p_bonus = 0;
		return $p_bonus;
	}
	
/*
Verifica quantas posicoes acima o jogador pode desafiar
*/
	public function ver_desafia($p){
		if ($p>=176)
			$p_desafia = 80;
		elseif (($p <= 175) && ($p >= 151))
			$p_desafia = 70;
		elseif (($p <= 150) && ($p >= 126))
			$p_desafia = 60;
		elseif (($p <= 125) && ($p >= 101))
			$p_desafia = 50;
		elseif (($p <= 100) && ($p >= 76))
			$p_desafia = 40;
		elseif (($p <= 75) && ($p >= 51))
			$p_desafia = 30;
		elseif (($p <= 50) && ($p >= 26))
			$p_desafia = 20;
		elseif ($p <= 25)
			$p_desafia = 10;
		return $p_desafia;
	}

/*
Verifica se o jogador está disponível para jogo
*/
	function disponivel($id = NULL, $desafio = false){
		if ($id==NULL)
			$id = $this->id;
		$q = "select jogo_agendado,ativo,sexo,cor from jogador where id=$id limit 1";
		$res = $this->banco->consultar($q);
		foreach ($res as $r){
			if ($r['jogo_agendado']==1) //já tem jogo agendado
				return false;
			if ($r['ativo']==0) // é ativo no ranking
				return false;
			if (($r['sexo']=='F') && ($desafio) && ($this->sexo=='M')) // homem não pode desafiar mulher
				return false;//echo "### " . $this->id . " x $id ###";
			if ((($r['cor']!='BRANCO') && ($r['cor']!='VERDE')) && ($desafio)) // no desafio pode desafiar branco e verde
				return false;
		}
		return true;
	}

/*
 * Registra nova posição e corrige dos demais jogadores
 */
	function nova_posicao($nova_posicao){
		//echo "# " . $this->nome_completo . " #<br>";
		//echo "# " . $this->ranking . " #<br>";
		$ranking = new ranking();
		if ($this->ranking == 'misto')
			if ($nova_posicao >= $ranking->misto_max){
				$nova_posicao = $ranking->misto_max;
				$this->nova_cor('VERDE'); // se é o último, cor fica VERDE
			}	
		if ($this->ranking == 'feminino')
			if ($nova_posicao >= $ranking->feminino_max) {
				$nova_posicao = $ranking->feminino_max;
				$this->nova_cor('VERDE');  // se é o último, cor fica VERDE
			}	
		if ($nova_posicao < $this->posicao)
			// corrige posição dos que estarão atrás do jogador se ele progrediu
			$q = "update jogador set posicao = posicao + 1 where posicao >= $nova_posicao and posicao < " . $this->posicao . " and ranking='" . $this->ranking . "'";
		elseif ($nova_posicao > $this->posicao)
			// corrige posição dos que estarão a frente do jogador se ele regrediu
			$q = "update jogador set posicao = posicao - 1 where posicao <= $nova_posicao and posicao > " . $this->posicao . " and ranking='" . $this->ranking . "'";
		else 
			return false; // não há alteração de posicao
		//echo "# " . $q . " #<br>";
		$this->banco->executar($q);
		// atualiza posicao do jogador
		$this->posicao = $nova_posicao;
		$q = "update jogador set posicao = " . $nova_posicao . " where id = " . $this->id . " limit 1";
		//echo "# " . $q . " #<br>";
		$this->banco->executar($q);
		// se nova posicao é 1, cor fica BRANCO
		if ($nova_posicao == 1)
			$this->nova_cor('BRANCO');
		return true;
	}
	
	/*
	 * Registra wo para o jogador
	 */
	function wo(){
		$q = "update jogador set wo = wo + 1 where id = " . $this->id . " limit 1";
		$this->banco->executar($q);
		return true;
	}

	/*
	 * Altera cor
	 */
	public function nova_cor($cor){
		$q = "update jogador set cor='$cor' where id=" . $this->id;
		$this->banco->executar($q);
		$this->cor = $cor;
		return true;
	}
	
	/*
	 * Adiciona vitoria
	 */
	public function ganhou(){
		$q = "update jogador set vitorias_total=vitorias_total+1,
								 vitorias_consecutivas=vitorias_consecutivas+1,
								 derrotas_consecutivas=0 where id=" . $this->id;
		$this->banco->executar($q);
		$this->vitorias_consecutivas++;
		$this->derrotas_consecutivas=0;
		$this->vitorias_total++;
		$this->nova_cor('VERDE');
		$this->jogou();
		$this->pode_desafiar();
		// verifica se pode desafiar na proxima rodada
		/*if (($this->vitorias_consecutivas%3 == 0) && ($this->vitorias_consecutivas > 0)){
			$q = "update jogador set pode_desafiar=1 where id=" . $this->id . " limit 1";
			$this->banco->executar($q);
		}*/
		return true;
	}
	
	/*
	 * Verifica se jogador pode realizar desafio
	 */
	public function pode_desafiar($id = NULL){
		if ($id==NULL)
			$id = $this->id;
		if (($this->vitorias_consecutivas%3 == 0) && ($this->vitorias_consecutivas > 0)){
			$q = "update jogador set pode_desafiar=1 where id=" . $id . " limit 1";
			$this->banco->executar($q);
		}
	}
	
	/*
	 * Adiciona derrota
	*/
	public function perdeu(){
		$q = "update jogador set derrotas_total=derrotas_total+1,
								 derrotas_consecutivas=derrotas_consecutivas+1,
								 vitorias_consecutivas=0 where id=" . $this->id;
		$this->banco->executar($q);
		$this->derrotas_consecutivas++;
		$this->vitorias_consecutivas=0;
		$this->derrotas_total++;
		$this->nova_cor('BRANCO');
		$this->jogou();
		$this->desbonus();
		return true;
	}
	
	/*
	 * Elimina o jogador
	 * Move registro para tabela jogador_inativos
	 */
	public function eliminar($motivo){
		// registra motivo da eliminacao
		$hoje = date('d-m-Y');
		$motivo = $hoje . ' -> ' . $motivo;
		$q = "update jogador set info='$motivo' where id=" . $this->id . " limit 1";
		$this->banco->executar($q);
		// insere na tabela jogador_inativos, o registro do eliminado
		$q = "insert into jogador_inativos select * from jogador where id=" . $this->id . " limit 1";
		$this->banco->executar($q);
		// apaga registro do eliminado da tabela de ativos
		$q = "delete from jogador where id=" . $this->id . " limit 1";
		$this->banco->executar($q);
		// corrige posição dos que estavam atrás do eliminado
		$q = "update jogador set posicao=posicao-1 where ranking='" . $this->ranking . "' and posicao>" . $this->posicao;
		$this->banco->executar($q);
		return true;
	}
	
	/*
	 * Reativa jogador eliminado
	 * Move registro para tabela jogador_inativos
	 */
	public function reativa($id){
		
		// terminar
		
	/*	$q = "insert into jogador select * from jogador_inativos where id=" . $id . " limit 1";
		$this->banco->executar($q);
		// apaga registro do ex-eliminado da tabela de eliminados
		$q = "delete from jogador_inativos where id=" . $id . " limit 1";
		$this->banco->executar($q);
		// coloca-o como verde e em último
		$q = "update jogador set posicao=posicao-1 where ranking='" . $this->ranking . "' and posicao>" . $this->posicao;
		$this->banco->executar($q);*/
		return true;
	}
	
	/*
	 * Sobe uma posição
	 */
	public function sobe(){
		if ($this->posicao <= 1)
			return false;
		$p = $this->posicao - 1;
		$this->nova_posicao($p);
		return true;
	}
	
	/*
	 * Sobe uma posição
	*/
	public function desce(){
		$ranking = new ranking();
		if ($this->ranking == 'misto')
			$max = $ranking->misto_max;
		if ($this->ranking == 'feminino')
			$max = $ranking->feminino_max;
		if ($this->posicao >= $max)
			return false;
		$p = $this->posicao + 1;
		$this->nova_posicao($p);
		return true;
	}
	
	/*
	 * coloca ou tira do licenciamento
	 */
	public function licenciar(){
		$hoje = date('d-m-Y');
		if ($this->cor=='AMARELO')
			$q = "update jogador set cor='BRANCO',info='' where id=" . $this->id . " limit 1";
		else 
			$q = "update jogador set cor='AMARELO',info='Licenciado em $hoje' where id=" . $this->id . " limit 1"; 
		$this->banco->executar($q);
		return true;
	}
	
	/*
	 * coloca informação no campo info
	 */
	public function add_info($info){
		$q = "update jogador set info='$info' where id=" . $this->id . " limit 1";
		$this->banco->executar($q);
		return true;
	}
	
	/*
	 * desbonus, derruba 8 posicoes se tem 4 derrotas consecutivas
	*/
	public function desbonus(){
		if (($this->derrotas_consecutivas%4 == 0) && ($this->derrotas_consecutivas > 0)){
			$rodada = new rodada();
			$nome_rodada = 'R ' . $rodada->numero . '-' . $rodada->ano;
			$this->add_info($nome_rodada . "<br>" . $this->posicao . '-DESBONUS-' . ($this->posicao + 8));
			$this->nova_posicao($this->posicao + 8);
			return true;
		}
		return false;
	}
	
	/*
	 * soma jogo
	*/
	public function jogou(){
		$q = "update jogador set jogos=jogos+1 where id=" . $this->id . " limit 1";
		$this->banco->executar($q);
		return true;
	}
	
	/*
	 * faz log
	 */
	public function log_resultado($log){
		@mkdir("jogadores/" . $this->id);
		$arq = "jogadores/" . $this->id . "/resultados.xml";
		$fp = fopen($arq, 'a');
		fwrite($fp, $log);
		fclose($fp);
		return true;
	}
	
	/*
	 * devolve o id do jogador anterior ou posterior no posicionamento do ranking
	 */
	public function vizinho($lado){
		if ($lado == 'anterior'){
			$p = $this->posicao - 1;
			if ($p < 1)
				return $this->id;
			else 
				return $this->jogador_na_posicao($p,$this->ranking);
		}
	if ($lado == 'posterior'){
			$p = $this->posicao + 1;
			if ($p > $this->ultima_posicao($this->ranking))
				return $this->id;
			else 
				return $this->jogador_na_posicao($p,$this->ranking);
		}
	return false;
	}
	
	/*
	 * devolve o id do último jogador no posicionamento
	 */
	public function ultimo_id($r = 'misto'){
		$p = $this->ultima_posicao($r);
		$q = "select id from jogador where posicao=$p and ranking='$r'";
		$res = $this->banco->consultar($q);
		foreach ($res as $r){
			$id = $r['id'];
		}
		return $id;
	}
	
	/*
	 * devolve a ultima posicao ocupada
	 */
	public function ultima_posicao($r = 'misto'){
		$q = "select max(posicao) as ultima_posicao from jogador where ranking='$r'";
		$res = $this->banco->consultar($q);
		foreach ($res as $r){
			$p = $r['ultima_posicao'];
		}
		return $p;
	}
	
	/*
	 * apaga o registro do jogador - diferente do eliminar que mantém o registro armazenado 
	 */
	public function apagar(){
		// apaga registro do eliminado da tabela de ativos
		$q = "delete from jogador where id=" . $this->id . " limit 1";
		$this->banco->executar($q);
		// corrige posição dos que estavam atrás do eliminado
		$q = "update jogador set posicao=posicao-1 where ranking='" . $this->ranking . "' and posicao>" . $this->posicao;
		$this->banco->executar($q);
		return true;
	}
	
	/*
	 * deixa o jogador com possibilidade de marcar jogo caso seu jogo tenha sido apagado durante a montagem da rodada
	 */
	public function jogo_apagado($desafio){
		$q = "update jogador set jogo_agendado=0,pode_desafiar=$desafio where id=" . $this->id . " limit 1";
		$this->banco->executar($q);	
		return true;
	}
	
}

?>