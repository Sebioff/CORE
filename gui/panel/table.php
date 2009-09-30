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
			$this->addJS('$().ready(function() { $("#'.$this->getID().'").tablesorter( { '.$this->getSorterOptions().'	} )	} );');
			$this->addClasses('core_gui_table_sortable');
		}
	}
	
	private function checkLine($line) {
		foreach ($line as $key => $value) {
			if ($value instanceof GUI_Panel)
				$line[$key] = $value->render();
		}
		return $line;
	}
	
	// GETTERS / SETTERS -------------------------------------------------------
	public function addLine(array $line) {
		if ($this->numberOfColumns == 0)
			$this->numberOfColumns = count($line);
		if (count($line) != $this->numberOfColumns) {
			$this->addError('Die \''.$line[0].'\' Zeile hat zu viele / wenige Spalten und wurde nicht angefügt!');
			return;
		}
		$this->lines[] = $this->checkLine($line);
	}
	
	public function addHeader(array $line) {
		if ($this->numberOfColumns == 0)
			$this->numberOfColumns = count($line);
		if (count($line) != $this->numberOfColumns) {
			$this->addError('Die \''.$line[0].'\' Headerzeile hat zu viele / wenige Spalten und wurde nicht angefügt!');
			return;
		}
		$this->header[] = $this->checkLine($line);
	}
	
	public function addFooter(array $line) {
		if ($this->numberOfColumns == 0)
			$this->numberOfColumns = count($line);
		if (count($line) != $this->numberOfColumns) {
			$this->addError('Die \''.$line[0].'\' Footerzeile hat zu viele / wenige Spalten und wurde nicht angefügt!');
			return;
		}
		$this->footer[] = $this->checkLine($line);
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
		$this->sorterOptions[] = '{ '.$javascript.' }';
	}
	
	private function getSorterOptions() {
		return implode(', ', $this->sorterOptions);
	}
}

?>