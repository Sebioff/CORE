<?php

/**
 * A panel that allows to sort its child panels using drag & drop.
 */
class GUI_Panel_Sortable extends GUI_Panel {
	private $saveTo;
	private $propertyName;
	private $options = array();
	
	/**
	 * @param DB_Record $saveTo the record used to store sorting information
	 * @param String $propertyName the property of the record in which to store
	 * sorting information; should be of type TEXT or equivalent
	 */
	public function __construct($name, DB_Record $saveTo, $propertyName) {
		parent::__construct($name);
		$this->saveTo = $saveTo;
		$this->propertyName = $propertyName;
	}
	
	public function afterInit() {
		parent::afterInit();
		
		$this->setTemplate(dirname(__FILE__).'/sortable.tpl');
		$this->getModule()->addJsRouteReference('core_js', 'jquery/jquery-ui.js');
		$additionalOptions = '';
		foreach ($this->options as $key => $value)
			$additionalOptions .= ', '.$key.': '.$value;
		$this->addJS(sprintf('$("#%1$s").sortable({
			update: function(event, ui) {
				var positions = $(this).sortable("toArray");
				for (var i = 0; i < positions.length; i++)
					positions[i] = positions[i].replace(/%1$s-/, "");
				$.core.ajaxRequest("%1$s", "ajaxSave", { positions: positions.join(",") });
			}
			%2$s
		});', $this->getAjaxID(), $additionalOptions));
	}
	
	/**
	 * @return array of sorted panels
	 */
	public function getSortedPanels() {
		$sortedPanels = array();
		$unsortedPanels = $this->getPanels();
		$order = explode(',', $this->saveTo->{Text::underscoreToCamelCase($this->propertyName)});
		foreach ($order as $panelName) {
			if ($this->hasPanel($panelName)) {
				$sortedPanels[$panelName] = $this->__get($panelName);
				unset($unsortedPanels[$panelName]);
			}
		}
		$sortedPanels += $unsortedPanels;
		return $sortedPanels;
	}
	
	public function ajaxSave() {
		$this->saveTo->{Text::underscoreToCamelCase($this->propertyName)} = $_POST['positions'];
		$this->saveTo->save();
	}
	
	// GETTERS / SETTERS -------------------------------------------------------
	public function setOption($name, $value) {
		$this->options[$name] = $value;
	}
	
	/**
	 * Specifies an element used to drag the panels
	 * @param String $cssSelector a jQuery css selector for an element/elements
	 * used for dragging the child panels
	 */
	public function setHandle($cssSelector) {
		$this->setOption('handle', '"'.$cssSelector.'"');
	}
}

?>