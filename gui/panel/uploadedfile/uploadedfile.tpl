<?
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
?>
<div id="<?= $this->getID() ?>" <?= $this->getAttributeString() ?>>
	<? if ($this->hasErrors()): ?>
		<? $this->displayErrors(); ?>
	<? endif; ?>
	<?php
	switch ($this->params->mimetype) {
		case GUI_Control_FileUpload::TYPE_JPEG:
		case GUI_Control_FileUpload::TYPE_GIF:
		case GUI_Control_FileUpload::TYPE_PNG:
			$file = new GUI_Panel_Image('picture', Router::get()->transformPathToHTMLPath(dirname(__FILE__).'/image.php').'?m='.$this->params->m.'&amp;c='.$this->params->c.'&amp;q='.$this->params->q, $this->params->caption);
			$file->display();
		break;
		
		case GUI_Control_FileUpload::TYPE_TXT:
		case GUI_Control_FileUpload::TYPE_PDF:
		case GUI_Control_FileUpload::TYPE_HTML:
		case GUI_Control_FileUpload::TYPE_WORD:
		case GUI_Control_FileUpload::TYPE_ODT:
	
		case GUI_Control_FileUpload::TYPE_ZIP:
		case GUI_Control_FileUpload::TYPE_RAR:
	
		default:
			$file = new GUI_Control_Link('link', $this->params->caption, Router::get()->transformPathToHTMLPath(dirname(__FILE__).'/download.php').'?m='.$this->params->m.'&amp;c='.$this->params->c.'&amp;q='.$this->params->q);
			$file->display();
		break;
	}
	?>
</div>