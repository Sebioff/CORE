<?php

/**
 * A panel that has methods to display its content on different pages
 */
class GUI_Panel_PageView extends GUI_Panel {
	private $itemsPerPage = 10;
	private $container = null;
	private $enableAjax = false;
	
	public function __construct($name, DB_Container $container, $title = '') {
		parent::__construct($name, $title);

		$this->container = $container;
		$this->setTemplate(dirname(__FILE__).'/pageview.tpl');
		$this->addClasses('core_gui_pageview');
	}
	
	public function init() {
		if ($this->enableAjax)
			$this->getModule()->addJsRouteReference('core_js', '/jquery/jquery.ajaxify.js');
		
		$this->addPanel(new GUI_Panel_PageView_Pages($this));
	}
	
	public function afterInit() {
		parent::afterInit();
		
		if ($this->enableAjax) {
			$this->addJS(sprintf('
				$("#%1$s-pages a").ajaxify({
					"append": "",
					"type": "POST",
					"dataFilter": function(data, type) {
						var panelNames = ["%1$s"];
						$.core.replacePanels(data, panelNames);
					},
					"data": {
						"core_ajax": "1",
						"refreshPanels": "%1$s"
					},
					"error": function(xhr) {
						alert(xhr.responseText);
					}
				});
			', $this->getID()));
		}
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
		if ($this->getModule()->getParam($this->getName().'-page')) {
			$page = (int)$this->getModule()->getParam($this->getName().'-page');
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
		$pageCount = ceil($this->getContainer()->count() / $this->getItemsPerPage());
		if ($pageCount <= 0)
			$pageCount = 1;
		return (int)$pageCount;
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
	
	/**
	 * @param boolean $enableAjax true to enable ajax for switching pages (default),
	 * false to disable
	 */
	public function enableAjax($enableAjax = true) {
		$this->enableAjax = $enableAjax;
	}
}

/**
 * A panel that displays the page numbers and buttons to switch pages of a
 * GUI_Panel_PageView
 */
class GUI_Panel_PageView_Pages extends GUI_Panel {
	private $pageView = null;
	
	public function __construct(GUI_Panel_PageView $pageView) {
		parent::__construct('pages', 'Seite');
		
		$this->pageView = $pageView;
		$this->setTemplate(dirname(__FILE__).'/pageview_pages.tpl');
		$this->addClasses('core_gui_pageview_pages');
	}
	
	public function init() {
		parent::init();

		$pageCount = $this->getPageView()->getPageCount();
		$actualPage = $this->getPageView()->getPage();
		$lastDisplayed = 0;
		for ($i = 1; $i <= $pageCount; $i++) {
			if ($i == $lastDisplayed + 2) {
				$this->addPanel(new GUI_Panel_Text('dots'.$i, '...'));
			}
			switch (true) {
				//display first 3 pages
				case ($i <= 3):
				//display actual page +- 1
				case ($i >= $actualPage - 1 && $i <= $actualPage + 1):
				//display last 3 pages
				case ($i > $pageCount - 3):
					$params = $this->getModule()->getParams();
					$params[$this->getPageView()->getName().'-page'] = $i;
					$url = $this->getModule()->getUrl($params);
					$this->addPanel($pageLink = new GUI_Control_Link($i, $i, $url));
					if ($i == $actualPage)
						$pageLink->addClasses('current_page');
					$lastDisplayed = $i;
			}
		}
	}
	
	public function afterInit() {
		parent::afterInit();
		
		$this->walkRecursive(array($this, 'addAnchor'));
	}
	
	protected function addAnchor($panel) {
		if ($panel instanceof GUI_Control_Link) {
			$panel->setUrl($panel->getUrl().'#'.$this->getParent()->getID());
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