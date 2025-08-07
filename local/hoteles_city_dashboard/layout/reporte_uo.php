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
 * Plugin strings are defined here.
 *
 * @package     local_hoteles_city_dashboard
 * @category    dashboard
 * @copyright   2023 Miguel Villegas <contacto@sdvir.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


 global $DB, $CFG;
 require_once(__DIR__ . '/../../../config.php');
 require_once(__DIR__ . '/../lib.php');
 
 //metadatos de la página
 $context_system = context_system::instance();
 $PAGE->set_context($context_system);
 $PAGE->set_url($CFG->wwwroot . "/local/hoteles_city_dashboard/layout/reporte_uo.php");
 $PAGE->set_title('Reportes por unidad operativa');
 $PAGE->set_heading('Reportes por unidad operativa');
 $PAGE->set_pagelayout('admin');

 $PAGE->requires->js(new moodle_url('/local/hoteles_city_dashboard/js/jspdf/jspdf.umd.min.js'), true);
 $PAGE->requires->js(new moodle_url('/local/hoteles_city_dashboard/js/jspdf/autotable.js'), true);
 $PAGE->requires->js(new moodle_url('/local/hoteles_city_dashboard/js/html2canvas/html2canvas.min.js'), true);
 $PAGE->requires->js(new moodle_url('/local/hoteles_city_dashboard/js/sheetsjs/xlsx.full.min.js'), true);

 echo $OUTPUT->header();

 $data = (object)[ ];

 $templatecontext = [
    
        "chartdata" => json_encode($data),
    
 ];
 
 //renderear el mustache
 //$PAGE->requires->js_call_amd('local_hoteles_city_dashboard/reportes_uo', 'init');

 echo $OUTPUT->render_from_template('local_hoteles_city_dashboard/reporte_uo', $templatecontext);
 echo $OUTPUT->footer();
 