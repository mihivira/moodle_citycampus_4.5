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
 * Form for editing a users profile
 *
 * @copyright 1999 Martin Dougiamas  http://dougiamas.com
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @package core_user
 */

if (!defined('MOODLE_INTERNAL')) {
    die('Direct access to this script is forbidden.');    //  It must be included from a Moodle page.
}

require_once($CFG->dirroot.'/lib/formslib.php');
require_once(__DIR__ . '/lib.php');
// require_once($CFG->dirroot . '/user/profile/lib.php');

/**
 * Class filter_settings.
 *
 * @copyright 2019 Subitus contacto@subitus.com
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class filter_settings extends moodleform {

    /**
     * Define the form.
     */
    public function definition() {
        global $USER, $CFG, $COURSE;

        $mform = $this->_form;

        $pluginname = 'local_hoteles_city_dashboard';

        if (!is_array($this->_customdata)) {
            throw new coding_exception('invalid custom data for custom_settings hoteles_city_dashboard');
        }
        $configs = $this->_customdata['configs'];
        $default_profile_fields = local_hoteles_city_dashboard_get_default_profile_fields(true);
        $all_default_profile_fields = local_hoteles_city_dashboard_get_default_profile_fields();
        $custom_fields = local_hoteles_city_dashboard_get_custom_profile_fields();
        $default_profile_fields[''] = 'Presione para seleccionar';
        $all_default_profile_fields[''] = 'Presione para seleccionar';
        $custom_fields[''] = 'Presione para seleccionar';        
        // echo local_hoteles_city_dashboard_print_theme_variables();
        // $configs = get_config('local_hoteles_city_dashboard');
        // $configs = (array) $configs;
        

        $mform->addElement('header', 'special_custom_fields', get_string('special_custom_fields_header', $pluginname));

        foreach (LOCAL_HOTELES_CITY_DASHBOARD_SPECIAL_CUSTOM_FIELDS as $key => $value) {
            $name = $key;
            $title = $value;
            $description = "Seleccione el campo personalizado correspondiente a " . strtolower($value); // get_string($name . '_desc', $pluginname);
            $default = !empty($configs[$name]) ? $configs[$name] : "";
            $select = $mform->addElement('select', $name, $title, $custom_fields);
            $mform->setDefault($name, $default);
            $mform->addElement('static', 'description', '', $description);
        }

        $courses = local_hoteles_city_dashboard_get_courses();
        $courses[''] = 'Presione para seleccionar';
        $name = 'dashboard_courses';
        $title = get_string('dashboard_courses', $pluginname);
        $description = get_string('dashboard_courses' . '_desc', $pluginname);
        $default = !empty($configs[$name]) ? $configs[$name] : '';
        $select = $mform->addElement('select', $name, $title, $courses, 'class = " multiselect-setting " size="10" ');
        $select->setMultiple(true);
        $mform->getElement($name)->setSelected(explode(',', $default));
        $mform->addElement('static', 'description', '', $description);

        $institutions = local_hoteles_city_dashboard_get_all_institutions();
        $institutions[''] = 'Presione para seleccionar';
        $name = 'direcciones_oficina_central';
        $title = get_string('direcciones_oficina_central', $pluginname);
        $description = get_string('direcciones_oficina_central' . '_desc', $pluginname);
        $default = !empty($configs[$name]) ? $configs[$name] : '';
        $select = $mform->addElement('select', $name, $title, $institutions, 'class = " multiselect-setting " size="10" ');
        $select->setMultiple(true);
        $mform->getElement($name)->setSelected(explode(',', $default));
        $mform->addElement('static', 'description', '', $description);

        $puestos = local_hoteles_city_dashboard_get_departments();
        $puestos[''] = 'Presione para seleccionar';
        $name = 'puestos_en_dashboard';
        $title = get_string('puestos_en_dashboard', $pluginname);
        $description = get_string('puestos_en_dashboard' . '_desc', $pluginname);
        $default = !empty($configs[$name]) ? $configs[$name] : '';
        $select = $mform->addElement('select', $name, $title, $puestos, 'class = " multiselect-setting " size="10" ');
        $select->setMultiple(true);
        $mform->getElement($name)->setSelected(explode(',', $default));
        $mform->addElement('static', 'description', '', $description);


        $mform->addElement('header', 'signinfields', get_string('signinfields', $pluginname));
        $mform->setExpanded('signinfields', false);

        $name = 'userformdefaultfields';
        $title = get_string('userformdefaultfields', 'local_hoteles_city_dashboard');
        $description = get_string('userformdefaultfields' . '_desc', $pluginname);
        $default = !empty($configs[$name]) ? $configs[$name] : "";
        $select = $mform->addElement('select', $name, $title, $default_profile_fields, 'class = " multiselect-setting " size="10" ');
        $select->setMultiple(true);
        $mform->getElement($name)->setSelected(explode(',', $default));
        $mform->addElement('static', 'description', '', $description);

        $name = 'userformcustomfields';
        $title = get_string('userformcustomfields', $pluginname);
        $description = get_string('userformcustomfields' . '_desc', $pluginname);
        $default = !empty($configs[$name]) ? $configs[$name] : "";
        $select = $mform->addElement('select', $name, $title, $custom_fields, 'class = " multiselect-setting " size="10" ');
        $select->setMultiple(true);
        $mform->getElement($name)->setSelected(explode(',', $default));
        $mform->addElement('static', 'description', '', $description);



        $mform->addElement('header', 'reportfields_header', get_string('reportfields_header', $pluginname));
        $mform->setExpanded('reportfields_header', false);

        $name = 'reportdefaultfields';
        $title = get_string('reportdefaultfields', $pluginname);
        $description = get_string('reportdefaultfields' . '_desc', $pluginname);
        $default = !empty($configs[$name]) ? $configs[$name] : "";
        $select = $mform->addElement('select', $name, $title, $all_default_profile_fields, 'class = " multiselect-setting " size="10" ');
        $select->setMultiple(true);
        $mform->getElement($name)->setSelected(explode(',', $default));
        $mform->addElement('static', 'description', '', $description);

        $name = 'reportcustomfields';
        $title = get_string('reportcustomfields', $pluginname);
        $description = get_string('reportcustomfields' . '_desc', $pluginname);
        $default = !empty($configs[$name]) ? $configs[$name] : "";
        $select = $mform->addElement('select', $name, $title, $custom_fields, 'class = " multiselect-setting " size="10" ');
        $select->setMultiple(true);
        $mform->getElement($name)->setSelected(explode(',', $default));
        $mform->addElement('static', 'description', '', $description);


        $mform->addElement('header', 'filterfields', get_string('filterfields', $pluginname));
        $mform->setExpanded('filterfields', false);


        $name = 'filtercustomfields';
        $title = get_string('filtercustomfields', $pluginname);
        $description = get_string('filtercustomfields' . '_desc', $pluginname);
        $default = !empty($configs[$name]) ? $configs[$name] : "";
        $select = $mform->addElement('select', $name, $title, $custom_fields, 'class = " multiselect-setting " size="10" ');
        $select->setMultiple(true);
        $mform->getElement($name)->setSelected(explode(',', $default));
        $mform->addElement('static', 'description', '', $description);

        




    }

    /**
     * Extend the form definition after data has been parsed.
     */
    public function definition_after_data() {

    }

    /**
     * Validate the form data.
     * @param array $usernew
     * @param array $files
     * @return array|bool
     */
    public function validation($usernew, $files) {

    }
}

