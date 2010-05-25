<?php

abstract class GUI_Panel_Plot extends GUI_Panel_Image {
	protected $graph = null;
	protected $height = 300;
	protected $width = 600;
	
	const LEGEND_POSITION_NORTH = 1;
	const LEGEND_POSITION_EAST = 2;
	const LEGEND_POSITION_SOUTH = 3;
	const LEGEND_POSITION_WEST = 4;
	
	public function __construct($name, $description = '', $title = '') {
		// wrap it... we don't need an url here
		parent::__construct($name, '', $description, $title);
	}
	
	protected function beforeDisplay() {
		$time = microtime(true);
		$filename = ini_get('upload_tmp_dir').'/'.$time;
		$this->graph->stroke($filename);
		$this->setURL(App::get()->getModule('plotimage')->getUrl(array('time' => $time)));
		
		parent::beforeDisplay();
	}
	
	// GETTERS / SETTERS -------------------------------------------------------
	/**
	 * @return Graph
	 */
	public function getGraph() {
		return $this->graph;
	}
	
	/**
	 * @param position: self::LEGEND_POSITION_*
	 */
	public function setLegendPosition($position) {
		switch ($position) {
			case self::LEGEND_POSITION_NORTH:
				$this->graph->legend->Pos(0.5, 0.09, 'center', 'bottom');
				$this->graph->legend->SetLayout(LEGEND_HOR);
				$this->graph->img->SetMargin(60, 20, 35, 65);
			break;
			case self::LEGEND_POSITION_EAST:
				$this->graph->legend->Pos(0.01, 0.01, 'left', 'top');
				$this->graph->legend->SetLayout(LEGEND_VERT);
				$this->graph->img->SetMargin(140, 10, 10, 35);
			break;
			case self::LEGEND_POSITION_SOUTH:
				$this->graph->legend->Pos(0.5, 0.9, 'center', 'top');
				$this->graph->legend->SetLayout(LEGEND_HOR);
				$this->graph->img->SetMargin(60, 20, 5, 65);
			break;
			case self::LEGEND_POSITION_WEST:
				$this->graph->legend->Pos(0.01, 0.01, 'right', 'top');
				$this->graph->legend->SetLayout(LEGEND_VERT);
				$this->graph->img->SetMargin(30, 110, 10, 35);
			break;
			default:
				$this->addError('unknown legend position: '.$position);
			break;
		}
	}
}
?>