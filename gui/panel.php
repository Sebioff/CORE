<?php

/**
 * @package CORE PHP Framework
 * @copyright Copyright (C) 2012 Sebastian Mayer, Andreas Sicking, Andre Jährling
 * @license GNU/GPL, see license.txt
 * This file is part of CORE PHP Framework.
 *
 * CORE PHP Framework is free software: you can redistribute it and/or modify it
 * under the terms of the GNU General Public License as published by the Free Software
 * Foundation, either version 3 of the License, or (at your option) any later version.
 *
 * CORE PHP Framework is distributed in the hope that it will be useful, but WITHOUT
 * ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS
 * FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with CORE PHP Framework. If not, see <http://www.gnu.org/licenses/>.
 */

/**
 * A panel is an element that containts any type of content that should be displayed.
 * Panels can have child panels.
 * If one of this child panels is a submittable control such as GUI_Control_SubmitButton
 * the panel can be submitted using POST.
 * Panels can have error messages attached to them. If you add validators to a
 * panel the validator automatically attaches its error message to the panel
 * if anything is wrong.
 *
 * Magic callbacks:
 * @method void onBUTTONNAME() (executed after the panel has been submitted by a GUI_Control_SubmitButton;
 * 		BUTTONNAME equals the submitting buttons name in camel case notation)
 * @method void ajaxACTION() (can be called via ajax, e.g. using $.core.ajaxRequest; ACTION
 * 		can be chosen freely, the method name just needs to start with "ajax"; the
 * 		return value of this method will be handed back as parameter to the calling
 * 		javascript methods sucessCallback)
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
	 * @param $name string name of the panel
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
		if ($this->getJS()) {
			$this->getModule()->addJsAfterContent($this->getJS());
		}
		return ob_get_clean();
	}
	
	public function display() {
		$this->beforeDisplay();
		
		if ($this->isSubmittable()) {
			echo sprintf('<form id="%sForm" action="%s" method="post" enctype="multipart/form-data" accept-charset="UTF-8">', $this->getID(), Text::escapeHTML($_SERVER['REQUEST_URI']));
			// fix for IE not submitting button name in post data if form is submitted with enter in forms with only one input
			echo '<!--[if IE]><input type="text" style="display: none;" disabled="disabled" size="1" name="IESucks" /><![endif]-->';
		}
		
		echo $this->render();
		
		if ($this->isSubmittable()) {
			$this->addPanel($hasBeenSubmittedBox = new GUI_Control_HiddenBox('hasbeensubmitted', 1));
			$hasBeenSubmittedBox->display();
			echo '</form>';
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
	public function displayLabelForPanel($panelName, array $additionalCSSClasses = array()) {
		if (!$this->hasPanel($panelName)) {
			IO_Log::get()->warning('Tried to display label for non-existant panel: '.$panelName);
			return;
		}
		
		if ($this->$panelName->hasErrors())
			$additionalCSSClasses[] = 'core_common_error_label';
		
		$additionalCSSClasses[] = 'core_gui_label';
		echo sprintf('<label for="%s" class="%s">', $this->$panelName->getID(), implode(' ', $additionalCSSClasses));
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
	 * @throws Core_Exception if a panel with the same name already exists or the
	 * name is invalid for other reasons
	 */
	public function addPanel(GUI_Panel $panel, $toBeginning = false) {
		if ($this->hasPanel($panel->getName()))
			throw new Core_Exception('Panel names must be unique; a panel with that name already exists: '.$panel->getName());

		// TODO its probably better to rename all attributes (e.g. $name to $_name) so that it's
		// quite unlikely that a panels name is equal to the name of an attribute.
		// -> the following check could be removed then.
		if (!$this->hasPanel($panel->getName()) && isset($this->{$panel->getName()}))
			throw new Core_Exception('Panel name is not allowed (already used internally): '.$panel->getName());
		
		$panel->setParent($this);
		$panel->beforeInit();
		$panel->init();

		if (!$toBeginning)
			$this->panels[$panel->getName()] = $panel;
		else
			$this->panels = array($panel->getName() => $panel) + $this->panels;
		if ($panel instanceof GUI_Control_SubmitButton) {
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
	 * @return string a string of classes belonging to this panel, e.g. for use
	 * in html output
	 */
	public function getClassString() {
		return implode(' ', $this->classes);
	}
	
	/**
	 * @return string a string of html attributes belonging to this panel
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
	 * @return boolean true if this panel has been submitted, false otherwise
	 */
	public function hasBeenSubmitted() {
		return isset($_POST[$this->getID().'-hasbeensubmitted']);
	}
	
	/**
	 * Displays a list of errors associated with this panel
	 */
	public function displayErrors() {
		if ($this->errors) {
			echo '<ul class="core_common_error_list">';
				echo '<li>'.implode('</li><li>', $this->errors).'</li>';
			echo '</ul>';
		}
	}
	
	/**
	 * Displays all messages of this panel
	 */
	public function displayMessages() {
		$this->displayErrors();
		if ($this->successMessage)
			echo '<div class="core_common_success">'.$this->successMessage.'</div>';
	}
	
	/**
	 * (Re-)generates a page-unique ID for this panel and all child panels based
	 * on the panel tree.
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
	 * @param $message string the error message to display
	 * @param $panel GUI_Panel the panel this error belongs to
	 */
	public function addError($message, GUI_Panel $panel = null) {
		if ($panel) {
			$errorLabel = new GUI_Panel_Label('error', $panel);
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
	 * @param $callback callback
	 */
	public function walkRecursive($callback) {
		call_user_func($callback, $this);
		foreach ($this->panels as $panel) {
			$panel->walkRecursive($callback);
		}
	}
	
	// CALLBACKS ---------------------------------------------------------------
	public function beforeInit() {
//		foreach ($this->panels as $panel)
//			$panel->beforeInit();
	}
	
	/**
	 * This method is the method that you'll probably need most when creating
	 * new panel classes. It is mainly intended to be used for setting the panels
	 * template (using setTemplate()) and adding sub-panels (using addPanel()).
	 * Always add new panels inside of this method, or you might get unexpected
	 * behaviour from some methods.
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
			
		// check if this is a top-level form
		if ($this->isSubmittable()) {
			$parent = $this->getParent();
			while ($parent != null) {
				if ($parent->isSubmittable()) {
					$this->submittable = false;
					break;
				}
				$parent = $parent->getParent();
			}
		}

		if ($this->isSubmittable()) {
			if ($validators = $this->getJsValidators()) {
				$module = Router::get()->getCurrentModule();
				$module->addJsRouteReference('core_js', 'jquery/jquery.validate.js');
				$this->addJS(sprintf('$(function() {$("#%sForm").validate({errorClass: "core_common_error", wrapper: "div class=\"core_common_error_js_wrapper\"", invalidHandler: function(form, validator) {$(this).find(":input[type=\'submit\']").removeClass(\'core_gui_submittable_disabled\');}}); %s});', $this->getID(), $validators));
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
			throw new Core_Exception('Child panel does not exist: '.$panelName);
	}
	
	public function getName() {
		return $this->_name;
	}
	
	/**
	 * Sets the name of this panel. The name may not contain uppercase letters
	 * and: -,
	 * @param string $name the name of this panel
	 * @throws Core_Exception if the panel name is invalid
	 */
	protected function setName($name) {
		if (preg_match('/[-,\p{Lu}]+/', $name))
			throw new Core_Exception('Panel name may not contain uppercase letters and: -,');
		$this->_name = $name;
		$this->generateID();
	}
	
	public function getTitle() {
		return $this->title;
	}
	
	public function setTitle($title) {
		$this->title = $title;
	}
	
	/**
	 * IDs are primarily used to identify controls in HTTP
	 * requests. They can also be used for CSS styling, though note that the ID
	 * changes if the name of a parent panel of the panel is changed or the panel
	 * is used in a different context. So, for styling, CSS classes are usually
	 * preferable over this IDs.
	 * NOTE: because IDs change as the panel tree changes, you can only be sure
	 * that this method returns the final panel ID if you call it after init() or
	 * if always using the init()-methods as intended.
	 * @return string the ID of this panel
	 */
	public function getID() {
		return $this->ID;
	}
	
	/**
	 * Returns the ID for this panel which has to be used in ajax calls.
	 */
	public function getAjaxID() {
		$ajaxID = $this->getID();
		if ($this->isSubmittable())
			$ajaxID .= 'Form';
		return $ajaxID;
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
	 * @param $attribute string the attribute name
	 * @param $value string the attribute value
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
	
	public function hasPanel($panelName) {
		return array_key_exists($panelName, $this->panels);
	}
	
	public function isSubmittable() {
		return $this->submittable;
	}
	
	/**
	 * @param $js string JavaScript belonging to this panel
	 */
	public function addJS($js) {
		$this->js .= $js;
	}
	
	public function getJS() {
		return $this->js;
	}
	
	public function getPanels() {
		return $this->panels;
	}
}

?>