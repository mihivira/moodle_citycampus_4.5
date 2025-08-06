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
 * Dominos Dashboard
 * @package local
 * @author: Subitus <contacto@subitus.com>
 * @date: 2019
 */

use core\plugininfo\local;

require_once(__DIR__ . "/../../config.php");
require_once(__DIR__ . '/lib.php');
//header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
require_login();

$PAGE->set_url($CFG->wwwroot . "/local/hoteles_city_dashboard/descargar_reporte.php");
$context_system = context_system::instance();
$PAGE->set_context($context_system);
$PAGE->set_pagelayout('admin');
$PAGE->set_title("Exportar Reporte");
$PAGE->set_heading("Exportar Reporte");
$PAGE->set_cacheable(false);
echo $OUTPUT->header();
$course = optional_param('course', '', PARAM_RAW);
$params = array();
if($course != ''){ // Vacío
    $params['reportCourses'] = $course;
}

if(local_hoteles_city_dashboard_is_director_regional() || local_hoteles_city_dashboard_is_gerente_general()){
   
    $params['institution'] = local_hoteles_city_dashboard_get_institutions(); 
    
}

echo $OUTPUT->render_from_template('local_hoteles_city_dashboard/generate_report', [
    'type' => LOCAL_HOTELES_CITY_DASHBOARD_COURSE_USERS_PAGINATION,
    'params' => json_encode($params),
    'wroot' => $CFG->wwwroot
    ]
    );
    

// Large exports are likely to take their time and memory.
//core_php_time_limit::raise();
//raise_memory_limit('1024M');
//local_hoteles_city_dashboard_export_configurable_report(LOCAL_HOTELES_CITY_DASHBOARD_COURSE_USERS_PAGINATION, $params);

//die;

// Never reached if download = true.
echo $OUTPUT->footer();
