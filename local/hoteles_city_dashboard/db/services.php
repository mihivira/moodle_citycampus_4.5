<?php

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
 * Web service local plugin template external functions and service definitions.
 *
 * @package    localwstemplate
 * @copyright  2011 Jerome Mouneyrac
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// We defined the web service functions to install.
$functions = array(
        'local_hoteles_city_dashboard_reports_uo' => array(
                'classname'   => 'local_hoteles_city_dashboard_reports_uo_external',
                'methodname'  => 'get_data_graphs',
                'classpath'   => 'local/hoteles_city_dashboard/externallib.php',
                'description' => '',
                'ajax' => true,
                'type'        => 'read',
        ),
        'local_hoteles_city_dashboard_get_institutions_ggc' => array(
                'classname'   => 'local_hoteles_city_dashboard_reports_uo_external',
                'methodname'  => 'get_institutions_ggc',
                'classpath'   => 'local/hoteles_city_dashboard/externallib.php',
                'description' => '',
                'ajax' => true,
                'type'        => 'read',
        ),
        'local_hoteles_city_dashboard_generate_report' => [
                'classname'   => 'local_hoteles_city_dashboard_external',
                'methodname'  => 'generate_report',
                'classpath'   => 'local/hoteles_city_dashboard/externallib.php',
                'description' => 'Genera un reporte de forma asíncrona.',
                'type'        => 'write',
                'ajax'        => true,
        ],
        'local_hoteles_city_dashboard_delete_user' => [
                'classname'   => 'local_hoteles_city_dashboard_external',
                'methodname'  => 'delete_user',
                'classpath'   => 'local/hoteles_city_dashboard/externallib.php',
                'description' => 'Eliminar un usuario específico',
                'type'        => 'write',
                'ajax'        => true,
                'capabilities'=> 'moodle/user:delete'
        ],
);

// We define the services to install as pre-build services. A pre-build service is not editable by administrator.
$services = array(
        'Dashboard hoteles city ws' => array(
                'functions' => array (
                    'local_hoteles_city_dashboard_reports_uo',
                    'local_hoteles_city_dashboard_get_institutions_ggc',
                    'local_hoteles_city_dashboard_generate_report',
                    'local_hoteles_city_dashboard_delete_user',
                ),
                'restrictedusers' => 0,
                'enabled'=>1,
        )
);