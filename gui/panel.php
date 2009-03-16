<?php

class GUI_Panel {
	public $params;
	
	protected $name;
	protected $title;
	protected $template;
	/** contains all the errors of this panel + subpanels (as strings) if the validation
	 *  of one of these panel failed */
	protected $errors = array();
	
	private $attributes = array();
	private $classes = array();
	/** the unique id used for identifying this panel */
	private $ID;
	private $panels = array();
	/** the parent panel */
	private $parent;
	/** decides whether this panel behaves like a formular */
	private $submittable = false;
	
	// CONSTRUCTORS ------------------------------------------------------------
	public function __construct($name, $title = '') {
		$this->setName($name);
		$this->setTitle($title);
		
		$this->setTemplate(dirname(__FILE__).'/panel.tpl');
		$this->params = new GUI_Params();
		// TODO rename to onConstruct()? init only for when the object is actually "constructed to be used"
		$this->init();
	}
	
	// CUSTOM METHODS ----------------------------------------------------------
	public function display() {
		if ($this->hasBeenSubmitted()) {
			// FIXME validation needs to be done by the mainpanel right after Module::init() (as soon as it exists)
			$this->validate();
			$this->onSubmit();
			$this->executeCallbacks();
		}
			
		if ($this->submittable) {
			echo sprintf('<form name="%s" action="%s" method="post">', $this->name, $_SERVER['REQUEST_URI']);
			echo "\n";
		}
		
		require $this->template;
		
		if ($this->submittable) {
			$this->addPanel($hasBeenSubmittedBox = new GUI_Control_HiddenBox('hasbeensubmitted', 1));
			$hasBeenSubmittedBox->display();
			echo '</form>', "\n";
		}
	}
	
	public function displayPanel($panelName) {
		if (array_key_exists($panelName, $this->panels))
			$this->$panelName->display();
	}
	
	public function displayErrorsForPanel($panelName) {
		if (array_key_exists($panelName, $this->panels))
			$this->$panelName->displayErrors();
	}
	
	public function displayLabelForPanel($panelName, $additionalCSSClasses = array()) {
		if ($this->$panelName->hasErrors())
			$additionalCSSClasses[] = 'core_common_error_label';
		
		echo sprintf('<label for="%s"', $this->$panelName->getID());
		if ($additionalCSSClasses)
			echo sprintf(' class="%s"', implode(' ', $additionalCSSClasses));
		echo '>';
		echo $this->$panelName->getTitle();
		if ($this->$panelName instanceof GUI_Control && $this->$panelName->hasValidator('GUI_Validator_Mandatory'))
			echo '<span class="core_common_mandatory_asterisk"> *</span>';
		echo '</label>';
	}
	
	public function addPanel(GUI_Panel $panel) {
		$panel->setParent($this);
		$this->panels[$panel->getName()] = $panel;
		if ($panel instanceof GUI_Control_Submitbutton) {
			$this->submittable = true;
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
		foreach ($this->attributes as $attribute => $value) {
			$attributeString .= $attribute.'="'.$value.'" ';
		}
		if (count($this->classes))
			$attributeString .= 'class="'.$this->getClassString().'" ';
		
		return rtrim($attributeString);
	}
	
	/**
	 * @return true if this panel has been submitted, false otherwhise
	 */
	public function hasBeenSubmitted() {
		return isset($_POST[$this->getID().'-hasbeensubmitted']);
	}
	
	public function displayErrors() {
		if ($this->hasErrors())
			echo '<div class="core_common_error_list">'.implode('<br />', $this->errors).'</div>';
	}
	
	protected function generateID() {
		if ($this->parent)
			$this->ID = $this->parent->getID().'-'.$this->getName();
		else
			$this->ID = $this->getName();
	}
	
	/**
	 * Executes all validators of the controls belonging to this panel.
	 * Fills Panel::errors with all errors found
	 */
	protected function validate() {
		foreach ($this->panels as $panel)
			foreach($panel->validate() as $error) {
				$errorLabel = new GUI_Control_Label('error', $panel);
				$this->errors[] = $errorLabel->render().': '.$error;
			}
			
		if ($this->hasErrors())
			$this->addClasses('core_common_error');
		
		return $this->errors;
	}
	
	protected function executeCallbacks() {
		foreach ($this->panels as $panel) {
			$panel->executeCallbacks();
		}
	}
	
	// CALLBACKS ---------------------------------------------------------------
	/**
	 * You probably don't want to override the constructor if it's not neccessary,
	 * right? Well, then override this function instead, please.
	 */
	protected function init() {
		// callback
	}
	
	/**
	 * Called as soon as the form has been submitted.
	 * NOTE: you need to check for yourself if the form has errors or not.
	 */
	protected function onSubmit() {	}
	
	// GETTERS / SETTERS -------------------------------------------------------
	public function __get($panelName) {
		if (array_key_exists($panelName, $this->panels))
			return $this->panels[$panelName];
		else
			throw new CORE_Exception('Child panel does not exist: '.$panelName);
	}
	
	public function getName() {
		return $this->name;
	}
	
	private function setName($name) {
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
	
	/**
	 * @return GUI_Panel
	 */
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
	
	public function hasErrors() {
		return (count($this->errors) > 0);
	}
}

?>