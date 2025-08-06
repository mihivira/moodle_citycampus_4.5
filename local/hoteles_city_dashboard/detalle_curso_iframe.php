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
     * Listado de usuarios inscritos en un curso
     *
     * @package     local_hoteles_city_dashboard
     * @category    admin
     * @copyright   2019 Subitus <contacto@subitus.com>
     * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
     */
    require_once(__DIR__ . '/../../config.php');
    require_once(__DIR__ . '/lib.php');
    local_hoteles_city_dashboard_user_has_access(LOCAL_HOTELES_CITY_DASHBOARD_REPORTES);
    $PAGE->set_context(context_system::instance());
    $courseid = optional_param('courseid', -1, PARAM_INT);
    // $course = $DB->get_record('course', array('id' => $courseid), 'id, fullname', MUST_EXIST);
    $PAGE->set_url($CFG->wwwroot . '/local/hoteles_city_dashboard/detalle_curso_iframe.php');
    $PAGE->set_pagelayout('admin');
    $PAGE->set_title('Detalle cursos');
    // echo $OUTPUT->header();
    $report_info = local_hoteles_city_dashboard_get_report_columns(LOCAL_HOTELES_CITY_DASHBOARD_COURSE_USERS_PAGINATION);
        
    $courses = local_hoteles_city_dashboard_get_courses_setting(true);
    if ($courseid != -1) {
        $default_courses = $courseid;
    } else {
        $default_courses = "";
    }
    $description = ""; // No es usado en esta sección
?>

