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
		$.post(document.location.href, { core_ajax: true, refreshPanels: panelNames.join(',') },
			function(panelData) {
				if (callback != null)
					callback(panelData);
			}
		);
	}
	
	// refreshs panels using ajax
	$.core.refreshPanels = function(panelNames) {
		$.core.loadPanels(panelNames, 
			function(panelData) {
				var panels = $.core.extractPanels(panelData, panelNames);
				for (panelName in panels) {
					$("#" + panelName).replaceWith(panels[panelName]);
				}
			}
		);
	}
})(jQuery);
