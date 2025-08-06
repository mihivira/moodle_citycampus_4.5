<?php


require_once($CFG->libdir . "/externallib.php");

require_once(__DIR__ . '/lib.php');

class local_hoteles_city_dashboard_reports_uo_external extends external_api {

    /**
     * Returns description of method parameters
     * @return external_function_parameters
     */
    public static function get_data_graphs_parameters() {
        return new external_function_parameters([
            
            'data' => new external_single_structure([
                'regions' => new external_value(PARAM_TEXT, 'Region seleccionada o todas'),
                'type' => new external_value(PARAM_TEXT, 'Tipo de consulta'),
            ]),
        ]);
    }
    
    public static function get_data_graphs_returns() {
        return new external_value(PARAM_TEXT, 'JSON object', VALUE_OPTIONAL);
    }

        /**
     * Create groups
     * @param array $groups array of group description arrays (with keys groupname and courseid)
     * @return array of newly created groups
     */
    public static function get_data_graphs($data) {
        global $CFG, $DB;

        $response = "";


        switch($data['type']){
            case "regions-avance":
                $progress_regions = local_hoteles_city_dashboard_get_report_regions(true);
                $response = self::graphs_regions($progress_regions, 'Reporte de avance por regiones');
                break;
            case "uos-avance":
                $progress_regions = local_hoteles_city_dashboard_get_report_uo($data['regions']);
                $response = self::graphs_regions($progress_regions, 'Reporte de avance por región '.$data['regions'],true);
                break;
            case "regions-certificaciones":
                $progress_regions = local_hoteles_city_dashboard_get_report_certifications();
                $response = self::graphs_regions($progress_regions, 'Reporte de avance por certificaciones');
                break;
            case "uos-certificaciones":
                $progress_regions = local_hoteles_city_dashboard_get_report_certifications($data['regions']);
                $response = self::graphs_regions($progress_regions, 'Reporte de avance por curso '.$data['regions']);
                break;
            case "uos2-certificaciones":
                $progress_regions = local_hoteles_city_dashboard_get_report_certifications($data['regions'], true);
                $response = self::graphs_regions($progress_regions, 'Reporte de avance por región '.$data['regions'], true);
                break;
            case "usuarios-certificaciones":
                $progress_regions = local_hoteles_city_dashboard_get_data_users_cert();
                $response = self::graphs_regions($progress_regions, 'Reporte de avance por región '.$data['regions']);
                break;
        }
        
        return $response;
        
    }

    public function graphs_regions($progress_regions, $title,$is_uos = false) {
        
        
        $arr_approved = $arr_not_approved = $arr_regions = [];
        
        foreach($progress_regions->elements as $region){
            if($is_uos){
                $arr_name = explode("_",$region->name);
                array_push($arr_regions, $arr_name[1]);
                $region->name = $arr_name[1];
            } else {
                array_push($arr_regions, $region->name);
            }
            
            array_push($arr_approved, $region->percentage);
            if($region->percentage > 0){
                array_push($arr_not_approved, round(100 - $region->percentage, 2));
            } else {
                array_push($arr_not_approved, "0");
            }
            
        }

        $response = (object)[
        'type' => $progress_regions->type,
        'table' => $progress_regions,
        'graph' => (object)
        [ 
            'type' => 'bar' , 
            'series' => [
                (object)
                [
                    'label' => '% Completado' , 
                    'labels' => null, 
                    'type' => null, 
                    'values' => $arr_approved, 
                    'colors' => ['green'], 
                    'axes' => (object)
                    [
                        'x' => null, 
                        'y' => null
                    ] , 
                    'smooth' => null
                ],
                (object)
                [
                    'label' => '% No completado' , 
                    'labels' => null, 
                    'type' => null, 
                    'values' => $arr_not_approved, 
                    'colors' => ['red'], 
                    'axes' => (object)
                    [
                        'x' =>null, 
                        'y' =>null
                    ], 
                    'smooth' => null,
                ]
            ], 
            'labels' => $arr_regions,
            'title' => $title , 
            'axes' => (object)
            [
                'x' => [], 
                'y' => 
                [
                    (object)[
                        'label' => null, 
                        'labels' => null, 
                        'max' => null, 
                        'min' => 0, 
                        'position' => null, 
                        'stepSize' => null
                    ] 
                ]
            ],
            'config_colorset' => null, 
            'horizontal' => true, 
            'stacked' => true
            
        ]
        ];
         

        return json_encode($response);
    }

