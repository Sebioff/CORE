<?php

/**
 * For handling default routes like core/reset
 * @author Patrick
 */
class CoreRoutes_Core extends Module {
	public function __construct($name) {
		parent::__construct($name);

		if (Environment::getCurrentEnvironment() == Environment::DEVELOPMENT) {
			$this->addSubmodule(new CoreRoutes_Reset('reset'));
		}
	}
}

?>