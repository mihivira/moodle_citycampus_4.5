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
 * Plugin administration pages are defined here.
 *
 * @package     local_subitus_theme_selector
 * @category    admin
 * @copyright   2019 Subitus <contacto@subitus.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

// TODO: Define the plugin settings page.
// https://docs.moodle.org/dev/Admin_settings

// $upgradepage = isset($_SERVER['REQUEST_URI']) ? strpos($_SERVER['REQUEST_URI'], "/admin/upgradesettings.php") : false;
// $up2 = isset($_SERVER['REQUEST_URI']) ? strpos($_SERVER['REQUEST_URI'], "/moodle36/admin/index.php") : false;
if($hassiteconfig){
    $pluginName = 'local_subitus_theme_selector';
    $pluginTitle = 'Selección de temas según campo';
    $settings = new admin_settingpage( $pluginName, $pluginTitle );
    $ADMIN->add('localplugins', $settings);

    $themes = array();
    foreach (get_list_of_themes() as $key => $theme) {
        $themes[$key] = $theme->name;
    }

    $settings->add(new admin_setting_heading($pluginName . '_num_rules' ,
     'Número de perfiles',
     "Si desea agregar nuevos perfiles, incremente este número y después de clic en guardar cambios para que aparezca el nuevo perfil y pueda agregarlo"));
    $setting = new admin_setting_configtext('num_rules', 'Número de perfiles', 'Nombre descriptivo del perfil', 6, PARAM_INT);
    $setting->plugin = $pluginName;
    $settings->add($setting);

    $num_rules = get_config($pluginName, 'num_rules');    
    if(empty($num_rules)){
        $num_rules = 6;
    }
    
    $custom_fields = $DB->get_records_menu($table = 'user_info_field', $conditions_array = array(), $sort = '', $fields = 'id, name');
    if(!empty($custom_fields)){
        for($i = 1; $i <= $num_rules; $i++){
            $settings->add(new admin_setting_heading($pluginName . '_' . $i,
             'Ingrese los campos que se requieren en este formulario para el perfil', "Todos los campos son necesarios o de lo contrario la configuración no será válida"));

            $setting = new admin_setting_configtext('name_'.$i, 'Nombre del perfil', 'Nombre descriptivo del perfil', 'Perfil ' . $i, PARAM_TEXT);
            $setting->plugin = $pluginName;
            $settings->add($setting);

            $setting = new admin_setting_configselect('field_'.$i, "Campo personalizado", 'El campo de perfil que contiene el tipo de usuario', null, $custom_fields);
            $setting->plugin = $pluginName;
            $settings->add($setting);

            $setting = new admin_setting_configtext('text_'.$i, 'Texto que debe contener', 'El usuario debe tener la información en el campo personalizado para aplicar el tema', '', PARAM_TEXT);
            $setting->plugin = $pluginName;
            $settings->add($setting);

            $setting = new admin_setting_configselect('theme_'.$i, "Se aplicará el tema", 'Este tema será aplicado al usuario que cumpla las condiciones', null, $themes);
            $setting->plugin = $pluginName;
            $settings->add($setting);

            $setting = new admin_setting_configselect('status_'.$i, "¿Activar?", '¿Desea que esta configuración sea utilizada?', null, array(1 => 'Sí', 0 => 'No'));
            $setting->plugin = $pluginName;
            $settings->add($setting);
        }
    }
}
// if($hassiteconfig){
//     if($upgradepage === false && $up2 === false){
//         $themes = array();
//         foreach (get_list_of_themes() as $key => $theme) {
//             $themes[$key] = $theme->name;
//         }
//         $custom_fields = $DB->get_records('user_info_field', array(), 'id,name');
//         $fields = array();
//         foreach ($custom_fields as $customField) {
//             $fields[$customField->id] = $customField->name;
//         }
//         $custom_fields = null;

//         // Comienza obtención de perfiles
//         $configs = (array) get_config('local_subitus_theme_selector');
//         $keys = array_keys($configs);
//         $perfiles = array();
//         $id_de_perfiles = array();
//         foreach ($keys as $key) {
//             if( strpos($key, 'name_') !== false ){
//                 $numero = substr($key, strpos($key, '_') + 1);
//                 array_push($id_de_perfiles, $numero);
//             }
//         }
//         // Termina obtención de perfiles
//         $helper = new AgregarReglas($pluginName, $themes, $fields, $configs);

//         $settings = new admin_settingpage( $pluginName, $pluginTitle );
//         $ADMIN->add('localplugins', $settings);
//         foreach ($id_de_perfiles as $perfil) {
//             if($configs['name_'.$perfil] == ''){
//                 $helper->mostrar_regla($settings, $perfil);
//             }else{
//                 $helper->mostrar_regla($settings, $perfil, false);
//             }
//         }

//         if(isset($perfil)){
//             if(is_numeric($perfil)){
//                 $ultimo_perfil = $perfil;
//             }else{
//                 $perfil = 1;
//             }
//         }else{
//             $perfil = 1;
//         }
//         if(empty($id_de_perfiles)){ // No existen perfiles
//             $perfil = 1;
//             $helper->mostrar_regla($settings, $perfil);
//         }else{ // Existe al menos un perfil en el administrador
//             if($configs['name_'.$perfil] != ''){
//                 $perfil = max($id_de_perfiles) + 1;
//                 $helper->mostrar_regla($settings, $perfil);
//             }
//         }
//     }else{
//         $pluginName = 'local_subitus_theme_selector';
//         $settings = new admin_settingpage( $pluginName, $pluginTitle );
//         $settings->add(new admin_setting_heading('h_', '¿Desea activar '.$pluginName.'?', ""));
//         $setting = new admin_setting_configselect('activar', "¿Activar?", '¿Desea que el plugin modifique los temas?', null, array(1 => 'Sí', 0 => 'No'));
//         $setting->plugin = $pluginName;
//         $settings->add($setting);
//         $ADMIN->add('localplugins', $settings);
//     }
// }
