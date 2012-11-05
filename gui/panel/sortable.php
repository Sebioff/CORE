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