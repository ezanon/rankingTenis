<?php

require('bootstrap.php');

function tira_acento($palavra){
	$palavra = preg_replace("/[ÂÁÀÃA]/", "A", $palavra);
	$palavra = preg_replace("/[ÊÉÈ]/", "E", $palavra);
	$palavra = preg_replace("/[ÍÌ]/", "I", $palavra);
	$palavra = preg_replace("/[ÔÓÒÕ]/", "O", $palavra);
	$palavra = preg_replace("/[ÜÚÙÛŨ]/", "U", $palavra);
	$palavra = preg_replace("/[Çç]/", "C", $palavra);
	$palavra = str_replace("Ñ", "N", $palavra);
	$palavra = str_replace("AA", "A", $palavra);
	$palavra = str_replace("EE", "E", $palavra);
	$palavra = str_replace("II", "I", $palavra);
	$palavra = str_replace("OO", "O", $palavra);
	$palavra = str_replace("UU", "U", $palavra);
	$palavra = str_replace("NN", "N", $palavra);
	return $palavra;
}

$banco = banco::instanciar();

switch ($_GET['id']){
/*
 * RODADA ATUAL SEM RESULTADOS EM EXCEL
 */
	case 1: 
		$rodada = new rodada();
		// cabecalho arquivo +
		// cabecalho jogos marcados
		$cabecalho = "<table>
				<tr>
					<td colspan=5><h1><i> $rodada->nome </i></h1></td>
				</tr>
				<tr><td>&nbsp;</td></tr>
				<tr>
					<td colspan=5><h3><i> $rodada->data </i></h3></td>
				</tr>
				<tr><td>&nbsp;</td></tr>
				<tr><td>&nbsp;</td></tr>
				";
		$agendados = '<tr><td colspan=5><h2>Jogos Agendados</h2></td></tr>
					<tr><td>&nbsp;</td></tr>
					<tr>
						<th>Horario</th>
						<th>Quadra</th>
						<th>Ranking</th>
						<th>Desafiante</th>
						<th>x</th>
						<th>Desafiado</th>
						<th>&nbsp;</th>
					</tr>';
		$possiveis = '<tr><td>&nbsp;</td></tr>
					<tr><td>&nbsp;</td></tr>
					<tr><td colspan=5><h2>Jogos Possiveis</h2></td></tr>
					<tr><td>&nbsp;</td></tr>
					<tr>
						<th>&nbsp;</th>
						<th>&nbsp;</th>
						<th>Ranking</th>
						<th>Desafiante</th>
						<th>x</th>
						<th>Desafiado</th>
						<th>&nbsp;</th>
					</tr>';
		// obtem jogos
		$q = "select id from rodada_atual order by confirmado desc, quadra, desafiante_posicao";
		$jogos = $banco->consultar($q);
		$i = 0;
		$ultima_quadra = '';
		foreach ($jogos as $j){
			$i++;
			$jogo = new jogo();
			$jogo->info($j['id'],'rodada_atual');
			if ($jogo->quadra==0)
				$quadra = NULL;
			else {
				$quadra = new quadra($jogo->quadra);
				if ($quadra->horario != $ultima_quadra)
					$agendados .= "<tr><td>&nbsp;</td></tr>";
				$ultima_quadra = $quadra->horario;
			}
			if ($jogo->desafio==1)
				$desafio = 'DESAFIO';
			else
				$desafio = '';
			$desafiante = new jogador($jogo->desafiante);
			$desafiado = new jogador($jogo->desafiado);
			// marcados e possiveis em variaveis strings separadas
			if ($jogo->confirmado==1) { // é com agendamento
				$agendados.="<tr>
								<td>" . $quadra->horario . "</td>
								<td>" . $quadra->quadra . "</td>
								<td>" . $jogo->ranking. "</td>
								<td>" . tira_acento($desafiante->nome_pos) . "</td>
								<td>x</td>
								<td>" . tira_acento($desafiado->nome_pos) . "</td>
								<td>$desafio</td>
							 </tr>";
			}
			else { // sem agendamento... jogo possivel
				$possiveis.="<tr>
								<td>&nbsp;</td>
								<td>&nbsp;</td>
								<td>" . $jogo->ranking. "</td>
								<td>" . tira_acento($desafiante->nome_pos) . "</td>
								<td>x</td>
								<td>" . tira_acento($desafiado->nome_pos) . "</td>
								<td>&nbsp;</td>
							</tr>";
			}
		}
		$str = $cabecalho . $agendados . $possiveis . "</table>";
		$arq = 'Rodada-' . $rodada->numero . '-' . $rodada->ano . '_atual_sem_resultados.xls';
		header("Content-type: application/vnd.ms-excel; charset=iso-8859-1");
		header("Content-type: application/force-download");
		header("Content-Disposition: attachment; filename=$arq");
		header("Pragma: no-cache");
		echo $str;
		break;
		
/*
 * RODADA ATUAL COM RESULTADOS EM EXCEL
*/
		case 2:
		$rodada = new rodada(); // dados da ultima rodada
		// cabecalho arquivo +
		// cabecalho jogos marcados
		$cabecalho = "<table>
						<tr>
						<td colspan=5><h1><i> $rodada->nome </i></h1></td>
						</tr>
						<tr><td>&nbsp;</td></tr>
						<tr>
						<td colspan=5><h3><i> $rodada->data </i></h3></td>
						</tr>
						<tr><td>&nbsp;</td></tr>
						<tr><td>&nbsp;</td></tr>
						";
		$agendados = '<tr><td colspan=5><h2>Jogos Agendados</h2></td></tr>
						<tr><td>&nbsp;</td></tr>
						<tr>
						<th>Horario</th>
						<th>Quadra</th>
						<th>Desafiante</th>
						<th>&nbsp;</th>
						<th>x</th>
						<th>&nbsp;</th>
						<th>Desafiado</th>
						<th>Parciais</th>
						<th>&nbsp;</th>
						</tr>';
		$possiveis = '<tr><td>&nbsp;</td></tr>
						<tr><td>&nbsp;</td></tr>
						<tr><td colspan=5><h2>Jogos Possiveis</h2></td></tr>
						<tr><td>&nbsp;</td></tr>
						<tr>
						<th>&nbsp;</th>
						<th>&nbsp;</th>
						<th>Desafiante</th>
						<th>&nbsp;</th>
						<th>x</th>
						<th>&nbsp;</th>
						<th>Desafiado</th>
						<th>Parciais</th>
						<th>&nbsp;</th>
						</tr>';
		// obtem jogos
		$q = "select id from rodada_atual order by confirmado desc, quadra, ranking desc, desafiante_posicao";
		$jogos = $banco->consultar($q);
		$i = 0;
		$ultima_quadra = '';
		foreach ($jogos as $j){
			$i++;
			$jogo = new jogo();
			$jogo->info($j['id'],'rodada_atual');
			if ($jogo->quadra==0)
				$quadra = NULL;
			else {
				$quadra = new quadra($jogo->quadra);
				if ($quadra->horario != $ultima_quadra)
					$agendados .= "<tr><td>&nbsp;</td></tr>";
				$ultima_quadra = $quadra->horario;
			}
			if ($jogo->desafio==1)
				$desafio = 'DESAFIO';
			else
				$desafio = '';
			$desafiante = new jogador($jogo->desafiante);
			$desafiado = new jogador($jogo->desafiado);
			// marcados e possiveis em variaveis strings separadas
			$parciais = $jogo->parciais;
			if ($jogo->cancelado==1)
				$parciais = 'ADIADO';
			if ($jogo->woduplo==1)
				$parciais = 'WO DUPLO';
			$set1 = $jogo->desafiante_sets;
			$set2 = $jogo->desafiado_sets;
		if (($set1 == 0) && ($set2 == 0) && ($set1 != 'W') && ($set2 != 'W'))  {
				$set1 = '&nbsp;'; 
				$set2 = '&nbsp;';
			}
			if ($jogo->confirmado==1) { // é com agendamento
				$agendados.="<tr>
								<td>" . $quadra->horario . "</td>
								<td>" . $quadra->quadra . "</td>
								<td>" . tira_acento($desafiante->nome_completo) . "(" . $jogo->desafiante_posicao . ")</td>
								<td><b>" . $set1 . "</b></td>
								<td>x</td>
								<td><b>" . $set2 . "</b></td>
								<td>" . tira_acento($desafiado->nome_completo) . "(" . $jogo->desafiado_posicao . ")</td>
								<td>" . $parciais . "</td>
								<td>" . $desafio . "</td>
							 </tr>";
			}
			else { // sem agendamento... jogo possivel
				$possiveis.="<tr>
								<td>$i</td>
								<td>&nbsp;</td>
								<td>" . tira_acento($desafiante->nome_completo) . "(" . $jogo->desafiante_posicao . ")</td>
								<td><b>" . $set1 . "</b></td>
								<td>x</td>
								<td><b>" . $set2 . "</b></td>
								<td>" . tira_acento($desafiado->nome_completo) . "(" . $jogo->desafiado_posicao . ")</td>
								<td>" . $parciais . "</td>
								<td>&nbsp;</td>
							</tr>";
			}
		}
		$str = $cabecalho . $agendados . $possiveis . "</table>";
		$arq = 'Rodada-' . $rodada->numero . '-' . $rodada->ano . '_atual_com_resultados.xls';
		header("Content-type: application/vnd.ms-excel");
		header("Content-type: application/force-download");
		header("Content-Disposition: attachment; filename=$arq");
		header("Pragma: no-cache");
		echo $str;
		break;
		

/*
 * RESULTADOS DA ULTIMA RODADA EM EXCEL
 */
	case 3:
		$rodada = new rodada(2); // dados da ultima rodada
		// cabecalho arquivo +
		// cabecalho jogos marcados
		$cabecalho = "<table>
						<tr>
						<td colspan=5><h1><i> $rodada->nome </i></h1></td>
						</tr>
						<tr><td>&nbsp;</td></tr>
						<tr>
						<td colspan=5><h3><i> $rodada->data </i></h3></td>
						</tr>
						<tr><td>&nbsp;</td></tr>
						<tr><td>&nbsp;</td></tr>
						";
		$agendados = '<tr><td colspan=5><h2>Jogos Agendados</h2></td></tr>
						<tr><td>&nbsp;</td></tr>
						<tr>
						<th>Horario</th>
						<th>Quadra</th>
						<th>Desafiante</th>
						<th>&nbsp;</th>
						<th>x</th>
						<th>&nbsp;</th>
						<th>Desafiado</th>
						<th>Parciais</th>
						<th>&nbsp;</th>
						</tr>';
		$possiveis = '<tr><td>&nbsp;</td></tr>
						<tr><td>&nbsp;</td></tr>
						<tr><td colspan=5><h2>Jogos Possiveis</h2></td></tr>
						<tr><td>&nbsp;</td></tr>
						<tr>
						<th>&nbsp;</th>
						<th>&nbsp;</th>
						<th>Desafiante</th>
						<th>&nbsp;</th>
						<th>x</th>
						<th>&nbsp;</th>
						<th>Desafiado</th>
						<th>Parciais</th>
						<th>&nbsp;</th>
						</tr>';
		// obtem jogos
		$q = "select id from ultima_rodada order by confirmado desc, quadra, ranking desc, desafiante_posicao";
		$jogos = $banco->consultar($q);
		$i = 0;
		$ultima_quadra = '';
		foreach ($jogos as $j){
			$i++;
			$jogo = new jogo();
			$jogo->info($j['id'],'ultima_rodada');
			if ($jogo->quadra==0)
				$quadra = NULL;
			else {
				$quadra = new quadra($jogo->quadra);
				if ($quadra->horario != $ultima_quadra)
					$agendados .= "<tr><td>&nbsp;</td></tr>";
				$ultima_quadra = $quadra->horario;
			}
			if ($jogo->desafio==1)
				$desafio = 'DESAFIO';
			else
				$desafio = '';
			$desafiante = new jogador($jogo->desafiante);
			$desafiado = new jogador($jogo->desafiado);
			// marcados e possiveis em variaveis strings separadas
			$parciais = $jogo->parciais;
			if ($jogo->cancelado==1)
				$parciais = 'ADIADO';
			if ($jogo->woduplo==1)
				$parciais = 'WO DUPLO';
			$set1 = $jogo->desafiante_sets;
			$set2 = $jogo->desafiado_sets;
			if (($set1 == 0) && ($set2 == 0) && ($set1 != 'W') && ($set2 != 'W'))  {
				$set1 = '&nbsp;'; 
				$set2 = '&nbsp;';
			}
			if ($jogo->confirmado==1) { // é com agendamento
				$agendados.="<tr>
								<td>" . $quadra->horario . "</td>
								<td>" . $quadra->quadra . "</td>
								<td>" . tira_acento($desafiante->nome_completo) . "(" . $jogo->desafiante_posicao . ")</td>
								<td><b>" . $set1 . "</b></td>
								<td>x</td>
								<td><b>" . $set2 . "</b></td>
								<td>" . tira_acento($desafiado->nome_completo) . "(" . $jogo->desafiado_posicao . ")</td>
								<td>" . $parciais . "</td>
								<td>" . $desafio . "</td>
							 </tr>";
			}
			else { // sem agendamento... jogo possivel
				$possiveis.="<tr>
								<td>&nbsp;</td>
								<td>&nbsp;</td>
								<td>" . tira_acento($desafiante->nome_completo) . "(" . $jogo->desafiante_posicao . ")</td>
								<td><b>" . $set1 . "</b></td>
								<td>x</td>
								<td><b>" . $set2 . "</b></td>
								<td>" . tira_acento($desafiado->nome_completo) . "(" . $jogo->desafiado_posicao . ")</td>
								<td>" . $parciais . "</td>
								<td>&nbsp;</td>
							</tr>";
			}
		}
		$str = $cabecalho . $agendados . $possiveis . "</table>";
		$arq = 'Rodada-' . $rodada->numero . '-' . $rodada->ano . '.xls';
		header("Content-type: application/vnd.ms-excel");
		header("Content-type: application/force-download");
		header("Content-Disposition: attachment; filename=$arq");
		header("Pragma: no-cache");
		echo $str;
		break;
/*
 * RANKING ATUAL EM EXCEL
*/
	case 4:
		$hoje = date('d/m/Y');
		$cabecalho = "<table>
			<tr>
			<td colspan=12><h2><i> $hoje </i></h2></td>
			</tr>
			";
		$cab_misto = '<tr><td>&nbsp;</td></tr><tr><td>&nbsp;</td></tr>
		   <tr><td colspan=12><h2>RANKING MISTO</h2></td></tr>
			<tr><td>&nbsp;</td></tr>
			<tr>
				<th>Posicao</th>
				<th>Nome</th>
				<th>Cor</th>
				<th>Tel. Res.</th>
				<th>Tel. Cel.</th>
				<th>Jogos</th>
				<th>Vitorias</th>
				<th>Derrotas</th>
				<th>Vit. Cons.</th>
				<th>Der. Cons.</th>
				<th>WO</th>
				<th>Informacao</th>
			</tr>';
		$cab_feminino = '<tr><td>&nbsp;</td></tr><tr><td>&nbsp;</td></tr>
		    <tr><td colspan=12><h2>RANKING FEMININO</h2></td></tr>
			<tr><td>&nbsp;</td></tr>
			<tr>
				<th>Posicao</th>
				<th>Nome</th>
				<th>Cor</th>
				<th>Tel. Res.</th>
				<th>Tel. Cel.</th>
				<th>Jogos</th>
				<th>Vitorias</th>
				<th>Derrotas</th>
				<th>Vit. Cons.</th>
				<th>Der. Cons.</th>
				<th>WO</th>
				<th>Informacao</th>
			</tr>';
		$cab_inativos = '<tr><td>&nbsp;</td></tr><tr><td>&nbsp;</td></tr>
				<tr><td colspan=12><h2>ELIMINADOS</h2></td></tr>
				<tr><td>&nbsp;</td></tr>
				<tr>
					<th>&nbsp;</th>
					<th>Nome</th>
					<th colspan=10>Informacao</th>
				</tr>';
		$ranking = array('misto','feminino');
		foreach ($ranking as $r){
			$q = "select id from jogador where ranking='$r' order by posicao";
			$jogadores = $banco->consultar($q);
			$$r = '';
			foreach ($jogadores as $j){
				$jogador = new jogador($j['id']);
				$wo = '';
				if ($jogador->wo > 0) $wo = $jogador->wo;
				$$r.= "<tr>
						<td>" . $jogador->posicao . "</td>
						<td>" . tira_acento($jogador->nome_completo) . "</td>
						<td>" . $jogador->cor . "</td>
						<td>" . $jogador->telres . "</td>
						<td>" . $jogador->telcel . "</td>
						<td>" . $jogador->jogos . "</td>
						<td>" . $jogador->vitorias_total . "</td>
						<td>" . $jogador->derrotas_total . "</td>
						<td>" . $jogador->vitorias_consecutivas . "</td>
						<td>" . $jogador->derrotas_consecutivas . "</td>
						<td>" . $wo . "</td>
						<td>" . $jogador->info . "</td>
					   </tr>";
			}
		}
		// obtem inativos
		$q = "select id,nome_completo,info from jogador_inativos order by id desc";
		$jogadores = $banco->consultar($q);
		$inativos = '';
		foreach ($jogadores as $j){
			$inativos.= "<tr>
							<td>" . $j['id'] . "</td>
							<td>" . tira_acento($j['nome_completo']) . "</td>
							<td colspan=10>" . $j['info'] . "</td>
						</tr>";
		}
		
		$str = $cabecalho . $cab_misto . $misto . $cab_feminino . $feminino . $cab_inativos . $inativos . '</table>';
		$hoje = date('Y-m-d');
		$arq = 'Ranking-' . $hoje . '.xls';
		header("Content-type: application/vnd.ms-excel");
		header("Content-type: application/force-download");
		header("Content-Disposition: attachment; filename=$arq");
		header("Pragma: no-cache");
		echo $str;
		break;
		
/*
 * SUMULAS DA RODADA ATUAL (JOGOS AGENDADOS)
*/
	case 9:	
		$sumula = new sumula();
		$sumula->obter_pdf();
		$sumula = NULL;
		break;	
	
	
	default: echo 'Arquivo indisponivel.';
} // FIM DO SWITCH



?>