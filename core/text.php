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

/**
 * Provides functions for common text manipulation tasks.
 */
abstract class Text {
	public static $numberFormatDefaultDecimals = 2;
	public static $numberFormatDefaultDecimalsSeparator = ',';
	public static $numberFormatDefaultThousandsSeparator = '.';
	
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
	
	public static function formatNumber($number, $decimals = null, $decimalsSeparator = null, $thousandsSeparator = null) {
		if ($decimals === null)
			$decimals = self::$numberFormatDefaultDecimals;
		if ($decimalsSeparator === null)
			$decimalsSeparator = self::$numberFormatDefaultDecimalsSeparator;
		if ($thousandsSeparator === null)
			$thousandsSeparator = self::$numberFormatDefaultThousandsSeparator;
		return number_format($number, (is_float($number) && round($number, $decimals) != floor($number)) ? $decimals : 0, $decimalsSeparator, $thousandsSeparator);
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
}

?>