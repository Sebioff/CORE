/**
 * Copyright (C) 2012 Sebastian Mayer, Andreas Sicking, Andre Jährling
 * This file is part of CORE PHP Framework.
 *
 * CORE PHP Framework is free software: you can redistribute it and/or modify it
 * under the terms of the GNU General Public License as published by the Free Software
 * Foundation, either version 3 of the License, or (at your option) any later version.
 *
 * CORE PHP Framework is distributed in the hope that it will be useful, but WITHOUT
 * ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS
 * FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with CORE PHP Framework. If not, see <http://www.gnu.org/licenses/>.
 */

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