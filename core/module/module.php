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
 * Module is an abstraction for a page
 */
class Module extends Scriptlet {
	public $contentPanel = 'GUI_Panel';
	
	protected $mainPanel = 'GUI_Panel_Main';
	
	private $jsRouteReferences = array();
	private $cssRouteReferences = array();
	private $metaTags = array();
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
	 * Called if a module has been invalidted
	 */
	// TODO Remove with PHP 5.3 and lazy module instantiation
	public function cleanup() {
		$this->jsAfterContent = '';
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
	
	/**
	 * @return GUI_Panel the panel with the given ID or null if it doesn't exist
	 */
	public function getPanelByID($panelID) {
		$panelTree = explode('-', $panelID);
		$panelTreeCount = count($panelTree);
		$currentPanel = $this->mainPanel;
		for ($j = 1; $j < $panelTreeCount; $j++) {
			if (!$currentPanel->hasPanel($panelTree[$j])) {
				return null;
			}
			
			$currentPanel = $currentPanel->{$panelTree[$j]};
		}
		
		return $currentPanel;
	}
}

?>