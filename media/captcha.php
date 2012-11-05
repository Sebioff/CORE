<?php

/**
 * @package CORE PHP Framework
 * @copyright Copyright (C) 2012 Sebastian Mayer, Andreas Sicking, Andre Jährling
 * @license GNU/GPL, see license.txt
 * This file is part of CORE PHP Framework.
 *
 * CORE PHP Framework is free software: you can redistribute it and/or modify it
 * under the terms of the GNU General Public License as published by the Free Software
 * Foundation, either version 3 of the License, or (at your option) any later version.
 *
 * CORE PHP Framework is distributed in the hope that it will be useful, but WITHOUT
 * ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS
 * FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with CORE PHP Framework. If not, see <http://www.gnu.org/licenses/>.
 */

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
		if (isset($_SESSION['captcha']))
			return $_SESSION['captcha'];
		else
			return null;
	}
	
	public function display() {
		$this->generateValue();
		
		header('Cache-Control: no-cache, must-revalidate');
		header('Expires: Sat, 26 Jul 1997 05:00:00 GMT');
		header('Content-type: image/png');
		$image = imagecreatetruecolor(110, 40);
		imageantialias($image, true);
		imagecolorallocate($image, 0, 0, 0);
		$textcolor = imagecolorallocate($image, 255, 255, 255);
		$size = rand(15, 18);
		$rotation = rand(-25, 25);
		$fontsDirectory = dirname(__FILE__).'/fonts';
		$fonts = IO_Utils::getFilesFromFolder($fontsDirectory, array('ttf'));
		$m = rand(-6, 6) / 85;
		$x = rand(10, 30);
		$w = $x + 40;
		$c = 20 + rand(0, 4);
		imageline($image, $x, $c, $x+$w, $m * ($x+$w) + $c, $textcolor);
		$this->waveAreaVertical($image, 0, 0, 110, 40, rand(8, 12), rand(10, 15));
		//imagearc($image, $w / 2, $m * ($x+$w)/2 + $c, 110, $c/2, 180, 0, $textcolor);
		$w = 0;
		$value = $this->getValue();
		$x += rand(0, 10);
		$c += 5;
		for ($i = 0; $i < 4; $i++) {
			$randomFont = $fonts[rand(0, count($fonts) - 1)];
			imagettftext($image, $size, $rotation, $x + $w, $c, $textcolor, $fontsDirectory.'/'.$randomFont, $value[$i]);
			$w += $size;
			$c += $m * $w;
		}
		$this->waveAreaVertical($image, 0, 0, 110, 40, rand(5, 8), rand(15, 20));
		$this->waveAreaHorizontal($image, 0, 0, 110, 40, rand(5, 10), rand(15, 20));
		imagepng($image);
		imagedestroy($image);
	}
	
	protected function waveAreaVertical($img, $x, $y, $width, $height, $amplitude = 10, $period = 10) {
		// Make a copy of the image twice the size
		$height2 = $height * 2;
		$width2 = $width * 2;

		$img2 = imagecreatetruecolor($width2, $height2);
		imagecopyresampled($img2, $img, 0, 0, $x, $y, $width2, $height2, $width, $height);

		if($period == 0) $period = 1;

		// Wave it
		for($i = 0; $i < ($width2); $i += 2)
			imagecopy($img2, $img2, $x + $i - 2, $y + sin($i / $period) * $amplitude, $x + $i, $y, 2, $height2);

		// Resample it down again
		imagecopyresampled($img, $img2, $x, $y, 0, 0, $width, $height, $width2, $height2);
		imagedestroy($img2);
	}
	
	protected function waveAreaHorizontal($img, $x, $y, $width, $height, $amplitude = 10, $period = 10) {
		// Make a copy of the image twice the size
		$height2 = $height * 2;
		$width2 = $width * 2;

		$img2 = imagecreatetruecolor($width2, $height2);
		imagecopyresampled($img2, $img, 0, 0, $x, $y, $width2, $height2, $width, $height);

		if($period == 0) $period = 1;

		// Wave it
		for($i = 0; $i < ($height2); $i += 2)
			imagecopy($img2, $img2, $x + sin($i / $period) * $amplitude, $y + $i - 2, $x, $y + $i, $width2, 2);

		// Resample it down again
		imagecopyresampled($img, $img2, $x, $y, 0, 0, $width, $height, $width2, $height2);
		imagedestroy($img2);
	}
	
	/**
	 * @return Media_Captcha
	 */
	public static function get() {
		return Router::get()->getModuleForRouteName('core')->getSubmodule('media_captcha');
	}
}

?>