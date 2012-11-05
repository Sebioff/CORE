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
 * Provides functions for handling different language versions
 */
// TODO PWO: rename?
class Language_Scriptlet {
	private static $instance = null;
	private $currentLanguage = null;
	private $availableLanguages = null;
	private $defaultLanguage = null;
	
	private function __construct() {
		// Singleton
	}
	
	// CUSTOM METHODS ----------------------------------------------------------
	
	/**
	 * sets default params of the language module
	 * @return boolean
	 */
	public function init() {
		if (count($this->getAvailableLanguages()) == 0)
			$this->setAvailableLanguages(array('de'));
		if (!$this->defaultLanguage)
			$this->setDefaultLanguage($this->availableLanguages[0]);
	}
	
	public function setAvailableLanguages(array $languages) {
		$this->availableLanguages = $languages;
	}
	
	public function getAvailableLanguages() {
		return $this->availableLanguages;
	}
	
	/**
	 * Reloads the current route with prepended default language identifier
	 */
	public function switchToDefaultLanguage() {
		$this->switchLanguage($this->defaultLanguage);
	}
	
	/**
	 * Reloads the current route for a given language
	 */
	public function switchLanguage($language) {
		$this->setCurrentLanguage($language);
		$redirectUrl = implode('/', Router::get()->getRequestParams());
		$url = sprintf('%s/%s/%s', PROJECT_ROOTURI, $this->defaultLanguage, $redirectUrl);
		Scriptlet::redirect($url);
	}
	
	/**
	 * @return true if the given string is a valid language identifier, false otherwhise
	 */
	public function isLanguageIdentifier($string) {
		return in_array($string, $this->availableLanguages);
	}
	
	// GETTERS / SETTERS -------------------------------------------------------
	public function setDefaultLanguage($language) {
		$this->defaultLanguage = $language;
	}
	
	public function getCurrentLanguage() {
		if ($this->currentLanguage)
			return $this->currentLanguage;
		else
			return $this->defaultLanguage;
	}
	
	/**
	 * Switches to the given language.
	 * @param string $language identifier for the language that sould be switched
	 * to
	 * @throws Core_Exception if the given language hasn't been made available before
	 */
	public function setCurrentLanguage($language) {
		if (in_array($language, $this->availableLanguages)) {
			$this->currentLanguage = $language;
		}
		else
			throw new Core_Exception('The language you want to switch to doesn\'t exist: '.$language);
	}
	
	/**
	 * @return Language_Scriptlet
	 */
	public static function get() {
		return (self::$instance) ? self::$instance : self::$instance = new self();
	}
}

?>