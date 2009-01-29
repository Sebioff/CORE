<?php
class Router
{
	private function __construct()
	{
		$requestURI = explode('/', $_SERVER['REQUEST_URI']);
		$scriptName = explode('/',$_SERVER['SCRIPT_NAME']);

		for($i= 0;$i < sizeof($scriptName);$i++)
		{
			if ($requestURI[$i] == $scriptName[$i])
			{
				unset($requestURI[$i]);
			}
		}
		$command = array_values($requestURI);
		$this->m_uri=$command;

		$this->m_scriptlet=isset($this->m_uri[1])?$this->m_uri[1]:'';;

		for($i=2; $i<count($this->m_uri); $i+=2)
		if(isset($this->m_uri[$i+1]))
			$this->m_actions[]=array('param'=>$this->m_uri[$i], 'value'=>$this->m_uri[$i+1]);
		else
			$this->m_actions[]=array('param'=>$this->m_uri[$i]);
	}

	private function checkLanguagePrefix()
	{
		if(!$this->m_languagingEnabled)
		return true;

		if(in_array($this->getLanguagePrefix(), $this->getAvialableLanguages()))
		return true;
		else
		return false;
	}

	private function addRoute($routeName_, $target_)
	{
		$this->m_routes[$routeName_]=$target_;
	}

	// Class Variables
	private $m_uri=array();
	private $m_languageModule=NULL;
	private $m_languagingEnabled=false;
	private $m_scriptlet=NULL;
	private $m_activeScriptlet=NULL;
	private $m_actions=array();
	private $m_routes=array();
	private static $instance = null;
	// ----
	private function getLanguagePrefix()
	{
		if(!$this->m_languagingEnabled)
		return NULL;
		$uri=$this->getUri();
		return $uri[0];
	}

	private function getAvialableLanguages()
	{
		return $this->m_languageModule->getLanguages();
	}

	// Setter/Getter
	public function getUri()											        {return $this->m_uri;}
	public function setUri($uri_)								  		    	{$this->m_uri=$uri_;}
	public function getActions()                          						{return $this->m_actions;}
	public function getScriptlet()                        						{return $this->m_scriptlet;}
	public function getActiveScriptlet()                  						{return $this->m_activeScriptlet;}

	public function setLanguageModule($module_)
	{
		if(!($module_ instanceof Language_Module))
		error(WRONG_CLASS_TYPE, get_class($module_));

		$this->m_languageModule=$module_;
		$this->m_languagingEnabled=true;
		if(!$this->checkLanguagePrefix())
		$this->m_languageModule->switchLanguage();
	}

	public function getLanguageModule()                   						{return $this->m_languageModule;}

	public function getSubfolders()
	{
		$requestURI = explode('/', $_SERVER['REQUEST_URI']);

		$uri=array();
		foreach($requestURI as $requestURIPart)
			if(!in_array($requestURIPart, $this->getUri()) && strlen($requestURIPart))
				$uri[]=$requestURIPart;
		return $uri;
	}

	public function getUrlForName($scriptlet_, $params_=null)
	{
		$prefix='';
		if($this->m_languagingEnabled)
			$prefix=$this->getLanguageModule()->getLanguage();
		$suffix='';
		if(isset($params_) && is_array($params_))
			foreach($params_ as $param)
				$suffix.='/'.implode('/', $param);
		$url=sprintf("http://%s/%s%s/%s%s", $_SERVER["SERVER_NAME"], implode('/', $this->getSubfolders()).(count($this->getSubfolders())?'/':''), $prefix, $scriptlet_, $suffix);
		return $url;
	}

	// TODO why don't you give the path to the file as the method name implies?
	public function addRoutesFile($file_)
	{
		$path=App::getPathFromUnderscore($file_).'.php';
		$contents=require $path;
		foreach($contents['navigation'] as $listen=>$scriptlet)
		$this->addRoute($listen, $scriptlet);

		foreach($this->m_routes as $listen=>$scriptlet)
		{
			$listens=explode(',', $listen);
			if(in_array($this->m_scriptlet, $listens))
			{
				$this->m_activeScriptlet=$scriptlet;
				break;
			}
		}
		return true;
	}

	public static function get() {
		return (self::$instance) ? self::$instance : self::$instance = new self();
	}
}
?>