<!DOCTYPE html>
<html lang="en">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta http-equiv="X-UA-Compatible" content="ie=edge">
        <title>Dashboard</title>

        <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
        <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">
        <link href="css/sb-admin-2.min.css" rel="stylesheet">
        <script src="vendor/chart.js/Chart.min.js"></script>
        <link href="estilos_city.css" rel="stylesheet">

    </head>

    <body style="background-color: #ecedf1; max-width: 100%;">
        <div class="row" >

            
            <?php
                if(local_hoteles_city_dashboard_is_gerente_general() || local_hoteles_city_dashboard_is_director_regional()){
                    echo "<div class='col-sm-12' style='margin: auto;'>
                        <!-- <button id='descargar_reporte' onclick='descargar_reporte()' class='btn btn-info'>Descargar reporte</button> -->
                        <button id='todos_cursos' class='btn btn-info'>Seleccionar todos los cursos</button>
                            <button id='quitar_cursos' class='btn btn-info'>Quitar todos los cursos</button>
                            <!-- <button onclick='top.window.location.href='orden.php'' style='margin:1%' class='btn btn-info'>Reordenar campos</button> -->
                        </div>";
                }else{
                    echo "<div class='col-sm-12' style='padding:15px;'>
                    <button id='descargar_cursos' class='btn btn-info'>Seleccionar todos los cursos</button>
                    <button id='quitar_cursos' class='btn btn-info'>Quitar todos los cursos</button>
                </div>
                    ";
                }

            ?>
        </div>
        <div class='row'>
            <?php
                echo local_hoteles_city_dashboard_print_multiselect('report_courses', "Cursos", $default_courses, $courses, true, $class = 'col-sm-10 barra-cursos');
            ?>
            <div class="col-sm-2" style='margin: auto;'>
                <button id='search_btn' class='btn btn-info'>Mostrar Información</button>
            </div>

            <!-- Div para pintar la grafica del curso -->
            <div class="col-12" id="grafica_reporte" style="left: 10rem;"></div>
        </div>

        <!-- Modal -->
        <div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog" style="text-aligment: center;" role="document">
                
                <p class="txt_modal">Se está procesando la información, por favor tenga paciencia...</p>
                <div class="spinner-border text-primary text-center"></div>
            </div>
        </div>

        <table id='empTable' class='display dataTable table table-bordered'>
            <thead>
                <tr>
                    <?php echo $report_info->table_code; ?>
                </tr>
            </thead>
            <tfoot>
                <tr>
                    <?php echo $report_info->table_code; ?>
                </tr>
            </tfoot>
        </table>

        <script src="vendor/jquery/jquery.min.js"></script>
        <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

        <!-- Datatable CSS -->
        <link href='datatables/jquery.dataTables.min.css' rel='stylesheet' type='text/css'>

        <!-- <link href='//cdn.datatables.net/1.10.19/css/jquery.dataTables.min.css' rel='stylesheet' type='text/css'> -->
        <link href="datatables/buttons.dataTables.min.css" rel="stylesheet">

        <!-- Datatable JS -->
        <script src="datatables/jquery.dataTables.min.js"></script>
        <script src="datatables/dataTables.buttons.min.js"></script>
        <script src="datatables/buttons.flash.min.js"></script>
        <script src="datatables/jszip.min.js"></script>
        <script src="datatables/pdfmake.min.js"></script>
        <script src="datatables/vfs_fonts.js"></script>
        <script src="datatables/buttons.html5.min.js"></script>
        <script src="datatables/buttons.print.min.js"></script>

        <link rel="stylesheet" href="choicesjs/styles/choices.min.css" />
        <script src="choicesjs/scripts/choices.min.js"></script>



        <!-- Table -->
        <script>
            var _datatable;
            var reportCourses;
            $(document).ready(function() {
                var multipleChoiceButton = null;


                $('select[multiple]').each(function(index, element) { // Generación de filtros con herramenta choices.js
                    multipleChoiceButton = new Choices('#' + element.id, {
                        removeItemButton: true,
                        searchEnabled: true,
                        placeholder: true,
                        duplicateItemsAllowed: false,

                    });
                });

                multipleChoiceButton.removeActiveItems();
                reportCourses = <?php echo ($courseid != -1) ? $courseid : "0"; ?>;
                coursesFull = <?= json_encode($courses); ?>;
                courses = Object.keys(coursesFull).map((key) => coursesFull[key]);
                

                obj_course = [];
                i=0;
                Object.entries(coursesFull).map(([key, value]) => {
                    obj = {};
                    obj['value'] = key;
                    obj['label'] = value;
                    obj_course[i] = obj;
                    i++;
                })
                
                _datatable = $('#empTable').DataTable({
                    'processing': true,
                    'serverSide': true,
                    'serverMethod': 'post',
                    initComplete: function() {
                        $('.dataTables_filter input').unbind();
                        $('.dataTables_filter input').bind('keyup', function(e){
                            var code = e.keyCode || e.which;
                            if (code == 13) {
                                _datatable.search(this.value).draw();
                            }
                        });
                    },
                    'ajax': {
                        'url': 'services.php',
                        data: function(d) {
                            d['request_type'] = 'course_users_pagination';
                            d['reportCourses'] = reportCourses;
                            console.log(d['reportCourses']);
                            mostrarGraficaDeCursos();
                            
                            if(reportCourses > 0){
                                select_curso(reportCourses);
                            }
                            
                        }
                    },
                    lengthMenu: [
                        [ -1],
                        ["Todos los registros"]
                    ],
                    'dom': 'Bfrtip',
                    "pageLength": -1,
                    buttons: [{
                            extend: 'excel',
                            text: '<span class="fa fa-file-excel-o"></span> Exportar a excel',
                            exportOptions: {
                                modifier: {
                                    search: 'applied',
                                    order: 'applied'
                                },
                                columns: [<?php echo $report_info->ajax_printed_rows; ?>],
                            },
                        },
                        {
                            extend: 'excel',
                            text: '<span class="fa fa-file-o"></span> Exportar a CSV',
                            exportOptions: {
                                modifier: {
                                    search: 'applied',
                                    order: 'applied'
                                },
                                columns: [<?php echo $report_info->ajax_printed_rows; ?>],
                            },
                        },
                        'pageLength',
                    ],
                    'columns': [
                        <?php echo $report_info->ajax_code; ?>
                    ],

                    language: {
                        "url": "datatables/Spanish.json",
                        "emptyTable": "No se encontró información",
                        // "infoFiltered":   "(filtered from _MAX_ total entries)",
                        "loadingRecords": "Cargando...",
                        "processing": "<div class='spinner'></div>",
                        "search": "Búsqueda:",
                        // "zeroRecords":    "No matching records found",
                        "paginate": {
                            "first": "Primera",
                            "last": "Última",
                            "next": "Siguiente",
                            "previous": "Anterior"
                        },
                        buttons: {
                            pageLength: {
                                _: "Mostrando %d filas",
                                '-1': "Mostrando todas las filas"
                            }
                        }
                    },
                    "columnDefs": [{
                        "targets": [<?php echo $report_info->ajax_link_fields; ?>],
                        "orderable": false
                    }]
                    // language: {
                    // },
                    // buttons: [ { extend: 'excel', action: newExportAction } ],
                });
                $.fn.dataTable.ext.errMode = 'throw';
                //$('.dataTable').dataTable().fnFilterOnReturn();
                $('#search_btn').click(function() { // Recargar página al seleccionar curso
                    reportCourses = $('#report_courses').val();
                    // console.log('Reload');
                    _datatable.ajax.reload();
                });

                $('#quitar_cursos').click(quitar_cursos);
                $('#todos_cursos').click(todos_cursos);
                $('#descargar_cursos').click(descargar_cursos);
                /**
                 Función que se ejecuta al elegir/quitar cursos
                */
                function mostrarGraficaDeCursos() {
                    
                    peticion = Array();
                    peticion.push({
                        name: 'request_type',
                        value: 'course_completion'
                    });
                    peticion.push({
                        name: 'courseid',
                        value: reportCourses
                    });


                    $.ajax({
                            type: "POST",
                            url: "services.php",
                            data: peticion,
                            dataType: "json"
                        })
                        .done(function(data) {
                            informacion = JSON.parse(JSON.stringify(data));
                            console.log('Gráfica detalle_curso_iframe.php', data);
                            informacion = data.data;
                            console.log(informacion)
                            cleanDiv();

                            var report = new GraphicsDashboard('grafica_reporte', 'Comparativa', informacion.chart, informacion, 8, informacion.id);
                            report.printCardCourseDetail();
                            if (informacion.chart == 'pie') {
                                report.individual_graph_report();
                            }
                            showPage();
                            

                        })
                        .fail(function(error, error2) {
                            console.log('Error en printInfoCards');
                            console.log(error);
                            console.log(error2);
                            showPage();
                        });
                }

                function cleanDiv() {
                    document.getElementById('grafica_reporte').innerHTML = '';
                }
                function quitar_cursos(){
                    multipleChoiceButton.enable();
                    multipleChoiceButton.removeActiveItems();
                    reportCourses = "";
                    multipleChoiceButton.clearStore();
                    multipleChoiceButton.setChoices(obj_course);
                    _datatable.ajax.reload();
                    
                }

                function todos_cursos(){
                    reportCourses = '<?php echo $default_courses = implode(',', array_keys($courses));?>';
                    multipleChoiceButton.setValue(courses);
                    _datatable.ajax.reload();
                    multipleChoiceButton.disable();
                }

                function select_curso(courseid){
                    
                    multipleChoiceButton.setValue([ coursesFull[courseid]]);
                }

                function descargar_cursos(){
                    descargar_reporte()
                }
                

            });
            function descargar_reporte(){
                url = "descargar_reporte.php?course=" + reportCourses;
                alert("Este proceso demora varios minutos, por lo tanto no abandones la sesión, ni vuelvas a dar clic en el botón hasta que se descargue el documento.");
                window.location.href = url;
                $("#exampleModal").modal('show');
            }

            

            

            
        </script>

        <script src="classes.js"></script>

    </body>

</html>
