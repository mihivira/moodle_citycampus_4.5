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
// Block 02 info.
$name = 'theme_norte19/block02info';
$heading = get_string('block02info', 'theme_norte19');
$information = get_string('block02infodesc', 'theme_norte19');
$setting = new admin_setting_heading($name, $heading, $information);
$page->add($setting);
// Enable or disable block 02 settings.
$name = 'theme_norte19/block02enabled';
$title = get_string('block02enabled', 'theme_norte19');
$description = get_string('block02enableddesc', 'theme_norte19');
$setting = new admin_setting_configcheckbox($name, $title, $description, 1);
$page->add($setting);

// Count block 2 settings.
$name = 'theme_norte19/block02count';
$title = get_string('block02count', 'theme_norte19');
$description = get_string('block02countdesc', 'theme_norte19');
$description = $description.get_string('underline', 'theme_norte19');
$default = 3;
$options = [];
for ($i = 2; $i < 5; $i++) {
     $options[$i] = $i;
}
$setting = new admin_setting_configselect($name, $title, $description, $default, $options);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);
// If we don't have an slide yet, default to the preset.
$count = get_config('theme_norte19', 'block02count');
// Block 02 general settings END.
// ------------------------------------------------------------------------------------.
for ($i = 1; $i < $count + 1; $i++) {
     // Block 02 icon.
     $name = 'theme_norte19/block02icon'.$i;
     $title = get_string('block02icon', 'theme_norte19', ['block' => $i]);
     $description = get_string('block02icondesc', 'theme_norte19');
     $default = get_string('block02icondefault'.$i, 'theme_norte19');
     $options = [];
     $options[] = theme_norte19_get_core_icon_list();
     $setting = new admin_setting_configselect($name, $title, $description, $default, $options);
     $setting->set_updatedcallback('theme_reset_all_caches');
     $page->add($setting);
     // Block 02 img.
     $name = 'theme_norte19/sliderimageblock02img'.$i;
     $title = get_string('sliderimageblock02img', 'theme_norte19', ['block' => $i]);
     $description = get_string('block02imgdesc', 'theme_norte19');
     $setting = new admin_setting_configstoredfile($name, $title, $description, 'sliderimageblock02img'.$i);
     $setting->set_updatedcallback('theme_reset_all_caches');
     $page->add($setting);
     // Block 02 title.
     $name = 'theme_norte19/block02title'.$i;
     $title = get_string('block02title', 'theme_norte19', ['block' => $i]);
     $description = get_string('block02titledesc', 'theme_norte19');
     $default = get_string('block02titledefault', 'theme_norte19');
     $setting = new admin_setting_configtext($name, $title, $description, $default);
     $page->add($setting);
     // Block 02 caption.
     $name = 'theme_norte19/block02caption'.$i;
     $title = get_string('block02caption', 'theme_norte19', ['block' => $i]);
     $description = get_string('block02captiondesc', 'theme_norte19');
     $default = get_string('block02captiondefault', 'theme_norte19');
     $setting = new admin_setting_confightmleditor($name, $title, $description, $default);
     $page->add($setting);
     // Block 02 button.
     $name = 'theme_norte19/block02button'.$i;
     $title = get_string('block02button', 'theme_norte19', ['block' => $i]);
     $description = get_string('block02buttondesc', 'theme_norte19');
     $default = get_string('button', 'theme_norte19');
     $setting = new admin_setting_configtext($name, $title, $description, $default, PARAM_TEXT);
     $page->add($setting);
     // Block 02 button link.
     $name = 'theme_norte19/block02buttonlink'.$i;
     $title = get_string('block02buttonlink', 'theme_norte19', ['block' => $i]);
     $description = get_string('block02buttonlinkdesc', 'theme_norte19');
     $description = $description.get_string('underline', 'theme_norte19');
     $default = get_string('buttonlink', 'theme_norte19');
     $setting = new admin_setting_configtext($name, $title, $description, $default, PARAM_URL);
     $page->add($setting);
}
