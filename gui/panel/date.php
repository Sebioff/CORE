<?php

class GUI_Panel_Date extends GUI_Panel {
	const FORMAT_DATETIME = 'd.m.Y, H:i:s';
	const FORMAT_DATE = 'd.m.Y';
	const FORMAT_TIME = 'H:i:s';
	
	private $time = 0;
	private $format;
	
	public function __construct($name, $time = 0, $title = '', $format = self::FORMAT_DATETIME) {
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
		return date($this->format, $this->getTime()).(in_array($this->format, array(self::FORMAT_TIME, self::FORMAT_DATETIME)) ? ' Uhr' : '');
	}
	
	public function getTime() {
		return $this->time;
	}
	
	public function setFormat($format) {
		$this->format = $format;
	}
}

?>