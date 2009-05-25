<?php

class GUI_Panel {
	public $params;
	
	protected $_name;
	protected $title;
	protected $template;
	/** contains all the errors of this panel + subpanels (as strings) if the validation
	  * of one of these panel failed */
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
	public function render() {
		ob_start();
		require $this->template;
		return ob_get_clean();
	}
	
	public function display() {
		$this->beforeDisplay();
		
		if ($this->submittable) {
			echo sprintf('<form name="%s" id="%s" action="%s" method="post">', $this->getID(), $this->getID(), $_SERVER['REQUEST_URI']);
			echo "\n";
		}
		
		echo $this->render();
		
		if ($this->submittable) {
			$this->addPanel($hasBeenSubmittedBox = new GUI_Control_HiddenBox('hasbeensubmitted', 1));
			$hasBeenSubmittedBox->display();
			echo '</form>', "\n";
		}
	}
	
	public function displayPanel($panelName) {
		if ($this->hasPanel($panelName))
			$this->$panelName->display();
		else
			IO_Log::get()->warning('Tried to display non-existant panel: '.$panelName);
	}
	
	public function displayErrorsForPanel($panelName) {
		if ($this->hasPanel($panelName))
			$this->$panelName->displayErrors();
	}
	
	public function displayLabelForPanel($panelName, $additionalCSSClasses = array()) {
		if (!$this->hasPanel($panelName)) {
			IO_Log::get()->warning('Tried to display label for non-existant panel: '.$panelName);
			return;
		}
		
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
	
	public function addPanel(GUI_Panel $panel, $toBeginning = false) {
		// TODO its probably better to rename all attributes (e.g. $name to $_name) so that it's
		// quite unlikely that a panels name is equal to the name of an attribute.
		// -> the following check could be removed then.
		if (!$this->hasPanel($panel->getName()) && isset($this->{$panel->getName()}))
			throw new Exception('Panel name is not allowed (already used internally): '.$panel->getName());
		
		$panel->setParent($this);
		
		if (!$toBeginning)
			$this->panels[$panel->getName()] = $panel;
		else
			$this->panels = array($panel->getName() => $panel) + $this->panels;
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
	 * Removes all given css classes from this panel
	 * @param a variable amount of classes (strings)
	 */
	public function removeClasses(/* strings */) {
		$removeClasses = func_get_args();
		$this->classes = array_diff($this->classes, $removeClasses);
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
		if (!empty($this->classes))
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
			
		foreach ($this->panels as $panel)
			$panel->generateID();
	}
	
	/**
	 * Executes all validators of the controls belonging to this panel.
	 * Fills Panel::errors with all errors found
	 */
	protected function validate() {
		foreach ($this->panels as $panel)
			foreach($panel->validate() as $error)
				$this->addError($error, $panel);
			
		return $this->errors;
	}
	
	protected function getJsValidators() {
		$validators = '';
		
		foreach ($this->panels as $panel)
			$validators .= $panel->getJsValidators();
			
		return $validators;
	}
	
	/**
	 * Adds a custom error.
	 * @param $message the error message to display
	 * @param $panel the panel this error belongs to
	 */
	public function addError($message, GUI_Panel $panel = null) {
		if ($panel) {
			$errorLabel = new GUI_Control_Label('error', $panel);
			$this->errors[] = $errorLabel->render().': '.$message;
			$panel->addClasses('core_common_error');
		}
		else
			$this->errors[] = $message;
	}
	
	protected function executeCallbacks() {
		foreach ($this->panels as $panel) {
			$panel->executeCallbacks();
		}
	}
	
	// CALLBACKS ---------------------------------------------------------------
	public function beforeInit() {
		foreach ($this->panels as $panel)
			$panel->beforeInit();
	}
	
	/**
	 * You probably don't want to override the constructor if it's not neccessary,
	 * right? Well, then override this function instead, please.
	 */
	protected function init() {
		// callback
	}
	
	/**
	 * Executed after init and before display.
	 * Executes validators and callbacks.
	 * NOTE: this method operates on all added panels. If you overwrite it and
	 * add panels in your method, you need to call parent::afterInit() AFTER
	 * adding your panels.
	 * => Sequence of:
	 *  - init (adding panels)
	 *  - afterInit (executing callbacks and validators)
	 *  - display (actually displaying the panel)
	 * needs to be kept for everything to work right!
	 */
	public function afterInit() {
		foreach ($this->panels as $panel)
			$panel->afterInit();

		if ($this->submittable) {
			if ($validators = $this->getJsValidators()) {
				$module = Router::get()->getCurrentModule();
				$module->addJsRouteReference('core_js', 'jquery/jquery.validate.js');
				$module->addJsAfterContent(sprintf('$().ready(function() {$("#%s").validate({errorClass: "core_common_error", wrapper: "div class=\"core_common_error_js_wrapper\"", invalidHandler: function(form, validator) {$(this).find(":input[type=\'submit\']").removeAttr("disabled");}}); %s});', $this->getID(), $validators));
			}
			
			if ($this->hasBeenSubmitted()) {
				$this->validate();
				$this->executeCallbacks();
				
				if ($this->hasErrors())
					$this->addClasses('core_common_error');
			}
		}
	}

	protected function beforeDisplay() {
		// overwrite
	}
	
	// GETTERS / SETTERS -------------------------------------------------------
	public function __get($panelName) {
		if (array_key_exists($panelName, $this->panels))
			return $this->panels[$panelName];
		else
			throw new CORE_Exception('Child panel does not exist: '.$panelName);
	}
	
	public function getName() {
		return $this->_name;
	}
	
	private function setName($name) {
		$this->_name = $name;
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
		return (!empty($this->errors));
	}
	
	protected function hasPanel($panelName) {
		return array_key_exists($panelName, $this->panels);
	}
}

?>