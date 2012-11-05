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
 * Class to provide some actions for images
 */
class IO_Image {
	const ROTATE_RIGHT = 1;
	const ROTATE_LEFT = -1;
	
	private $image = null;
	private $imagePath = null;
	private $mimetype = null;
	
	public function __construct($image) {
		$this->imagePath = $image;
		$this->mimetype = image_type_to_mime_type(exif_imagetype($image));
		switch ($this->mimetype) {
			case 'image/gif':
				$this->image = imagecreatefromgif($image);
			break;
			case 'image/jpeg':
				$this->image = imagecreatefromjpeg($image);
			break;
			case 'image/png':
				$this->image = imagecreatefrompng($image);
			break;
			default:
				Router::get()->getCurrentModule()->contentPanel->addError('Can\'t create image from mime-type: '.$this->mimetype);
			break;
		}
	}
	
	/**
	 * enlarge / downsize this image to new dimensions
	 * actual width-to-height ratio stays the same
	 * @param $newX new width
	 * @param $newY new height
	 */
	public function resize($newX, $newY) {
		if (!$this->image)
			return;
		
		$aktX = imagesx($this->image);
		$aktY = imagesy($this->image);
		$newImgX = 0;
		$newImgY = 0;
		if ($aktX > $newX) {
			// make it smaller
			if ($aktX > $aktY) {
				if ($aktY >= $newY) {
					$newImgX = $aktX / $aktY * $newY;
					$newImgY = $newY;
				}
				if (($aktX > $newX) || ($newImgY > $newY)) {
					$newImgX = $newX;
					$newImgY = $aktY / $aktX * $newX;
				}
			} else {
				if ($aktX >= $newX) {
					$newImgX = $newX;
					$newImgY = $aktY / $aktX * $newX;
				}
				if (($aktY > $newY) || ($newImgY > $newY)) {
					$newImgX = $aktX / $aktY * $newY;
					$newImgY = $newY;
				}
			}
		} else {
			// make it bigger
			if ($aktX > $aktY) {
				if ($newX >= $newY) {
					$newImgX = $newX;
					$newImgY = $aktY / $aktX * $newX;
				}
				if (($newX < $newY) || ($newImgX > $newX)) {
					$newImgY = $newY;
					$newImgX = $aktX / $newX * $newY;
				}
			} else {
				if ($newX >= $newY) {
					$newImgX = $newX;
					$newImgY = $aktY / $aktX * $newX;
				}
				if (($newX < $newY) || ($newImgY > $newY)) {
					$newImgY = $newY;
					$newImgX = $aktX / $aktY * $newY;
				}
			}
		}
		$newImage = imagecreatetruecolor($newImgX, $newImgY);
		if (imagecopyresampled($newImage, $this->image, 0, 0, 0, 0, $newImgX, $newImgY, $aktX, $aktY))
			$this->image = $newImage;
	}
	
	/**
	 * Rotates this image by 90°
	 * @param $direction use self::ROTATE_*
	 */
	public function rotate($direction = self::ROTATE_RIGHT) {
		if (!$this->image)
			return;
		
		$this->image = imagerotate($this->image, 90 * (int)$direction, 0);
	}
	
	/**
	 * Removes colors from given image
	 */
	public function blackAndWhite() {
		if (!$this->image)
			return;
		
		imagefilter($this->image, MG_FILTER_GRAYSCALE);
	}
	
	/**
	 * Save changes to given image
	 */
	public function save() {
		if (!$this->image)
			return;
		
		switch ($this->mimetype) {
			case 'image/gif':
				imagegif($this->image, $this->imagePath);
			break;
			case 'image/jpeg':
				imagejpeg($this->image, $this->imagePath);
			break;
			case 'image/png':
				imagepng($this->image, $this->imagePath);
			break;
		}
		imagedestroy($this->image);
	}
}
?>