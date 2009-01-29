<?php
class Language_Module
{
	public function addLanguages($languages_)
	{
		if(is_array($languages_))
		{
			foreach($languages_ as $language)
			if(!in_array($language, $this->m_languages))
			$this->m_languages[]=$language;
		}
		else
		if(!in_array($languages_, $this->m_languages))
		$this->m_languages[]=$languages_;
	}

	// Class Variables
	private $m_languages=array();
	private $m_defaultLanguage='';
	private $m_routerModule=NULL;

	// Setter/Getter
	public function getLanguages()										{return $this->m_languages;}
	public function getLanguage()
	{
		$uri=$this->getRouterModule()->getUri();
		if(in_array($uri[0], $this->getLanguages()))
		return $uri[0];
		else
		return $this->getDefaultLanguage();
	}
	public function setRouterModule(Router $module_)
	{
		$this->m_routerModule=$module_;
	}
	public function getRouterModule()									{return $this->m_routerModule;}
	public function setDefaultLanguage($lang_)
	{
		if(!in_array($lang_, $this->getLanguages()))
		App::exception(ERROR_OCCURED, 'Switching to this default language is not possible.');
		$this->m_defaultLanguage=$lang_;
	}
	public function getDefaultLanguage()								{return $this->m_defaultLanguage;}
	public function getSwitchedLanguageUri($lang_)
	{
		if(!in_array($lang_, $this->getLanguages()))
		App::exception(ERROR_OCCURED, 'Switching to the language '.$lang_.'is not possible.');

		$uri=$this->getRouterModule()->getUri();
		if(in_array($uri[0], $this->getLanguages()))
		$uri[0]=$lang_;
		else
		{
			$uri2=$uri;
			$uri=array(0=>$lang_);
			foreach($uri2 as $uris)
			array_push($uri, $uris);
		}

		$url='http://';
		$serverName = $_SERVER['SERVER_NAME'];
		$url.=$serverName;
		foreach($this->getRouterModule()->getSubfolders() as $subfolder)
		$url.='/'.$subfolder;
		foreach($uri as $urlPart)
		$url.='/'.$urlPart;
		return $url;
	}
	public function switchLanguage($lang_=NULL)
	{
		if($lang_===NULL || !in_array($lang_, $this->getLanguages()))
		$lang_=$this->getDefaultLanguage();

		$url=$this->getSwitchedLanguageUri($lang_);

		$location = 'Location:'.$url;
		header("Status: 301 Moved Permanently");
		header($location);
	}
	public function getTranslateObject()
	{
		return new Language_Translator($this);
	}
}
?>