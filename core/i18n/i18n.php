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
 * Class that provides methods for internationalization.
 *
 * WARNING: This requires the PHP gettext extension to be enabled!
 */
class I18N {
	private static $instance = null;

	private function __construct() {
		// singleton
		// set language
		setlocale(5, Language_Scriptlet::get()->getCurrentLanguage());
		// set translation file folder
		bindtextdomain(PROJECT_NAME, PROJECT_PATH.'/translations');
		// set encoding
		bind_textdomain_codeset(PROJECT_NAME, 'UTF-8');
		// use domain
		textdomain(PROJECT_NAME);
	}

	// CUSTOM METHODS ----------------------------------------------------------
	/**
	 * Translates the given text into the current language
	 */
	public static function translate($text) {
		return gettext($text);
	}
	
	/**
	 * Translation that works like sprintf
	 */
	public static function translatef($text /* arg1, arg2, ... */) {
		$args = func_get_args();
		$args[0] = gettext($args[0]);
		return call_user_func_array('sprintf', $args);
	}
	
	/**
	 * Translates text that has singular/plural form
	 */
	public static function translaten($single, $plural, $number) {
		return ngettext($single, $plural, $number);
	}

	/**
	 * @return I18N
	 */
	public static function get() {
		return (self::$instance) ? self::$instance : self::$instance = new self();
	}
}

?>