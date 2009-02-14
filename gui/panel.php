<?php

class GUI_Panel {
	private $attributes = array();
	private $classes = array();
	private $panels = array();
	
	// CUSTOM METHODS ----------------------------------------------------------
	public function display() {
		foreach ($this->panels as $panel)
			$panel->display();
	}
	
	public function addPanel(GUI_Panel $panel) {
		$this->panels[] = $panel;
	}
	
	/**
	 * Adds all given css classes to this panel
	 * @param a variable amount of classes (strings)
	 */
	public function addClasses(/* array of strings */) {
		$additionalClasses = func_get_args();
		$this->classes = array_merge($this->classes, $additionalClasses);
	}
	
	/**
	 * @return a string of classes belonging to this template, e.g. for use in
	 * html output
	 */
	public function getClassString() {
		return implode(' ', $this->classes);
	}
	
	/**
	 * @return a string of html attributes belonging to this template
	 */
	public function getAttributeString() {
		$attributeString = null;
		foreach($this->attributes as $attribute => $value) {
			$attributeString .= $attribute.'="'.$value.'" ';
		}
		$attributeString .= 'class="'.$this->getClassString().'"';
		
		return $attributeString;
	}
	
	// GETTERS / SETTERS -------------------------------------------------------
	public function __get($panelName) {
        if (array_key_exists($panelName, $this->panels))
            return $this->panels[$panelName];
        else
        	throw new CORE_Exception('Child panel does not exist: '.$panelName);
	}
	
	/**
	 * For setting html attributes
	 * @param $attribute the attribute name
	 * @param $value the attribute value
	 */
	public function setAttribute($attribute, $value) {
		$this->attributes[$attribute] = $value;
	}
}

?>