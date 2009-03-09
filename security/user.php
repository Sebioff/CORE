<?php
class Security_User extends Security_User_Dummy {
	private $options = array();
	public function addOptionClass(Security_User_Dummy $class) {
		$this->options[(String)$class] = $class;
		$this->updateRights();
	}
	public function updateRights() {
		if (count($this->options) > 0) {
			foreach ($this->options AS $path => $o) {
				$this->test = $o->test;
			}
		} else 
			throw Core_Exception('No Option Classes found');
	}
}
?>