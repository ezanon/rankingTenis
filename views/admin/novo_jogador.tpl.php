<h3>Novo Jogador</h3>
<form id="form1" name="form1" method="post" action="?module=admin&action=add_jogador">
<table border="1">
<tr>
<td>Nome</td>
<td>
<input name="nome" type="text" id="nome" size="60" />
</td>
</tr>
<tr>
<td>Unidade</td>
<td><input name="unidade" type="text" id="unidade" size="60" /></td>
</tr>
<tr>
<td>Categoria</td>
<td><label>
<select name="categoria" id="categoria">
<option value="Aluno" selected="selected">Aluno</option>
<option value="Funcionario">Funcionario</option>
<option value="Docente">Docente</option>
<option value="ExAluno">ExAluno</option>
<option value="Dependente">Dependente</option>
</select>
</label></td>
</tr>
<tr>
<td>Sexo</td>
<td><label>
<select name="sexo" id="sexo">
<option value="M" selected="selected">Masculino</option>
<option value="F">Feminino</option>
</select>
</label></td>
</tr>
<tr>
<td>e-mail</td>
<td><input name="email" type="text" id="email" size="40" /></td>
</tr>
<tr>
<td>Tel Celular</td>
<td><input name="cel" type="text" id="cel" size="40" /></td>
</tr>
<tr>
<td>Tel Residencial</td>
<td><input name="res" type="text" id="res" size="40" /></td>
</tr>
<tr>
<td>Tel Comercial</td>
<td><input name="cml" type="text" id="cml" size="40" /></td>
</tr>
<tr>
<td>&nbsp;</td>
<td>&nbsp;</td>
</tr>
<tr>
<td>Ranking</td>
<td><label>
<select name="ranking" id="ranking">
<option value="misto">Misto</option>
<option value="feminino">Feminino</option>
</select>
</label></td>
</tr>
<tr>
<td>Cor Inicial</td>
<td><label>
<select name="cor" id="cor">
<option value="VERDE">Verde</option>
<option value="BRANCO">Branco</option>
<option value="AMARELO">Amarelo</option>
</select>
</label></td>
</tr>
<tr>
<td>Posição</td>
<td><input type="text" name="posicao" id="posicao" size="40" value='<?php echo $data; ?>' /></td>
</tr>
</table>
<p>
<label>
<input type="submit" name="insere" id="insere" value="Registrar novo jogador" />
</label>
</p>
</form>