<?php

/**
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

// phpcs:ignore PSR1.Classes.ClassDeclaration.MissingNamespace
class PluginFooterConfig extends CommonDBTM
{
    public static $rightname = 'config';

    private static $instance = null;

    /**
     * {@inheritDoc}
     */
    public function __construct()
    {
        /** @var \DBmysql $DB */
        global $DB;

        if ($DB->tableExists($this->getTable())) {
            $this->getFromDB(1);
        }
    }

    /**
     * {@inheritDoc}
     */
    public static function getTypeName($nb = 0): string
    {
        return 'Footer';
    }

    /**
     * @return PluginFooterConfig
     */
    public static function getInstance(): PluginFooterConfig
    {
        if (!isset(self::$instance)) {
            self::$instance = new self();
            if (!self::$instance->getFromDB(1)) {
                self::$instance->getEmpty();
            }
        }

        return self::$instance;
    }

    /**
     * {@inheritDoc}
     */
    public function getTabNameForItem(CommonGLPI $item, $withtemplate = 0): string
    {
        if ($item->getType() == 'Config') {
            return self::createTabEntry(self::getTypeName());
        }

        return '';
    }

    /**
     * {@inheritDoc}
     */
    public static function displayTabContentForItem(CommonGLPI $item, $tabnum = 1, $withtemplate = 0): bool
    {
        if ($item->getType() == 'Config') {
            return self::showConfigForm();
        }

        return false;
    }

    /**
     * @return bool
     */
    public static function showConfigForm(): bool
    {
        /** @var array $CFG_GLPI */
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

        return true;
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
        $config = new self();
        if (!$DB->tableExists($table)) {
            $migration->displayMessage("Installing $table");
            $query = "CREATE TABLE IF NOT EXISTS $table (
				`id` int {$default_key_sign} NOT NULL auto_increment,
				`fixed` tinyint NOT NULL DEFAULT '0',
				PRIMARY KEY (`id`)
				) ENGINE=InnoDB DEFAULT CHARSET={$default_charset} COLLATE={$default_collation} ROW_FORMAT=DYNAMIC;";
            $DB->doQuery($query);

            $config->add([
                'id' => 1,
            ]);
        }
    }
}
