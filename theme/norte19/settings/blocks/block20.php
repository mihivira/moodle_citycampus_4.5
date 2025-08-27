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
// Block 20 info.
$name = 'theme_norte19/block20info';
$heading = get_string('block20info', 'theme_norte19');
$information = get_string('block20infodesc', 'theme_norte19');
$setting = new admin_setting_heading($name, $heading, $information);
$page->add($setting);
// Enable or disable block 20 settings.
$name = 'theme_norte19/block20enabled';
$title = get_string('block20enabled', 'theme_norte19');
$description = get_string('block20enableddesc', 'theme_norte19');
$setting = new admin_setting_configcheckbox($name, $title, $description, 1);
$page->add($setting);
// Block 20 background color palet.
$name = 'theme_norte19/footerbackgroundcolor';
$title = get_string('footerbackgroundcolor', 'theme_norte19');
$description = get_string('footerbackgroundcolordesc', 'theme_norte19');
$setting = new admin_setting_configcolourpicker($name, $title, $description, '');
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);
// Block 20 logo select.
$name = 'theme_norte19/block20logo';
$title = get_string('block20logo', 'theme_norte19');
$description = get_string('block20logodesc', 'theme_norte19');
$description = $description.get_string('underline', 'theme_norte19');
$default = "None";
$options = [
     'None' => 'None',
     'Logo' => 'Logo',
     'Small logo' => 'Small logo',
];
$setting = new admin_setting_configselect($name, $title, $description, $default, $options);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);
// Block 20 col 1 header.
$name = 'theme_norte19/block20col1header';
$title = get_string('block20col1header', 'theme_norte19');
$description = get_string('block20col1headerdesc', 'theme_norte19');
$default = "Site Name";
$setting = new admin_setting_configtext($name, $title, $description, $default, PARAM_TEXT);
$page->add($setting);
// Block 20 col 1 caption.
$name = 'theme_norte19/block20col1caption';
$title = get_string('block20col1caption', 'theme_norte19');
$description = get_string('block20col1captiondesc', 'theme_norte19');
$description = $description.get_string('underline', 'theme_norte19');
$default = get_string('block20col1captiondefault', 'theme_norte19');
$setting = new admin_setting_configtextarea($name, $title, $description, $default, PARAM_RAW, '1', '3');
$page->add($setting);
// Block 20 col 2 header.
$name = 'theme_norte19/block20col2header';
$title = get_string('block20col2header', 'theme_norte19');
$description = get_string('block20col2headerdesc', 'theme_norte19');
$default = "Company";
$setting = new admin_setting_configtext($name, $title, $description, $default, PARAM_TEXT);
$page->add($setting);
// Block 20 col 2 link area.
$name = 'theme_norte19/block20col2link';
$title = get_string('block20col2link', 'theme_norte19');
$description = get_string('block20col2linkdesc', 'theme_norte19');
$description = $description.get_string('underline', 'theme_norte19');
$default = get_string('block20col2linkdefault', 'theme_norte19');
$setting = new admin_setting_configtextarea($name, $title, $description, $default, PARAM_RAW, '1', '6');
$page->add($setting);
// Block 20 col 3 header.
$name = 'theme_norte19/block20col3header';
$title = get_string('block20col3header', 'theme_norte19');
$description = get_string('block20col3headerdesc', 'theme_norte19');
$default = "Help";
$setting = new admin_setting_configtext($name, $title, $description, $default, PARAM_TEXT);
$page->add($setting);
// Block 20 col 3 link area.
$name = 'theme_norte19/block20col3link';
$title = get_string('block20col3link', 'theme_norte19');
$description = get_string('block20col3linkdesc', 'theme_norte19');
$description = $description.get_string('underline', 'theme_norte19');
$default = get_string('block20col3linkdefault', 'theme_norte19');
$setting = new admin_setting_configtextarea($name, $title, $description, $default, PARAM_RAW, '1', '6');
$page->add($setting);
// Block 20 col 4 header.
$name = 'theme_norte19/block20col4header';
$title = get_string('block20col4header', 'theme_norte19');
$description = get_string('block20col4headerdesc', 'theme_norte19');
$default = "Company";
$setting = new admin_setting_configtext($name, $title, $description, $default, PARAM_TEXT);
$page->add($setting);
// Block 20 col 3 caption.
$name = 'theme_norte19/block20col4caption';
$title = get_string('block20col4caption', 'theme_norte19');
$description = get_string('block20col4captiondesc', 'theme_norte19');
$description = $description.get_string('underline', 'theme_norte19');
$default = get_string('block20col4captiondefault', 'theme_norte19');
$setting = new admin_setting_configtextarea($name, $title, $description, $default, PARAM_RAW, '1', '3');
$page->add($setting);
// Block 20 social links.
$name = 'theme_norte19/block20social';
$title = get_string('block20social', 'theme_norte19');
$description = get_string('block20socialdesc', 'theme_norte19');
$description = $description.get_string('underline', 'theme_norte19');
$default = get_string('block20socialdefault', 'theme_norte19');
$setting = new admin_setting_configtextarea($name, $title, $description, $default, PARAM_RAW, '1', '3');
$page->add($setting);
// Block 20 Copyright.
$name = 'theme_norte19/block20copyright';
$title = get_string('block20copyright', 'theme_norte19');
$description = get_string('block20copyrightdesc', 'theme_norte19');
$default = get_string('block20copyrightdefault', 'theme_norte19');
$setting = new admin_setting_configtextarea($name, $title, $description, $default, PARAM_RAW, '1', '2');
$page->add($setting);
// Enable or disable moodle frontpage orjinal button.
$name = 'theme_norte19/block20moodle';
$title = get_string('block20moodle', 'theme_norte19');
$description = get_string('block20moodledesc', 'theme_norte19');
$setting = new admin_setting_configcheckbox($name, $title, $description, 0);
$page->add($setting);
