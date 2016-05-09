<?php $j = new jogador($_GET['id']); ?>
<h3>Editar Jogador</h3>

<p><a href=?module=admin&action=editar_jogador&id=<?php echo $j->vizinho('anterior'); ?>>Anterior</a> 
 - <a href=?module=admin&action=editar_jogador&id=<?php echo $j->vizinho('posterior'); ?>>Próximo</a></p>

<form id="form1" name="form1" method="post" action="?module=admin&action=alterar_jogador&id=<?php echo $j->id; ?>  ">
<table border="1">
<tr>
<td>Nome</td>
<td>
<input name="nome" type="text" id="nome" size="60" value="<?php echo $j->nome_completo; ?>" />
</td>
</tr>
<tr>
<td>Unidade</td>
<td><input name="unidade" type="text" id="unidade" size="60" value="<?php echo $j->unidade; ?>"  /></td>
</tr>
<tr>
<td>Categoria</td>
<td><label>
<select name="categoria" id="categoria">
	<option value="" <?php if ($j->categoria=='') echo 'selected="selected"'; ?>>-</option>
	<option value="Aluno" <?php if ($j->categoria=='Aluno') echo 'selected="selected"'; ?>>Aluno</option>
	<option value="Funcionario" <?php if ($j->categoria=='Funcionario') echo 'selected="selected"'; ?>>Funcionario</option>
	<option value="Docente" <?php if ($j->categoria=='Docente') echo 'selected="selected"'; ?>>Docente</option>
	<option value="ExAluno" <?php if ($j->categoria=='ExAluno') echo 'selected="selected"'; ?>>ExAluno</option>
	<option value="Dependente" <?php if ($j->categoria=='Dependente') echo 'selected="selected"'; ?>>Dependente</option>
</select>
</label></td>
</tr>
<tr>
<td>Sexo</td>
<td><label>
<select name="sexo" id="sexo">
	<option value="M" <?php if ($j->sexo=='M') echo 'selected="selected"'; ?>>Masculino</option>
	<option value="F" <?php if ($j->sexo=='F') echo 'selected="selected"'; ?>>Feminino</option>
</select>
</label></td>
</tr>
<tr>
<td>e-mail</td>
<td><input name="email" type="text" id="email" size="40" value="<?php echo $j->email; ?>" /></td>
</tr>
<tr>
<td>Tel Celular</td>
<td><input name="cel" type="text" id="cel" size="40" value="<?php echo $j->telcel; ?>" /></td>
</tr>
<tr>
<td>Tel Residencial</td>
<td><input name="res" type="text" id="res" size="40" value="<?php echo $j->telres; ?>" /></td>
</tr>
<tr>
<td>Tel Comercial</td>
<td><input name="cml" type="text" id="cml" size="40" value="<?php echo $j->telcml; ?>" /></td>
</tr>
<tr>
<td>&nbsp;</td>
<td>&nbsp;</td>
</tr>
<tr>
<td>Ranking</td>
<td>
<?php echo $j->ranking; ?>
</td>
</tr>
<tr>
<td>Cor</td>
<td>
<?php echo $j->cor; ?>
</td>
</tr>
<tr>
<td>Posição</td>
<td>
<?php echo $j->posicao; ?>
</td>
</tr>
<tr>
<td>Informação</td>
<td>
<input name="info" type="text" id="info" size="60" value="<?php echo $j->info; ?>" />
</td>
</tr>
<tr>
<td>Observações</td>
<td>
<input name="observacoes" type="text" id="observacoes" size="60" value="<?php echo $j->observacoes; ?>" />
</td>
</tr>
</table>
<p>


<table>
<tr>
	<th>Jogos</th>
	<th>Vit total</th>
	<th>Der total</th>
	<th>Vit consec</th>
	<th>Der consec</th>
	<th>WOs</th>
	<th>Pode desafiar?<br>0 é não ; 1 é sim</th>
