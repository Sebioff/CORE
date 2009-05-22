<?php

class GUI_Panel_Image extends GUI_Panel {
	private $url = '';
	private $description = '';
	
	public function __construct($name, $url, $description = '', $title = '') {
		parent::__construct($name, $title);
		
		if ($title && !$description)
			$description = $title;
		$this->setDescription($description);
		$this->setURL($url);
		$this->setTemplate(dirname(__FILE__).'/image.tpl');
	}

	// GETTERS / SETTERS -------------------------------------------------------
	public function getURL() {
		return $this->url;
	}
	
	public function setURL($url) {
		$this->url = $url;
	}
	
	public function getDescription() {
		return $this->description;
	}
	
	public function setDescription($description) {
		$this->description = $description;
	}
}

?>