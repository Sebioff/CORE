var fixed = new Array();

function GUI_Panel_HoverInfo(controlID, hoverText) {
	fixed[controlID] = false;
	posX = new Array();
	posY = new Array();
	$("#"+controlID).mouseover(function() {
		if (!fixed[controlID])
			$("body").append('<div id="' + controlID + '_hover" class="core_gui_hoverinfo" style="position:absolute;">' + hoverText + '</div>');
	}).mouseout(function() {
		if (!fixed[controlID])
			$("#"+controlID+"_hover").remove();
	}).click(function() {
		if (fixed[controlID]) {
			$("#"+controlID+"_hover_close").remove();
			fixed[controlID] = false;
		} else {
			$("#"+controlID+"_hover").append('<a id="'+controlID+'_hover_close" href="#" onclick="GUI_Panel_HoverInfo_Close(\''+controlID+'\');" style="text-align:right; display:block;">close</a>');
			fixed[controlID] = true;
		}
	}).mousemove(function(e) {
		if (!fixed[controlID]) {
			posX[controlID] = e.pageX;
			posY[controlID] = e.pageY;
		}
		$("#"+controlID+"_hover").css("left", posX[controlID] + 10).css("top", posY[controlID] + 10);
	});
}

function GUI_Panel_HoverInfo_Close(controlID) {
	$("#"+controlID+"_hover").remove();
	fixed[controlID] = false;
}