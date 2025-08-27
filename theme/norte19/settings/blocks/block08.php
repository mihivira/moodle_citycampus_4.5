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
// Block 08 info.
$name = 'theme_norte19/block08info';
$heading = get_string('block08info', 'theme_norte19');
$information = get_string('block08infodesc', 'theme_norte19');
$setting = new admin_setting_heading($name, $heading, $information);
$page->add($setting);
// Enable or disable block 08 settings.
$name = 'theme_norte19/block08enabled';
$title = get_string('block08enabled', 'theme_norte19');
$description = get_string('block08enableddesc', 'theme_norte19');
$setting = new admin_setting_configcheckbox($name, $title, $description, 1);
$page->add($setting);
// Block 08 design select.
$name = 'theme_norte19/block08design';
$title = get_string('block08design', 'theme_norte19');
$description = get_string('block08designdesc', 'theme_norte19');
$default = 1;
$options = [];
for ($i = 1; $i <= 5; $i++) {
     $options[$i] = $i;
}
$setting = new admin_setting_configselect($name, $title, $description, $default, $options);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);
// Show total number of courses and students.
$name = 'theme_norte19/block08total';
$title = get_string('block08total', 'theme_norte19');
$description = get_string('block08totaldesc', 'theme_norte19');
$setting = new admin_setting_configcheckbox($name, $title, $description, 0);
$page->add($setting);
// Show description.
$name = 'theme_norte19/block08description';
$title = get_string('block08description', 'theme_norte19');
$description = get_string('block08descriptiondesc', 'theme_norte19');
$setting = new admin_setting_configcheckbox($name, $title, $description, 0);
$page->add($setting);
// Block 08 select show role.
$name = 'theme_norte19/block08showrole';
$title = get_string('block08showrole', 'theme_norte19');
$description = get_string('block08showroledesc', 'theme_norte19');
$default = 'editingteacher';
$options = [];
$role = $DB->get_records('role');
foreach ($role as $roles) {
     $options[$roles->id] = $roles->shortname;
}
$setting = new admin_setting_configselect($name, $title, $description, $default, $options);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);
// Block 08 select student role.
$name = 'theme_norte19/block08studentrole';
$title = get_string('block08studentrole', 'theme_norte19');
$description = get_string('block08studentroledesc', 'theme_norte19');
$default = 'student';
$options = [];
$role = $DB->get_records('role');
foreach ($role as $roles) {
     $options[$roles->id] = $roles->shortname;
}
$setting = new admin_setting_configselect($name, $title, $description, $default, $options);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);
// Block 08 count text.
$name = 'theme_norte19/block08count';
$title = get_string('block08count', 'theme_norte19');
$description = get_string('block08countdesc', 'theme_norte19');
$default = get_string('block08countdefault', 'theme_norte19');
$setting = new admin_setting_configtext($name, $title, $description, $default, PARAM_TEXT, '1');
$page->add($setting);
// Block 08 header text.
$name = 'theme_norte19/block08header';
$title = get_string('block08header', 'theme_norte19');
$description = get_string('block08headerdesc', 'theme_norte19');
$default = get_string('block08headerdefault', 'theme_norte19');
$setting = new admin_setting_configtext($name, $title, $description, $default, PARAM_TEXT);
$page->add($setting);
// Block 08 caption text.
$name = 'theme_norte19/block08caption';
$title = get_string('block08caption', 'theme_norte19');
$description = get_string('block08captiondesc', 'theme_norte19');
$default = get_string('block08captiondefault', 'theme_norte19');
$setting = new admin_setting_configtextarea($name, $title, $description, $default, PARAM_RAW, '1', '2');;
$page->add($setting);
