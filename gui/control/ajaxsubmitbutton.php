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
 * Submits the form using ajax. Updates the form.
 */
class GUI_Control_AjaxSubmitButton extends GUI_Control_SubmitButton {
	private $refreshPanels = array();
	
	public function init() {
		parent::init();
		
		$this->getModule()->addJsRouteReference('core_js', '/jquery/jquery.ajaxify.js');
	}
	
	public function afterInit() {
		parent::afterInit();
		
		$formPanel = $this->getParent();
		while (!$formPanel->isSubmittable()) {
			$formPanel = $formPanel->getParent();
		}
		
		$refreshPanelsIDs = array();
		foreach ($this->refreshPanels as $panel)
			$refreshPanelsIDs[] = $panel->getID();
		$totalRefreshPanelsIDs = $refreshPanelsIDs;
		$totalRefreshPanelsIDs[] = $formPanel->getAjaxID();
		$totalRefreshPanelsString = implode(',', $totalRefreshPanelsIDs);
		$this->addJS(sprintf('
			$("#%1$s")
			.bind("submit", $.core.formPreventDoubleSubmitEventHandler)
			.ajaxify({
				"append": "",
				"contentType": "application/x-www-form-urlencoded; charset=UTF-8",
				"dataFilter": function(data, type) {
					'.(($refreshPanelsIDs) ?
						'var panelNames = new Array("'.implode('", "', $totalRefreshPanelsIDs).'");'
					:
						'var panelNames = ["%1$s"];'
					).'
					$.core.replacePanels(data, panelNames);
				},
				"data": {
					"core_ajax": "1",
					"core_ajax_method": "display",
					"refreshPanels": "%2$s"
				},
				"buttons": "#%3$s",
				"error": function(xhr) {
					alert(xhr.responseText);
					$("#%3$s").removeClass("core_gui_submittable_disabled");
				}
			});
		', $formPanel->getAjaxID(), $totalRefreshPanelsString, $this->getID()));
	}
	
	// GETTERS / SETTERS -------------------------------------------------------
	/**
	 * Adds a panel that needs to be refreshed after submitting this button.
	 */
	public function addRefreshPanels(GUI_Panel $panel) {
		$this->refreshPanels[] = $panel;
	}
}

?>