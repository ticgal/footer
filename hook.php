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

/**
 * @return bool
 */
function plugin_footer_install(): bool
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

/**
 * @return bool
 */
function plugin_footer_uninstall(): bool
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

/**
 * Only users able to update the translated link may add, update or purge its
 * translations, and translated URLs must be safe.
 *
 * @param DropdownTranslation $translation
 *
 * @return void
 */
function plugin_footer_pre_translation_write(DropdownTranslation $translation): void
{
    $types   = [PluginFooterLink::class, PluginFooterMenu::class];
    $input   = is_array($translation->input) ? $translation->input : [];
    $targets = [];

    if (!empty($translation->fields['itemtype'])) {
        $targets[] = [$translation->fields['itemtype'], (int) $translation->fields['items_id']];
    }
    if (isset($input['itemtype'])) {
        $targets[] = [$input['itemtype'], (int) ($input['items_id'] ?? 0)];
    }

    $is_ours = false;
    foreach ($targets as [$itemtype, $items_id]) {
        if (!in_array($itemtype, $types, true)) {
            continue;
        }
        $is_ours = true;
        $item    = new $itemtype();
        if (!$item->can($items_id, UPDATE)) {
            $translation->input = false;
            return;
        }
    }

    $field = $input['field'] ?? $translation->fields['field'] ?? '';
    if (
        $is_ours
        && $field === 'url'
        && array_key_exists('value', $input)
        && !PluginFooterLink::isSafeUrl($input['value'])
    ) {
        $translation->input = false;
    }
}

/**
 * @return array
 */
function plugin_footer_getDropdown(): array
{
    $plugin = new Plugin();

    if ($plugin->isActivated("footer")) {
        return [
            PluginFooterLink::class => PluginFooterLink::getTypeName(),
            PluginFooterMenu::class => PluginFooterMenu::getTypeName(),
        ];
    }

    return [];
}
