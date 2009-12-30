<?php

class GUI_Panel_Plot_Image extends Scriptlet {

	public function display() {
		header('Content-type: image/png');
		$image = imagecreatefrompng(dirname(__FILE__).'/'.$this->getParam('time'));
		imagepng($image);
		imagedestroy($image);
		$file = new IO_File(dirname(__FILE__).'/'.$this->getParam('time'));
		$file->delete();
	}
}
?>