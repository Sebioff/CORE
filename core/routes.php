<?php

/**
 * For handling default routes like core/reset
 * @author Patrick
 */
// TODO PWO: coding style; move to core/router (there are already too many files in core)
// Core_Routes_DefaultRoutes?
class Core_Routes extends Module {
	private $router=null;
	
	public function init() {
		parent::init();
		$this->router=Router::get();
		$params=$this->router->getParams();
		if(isset($params[0]['params'][0])&&$params[0]['params'][0]=='reset') {
			DB_Connection::get()->deleteTables();
			Core_MigrationsLoader::reset();
			$url=sprintf('http://%s', $_SERVER['SERVER_NAME']);
			Scriptlet::redirect($url);
		}
	}
}

?>