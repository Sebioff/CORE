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
 * Delegates unknown methods to the currently active module.
 */
class GUI_Panel_Main extends GUI_Panel {
	private $module = null;
	private $pageTitle = '';
	
	public function __construct($name, Module $module) {
		parent::__construct($name);
		
		$this->module = $module;
		$this->setTemplate(dirname(__FILE__).'/main.tpl');
	}
	
	public function beforeDisplay() {
		$this->module->contentPanel->beforeDisplay();
	}
	
	public function displayPage() {
		$this->module->contentPanel->display();
	}
	
	public function displayContent() {
		if (Router::get()->getRequestMode() != Router::REQUESTMODE_AJAX) {
			require dirname(__FILE__).'/page.tpl';
		}
		else {
			require dirname(__FILE__).'/page_ajax.tpl';
		}
	}
	
	/**
	 * Displays the content of a page that is loaded via Ajax
	 * -> displays only specific panels
	 */
	public function displayContentAjax() {
		$panels = explode(',', $_POST['refreshPanels']);
		$panelsCount = count($panels);
		for ($i = 0; $i < $panelsCount; $i++) {
			// the ajax ID of submittable panels ends with "Form"
			if (preg_match('/Form$/', $panels[$i]))
				$panels[$i] = substr($panels[$i], 0, -4);
			if ($currentPanel = $this->getModule()->getPanelByID($panels[$i])) {
				echo $currentPanel->display();
			}
		}
	}
	
	// GETTERS / SETTERS -------------------------------------------------------
	public function setPageTitle($pageTitle) {
		$this->pageTitle = $pageTitle;
	}
	
	public function getPageTitle() {
		return $this->pageTitle;
	}
	
	/**
	 * Delegate unknown functions to the currently active module.
	 */
	public function __call($name, $params) {
		return call_user_func_array(array($this->module, $name), $params);
	}
}

?>