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

use GlpiPlugin\Footer\Config;
use GlpiPlugin\Footer\Link;
use GlpiPlugin\Footer\Menu;

/**
 * Renames the itemtypes stored in the database (logs, display preferences, translations...)
 * from the legacy class names (GLPI < 12 version of the plugin) to the namespaced ones.
 *
 * @param Migration $migration
 *
 * @return void
 */
function plugin_footer_migrate_itemtypes(Migration $migration): void
{
    /** @var \DBmysql $DB */
    global $DB;

    $legacy_itemtypes = [
        'PluginFooterConfig' => Config::class,
        'PluginFooterLink'   => Link::class,
        'PluginFooterMenu'   => Menu::class,
    ];

    foreach ($legacy_itemtypes as $legacy => $itemtype) {
        // Rows already stored with the new name (plugin used before the migration) would collide
        // with the legacy ones on the unique index of the display preferences
        $DB->doQuery(
            "DELETE `legacy` FROM `glpi_displaypreferences` AS `legacy`
            INNER JOIN `glpi_displaypreferences` AS `current`
                ON `current`.`itemtype` = " . $DB->quote($itemtype) . "
                AND `current`.`users_id` = `legacy`.`users_id`
                AND `current`.`num` = `legacy`.`num`
            WHERE `legacy`.`itemtype` = " . $DB->quote($legacy),
        );
        $migration->renameItemtype($legacy, $itemtype, false);
    }
}

/**
 * @return bool
 */
function plugin_footer_install(): bool
{
    $migration = new Migration(PLUGIN_FOOTER_VERSION);

    plugin_footer_migrate_itemtypes($migration);

    foreach (PLUGIN_FOOTER_ITEMTYPES as $classname) {
        $classname::install($migration);
    }
    $migration->executeMigration();

    return true;
}

/**
 * Removes the core data tied to the plugin itemtypes (translations, listing columns,
 * history, saved searches), including the rows still stored with the legacy class names,
 * so they do not come back as orphans when the plugin is installed again.
 *
 * @return void
 */
function plugin_footer_clean_core_data(): void
{
    /** @var \DBmysql $DB */
    global $DB;

    $itemtypes = array_merge(
        PLUGIN_FOOTER_ITEMTYPES,
        ['PluginFooterConfig', 'PluginFooterLink', 'PluginFooterMenu'],
    );

    $searches = array_column(
        iterator_to_array($DB->request([
            'SELECT' => 'id',
            'FROM'   => 'glpi_savedsearches',
            'WHERE'  => ['itemtype' => $itemtypes],
        ])),
        'id',
    );
    if ($searches !== []) {
        $DB->delete('glpi_savedsearches_users', ['savedsearches_id' => $searches]);
        $DB->delete('glpi_savedsearches_alerts', ['savedsearches_id' => $searches]);
        $DB->delete('glpi_savedsearches', ['id' => $searches]);
    }

    foreach (['glpi_dropdowntranslations', 'glpi_displaypreferences', 'glpi_logs'] as $table) {
        $DB->delete($table, ['itemtype' => $itemtypes]);
    }
}

/**
 * @return bool
 */
function plugin_footer_uninstall(): bool
{
    $migration = new Migration(PLUGIN_FOOTER_VERSION);

    foreach (PLUGIN_FOOTER_ITEMTYPES as $classname) {
        $classname::uninstall($migration);
    }
    plugin_footer_clean_core_data();
    $migration->executeMigration();

    return true;
}

/**
 * @return array
 */
function plugin_footer_getDropdown(): array
{
    $plugin = new Plugin();

    if ($plugin->isActivated("footer")) {
        return [
            Link::class => Link::getTypeName(),
            Menu::class => Menu::getTypeName(),
        ];
    }

    return [];
}
