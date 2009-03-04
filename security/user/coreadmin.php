<?php
class SECURITY_User_Coreadmin extends SECURITY_USER_Dummy {
	public function __construct() {
		$this->test = 'cadmin';
	}
}
?>