<?php

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
		$totalRefreshPanelsIDs[] = $formPanel->getID();
		$totalRefreshPanelsString = implode(',', $totalRefreshPanelsIDs);
		$this->addJS(sprintf('
			$("#%1$s").ajaxify({
				"append": "",
				"contentType": "application/x-www-form-urlencoded",
				'.
				(($refreshPanelsIDs) ?
					'
					"dataFilter": function(data, type) {
						var panels = $.core.extractPanels(data, new Array("'.implode('", "', $totalRefreshPanelsIDs).'"));
						for (panelName in panels) {
							if (panelName == "%1$s")
								continue;
							$("#" + panelName).replaceWith(panels[panelName]);
						}
					
						return panels["%1$s"];
					},
					'
					:
					''
				)
				.'
				"data": {
					"core_ajax": "1",
					"refreshPanels": "%2$s",
				},
				"buttons": "#%3$s"
			});
		', $formPanel->getID(), $totalRefreshPanelsString, $this->getID()));
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