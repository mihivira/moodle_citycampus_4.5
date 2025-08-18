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
 * @package     local_hoteles_city_dashboard
 * @category    admin
 * @copyright   2019 Subitus <contacto@subitus.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

header("Content-Type: application/json");
require_once(__DIR__ . '/../../config.php');
require_once(__DIR__ . '/lib.php');
$context_system = context_system::instance();
local_hoteles_city_dashboard_user_has_access(LOCAL_HOTELES_CITY_DASHBOARD_SERVICES);
if($_SERVER['REQUEST_METHOD'] == 'POST' && !empty($_POST['request_type'])){
    $request_type = $_POST['request_type'];
     error_log("Recibiendo petición: " . print_r($_POST, true));
    switch($request_type){
        case 'dashboard':
            die(local_hoteles_city_dashboard_format_response(local_hoteles_city_dashboard_get_dashboard_windows($params = $_POST)));
            break;
        case 'dashboard_cards':
            die(local_hoteles_city_dashboard_format_response(local_hoteles_city_dashboard_get_dashboard_cards_info($params = $_POST)));
            break;
        case 'course_completion':
            if(!empty($_POST['courseid'])){
                $courseid = $_POST['courseid'];
                die(local_hoteles_city_dashboard_format_response(local_hoteles_city_dashboard_get_course_information($courseid, $params = $_POST)));
            }else{
                die(local_hoteles_city_dashboard_error_response("courseid (int) not found"));
            }
            break;
        case 'course_list':
            die(local_hoteles_city_dashboard_format_response(local_hoteles_city_dashboard_get_courses_overview($_POST)));
            break;
        case 'competencies': 
            die(local_hoteles_city_dashboard_format_response(local_hoteles_city_dashboard_get_all_user_competencies($_POST)));
            break;
        case 'catalogues':
            die(local_hoteles_city_dashboard_format_response(local_hoteles_city_dashboard_get_user_catalogues($_POST)));
            break;
        case 'course_historics':
            if(empty($_POST['courseid'])){
                die(local_hoteles_city_dashboard_error_response("courseid (int) not found"));
            }else{
                die(local_hoteles_city_dashboard_format_response(local_hoteles_city_dashboard_get_historic_reports(intval($_POST['courseid']))));
            }
            break;
        case 'course_comparative':
            if(!empty($_POST['courseid'])){
                if(isset($_POST['selected_filter'])){
                    $courseid = $_POST['courseid'];
                    die(local_hoteles_city_dashboard_format_response(local_hoteles_city_dashboard_get_course_comparative($courseid, $params = $_POST)));
                }else{
                    die(local_hoteles_city_dashboard_error_response("selected_filter (string) not found"));                
                }
            }else{
                die(local_hoteles_city_dashboard_error_response("courseid (int) not found"));
            }
            break;
        case 'create_region':
            die(local_hoteles_city_dashboard_create_region($params = $_POST));
        break;
        case 'relate_region_institution':
            die(local_hoteles_city_dashboard_relate_region_institution($params = $_POST));
        break;
        case 'update_region':
            die(local_hoteles_city_dashboard_update_region($params = $_POST));
        break;
        case 'get_region_institutions':
            $regionid = isset($_POST['region']) ? $_POST['region'] : '';
            die(local_hoteles_city_dashboard_get_region_insitutions($regionid, true));
        break;
        case 'test':
            global $DB;
            $query = local_hoteles_city_dashboard_create_cache_query_from_params($_POST);
            $record = new stdClass();
            $record->query = $query;
            $record->enrolled_users = 100;
            $record->approved_users = 66;
            $record->percentage = 66;
            $record->timecreated = time();
            $DB->insert_record('dashboard_cache', $record);

            dd(json_encode($request));
            dd($request);
        break;
        case 'save_settings':
            die(local_hoteles_city_dashboard_save_custom_settings($_POST));
        break;
        case 'course_users_pagination':
            die(local_hoteles_city_dashboard_get_paginated_users($_POST, LOCAL_HOTELES_CITY_DASHBOARD_COURSE_USERS_PAGINATION));
        break;
        case 'users_2':
            die(local_hoteles_city_dashboard_get_paginated_users($_POST, LOCAL_HOTELES_CITY_DASHBOARD_ALL_USERS_PAGINATION));
        break;
        case 'users_3':
            die(local_hoteles_city_dashboard_get_paginated_users($_POST, LOCAL_HOTELES_CITY_DASHBOARD_SUSPENDED_USERS_PAGINATION));
        break;
        case 'users_4':
            die(local_hoteles_city_dashboard_get_paginated_users($_POST, LOCAL_HOTELES_CITY_DASHBOARD_ACTIVED_USERS_PAGINATION));
        break;
        case 'users_5':
            die(local_hoteles_city_dashboard_get_paginated_users($_POST, LOCAL_HOTELES_CITY_DASHBOARD_OFICINA_CENTRAL_PAGINATION));
        break;
        case 'establecer_gerente_hotel':	
            die(local_hoteles_city_dashboard_update_gerente_general($params = $_POST));	
        break;
        case 'obtener_gerentes_temporales':
            if(empty($_POST['institution'])){
                print_error('institution not found');
            }else{
                die(local_hoteles_city_dashboard_get_gerentes_temporales_institution($_POST['institution'], false));
            }
        break;
        case 'editar_gerentes_temporales':
            die(local_hoteles_city_dashboard_update_gerentes_temporales($_POST));	
        break;
        case 'get_all_marcas_uo':
            die(local_hoteles_city_dashboard_format_response(local_hoteles_city_dashboard_get_all_marcas_uo()));
            break;        
        default:
            die(local_hoteles_city_dashboard_error_response("request_type not allowed"));
            break;
    }
}
die(local_hoteles_city_dashboard_error_response($_SERVER['REQUEST_METHOD'] . " method not allowed"));