<?php
require_once('../../config.php');
require_once('lib.php');
require_login();
global $DB, $USER, $PAGE, $OUTPUT;

$context = context_system::instance();
$PAGE->set_context($context);
$PAGE->set_url(new moodle_url('/local/hoteles_city_dashboard/reportes.php'));
$PAGE->set_title('Mis Reportes');
$PAGE->set_heading('Mis Reportes');
$PAGE->set_pagelayout('admin');

// Obtener los reportes del usuario actual
$reports_records = $DB->get_records('local_hc_dashboard_tasks', ['userid' => $USER->id], 'created_at DESC');

// Preparar los datos para la plantilla
$reports = [];
foreach ($reports_records as $report) {
    $downloadurl = null;
    if ($report->status === 'completed') {
        $downloadurl = local_hoteles_city_dashboard_get_download_link(
            $context->id,
            $report->id,
            $report->filename
        );
    }

    $reports[] = [
        'id' => $report->id,
        'filename' => $report->filename,
        'status' => $report->status,
        'createdat' => userdate($report->created_at),
        'downloadurl' => $downloadurl ? $downloadurl->out() : null
    ];
}

$templatecontext = [
    'reports' => $reports
];

// Renderizar la página usando la plantilla Mustache
echo $OUTPUT->header();
echo $OUTPUT->render_from_template('local_hoteles_city_dashboard/reportes', $templatecontext);
echo $OUTPUT->footer();
