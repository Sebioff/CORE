<?php

abstract class GUI_Panel_Plot extends GUI_Panel_Image {
	protected $graph = null;
	protected $height = 300;
	protected $width = 600;
	
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
}
?>