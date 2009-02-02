<?php

class GUI_Panel {
	private $panels = array();
	
	public function display() {
		foreach ($this->panels as $panel)
			$panel->display();
	}
	
	public function addPanel(GUI_Panel $panel) {
		$this->panels []= $panel;
	}
	
	public function __get($panelName) {
        if (array_key_exists($panelName, $this->panels))
            return $this->panels[$panelName];
        else
        	throw new CORE_Exception('Child panel does not exist: '.$panelName);
	}
}

?>