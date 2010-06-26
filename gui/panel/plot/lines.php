<?php

//require jpgraph
require_once dirname(__FILE__).'/../3rdparty/jpgraph/jpgraph.php';
//require the plot files
require_once dirname(__FILE__).'/../3rdparty/jpgraph/jpgraph_line.php';
require_once dirname(__FILE__).'/../3rdparty/jpgraph/jpgraph_plotline.php';

class GUI_Panel_Plot_Lines extends GUI_Panel_Plot {
	public function __construct($name, $width = 600, $height = 300, $description = '', $title = '') {
		$this->graph = new Graph($width, $height, 'auto');
		$this->graph->SetScale('textlin');
		// format the image
		$this->setLegendPosition(parent::LEGEND_POSITION_NORTH);
		if ($title)
			$this->graph->title->Set($title);
		
		parent::__construct($name, $description, $title);
	}

	public function addLine(array $line, $name = '', $color = '') {
		$plot = new LinePlot($line);
		if (Text::length($name) > 10)
			$name = substr($name, 0, 10);
		$plot->SetLegend($name);
		$plot->SetWeight(2);
		if (Text::length($color) > 0)
			$plot->SetColor($color);
		$this->graph->add($plot);
	}
	
	public function setTitle($title) {
		$this->graph->title->set($title);
	}
	
	public function setXNames(array $names) {
		$this->graph->xaxis->setTickLabels(array_values($names));
		if (Text::length(reset($names)) > 2)
			$this->graph->xaxis->SetLabelAngle(90);
	}
}

?>