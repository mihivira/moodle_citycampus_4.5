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
 * @copyright   2019 Subitus <contacto@subitus.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
require_once(__DIR__ . '/../../config.php');
$context_system = context_system::instance();
require_once(__DIR__ . '/lib.php');
require_login();

$PAGE->set_url($CFG->wwwroot . "/local/hoteles_city_dashboard/descarga_r.php");
$PAGE->set_context($context_system);
$PAGE->set_pagelayout('admin');
$PAGE->set_title(get_string('pluginname', 'local_hoteles_city_dashboard'));


echo $OUTPUT->header();
echo "<div class='alert alert-info alert-block fade in'>A continuacion se descargará el reporte, por favor no cierre la ventana ni de clic en otro botón ni de clic en continuar (si aparece en la parte de abajo de este mensaje), el proceso tardara algunos minutos</div>";
redirect($CFG->wwwroot . '/local/hoteles_city_dashboard/descargar_reporte.php');
echo $OUTPUT->footer();
