<?php

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
		$this->addPanel(new GUI_Panel_Image('image', Media_Captcha::get()->getUrl(), 'Captcha'));
		$this->addPanel($input = new GUI_Control_TextBox('input'));
		$input->addValidator(new GUI_Validator_Mandatory());
	}
	
	protected function validate() {
		parent::validate();
		
		$captchaValue = $this->getValue();
		$inputValue = Text::toUpperCase($this->input->getValue());
		$correct = true;
		for ($i = 0; $i < 4; $i++) {
			if ($inputValue[$i] != $captchaValue[$i]) {
				$correct = false;
				break;
			}
		}
		if (Text::length($inputValue) != 4)
			$correct = false;
		
		if (!$correct)
			$this->errors[] = 'Falsche Eingabe';
		
		return $this->errors;
	}
	
	public function getValue() {
		return Media_Captcha::get()->getValue();
	}
	
}

?>