<?php

class GUI_Panel_ServerTime extends GUI_Panel {
	public function __construct($name, $title = '') {
		parent::__construct($name, $title);
		
		$this->setTemplate(dirname(__FILE__).'/servertime.tpl');
	}
	
	public function init() {
		parent::init();
		
		$this->addPanel($date = new GUI_Panel_Date('servertime', time(), 'Serverzeit'));
		$js = 'var year = '.date('Y').'; var month = '.(int)date('m').'; var day = '.date('j').'; var hour = '.date('G').'; var minute = '.(int)date('i').'; var second = '.(int)date('s').'; function setServertime() { second++; if (second >= 60) { second = 0; minute++; } if (minute >= 60) { minute = 0; hour++; } if (hour >= 24) { hour = 0; day++; } if (day > '.date('t').') { day = 1; month++; } if (month > 12) { month = 1; year++; } $("#'.$this->getID().'-'.$this->getName().'").text((day < 10 ? "0"+day : day)+"."+(month < 10 ? "0"+month : month)+"."+year+", "+(hour < 10 ? "0"+hour : hour)+":"+(minute < 10 ? "0"+minute : minute)+":"+(second < 10 ? "0"+second : second)+" Uhr"); window.setTimeout("setServertime()", 1000); } window.setTimeout("setServertime()", '.(1000 - (int)substr(microtime(), 2, 3)).');';
		$this->addJS($js);
	}
}
?>