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

if (!defined('GLPI_ROOT')) {
	die("Sorry. You can't access directly to this file");
}

class PluginFooterConfig extends CommonDBTM
{
	static private $_instance = null;

	public function __construct()
	{
		global $DB;
		if ($DB->tableExists($this->getTable())) {
			$this->getFromDB(1);
		}
	}

	static function canCreate()
	{
		return Session::haveRight('config', UPDATE);
	}

	static function canView()
	{
		return Session::haveRight('config', READ);
	}

	static function canUpdate()
	{
		return Session::haveRight('config', UPDATE);
	}

	static function getTypeName($nb = 0)
	{
		return 'Footer';
	}

	static function getInstance()
	{
		if (!isset(self::$_instance)) {
			self::$_instance = new self();
			if (!self::$_instance->getFromDB(1)) {
				self::$_instance->getEmpty();
			}
		}
		return self::$_instance;
	}

	static function getConfig($update = false)
	{
		static $config = null;
		if (is_null(self::$config)) {
			$config = new self();
		}
		if ($update) {
			$config->getFromDB(1);
		}
		return $config;
	}

	function getTabNameForItem(CommonGLPI $item, $withtemplate = 0)
	{
		if ($item->getType() == 'Config') {
			return self::getTypeName();
		}
		return '';
	}

	static function displayTabContentForItem(CommonGLPI $item, $tabnum = 1, $withtemplate = 0)
	{
		if ($item->getType() == 'Config') {
			self::showConfigForm($item);
		}
		return true;
	}

	static function showConfigForm(){
		global $CFG_GLPI;

		$config = new self();

		$config->showFormHeader(['colspan' => 2]);

		echo "<div class='form-field row col-12 col-sm-12 mb-2'>";
		echo "<label class='col-form-label col-xxl-2 text-xxl-end'>" . __('Fixed footer', 'footer') . "</label>";
		echo "<div class='col-xxl-10  field-container'>";

		Dropdown::showYesNo('fixed', $config->fields['fixed']);
		echo "</div>";
		echo "</div>";

		$config->showFormButtons(['colspan' => 4, 'candel' => false]);

		return false;
	}

	static function install(Migration $migration)
	{
		global $DB;

		$default_charset = DBConnection::getDefaultCharset();
		$default_collation = DBConnection::getDefaultCollation();
		$default_key_sign = DBConnection::getDefaultPrimaryKeySignOption();

		$table = self::getTable();
		$config = new self();
		if (!$DB->tableExists($table)) {
			$migration->displayMessage("Installing $table");
			$query = "CREATE TABLE IF NOT EXISTS $table (
				`id` int {$default_key_sign} NOT NULL auto_increment,
				`fixed` tinyint NOT NULL DEFAULT '0',
				PRIMARY KEY (`id`)
				) ENGINE=InnoDB DEFAULT CHARSET={$default_charset} COLLATE={$default_collation} ROW_FORMAT=DYNAMIC;";
			$DB->query($query) or die($DB->error());

			$config->add([
				'id' => 1,
			]);
		}
	}
}
