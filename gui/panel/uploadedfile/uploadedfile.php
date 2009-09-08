<?php

class GUI_Panel_UploadedFile extends GUI_Panel {
	private $path = null;
	
	public function __construct($name, $path, $caption, $title = '') {
		$this->path = $path;
		
		parent::__construct($name, $title);
		$this->params->caption = $caption;
	}
	
	public function init() {
		parent::init();
		
		if (!is_file($this->path))
			return;
		
		$this->setTemplate(dirname(__FILE__).'/uploadedfile.tpl');
		//TODO: use http://www.php.net/manual/en/ref.fileinfo.php with PHP 5.3
		// up to now we only can differ between image / no image
		$imagesize = getimagesize($this->path);
		// just to confuse attackers...
		$this->params->mimetype = $imagesize ? $imagesize['mime'] : time();
		$this->params->q = str_rot13(base64_encode($this->path));
		$this->params->c = md5($this->path);
		$this->params->m = str_rot13(base64_encode($this->params->mimetype));
	}
}
?>