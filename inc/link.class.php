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

if (!defined('GLPI_ROOT')) {
	die("Sorry. You can't access directly to this file");
}

class PluginFooterLink extends CommonDropdown
{
	static function getTypeName($nb = 0)
	{
		return _n('Footer link', 'Footer links', $nb, 'footer');
	}

	function getAdditionalFields()
	{
		return [
			[
				'name' => 'url',
				'label' => __('URL'),
				'type' => 'text',
				'list' => true
			]
		];
	}

	function rawSearchOptions()
	{
		$tab = parent::rawSearchOptions();

		$tab[] = [
			'id' => '23',
			'table' => self::getTable(),
			'field' => 'url',
			'name' => __('URL'),
			'datatype' => 'text'
		];

		return $tab;
	}

	static function install(Migration $migration)
	{
		global $DB;

		$default_charset = DBConnection::getDefaultCharset();
		$default_collation = DBConnection::getDefaultCollation();
		$default_key_sign = DBConnection::getDefaultPrimaryKeySignOption();

		$table = self::getTable();
		if (!$DB->tableExists($table)) {
			$query = "CREATE TABLE `$table` (
				`id` INT {$default_key_sign} NOT NULL AUTO_INCREMENT,
				`name` varchar(255) default NULL,
				`url` varchar(255) default NULL,
				`comment` text,
				`date_mod` TIMESTAMP NULL DEFAULT NULL,
				`date_creation` TIMESTAMP NULL DEFAULT NULL,
				`entities_id` int {$default_key_sign} NOT NULL DEFAULT '0',
				`is_recursive` tinyint NOT NULL DEFAULT '0',
				PRIMARY KEY (`id`),
				KEY `name` (`name`),
				KEY `url` (`url`),
				KEY `entities_id` (`entities_id`),
				KEY `is_recursive` (`is_recursive`),
				KEY `date_mod` (`date_mod`),
				KEY `date_creation` (`date_creation`)
			) ENGINE=InnoDB DEFAULT CHARSET={$default_charset} COLLATE={$default_collation} ROW_FORMAT=DYNAMIC;";
			$DB->query($query) or die($DB->error());
		}
	}

	static function uninstall()
	{
		global $DB;

		$table = self::getTable();
		if ($DB->tableExists($table)) {
			$query = "DROP TABLE IF EXISTS `$table`";
			$DB->query($query) or die($DB->error());
		}
	}
}