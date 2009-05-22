<?php

class GUI_Control_Captcha extends GUI_Control {
	public function __construct($name, $title) {
		parent::__construct($name, null, $title);
	}
	
	public function init() {
		$this->addPanel(new GUI_Panel_Image('image', Media_Captcha::get()->getUrl(), 'Captcha'));
	}
}

?>