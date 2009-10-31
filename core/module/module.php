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
	private $isInvalid = false;
	
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
		if (isset($this->submodules[$moduleRouteName])) {
			if (!($this->submodules[$moduleRouteName] instanceof Scriptlet_Privileged) || $this->submodules[$moduleRouteName]->checkPrivileges())
				return $this->submodules[$moduleRouteName];
		}
		else {
			return null;
		}
	}
	
	/**
	 * @return Module
	 */
	public function getSubmoduleByName($moduleName) {
		foreach ($this->submodules as $submodule) {
			if ($submodule->getName() == $moduleName) {
				if (!($submodule instanceof Scriptlet_Privileged) || $submodule->checkPrivileges())
					return $submodule;
				else
					return null;
			}
		}
		return null;
	}
	
	// TODO privilege checks are ignored here
	public function getAllSubmodules() {
		return $this->submodules;
	}
	
	public function hasSubmodule($moduleRouteName) {
		return (isset($this->submodules[$moduleRouteName]) && (!($this->submodules[$moduleRouteName] instanceof Scriptlet_Privileged) || $this->submodules[$moduleRouteName]->checkPrivileges()));
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
		$this->jsRouteReferences[$routeName.$path] = $this->applyStaticFileVersioning(Router::get()->getStaticRoute($routeName, $path));
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
		$this->cssRouteReferences[$routeName.$path] = $this->applyStaticFileVersioning(Router::get()->getStaticRoute($routeName, $path));
	}
	
	public function getCssRouteReferences() {
		return $this->cssRouteReferences;
	}
	
	/**
	 * Modifies the name of static files so that the file name is unique for
	 * each version of the project. This way it's possible to use methods like
	 * browser-side-caching without any problems, because if the project version
	 * changes (which means the static files might have changed as well) the
	 * filenames will change and thus re-caching is enforced (= "cache busting").
	 */
	private function applyStaticFileVersioning($fileName) {
		$fileNameParts = pathinfo($fileName);
		/*
		 * Apparently there can be problems regarding css/js-files with query-part.
		 * Thus, if url rewriting is available, we rewrite the filename. Otherwise
		 * we got no other chance than to use query-part-cache-busting.
		 */
		if (Router::get()->getEnableURLRewrite())
			return $fileNameParts['dirname'].'/'.$fileNameParts['filename'].'-cb'.PROJECT_VERSION.'.'.$fileNameParts['extension'];
		else
			return $fileNameParts['dirname'].'/'.$fileNameParts['filename'].'.'.$fileNameParts['extension'].'?cb='.PROJECT_VERSION;
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
	 * In some situations changes made in panel A can affect the content that should
	 * be displayed in panel B.
	 * For example, if panel A modifies values in the database in its submit-handler
	 * that have been read by panel B before in its init-method, panel B will display
	 * old data.
	 * You can fix this by using event-handlers on the appropriate containers, though
	 * this can be quite a lot of work. Another solution is calling this method
	 * after panel A modified the data, resulting in the whole module being invalidated
	 * and executed another time. Thanks to database queries being cached per
	 * page-load this usually results in near to no overhead. Considering this
	 * requires a lot less implementation-work this solution might be preferred.
	 */
	public function invalidate() {
		$this->isInvalid = true;
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
	
	// GETTERS / SETTERS -------------------------------------------------------
	public function getMetaTags() {
		return $this->metaTags;
	}
	
	public function setMetaTag($name, $content) {
		$this->metaTags[$name] = Text::escapeHTML($content);
	}

	public function isInvalid() {
		return $this->isInvalid;
	}
}

?>