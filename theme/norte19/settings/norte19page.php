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
 * Theme norte19 page.
 *
 * @package  theme_norte19
 * @copyright 2022 ThemesAlmond  - http://themesalmond.com
 * @author    ThemesAlmond - Developer Team
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();
$page = new admin_settingpage('theme_norte19_norte19page', get_string('norte19page', 'theme_norte19'));
$page->add(new admin_setting_heading('theme_norte19_norte19page', get_string('norte19pageheading', 'theme_norte19'),
format_text(get_string('norte19pageheadingdesc', 'theme_norte19'), FORMAT_MARKDOWN)));
// Enable or disable page settings.
$name = 'theme_norte19/norte19pageenabled';
$title = get_string('norte19pageenabled', 'theme_norte19');
$description = get_string('norte19pageenableddesc', 'theme_norte19');
$setting = new admin_setting_configcheckbox($name, $title, $description, 1);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);
// Count page settings.
$name = 'theme_norte19/norte19pagecount';
$title = get_string('norte19pagecount', 'theme_norte19');
$description = get_string('norte19pagecountdesc', 'theme_norte19');
$default = 1;
$options = [];
for ($i = 1; $i <= 10; $i++) {
    $options[$i] = $i;
}
$setting = new admin_setting_configselect($name, $title, $description, $default, $options);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);
// If we don't have an slide yet, default to the preset.
$norte19pagecount = get_config('theme_norte19', 'norte19pagecount');
if (!$norte19pagecount) {
    $norte19pagecount = 2;
}
for ($count = 1; $count <= $norte19pagecount; $count++) {
    $name = 'theme_norte19/norte19page' . $count . 'info';
    $heading = get_string('norte19pageno', 'theme_norte19', ['norte19page' => $count]);
    $information = get_string('norte19pagenodesc', 'theme_norte19', ['norte19page' => $count]);
    $setting = new admin_setting_heading($name, $heading, $information);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);
    // Page title.
    $name = 'theme_norte19/norte19pagetitle' . $count;
    $title = get_string('norte19pagetitle', 'theme_norte19');
    $description = get_string('norte19pagetitledesc', 'theme_norte19');
    $setting = new admin_setting_configtext($name, $title, $description, '', PARAM_TEXT);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);
    // Page caption.
    $name = 'theme_norte19/norte19pagecap' . $count;
    $title = get_string('norte19pagecaption', 'theme_norte19');
    $description = get_string('norte19pagecaptiondesc', 'theme_norte19');
    $default = '';
    $setting = new admin_setting_confightmleditor($name, $title, $description, $default);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);
    // Page css link.
    $name = 'theme_norte19/norte19pagecsslink' . $count;
    $title = get_string('norte19pagecsslink', 'theme_norte19');
    $description = get_string('norte19pagecsslinkdesc', 'theme_norte19');
    $default = '';
    $setting = new admin_setting_configtextarea($name, $title, $description, $default, PARAM_RAW, '1', '1');
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);
    // Page img folder link.
    $name = 'theme_norte19/norte19pageimglink' . $count;
    $title = get_string('norte19pageimglink', 'theme_norte19');
    $description = get_string('norte19pageimglinkdesc', 'theme_norte19');
    $default = '';
    $setting = new admin_setting_configtextarea($name, $title, $description, $default, PARAM_RAW, '1', '1');
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);
    // Page css.
    $name = 'theme_norte19/norte19pagecss' . $count;
    $title = get_string('norte19pagecss', 'theme_norte19');
    $description = get_string('norte19pagecssdesc', 'theme_norte19');
    $default = '';
    $setting = new admin_setting_scsscode($name, $title, $description, $default, PARAM_RAW);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);
    // Add navbar to info page.
    $name = 'theme_norte19/norte19pagenavbar'. $count;
    $title = get_string('norte19pagenavbar', 'theme_norte19');
    $description = get_string('norte19pagenavbardesc', 'theme_norte19');
    $setting = new admin_setting_configcheckbox($name, $title, $description, 0);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);
    // Add header to info page.
    $name = 'theme_norte19/norte19pageheader'. $count;
    $title = get_string('norte19pageheader', 'theme_norte19');
    $description = get_string('norte19pageheaderdesc', 'theme_norte19');
    $setting = new admin_setting_configcheckbox($name, $title, $description, 0);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);
    // Add footer to info page.
    $name = 'theme_norte19/norte19pagefooter'. $count;
    $title = get_string('norte19pagefooter', 'theme_norte19');
    $description = get_string('norte19pagefooterdesc', 'theme_norte19');
    $setting = new admin_setting_configcheckbox($name, $title, $description, 0);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);
}
// Simple page.
$name = 'theme_norte19/norte19pageheadingsimple';
$heading = get_string('norte19pageheadingsimple', 'theme_norte19');
$information = get_string('norte19pageheadingsimpledesc', 'theme_norte19');
$setting = new admin_setting_heading($name, $heading, $information);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);
// Enable or disable page settings.
$name = 'theme_norte19/norte19pageenabledsimple';
$title = get_string('norte19pageenabledsimple', 'theme_norte19');
$description = get_string('norte19pageenabledsimpledesc', 'theme_norte19');
$setting = new admin_setting_configcheckbox($name, $title, $description, 1);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);
// Count page settings.
$name = 'theme_norte19/norte19pagecountsimple';
$title = get_string('norte19pagecountsimple', 'theme_norte19');
$description = get_string('norte19pagecountsimpledesc', 'theme_norte19');
$default = 1;
$options = [];
for ($i = 1; $i <= 10; $i++) {
    $options[$i] = $i;
}
$setting = new admin_setting_configselect($name, $title, $description, $default, $options);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);
// If we don't have an page yet, default to the preset.
$norte19pagecount = get_config('theme_norte19', 'norte19pagecountsimple');
if (!$norte19pagecount) {
    $norte19pagecount = 2;
}
for ($count = 1; $count <= $norte19pagecount; $count++) {
    $name = 'theme_norte19/norte19pagesimple' . $count . 'info';
    $heading = get_string('norte19pagenosimple', 'theme_norte19', ['norte19page' => $count]);
    $information = get_string('norte19pagenosimpledesc', 'theme_norte19', ['norte19page' => $count]);
    $setting = new admin_setting_heading($name, $heading, $information);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);
    // Page title.
    $name = 'theme_norte19/norte19pagetitlesimple' . $count;
    $title = get_string('norte19pagetitlesimple', 'theme_norte19');
    $description = get_string('norte19pagetitlesimpledesc', 'theme_norte19');
    $setting = new admin_setting_configtext($name, $title, $description, '', PARAM_TEXT);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);
    // Page image.
    $fileid = 'sliderimagenorte19pagesimple'.$count;
    $name = 'theme_norte19/sliderimagenorte19pagesimple'.$count;
    $title = get_string('norte19pageimagesimple', 'theme_norte19');
    $description = get_string('norte19pageimagesimpledesc', 'theme_norte19');
    $opts = ['accepted_types' => ['.png', '.jpg', '.gif', '.webp', '.tiff', '.svg'], 'maxfiles' => 1];
    $setting = new admin_setting_configstoredfile($name, $title, $description, $fileid,  0, $opts);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);
    // Count page settings.
    $name = 'theme_norte19/norte19pageimgpositionsimple'.$count;
    $title = get_string('norte19pageimgpositionsimple', 'theme_norte19');
    $description = get_string('norte19pageimgpositionsimpledesc', 'theme_norte19');
    $default = 1;
    $options = [
        "1" => "Background",
        "2" => "Top",
        "21" => "Full Top",
        "3" => "Left",
        "4" => "Right",
    ];
    $setting = new admin_setting_configselect($name, $title, $description, $default, $options);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);
    // Page caption.
    $name = 'theme_norte19/norte19pagecapsimple'.$count;
    $title = get_string('norte19pagecaptionsimple', 'theme_norte19');
    $description = get_string('norte19pagecaptionsimpledesc', 'theme_norte19');
    $default = '';
    $setting = new admin_setting_confightmleditor($name, $title, $description, $default);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);
    // Add header to info page.
    $name = 'theme_norte19/norte19pageheadersimple'. $count;
    $title = get_string('norte19pageheadersimple', 'theme_norte19');
    $description = get_string('norte19pageheadersimpledesc', 'theme_norte19');
    $setting = new admin_setting_configcheckbox($name, $title, $description, 0);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);
    // Add footer to info page.
    $name = 'theme_norte19/norte19pagefootersimple'. $count;
    $title = get_string('norte19pagefootersimple', 'theme_norte19');
    $description = get_string('norte19pagefootersimpledesc', 'theme_norte19');
    $setting = new admin_setting_configcheckbox($name, $title, $description, 0);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);
}
$page->add(new admin_setting_heading('theme_norte19_norte19pageend', get_string('norte19pageend', 'theme_norte19'),
format_text(get_string('norte19pageenddesc', 'theme_norte19'), FORMAT_MARKDOWN)));
$settings->add($page);
