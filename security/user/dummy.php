<?php
//Blubb
abstract class Security_User_Dummy {
	public $test;
	public $rights = array();
	public function __toString() {
		list(, , $name) = explode('_', get_class($this));
		return $name;
	}
}

?>