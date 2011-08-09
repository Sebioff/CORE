<?php

class GUI_Panel_Plot_Image extends Scriptlet {
	const SCRIPTLET_NAME = 'plotimage';
	
	public function __construct() {
		parent::__construct(self::SCRIPTLET_NAME);
	}
	
	public function display() {
		header('Content-type: image/png');
		$filename = System::getTemporaryDirectory().DS.$this->getParam('img');
		$file = new IO_File($filename);
		if ($file->exists()) {
			$image = imagecreatefrompng($filename);
			imagepng($image);
			imagedestroy($image);
			$file->delete();
		}
		else {
			// TODO throw exception?
			$image = imagecreate(110, 20);
			$backgroundColor = imagecolorallocate($image, 0, 0, 0);
			$textColor = imagecolorallocate($image, 255, 0, 0);
			imagestring($image, 1, 5, 5,  'Couldn\'t load image', $textColor);
			imagepng($image);
			imagedestroy($image);
		}
	}
}

?>