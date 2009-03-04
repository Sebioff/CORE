<?php
class SECURITY_User {
	private $options = array();
	public function addOptionClass(SECURITY_User_Dummy $class) {
		$this->options[(String)$class] = $class;
	}
	public function getOptionClasses() {
		return $this->options;
	}
}
?>