<?php

// TODO PWO: coding style; move to core or gui?
class Scriptlet {
	public static function redirect($url) {
    header("Status: 301 Moved Permanently");
    header('Location:'.$url);
	}
}

?>