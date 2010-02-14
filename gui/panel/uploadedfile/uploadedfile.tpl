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