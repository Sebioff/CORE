<?php

abstract class Text {
	public static function underscoreToCamelCase($string)
    {
      $camelCase=ucwords(strtr(trim($string), '_', ' '));
      $camelCase[0]=strtolower($camelCase[0]);
      return str_replace(' ', '', $camelCase);
    }
    
	public static function camelCaseToUnderscore($string) {
		return strtolower(preg_replace(array('/[^A-Z^a-z^0-9^\/]+/','/([a-z\d])([A-Z])/','/([A-Z]+)([A-Z][a-z])/'), array('_','\1_\2','\1_\2'), $string));
	}
	
	public static function escapeHTML($string) {
		return htmlspecialchars($string, ENT_COMPAT, 'UTF-8');
	}
}

?>