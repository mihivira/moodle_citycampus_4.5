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
// Block 06 info.
$name = 'theme_norte19/block06info';
$heading = get_string('block06info', 'theme_norte19');
$information = get_string('block06infodesc', 'theme_norte19');
$setting = new admin_setting_heading($name, $heading, $information);
$page->add($setting);
// Enable or disable block 06 settings.
$name = 'theme_norte19/block06enabled';
$title = get_string('block06enabled', 'theme_norte19');
$description = get_string('block06enableddesc', 'theme_norte19');
$setting = new admin_setting_configcheckbox($name, $title, $description, 1);
$page->add($setting);
// Block 05 design select.
$name = 'theme_norte19/block06design';
$title = get_string('block06design', 'theme_norte19');
$description = get_string('block06designdesc', 'theme_norte19');
$default = 1;
$options = [];
for ($i = 1; $i < 3; $i++) {
     $options[$i] = $i;
}
$setting = new admin_setting_configselect($name, $title, $description, $default, $options);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);
// Block 06 background color.
$name = 'theme_norte19/block06color';
$title = get_string('block06color', 'theme_norte19');
$description = get_string('block06colordesc', 'theme_norte19');
$setting = new admin_setting_configcolourpicker($name, $title, $description, '');
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);
// Block 06 img select.
$name = 'theme_norte19/sliderimageblock06img';
$title = get_string('sliderimageblock06img', 'theme_norte19');
$description = get_string('block06imgdesc', 'theme_norte19');
$setting = new admin_setting_configstoredfile($name, $title, $description, 'sliderimageblock06img');
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);
// Block 06 header text.
$name = 'theme_norte19/block06header';
$title = get_string('block06header', 'theme_norte19');
$description = get_string('block06headerdesc', 'theme_norte19');
$default = get_string('block06headerdefault', 'theme_norte19');
$setting = new admin_setting_configtext($name, $title, $description, $default, PARAM_TEXT);
$page->add($setting);
// Block 06 caption.
$name = 'theme_norte19/block06caption';
$title = get_string('block06caption', 'theme_norte19');
$description = get_string('block06captiondesc', 'theme_norte19');
$default = get_string('block06captiondefault', 'theme_norte19');
$setting = new admin_setting_confightmleditor($name, $title, $description, $default);
$page->add($setting);
// Block 06 button.
$name = 'theme_norte19/block06button';
$title = get_string('block06button', 'theme_norte19');
$description = get_string('block06buttondesc', 'theme_norte19');
$default = get_string('button', 'theme_norte19');
$setting = new admin_setting_configtext($name, $title, $description, $default, PARAM_TEXT);
$page->add($setting);
// Block 06 button link.
$name = 'theme_norte19/block06buttonlink';
$title = get_string('block06buttonlink', 'theme_norte19');
$description = get_string('block06buttonlinkdesc', 'theme_norte19');
$default = 'http://www.example.com/';
$setting = new admin_setting_configtext($name, $title, $description, $default, PARAM_URL);
$page->add($setting);
