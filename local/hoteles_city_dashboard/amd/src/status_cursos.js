define([
    'jquery',
    'core/chartjs',
    'core/ajax',
    'core/templates',
    'core/notification',
    'local_hoteles_city_dashboard/graphics_courses'
], function($, Chart, Ajax, Templates, Notification, GraphicsDashboard) {

    var pageCurrent = 1;
    var items = 0;
    var informacion = '';
    var comparativeChart = null;

    /**
     * Initialize the module
     */
    function init() {
        $(document).ready(function() {
            initializeChoices();
            obtenerGraficas();
            setupEventListeners();
        });
    }

    /**
     * Initialize Choices.js for multiselect elements
     */
    function initializeChoices() {
        $('.multiselect-setting').each(function(index, element) {
            // You'll need to include Choices.js as a dependency or use Moodle's built-in select components
            var multipleCancelButton = new Choices( '#' + element.id, { removeItemButton: true, searchEnabled: true,
                    placeholderValue: 'Presione aquí para agregar un filtro', searchPlaceholderValue: 'Buscar filtro',
                    placeholder: true,
                } );
        });
    }

    /**
     * Setup event listeners
     */
    function setupEventListeners() {
        $('#apply-filters-btn').on('click', function(e) {
            e.preventDefault();
            obtenerGraficas();
            modalLoader();
        });
    }

    /**
     * Show loader modal
     */
    function modalLoader() {
        $('#exampleModal').modal('show');
    }

    /**
     * Hide loader modal
     */
    function hideLoader() {
        $('#exampleModal').modal('hide');
    }

    /**
     * Get graphics data
     */
    function obtenerGraficas() {
        var peticion = $('#local_hoteles_city_dashboard_filters').serializeArray();
        peticion.push({name: 'request_type', value: 'course_list'});

        $.ajax({
            type: "POST",
            url: M.cfg.wwwroot + "/local/hoteles_city_dashboard/services.php",
            data: peticion,
            dataType: "json"
        })
        .done(function(data) {
            informacion = data.data;

            console.log('Data received:', data);
            
            cleanDiv();
            $('#div_comparativa').html('<canvas id="grafica_comparativa" width="400" height="200"></canvas>');
            comparative(informacion);

            if(informacion[0] !== undefined && informacion[0].query) {
                var filtros = informacion[0].query;
                var prueba = JSON.parse(filtros);
                var custom = '';
                for(const prop in prueba) {
                    if(prop === 'institution') {
                        if(typeof prueba.institution === "object") {
                            var f_uo = prueba.institution;
                            $('#uo').html(JSON.stringify(f_uo));
                        } else {
                            $('#uo').html('');
                        }
                    } else if(prop === 'department') {
                        $('#puesto').html(prueba[prop]);
                    } else {
                        custom += prueba[prop] + '<br><br>';
                        $('#marca').html(custom);
                    }
                }
            }

            items = getNumPages(informacion.length);
            var itemsPage = '<ul class="pagination">' +
                          '<li class="page-item">' +
                          '<a class="page-link" href="#pag-content" aria-label="Previous" onclick="pagePrev(); return false;">' +
                          '<span aria-hidden="true">&laquo;</span>' +
                          '<span class="sr-only">Previous</span>' +
                          '</a>' +
                          '</li>';
                          
            for(var i = 0; i < items; i++) {
                itemsPage = agregaItemPag(itemsPage, i + 1);
            }
            
            itemsPage += '<li class="page-item">' +
                        '<a class="page-link" href="#pag-content" aria-label="Next" onclick="pageNext(); return false;">' +
                        '<span aria-hidden="true">&raquo;</span>' +
                        '<span class="sr-only">Next</span>' +
                        '</a>' +
                        '</li>' +
                        '</ul>';
                        
            $("#paginacion").html(itemsPage);
            getInformationToPage();
            hideLoader();
        })
        .fail(function(error, error2) {
            console.log('Entra a fail');
            console.log(error);
            console.log(error2);
            hideLoader();
        });
    }

    /**
     * Get information for current page
     */
    function getInformationToPage() {
        var min = getMinIndex(pageCurrent);
        var max = getMaxIndex(pageCurrent);

        document.getElementById('curso_graficas').innerHTML = '';
        
        // Limpiar clases active de paginación
        $(".page-item").removeClass('active');
        $("#page" + pageCurrent).addClass('active');

        for(var i = 0; i < informacion.length; i++) {
            if(i >= min && i < max) {
                var info = informacion[i];

                var course = new GraphicsDashboard('curso_graficas', info.title, info.chart, info, 4, info.id);
                if(info.enrolled_users > 0) {
                    course.printCardCourseInfo();
                    // Renderizar la gráfica después de crear el elemento
                    setTimeout((function(course_instance, course_info) {
                        return function() {
                            renderCourseChart(course_instance, course_info);
                        };
                    })(course, info), 100);
                } else {
                    course.printCardSinInfo();
                }

                // Actualizar porcentajes
                setTimeout((function(info_copy) {
                    return function() {
                        if($('#aprobados' + info_copy.id).length) {
                            $('#aprobados' + info_copy.id).html(info_copy.percentage + "%");
                            var na = 100 - info_copy.percentage;
                            var not_approved_users = na.toFixed(2);
                            var dec = not_approved_users.split(".");
                            
                            if(dec[1] === "00" || dec[1] === "0" || dec[1] === 0) {
                                $('#no_aprobados' + info_copy.id).html(na + "%");
                            } else {
                                $('#no_aprobados' + info_copy.id).html(not_approved_users + "%");
                            }
                            $('#inscritos' + info_copy.id).html(info_copy.enrolled_users);
                        }
                    };
                })(info), 150);
            }
        }
    }

    /**
     * Render course chart
     */
    function renderCourseChart(course, info) {
        console.log('Rendering chart for:', info.id, 'Type:', info.chart);
        
        if(info.chart === 'bar-agrupadas' || info.chart === 'horizontalBar' || 
           info.chart === 'line' || info.chart === 'burbuja') {
            course.comparative_graph();
        } else if(info.chart === 'pie' || info.chart === 'bar') {
            course.individual_graph();
        } else {
            // Default to comparative graph
            course.comparative_graph();
        }
    }

    /**
     * Get number of pages
     */
    function getNumPages(n) {
        if(n % 10 > 0 && n % 10 < 5) {
            return Math.round(n / 10) + 1;
        } else {
            return Math.round(n / 10);
        }
    }

    /**
     * Clean div elements
     */
    function cleanDiv() {
        // Destruir chart comparativa si existe
        if(comparativeChart) {
            comparativeChart.destroy();
            comparativeChart = null;
        }
        
        document.getElementById('curso_graficas').innerHTML = '';
        document.getElementById('uo').innerHTML = '';
        document.getElementById('puesto').innerHTML = '';
        document.getElementById('marca').innerHTML = '';
        document.getElementById('region').innerHTML = '';
    }

    /**
     * Comparative chart
     */
    function comparative(informacion) {
        if(informacion.length === 0) return;
        
        var data_labels = [];
        var arr_completado_percantage = [];
        var arr_nocompletado_percentage = [];
        
        for(var i = 0; i < informacion.length; i++) {
            var info = informacion[i];
            data_labels.push(info.title || 'Curso ' + (i+1));
            arr_completado_percantage.push(Math.max(0, info.percentage || 0));
            var approved_users = Math.max(0, info.percentage || 0);
            var not_approved = Math.max(0, 100 - approved_users);
            var percentage_not_approved = not_approved.toFixed(2);
            arr_nocompletado_percentage.push(parseFloat(percentage_not_approved));
        }
        
        var datasets_completado = { 
            label: 'Completado', 
            backgroundColor: '#1cc88a', 
            stack: 'Stack 0', 
            data: arr_completado_percantage 
        };
        var datasets_nocompletado = { 
            label: 'No Completado', 
            backgroundColor: '#e74a3b', 
            stack: 'Stack 0', 
            data: arr_nocompletado_percentage 
        };
        
        var dataset = [datasets_completado, datasets_nocompletado];
        var d_graph = { labels: data_labels, datasets: dataset };
        
        var ctx = document.getElementById('grafica_comparativa');
        if(ctx) {
            // Destruir instancia anterior si existe
            if(comparativeChart) {
                comparativeChart.destroy();
            }
            
            ctx.height = Math.max(100, informacion.length * 30);
            
            comparativeChart = new Chart(ctx, {
                type: 'bar',
                data: d_graph,
                options: {
                    indexAxis: 'y',
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        x: {
                            stacked: true,
                            max: 100,
                            beginAtZero: true
                        },
                        y: {
                            stacked: true,
                            beginAtZero: true
                        }
                    }
                }
            });
            console.log('Comparative chart created successfully');
        } else {
            console.error('Comparative chart canvas not found');
        }
    }

    /**
     * Add pagination item
     */
    function agregaItemPag(elem, i) {
        return elem + '<li class="page-item" id="page' + i + '"><a class="page-link" href="#pag-content" onclick="goToPage(' + i + '); return false;">' + i + '</a></li>';
    }

    /**
     * Next page
     */
    function pageNext() {
        if(pageCurrent < items) {
            pageCurrent++;
            getInformationToPage();
        }
    }

    /**
     * Previous page
     */
    function pagePrev() {
        if(pageCurrent > 1) {
            pageCurrent--;
            getInformationToPage();
        }
    }

    /**
     * Go to specific page
     */
    function goToPage(page) {
        pageCurrent = parseInt(page);
        getInformationToPage();
    }

    /**
     * Get minimum index
     */
    function getMinIndex(n) {
        if(n - 1 === 0) {
            return 0;
        } else if(n - 1 > 0) {
            return (n - 1) * 10;
        }
        return 0;
    }

    /**
     * Get maximum index
     */
    function getMaxIndex(n) {
        return n * 10;
    }

    // Exponer funciones globalmente para los onclick handlers
    window.pageNext = pageNext;
    window.pagePrev = pagePrev;
    window.goToPage = goToPage;
    window.getMinIndex = getMinIndex;
    window.getMaxIndex = getMaxIndex;

    return {
        init: init
    };
});