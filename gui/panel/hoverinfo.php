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

class GUI_Panel_HoverInfo extends GUI_Panel {
	private $text = '';
	private $hoverText = '';
	private $enableLocking = false;
	private $enableAjax = false;
	private $ajaxCallback = array();
	
	public function __construct($name, $text, $hoverText) {
		parent::__construct($name);
		
		$this->text = $text;
		$this->hoverText = $hoverText;
		
		$this->setTemplate(dirname(__FILE__).'/hoverinfo.tpl');
		$this->addClasses('core_gui_hoverinfo_panel');
	}
	
	public function afterInit() {
		parent::afterInit();
		
		$module = Router::get()->getCurrentModule();
		$module->addJsRouteReference('core_js', 'panel/hoverinfo.js');
		$this->addJS(sprintf('new GUI_Panel_HoverInfo("%s", "%s", "%s", "%s");', $this->getID(), $this->getHoverText(), $this->enableLocking, $this->enableAjax ? $this->getAjaxID() : ''));
	}
	
	public function __toString() {
		$this->beforeDisplay();
		return $this->render();
	}
	
	public function ajaxOnHover() {
		return call_user_func($this->ajaxCallback);
	}
	
	// GETTERS / SETTERS -------------------------------------------------------
	public function setText($text) {
		$this->text = $text;
	}
	
	public function getText() {
		return $this->text;
	}
	
	public function setHoverText($hoverText) {
		$this->hoverText = $hoverText;
	}
	
	public function getHoverText() {
		return $this->hoverText;
	}
	
	public function enableAjax($enableAjax, array $ajaxCallback = array()) {
		if ($enableAjax && !$ajaxCallback)
			throw new Core_Exception('A callback is needed for enabling ajax');
		$this->enableAjax = $enableAjax;
		$this->ajaxCallback = $ajaxCallback;
	}
	
	public function enableLocking($enableLocking = true) {
		$this->enableLocking = $enableLocking;
	}
}

?>