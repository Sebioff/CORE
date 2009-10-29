<?php

/**
 * Ensures the controls value only contains allowed HTML elements.
 */
class GUI_Validator_HTML extends GUI_Validator {
	private $blacklist = array('applet',
		'base', 'basefont', 'bdo', 'body', 'button',
		'dir',
		'fieldset', 'form', 'frame', 'frameset',
		'head', 'html',
		'iframe', 'input', 'isindex',
		'kbd',
		'label', 'legend', 'link',
		'menu', 'meta',
		'noframe', 'noscript',
		'object', 'optgroup', 'option',
		'param',
		'script', 'select', 'style',
		'textarea', 'title',
		'var'
	);
	private $elements = array('a', 'abbr', 'acronym', 'address', 'applet', 'area',
		'b', 'base', 'basefont', 'bdo', 'big', 'blockquote', 'body', 'br', 'button',
		'caption', 'center', 'cite', 'code', 'col', 'colgroup',
		'dd', 'del', 'dfn', 'dir', 'div', 'dl', 'dt',
		'em',
		'fieldset', 'font', 'form', 'frame', 'frameset',
		'h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'head', 'hr', 'html',
		'i', 'iframe', 'img', 'input', 'ins', 'isindex',
		'kbd',
		'label', 'legend', 'li', 'link',
		'map', 'menu', 'meta',
		'noframe', 'noscript',
		'object', 'ol', 'optgroup', 'option',
		'p', 'param', 'pre',
		'q',
		's', 'samp', 'script', 'select', 'small', 'span', 'strike', 'strong', 'style', 'sub', 'sup',
		'table', 'tbody', 'td', 'textarea', 'tfoot', 'th', 'thead', 'title', 'tr', 'tt',
		'u', 'ul',
		'var'
	);
	
	// OVERRIDES / IMPLEMENTS --------------------------------------------------
	public function isValid() {
		preg_match_all('=<([a-z]+[1-6]?).*?>=im', $this->control->getValue(), $matches);
		foreach ($matches[1] as $element) {
			if (in_array($element, $this->blacklist)) {
				return false;
			}
		}
		return true;
	}
	
	/**
	 * Add an HTML-element to blacklist.
	 * @param $element
	 */
	public function addToBlacklist($element) {
		if (in_array($element, $this->elements) && !in_array($element, $this->blacklist)) {
			$this->blacklist[] = $element;
		}
	}
	
	/**
	 * Override actual blacklist with new blacklist.
	 * @param list of elements
	 */
	public function setBlacklistElements() {
		$elements = func_get_args();
		if (is_array($elements[0]))
			$elements = $elements[0];
		$new_denieds = array();
		foreach ($elements as $element) {
			if (in_array($element, $this->elements)) {
				$new_elements[] = $element;
			}
		}
		if (!empty($new_elements)) {
			$this->blacklist = $new_elements;
		}
	}
	
	/**
	 * Override actual blacklist with a whitelist
	 * @param list of elements
	 */
	public function setWhitelistElements() {
		$elements = func_get_args();
		if (is_array($elements[0]))
			$elements = $elements[0];
		$whitelist = array();
		foreach ($elements as $element) {
			if (in_array($element, $this->elements)) {
				$whitelist[] = $element;
			}
		}
		if (!empty($whitelist)) {
			$this->blacklist = array_diff($this->elements, $whitelist);
		}
	}
	
	public function getAllowedElements() {
		return array_diff($this->elements, $this->blacklist);
	}
	
	public function getError() {
		return 'Text enthält nicht erlaubte HTML-Elemente';
	}
	
	public function getJs() {
		$js = 'jQuery.validator.addMethod("html'.$this->control->getName().'", function(value, element) {
				elements = " '.implode(' ', $this->blacklist).' ";
				ret = true;
				pattern = /<([a-z]+[1-6]?).*?>/gim;
				while (ret && (treffer = pattern.exec(value))) {
					if (elements.indexOf(" "+treffer[1]+" ") != -1) {
						ret = false;
					}
				}
				return ret;
			},
			"'.$this->getError().'"
		);';
		$this->control->addJS($js);
		return array('html'.$this->control->getName(), 'true');
	}
}

?>