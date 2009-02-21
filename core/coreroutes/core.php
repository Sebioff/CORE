<?php

/**
 * For handling default routes like core/reset
 * @author Patrick
 */
class CoreRoutes_Core extends Module {
	public function init() {
		parent::init();
		
		Router::get()->addStaticRoute('core_css', './../../CORE/www/css');
  		Router::get()->addStaticRoute('core_js', './../../CORE/www/js');
		
		$params = Router::get()->getParams();
		if(isset($params[0]['params'][0]) && $params[0]['params'][0] == 'reset') {
			DB_Connection::get()->deleteTables();
			Core_MigrationsLoader::reset();
			$url = sprintf('http://%s', $_SERVER['SERVER_NAME']);
			Scriptlet::redirect($url);
		}
	}
}

?>