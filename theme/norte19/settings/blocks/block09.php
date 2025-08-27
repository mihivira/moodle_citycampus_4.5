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
 * Parent theme: boost
 *
 * @package  theme_norte19
 * @copyright 2022 ThemesAlmond  - http://themesalmond.com
 * @author    ThemesAlmond - Developer Team
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();
GLOBAL  $DB;
// Block 09 info.
$name = 'theme_norte19/block09info';
$heading = get_string('block09info', 'theme_norte19');
$information = get_string('block09infodesc', 'theme_norte19');
$setting = new admin_setting_heading($name, $heading, $information);
$page->add($setting);
// Enable or disable block 09 settings.
$name = 'theme_norte19/block09enabled';
$title = get_string('block09enabled', 'theme_norte19');
$description = get_string('block09enableddesc', 'theme_norte19');
$setting = new admin_setting_configcheckbox($name, $title, $description, 1);
$page->add($setting);
// Block 09 design select.
$name = 'theme_norte19/block09design';
$title = get_string('block09design', 'theme_norte19');
$description = get_string('block09designdesc', 'theme_norte19');
$default = 1;
$options = [];
for ($i = 1; $i < 4; $i++) {
     $options[$i] = $i;
}
$setting = new admin_setting_configselect($name, $title, $description, $default, $options);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);
// Block 09 background color or picture select.
$name = 'theme_norte19/block09background';
$title = get_string('block09background', 'theme_norte19');
$description = get_string('block09backgrounddesc', 'theme_norte19');
$default = "0";
$options = [
     '0' => 'none',
     '1' => 'color',
     '2' => 'picture',
];
$setting = new admin_setting_configselect($name, $title, $description, $default, $options);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);
// Block 09 count .
$name = 'theme_norte19/block09count';
$title = get_string('block09count', 'theme_norte19');
$description = get_string('block09countdesc', 'theme_norte19');
$default = get_string('block09countdefault', 'theme_norte19');
$setting = new admin_setting_configtext($name, $title, $description, $default, PARAM_TEXT, '1');
$page->add($setting);
// Box shadow enable or disable block 09 settings.
$name = 'theme_norte19/block09boxshadow';
$title = get_string('block09boxshadow', 'theme_norte19');
$description = get_string('block09boxshadowdesc', 'theme_norte19');
$setting = new admin_setting_configcheckbox($name, $title, $description, 0);
$page->add($setting);
// Block 09 header text.
$name = 'theme_norte19/block09header';
$title = get_string('block09header', 'theme_norte19');
$description = get_string('block09headerdesc', 'theme_norte19');
$default = get_string('block09headerdefault', 'theme_norte19');
$setting = new admin_setting_configtext($name, $title, $description, $default, PARAM_TEXT);
$page->add($setting);
// Block 09 caption text.
$name = 'theme_norte19/block09caption';
$title = get_string('block09caption', 'theme_norte19');
$description = get_string('block09captiondesc', 'theme_norte19');
$default = get_string('block09captiondefault', 'theme_norte19');
$setting = new admin_setting_configtextarea($name, $title, $description, $default, PARAM_RAW, '1', '2');;
$page->add($setting);
// Block 09 category id select.
$name = 'theme_norte19/block09ctgid';
$title = get_string('block09ctgid', 'theme_norte19');
$description = get_string('block09ctgiddesc', 'theme_norte19');
$default = "";
$setting = new admin_setting_configtext($name, $title, $description, $default, PARAM_TEXT);
$page->add($setting);