/**
 * Class permission_settings.
 *
 * @copyright 2019 Subitus contacto@subitus.com
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class permission_settings extends moodleform {

    /**
     * Define the form.
     */
    public function definition() {
        global $USER, $CFG, $COURSE;

        $mform = $this->_form;
        echo local_hoteles_city_dashboard_print_theme_variables();
        // $configs = get_config('local_hoteles_city_dashboard');
        // $configs = (array) $configs;
        $pluginname = 'local_hoteles_city_dashboard';

        if (!is_array($this->_customdata)) {
            throw new coding_exception('invalid custom data for custom_settings hoteles_city_dashboard');
        }
        $configs = $this->_customdata['configs'];


        // $strgeneral  = get_string('general');
        $strrequired = get_string('required');

        $mform->addElement('header', 'permissions', get_string('filterfields', $pluginname));
        $mform->setExpanded('permissions', true);

        foreach (local_hoteles_city_dashboard_get_dashboard_roles() as $key => $value) {
            $name = $key;            
            $default = !empty($configs[$name]) ? $configs[$name] : "";
            $mform->addElement('text', $name, $value, 'size = "80"');
            $mform->addRule($name, $strrequired, 'required');
            $mform->setDefault($name, $default);
            $mform->setType($name, PARAM_TEXT);
            $mform->addElement('static', 'description', '', 'Escriba el correo de los usuarios que tendrán el perfil "' . strtolower($value) . '" separados por un espacio');
        }

        $gerentes_generales = local_hoteles_city_dashboard_get_gerentes_generales();
        $subdirectores_regionales = local_hoteles_city_dashboard_get_subdirectores_regionales();
        $directores_regionales = local_hoteles_city_dashboard_get_directores_regionales_op();        

        $gerentes_generales = implode(', ', $gerentes_generales);
        $subdirectores_regionales = implode(', ', $subdirectores_regionales);
        $directores_regionales = implode(', ', $directores_regionales);

        $mform->addElement('static', 'description', '', '');
        $mform->addElement('static', 'description', 'Gerentes generales', $gerentes_generales);

        $mform->addElement('static', 'description', 'Subdirectores regionales de ventas', $subdirectores_regionales);
        $mform->addElement('static', 'description', '', '');

        $mform->addElement('static', 'description', 'Directores regionales de Operaciones', $directores_regionales);
        $mform->addElement('static', 'description', '', '');
    }

    /**
     * Extend the form definition after data has been parsed.
     */
    public function definition_after_data() {

    }

    /**
     * Validate the form data.
     * @param array $usernew
     * @param array $files
     * @return array|bool
     */
    public function validation($usernew, $files) {

    }
}

