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

class PluginFooterMenu extends CommonDropdown
{
	static function getTypeName($nb = 0)
	{
		return _n('Menu link', 'Menu links', $nb, 'footer');
	}

	function getAdditionalFields()
	{
		return [
			[
				'name' => 'url',
				'label' => __('URL'),
				'type' => 'text',
				'list' => true
			],
			[
				'name' => 'interface',
				'label' => __('Interface'),
				'type' => '',
			],
			[
				'name' => 'icon',
				'label' => __('Icon'),
				'type' => '',
				'list' => true
			],
			[
				'name' => 'is_blank',
				'label' => __('New tab', 'footer'),
				'type' => 'bool',
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

		$tab[] = [
			'id' => '24',
			'table' => self::getTable(),
			'field' => 'interface',
			'name' => __('Interface'),
			'searchtype' => ['equals', 'notequals'],
			'datatype' => 'specific'
		];

		$tab[] = [
			'id' => '25',
			'table' => self::getTable(),
			'field' => 'is_blank',
			'name' => __('New tab', 'footer'),
			'datatype' => 'bool'
		];

		return $tab;
	}

	public function displaySpecificTypeField($ID, $field = [], array $options = [])
	{

		switch ($field['name']) {
			case 'interface':
				Dropdown::showFromArray($field['name'], self::getInterfaces(), ['value' => $this->fields[$field['name']]]);
				break;
			case 'icon':
				$icon = $this->fields[$field['name']];
				$selector_id = 'icon_' . mt_rand();
				echo Html::select('icon', [$icon => $icon], ['id' => $selector_id, 'selected' => $icon, 'style' => 'width:100%;']);
				echo Html::script('js/Forms/FaIconSelector.js');
				echo Html::scriptBlock(
					<<<JAVASCRIPT
         $(
            function() {
               var icon_selector = new GLPI.Forms.FaIconSelector(document.getElementById('{$selector_id}'));
               icon_selector.init();
            }
         );
JAVASCRIPT
				);
				break;
			default:
				parent::displaySpecificTypeField($ID, $field, $options);
				break;
		}
	}

	public static function getSpecificValueToDisplay($field, $values, array $options = [])
    {
        if (!is_array($values)) {
            $values = [$field => $values];
        }
        switch ($field) {
            case 'interface':
					$interfaces = self::getInterfaces();
                    if (isset($interfaces[$values[$field]])) {
						return $interfaces[$values[$field]];
					}
					return '';
				break;
        }

        return parent::getSpecificValueToDisplay($field, $values, $options);
    }

	public static function getSpecificValueToSelect($field, $name = '', $values = '', array $options = [])
    {

        if (!is_array($values)) {
            $values = [$field => $values];
        }
        switch ($field) {
			case 'interface':
				return Dropdown::showFromArray($name, self::getInterfaces(), ['value' => $values[$field], 'display' => false]);
				break;
		}

        return parent::getSpecificValueToSelect($field, $name, $values, $options);
    }

	public static function getInterfaces()
	{
		return [
			'helpdesk' => __('Simplified interface'),
			'central' => __('Standard interface'),
			'both' => __('Both', 'footer'),
		];
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
				`name` varchar(255) DEFAULT NULL,
				`url` varchar(255) DEFAULT NULL,
				`icon` varchar(255) DEFAULT NULL,
				`interface` varchar(255) DEFAULT 'helpdesk',
				`is_blank` tinyint NOT NULL DEFAULT '0',
				`comment` text,
				`date_mod` TIMESTAMP NULL DEFAULT NULL,
				`date_creation` TIMESTAMP NULL DEFAULT NULL,
				`entities_id` INT {$default_key_sign} NOT NULL DEFAULT '0',
				`is_recursive` tinyint NOT NULL DEFAULT '0',
				PRIMARY KEY (`id`),
				KEY `name` (`name`),
				KEY `url` (`url`),
				KEY `icon` (`icon`),
				KEY `interface` (`interface`),
				KEY `is_blank` (`is_blank`),
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
