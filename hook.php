<?php
/*
 -------------------------------------------------------------------------
 Footer plugin for GLPI
 Copyright (C) 2024 by the TICgal Team.
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
 @copyright Copyright (c) 2024 TICgal team
 @license   AGPL License 3.0 or (at your option) any later version
				http://www.gnu.org/licenses/agpl-3.0-standalone.html
 @link      https://www.tic.gal
 @since     2024
 ----------------------------------------------------------------------
*/

function plugin_footer_install()
{
	$migration = new Migration(PLUGIN_FOOTER_VERSION);

	foreach (glob(dirname(__FILE__) . '/inc/*') as $filepath) {
		if (preg_match("/inc.(.+)\.class.php/", $filepath, $matches)) {
			$classname = 'PluginFooter' . ucfirst($matches[1]);
			include_once($filepath);
			if (method_exists($classname, 'install')) {
				$classname::install($migration);
			}
		}
	}
	$migration->executeMigration();

	return true;
}

function plugin_footer_uninstall()
{
	$migration = new Migration(PLUGIN_FOOTER_VERSION);

	foreach (glob(dirname(__FILE__) . '/inc/*') as $filepath) {
		if (preg_match("/inc.(.+)\.class.php/", $filepath, $matches)) {
			$classname = 'PluginFooter' . ucfirst($matches[1]);
			include_once($filepath);
			if (method_exists($classname, 'uninstall')) {
				$classname::uninstall($migration);
			}
		}
	}
	$migration->executeMigration();

	return true;
}

function plugin_footer_getDropdown() {
	$plugin = new Plugin();

	if ($plugin->isActivated("footer")) {
		return [
			'PluginFooterLink' => PluginFooterLink::getTypeName(),
			'PluginFooterMenu' => PluginFooterMenu::getTypeName()
		];
	}

	return [];
}