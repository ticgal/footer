<?php

/**
 * -------------------------------------------------------------------------
 * Footer plugin for GLPI
 * Copyright (C) 2024 - 2026 by the TICGAL Team.
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
 * @copyright Copyright (C) 2024 - 2026 TICGAL team
 * @license   AGPL License 3.0 or (at your option) any later version
 *            http://www.gnu.org/licenses/agpl-3.0-standalone.html
 * @link      https://www.tic.gal
 * @since     2024
 * -------------------------------------------------------------------------
 */

// phpcs:disable PSR1.Classes.ClassDeclaration.MissingNamespace
class PluginFooterLink extends CommonDropdown
{
    /**
     * {@inheritDoc}
     */
    public static function getTypeName($nb = 0): string
    {
        return _n('Footer link', 'Footer links', $nb, 'footer');
    }

    /**
     * {@inheritDoc}
     */
    public function getAdditionalFields(): array
    {
        return [
            [
                'name'  => 'url',
                'label' => __('URL'),
                'type'  => 'text',
                'list'  => true,
            ],
        ];
    }

    /**
     * {@inheritDoc}
     */
    public function rawSearchOptions(): array
    {
        $tab = parent::rawSearchOptions();

        $tab[] = [
            'id'        => '23',
            'table'     => self::getTable(),
            'field'     => 'url',
            'name'      => __('URL'),
            'datatype'  => 'text',
        ];

        return $tab;
    }

    public static function getIcon()
    {
        return PLUGIN_FOOTER_ICON;
    }

    /**
     * @param Migration $migration
     *
     * @return void
     */
    public static function install(Migration $migration): void
    {
        /** @var \DBmysql $DB */
        global $DB;

        $default_charset    = DBConnection::getDefaultCharset();
        $default_collation  = DBConnection::getDefaultCollation();
        $default_key_sign   = DBConnection::getDefaultPrimaryKeySignOption();

        $table = self::getTable();
        if (!$DB->tableExists($table)) {
            $migration->displayMessage("Installing $table");
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
            $DB->doQuery($query);
        }
    }

    /**
     * @param Migration $migration
     *
     * @return void
     */
    public static function uninstall(Migration $migration): void
    {
        $table = self::getTable();
        $migration->displayMessage("Uninstalling $table");
        $migration->dropTable($table);
    }
}
