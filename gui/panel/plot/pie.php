<?php
//require jpgraph
require_once(dirname(__FILE__).'/../3rdparty/jpgraph/jpgraph.php');
//require the plot files
require_once(dirname(__FILE__).'/../3rdparty/jpgraph/jpgraph_pie.php');
require_once(dirname(__FILE__).'/../3rdparty/jpgraph/jpgraph_pie3d.php');

class GUI_Panel_Plot_Pie extends GUI_Panel_Plot {
	private $data = array();
	public function __construct($name, $width = 600, $height = 300, $description = '', $title = '') {
		$this->graph = new PieGraph($width, $height, 'auto');
		$this->graph->SetScale('textlin');
		
		parent::__construct($name, $description, $title);
	}
	
	public function setData(array $data) {
		$this->data = $data;
	}
	
	public function setDataNames(array $names) {
		foreach ($names as $key => $val) {
			if (Text::length($val) > 10)
				$names[$key] = substr($val, 0, 10);
		}
		$this->names = $names;
	}
	
	protected function beforeDisplay() {
		$plot = new PiePlot3d($this->data);
		$this->graph->add($plot);
		$plot->SetLegends($this->names);
		$plot->SetAngle(30);
		// Move the pie slightly to the left
		$plot->SetCenter(0.4, 0.6);
		
		parent::beforeDisplay();
	}
}
?>