<?php

/**
 * Provides functions for common text manipulation tasks.
 */
abstract class Text {
	/**
	 * Example: some_string -> someString
	 * @param $string
	 * @param $ucfirst boolean true if the first character should be uppercase
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
		$sym = '(?:[,;.:"\'!?()\[\]{}-]|&amp;|&quot;|&#039;|&[gl]t;)*';
		$beg = '((?:^|>|\s)'.$sym.'?)';
		$end = '('.$sym.'(?:\s|<|$))';
		$search = array();
		$search[] = '/'.$beg.'((?:https?|ftp|news|telnet|ed2k):[\S]+)'.$end.'/Ui';
		$search[] = '/'.$beg.'(www\.[\S]+)'.$end.'/Ui';
		$search[] = '/'.$beg.'(ftp\.[\S]+)'.$end.'/Ui';
		$search[] = '/'.$beg.'([^\s\/]+@[a-z0-9.-]+\.[a-z]{2,6})'.$end.'/Ui';
		$replace = array();
		$replace[] = '\\1<a href="\\2" target="_blank">\\2</a>\\3';
		$replace[] = '\\1<a href="http://\\2" target="_blank">\\2</a>\\3';
		$replace[] = '\\1<a href="ftp://\\2" target="_blank">\\2</a>\\3';
		$replace[] = '\\1<a href="mailto:\\2" target="_blank" onClick="return mailto(this.href)">\\2</a>\\3';
		return nl2br(preg_replace($search, $replace, $string));
	}
	
	/**
	 * Shortens a string to a given $maxLength. $appendText is appended to the
	 * end of the string if provided and if the string is longer than $maxLength
	 * (note that the string is shortened in a way so that it is not longer than
	 * $maxLength in total, that is, with $appendText counted in).
	 * @param string $string the string to shorten
	 * @param int $maxLength the maximum length the final string should have
	 * @param string $appendText a text that is appended to the string if it is
	 * longer than $maxLength; the length of $appendText may not exceed $maxLengh
	 */
	public static function shorten($string, $maxLength, $appendText = '') {
		if (self::length($string) > $maxLength) {
			return mb_substr($string, 0, $maxLength - self::length($appendText), 'UTF-8').$appendText;
		}
		else {
			return $string;
		}
	}
	
	public static function replace($search, $replace, $subject, &$count = null) {
		return str_replace($search, $replace, $subject, $count);
	}
}

?>