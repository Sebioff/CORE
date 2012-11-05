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