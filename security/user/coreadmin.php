<?php
class Security_User_Coreadmin extends Security_User_Dummy {
	public function __construct() {
		$this->test = 'cadmin';
	}
}
?>