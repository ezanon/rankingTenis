<?php
if (@$_SESSION['acesso_autorizado']){
	global $url;
	$url .= "?module=jogador&action=listar";
	header("Refresh: 0; URL=$url");
}
	
?>
<form id="form1" name="form1" method="post" action="">
  <label>loginn
  <input type="text" name="login" id="login" />
  </label>
  <p>
    <label>senha
    <input type="password" name="senha" id="senha" />
    </label>
  </p>
  <p>
    <label>
    <input type="submit" name="button" id="button" value="Login" />
    </label>
    <input name="module" type="hidden" id="module" value="login" />
    <input name="action" type="hidden" id="action" value="logar" />
  </p>
</form>