/**
 * Class permission_settings.
 *
 * @copyright 2019 Subitus contacto@subitus.com
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class gestor_settings extends moodleform {

    /**
     * Define the form.
     */
    public function definition() {
        global $USER, $CFG, $COURSE;

        $mform = $this->_form;   
        
        $pluginname = 'local_hoteles_city_dashboard';

        $configs = $this->_customdata['configs'];
        
        // Marcas y Unidades

        $mform->addElement('header', 'gestortxt', get_string('gestor_title_settings', $pluginname));
        $mform->setExpanded('gestortxt', true);

        $name = 'gestor_marca';
        $description = 'Nota: En caso de que la Marca contenga espacios, reemplazarlos con _';
        $default = !empty($configs[$name]) ? $configs[$name] : "";
        $mform->setDefault($name, $default);
        $mform->addElement('textarea', $name, 'Marca (una por línea)', 'wrap="virtual" rows="6" cols="40"');
        $mform->addElement('static', 'description', '', $description);

        $name = 'gestor_uo';
        $default = !empty($configs[$name]) ? $configs[$name] : "";
        $mform->setDefault($name, $default);
        $mform->addElement('textarea', 'gestor_uo', 'Unidad Operativa (una por línea)', 'wrap="virtual" rows="6" cols="40"');

        // Seleccionar Marcas y Unidades

        $mform->addElement('header', 'selectfields', 'Seleccionar Unidades Operativas en las Marcas');
        $mform->setExpanded('selectfields', false);

        $getmarcas = get_config('local_hoteles_city_dashboard', 'gestor_marca');        
        $arraymarca = explode("\n", $getmarcas);
        $countmarca = count($arraymarca);
        $getuo = get_config('local_hoteles_city_dashboard', 'gestor_uo');
        $arrayuo = explode("\n", $getuo);
        $arrayunidades = array();
        foreach ($arrayuo as $single_uo) {
            $txt_uo = preg_replace("/[\r\n|\n|\r]+/", "", $single_uo);
            $arrayunidades[$txt_uo] = $txt_uo;            
        }         
        $unidadoperativa = $arrayunidades; 
        $unidadoperativa['-1'] = '---';          
             
        if($getmarcas){
            for ($i=0; $i < $countmarca; $i++) {                
                $txt_marca = preg_replace("/[\r\n|\n|\r]+/", "", $arraymarca[$i]);                
                $id_name = 'uo_'.$txt_marca;                              
                $txt_name = preg_replace("/[\r\n|\n|\r]+/", "", $id_name);               
                $name = $txt_name;                
                $title = $name;                 
                $addr = strtr($name, "_", " ");                           
                $description = 'Selecciona las unidades operativas para ' . $arraymarca[$i];
                $default = !empty($configs[$name]) ? $configs[$name] : "";                
                $select = $mform->addElement('select', $name, $title, $unidadoperativa, 'class = " multiselect-setting " size="10" ');
                $select->setMultiple(true);
                $mform->getElement($name)->setSelected(explode(',', $default));                        
                $mform->addElement('static', 'description', '', $description);
                
            } 
        }        
    }

    /**
     * Extend the form definition after data has been parsed.
     */
    public function definition_after_data() {

    }

    /**
     * Validate the form data.
     * @param array $usernew
     * @param array $files
     * @return array|bool
     */
    public function validation($usernew, $files) {

    }
}

