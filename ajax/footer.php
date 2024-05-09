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

global $DB;

include("../../../inc/includes.php");
header("Content-Type: text/html; charset=UTF-8");
Html::header_nocache();

Session::checkLoginUser();

use Glpi\Toolbox\Sanitizer;

$config = new PluginFooterConfig();

if (isset($_POST['action']) && $_POST['action'] == 'get_footer') {
	$query = [
		'FROM' => PluginFooterLink::getTable(),
		'WHERE' => getEntitiesRestrictCriteria(PluginFooterLink::getTable(), '', '', true),
	];

	$link = [
		'config' => [
			'fixed' => $config->fields['fixed'],
		],
		'links' => []
	];
	foreach ($DB->request($query) as $data) {
		$link['links'][] = [
			'url' => DropdownTranslation::getTranslatedValue(
				$data['id'],
				PluginFooterLink::getType(),
				'url',
				$_SESSION['glpilanguage'],
				$data['url']
			),
			'name' => Sanitizer::decodeHtmlSpecialChars(DropdownTranslation::getTranslatedValue(
				$data['id'],
				PluginFooterLink::getType(),
				'name',
				$_SESSION['glpilanguage'],
				$data['name']
			)),
		];
	}

	echo json_encode($link);
} elseif (isset($_POST['action']) && $_POST['action'] == 'get_menu') {
	$interface = 'helpdesk';
	if (isset($_SESSION['glpiactiveprofile']['interface'])) {
		$interface = $_SESSION['glpiactiveprofile']['interface'];
	}
	$query = [
		'FROM' => PluginFooterMenu::getTable(),
		'WHERE' => [
			'interface' => ['both', $interface],
		] + getEntitiesRestrictCriteria(PluginFooterMenu::getTable(), '', '', true),
	];
	$link = [];
	foreach ($DB->request($query) as $data) {
		$link[] = [
			'url' => DropdownTranslation::getTranslatedValue(
				$data['id'],
				PluginFooterMenu::getType(),
				'url',
				$_SESSION['glpilanguage'],
				$data['url']
			),
			'name' => Sanitizer::decodeHtmlSpecialChars(DropdownTranslation::getTranslatedValue(
				$data['id'],
				PluginFooterMenu::getType(),
				'name',
				$_SESSION['glpilanguage'],
				$data['name']
			)),
			'icon' => $data['icon'],
			'target' => ($data['is_blank']) ? '_blank' : '_self',
		];
	}
	echo json_encode($link);
}
