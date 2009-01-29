<?php
class Language_Translator
{
	public function __construct($languageModule_)
	{
		if(!($languageModule_ instanceof Language_Module))
		App::exception(WRONG_CLASS_TYPE, get_class($languageModule_));
		$this->m_languageModule=$languageModule_;
		$this->m_currentLanguage=$this->m_languageModule->getLanguage();
	}
	 
	function translate($key_)
	{
		$keys=array(
			'hallo'=>array(
			'de'=>'Willkommen',
			'fr'=>'Bienevuekp',
			'en'=>'Welcome'),
			'tach'=>array(
			'de'=>'Willkasdommen',
			'fr'=>'Bieneasdddvuekp',
			'en'=>'Welcsadsasadome',
			'cz'=>'Tschechski')
		);
		if(isset($keys[$key_]{$this->m_currentLanguage}) && $keys[$key_]{$this->m_currentLanguage} === NULL)
		App::exception(MISSING_TRANSLATION, NULL, true);
		return $keys[$key_]{$this->m_currentLanguage};
	}
	 
	// Class Variables
	private $m_currentLanguage='';
	private $m_languageModule=NULL;
}
?>