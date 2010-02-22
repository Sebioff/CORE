<?php

class GUI_Control_Slider extends GUI_Control {
	private $slideJS = '';
	
	public function init() {
		parent::init();
		
		$this->setTemplate(dirname(__FILE__).'/slider.tpl');
		$this->getModule()->addJsRouteReference('core_js', '/jquery/jquery-ui.js');
		$this->getModule()->addCssRouteReference('core_jquery_css', '/smoothness/jquery-ui.css');
		$this->addPanel($valueBox = new GUI_Control_Digitbox('valuebox'));
		$valueBox->setAttribute('style', 'display:none;');
	}
	
	public function beforeDisplay() {
		parent::beforeDisplay();
		
		$this->addJS(
			str_replace(
				array("\r\n", "\r", "\n", "\t"), " ", "
				$().ready(
					function() {
						$('#".$this->getID()."').slider(
							{
								slide: function(event, ui) {
									$('#".$this->valuebox->getID()."').val(ui.value);
									".$this->slideJS."
								}
							}
						);
					}
				);
			")
		);
	}
	
	public function getValue() {
		return $this->valuebox->getValue();
	}
	
	public function addOnSlideJS($js) {
		$this->slideJS .= $js;
	}
}
?>