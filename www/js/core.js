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
	
	$.core.loadPanels = function(panelNames, callback) {
		$.post(document.location.href, { core_ajax: true, refreshPanels: panelNames.join(',') },
			function(data) {
				var result = new Array();
				data = $(data);
				
				for(var i = 0; i < panelNames.length; i++) {
					result[panelNames[i]] = data.find("#" + panelNames[i]);
				}
				
				if (callback != null)
					callback(result);
			}
		);
	}
	
	$.core.refreshPanels = function(panelNames) {
		$.core.loadPanels(panelNames, 
			function(panelData) {
				for (panelName in panelData) {
					$("#" + panelName).replaceWith(panelData[panelName]);
				}
			}
		);
	}
})(jQuery);
