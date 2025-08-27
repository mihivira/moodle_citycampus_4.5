<?php


require_once(__DIR__ . '/../lib.php');

// General settings page.
$page = new admin_settingpage('theme_norte19_brands', get_string('brands', 'theme_norte19'));

// Brand field setting.
$name = 'theme_norte19/brandfield';
$title = get_string('brandfield', 'theme_norte19');
$description = get_string('brandfield_desc', 'theme_norte19');
$default = 'marca';
$setting = new admin_setting_configtext($name, $title, $description, $default);
$page->add($setting);

// Marcas configuration (formato multilínea: marca1|Nombre Marca 1)
$name = 'theme_norte19/gestor_marca';
$title = get_string('gestor_marca', 'theme_norte19');
$description = get_string('gestor_marca_desc', 'theme_norte19');
$default = "marca1|Marca 1\nmarca2|Marca 2\nmarca3|Marca 3";
$setting = new admin_setting_configtextarea($name, $title, $description, $default);
$page->add($setting);

// Default logo.
$name = 'theme_norte19/logo';
$title = get_string('logo', 'theme_norte19');
$description = get_string('logodesc', 'theme_norte19');
$setting = new admin_setting_configstoredfile($name, $title, $description, 'logo');
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);

// Default colors (para usuarios sin marca o marca no configurada)
$name = 'theme_norte19/brandcolor';
$title = get_string('brandcolor', 'theme_norte19');
$description = get_string('brandcolor_desc', 'theme_norte19');
$default = '#0056b3';
$setting = new admin_setting_configcolourpicker($name, $title, $description, $default);
$page->add($setting);

$name = 'theme_norte19/backcolor';
$title = get_string('backcolor', 'theme_norte19');
$description = get_string('backcolor_desc', 'theme_norte19');
$default = '#ffffff';
$setting = new admin_setting_configcolourpicker($name, $title, $description, $default);
$page->add($setting);

$name = 'theme_norte19/navbarcolor';
$title = get_string('navbarcolor', 'theme_norte19');
$description = get_string('navbarcolor_desc', 'theme_norte19');
$default = '#0056b3';
$setting = new admin_setting_configcolourpicker($name, $title, $description, $default);
$page->add($setting);

$name = 'theme_norte19/sitecolor';
$title = get_string('sitecolor', 'theme_norte19');
$description = get_string('sitecolor_desc', 'theme_norte19');
$default = '#0056b3';
$setting = new admin_setting_configcolourpicker($name, $title, $description, $default);
$page->add($setting);

// Obtener marcas configuradas para crear settings dinámicos
$marcas = theme_norte19_get_configured_brands();

foreach ($marcas as $marcaid => $marcaname) {
    
    // Brand logo.
   

    $name = "theme_norte19/logo_{$marcaid}";
    $title = get_string("{$marcaid}logo", 'theme_norte19', $marcaname);
    $description = get_string("{$marcaid}logo_desc", 'theme_norte19', $marcaname);
    $marcaidclean = strtolower(str_replace('_', '', $marcaid));
    $setting = new admin_setting_configstoredfile($name, $title, $description, "brand".$marcaidclean);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    // Brand color.
    $name = "theme_norte19/{$marcaid}color";
    $title = get_string("{$marcaid}color", 'theme_norte19', $marcaname);
    $description = get_string("{$marcaid}color_desc", 'theme_norte19', $marcaname);
    $default = '#0056b3';
    $setting = new admin_setting_configcolourpicker($name, $title, $description, $default);
    $page->add($setting);

    // Background color.
    $name = "theme_norte19/{$marcaid}backcolor";
    $title = get_string("{$marcaid}backcolor", 'theme_norte19', $marcaname);
    $description = get_string("{$marcaid}backcolor_desc", 'theme_norte19', $marcaname);
    $default = '#ffffff';
    $setting = new admin_setting_configcolourpicker($name, $title, $description, $default);
    $page->add($setting);

    // Navbar color.
    $name = "theme_norte19/{$marcaid}navbarcolor";
    $title = get_string("{$marcaid}navbarcolor", 'theme_norte19', $marcaname);
    $description = get_string("{$marcaid}navbarcolor_desc", 'theme_norte19', $marcaname);
    $default = '#0056b3';
    $setting = new admin_setting_configcolourpicker($name, $title, $description, $default);
    $page->add($setting);

    // Site color.
    $name = "theme_norte19/{$marcaid}sitecolor";
    $title = get_string("{$marcaid}sitecolor", 'theme_norte19', $marcaname);
    $description = get_string("{$marcaid}sitecolor_desc", 'theme_norte19', $marcaname);
    $default = '#0056b3';
    $setting = new admin_setting_configcolourpicker($name, $title, $description, $default);
    $page->add($setting);
}



// Must add the page after definiting all the settings!
$settings->add($page);