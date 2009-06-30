<?php

/**
 * Panel to create a plot Panel.
 * To add more plot types include them first in plotimg.php.
 */
class GUI_Panel_Plot extends GUI_Panel {
	
	public function init() {
		parent::init();
		
		$this->setTemplate(dirname(__FILE__).'/plot.tpl');
	}
	
	public function createPiePlot($data, $width = 600, $height = 300) {
		$this->params->data = urlencode(serialize($data));
		$this->params->width = $width;
		$this->params->height = $height;
	}
}
?>