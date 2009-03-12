<?php

// TODO PWO: rename?
class Language_Scriptlet {
	private static $instance = null;
	private $currentLanguage = null;
	private $availableLanguages = null;
	private $defaultLanguage = null;
	
	private function __construct() {
		// Singleton
	}
	
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
	
	public function setDefaultLanguage($language) {
		$this->defaultLanguage = $language;
	}
	
	public function getCurrentLanguage() {
		if ($this->currentLanguage)
			return $this->currentLanguage;
		else
			return $this->defaultLanguage;
	}
	
	public function setCurrentLanguage($language) {
		if (in_array($language, $this->availableLanguages)) {
			$this->currentLanguage = $language;
		}
		else
			throw new Core_Exception('The language you want to switch to doesn\'t exist: '.$language);
	}
	
	/**
	 * Reloads the current route with prepended language identifier
	 */
	public function switchToDefaultLanguage() {
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
	
	public static function get() {
		return (self::$instance) ? self::$instance : self::$instance = new self();
	}
}

?>