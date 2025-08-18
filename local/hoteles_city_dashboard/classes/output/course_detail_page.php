<?php
namespace local_hoteles_city_dashboard\output;

use renderable;
use templatable;
use renderer_base;
use stdClass;

class course_detail_page implements renderable, templatable {
    private $courseid;
    private $courses;
    private $reportinfo;
    private $defaultcourses;
    private $multiselect;

    public function __construct($courseid, $courses, $reportinfo, $defaultcourses, $multiselect) {
        $this->courseid = $courseid;
        $this->courses = $courses;
        $this->reportinfo = $reportinfo;
        $this->defaultcourses = $defaultcourses;
        $this->multiselect = $multiselect;
    }

    public function export_for_template(renderer_base $output) {
        $data = new stdClass();
        $data->courseid = $this->courseid;
        $data->courses = array();
        
        foreach ($this->courses as $id => $name) {
            $data->courses[] = [
                'id' => $id,
                'name' => $name,
                'selected' => ($this->courseid != -1 && $this->courseid == $id)
            ];
        }
        
        $data->report_info = [
            'table_code' => $this->reportinfo->table_code,
            'ajax_code' => $this->reportinfo->ajax_code,
            'ajax_printed_rows' => $this->reportinfo->ajax_printed_rows,
            'ajax_link_fields' => $this->reportinfo->ajax_link_fields
        ];
        
        $data->default_courses = $this->defaultcourses;
        $data->is_gerente_general = local_hoteles_city_dashboard_is_gerente_general();
        $data->is_director_regional = local_hoteles_city_dashboard_is_director_regional();
        $data->wwwroot = $GLOBALS['CFG']->wwwroot;
        $data->multiselect = $this->multiselect;
        
        return $data;
    }
}