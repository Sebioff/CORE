<?php

/**
 * Control to create a link,
 * which performs a JavaScript onclick event before entering the given url.
 */
class GUI_Control_JsLink extends GUI_Control_Link {
	public function __construct($name, $caption, $js, $url = '#', $title = '') {
		parent::__construct($name, $caption, $url, $title);
		
		$this->setJs($js);
	}
	
	public function setJs($js) {
		$this->setAttribute('onclick', $js);
	}
}

?>