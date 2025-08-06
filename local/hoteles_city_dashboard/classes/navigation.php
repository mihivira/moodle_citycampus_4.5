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
 * Navigation extension for local_hoteles_city_dashboard.
 *
 * @package    local_hoteles_city_dashboard
 * @copyright  2025
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_hoteles_city_dashboard;

use core\navigation\view\secondary\global_navigation;
use core\navigation\navigation;
use moodle_url;


require_once(__DIR__ . '/../lib.php');
/**
 * Extend the navigation.
 */
class navigation {

    /**
     * Extend the navigation.
     *
     * @param navigation $navigation
     * @param global_navigation $globalnav
     */
    public static function extend_navigation(navigation $navigation, global_navigation $globalnav) {
        global $CFG;

        // Obtener permisos del usuario.
        $permissions = self::get_user_permissions();

        foreach ($permissions as $key) {
            switch ($key) {
                case LOCAL_HOTELES_CITY_DASHBOARD_ALTA_BAJA_USUARIOS:
                    $node = $navigation->add(
                        get_string('alta_baja_usuarios', 'local_hoteles_city_dashboard'),
                        new moodle_url($CFG->wwwroot . '/local/hoteles_city_dashboard/usuarios.php', [
                            'type' => LOCAL_HOTELES_CITY_DASHBOARD_ACTIVED_USERS_PAGINATION
                        ]),
                        navigation::TYPE_CUSTOM,
                        null,
                        null,
                        new \pix_icon('i/users', '')
                    );
                    $node->showinflatnavigation = true;
                    break;

                case LOCAL_HOTELES_CITY_DASHBOARD_ALTA_BAJA_USUARIOS_OFICINA_CENTRAL:
                    $node = $navigation->add(
                        get_string('alta_baja_usuarios_oficina_central', 'local_hoteles_city_dashboard'),
                        new moodle_url($CFG->wwwroot . '/local/hoteles_city_dashboard/usuarios.php', [
                            'type' => LOCAL_HOTELES_CITY_DASHBOARD_OFICINA_CENTRAL_PAGINATION
                        ]),
                        navigation::TYPE_CUSTOM,
                        null,
                        null,
                        new \pix_icon('i/users', '')
                    );
                    $node->showinflatnavigation = true;
                    break;

                case LOCAL_HOTELES_CITY_DASHBOARD_CAMBIO_USUARIOS:
                    $node = $navigation->add(
                        get_string('cambio_usuarios', 'local_hoteles_city_dashboard'),
                        new moodle_url($CFG->wwwroot . '/local/hoteles_city_dashboard/usuarios.php', [
                            'type' => LOCAL_HOTELES_CITY_DASHBOARD_SUSPENDED_USERS_PAGINATION
                        ]),
                        navigation::TYPE_CUSTOM,
                        null,
                        null,
                        new \pix_icon('i/users', '')
                    );
                    $node->showinflatnavigation = true;
                    break;

                case LOCAL_HOTELES_CITY_DASHBOARD_LISTADO_TODOS_LOS_USUARIOS:
                    $node = $navigation->add(
                        get_string('listado_todos_los_usuarios', 'local_hoteles_city_dashboard'),
                        new moodle_url($CFG->wwwroot . '/local/hoteles_city_dashboard/usuarios.php', [
                            'type' => LOCAL_HOTELES_CITY_DASHBOARD_ALL_USERS_PAGINATION
                        ]),
                        navigation::TYPE_CUSTOM,
                        null,
                        null,
                        new \pix_icon('i/users', '')
                    );
                    $node->showinflatnavigation = true;
                    break;

                case LOCAL_HOTELES_CITY_DASHBOARD_REPORTES:
                    $navigation->add(
                        get_string('graficas_cursos', 'local_hoteles_city_dashboard'),
                        new moodle_url($CFG->wwwroot . '/local/hoteles_city_dashboard/estatus_curso.php'),
                        navigation::TYPE_CUSTOM,
                        null,
                        null,
                        new \pix_icon('i/chart', '')
                    )->showinflatnavigation = true;

                    $navigation->add(
                        get_string('reporte_cursos', 'local_hoteles_city_dashboard'),
                        new moodle_url($CFG->wwwroot . '/local/hoteles_city_dashboard/detalle_curso.php'),
                        navigation::TYPE_CUSTOM,
                        null,
                        null,
                        new \pix_icon('i/report', '')
                    )->showinflatnavigation = true;

                    $navigation->add(
                        get_string('descargar_reporte_cursos', 'local_hoteles_city_dashboard'),
                        new moodle_url($CFG->wwwroot . '/local/hoteles_city_dashboard/descargar_reporte.php'),
                        navigation::TYPE_CUSTOM,
                        null,
                        null,
                        new \pix_icon('i/download', '')
                    )->showinflatnavigation = true;
                    break;

                case LOCAL_HOTELES_CITY_DASHBOARD_REPORTES_UO:
                    $navigation->add(
                        get_string('reportes_uo', 'local_hoteles_city_dashboard'),
                        new moodle_url($CFG->wwwroot . '/local/hoteles_city_dashboard/layout/reporte_uo.php'),
                        navigation::TYPE_CUSTOM,
                        null,
                        null,
                        new \pix_icon('i/report', '')
                    )->showinflatnavigation = true;
                    break;

                case LOCAL_HOTELES_CITY_DASHBOARD_GRAFICAS_COMPARATIVAS:
                    $navigation->add(
                        get_string('graficas_comparativas', 'local_hoteles_city_dashboard'),
                        new moodle_url($CFG->wwwroot . '/local/hoteles_city_dashboard/dashboard.php'),
                        navigation::TYPE_CUSTOM,
                        null,
                        null,
                        new \pix_icon('i/chart', '')
                    )->showinflatnavigation = true;
                    break;

                case LOCAL_HOTELES_CITY_DASHBOARD_AJUSTES:
                    $navigation->add(
                        get_string('ajustes', 'local_hoteles_city_dashboard'),
                        new moodle_url($CFG->wwwroot . '/local/hoteles_city_dashboard/ajustes.php'),
                        navigation::TYPE_CUSTOM,
                        null,
                        null,
                        new \pix_icon('i/settings', '')
                    )->showinflatnavigation = true;
                    break;

                case LOCAL_HOTELES_CITY_FORCE_DELETE_USERS:
                    $navigation->add(
                        get_string('force_delete_users', 'local_hoteles_city_dashboard'),
                        new moodle_url($CFG->wwwroot . '/local/hoteles_city_dashboard/force_delete_users_masive.php'),
                        navigation::TYPE_CUSTOM,
                        null,
                        null,
                        new \pix_icon('i/trash', '')
                    )->showinflatnavigation = true;
                    break;
            }
        }
    }

    /**
     * Obtener permisos del usuario (debes implementar esta lógica).
     *
     * @return array
     */
    private static function get_user_permissions(): array {
        // Aquí debes implementar tu lógica de permisos.
        // Por ejemplo:
        // return \local_hoteles_city_dashboard\permission::get_user_permissions();
        // O simplemente llamar a una función global si aún la usas.
        return function_exists('local_hoteles_city_dashboard_get_user_permissions') ?
            local_hoteles_city_dashboard_get_user_permissions() : [];
    }
}