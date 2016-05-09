<?php

class view {
	
	public function load($module,$action,$data=''){
		include('views/header.tpl.php');
		$menu = new menu;
		echo $menu->imprimir();
		include("views/$module/$action.tpl.php");
		include('views/footer.tpl.php');
		return true;
	}

}

?>