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
 * Theme norte19 slides.
 *
 * @package  theme_norte19
 * @copyright 2022 ThemesAlmond  - http://themesalmond.com
 * @author    ThemesAlmond - Developer Team
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();
$page = new admin_settingpage('theme_norte19_slide', get_string('slideshow', 'theme_norte19'));
$page->add(new admin_setting_heading('theme_norte19_slideshow', get_string('slideshowheading', 'theme_norte19'),
format_text(get_string('slideshowheadingdesc', 'theme_norte19'), FORMAT_MARKDOWN)));
// Slideshow design select.
$name = 'theme_norte19/sliderdesign';
$title = get_string('sliderdesign', 'theme_norte19');
$description = get_string('sliderdesigndesc', 'theme_norte19');
$default = 1;
$options = [];
for ($i = 1; $i < 5; $i++) {
    $options[$i] = $i;
}
$setting = new admin_setting_configselect($name, $title, $description, $default, $options);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);

// Enable or disable Slideshow settings.
$name = 'theme_norte19/sliderenabled';
$title = get_string('sliderenabled', 'theme_norte19');
$description = get_string('sliderenableddesc', 'theme_norte19');
$setting = new admin_setting_configcheckbox($name, $title, $description, 1);
$page->add($setting);

// Count Slideshow settings.
$name = 'theme_norte19/slidercount';
$title = get_string('slidercount', 'theme_norte19');
$description = get_string('slidercountdesc', 'theme_norte19');
$default = 4;
$options = [];
for ($i = 0; $i < 7; $i++) {
    $options[$i] = $i;
}
$setting = new admin_setting_configselect($name, $title, $description, $default, $options);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);
// If we don't have an slide yet, default to the preset.
$slidercount = get_config('theme_norte19', 'slidercount');

if (!$slidercount) {
    $slidercount = 1;
}
// Header size setting.
$name = 'theme_norte19/slidershowheight';
$title = get_string('slidershowheight', 'theme_norte19');
$description = get_string('slidershowheight_desc', 'theme_norte19');
$default = '550';
$options = [
    '250' => '250',
    '275' => '275',
    '300' => '300',
    '325' => '325',
    '350' => '350',
    '375' => '375',
    '400' => '400',
    '425' => '425',
    '450' => '450',
    '475' => '475',
    '500' => '500',
    '525' => '525',
    '550' => '550',
    '575' => '575',
    '600' => '600',
];
$setting = new admin_setting_configselect($name, $title, $description, $default, $options);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);

// Frontpage color opacity to slider.
$name = 'theme_norte19/slideropacity';
$title = get_string('slideropacity', 'theme_norte19');
$description = get_string('slideropacitydesc', 'theme_norte19');
$setting = new admin_setting_configcheckbox($name, $title, $description, 0);
$page->add($setting);

for ($count = 1; $count <= $slidercount; $count++) {
    $name = 'theme_norte19/slide' . $count . 'info';
    $heading = get_string('slideno', 'theme_norte19', ['slide' => $count]);
    $information = get_string('slidenodesc', 'theme_norte19', ['slide' => $count]);
    $setting = new admin_setting_heading($name, $heading, $information);
    $page->add($setting);
    // Slider image.
    $fileid = 'sliderimage'.$count;
    $name = 'theme_norte19/sliderimage'.$count;
    $title = get_string('sliderimage', 'theme_norte19');
    $description = get_string('sliderimagedesc', 'theme_norte19');
    $opts = ['accepted_types' => ['.png', '.jpg', '.gif', '.webp', '.tiff', '.svg'], 'maxfiles' => 1];
    $setting = new admin_setting_configstoredfile($name, $title, $description, $fileid,  0, $opts);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);
    // Slider title.
    $name = 'theme_norte19/slidertitle' . $count;
    $title = get_string('slidertitle', 'theme_norte19');
    $description = get_string('slidertitledesc', 'theme_norte19');
    $setting = new admin_setting_configtext($name, $title, $description, '', PARAM_TEXT);
    $page->add($setting);
    // Slider caption.
    $name = 'theme_norte19/slidercap' . $count;
    $title = get_string('slidercaption', 'theme_norte19');
    $description = get_string('slidercaptiondesc', 'theme_norte19');
    $default = '';
    $setting = new admin_setting_confightmleditor($name, $title, $description, $default);
    $page->add($setting);
    // Slider button.
    $name = 'theme_norte19/sliderbutton' . $count;
    $title = get_string('sliderbutton', 'theme_norte19');
    $description = get_string('sliderbuttondesc', 'theme_norte19');
    $default = get_string('button', 'theme_norte19');
    $setting = new admin_setting_configtext($name, $title, $description, $default, PARAM_TEXT);
    $page->add($setting);
    // Slide button link.
    $name = 'theme_norte19/sliderurl'. $count;
    $title = get_string('sliderbuttonurl', 'theme_norte19');
    $description = get_string('sliderbuttonurldesc', 'theme_norte19');
    $default = 'http://www.example.com/';
    $setting = new admin_setting_configtext($name, $title, $description, $default, PARAM_URL);
    $page->add($setting);
}
$page->add(new admin_setting_heading('theme_norte19_slideend', get_string('slideshowend', 'theme_norte19'),
format_text(get_string('slideshowenddesc', 'theme_norte19'), FORMAT_MARKDOWN)));
$settings->add($page);
