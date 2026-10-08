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

/** @var \DBmysql $DB */
global $DB;

header("Content-Type: application/json; charset=UTF-8");
Html::header_nocache();

Session::checkLoginUser();

$config = new Config();

if (isset($_POST['action']) && $_POST['action'] == 'get_footer') {
    $query = [
        'FROM' => Link::getTable(),
        'WHERE' => getEntitiesRestrictCriteria(Link::getTable(), '', '', true),
    ];

    $link = [
        'config' => [
            'fixed' => $config->fields['fixed'],
        ],
        'links' => [],
    ];
    foreach ($DB->request($query) as $data) {
        $url = DropdownTranslation::getTranslatedValue(
            $data['id'],
            Link::getType(),
            'url',
            $_SESSION['glpilanguage'],
            $data['url'],
        );
        if (!Link::isSafeUrl((string) $url)) {
            continue;
        }
        $link['links'][] = [
            'url' => $url,
            'name' => DropdownTranslation::getTranslatedValue(
                $data['id'],
                Link::getType(),
                'name',
                $_SESSION['glpilanguage'],
                $data['name'],
            ),
        ];
    }

    echo json_encode($link, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT);
} elseif (isset($_POST['action']) && $_POST['action'] == 'get_menu') {
    $interface = 'helpdesk';
    if (isset($_SESSION['glpiactiveprofile']['interface'])) {
        $interface = $_SESSION['glpiactiveprofile']['interface'];
    }
    $query = [
        'FROM' => Menu::getTable(),
        'WHERE' => [
            'interface' => ['both', $interface],
        ] + getEntitiesRestrictCriteria(Menu::getTable(), '', '', true),
    ];
    $link = [];
    foreach ($DB->request($query) as $data) {
        $url = DropdownTranslation::getTranslatedValue(
            $data['id'],
            Menu::getType(),
            'url',
            $_SESSION['glpilanguage'],
            $data['url'],
        );
        if (!Link::isSafeUrl((string) $url)) {
            continue;
        }
        $link[] = [
            'url' => $url,
            'name' => DropdownTranslation::getTranslatedValue(
                $data['id'],
                Menu::getType(),
                'name',
                $_SESSION['glpilanguage'],
                $data['name'],
            ),
            'icon' => $data['icon'],
            'target' => ($data['is_blank']) ? '_blank' : '_self',
        ];
    }
    echo json_encode($link, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT);
}
