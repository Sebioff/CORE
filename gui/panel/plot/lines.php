<?php
//require jpgraph
require_once(dirname(__FILE__).'/../3rdparty/jpgraph/jpgraph.php');
//require the plot files
require_once(dirname(__FILE__).'/../3rdparty/jpgraph/jpgraph_line.php');
require_once(dirname(__FILE__).'/../3rdparty/jpgraph/jpgraph_plotline.php');

class GUI_Panel_Plot_Lines extends GUI_Panel_Plot {
	
	public function __construct($name, $width = 600, $height = 300, $description = '', $title = '') {
		$this->image = new Graph($width, $height, 'auto');
		$this->image->SetScale('textlin');
		// format the image
		$this->image->legend->Pos(0.5, 0.09, 'center', 'bottom');
		$this->image->legend->SetLayout(LEGEND_HOR);
		$this->image->img->SetMargin(60, 20, 35, 65);
		
		parent::__construct($name, $description, $title);
	}

	public function addLine(array $line, $name = '', $color = '') {
		$plot = new LinePlot($line);
		$plot->SetLegend($name);
		$plot->SetWeight(2);
		if (Text::length($color) > 0)
			$plot->SetColor($color);
		$this->image->add($plot);
	}
	
	public function setTitle($title) {
		$this->image->title->set($title);
	}
	
	public function setXNames(array $names) {
		$this->image->xaxis->setTickLabels($names);
		if (Text::length($names[0]) > 2)
			$this->image->xaxis->SetLabelAngle(90);
	}
}
?>