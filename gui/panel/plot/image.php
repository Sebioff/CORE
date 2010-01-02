<?php

class GUI_Panel_Plot_Image extends Scriptlet {

	public function display() {
		header('Content-type: image/png');
		$image = imagecreatefrompng(ini_get('upload_tmp_dir').'/'.$this->getParam('time'));
		imagepng($image);
		imagedestroy($image);
		$file = new IO_File(ini_get('upload_tmp_dir').'/'.$this->getParam('time'));
		$file->delete();
	}
}
?>