</tr>
<tr>
	<td><input name="jogos" type="text" id="jogos" size="5" value="<?php echo $j->jogos; ?>" /></td>
	<td><input name="vitorias_total" type="text" id="vitorias_total" size="5" value="<?php echo $j->vitorias_total; ?>" /></td>
	<td><input name="derrotas_total" type="text" id="derrotas_total" size="5" value="<?php echo $j->derrotas_total; ?>" /></td>
	<td><input name="vitorias_consecutivas" type="text" id="vitorias_consecutivas" size="5" value="<?php echo $j->vitorias_consecutivas; ?>" /></td>
	<td><input name="derrotas_consecutivas" type="text" id="derrotas_consecutivas" size="5" value="<?php echo $j->derrotas_consecutivas; ?>" /></td>
	<td><input name="wo" type="text" id="wo" size="5" value="<?php echo $j->wo; ?>" /></td>
	<td><input name="pode_desafiar" type="text" id="pode_desafiar" size="5" value="<?php echo $j->pode_desafiar; ?>" /></td>
</tr>
</table>
<label>
<input type="submit" name="insere" id="insere" value="Alterar informações" />
</label>
</p>
</form>
<h3>Funções</h3>
<blockquote>
<?php 
// se a rodada está em andamento, não libera as funções que alteram o status do jogador
$rodada = new rodada();
if ($rodada->nova_rodada == 0){
	$altera_status = false;
}
else {
	$altera_status = true;
}

?>

<?php 

if (!$altera_status){
	echo "<p>Somente é possível alterar os status do jogador quando a rodada é encerrada.</p>
		  <p>Licenciar, Alterar cor, Alterar posição, Eliminar e Apagar bloqueados.</p>";
	echo "<!-- ";
}

?>

<ul>
<li><?php 
	// LICENCIAMENTO
	if ($altera_status){
		if ($j->cor=='AMARELO')
			echo "<p><a href=?module=admin&action=licencia_jogador&id=" . $j->id . ">Voltar da Licença</a></p>";
		else
			echo "<p><a href=?module=admin&action=licencia_jogador&id=" . $j->id . ">Licenciar</a></p>";
	}
	?>


<li><?php 
	// MUDANÇA DE COR
	if ($altera_status){
		if ($j->cor=='BRANCO')
			echo "<p><a href=?module=admin&action=jogador_cor&cor=VERDE&id=" . $j->id . ">Tornar VERDE</a></p>";
		if ($j->cor=='VERDE')
			echo "<p><a href=?module=admin&action=jogador_cor&cor=BRANCO&id=" . $j->id . ">Tornar BRANCO</a></p>";
		if ($j->cor=='AMARELO')
			echo "<p>Em licença não é possível alterar cor</p>";
	}
	?>
	
<li><?php 
	// MUDANÇA DE POSICAO
	if ($altera_status){
		echo "<p><form method=post action=?module=admin&action=jogador_posicao&id=" . $j->id . " >
					Colocar jogador na posição 
					<input type=text name=posicao />
					<input type=submit name=botao value=Alterar />
					</form></p>";
	}
	?>
	
<li><?php 
	// ELIMINACAO
	if ($altera_status){
		echo "<p><form method=post action=?module=admin&action=jogador_eliminar&id=" . $j->id . " >
					Eliminar jogador pelo motivo
					<input type=text name=motivo size=50 />
					<input type=submit name=botao value=Eliminar />
					</form></p>";
	}
	?>
<hr><br><br>
<li><?php 
	// APAGAR
	if ($altera_status){
		echo "<p><form method=post action=?module=admin&action=jogador_apagar&id=" . $j->id . " >
					<font color=red>CUIDADO !!! Apagar Jogador !!! CUIDADO<br>
					NÃO É POSSÍVEL RECUPERAR UM JOGADOR APAGADO.<br>
					USAR APENAS EM CASO DE ERRO.</font><br>
					<input type=submit name=botao value='CUIDADO !!! Apagar Jogador !!! CUIDADO' />
					</form></p>";
	}
	?>
	
</ul>	

<?php 

if (!$altera_status){
	echo "<!-- ";
}

?>

</blockquote>



