<?php

/**
 * For handling default routes like core/reset
 * @author Patrick
 */
class CoreRoutes_Core extends Module {
	public function __construct($name) {
		parent::__construct($name);

		// Project reset
		if (Environment::getCurrentEnvironment() == Environment::DEVELOPMENT) {
			$this->addSubmodule(new CoreRoutes_Reset('reset'));
		}
		
		// Other routes
		$this->addSubmodule(new Media_Captcha('media_captcha'));
	}
}

?>