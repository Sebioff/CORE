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
 * Scriptlets are simple php scripts that are reachable under a specific route
 */
class Scriptlet {
	private $name = '';
	private $routeName = '';
	private $parent = null;
	private $submodules = array();
	private $cacheForSeconds = 0;
	
	/**
	 * @param string $name the name of this scriptlet
	 * @throws Core_Exception if an invalid name is given
	 */
	public function __construct($name) {
		if ($name != Text::toLowerCase($name))
			throw new Core_Exception('Use lowercase scriptlet names.');
			
		$this->name = $name;
		$this->routeName = $name;
	}
	
	/**
	 * @param $params associative array of url parameters
	 * @return the url to this scriptlet.
	 */
	public function getUrl(array $params = array()) {
		$route = $this->getRouteName();
		$module = $this;
		
		while ($module = $module->getParent()) {
			$route = $module->getRouteName().'/'.$route;
		}
		
		if (count(Language_Scriptlet::get()->getAvailableLanguages()) > 1)
			$route = Language_Scriptlet::get()->getCurrentLanguage().'/'.$route;
		
		$completeRoute = rtrim(PROJECT_ROOTURI.'/'.$route, '/'); // trim trailing slash on empty route
		
		foreach ($params as $param => $value) {
			$completeRoute .= '/'.$param.'_'.$value;
		}
			
		return $completeRoute;
	}
	
	/**
	 * @param $name
	 * @return string the value of the scriptlet parameter identified by $name or
	 * null if not set
	 */
	public function getParam($name) {
		$params = Router::get()->getParamsForScriptlet($this);
		if (isset($params[$name]))
			return $params[$name];
		else
			return null;
	}
	
	/**
	 * @return array of params for this scriptlet
	 */
	public function getParams() {
		return Router::get()->getParamsForScriptlet($this);
	}
	
	public function display() {
		
	}
	
	public function init() {
		
	}
	
	public function beforeInit() {
		
	}
	
	public function afterInit() {
		
	}
	
	public static function redirect($url) {
	    header('Status: 302 Moved Temporarily');
	    header('Location: '.$url);
	    exit;
	}
	
	public function addSubmodule(Scriptlet $submodule) {
		$this->submodules[$submodule->getRouteName()] = $submodule;
		$submodule->setParent($this);
	}
	
	/**
	 * @return Module
	 */
	// TODO: use $moduleName here instead of routename
	// change getSubmoduleByName to getSubmoduleByRouteName
	public function getSubmodule($moduleRouteName) {
		if (isset($this->submodules[$moduleRouteName])) {
			if (!($this->submodules[$moduleRouteName] instanceof Scriptlet_Privileged) || $this->submodules[$moduleRouteName]->checkPrivileges())
				return $this->submodules[$moduleRouteName];
		}
		else {
			return null;
		}
	}
	
	/**
	 * @return Module
	 */
	public function getSubmoduleByName($moduleName) {
		foreach ($this->submodules as $submodule) {
			if ($submodule->getName() == $moduleName) {
				if (!($submodule instanceof Scriptlet_Privileged) || $submodule->checkPrivileges())
					return $submodule;
				else
					return null;
			}
		}
		return null;
	}
	
	// TODO privilege checks are ignored here
	public function getAllSubmodules() {
		return $this->submodules;
	}
	
	public function hasSubmodule($moduleRouteName) {
		return (isset($this->submodules[$moduleRouteName]) && (!($this->submodules[$moduleRouteName] instanceof Scriptlet_Privileged) || $this->submodules[$moduleRouteName]->checkPrivileges()));
	}

	public function hasSubmodules() {
		return !empty($this->submodules);
	}
	
