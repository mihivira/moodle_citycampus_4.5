<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Theme norte19 settings.
 *
 * @package   theme_norte19
 * @copyright 2022 ThemesAlmond  - http://themesalmond.com
 * @author    ThemesAlmond - Developer Team
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


defined('MOODLE_INTERNAL') || die();

if ($ADMIN->fulltree) {
    $settings = new theme_boost_admin_settingspage_tabs('themesettingnorte19', get_string('configtitle', 'theme_norte19'));
    $page = new admin_settingpage('theme_norte19_general', get_string('generalsettings', 'theme_norte19'));
    require_once('settings/iconlist.php');
    require('settings/general.php');
    require('settings/advanced.php');
    require('settings/theme.php');
    require('settings/frontpage_settings.php');
    require('settings/norte19page.php');
    require('settings/slideshow.php');
    require('settings/frontpage_block.php');
    require('settings/brands.php');
}
