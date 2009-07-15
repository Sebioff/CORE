<?php

class GUI_Validator_HTML extends GUI_Validator {
	private $denied = array('applet',
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
		preg_match_all('=<([a-z]+\d?).*>=Uim', $this->control->getValue(), $matches);
		foreach ($matches[1] as $element) {
			if (in_array($element, $this->denied)) {
				return false;
			}
		}
		return true;
	}
	
	public function addDeniedElement($element) {
		if (in_array($element, $this->elements) && !in_array($element, $this->denied)) {
			$this->denied[] = $element;
		}
	}
	
	public function getAllowedElements() {
		return array_diff($this->elements, $this->denied);
	}
	
	public function getError() {
		return 'Text enthält nicht erlaubte HTML-Elemente';
	}
	
	public function getJs() {
		$js = 'jQuery.validator.addMethod("html", function(value, element) {
				pattern = new RegExp("/'.implode('|', $this->denied).'/im");
				return !pattern.test(value);
			},
			"Text enthält nicht erlaubte HTML-Elemente"
		);';
		Router::get()->getCurrentModule()->addJsAfterContent($js);
		return array('html', 'true'); 
	}
}

?>