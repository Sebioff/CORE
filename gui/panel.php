<?php

class GUI_Panel {
	protected $name;
	protected $params;
	protected $title;
	protected $template;
	
	private $attributes = array();
	private $classes = array();
	private $panels = array();
	/** decides whether this panel behaves like a formular */
	private $submitable = false;
	/** the parent panel */
	private $parent;
	/** the unique id used for identifying this panel */
	private $ID;
	
	// CONSTRUCTORS ------------------------------------------------------------
	public function __construct($name, $title = '') {
		$this->setName($name);
		$this->setTitle($title);
		
		$this->setTemplate(dirname(__FILE__).'/panel.tpl');
		$this->params = new GUI_Params();
	}
	
	// CUSTOM METHODS ----------------------------------------------------------
	public function display() {
		if($this->submitable) {
			echo sprintf('<form name="%s" action="%s" method="post">', $this->name, $_SERVER['REQUEST_URI']);
			echo "\n";
		}
		
		require $this->template;
		
		if($this->submitable)
			echo '</form>', "\n";
	}
	
	public function displayLabelForPanel($panelName) {
		$label = new GUI_Control_Label('label', $this->$panelName);
		$label->display();
	}
	
	public function addPanel(GUI_Panel $panel) {
		$panel->setParent($this);
		$this->panels[$panel->getName()] = $panel;
		if($panel instanceof GUI_Control_Submitbutton) {
			$this->submitable = true;
		}
	}
	
	/**
	 * Adds all given css classes to this panel
	 * @param a variable amount of classes (strings)
	 */
	public function addClasses(/* strings */) {
		$additionalClasses = func_get_args();
		$this->classes = array_merge($this->classes, $additionalClasses);
	}
	
	/**
	 * @return a string of classes belonging to this panel, e.g. for use in
	 * html output
	 */
	public function getClassString() {
		return implode(' ', $this->classes);
	}
	
	/**
	 * @return a string of html attributes belonging to this panel
	 */
	public function getAttributeString() {
		$attributeString = null;
		foreach($this->attributes as $attribute => $value) {
			$attributeString .= $attribute.'="'.$value.'" ';
		}
		if (count($this->classes))
			$attributeString .= 'class="'.$this->getClassString().'"';
		
		return $attributeString;
	}
	
	private function generateID() {
		if ($this->parent)
			$this->ID = $this->parent->getID().'-'.$this->getName();
		else
			$this->ID = $this->getName();
	}
	
	// GETTERS / SETTERS -------------------------------------------------------
	public function __get($panelName) {
		if(array_key_exists($panelName, $this->panels))
			return $this->panels[$panelName];
		else
			throw new CORE_Exception('Child panel does not exist: '.$panelName);
	}
	
	public function getName() {
		return $this->name;
	}
	
	public function setName($name) {
		$this->name = $name;
		$this->generateID();
	}
	
	public function getTitle() {
		return $this->title;
	}
	
	public function setTitle($title) {
		$this->title = $title;
	}
	
	public function getID() {
		return $this->ID;
	}
	
	public function getParent() {
		return $this->parent;
	}
	
	protected function setParent(GUI_Panel $parent) {
		$this->parent = $parent;
		$this->generateID();
	}
	
	public function setTemplate($template) {
		$this->template = $template;
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