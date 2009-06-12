<?php

/**
 * A panel that has methods to display its content on different pages
 */
class GUI_Panel_PageView extends GUI_Panel {
	private $itemsPerPage = 10;
	private $container = null;
	
	public function __construct($name, DB_Container $container, $title = '') {
		parent::__construct($name, $title);

		$this->container = $container;
		$this->setTemplate(dirname(__FILE__).'/pageview.tpl');
		$this->addClasses('core_gui_pageview');
	}
	
	public function init() {
		$this->addPanel(new GUI_Panel_PageView_Pages($this));
	}
	
	public function getOptions() {
		$options = array();
		$options['limit'] = $this->getItemsPerPage();
		$options['offset'] = ($this->getPage() - 1) * $this->getItemsPerPage();
		
		return $options;
	}
	
	/**
	 * @return DB_Container
	 */
//	public function getFilteredContainer() {
//		return $this->getContainer()->select($this->getOptions());
//	}
	
	public function getPage() {
		if ($this->getModule()->getParam('page'))
			return (int)$this->getModule()->getParam('page');
		else
			return 1;
	}
	
	// GETTERS / SETTERS -------------------------------------------------------
	public function setItemsPerPage($itemsPerPage) {
		$this->itemsPerPage = $itemsPerPage;
	}
	
	public function getItemsPerPage() {
		return $this->itemsPerPage;
	}
	
	/**
	 * @return DB_Container
	 */
	public function getContainer() {
		return $this->container;
	}
}

/**
 * A panel that displays the page numbers and buttons to switch pages of a
 * GUI_Panel_PageView
 */
class GUI_Panel_PageView_Pages extends GUI_Panel {
	private $pageView = null;
	
	public function __construct(GUI_Panel_PageView $pageView) {
		parent::__construct('pages');
		
		$this->pageView = $pageView;
	}
	
	public function init() {
		parent::init();
		
		$pageCount = ceil($this->pageView->getContainer()->count() / $this->pageView->getItemsPerPage());
		for ($i = 1; $i <= $pageCount; $i++) {
			$url = $this->getModule()->getUrl(array('page' => $i));
			$this->addPanel(new GUI_Control_Link($i, $i, $url));
		}
	}
}

?>