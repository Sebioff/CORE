<?php

class CMS_Navigation_UrlNode extends CMS_Navigation_Node {
	private $url = null;
	
	public function __construct($url, $title, $cssClasses = array()) {
		parent::__construct($title, $cssClasses);
		$this->url = $url;
	}
	
	public function setUrl($url) {
		$this->url = $url;
	}
	
	/**
	 * @return GUI_Control_Link
	 */
	public function getLink() {
		return new GUI_Control_Link('core_navigation_node_link', $this->getTitle(), $this->url);
	}
	
	public function isActive() {
		// TODO this isn't exactly correct, e.g. think of url params
		return (Router::get()->getCurrentModule()->getUrl() == $this->url);
	}
}

?>