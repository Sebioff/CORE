<?php

class GUI_Panel_Date extends GUI_Panel {
	const FORMAT_DATETIME = 'd.m.Y, H:i:s';
	const FORMAT_DATE = 'd.m.Y';
	const FORMAT_TIME = 'H:i:s';
	
	private $time = 0;
	private $format;
	
	public function __construct($name, $time = 0, $format = self::FORMAT_DATETIME, $title = '') {
		parent::__construct($name, $title);
		
		if ($time == 0)
			$time = time();
		$this->time = $time;
		$this->format = $format;
		$this->setTemplate(dirname(__FILE__).'/date.tpl');
		$this->addClasses('core_gui_date');
	}
	
	// GETTERS / SETTERS -------------------------------------------------------
	public function getValue() {
		return date($this->format, $this->getTime());
	}
	
	public function getTime() {
		return $this->time;
	}
}

?>