<?php

/**
 * Panel to display a html table
 */
class GUI_Panel_Table extends GUI_Panel {
	private $lines = array();
	private $header = array();
	private $footer = array();
	private $numberOfColumns = 0;
	private $enableSortable = false;
	private $sorterOptions = array();
	
	public function __construct($name, $title = '') {
		parent::__construct($name, $title);
		
		$this->setTemplate(dirname(__FILE__).'/table.tpl');
		$this->setAttribute('summary', $title);
		$this->addClasses('core_gui_table');
	}
	
	public function afterInit() {
		if ($this->enabledSortable()) {
			$this->getModule()->addJsRouteReference('core_js', 'jquery/jquery.tablesorter.js');
			$this->addJS('
				$().ready(
					function() {
						$("#'.$this->getID().'").tablesorter(
							{
								'.$this->getSorterOptions().'
							}
						)
					}
				);
				$.tablesorter.addParser(
					{
						id: "separatedDigit",
						is: function(s) {
							return false;
						},
						format: function(s) {
							return jQuery.tablesorter.formatFloat(s.match(/>(.+)<\//)[1].replace(/\./g, ""));
						}, 
						type: "numeric"
					}
				);
			');
			$this->addClasses('core_gui_table_sortable');
		}
	}
	
	// GETTERS / SETTERS -------------------------------------------------------
	public function addLine(array $line) {
		if ($this->numberOfColumns == 0)
			$this->numberOfColumns = count($line);
		if (count($line) != $this->numberOfColumns) {
			$this->addError('Die \''.$line[0].'\' Zeile hat zu viele / wenige Spalten und wurde nicht angefügt!');
			return;
		}
		$this->lines[] = $line;
	}
	
	public function addHeader(array $line) {
		if ($this->numberOfColumns == 0)
			$this->numberOfColumns = count($line);
		if (count($line) != $this->numberOfColumns) {
			$this->addError('Die \''.$line[0].'\' Headerzeile hat zu viele / wenige Spalten und wurde nicht angefügt!');
			return;
		}
		$this->header[] = $line;
	}
	
	public function addFooter(array $line) {
		if ($this->numberOfColumns == 0)
			$this->numberOfColumns = count($line);
		if (count($line) != $this->numberOfColumns) {
			$this->addError('Die \''.$line[0].'\' Footerzeile hat zu viele / wenige Spalten und wurde nicht angefügt!');
			return;
		}
		$this->footer[] = $line;
	}
	
	public function getLines() {
		return $this->lines;
	}
	
	public function getHeaders() {
		return $this->header;
	}
	
	public function getFooters() {
		return $this->footer;
	}
	
	public function enabledSortable() {
		return $this->enableSortable;
	}
	
	public function enableSortable($enable = true) {
		$this->enableSortable = $enable;
	}
	
	public function addSorterOption($javascript) {
		$this->sorterOptions[] = $javascript;
	}
	
	private function getSorterOptions() {
		return implode(', ', $this->sorterOptions);
	}
	
	public function getColumnCount() {
		return $this->numberOfColumns;
	}
}

?>