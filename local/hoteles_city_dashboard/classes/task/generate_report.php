<?php

namespace local_hoteles_city_dashboard\task;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/local/hoteles_city_dashboard/classes/MoodleExcelWorkbookExtended.php');
require_once($CFG->dirroot . '/local/hoteles_city_dashboard/lib.php');

class generate_report extends \core\task\scheduled_task {
    public function get_name() {
        return get_string('taskname', 'local_hoteles_city_dashboard');
    }

    public function execute() {
        global $DB;

       
        // Obtener tareas pendientes
        $likestatus = $DB->sql_like('status', ':stat');
        $tasks = $DB->get_records_sql(
            "SELECT * FROM {local_hc_dashboard_tasks} WHERE {$likestatus} limit 1",
            [
                'stat' => 'pending',
            ]
        );
         //$DB->get_records('local_hc_dashboard_tasks', ['status' => 'pending'], '', '*', 0, 1);

        if (empty($tasks)) {
            
            return;
        }

        foreach ($tasks as $task) {
            
            // Marcar la tarea como "in_progress"
            $task->status = 'in_progress';
            $task->updated_at = time();
            $DB->update_record('local_hc_dashboard_tasks', $task);
    
            // Procesar el tipo de reporte y los datos
            $data = json_decode($task->data, true); // Decodifica los datos JSON
            $report_type = $task->reporttype;

            $response = local_hoteles_city_dashboard_export_configurable_report($report_type, $data, $task->filename);

            if($response['status'] == 'success'){

                $task->fileid = $response['id'];
                    // Actualizar la tarea como completada
                $task->status = 'completed';
                $task->updated_at = time();
                $DB->update_record('local_hc_dashboard_tasks', $task);
            } else {
                    // Si hay un error, marca la tarea como fallida
                $task->status = 'failed';
                $task->updated_at = time();
                $DB->update_record('local_hc_dashboard_tasks', $task);
            }
            
        }
        
    }
}
