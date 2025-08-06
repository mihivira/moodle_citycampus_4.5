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

local_hoteles_city_dashboard_user_has_access(LOCAL_HOTELES_CITY_DASHBOARD_REPORTES);
$url = 'graficas.php';

require_login();

$PAGE->set_url($CFG->wwwroot . "/local/hoteles_city_dashboard/estatus_curso.php");
$PAGE->set_context($context_system);
$PAGE->set_pagelayout('admin');
$PAGE->set_title(get_string('pluginname', 'local_hoteles_city_dashboard'));

echo $OUTPUT->header();


?>
<div class="modal d-block d-sm-none" tabindex="-1" role="dialog" style="background: rgba(0,0,0,0.6);">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                <div>
                    <h5 class="modal-title"></h5>

                </div>
                <div class="modal-body">
                    <p>Esta sección se visualiza de mejor forma desde una computadora.</p>
                </div>
                <div class="modal-footer">
                <?php echo '<a href="'.$CFG->wwwroot.'"><button type="button" class="btn btn-primary">Regresar</button></a>'; ?>

                </div>
                </div>
            </div>
</div>

<iframe src="<?php echo $url; ?>" id="page_iframe" frameborder="0" style="width: 100%; overflow: hidden;"></iframe>
<!-- <div class="btnimprimir">
    <button class="btn btn-primary" onclick="imprimir();">Imprimir</button>       
</div> -->
<script>
    document.addEventListener("DOMContentLoaded", function() {
        document.getElementById('region-main').style.width = "100%";
        require(['jquery'], function ($) {
            setInterval(function() { iResize('page_iframe'); }, 1000);
        });
    });
    var intervals = 0;
    function iResize(frame_id) {
        // if(intervals > 60){
        //     return;
        // }
        // intervals++;
        element = document.getElementById(frame_id);
        if(element != null){
            if(element.contentWindow != null){
                if(element.contentWindow.document != null){
                    if(element.contentWindow.document.body != null){
                        if(element.contentWindow.document.body.offsetHeight != null){
                            size = (element.contentWindow.document.body.offsetHeight ) + 'px';
                            document.getElementById(frame_id).style.height = size;
                        }
                    }
                }
            }
        }
    }

    // function imprimir() {
    //     window.print();
    // }

</script>
<?php

echo $OUTPUT->footer();