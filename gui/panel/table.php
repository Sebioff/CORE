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
	private $tableCssClasses = array();
	
	private static $firstTableOnPage = true;
	
	public function __construct($name, $title = '') {
		parent::__construct($name, $title);
		
		$this->setTemplate(dirname(__FILE__).'/table.tpl');
		$this->setAttribute('summary', $title);
		$this->addClasses('core_gui_table');
	}
	
	public function afterInit() {
		parent::afterInit();
		
		if ($this->enabledSortable()) {
			$this->getModule()->addJsRouteReference('core_js', 'jquery/jquery.tablesorter.js');
			
			if (self::$firstTableOnPage) {
				$this->addJS('
					$.tablesorter.addParser(
						{
							id: "separatedDigit",
							is: function(s) {
								return false;
							},
							format: function(s) {
								return jQuery.tablesorter.formatFloat(s.replace(/\./g, ""));
							},
							type: "numeric"
						}
					);
				');
				self::$firstTableOnPage = false;
			}
			
			$this->addJS('
				$("#'.$this->getID().'").tablesorter(
					{
						'.$this->getSorterOptions().'
					}
				);
			');
			$this->addClasses('core_gui_table_sortable');
		}
	}
	
	public function displayCell($cell) {
		if ($cell instanceof GUI_Panel)
			$cell->display();
		else
			echo $cell;
	}
	
	// GETTERS / SETTERS -------------------------------------------------------
	public function addLine(array $line) {
		if ($this->numberOfColumns == 0)
			$this->numberOfColumns = count($line);
		if (count($line) != $this->numberOfColumns) {
			$this->addError('Die \''.$line[0].'\' Zeile hat zu viele / wenige Spalten und wurde nicht angefügt!');
			return;
		}
		foreach ($line as $column) {
			if ($column instanceof GUI_Panel)
				$this->addPanel($column);
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
		foreach ($line as $column) {
			if ($column instanceof GUI_Panel)
				$this->addPanel($column);
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
		foreach ($line as $column) {
			if ($column instanceof GUI_Panel)
				$this->addPanel($column);
		}
		$this->footer[] = $line;
	}
	
	/**
	 * Sets a css class for given rows/columns
	 * To set a class for every n-th column, set $line to null
	 * To set a class for every n-th line, set $column to null
	 * @param string $class
	 * @param int $column
	 * @param int $line
	 */
	public function addTableCssClass($class, $column = null, $line = null) {
		$this->tableCssClasses[$column][$line]['classes'][] = $class;
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
	
	public function getTrAttributeString($row) {
		if (isset($this->tableCssClasses[null][$row]['classes']))
			return 'class="'.implode(' ', $this->tableCssClasses[null][$row]['classes']).'"';
		else
			return '';
	}
	
	public function getTdAttributeString($column, $row) {
		$classes = array();
		if (isset($this->tableCssClasses[$column][null]['classes']))
			$classes = array_merge($classes, $this->tableCssClasses[$column][null]['classes']);
		if (isset($this->tableCssClasses[$column][$row]['classes']))
			$classes = array_merge($classes, $this->tableCssClasses[$column][$row]['classes']);
		if ($classes)
			return 'class="'.implode(' ', $classes).'"';
		else
			return '';
	}
}

?>