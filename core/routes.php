<?php

/**
 * For handling default routes like core/reset
 * @author Patrick
 */
class Core_Routes extends Module {
	private $router=null;
	
	public function init() {
		parent::init();
		$this->router=Router::get();
		$params=$this->router->getParams();
		if(isset($params[0]['param'])&&$params[0]['param']=='reset') {
			Core_MigrationsLoader::reset();
		}
	}
}

?>