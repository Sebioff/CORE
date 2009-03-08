<?php

class HTMLTidy {
	public static function tidy() {
		$tidy = new tidy();
		$config = array(
			'indent-spaces' => 2,
			'indent' => true,
		    'wrap' => 160,
			'output-xhtml' => true,
			'drop-proprietary-attributes' => true,
			'drop-empty-paras' => true
		);
		$tidy->parseString(ob_get_clean(), $config);
		ob_start();
		echo $tidy;
		
		// TODO ugh. JS can't be outputted using the Module's functions, since
		// Module::display() has already been called when tidy is called...
		if ($tidy->errorBuffer) {
			echo '<script type="text/javascript" src="./../../CORE/www/js/jquery/jquery.js"></script>';
			echo '<script type="text/javascript" src="./../../CORE/www/js/jquery/jquery-ui.js"></script>';
			echo '<script>$(document).ready(function(){$("#core_htmltidy_errors").draggable().mouseover(function(){$(this).css("opacity", "0.25");}).mouseout(function(){$(this).css("opacity", "1");});});</script>';
			echo '<div id="core_htmltidy_errors" style="cursor:move;display:inline-block; position:absolute;top:0px;left:0px;z-index:1000;background-color:red;border:1px solid black;"><pre style="margin:5px;">';
			echo Text::escapeHTML($tidy->errorBuffer);
			echo '</pre></div>';
		}
	}
}

?>