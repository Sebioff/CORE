<?php
	// TODO PWO: rename?
	// TODO PWO: coding style!
	class Language_Scriptlet {
		private static $instance = null;
		public $protocol = 'http';
		private  $language = null;
		private  $languages = null;
		private  $defaultLanguage = null;
		
		private function __construct() {
			// Singleton
		}
		
		/**
		 * sets default params of the language module
		 * @return boolean
		 */
		// TODO PWO: init() needed or could this be done in the constructor as well?
		public function init() {
			$this->setLanguages(array('de', 'en'));
			$this->setDefaultLanguage('de');
		}
		
		// TODO PWO: setLanguages/setLanguage do quite different things, but the
		// names are really similiar. rename to setCurrentLanguage or setAvailableLanguages or sth
		public function setLanguages(array $languages) {
			$this->languages=$languages;
		}
		
		public function setDefaultLanguage($language) {
			$this->defaultLanguage=$language;
			if(!$this->language)
				$this->language=$language;
		}
		
		public function getLanguage() {
			return $this->language;
		}
		
		public function setLanguage($language) {
			if(in_array($language, $this->languages)) {
				$this->language=$language;
			}
			else
				throw new Core_Exception('The language you want to switch to doesn\'t exist: '.$language);
		}
		
		public function switchToDefaultLanguage() {
			$protocol='http';
			$serverName=$_SERVER['SERVER_NAME'];
			$redirectUrl=$_SERVER['REDIRECT_URL'];
			$url=sprintf('%s://%s/%s%s', $protocol, $serverName, $this->defaultLanguage, $redirectUrl);
			Scriptlet::redirect($url);
		}
		
		public function isLanguageParam($string) {
			return in_array($string, $this->languages);
		}
		
		public static function get() {
			return (self::$instance) ? self::$instance : self::$instance = new self();
		}
	}
?>