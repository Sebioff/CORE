var hasBeenSubmitted = false;

$().ready( function() {
	$("form").submit(function() {
		if (hasBeenSubmitted)
			return false;
		else {
			hasBeenSubmitted = true;
			// sadly we can't just disable the button, since disabled elements aren't submitted
			$(this).find(":input[type='submit']").addClass('core_gui_submittable_disabled');
		}
	});
});

(function($) {
	// core object -------------------------------------------------------------
	$.core = function() {}
	
	// extracts specific panels from a bunch of code
	$.core.extractPanels = function(panelData, panelNames) {
		var result = new Array();
		panelData = $(panelData);
		
		for (var i = 0; i < panelNames.length; i++) {
			result[panelNames[i]] = panelData.find("#" + panelNames[i]);
		}
		
		return result;
	}
	
	// loads panel data using ajax
	$.core.loadPanels = function(panelNames, callback) {
		$.core.ajaxRequest('', 'display', { refreshPanels: panelNames.join(',') }, callback);
	}
	
	// refreshs panels using ajax
	$.core.refreshPanels = function(panelNames) {
		$.core.loadPanels(panelNames, 
			function(panelData) {
				$.core.replacePanels(panelData, panelNames);
			}
		);
	}
	
	// replaces all panels specified by panelNames and contained in panelData
	$.core.replacePanels = function(panelData, panelNames) {
		var panels = $.core.extractPanels(panelData, panelNames);
		for (panelName in panels) {
			$("#" + panelName).replaceWith(panels[panelName]);
		}
		// execute loaded js
		$("body").append("<script type=\"text/javascript\">" + $(panelData).find("#ajax_js").text() + "</script>");
	}
	
	// executes the ajaxACTION() method of a given panel. successCallback recieves
	// as parameter whatever the ajaxACTION() method returns.
	$.core.ajaxRequest = function(panelAjaxID, panelMethod, parameters, successCallback) {
		var params = { core_ajax: true, core_ajax_panel: panelAjaxID, core_ajax_method: panelMethod };
		if (parameters !== undefined) {
			for (attribute in parameters) {
				params[attribute] = parameters[attribute];
			}
		}
		$.post(document.location.href, params,
			function(panelData) {
				if (successCallback != null)
					successCallback(panelData);
			}
		);
	}
})(jQuery);
