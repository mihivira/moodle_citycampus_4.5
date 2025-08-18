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
 * Listado de usuarios inscritos en un curso
 *
 * @package     local_hoteles_city_dashboard
 * @category    admin
 * @copyright   2025 Miguel villegas <mihivira@gmail.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../../config.php');
require_once(__DIR__ . '/lib.php');

local_hoteles_city_dashboard_user_has_access(LOCAL_HOTELES_CITY_DASHBOARD_REPORTES);

$context = context_system::instance();
$PAGE->set_context($context);
$courseid = optional_param('courseid', -1, PARAM_INT);
$PAGE->set_url($CFG->wwwroot . '/local/hoteles_city_dashboard/detalle_curso.php');


// Mantener tu configuración manual
$PAGE->requires->css('/local/hoteles_city_dashboard/styles/datatables.css');
$PAGE->requires->css('/local/hoteles_city_dashboard/styles/buttons.datatables.css');
$PAGE->requires->js(new moodle_url('/local/hoteles_city_dashboard/datatables/v2.3.2/datatables.js'), true);
$PAGE->requires->js(new moodle_url('/local/hoteles_city_dashboard/datatables/v2.3.2/buttons.datatables.js'), true);
$PAGE->requires->js(new moodle_url('/local/hoteles_city_dashboard/datatables/v2.3.2/buttons.html5.datatables.js'), true);
$PAGE->requires->js(new moodle_url('/local/hoteles_city_dashboard/datatables/jszip.min.js'), true);

$PAGE->requires->css(new moodle_url('/local/hoteles_city_dashboard/choicesjs/styles/choices.min.css'));
$PAGE->requires->js(new moodle_url('/local/hoteles_city_dashboard/choicesjs/scripts/choices.min.js'));

$PAGE->set_pagelayout('admin');
$PAGE->set_title('Detalle cursos');

$PAGE->set_heading('Detalle cursos');


echo $OUTPUT->header();

$report_info = local_hoteles_city_dashboard_get_report_columns(LOCAL_HOTELES_CITY_DASHBOARD_COURSE_USERS_PAGINATION);
$courses = local_hoteles_city_dashboard_get_courses_setting(true);

if($courseid != -1){
    $default_courses = $courseid;
}else{
    $default_courses = "";
}

$multiselect = local_hoteles_city_dashboard_print_multiselect('report_courses', "Cursos", $default_courses, $courses, true, $class = 'col-sm-10 barra-cursos');

// Llamar al AMD con los parámetros correctos
$config = [
    'courseid' => $courseid,
    'default_courses' => $default_courses,
    'ajax_code' => isset($report_info->ajax_code) ? $report_info->ajax_code : '',
    'ajax_printed_rows' => isset($report_info->ajax_printed_rows) ? $report_info->ajax_printed_rows : '0,1,2,3',
    'ajax_link_fields' => isset($report_info->ajax_link_fields) ? $report_info->ajax_link_fields : '3',
    'wwwroot' => $CFG->wwwroot,
];

$PAGE->requires->js_call_amd('local_hoteles_city_dashboard/course_detail', 'init', [$config]);


// Crear la instancia de la página correctamente
$course_detail_page = new \local_hoteles_city_dashboard\output\course_detail_page($courseid, $courses, $report_info, $default_courses, $multiselect);
$renderer = $PAGE->get_renderer('local_hoteles_city_dashboard');
$output = $renderer->render_course_detail_page($course_detail_page);



echo $output;
echo $OUTPUT->footer();