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
require_once($CFG->dirroot . '/local/hoteles_city_dashboard/lib.php');

$context = context_system::instance();
require_login();


$PAGE->set_url(new moodle_url('/local/hoteles_city_dashboard/estatus_curso.php'));
$PAGE->set_context($context);
$PAGE->set_pagelayout('admin');
$PAGE->set_title(get_string('pluginname', 'local_hoteles_city_dashboard'));
$PAGE->set_heading(get_string('pluginname', 'local_hoteles_city_dashboard'));
$PAGE->requires->css(new moodle_url('/local/hoteles_city_dashboard/estilos_city.css'));
$PAGE->requires->css(new moodle_url('/local/hoteles_city_dashboard/choicesjs/styles/choices.min.css'));

// Load JavaScript for the page
$PAGE->requires->js(new moodle_url('/local/hoteles_city_dashboard/choicesjs/scripts/choices.min.js'));


// Add AMD module
$PAGE->requires->js_call_amd('local_hoteles_city_dashboard/status_cursos', 'init');

echo $OUTPUT->header();

// Render the template
$renderer = $PAGE->get_renderer('local_hoteles_city_dashboard');
echo $renderer->render_graficas_cursos();

echo $OUTPUT->footer();