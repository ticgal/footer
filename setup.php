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

use Glpi\Plugin\Hooks;

define('PLUGIN_FOOTER_VERSION', '2.0.0-beta');
define('PLUGIN_FOOTER_MIN_GLPI', '11.0');
define('PLUGIN_FOOTER_MAX_GLPI', '11.9');
define('PLUGIN_FOOTER_ICON', 'fa-solid fa-shoe-prints');

/**
 * @return array
 */
function plugin_version_footer(): array
{
    return [
        'name'      => 'Footer',
        'version'   => PLUGIN_FOOTER_VERSION,
        'author'    => '<a href="https://tic.gal">TICGAL</a>',
        'homepage'  => 'https://tic.gal',
        'license'   => 'GPLv3+',
        'requirements' => [
            'glpi' => [
                'min' => PLUGIN_FOOTER_MIN_GLPI,
                'max' => PLUGIN_FOOTER_MAX_GLPI,
            ],
        ],
    ];
}

/**
 * @return void
 */

function plugin_init_footer(): void
{
    /**
     * @var array $PLUGIN_HOOKS
     * @var array $CFG_GLPI
     */
    global $PLUGIN_HOOKS, $CFG_GLPI;

    $PLUGIN_HOOKS['csrf_compliant']['footer'] = true;

    $plugin = new Plugin();
    if ($plugin->isActivated('footer')) {
        if (Session::getLoginUserID() && (!isset($_REQUEST['_in_modal']) || !$_REQUEST['_in_modal'])) {
            $PLUGIN_HOOKS[Hooks::ADD_JAVASCRIPT]['footer'] = ['public/footer.js'];
        }

        $PLUGIN_HOOKS['config_page']['footer'] = 'front/config.form.php';
        Plugin::registerClass('PluginFooterConfig', ['addtabon' => 'Config']);

        // Include $CFG_GLPI PluginFooterMenu table due incompatibility with autoload
        $CFG_GLPI['glpiitemtypetables'][PluginFooterMenu::getTable()] = PluginFooterMenu::class;
    }
}
