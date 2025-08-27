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
// Block 5 info.
$name = 'theme_norte19/block05info';
$heading = get_string('block05info', 'theme_norte19');
$information = get_string('block05infodesc', 'theme_norte19');
$setting = new admin_setting_heading($name, $heading, $information);
$page->add($setting);
// Enable or disable block 5 settings.
$name = 'theme_norte19/block05enabled';
$title = get_string('block05enabled', 'theme_norte19');
$description = get_string('block05enableddesc', 'theme_norte19');
$setting = new admin_setting_configcheckbox($name, $title, $description, 1);
$page->add($setting);
// Block 5 design select.
$name = 'theme_norte19/block05design';
$title = get_string('block05design', 'theme_norte19');
$description = get_string('block05designdesc', 'theme_norte19');
$default = 1;
$options = [];
for ($i = 1; $i < 2; $i++) {
     $options[$i] = $i;
}
$setting = new admin_setting_configselect($name, $title, $description, $default, $options);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);
// Block 5 img select.
$name = 'theme_norte19/sliderimageblock05img';
$title = get_string('sliderimageblock05img', 'theme_norte19');
$description = get_string('block05imgdesc', 'theme_norte19');
$setting = new admin_setting_configstoredfile($name, $title, $description, 'sliderimageblock05img');
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);
// Block 5 header text.
$name = 'theme_norte19/block05header';
$title = get_string('block05header', 'theme_norte19');
$description = get_string('block05headerdesc', 'theme_norte19');
$description = $description.get_string('underline', 'theme_norte19');
$default = get_string('block05headerdefault', 'theme_norte19');
$setting = new admin_setting_configtext($name, $title, $description, $default, PARAM_TEXT);
$page->add($setting);
// Block 05 general settings END.
// ------------------------------------------------------------------------------------.
for ($i = 1; $i <= 3; $i++) {
     // Block 05 icon .
     $name = 'theme_norte19/block05icon'.$i;
     $title = get_string('block05icon', 'theme_norte19', ['block' => $i]);
     $description = get_string('block05icondesc', 'theme_norte19');
     $default = get_string('block05icondefault'.$i, 'theme_norte19');
     $options = [];
     $options[] = theme_norte19_get_core_icon_list();
     $setting = new admin_setting_configselect($name, $title, $description, $default, $options);
     $setting->set_updatedcallback('theme_reset_all_caches');
     $page->add($setting);
     // Block 05 title.
     $name = 'theme_norte19/block05title'.$i;
     $title = get_string('block05title', 'theme_norte19', ['block' => $i]);
     $description = get_string('block05titledesc', 'theme_norte19');
     $default = 'WHY CHOOSE US '.$i;
     $setting = new admin_setting_configtext($name, $title, $description, $default);
     $page->add($setting);
     // Block 05 caption.
     $name = 'theme_norte19/block05caption'.$i;
     $title = get_string('block05caption', 'theme_norte19', ['block' => $i]);
     $description = get_string('block05captiondesc', 'theme_norte19');
     $default = 'Latest caption'.$i;
     $setting = new admin_setting_configtextarea($name, $title, $description, $default, PARAM_RAW, '1', '2');
     $page->add($setting);
     // Block 05 link .
     $name = 'theme_norte19/block05link'.$i;
     $title = get_string('block05link', 'theme_norte19', ['block' => $i]);
     $description = get_string('block05linkdesc', 'theme_norte19');
     $description = $description.get_string('underline', 'theme_norte19');
     $default = "https://moodle.org/";
     $setting = new admin_setting_configtext($name, $title, $description, $default, PARAM_URL);
     $page->add($setting);
}
