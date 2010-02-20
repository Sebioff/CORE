<?php

/**
 * Provides functions for common text manipulation tasks.
 */
abstract class Text {
	/**
	 * Example: some_string -> someString
	 * @param $string
	 * @param $ucfirst true if the first character should be uppercase
	 */
	public static function underscoreToCamelCase($string, $ucfirst = false) {
		$camelCase = ucwords(strtr(trim($string), '_', ' '));
		if (!$ucfirst)
			$camelCase[0] = Text::toLowerCase($camelCase[0]);
		return str_replace(' ', '', $camelCase);
	}
    
	/**
	 * Example: SomeString -> some_string
	 * @param $string
	 */
	public static function camelCaseToUnderscore($string) {
		return strtolower(preg_replace(array('/[^A-Z^a-z^0-9^\/]+/','/([a-z\d])([A-Z])/','/([A-Z]+)([A-Z][a-z])/'), array('_','\1_\2','\1_\2'), $string));
	}
	
	public static function escapeHTML($string) {
		return htmlspecialchars($string, ENT_COMPAT, 'UTF-8');
	}
	
	public static function toLowerCase($string) {
		return mb_strtolower($string, 'UTF-8');
	}
	
	public static function toUpperCase($string) {
		return mb_strtoupper($string, 'UTF-8');
	}
	
	public static function length($string) {
		return mb_strlen($string, 'UTF-8');
	}
	
	public static function format($string) {
		$search = array();
		$search[] = '=http://([a-z0-9\.\?=\-_/#]+)=ism';
		$replace = array();
		$replace[] = '<a href="http://$1" class="core_gui_link">http://$1</a>';
		return nl2br(preg_replace($search, $replace, $string));
	}
}

?>