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
 * Listado de usuarios seccionados a los que el usuario tenga acceso
 *
 * @package     local_hoteles_city_dashboard
 * @category    admin
 * @copyright   2019 Subitus <contacto@subitus.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../../config.php');
require_once(__DIR__ . '/lib.php');

// Contexto del sistema.
$context = context_system::instance();
$PAGE->set_context($context);

// Parámetros.
$type = optional_param('type', LOCAL_HOTELES_CITY_DASHBOARD_ALL_USERS_PAGINATION, PARAM_INT);
$PAGE->set_url(new moodle_url('/local/hoteles_city_dashboard/usuarios.php', ['type' => $type]));
$PAGE->requires->css('/local/hoteles_city_dashboard/styles/datatables.css');
$PAGE->requires->css('/local/hoteles_city_dashboard/styles/buttons.datatables.css');
$PAGE->requires->js(new moodle_url('/local/hoteles_city_dashboard/datatables/v2.3.2/datatables.js'), true);
$PAGE->requires->js(new moodle_url('/local/hoteles_city_dashboard/datatables/v2.3.2/buttons.datatables.js'), true);
$PAGE->requires->js(new moodle_url('/local/hoteles_city_dashboard/datatables/v2.3.2/buttons.html5.datatables.js'), true);
$PAGE->requires->js(new moodle_url('/local/hoteles_city_dashboard/datatables/jszip.min.js'), true);
//$PAGE->requires->js(new moodle_url('/local/hoteles_city_dashboard/datatables/dataTables.buttons.min.js'), true);
//$PAGE->requires->js_call_amd('local_hoteles_city_dashboard/datatables', 'init');
// Verificar acceso.
local_hoteles_city_dashboard_user_has_access(LOCAL_HOTELES_CITY_DASHBOARD_REPORTES);

// Título.
$title = local_hoteles_city_dashboard_get_pagination_name($type);
$PAGE->set_title($title);
$PAGE->set_heading($title);
$PAGE->set_pagelayout('admin');

// Obtener información del reporte.
$report_info = local_hoteles_city_dashboard_get_report_columns($type);

$request_type = "users_" . $type;


// Comenzar salida.
echo $OUTPUT->header();

// Mostrar botón de crear usuario si tiene permiso.
$show_create_user_button = local_hoteles_city_dashboard_user_has_access(LOCAL_HOTELES_CITY_DASHBOARD_CREATE_USER, '', false); 


$data = [
    'cancreateuser' => $show_create_user_button,
    'createuserurl' => new moodle_url('/local/hoteles_city_dashboard/administrar_usuarios.php'),
    'requesttype' => $request_type,
    'columns' => $report_info->table_code,
    'wwwroot' => $CFG->wwwroot

];

// Call AMD with only a selector or ID
$PAGE->requires->js_call_amd('local_hoteles_city_dashboard/usuarios_table', 'init', [
    '#empTable',
    $request_type,
    $report_info->ajax_printed_rows,
    $report_info->ajax_link_fields,
    'usermanagement-config'
    
]);
echo $OUTPUT->render_from_template('local_hoteles_city_dashboard/user_management/index', $data);

echo html_writer::tag('div', '', [
    'id' => 'usermanagement-config',
    'data-request-type' => $request_type,
    'data-columns' => json_encode($report_info->ajax_code, JSON_UNESCAPED_UNICODE), // Ajusta según formato
    'style' => 'display: none;'
]);

echo $OUTPUT->footer();