<?php

/**
 * This scriptlet generates an image containing some characters (Captcha)
 */
class Media_Captcha extends Scriptlet {
	protected function generateValue() {
		$value = array();
		for ($i = 0; $i < 4; $i++) {
			if (rand(0, 1))
				$value[$i] = rand(2, 9);
			else
				$value[$i] = chr(rand(65, 90));
		}
		$_SESSION['captcha'] = $value;
	}
	
	public function getValue() {
		return $_SESSION['captcha'];
	}
	
	public function display() {
		$this->generateValue();
		
		header("Cache-Control: no-cache, must-revalidate");
		header("Expires: Sat, 26 Jul 1997 05:00:00 GMT");
		header("Content-type: image/png");
		$image = imagecreatetruecolor(110, 40);
		imageantialias($image, true);
		imagecolorallocate($image, 0, 0, 0);
		$textcolor = imagecolorallocate($image, 255, 255, 255);
		$size=rand(14, 18);
		$rotation=rand(-20, 20);
		$fontsDirectory = dirname(__FILE__).'/fonts';
		$fonts = IO_Utils::getFilesFromFolder($fontsDirectory, array('ttf'));
		$m = rand(-10, 10) / 85;
		$x = 5 + rand(0, 10);
		$w = 110 - $x;
		$c = 17 + rand(0, 5);
		imageline($image, $x, $c, $x+$w, $m * ($x+$w) + $c, $textcolor);
		//imagearc($image, $w / 2, $m * ($x+$w)/2 + $c, 110, $c/2, 180, 0, $textcolor);
		$w = 0;
		$value = $this->getValue();
		for ($i = 0; $i < 4; $i++) {
			$w += rand(15, 18);
			$randomFont = $fonts[rand(0, count($fonts) - 1)];
			imagettftext($image, $size, $rotation, $x + $w, $m * $w + $x + $c - $size / 3, $textcolor, $fontsDirectory.'/'.$randomFont, $value[$i]);
		}
		imagepng($image);
		imagedestroy($image);
	}
	
	public static function get() {
		return Router::get()->getModuleForRouteName('core')->getSubmodule('media_captcha');
	}
}

?>