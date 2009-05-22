<?php

class Media_Captcha extends Scriptlet {
	public function display() {
		header("Content-type: image/png");
		$image = imagecreate(110, 20);
		imagecolorallocate($image, 0, 0, 0);
		$textcolor = imagecolorallocate($image, 255, 255, 255);
		$size=rand(10,16);
		$rotation=rand(-20,20);
		$fontsDirectory = dirname(__FILE__).'/fonts';
		$fonts = IO_Utils::getFilesFromFolder($fontsDirectory, array('ttf'));
		$randomFont = $fonts[rand(0, count($fonts) - 1)];
		imagettftext($image, $size, $rotation, 20, 20, $textcolor, $fontsDirectory.'/'.$randomFont, "Test");
		imagepng($image);
		imagedestroy($image);
	}
	
	public static function get() {
		return Router::get()->getModuleForRouteName('core')->getSubmodule('media_captcha');
	}
}

?>