<?php

/**
 * A panel is an element that containts any type of content that should be displayed.
 * Panels can have child panels.
 * If one of this child panels is a submittable control such as GUI_Control_SubmitButton
 * the panel can be submitted using POST.
 * Panels can have error messages attached to them. If you add validators to a
 * panel the validator automatically attaches its error message to the panel
 * if anything is wrong.
 */
class GUI_Panel {
	public $params;
	
	protected $_name;
	protected $title;
	protected $template;
	/** contains all the errors of this panel + subpanels (as strings) if the validation
	  * of one of these panel failed */
	protected $errors = array();
	protected $successMessage = '';
	
	private $attributes = array();
	private $classes = array();
	/** the unique id used for identifying this panel */
	private $ID;
	private $panels = array();
	/** the parent panel */
	private $parent;
	/** decides whether this panel behaves like a formular */
	private $submittable = false;
	private $js;
	
	// CONSTRUCTORS ------------------------------------------------------------
	/**
	 * @param $name name of the panel, MAY NOT CONTAIN: -,
	 * Those chars are currently not prohibited by code (to save time), but don't use them!
	 */
	public function __construct($name, $title = '') {
		$this->setName($name);
		$this->setTitle($title);
		
		$this->setTemplate(dirname(__FILE__).'/panel.tpl');
		$this->params = new GUI_Params();
		// TODO rename to onConstruct()? init only for when the object is actually "constructed to be used"
		//$this->init();
	}
	
	// CUSTOM METHODS ----------------------------------------------------------
	public function render() {
		ob_start();
		require $this->template;
		/**
		 * When working with ajax we only want to execute js belonging to requested
		 * panels, so js belonging to this panel is only added to the page if
		 * the panel is being displayed. Otherwise it is instantly added to the
		 * page (see GUI_Panel::addJS()).
		 */
		if ($this->getJS()) {
			$this->getModule()->addJsAfterContent($this->getJS());
		}
		return ob_get_clean();
	}
	
	public function display() {
		$this->beforeDisplay();
		
		if ($this->submittable) {
			echo sprintf('<form id="%s" action="%s" method="post" enctype="multipart/form-data">', $this->getID(), $_SERVER['REQUEST_URI']);
			echo '<fieldset>';
			// fix for IE not submitting button name in post data if form is submitted with enter in forms with only one input
			echo '<!--[if IE]><input type="text" style="display: none;" disabled="disabled" size="1" name="IESucks" /><![endif]-->';
		}
		
		echo $this->render();
		
		if ($this->submittable) {
			$this->addPanel($hasBeenSubmittedBox = new GUI_Control_HiddenBox('hasbeensubmitted', 1));
			$hasBeenSubmittedBox->display();
			echo '</fieldset>', '</form>';
		}
	}
	
	/**
	 * Displays a specific panel
	 * Creates a warning in the error log if the panel doesn't exist
	 * @param $panelName
	 */
	public function displayPanel($panelName) {
		if ($this->hasPanel($panelName))
			$this->$panelName->display();
		else
			IO_Log::get()->warning('Tried to display non-existant panel: '.$panelName);
	}
	
	/**
	 * Displays all errors of a specific panel
	 * @param $panelName the name of the panel
	 */
	public function displayErrorsForPanel($panelName) {
		if ($this->hasPanel($panelName))
			$this->$panelName->displayErrors();
	}
	
	/**
	 * Displays a label for a child panel. The label uses the titel of the specified
	 * panel. It can indicate if the panel has any errors.
	 * If the panel is mandatory an asterisk will be displayed after the panels
	 * title.
	 * @param $panelName string the name of the child panel for which to display
	 * the label
	 * @param $additionalCSSClasses adds the given css classes to the label
	 */
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
	
	/**
	 * Adds a child panel to this panel.
	 * @param $panel GUI_Panel the panel to be added
	 * @param $toBeginning bool by default the child panel is added to the end of the
	 * panel list. In some cases (e.g. if you iterate over the panel list) it
	 * can be useful to add a panel to the beginning of the list instead.
	 */
	public function addPanel(GUI_Panel $panel, $toBeginning = false) {
		if ($this->hasPanel($panel->getName()))
			throw new Exception('Panel names must be unique; a panel with that name already exists: '.$panel->getName());

		// TODO its probably better to rename all attributes (e.g. $name to $_name) so that it's
		// quite unlikely that a panels name is equal to the name of an attribute.
		// -> the following check could be removed then.
		if (!$this->hasPanel($panel->getName()) && isset($this->{$panel->getName()}))
			throw new Exception('Panel name is not allowed (already used internally): '.$panel->getName());
		
		$panel->setParent($this);
		$panel->beforeInit();
		$panel->init();

		if (!$toBeginning)
			$this->panels[$panel->getName()] = $panel;
		else
			$this->panels = array($panel->getName() => $panel) + $this->panels;
		if ($panel instanceof GUI_Control_Submitbutton) {
			$this->submittable = true;
		}
	}
	
