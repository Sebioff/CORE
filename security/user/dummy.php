<?php
//Blubb
abstract class Security_User_Dummy {
	public $test;
	public $right_delete_alliance = false;
	public function __toString() {
		list(, , $name) = explode('_', get_class($this));
		return $name;
	}
}

?>