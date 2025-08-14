<?php

namespace local_hoteles_city_dashboard\external;

use external_api;
use external_function_parameters;
use external_single_structure;
use external_multiple_structure;
use external_value;


defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . "/externallib.php");
require_once($CFG->dirroot . '/local/hoteles_city_dashboard/lib.php');

class get_users extends external_api {
    
    public static function execute_parameters() {
        return new external_function_parameters([
            'type' => new external_value(PARAM_INT, 'Type of user pagination', VALUE_DEFAULT, LOCAL_HOTELES_CITY_DASHBOARD_ALL_USERS_PAGINATION)
        ]);
    }

    public static function execute($type) {
        global $USER;

        // Check if the user has access to the report.
        local_hoteles_city_dashboard_user_has_access(LOCAL_HOTELES_CITY_DASHBOARD_REPORTES);
        // Obtener información del reporte.
        $report_info = local_hoteles_city_dashboard_get_report_columns($type);

        $post = $report_info->ajax_code;
        print_r($post);
        $response = local_hoteles_city_dashboard_get_paginated_users($post, $type);
        
        // If the response is empty, return an empty JSON object.
        if (empty($response)) {
            return json_encode([]);
        }

        // Get users based on the type.
        return $response;
    }

    public static function execute_returns() {
        return new external_value(PARAM_TEXT, 'JSON object', VALUE_OPTIONAL);
    }
}