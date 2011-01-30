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