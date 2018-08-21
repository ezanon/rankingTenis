<?php

class view2 {
	
	public function load($module,$action,$data=''){
		include("views/$module/$action.tpl.php");
		return true;
	}

}

?>