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

class HTMLTidy {
	public static function tidy() {
		if (Router::get()->getRequestMode() != Router::REQUESTMODE_GET)
			return;
		
		$tidy = new tidy();
		$config = array(
			'indent-spaces' => 2,
			'indent' => true,
		    'wrap' => 160,
			'output-xhtml' => true,
			'drop-proprietary-attributes' => true,
			'drop-empty-paras' => true
		);
		$tidy->parseString(ob_get_clean(), $config, 'utf8');
		ob_start();
		echo $tidy;
		
		// Sadly, JS can't be outputted using the Module's functions, since
		// Module::display() has already been called when tidy is called...
		if ($tidy->errorBuffer) {
			echo '
				<script type="text/javascript">
					if (typeof jQuery == "undefined"){
						var head = document.getElementsByTagName("head")[0];
						script = document.createElement("script");
						script.id = "jQuery";
						script.type = "text/javascript";
						script.src = "'.Router::get()->getStaticRoute('core_js', 'jquery/jquery.js').'";
						head.appendChild(script);
					}
					$(function() {
						if (typeof jQuery.ui == "undefined") {
							$.getScript("'.Router::get()->getStaticRoute('core_js', 'jquery/jquery-ui.js').'", function() {
								makeDraggable();
							});
						}
						else {
							makeDraggable();
						}
					});
					function makeDraggable() {
						$("#core_htmltidy_errors").draggable().mouseover(function(){$(this).css("opacity", "0.25");}).mouseout(function(){$(this).css("opacity", "1");});
					}
				</script>
			';
			echo '<div id="core_htmltidy_errors" style="cursor:move;display:inline-block; position:absolute;top:0px;left:0px;z-index:1000;background-color:red;border:1px solid black;"><pre style="margin:5px;">';
			echo Text::escapeHTML($tidy->errorBuffer);
			echo '</pre></div>';
		}
	}
}

?>