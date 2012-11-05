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