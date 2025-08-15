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

namespace local_hoteles_city_dashboard\output;

use renderable;
use renderer_base;
use templatable;
use stdClass;


require_once($CFG->dirroot . '/local/hoteles_city_dashboard/lib.php');

/**
 * Main page renderer.
 *
 * @package     local_hoteles_city_dashboard
 * @copyright   2025 Miguel Villegas <mihivira@gmail.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class renderer extends \plugin_renderer_base {

    /**
     * Render the main page
     *
     * @return string HTML to display
     */
    public function render_graficas_cursos() {
        $data = new stdClass();
        $data->pluginname = get_string('pluginname', 'local_hoteles_city_dashboard');
        $data->apply_filters = get_string('apply_filters', 'local_hoteles_city_dashboard');
        $data->filters = local_hoteles_city_dashboard_print_filters();
        $data->comparative = get_string('comparative', 'local_hoteles_city_dashboard');
        $data->selected_filters = get_string('selected_filters', 'local_hoteles_city_dashboard');
        $data->sesskey = sesskey();
        
        return $this->render_from_template('local_hoteles_city_dashboard/graficas_cursos', $data);
    }
}