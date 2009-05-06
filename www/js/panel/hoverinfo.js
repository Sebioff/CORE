function GUI_Panel_HoverInfo(controlID, hoverText) {
	$("#"+controlID).mouseover(function() {
		$("body").append('<div id="' + controlID + '_hover" class="core_gui_hoverinfo" style="position:absolute;">' + hoverText + '</div>');
	}).mouseout(function() {
		$("#"+controlID+"_hover").remove();
	}).mousemove(function(e) {
		$("#"+controlID+"_hover").css("left", e.pageX + 10).css("top", e.pageY + 10);
	});
}