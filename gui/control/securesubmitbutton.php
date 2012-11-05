<?php

/**
 * @package CORE PHP Framework
 * @copyright Copyright (C) 2012 Sebastian Mayer, Andreas Sicking, Andre Jährling
 * @license GNU/GPL, see license.txt
 * This file is part of CORE PHP Framework.
 *
 * CORE PHP Framework is free software: you can redistribute it and/or modify it
 * under the terms of the GNU General Public License as published by the Free Software
 * Foundation, either version 3 of the License, or (at your option) any later version.
 *
 * CORE PHP Framework is distributed in the hope that it will be useful, but WITHOUT
 * ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS
 * FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with CORE PHP Framework. If not, see <http://www.gnu.org/licenses/>.
 */

/**
 * Submitbutton that is secured against being triggered by post-data being send
 * to the server without the user wanting it (aka Cross-Site Request Forgery, CSRF).
 */
class GUI_Control_SecureSubmitButton extends GUI_Control_SubmitButton {
	// CONSTRUCTORS ------------------------------------------------------------
	public function __construct($name, $caption = '') {
		parent::__construct($name, $caption);
		
		$this->setTemplate(dirname(__FILE__).'/securesubmitbutton.tpl');
	}
	
	// CUSTOM METHODS ----------------------------------------------------------
	public function afterInit() {
		parent::afterInit();
		
		if (!isset($_SESSION[get_class($this)][$this->getID()]))
			$_SESSION[get_class($this)][$this->getID()] = md5(time().$this->getID().$this->getValue());
		$this->addPanel(new GUI_Control_HiddenBox('token', $_SESSION[get_class($this)][$this->getID()]));
	}
	
	protected function validate() {
		$errors = parent::validate();

		if (!isset($_SESSION[get_class($this)][$this->getID()]) || $_SESSION[get_class($this)][$this->getID()] != $this->token->getValue())
			$errors[] = 'Eventuell nicht richtig per Browser versendet';
			
		return $errors;
	}
}

?>