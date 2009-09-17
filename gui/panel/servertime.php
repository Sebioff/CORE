<?php

class GUI_Panel_ServerTime extends GUI_Panel {
	public function __construct($name, $title = '') {
		parent::__construct($name, $title);
		
		$this->setTemplate(dirname(__FILE__).'/servertime.tpl');
	}
	
	public function init() {
		parent::init();
		
		$this->addPanel($date = new GUI_Panel_Date('servertime', time(), 'Serverzeit'));
		$js = 'servertime = new Date('.date('Y, n, j, G, i, s').');
//		alert(servertime.toGMTString());
		';
		$this->getModule()->addJsAfterContent($js);
	}
}
?>