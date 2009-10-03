<?php

class GUI_Panel_ServerTime extends GUI_Panel {
	public function __construct($name, $title = '') {
		parent::__construct($name, $title);
		
		$this->setTemplate(dirname(__FILE__).'/servertime.tpl');
	}
	
	public function init() {
		parent::init();
		
		$this->addPanel($date = new GUI_Panel_Date('servertime', time(), 'Serverzeit'));
		$js = 'var second = '.(int)date('s').'; function setServertime() { second++; date = new Date('.date('Y').', '.(int)date('m').', '.date('j').', '.date('G').', '.(int)date('i').', second); month = date.getMonth() <= 9 ? "0"+date.getMonth() : date.getMonth(); day = date.getDate() <= 9 ? "0"+date.getDate() : date.getDate(); hours = date.getHours() <= 9 ? "0"+date.getHours() : date.getHours(); minutes = date.getMinutes() <= 9 ? "0"+date.getMinutes() : date.getMinutes(); seconds = date.getSeconds() <= 9 ? "0"+date.getSeconds() : date.getSeconds(); $("#'.$this->getID().'-'.$this->getName().'").text(day+"."+month+"."+date.getFullYear()+", "+hours+":"+minutes+":"+seconds+" Uhr"); window.setTimeout("setServertime()", 1000); } window.setTimeout("setServertime()", '.(1000 - (int)substr(microtime(), 2, 3)).');';
		$this->addJS($js);
	}
}
?>