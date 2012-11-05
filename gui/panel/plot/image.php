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