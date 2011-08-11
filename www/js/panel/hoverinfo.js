var fixed = new Array();
var loading = new Array();

function GUI_Panel_HoverInfo(controlID, hoverText, enableLocking, controlAjaxID) {
	fixed[controlID] = false;
	loading[controlID] = false;
	posX = new Array();
	posY = new Array();
	var ajaxControlContent = '';
	
	if (controlAjaxID) {
		$("#"+controlID).mouseover(function() {
			if (!fixed[controlID])
				$("body").append('<div id="' + controlID + '_hover" class="core_gui_hoverinfo" style="position:absolute;">' + (ajaxControlContent ? ajaxControlContent : hoverText) + '</div>');
			if (!ajaxControlContent && !loading[controlID]) {
				$("#"+controlID+"_hover").addClass('core_ajax_loading');
				loading[controlID] = true;
				$.core.ajaxRequest(controlAjaxID, 'ajaxOnHover', undefined, function(data) {
					ajaxControlContent = data;
					$("#"+controlID+"_hover").removeClass('core_ajax_loading');
					loading[controlID] = false;
					$("#"+controlID+"_hover").html(data);
					if (fixed[controlID])
						$("#"+controlID+"_hover").append('<a id="'+controlID+'_hover_close" class="core_gui_hoverinfo_close" href="#" onclick="GUI_Panel_HoverInfo_Close(\''+controlID+'\'); return false;" style="text-align:right; display:block;">close</a>');
				});
			}
		});
	}
	else {
		$("#"+controlID).mouseover(function() {
			if (!fixed[controlID])
				$("body").append('<div id="' + controlID + '_hover" class="core_gui_hoverinfo" style="position:absolute;">' + hoverText + '</div>');
		});
	}
	
	$("#"+controlID).mouseout(function() {
		if (!fixed[controlID])
			$("#"+controlID+"_hover").remove();
	}).mousemove(function(e) {
		if (!fixed[controlID]) {
			posX[controlID] = e.pageX;
			posY[controlID] = e.pageY;
		}
		$("#"+controlID+"_hover").css("left", posX[controlID] + 10).css("top", posY[controlID] + 10);
	});
	
	if (enableLocking) {
		$("#"+controlID).click(function() {
			if (fixed[controlID]) {
				$("#"+controlID+"_hover_close").remove();
				fixed[controlID] = false;
			} else {
				$("#"+controlID+"_hover").append('<a id="'+controlID+'_hover_close" class="core_gui_hoverinfo_close" href="#" onclick="GUI_Panel_HoverInfo_Close(\''+controlID+'\'); return false;" style="text-align:right; display:block;">close</a>');
				fixed[controlID] = true;
			}
		})
	}
}

function GUI_Panel_HoverInfo_Close(controlID) {
	$("#"+controlID+"_hover").remove();
	fixed[controlID] = false;
}