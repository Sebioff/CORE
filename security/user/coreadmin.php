<?php

class Security_User_Coreadmin extends Security_User_Dummy {
	// TODO ooo: what is an empty constructor needed for...?
	// don't know the needed rights now, but they must be set by the constructor
	public function __construct() {
		$this->rights['super_geheimes_coreadmin_recht'] = true;
	}
}

?>