<?php
class Security_User extends Security_User_Dummy {
	private $options = array();
	public function addOptionClass(Security_User_Dummy $class) {
		$this->options[(String)$class] = $class;
		$this->updateRights();
	}
	public function getPossibleRights() {
		return array_keys($this->rights);
	}
	public function getRight($r) {
		return isset($this->rights[$r]) ? $this->rights[$r] : false;
	}
	public function updateRights() {
		if (count($this->options) > 0) {
			foreach ($this->options AS $o) {
				array_merge($this->rights, $o->rights);
			}
		} else 
			throw Core_Exception('No Option Classes found');
	}
}
?>