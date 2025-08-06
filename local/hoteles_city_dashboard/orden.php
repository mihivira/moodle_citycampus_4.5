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
require_login();
require_once(__DIR__ . '/lib.php');
local_hoteles_city_dashboard_user_has_access(LOCAL_HOTELES_CITY_DASHBOARD_REPORTES);

global $DB, $CFG;
$PAGE->set_url($CFG->wwwroot . "/local/hoteles_city_dashboard/orden.php");
$PAGE->set_context($context_system);
$PAGE->set_pagelayout('admin');
$PAGE->set_title(get_string('pluginname', 'local_hoteles_city_dashboard'));
echo $OUTPUT->header();
$update_action = $update_key = '';
if(isset($_POST['key']) && isset($_POST['action'])){
    $update_key = $_POST['key'];
    $update_action = $_POST['action'];
}

$updated = false;
if($update_key != '' && $update_action != ''){
    $updated = local_hoteles_city_dashboard_set_new_order($update_key, $update_action);
}

$allfields = local_hoteles_city_dashboard_get_report_fields_in_order();
$num_fields = count($allfields);
if($num_fields == 0){
    print_error('No se ha configurado ningún campo para el reporte', 'error', $CFG->wwwroot . '/local/hoteles_city_dashboard/ajustes.php');
}
$keys = array_keys($allfields);
$firstkey = $keys[0];
if($num_fields > 1){
    $lastkey  = $keys[$num_fields - 1];
}else{
    $lastkey = '';
}
$reporte = $CFG->wwwroot . '/local/hoteles_city_dashboard/detalle_curso.php';
$settingsurl = $CFG->wwwroot . '/local/hoteles_city_dashboard/ajustes.php';
?>
<div id="update_message"></div>
<div class="row" style="padding-bottom: 2%;">
    <div class="col-sm-6" style="text-align: center;">
        <!-- <h4>Si el reporte no tiene la estructura necesaria, por favor ordene los campos del reporte en el siguiente enlace: </h4> -->
        <a class="btn btn-primary btn-lg" href="<?php echo $reporte; ?>">Ver reporte personalizado</a>
    </div>
    <div class="col-sm-6" style="text-align: center;">
        <h4>Si desea modificar los campos que aparecerán, o los cursos incluídos por favor edítelo en Configuraciones del plugin->reportes personalizados </h4>
        <a class="btn btn-primary btn-lg" href="<?php echo $settingsurl; ?>">Configuraciones del plugin</a>
    </div>
</div>
<?php
echo "<table class='table table-hover text-center'  rules='rows' style='width: 80%; text-align: center; margin: auto;'>";
echo "
    <thead class='thead-dark' style='background: black; color: white; font-weight: bold;'>
    <tr>
        <td>Nombre del filtro</td>
        <td>Subir posición</td>
        <td>Bajar posición</td>
    </tr>
    </thead><tbody>
    ";
foreach($allfields as $key => $name){
    echo "<tr>";
    echo "<td>{$name} </td>";
    if($firstkey !== $key){
        echo "<td><button onclick='moverElemento(\"{$key}\", \"up\")' class='btn btn-info'>Subir</button></td>";
    }else{
        echo "<td></td>";
    }
    if($lastkey !== $key){
        echo "<td><button onclick='moverElemento(\"{$key}\", \"down\")' class='btn btn-info'>Bajar</button></td>";
    }else{
        echo "<td></td>";
    }
    echo "</tr>";
}
echo "</tbody></table>";
if($updated){
    echo "<script>document.getElementById('update_message').innerHTML = 
        '<div class=\"alert alert-success alert-block fade in \" role=\"alert\"> <button type=\"button\" class=\"close\" data-dismiss=\"alert\">×</button>Cambios actualizados</div>';
    </script>";
}
?>
<form action="" method="post" id="order_form" id="order_form">
    <input type="hidden" name="key" id="item_key">
    <input type="hidden" name="action" id="item_action">
</form>
<script src="js/jquery.min.js"></script>
<script>
    function moverElemento(clave, movimiento){
        $('#item_key').val(clave);
        $('#item_action').val(movimiento);
        document.forms.order_form.submit();
        // urlActual = location.protocol + '//' + location.host + location.pathname;
        // nueva_url = urlActual = `?update_key=${clave}&update_action=${movimiento}`;
        // window.location.href = nueva_url;
    }
</script>
<?php
echo $OUTPUT->footer();
