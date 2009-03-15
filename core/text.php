<?php

/**
 * Provides functions for common text manipulation tasks.
 */
abstract class Text {
	public static function underscoreToCamelCase($string) {
      $camelCase = ucwords(strtr(trim($string), '_', ' '));
      $camelCase[0] = Text::toLowerCase($camelCase[0]);
      return str_replace(' ', '', $camelCase);
    }
    
	public static function camelCaseToUnderscore($string) {
		return strtolower(preg_replace(array('/[^A-Z^a-z^0-9^\/]+/','/([a-z\d])([A-Z])/','/([A-Z]+)([A-Z][a-z])/'), array('_','\1_\2','\1_\2'), $string));
	}
	
	public static function escapeHTML($string) {
		return htmlspecialchars($string, ENT_COMPAT, 'UTF-8');
	}
	
	public static function toLowerCase($string) {
		return mb_strtolower($string, 'UTF-8');
	}
	
	public static function length($string) {
		return mb_strlen($string, 'UTF-8');
	}
}

?>