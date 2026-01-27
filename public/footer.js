/*
 * -------------------------------------------------------------------------
 * Footer plugin for GLPI
 * Copyright (C) 2025 by the TICGAL Team.
 * https://www.tic.gal
 * -------------------------------------------------------------------------
 * LICENSE
 * This file is part of the Footer plugin.
 * Footer plugin is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 3 of the License, or
 * (at your option) any later version.
 * Footer plugin is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License
 * along with Footer. If not, see <http://www.gnu.org/licenses/>.
 * -------------------------------------------------------------------------
 * @package   footer
 * @author    the TICGAL team
 * @copyright Copyright (c) 2024-2025 TICGAL team
 * @license   AGPL License 3.0 or (at your option) any later version
 *            http://www.gnu.org/licenses/agpl-3.0-standalone.html
 * @link      https://www.tic.gal
 * @since     2024
 * -------------------------------------------------------------------------
 */

$(document).ready(function () {
	if (typeof CFG_GLPI === "undefined" || typeof GLPI_PLUGINS_PATH === "undefined") {
		console.error("GLPI variables not defined yet. Cannot initialize footer AJAX URL.");
		return;
	}

	if (window.location === window.parent.location) {
		$.ajax({
			url: '/plugins/footer/ajax/footer.php',
			type: "POST",
			data: { "action": "get_footer" },
			dataType: "json",
			success: function (data) {
				if (data.links.length > 0) {
					var html_class = "footer d-flex justify-content-end align-items-center w-100";
					var style_fixed = "";

					if (data.config.fixed === 1) {
						html_class += " position-fixed";
						style_fixed = "bottom: 0; left: 0;";
						$("body > div.page").css("padding-bottom", "50px");
					}

					var html = "<footer id='pluginfooter' class='" + html_class + "' style='z-index: 999; font-family: var(--tblr-body-font-family); " + style_fixed + " background-color: #f8f9fa; padding: 10px 20px; border-top: 1px solid #ddd;'>" +
						"<ul class='nav justify-content-end mb-0' style='font-size: small; list-style: none;'>";

					$.each(data.links, function (key, value) {
						html += "<li class='nav-item d-inline-block mx-2'><a class='nav-link p-1' href='" + value.url + "' target='_blank'>" + value.name + "</a></li>";
					});
					html += "</ul></footer>";
					$("body").append(html);
				}
			}
		});

		$.ajax({
			url: '/plugins/footer/ajax/footer.php',
			type: "POST",
			data: { "action": "get_menu" },
			dataType: "json",
			success: function (data) {
				if (data.length > 0) {
					$.each(data, function (key, value) {
						var li = document.createElement("li");
						li.setAttribute("class", "nav-item dropdown");
						var a = document.createElement("a");
						a.setAttribute("class", "nav-link");
						a.setAttribute("href", value.url);
						a.setAttribute("target", value.target);
						a.setAttribute("title", value.name);
						var i = document.createElement("i");
						i.setAttribute("class", value.icon);
						var span = document.createElement("span");
						span.setAttribute("class", "menu-label");
						span.innerHTML = value.name;
						a.appendChild(i);
						a.appendChild(span);
						li.appendChild(a);
						$("#navbar-menu ul").append(li);
					});
				}
			}
		});
	}
});
