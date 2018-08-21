<?php

class controller2{
	
	public function __construct(){
		$view = new view2;
		if ( isset($_REQUEST['action']) && isset($_REQUEST['module'])) {
			$action = filter_input(INPUT_GET, 'action');
			$module = filter_input(INPUT_GET, 'module');
			$model = new $module();
			if (isset($_REQUEST['id']))
				$data = $model->$action(filter_input(INPUT_GET, 'id'));
			else
				$data = $model->$action();
			$view->load($module,$action,$data);
		}
		return true;
	}
	
	
}

?>