<?php

/**
 * Module is an abstraction for a page
 */
class Module extends Scriptlet {
	public $contentPanel = 'GUI_Panel';
	
	protected $mainPanel = 'GUI_Panel_Main';
	
	private $jsRouteReferences = array();
	private $cssRouteReferences = array();
	private $metaTags = array();
	private $submodules = array();
	private $jsAfterContent = '';
	
	public function __construct($name) {
		parent::__construct($name);
		$this->onConstruct();
	}
	
	// CUSTOM METHODS ----------------------------------------------------------
	public function beforeInit() {
		$this->addJsRouteReference('core_js', 'jquery/jquery.js');
		$this->addJsRouteReference('core_js', 'core.js');
		$this->contentPanel = new $this->contentPanel($this->getName().'_content');
		$this->mainPanel = new $this->mainPanel('main', $this);
		$this->mainPanel->addClasses($this->getName().'_main');
		$this->mainPanel->addPanel($this->contentPanel);
		$this->mainPanel->beforeInit();
	}
	
	public function init() {
		$this->mainPanel->init();
	}
	
	public function addSubmodule(Scriptlet $submodule) {
		$this->submodules[$submodule->getRouteName()] = $submodule;
		$submodule->setParent($this);
	}
	
	/**
	 * @return Module
	 */
	// TODO: use $moduleName here instead of routename
	// change getSubmoduleByName to getSubmoduleByRouteName
	public function getSubmodule($moduleRouteName) {
		if (isset($this->submodules[$moduleRouteName]) && $this->submodules[$moduleRouteName]->checkPrivileges())
			return $this->submodules[$moduleRouteName];
		else
			return null;
	}
	
	/**
	 * @return Module
	 */
	public function getSubmoduleByName($moduleName) {
		foreach ($this->submodules as $submodule) {
			if ($submodule->getName() == $moduleName) {
				if ($submodule->checkPrivileges())
					return $submodule;
				else
					return null;
			}
		}
		return null;
	}
	
	public function getAllSubmodules() {
		return $this->submodules;
	}	
	
	public function hasSubmodule($moduleRouteName) {
		return (isset($this->submodules[$moduleRouteName]) && $this->submodules[$moduleRouteName]->checkPrivileges());
	}

	public function hasSubmodules() {
		return !empty($this->submodules);
	}
	
	public function afterInit() {
		$this->mainPanel->afterInit();
	}
	
	public function display() {
		$this->mainPanel->displayContent();
	}
	
	/**
	 * Adds a reference to a .js file
	 * @param $routeName the name of a static route, as e.g. defined in routes.php
	 * @param $path the name of your .js file
	 */
	public function addJsRouteReference($routeName, $path) {
		$this->jsRouteReferences[$routeName.$path] = Router::get()->getStaticRoute($routeName, $path);
	}
	
	public function getJsRouteReferences() {
		return $this->jsRouteReferences;
	}
	
	/**
	 * Adds JavaScript to the end of the page.
	 */
	public function addJsAfterContent($js) {
		$this->jsAfterContent .= $js;
	}
	
	public function getJsAfterContent() {
		return $this->jsAfterContent;
	}
	
	/**
	 * Adds a reference to a .css file
	 * @param $routeName the name of a static route, as e.g. defined in routes.php
	 * @param $path the name of your .css file
	 */
	public function addCssRouteReference($routeName, $path) {
		$this->cssRouteReferences[$routeName.$path] = Router::get()->getStaticRoute($routeName, $path);
	}
	
	public function getCssRouteReferences() {
		return $this->cssRouteReferences;
	}
	
	/**
	 * Redirects to the specified url after an amout of time, using JavaScript
	 * @param $url the url to redirect to
	 * @param $timeOffset the amount of time in milliseconds after which to redirect
	 */
	public function jsRedirect($url, $timeOffset = 0) {
		$this->addJsAfterContent(sprintf('setTimeout(function() {window.location=\'%s\';}, %d);', $url, $timeOffset));
	}
	
	/**
	 * Called as soon as the module is constructed.
	 * Override this callback if you want to add additional functionality to the
	 * constructor, without having to override it (-> you don't need to copy all
	 * the parameters).
	 */
	public function onConstruct() {
		// callback
	}
	
	/**
	 * Override this if you want to restrict access to this module.
	 * @return boolean true if acccess is granted, false otherwhise
	 */
	public function checkPrivileges() {
		return true;
	}
	
	// GETTERS / SETTERS -------------------------------------------------------
	public function getMetaTags() {
		return $this->metaTags;
	}
	
	public function setMetaTag($name, $content) {
		$this->metaTags[$name] = Text::escapeHTML($content);
	}
}

?>