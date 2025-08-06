<?php
// This file is part of Moodle - https://moodle.org/
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
// along with Moodle.  If not, see <https://www.gnu.org/licenses/>.


/**
 * Definición de trabajos agendados
 *
 * @package     local_hoteles_city_dashboard
 * @category    dashboard
 * @copyright   2024 Miguel Villegas <contacto@sdvir.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Custom code to be run on installing the plugin.
 */
function xmldb_local_hoteles_city_dashboard_install() {

    global $DB;
    $dbman = $DB->get_manager();
    
    // Instalar las tablas desde install.xml
    $xmldb_file = __DIR__ . '/install.xml';
    upgrade_main_savepoint(true, 2024103004); // Marca la instalación como exitosa
}
