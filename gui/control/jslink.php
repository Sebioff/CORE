<?php

class GUI_Control_JsLink extends GUI_Control_Link {
	public function __construct($name, $caption, $js, $url = '#', $title = '') {
		parent::__construct($name, $caption, $url, $title);
		
		$this->setAttribute('onclick', $js);
	}
	
	public function setJs($js) {
		$this->setAttribute('onclick', $js);
	}
}

?>