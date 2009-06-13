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
	 * @return int the number of the currently opened page
	 */
	public function getPage() {
		if ($this->getModule()->getParam('page')) {
			$page = (int)$this->getModule()->getParam('page');
			if ($page > $this->getPageCount())
				$page = $this->getPageCount();
			return $page;
		}
		else
			return 1;
	}
	
	/**
	 * @return int the amount of available pages
	 */
	public function getPageCount() {
		return ceil($this->getContainer()->count() / $this->getItemsPerPage());
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
		$this->setTemplate(dirname(__FILE__).'/pageview_pages.tpl');
		$this->addClasses('core_gui_pageview_pages');
	}
	
	public function init() {
		parent::init();
		
		for ($i = 1; $i <= $this->getPageView()->getPageCount(); $i++) {
			$url = $this->getModule()->getUrl(array('page' => $i));
			$this->addPanel($pageLink = new GUI_Control_Link($i, $i, $url));
			if ($i == $this->getPageView()->getPage())
				$pageLink->addClasses('current_page');
		}
	}
	
	// GETTERS / SETTERS -------------------------------------------------------
	/**
	 * @return GUI_Panel_PageView
	 */
	public function getPageView() {
		return $this->pageView;
	}
}

?>