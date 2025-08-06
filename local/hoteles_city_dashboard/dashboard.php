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

local_hoteles_city_dashboard_user_has_access(LOCAL_HOTELES_CITY_DASHBOARD_GRAFICAS_COMPARATIVAS);
$url = 'dashboard_iframe.php';
require_login();

$PAGE->set_url($CFG->wwwroot . "/local/hoteles_city_dashboard/dashboard.php");
$PAGE->set_context($context_system);
$PAGE->set_pagelayout('admin');
$PAGE->set_title(get_string('pluginname', 'local_hoteles_city_dashboard'));
$PAGE->requires->css('/local/hoteles_city_dashboard/vendor/fontawesome-free/css/all.min.css'); 
//$PAGE->requires->css('https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i');
$PAGE->requires->css('/local/hoteles_city_dashboard/css/sb-admin-2.min.css'); 
$PAGE->requires->css('/local/hoteles_city_dashboard/estilos_city.css'); 
echo $OUTPUT->header();

echo $OUTPUT->render_from_template('local_hoteles_city_dashboard/dashboard', []);

echo $OUTPUT->footer();