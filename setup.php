<?php
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

use Glpi\Plugin\Hooks;

define('PLUGIN_FOOTER_VERSION', '1.0.0');
define('PLUGIN_FOOTER_MIN_GLPI', '10.0.0');
define('PLUGIN_FOOTER_MAX_GLPI', '10.1.99');

function plugin_version_footer()
{
	return [
		'name' => 'Footer',
		'version' => PLUGIN_FOOTER_VERSION,
		'author' => '<a href="https://tic.gal">TICgal</a>',
		'homepage' => 'https://tic.gal',
		'license' => 'GPLv3+',
		'requirements' => [
			'glpi' => [
				'min' => PLUGIN_FOOTER_MIN_GLPI,
				'max' => PLUGIN_FOOTER_MAX_GLPI,
			]
		]
	];
}

function plugin_init_footer()
{
	global $PLUGIN_HOOKS, $CFG_GLPI;

	$PLUGIN_HOOKS['csrf_compliant']['footer'] = true;

	$plugin = new Plugin();
	if ($plugin->isActivated('footer')) {
		if (Session::getLoginUserID()) {
			if (!isset($_REQUEST['_in_modal']) || !$_REQUEST['_in_modal']) {
				$PLUGIN_HOOKS[Hooks::ADD_JAVASCRIPT]['footer'] = ['js/footer.js'];
			}
		}
	}
}