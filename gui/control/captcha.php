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
 * Control that displays a captcha and an input field; validates that input
 * is equal to displayed captcha.
 */
class GUI_Control_Captcha extends GUI_Control {
	public function __construct($name, $title) {
		parent::__construct($name, null, $title);
	}

	public function init() {
		parent::init();

		$this->setTemplate(dirname(__FILE__).'/captcha.tpl');
		$this->addPanel(new GUI_Panel_Image('image', Media_Captcha::get()->getUrl().'?cb='.time(), 'Captcha'));
		$this->addPanel($input = new GUI_Control_TextBox('input'));
		$input->addValidator(new GUI_Validator_Mandatory());
		$input->addValidator(new GUI_Validator_MaxLength(4));
		$this->addPanel(new GUI_Control_Link('reload', 'Neu laden', $this->getModule()->getUrl(), 'Neu laden'));
	}
	
	public function afterInit() {
		parent::afterInit();
		
		$this->reload->setAttribute('onclick', sprintf('$(\'#%s .core_gui_captcha_image img\').attr(\'src\', \'%s?cb=\' + new Date().getTime()); return false;', $this->getID(), Media_Captcha::get()->getUrl()));
	}

	protected function validate() {
		parent::validate();

		$captchaValue = $this->getValue();
		$inputValue = Text::toUpperCase($this->input->getValue());
		$correct = true;
		
		if (Text::length($inputValue) != 4) {
			$correct = false;
		}
		else {
			for ($i = 0; $i < 4; $i++) {
				if ($inputValue[$i] != $captchaValue[$i]) {
					$correct = false;
					break;
				}
			}
		}
		
		if (!$correct)
			$this->errors[] = 'Falsche Eingabe';

		return $this->errors;
	}

	public function getValue() {
		return Media_Captcha::get()->getValue();
	}
}

?>