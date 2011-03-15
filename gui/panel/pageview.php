<?php

/**
 * A panel that has methods to display its content on different pages
 */
class GUI_Panel_PageView extends GUI_Panel {
	private static $firstPageViewOnPage = true;
	
	private $itemsPerPage = 10;
	private $container = null;
	private $enableAjax = true;
	private $page = 0;
	
	public function __construct($name, DB_Container $container, $title = '') {
		parent::__construct($name, $title);

		$this->container = $container;
		$this->setTemplate(dirname(__FILE__).'/pageview.tpl');
		$this->addClasses('core_gui_pageview');
	}
	
	public function init() {
		if ($this->enableAjax)
			$this->getModule()->addJsRouteReference('core_js', '/jquery/jquery.bbq.js');
			
		$this->addPanel(new GUI_Panel_PageView_Pages($this));
	}
	
	public function afterInit() {
		parent::afterInit();
		
		if ($this->enableAjax) {
			if (Router::get()->getRequestMode() != Router::REQUESTMODE_AJAX) {
				if (self::$firstPageViewOnPage) {
					self::$firstPageViewOnPage = false;
					$this->addJS('
						GUI_Panel_PageView__data = {};
					');
				}
				
				$this->addJS(sprintf('
					currentUrl = document.location.href;
			 		GUI_Panel_PageView__data["%1$s"] = {
			 			cache: {
							currentUrl: $("#%2$s")
						}
					}
					
					$(window).bind("hashchange", function(e) {
						var that = $("#%1$s"),
						data = GUI_Panel_PageView__data["%1$s"],
						url = e.getState("%1$s") || "";
						
						if (!url || data.url === url) return;
						data.url = url;
						
						if (data.cache[url]) {
							$.core.ajaxCurrentUrl = url;
							var panelNames = ["%2$s"];
							$.core.replacePanels(data.cache[url], panelNames);
						}
						else {
							that.addClass("core_ajax_loading");
							$.core.ajaxCurrentUrl = url;
							var panelNames = ["%2$s"];
							$.core.refreshPanels(panelNames, function(panelData) {
								data.cache[url] = panelData;
								that.removeClass("core_ajax_loading");
							});
						}
					});
					
					if (currentUrl.indexOf("#") >= 0)
						$(window).trigger("hashchange");
				', $this->getID(), $this->getAjaxID()));
			}
			
			$this->addJS(sprintf('
				$("#%1$s-pages a").click(function(e){
					var state = {},
					url = $(this).attr("href");
      
					state["%1$s"] = url;
				    $.bbq.pushState(state);
				    
					return false;
				});
			', $this->getID(), $this->getAjaxID()));
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
		$page = ($this->page) ? $this->page : (int)$this->getModule()->getParam($this->getName().'-page');
		if ($page) {
			if ($page > $this->getPageCount())
				$page = $this->getPageCount();
			return $page;
		}
		else
			return 1;
	}
	
	public function setPage($page) {
		$this->page = $page;
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