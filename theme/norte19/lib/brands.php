<?php
defined('MOODLE_INTERNAL') || die();

// Importar las funciones necesarias si no están disponibles globalmente
require_once($CFG->libdir . '/filelib.php');
require_once($CFG->libdir . '/weblib.php');

/**
 * Get configured brands from multiline setting
 */
function theme_norte19_get_configured_brands() {
    $gestor_marca = get_config('theme_norte19', 'gestor_marca');
    $marcas = [];
    
    if (!empty($gestor_marca)) {
        $lineas = explode("\n", $gestor_marca);
        foreach ($lineas as $linea) {
            $linea = trim($linea);
            if (!empty($linea)) {
                $partes = explode('|', $linea);
                $id = trim($partes[0]);
                $nombre = count($partes) > 1 ? trim($partes[1]) : $id;
                $marcas[$id] = $nombre;
            }
        }
    }
    
    return $marcas;
}

/**
 * Get the user's brand based on custom profile field
 */
function theme_norte19_get_user_brand($userid) {
    global $DB;
    
    $brandfield = get_config('theme_norte19', 'brandfield') ?: 'marca';
    $marcas_configuradas = theme_norte19_get_configured_brands();
    
    // Get user info with custom field
    $sql = "SELECT u.id, ud.data as brandvalue
            FROM {user} u
            LEFT JOIN {user_info_data} ud ON u.id = ud.userid
            LEFT JOIN {user_info_field} uf ON ud.fieldid = uf.id
            WHERE u.id = :userid AND uf.shortname = :fieldname";
    
    $params = ['userid' => $userid, 'fieldname' => $brandfield];
    $record = $DB->get_record_sql($sql, $params);
    
    if ($record && !empty($record->brandvalue)) {
        $brandvalue = trim($record->brandvalue);

        $user_marca = array_filter($marcas_configuradas, function($value) use ($brandvalue) {
            
            return $value === $brandvalue;
        });

        return !empty($user_marca) ? key($user_marca) : null;
    
    }
    
    return null;
}

/**
 * Get brand-specific configuration value
 */
function theme_norte19_get_brand_config($brand, $configkey, $default = null) {
    if ($brand) {
        $value = get_config('theme_norte19', $brand . $configkey);
        if (!empty($value)) {
            return $value;
        }
    }
    
    // Fallback to default configuration
    return get_config('theme_norte19', $configkey) ?: $default;
}

/**
 * Get logo URL based on user brand
 */
function theme_norte19_get_logo_url($brand = null) {
    if ($brand) {
       
        $logofile = get_config('theme_norte19', 'logo_' . $brand);
      
        $brand = strtolower(str_replace('_', '', $brand));
      
        if (strpos($logofile, '/') === 0) {
            $logofile = substr($logofile, 1);
        }
        if ($logofile) {
            $syscontext = context_system::instance();
            $url = moodle_url::make_pluginfile_url(
                $syscontext->id,
                'theme_norte19',
                'brand' . $brand,
                0,
                '/',
                $logofile
            );
            return $url->out();
        }
    }
    
    // Default logo
    $logourl = theme_norte19_get_setting_file_url('logo', 'logo');
    if (empty($logourl)) {
        $logourl = new moodle_url('/theme/norte19/pix/logo.png');
    }
    
    return $logourl;
}

/**
 * Helper function to get setting file URL
 */
function theme_norte19_get_setting_file_url($setting, $filearea) {
    global $PAGE;
    
    if (empty($PAGE->theme->settings->$setting)) {
        return null;
    }
    
    $itemid = theme_get_revision();
    $syscontext = context_system::instance();
    
    $url = moodle_url::make_pluginfile_url(
        $syscontext->id,
        'theme_norte19',
        $filearea,
        $itemid,
        '/',
        $PAGE->theme->settings->$setting
    );
    
    return $url->out();
}



/**
 * Debug function to check all brand configurations
 */
function theme_norte19_debug_configurations() {
    $brands = theme_norte19_get_configured_brands();
    $configs = ['color', 'backcolor', 'navbarcolor', 'sitecolor', 'logo'];
    
    foreach ($brands as $brandid => $brandname) {
        echo "<h3>Configuración de {$brandname} ({$brandid})</h3>";
        foreach ($configs as $config) {
            $value = get_config('theme_norte19', $brandid . $config);
            echo "<p>{$config}: " . ($value ?: '<span style="color:red">NO CONFIGURADO</span>') . "</p>";
        }
    }
    
    // Mostrar configuración por defecto
    echo "<h3>Configuración por defecto</h3>";
    foreach ($configs as $config) {
        $value = get_config('theme_norte19', $config);
        echo "<p>{$config}: " . ($value ?: '<span style="color:red">NO CONFIGURADO</span>') . "</p>";
    }
}

/**
 * Función temporal para forzar colores (para testing)
 */
function theme_norte19_force_colors($theme) {
    global $USER, $PAGE;
    $brand = theme_norte19_get_user_brand($USER->id);
    
    if ($brand) {
        $primary = get_config('theme_norte19', $brand . 'color') ?: '#ff0000'; // Rojo para testing
        $background = get_config('theme_norte19', $brand . 'backcolor') ?: '#f0f0f0';
        
        return "
            \$primary: {$primary};
            \$white: {$background};
            \$hynavbar: {$primary};
            \$hythemecolor: {$primary};
            
            body {
                background-color: {$background} !important;
            }
            
            .bg-primary {
                background-color: {$primary} !important;
            }
            
            .btn-primary {
                background-color: {$primary} !important;
                border-color: darken({$primary}, 10%) !important;
            }
        ";
    }
    
    return '';
}