	/**
	 * Enables caching of this scriptlet. The content of cached scriptlets is only
	 * regenerated if the cached version is older than $cacheForSeconds seconds.
	 * Otherwise the cached version is served.
	 * The output of cached scriptlets is stored in System::getTemporaryDirectory().'/pagecache'.
	 * NOTE: This method should be called from within the scriptlets constructor.
	 * Otherwise the content might get served from the cache file, but some expensive
	 * methods of the scriptlet lifecycle such as init() might be called anyway,
	 * which kinda defeats the purpose of caching.
	 * @param int $cacheForSeconds the amount of time in seconds the output of this
	 * scriptlet should be cached.
	 */
	public function enableCaching($cacheForSeconds) {
		$this->cacheForSeconds = $cacheForSeconds;
	}
	
	public final function output() {
		if ($this->cacheForSeconds > 0) {
			$cacheFile = new IO_File(self::getPageCacheDirectory().'/'.$this->getCacheFileName());
			// content not yet cached or cache expired?
			if (!$this->canServeCachedVersion()) {
				ob_start();
				$this->display();
				$output = ob_get_clean();
				$cacheFile->open(IO_File::WRITE_NEWFILE);
				$cacheFile->write($output);
				echo $output;
			}
			// valid cached version available
			else {
				// don't send back any content if it hasn't been modified and If-Modified-Since header is set/available
				if (function_exists('getallheaders')) {
					$request = getallheaders();
					if (isset($request['If-Modified-Since'])) {
						$modifiedSince = explode(';', $request['If-Modified-Since']);
						$modifiedSince = strtotime($modifiedSince[0]);
						if ($cacheFile->getLastModifiedTime() <= $modifiedSince) {
							header('HTTP/1.1 304 Not Modified');
							header('Expires: '.gmdate('D, d M Y H:i:s', $cacheFile->getLastModifiedTime() + $this->cacheForSeconds).' GMT');
							header('Last-Modified: '.gmdate('D, d M Y H:i:s', $cacheFile->getLastModifiedTime()).' GMT');
							header('Cache-Control: max-age='.$this->cacheForSeconds);
							return;
						}
					}
				}
				// send cached content if conditional GET fails
				$cacheFile->open(IO_File::READ_PREPEND);
				echo $cacheFile->read();
			}
			clearstatcache();
			header('Expires: '.gmdate('D, d M Y H:i:s', $cacheFile->getLastModifiedTime() + $this->cacheForSeconds).' GMT');
			header('Last-Modified: '.gmdate('D, d M Y H:i:s', $cacheFile->getLastModifiedTime()).' GMT');
			header('Cache-Control: max-age='.$this->cacheForSeconds);
			$cacheFile->close();
		}
		else {
			$this->display();
		}
	}
	
	public function canServeCachedVersion() {
		if ($this->cacheForSeconds > 0) {
			clearstatcache();
			if (!file_exists(self::getPageCacheDirectory()))
				mkdir(self::getPageCacheDirectory(), 0770);
			$cacheFilePath = self::getPageCacheDirectory().'/'.$this->getCacheFileName();
			$cacheFile = new IO_File($cacheFilePath);
			return ($cacheFile->exists() && time() - $cacheFile->getLastModifiedTime() < $this->cacheForSeconds);
		}
		else {
			return false;
		}
	}
	
	/**
	 * Override e.g. if you want to cache different outputs for different users.
	 * The cache files are stored relative to the returned path of Scriptlet::getPageCacheDirectory().
	 * @return string the name of the file this scriptlets output will be cached
	 * in
	 */
	protected function getCacheFileName() {
		return md5($_SERVER['REQUEST_URI']).'.cache';
	}
	
	/**
	 * Returns the path to the directory where scriptlet output will be cached.
	 * @return string the path to the directory where scriptlet output will be cached
	 */
	public static final function getPageCacheDirectory() {
		return System::getTemporaryDirectory().'/pagecache';
	}
	
	// GETTERS / SETTERS -------------------------------------------------------
	/**
	 * @return Module
	 */
	public function getParent() {
		return $this->parent;
	}
	
	public function setParent(Scriptlet $parentModule) {
		$this->parent = $parentModule;
	}
	
	public function getRouteName() {
		return $this->routeName;
	}
	
	public function setRouteName($routeName) {
		$this->routeName = $routeName;
	}
	
	public function getName() {
		return $this->name;
	}
}

?>