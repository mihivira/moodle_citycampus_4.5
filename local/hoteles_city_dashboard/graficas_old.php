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
global $DB;
$PAGE->set_url($CFG->wwwroot . "/local/hoteles_city_dashboard/graficas.php");
$PAGE->set_context($context_system);
$PAGE->set_pagelayout('admin');
$PAGE->set_title(get_string('pluginname', 'local_hoteles_city_dashboard'));
//$tabOptions = local_hoteles_city_dashboard_get_course_tabs();
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

    <!-- Custom scripts for all pages-->
    <!-- <script src="js/sb-admin-2.min.js"></script> -->

    <script src="vendor/chart.js/Chart.js"></script>

    <!-- <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css"> -->
    <!-- <link rel="stylesheet" href="css/jquery.loadingModal.css"> -->
    <link href="estilos_city.css" rel="stylesheet">
    <!-- <script src="hoteles_city_scripts.js"></script> -->


</head>

<body style="background-color: #ecedf1; max-height: 100%; overflow: hidden;">

<!-- Modal -->
    <div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" style="max-width: 100%;">
        <div class="modal-dialog" role="document">
            <!-- <h1 class="txt_modal">Cargando...</h1> -->
            <div id="loader" style="margin-top: 100px;"></div>
        </div>
    </div>


    <div class="row container filtro_m">
        <form action="" name='local_hoteles_city_dashboard_filters' class='row col-sm-12' id='local_hoteles_city_dashboard_filters' >
            <?php
                local_hoteles_city_dashboard_print_filters();
            ?>
        </form>

            <div class="col-12 text-right" style="padding-right: 2%;">
                <button class='btn btn-primary' onclick="obtenerGraficas();modalLoader();">Aplicar filtros</button>
            </div>

    </div>

    <!-- <div>
        <pre id="json"></pre>
    </div> -->

        <!-- <div class="modal d-block d-sm-none" tabindex="-1" role="dialog" style="background: rgba(0,0,0,0.6);">
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
         </div> -->



    <!-- Div para pintar la grafica comparativa de los cursos -->
    <div class="row" style="justify-content: center; max-width: 100%;padding-top: 1rem;" id="comparative">
        <div class="col-sm-12 col-md-8">
                    <div class="card shadow mb-4">
                        <!-- Card Header - Dropdown -->
                        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                            <h6 class="m-0 font-weight-bold text-primary"><a href="#">Comparativa</a></h6>
                            <div class="dropdown no-arrow">


                            </div>
                        </div>
                        <!-- Card Body -->
                        <div class="card-body">
                            <div class="" id="div_comparativa">
                                <!-- <canvas id="grafica_comparativa"></canvas>                   -->
                            </div>
                        </div>
                    </div>
        </div>
    </div>

     <!-- Div para filtros seleccionados -->
     <div class="col-sm-3">Filtros seleccionados:</div>
    <div class="col-sm-12 row filtro_div" style="text-align: center;">
        <div class="col-sm-3" id="uo" style="word-break: break-word;"></div>
        <div class="col-sm-3" id="puesto"></div>
        <div class="col-sm-3" id="marca"></div>
        <div class="col-sm-3" id="region"></div>
    </div>
    <div  class="row">
      <div id="pag-content" class="col-sm-12 center p-3">
        <nav id="paginacion" aria-label="Page navigation example">

        </nav>
      </div>


    </div>

    <!-- Div para pintar las graficas de los cursos -->
    <div id="curso_graficas" class="row" style="padding: 15px 25px; max-width: 100%;"></div>


    <!-- Bootstrap core JavaScript-->
    <script src="vendor/jquery/jquery.min.js"></script>
    <!-- Core plugin JavaScript-->
    <!-- <script src="vendor/jquery-easing/jquery.easing.min.js"></script> -->
    <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

    <link rel="stylesheet" href="choicesjs/styles/choices.min.css" />
    <script src="choicesjs/scripts/choices.min.js"></script>
    <script src="./js/main.js"></script>

    <script>

    var pageCurrent = 1;
    var items = 0;
    var informacion = '';


        $(document).ready(function(){
            $('.multiselect-setting').each(function(index, element){ // Generación de filtros con herramenta choices.js
                var multipleCancelButton = new Choices( '#' + element.id, { removeItemButton: true, searchEnabled: true,
                    placeholderValue: 'Presione aquí para agregar un filtro', searchPlaceholderValue: 'Buscar filtro',
                    placeholder: true,
                } );
            });
            obtenerGraficas();

        });
        function obtenerGraficas(){
            
            // peticion = [];
            peticion = $('#local_hoteles_city_dashboard_filters').serializeArray();
            peticion.push({name: 'request_type', value: 'course_list'});

            $.ajax({
                type: "POST",
                url: "services.php",
                data: peticion,
                dataType: "json"
            })
                .done(function(data) {
                    

                    informacion = JSON.parse(JSON.stringify(data));
                    informacion = informacion.data;

                    console.log('Esto es data')
                    console.log(data);
                    console.log('Esto es informacion')
                    console.log(informacion);
                    cleanDiv();
                    $('#div_comparativa').html('<canvas id="grafica_comparativa"></canvas>');
                    //$('#grafica_comparativa').height(informacion.length * 30);
                    comparative(informacion);
                  

                    console.log('INFORMACION')
                    console.log(informacion[0])
                    if(informacion[0] != undefined && informacion[0].query ){
                        filtros = JSON.parse(JSON.stringify(informacion[0].query));
                        console.log('JSON')
                        console.log(filtros)
                        prueba = JSON.parse(filtros);
                        // console.log('PRUEBA')
                        console.log(Object.keys(prueba).length);
                        var custom = '';
                        for(const prop in prueba){
                          console.log(`prueba.${prop} = ${prueba[prop]}`);
                          if(prop == 'institution'){
                            if(typeof prueba.institution === "object"){
                                f_uo = JSON.parse(JSON.stringify(prueba.institution));
                                $('#uo').html(f_uo);
                            }else{
                                $('#uo').html('');
                            }
                            // $('#uo').html(`${prueba[prop]}`);
                          } else if(prop == 'department'){
                            $('#puesto').html(`${prueba[prop]}`);
                          } else {
                            custom += `${prueba[prop]}`+'<br><br>';
                            $('#marca').html(custom);
                          }
                        }

                    }

                    //items = Math.round(informacion.length /10);
                    items = getNumPages(informacion.length);
                    //console.log(items);
                    var itemsPage = `<ul class="pagination">
                                      <li class="page-item">
                                        <a class="page-link" href="#pag-content" onclick="pagePrev();" aria-label="Previous">
                                          <span aria-hidden="true">&laquo;</span>
                                          <span class="sr-only">Previous</span>
                                        </a>
                                      </li>`;
                    for(i=0;i<items;i++){
                      itemsPage = agregaItemPag(itemsPage, i +1);
                    }
                    itemsPage += `<li class="page-item">
                                  <a class="page-link" href="#pag-content" onclick="pageNext();" aria-label="Next">
                                    <span aria-hidden="true">&raquo;</span>
                                    <span class="sr-only">Next</span>
                                  </a>
                                </li>
                              </ul>`;
                    $("#paginacion").html(itemsPage);

                    getInformationToPage();



                })
                .fail(function (error, error2) {
                    isCourseLoading = false;
                    console.log('Entra a fail');
                    console.log(error);
                    console.log(error2);
                    showPage();
                });
        }

        function getInformationToPage(){

          var min = getMinIndex(pageCurrent);
          var max = getMAxIndex(pageCurrent);

          document.getElementById('curso_graficas').innerHTML='';
          $(".page-item").each(function(){
            if($(this).hasClass('active')){
              $(this).removeClass('active');
            }

          })

          $("#page"+pageCurrent).addClass('active');

          for(var i = 0; i < informacion.length; i++){

            if(i >= min && i < max){
              info = informacion[i];

              //console.log(info);



              var course = new GraphicsDashboard('curso_graficas',info.title,info.chart,info,12,info.id);
              if(info.enrolled_users > 0){
                  course.printCardCourseInfo();
              }else{
                  course.printCardSinInfo();
              }

              if(info.chart == 'bar-agrupadas'){
                  course.comparative_graph();
              }
              if(info.chart == 'line'){
                  course.comparative_graph();
              }
              if(info.chart == 'horizontalBar'){
                  course.comparative_graph();
              }
              if (info.chart == 'burbuja') {
                  course.comparative_graph();
              }
              if (info.chart == 'pie') {
                  course.individual_graph();
              }
              if (info.chart == 'bar') {
                  course.individual_graph();
              }

              $('#aprobados'+info.id).html(info.percentage +"%");
              var  na = 100 - info.percentage;
              not_approved_users = na.toFixed(2);
              var dec = not_approved_users.split(".");
              if(dec[1] == 00){
                  $('#no_aprobados'+info.id).html(na +"%");
                  // console.log('Entero')
                  // console.log(na)
              }
              else{
                  $('#no_aprobados'+info.id).html(not_approved_users +"%");
                  // console.log('No Entero')
                  // console.log(not_approved_users)
              }
              $('#inscritos'+info.id).html(info.enrolled_users);


            }
          }



        }

        function getNumPages(n){
          if(n%10 > 0 && n%10 < 5){
            return Math.round(n/10) + 1;
          } else {
            return Math.round(n/10);
          }
        }


        function cleanDiv(){
            document.getElementById('curso_graficas').innerHTML='';
            document.getElementById('uo').innerHTML='';
            document.getElementById('puesto').innerHTML='';
            document.getElementById('marca').innerHTML='';
            document.getElementById('region').innerHTML='';
        }
        //Informacion para la grafica comparativa
        function comparative(informacion){
            data_labels = [];
            arr_completado_percantage = [];
            arr_nocompletado_percentage = [];
            datasets_completado = { label: 'Completado', backgroundColor: '#1cc88a', stack: 'Stack 0', data: arr_completado_percantage }
            datasets_nocompletado = { label: 'No Completado', backgroundColor: '#e74a3b', stack: 'Stack 0', data: arr_nocompletado_percentage }
            dataset = [];
            for(var i = 0; i < informacion.length; i++){
                info = informacion[i];
                data_labels.push(info.title);
                arr_completado_percantage.push(info.percentage);
                approved_users = info.percentage;
                not_approved = 100 - approved_users;
                percentage_not_approved = not_approved.toFixed(2);
                arr_nocompletado_percentage.push(percentage_not_approved);
                // console.log('% NO COMPLETADO');
                // console.log(arr_nocompletado_percentage)

            }
            dataset.push(datasets_completado);
            dataset.push(datasets_nocompletado);
            d_graph = { labels: data_labels, datasets: dataset };
            // console.log(datasets_nocompletado)
            // console.log('INFO')
            // console.log(d_graph)
            //return d_graph;
            var ctx = document.getElementById('grafica_comparativa');
            ctx.height = (informacion.length * 10);
            var chart = new Chart(ctx, {
                type: 'horizontalBar',
                data: d_graph,
                options:{
                  responsive:true,

                }

            });
        }
        function onchangeFilter(filterid){ // Se ejecuta esta función cuando el elemento ha cambiado
            // console.log('El elemento ha cambiado');
        }

        function agregaItemPag(elem,i){
          return elem += `<li class="page-item" id="page`+i+`"><a  class="page-link" onclick=goToPage(`+i+`); href="#pag-content">`+i+`</a></li>`;
        }

        function pageNext(){
          if(pageCurrent++ <= items){
            pageCurrent++;
            getInformationToPage();
          }
        }

        function pagePrev(){
          if(pageCurrent-- >= 1){
            pageCurrent--;
            getInformationToPage();

          }
        }

        function goToPage(page){

          pageCurrent = page;
          getInformationToPage();

        }

        function getMinIndex(n){
          if(n - 1 == 0){
            return 0;
          } else if(n - 1 > 0){
            return (n -1) * 10;
          }
        }

        function getMAxIndex(n){
          return n * 10;
        }


    </script>

    <style>
        .choices__button:hover{
            text-indent: -9999px;
            -webkit-appearance: none;
            -moz-appearance: none;
            appearance: none;
            border: 0;
            background-color: transparent;
            background-repeat: no-repeat;
            background-position: center;
            cursor: pointer;
        }
        .choices__button:active{
            text-indent: -9999px;
            -webkit-appearance: none;
            -moz-appearance: none;
            appearance: none;
            border: 0;
            background-color: transparent;
            background-repeat: no-repeat;
            background-position: center;
            cursor: pointer;
        }
        .choices__button:visited{
            text-indent: -9999px;
            -webkit-appearance: none;
            -moz-appearance: none;
            appearance: none;
            border: 0;
            background-color: transparent;
            background-repeat: no-repeat;
            background-position: center;
            cursor: pointer;
        }
        .choices__button:focus{
            text-indent: -9999px;
            -webkit-appearance: none;
            -moz-appearance: none;
            appearance: none;
            border: 0;
            background-color: transparent;
            background-repeat: no-repeat;
            background-position: center;
            cursor: pointer;
        }
        .choices__button:focus-within{
            text-indent: -9999px;
            -webkit-appearance: none;
            -moz-appearance: none;
            appearance: none;
            border: 0;
            background-color: transparent;
            background-repeat: no-repeat;
            background-position: center;
            cursor: pointer;
        }
    </style>

    <script src="classes.js"></script>

</body>

</html>
