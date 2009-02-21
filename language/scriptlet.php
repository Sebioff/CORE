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
	// TODO PWO: is init() needed or could this be done in the constructor as well?
	// TODO PWO: make available languages/default language customizable
	public function init() {
		$this->setAvailableLanguages(array('de', 'en'));
		$this->setDefaultLanguage('de');
	}
	
	public function setAvailableLanguages(array $languages) {
		$this->availableLanguages = $languages;
	}
	
	public function setDefaultLanguage($language) {
		$this->defaultLanguage = $language;
	}
	
	public function getCurrentLanguage() {
		if($this->currentLanguage)
			return $this->currentLanguage;
		else
			return $this->defaultLanguage;
	}
	
	public function setCurrentLanguage($language) {
		if(in_array($language, $this->availableLanguages)) {
			$this->currentLanguage = $language;
		}
		else
			throw new Core_Exception('The language you want to switch to doesn\'t exist: '.$language);
	}
	
	public function switchToDefaultLanguage() {
		$protocol = ($_SERVER['HTTPS'])?'https':'http';
		$serverName = $_SERVER['SERVER_NAME'];
		$redirectUrl = $_SERVER['REDIRECT_URL'];
		$url = sprintf('%s://%s/%s%s', $protocol, $serverName, $this->defaultLanguage, $redirectUrl);
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