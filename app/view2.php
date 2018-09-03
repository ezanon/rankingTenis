<?php

class view2 {
	
	public function load($module,$action,$data=''){
            include('views/header2.tpl.php');
            include("views/$module/$action.tpl.php");
            include('views/footer2.tpl.php');
            return true;
	}

}

?>