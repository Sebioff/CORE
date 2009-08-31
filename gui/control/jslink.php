<?php

class GUI_Control_JsLink extends GUI_Control_Link {
	
	public function __construct($name, $caption, $js, $fallbackurl = '#', $title = '') {
		parent::__construct($name, $caption, $fallbackurl, $title);
		
		$this->setAttribute('onclick', $js);
	}
	
	public function setFallbackUrl($url) {
		$this->setUrl($url);
	}
	
	public function getFallbackUrl() {
		return $this->getUrl();
	}
	
	public function setJs($js) {
		$this->setAttribute('onclick', $js);
	}
}
?>