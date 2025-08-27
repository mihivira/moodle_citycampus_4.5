<?php
require_once('../../config.php');
require_once($CFG->dirroot . '/theme/norte19/lib.php');

require_login();

$PAGE->set_context(context_system::instance());
$PAGE->set_url('/theme/norte19/diagnostic.php');
$PAGE->set_title('Diagnóstico de tema Norte19');
$PAGE->set_heading('Diagnóstico de tema Norte19');

echo $OUTPUT->header();

echo '<h2>Información del usuario</h2>';
echo '<p>Usuario ID: ' . $USER->id . '</p>';
echo '<p>Nombre: ' . fullname($USER) . '</p>';

$brand = theme_norte19_get_user_brand($USER->id);
echo '<p>Marca detectada: <strong>' . ($brand ?: 'NINGUNA') . '</strong></p>';

if ($brand) {
    $marcas = theme_norte19_get_configured_brands();
    echo '<p>Nombre marca: ' . ($marcas[$brand] ?? 'Desconocido') . '</p>';
}

echo '<h2>Configuraciones de marca</h2>';
theme_norte19_debug_configurations();

echo '<h2>Campo personalizado de usuario</h2>';
$brandfield = get_config('theme_norte19', 'brandfield') ?: 'marca';
echo '<p>Campo configurado: ' . $brandfield . '</p>';

// Verificar campo personalizado
$sql = "SELECT uf.* 
        FROM {user_info_field} uf 
        WHERE uf.shortname = :fieldname";
$field = $DB->get_record_sql($sql, ['fieldname' => $brandfield]);

if ($field) {
    echo '<p>Campo encontrado: ' . $field->name . '</p>';
    
    // Valor del campo para este usuario
    $sql = "SELECT ud.data 
            FROM {user_info_data} ud 
            WHERE ud.userid = :userid AND ud.fieldid = :fieldid";
    $value = $DB->get_field_sql($sql, ['userid' => $USER->id, 'fieldid' => $field->id]);
    
    echo '<p>Valor del campo: <strong>' . ($value ?: 'NO TIENE VALOR') . '</strong></p>';
} else {
    echo '<p style="color: red;">Campo personalizado NO encontrado: ' . $brandfield . '</p>';
}

echo '<h2>SCSS Generado</h2>';
$theme = theme_config::load('norte19');
$scss = theme_norte19_get_pre_scss($theme);
echo '<pre style="background: #f0f0f0; padding: 10px;">' . htmlspecialchars($scss) . '</pre>';

echo $OUTPUT->footer();