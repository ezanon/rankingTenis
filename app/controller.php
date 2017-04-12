<?php

class controller{
	
	public function __construct(){
		$view = new view;
		if ( isset($_REQUEST['action']) && isset($_REQUEST['module'])) {
			$action = $_REQUEST['action'];
			$module = $_REQUEST['module'];
			$model = new $module();
			if (isset($_REQUEST['id']))
				$data = $model->$action($_REQUEST['id']);
			else
				$data = $model->$action();
			$view->load($module,$action,$data);
		}
		else {
			$view->load('','home');
		}
		return true;
	}
	
	
}

?>