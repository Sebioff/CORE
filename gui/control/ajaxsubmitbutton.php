<?php

/**
 * Submits the form using ajax. Updates the form.
 */
class GUI_Control_AjaxSubmitButton extends GUI_Control_SubmitButton {
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
		
		$this->getModule()->addJsAfterContent(sprintf('
		$("#%s").ajaxify({
			"append": "",
			"contentType": "application/x-www-form-urlencoded",
			"data": {
				"core_ajax": "1",
				"refreshPanels": "%s",
			},
			"update": "#%s",
			"buttons": "#%s"
		});
		', $formPanel->getID(), $formPanel->getID(), $formPanel->getID(), $this->getID()));
	}
}

?>