	/**
	 * Removes a child panel from this panel.
	 * @param $panel GUI_Panel the panel to be removed
	 */
	public function removePanel(GUI_Panel $panel) {
		$panel->setParent(null);
		unset($this->panels[$panel->getName()]);
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
	
	/**
	 * Displays a list of errors associated with this panel
	 */
	public function displayErrors() {
		echo '<ul class="core_common_error_list">';
		if ($this->errors)
			echo '<li>'.implode('</li><li>', $this->errors).'</li>';
		echo '</ul>';
	}
	
	/**
	 * Displays all messages of this panel
	 */
	public function displayMessages() {
		$this->displayErrors();
		echo '<div class="core_common_success">'.$this->successMessage.'</div>';
	}
	
	/**
	 * @return string a unique id used to identify this panel
	 */
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
			foreach ($panel->validate() as $error)
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
	
	/**
	 * Removes all errors from this panel
	 */
	public function removeErrors() {
		$this->errors = array();
	}
	
	protected function executeCallbacks() {
		foreach ($this->panels as $panel) {
			$panel->executeCallbacks();
		}
	}
	
	/**
	 * Executes the given callback for this panel and all child panels
	 * Callback receives the panel as first parameter
	 * @param $callback
	 */
	public function walkRecursive($callback) {
		call_user_func($callback, $this);
		foreach ($this->panels as $panel) {
			call_user_func($callback, $panel);
			$panel->walkRecursive($callback);
		}
	}
	
	// CALLBACKS ---------------------------------------------------------------
	public function beforeInit() {
//		foreach ($this->panels as $panel)
//			$panel->beforeInit();
	}
	
	/**
	 * You probably don't want to override the constructor if it's not neccessary,
	 * right? Well, then override this function instead, please.
	 */
	public function init() {
		// callback
	}
	
	/**
	 * Executed after init and before display.
	 * Executes validators and callbacks.
	 * NOTE: this method operates on all added panels. If you overwrite it and
	 * add controls in your method, you need to call parent::afterInit() AFTER
	 * adding your controls, otherwhise their callbacks won't work.
	 * You CAN add normal panels that just display something after afterInit().
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
				$this->addJS(sprintf('$().ready(function() {$("#%s").validate({errorClass: "core_common_error", wrapper: "div class=\"core_common_error_js_wrapper\"", invalidHandler: function(form, validator) {hasBeenSubmitted = false;}}); %s});', $this->getID(), $validators));
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
		$panelName = Text::camelCaseToUnderscore($panelName);
		if (array_key_exists($panelName, $this->panels))
			return $this->panels[$panelName];
		else
			throw new CORE_Exception('Child panel does not exist: '.$panelName);
	}
	
	public function getName() {
		return $this->_name;
	}
	
	protected function setName($name) {
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
	
	public function setSuccessMessage($successMessage) {
		$this->successMessage = $successMessage;
		// TODO use lambda-function with PHP 5.3
		$this->walkRecursive(array('GUI_Control', 'resetValueFunction'));
	}
	
	/**
	 * @return GUI_Panel
	 */
	public function getParent() {
		return $this->parent;
	}
	
	protected function setParent(GUI_Panel $parent = null) {
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
	
	public function hasMessages() {
		return (!empty($this->errors) || $this->successMessage);
	}
	
	/**
	 * Just a shorter version of a common task
	 * @return Module the currently active module
	 */
	public function getModule() {
		return Router::get()->getCurrentModule();
	}
	
	protected function hasPanel($panelName) {
		return array_key_exists($panelName, $this->panels);
	}
	
	public function isSubmittable() {
		return $this->submittable;
	}
	
	public function addJS($js) {
		if (Router::get()->getRequestMode() == Router::REQUESTMODE_AJAX)
			$this->js .= $js;
		else
			$this->getModule()->addJsAfterContent($js);
	}
	
	public function getJS() {
		return $this->js;
	}
}

?>