     /**
     * Returns description of method parameters
     * @return external_function_parameters
     */
    public static function get_institutions_ggc_parameters() {
        return new external_function_parameters([
            
            'data' => new external_single_structure([
                'institution' => new external_value(PARAM_TEXT, 'Institución a buscar'),
                
            ]),
        ]);
    }
    
    public static function get_institutions_ggc_returns() {
        return new external_value(PARAM_TEXT, 'JSON object', VALUE_OPTIONAL);
    }

        /**
     * Create groups
     * @param array $groups array of group description arrays (with keys groupname and courseid)
     * @return array of newly created groups
     */
    public static function get_institutions_ggc($data) {
        global $CFG, $DB;

        $response = "";

        $response = local_hoteles_city_dashboard_get_departments_catalogue($data['institution']);

        
        return json_encode($response);
        
    }
}

class local_hoteles_city_dashboard_external extends external_api {

    public static function generate_report_parameters() {
        return new external_function_parameters([
            'data' => new external_single_structure([
                'tipo' => new external_value(PARAM_INT, 'Tipo de reporte a generar'),
                'datos' => new external_value(PARAM_RAW, 'JSON con las instituciones')
            ])
            
            
        ]);
    }

    public static function generate_report($data) {
        global $DB, $USER;

        $report_name = "Reporte_de_cursos_";

        $currentdate = date("d-m-Y_H:i:s");
        $filename = $report_name . $currentdate;

        // Crear una entrada en la base de datos para la tarea de reporte pendiente
        $task = new stdClass();
        $task->userid = $USER->id;
        $task->reporttype = $data['tipo'];
        $task->status = 'pending';
        $task->created_at = time();
        $task->updated_at = time();
        $task->filename = $filename;
        $task->data = $data['datos']; // Almacena los datos en formato JSON
        $new_task = $DB->insert_record('local_hc_dashboard_tasks', $task);

        // Devuelve el ID de la tarea para que el usuario pueda verificar el estado más tarde
        return ['task_id' => $new_task];
    }

    public static function generate_report_returns() {
        return new external_single_structure([
            'task_id' => new external_value(PARAM_INT, 'ID de la tarea creada')
        ]);
    }

    /**
     * Define the parameters of the function.
     * @return external_function_parameters
     */
    public static function delete_user_parameters() {
        return new external_function_parameters([
            'user_id' => new external_value(PARAM_INT, 'ID del usuario a eliminar')
        ]);
    }

    /**
     * Main function to delete the user.
     * @param int $user_id
     * @return array
     */
    public static function delete_user($user_id) {
        global $DB;

        // Validar parámetros y permisos.
        $params = self::validate_parameters(self::delete_user_parameters(), ['user_id' => $user_id]);
        $context = context_system::instance();
        self::validate_context($context);
        require_capability('moodle/user:delete', $context);

        // Obtener usuario.
        $user = $DB->get_record('user', ['id' => $params['user_id']], '*', MUST_EXIST);
        if (!$user) {
            throw new invalid_parameter_exception('Usuario no encontrado');
        }

        // Intentar eliminar al usuario.
        //$success = delete_user($user);
        $success =  local_hoteles_city_dashboard_delete_user($user);
        if (!$success) {
            return ['success' => false, 'error' => 'No se pudo eliminar el usuario'];
        }

        return ['success' => true];
    }

    /**
     * Define the return values of the function.
     * @return external_single_structure
     */
    public static function delete_user_returns() {
        return new external_single_structure([
            'success' => new external_value(PARAM_BOOL, 'El usuario ha sido eliminado'),
            'error'   => new external_value(PARAM_TEXT, 'Mensaje de error en caso de fallo', VALUE_OPTIONAL)
        ]);
    }
}