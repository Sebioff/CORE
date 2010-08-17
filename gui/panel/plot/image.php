<?php

class GUI_Panel_Plot_Image extends Scriptlet {
	const SCRIPTLET_NAME = 'plotimage';
	
	public function __construct() {
		parent::__construct(self::SCRIPTLET_NAME);
	}
	
	public function display() {
		header('Content-type: image/png');
		$filename = ini_get('upload_tmp_dir').DS.$this->getParam('img');
		$image = imagecreatefrompng($filename);
		imagepng($image);
		imagedestroy($image);
		$file = new IO_File($filename);
		$file->delete();
	}
}

?>