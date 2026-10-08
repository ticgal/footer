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

namespace GlpiPlugin\Footer;

use CommonDBTM;
use CommonGLPI;
use DBConnection;
use Dropdown;
use Migration;

class Config extends CommonDBTM
{
    public static string $rightname = 'config';

    private static $instance = null;

    /**
     * The configuration is protected like the core one: it asks for a re-authentication.
     *
     * {@inheritDoc}
     */
    protected static function itemTypeRequiresReauthentication(): bool
    {
        return true;
    }

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
     * @return Config
     */
    public static function getInstance(): Config
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

    /**
     * {@inheritDoc}
     */
    public function prepareInputForAdd($input)
    {
        $input = self::normalizeInput($input);
        if ($input === false) {
            return false;
        }

        return parent::prepareInputForAdd($input);
    }

    /**
     * {@inheritDoc}
     */
    public function prepareInputForUpdate($input)
    {
        $input = self::normalizeInput($input);
        if ($input === false) {
            return false;
        }

        return parent::prepareInputForUpdate($input);
    }

    /**
     * "fixed" is a yes/no option: anything else is rejected.
     *
     * @param array $input
     *
     * @return array|false
     */
    private static function normalizeInput(array $input): array|false
    {
        if (isset($input['fixed'])) {
            $fixed = filter_var($input['fixed'], FILTER_VALIDATE_INT, ['options' => ['min_range' => 0, 'max_range' => 1]]);
            if ($fixed === false) {
                return false;
            }
            $input['fixed'] = $fixed;
        }

        return $input;
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

    /**
     * @param Migration $migration
     * @return void
     */
    public static function uninstall(Migration $migration): void
    {
        $table = self::getTable();
        $migration->displayMessage("Uninstalling $table");
        $migration->dropTable($table);
    }
}
