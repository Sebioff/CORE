<?php

/**
 * Submitbutton that is secured against being triggered by post-data being send
 * to the server without the user wanting it.
 */
class GUI_Control_SecureSubmitButton extends GUI_Control_SubmitButton {
	// CONSTRUCTORS ------------------------------------------------------------
	public function __construct($name, $caption = '') {
		parent::__construct($name, $caption);
		
		$this->setTemplate(dirname(__FILE__).'/securesubmitbutton.tpl');
	}
	
	// CUSTOM METHODS ----------------------------------------------------------
	public function init() {
		parent::init();
		
		if (!isset($_SESSION[get_class($this)][$this->getName()]))
			$_SESSION[get_class($this)][$this->getName()] = md5(time().$this->getName().$this->getValue());
		$this->addPanel(new GUI_Control_HiddenBox('token', $_SESSION[get_class($this)][$this->getName()]));
	}
	
	protected function validate() {
		$errors = parent::validate();

		if (!isset($_SESSION[get_class($this)][$this->getName()]) || $_SESSION[get_class($this)][$this->getName()] != $this->token->getValue())
			$errors[] = 'Eventuell nicht richtig per Browser versendet';
		
		return $errors;
	}
}

?>