/**
 * Class 
 *
 * @copyright 2023 Miguel Villegas contacto@sdvir.com
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class certificacion_settings extends moodleform {

    /**
     * Define the form.
     */
    public function definition() {
        global $USER, $CFG, $COURSE;

        $mform = $this->_form;   
        
        $pluginname = 'local_hoteles_city_dashboard';

        $configs = $this->_customdata['configs'];

        $courses = local_hoteles_city_dashboard_get_courses();
        $courses['-1'] = '---'; 
        $name = 'certification_courses';
        $title = get_string('certification_courses', $pluginname);
        $description = get_string('certification_courses' . '_desc', $pluginname);
        $default = !empty($configs[$name]) ? $configs[$name] : '';
        $select = $mform->addElement('select', $name, $title, $courses, 'class = " multiselect-setting " size="10" ');
        $select->setMultiple(true);
        $mform->getElement($name)->setSelected(explode(',', $default));
        $mform->addElement('static', 'description', '', $description);

        $regs = local_hoteles_city_dashboard_get_regions();
        $regions = [];
        
        foreach ($regs as $region) {  
            $regions[$region->name] = $region->name;
        }
        $regions['-1'] = '---';
        
        $name = 'certification_regions';
        $title = get_string('certification_regions', $pluginname);
        $description = get_string('certification_regions' . '_desc', $pluginname);
        $default = !empty($configs[$name]) ? $configs[$name] : '';
        $select = $mform->addElement('select', $name, $title, $regions, 'class = " multiselect-setting " size="10" ');
        $select->setMultiple(true);
        $mform->getElement($name)->setSelected(explode(',', $default));
        $mform->addElement('static', 'description', '', $description);
        
        
    }

    /**
     * Extend the form definition after data has been parsed.
     */
    public function definition_after_data() {

    }

    /**
     * Validate the form data.
     * @param array $usernew
     * @param array $files
     * @return array|bool
     */
    public function validation($usernew, $files) {

    }
}


