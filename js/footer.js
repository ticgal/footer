/*
 -------------------------------------------------------------------------
 Footer plugin for GLPI
 Copyright (C) 2022 by the TICgal Team.
 https://www.tic.gal
 -------------------------------------------------------------------------
 LICENSE
 This file is part of the Footer plugin.
 Footer plugin is free software; you can redistribute it and/or modify
 it under the terms of the GNU General Public License as published by
 the Free Software Foundation; either version 3 of the License, or
 (at your option) any later version.
 Footer plugin is distributed in the hope that it will be useful,
 but WITHOUT ANY WARRANTY; without even the implied warranty of
 MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 GNU General Public License for more details.
 You should have received a copy of the GNU General Public License
 along with Footer. If not, see <http://www.gnu.org/licenses/>.
 --------------------------------------------------------------------------
 @package   Footer
 @author    the TICgal team
 @copyright Copyright (c) 2022 TICgal team
 @license   AGPL License 3.0 or (at your option) any later version
				http://www.gnu.org/licenses/agpl-3.0-standalone.html
 @link      https://www.tic.gal
 @since     2022
 ----------------------------------------------------------------------
*/

var ajax_url = CFG_GLPI.root_doc + "/" + GLPI_PLUGINS_PATH.footer + "/ajax/footer.php";
$(document).ready(function () {
	jQuery.ajax({
		url: ajax_url,
		type: "POST",
		data: {
			"action": "get_footer"
		},
		dataType: "json",
		success: function (data) {
			if (data.length > 0) {
				var html = "<footer style='z-index: 999;' class='position-fixed d-flex flex-row bottom-0 w-100 card'>"+
					"<div class='d-flex ms-auto flex-row align-items-center'>"+
					"<div class='d-flex w-100 justify-content-between align-items-center'>"+
					"<ul class='nav nav-tabs align-items-center border-0' style='font-size: xx-small;'>";
					$.each(data, function (key, value) {
						html += "<li class='d-inline-block p-2'><a class='' href='" + value.url + "' target='_blank'>"+value.name+"</a></li>";
					});
					html += "</ul></div></div></footer>";
					$("html").append(html);
					$("body > div.page").css("padding-bottom", "30px");
			}
		}
	});
});