<?php
//require jpgraph
require_once(dirname(__FILE__).'/3rdparty/jpgraph/jpgraph.php');
//require the plot files
require_once(dirname(__FILE__).'/3rdparty/jpgraph/jpgraph_pie.php');
require_once(dirname(__FILE__).'/3rdparty/jpgraph/jpgraph_pie3d.php');

/**
 * Class to send a plot to the browser.
 */
class plotimg {
	private $data = null;
	private $width = 0;
	private $height = 0;
	private $type = '';
	
	private $graph = null;
	
	public function __construct() {
		$data = unserialize(stripslashes(urldecode($_GET['data'])));
		$this->legend = array_keys($data);
		$this->data = array_values($data);
		$this->width = isset($_GET['width']) ? $_GET['width'] : 600;
		$this->height = isset($_GET['height']) ? $_GET['height'] : 300;
		$this->type = isset($_GET['type']) ? $_GET['type'] : 'pie3d';
		switch ($this->type) {
			case 'pie3d':
				$this->pie3d();
			break;
		}
	}
	
	private function pie3d() {
		$this->graph = new PieGraph($this->width, $this->height, 'auto');
		// Create pie plot
		$p1 = new PiePlot3d($this->data);
		$p1->SetTheme('sand'); // earth / pastel / water / sand
		$p1->SetAngle(30);
		$p1->value->SetFont(FF_ARIAL,FS_NORMAL,12);
		$p1->SetLegends($this->legend);
		// Move the pie slightly to the left
		$p1->SetCenter(0.4,0.6);
		$this->graph->Add($p1);
	}
	
	public function stroke() {
		header('content-type:image/png');
		$this->graph->Stroke();
	}
}

$image = new plotimg();
$image->stroke();
?>