/**
 * Class .
 *
 * @copyright 2023 Miguel Villegas contacto@sdvir.com
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class catalogo_puestos_settings extends moodleform {

    /**
     * Define the form.
     */
    public function definition() {
        global $USER, $CFG, $COURSE;

        $mform = $this->_form;   
        
        $pluginname = 'local_hoteles_city_dashboard';

        $configs = $this->_customdata['configs'];
        
        // Marcas y Puestos

        $puestos = local_hoteles_city_dashboard_get_departments();
        $puestos['-1'] = '---';
     
        $getmarcas = get_config('local_hoteles_city_dashboard', 'gestor_marca');        
        $arraymarca = explode("\n", $getmarcas);
        $countmarca = count($arraymarca);
                
             
        if($getmarcas){
            for ($i=0; $i < $countmarca; $i++) {                
                $txt_marca = preg_replace("/[\r\n|\n|\r]+/", "", $arraymarca[$i]);                
                $id_name = 'catjobs_'.$txt_marca;                              
                $txt_name = preg_replace("/[\r\n|\n|\r]+/", "", $id_name);               
                $name = $txt_name;                
                $title = $txt_marca;                                          
                $description = 'Selecciona los puestos disponibles para ' . $arraymarca[$i];
                $default = !empty($configs[$name]) ? $configs[$name] : "";                
                $mform->addElement('header',"header", $description);
                $select = $mform->addElement('select', $name, $title, $puestos, 'class = " multiselect-setting " size="10" ');
                $select->setMultiple(true);
                $mform->getElement($name)->setSelected(explode(',', $default));                        
                
                
            } 
        }     
        
        $mform->addElement('header', 'exceptionstxt', 'Añadir Excepciones');
        $mform->setExpanded('exceptionstxt', true);

        $name = 'exc_cont_';                
        $title = 'Número de excepciones';                                          
        $description = 'Seleccione el numero de excepciones';
        $defaultcount = !empty($configs[$name]) ? $configs[$name] : "";                
        $select = $mform->addElement('select', $name, $title, array(1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30));
        $mform->getElement($name)->setSelected(explode(',', $defaultcount));                        
        $mform->addElement('static', 'description', '', $description);

        $getuo = get_config('local_hoteles_city_dashboard', 'gestor_uo');
        $arrayuo = explode("\n", $getuo);
        $arrayunidades = array();
        foreach ($arrayuo as $single_uo) {
            $txt_uo = preg_replace("/[\r\n|\n|\r]+/", "", $single_uo);
            $arrayunidades[$txt_uo] = $txt_uo;            
        }   
        $unidadoperativa = $arrayunidades; 
        $unidadoperativa['-1'] = '---'; 
        for($i=0; $i <= $defaultcount; $i++){
            $name = 'exc_uo_'.$i;                
            $title = $name;                                          
            $description = 'Selecciona la Unidad operativa con excepción';
            $default = !empty($configs[$name]) ? $configs[$name] : "";    
            $titleheader = "Configuración de Excepción ".$i;
            $mform->addElement('header',"header", $titleheader );            
            $select = $mform->addElement('select', $name, $title, $unidadoperativa);
            $mform->getElement($name)->setSelected(explode(',', $default));                        
            $mform->addElement('static', 'description', '', $description);

            $name = 'exc_catjobs_'.$i;                
            $title = $name;                                          
            $description = 'Selecciona los puesto para la unidad operativa';
            $default = !empty($configs[$name]) ? $configs[$name] : "";                
            $select = $mform->addElement('select', $name, $title, $puestos, 'class = " multiselect-setting " size="10" ');
            $select->setMultiple(true);
            $mform->getElement($name)->setSelected(explode(',', $default));                        
            $mform->addElement('static', 'description', '', $description);
        }      
           
        


    }

    /**
     * Extend the form definition after data has been parsed.
     */
    public function definition_after_data() {

    }

    /**
     * Validate the form data.
     * @param array $usernew
     * @param array $files
     * @return array|bool
     */
    public function validation($usernew, $files) {

    }
}
