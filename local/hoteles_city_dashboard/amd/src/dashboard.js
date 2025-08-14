define([
    'jquery',
    'core/ajax',
    'core/chartjs',
    'core/templates',
    'core/str',
    'core/notification'
], function($, Ajax, Chart, Templates, Str, Notification) {

    // Variables globales
    var isCourseLoading = false;
    var suma_inscritos = 0;
    var suma_no_aprobados = 0;
    var suma_aprobados = 0;
    var arr_total_inscritos = [];
    var arr_total_no_aprobados = [];
    var arr_total_aprobados = [];
    var total_inscritos = 0;
    var total_no_aprobados = 0;
    var total_aprobados = 0;
    var porcentaje_aprobados = 0;
    var porcentaje_noaprobados = 0;

    /**
     * Muestra el overlay de carga
     */
    function mostrarCargando() {
        var overlay = document.getElementById('overlay');
        if (overlay) {
            overlay.style.display = 'flex';
        }
    }

    /**
     * Oculta el overlay de carga
     */
    function ocultarCargando() {
        var overlay = document.getElementById('overlay');
        if (overlay) {
            overlay.style.display = 'none';
        }
    }

    /**
     * Obtiene información por curso
     */
    function regresaInfoByCurso() {
        mostrarCargando();
        
        var requests = Ajax.call([{
            methodname: 'local_hoteles_city_dashboard_data',
            args: {
               
            }
        }]);

        requests[0].done(function(data) {
            ocultarCargando();
            isCourseLoading = false;

            data = JSON.parse(data);

            console.log("data JSON.parse");
            console.log(data);

            var respuesta = data.data;

            console.log(respuesta);
            
            var respuesta = data.data;
            
            for (var i = 0; i < respuesta.length; i++) {
                var resp = respuesta[i];
                var curso = new GraphicsDashboard('contenedor_graficas', resp.name, resp.chart, resp, 6);
                
                if (resp.elements.length > 0) {
                    curso.printCardAdvance();
                } else {
                    curso.printCardSinInfo();
                }

                switch (resp.chart) {
                    case 'bar-agrupadas':
                    case 'line':
                    case 'horizontalBar':
                    case 'burbuja':
                        curso.comparative_graph();
                        break;
                    case 'pie':
                    case 'bar':
                        curso.individual_graph();
                        break;
                }
            }
            printInfoCards();

        }).fail(function(error) {
            isCourseLoading = false;
            Notification.exception(error);
        });
    }

    /**
     * Procesa datos para gráficas
     */
    function graph_data(respuesta) {
        var data_labels = [];
        var arr_datasets_aprobados_percentage = [];
        var arr_datasets_no_aprobados_percentage = [];
        var arr_datasets_inscritos = [];
        var dataset = [];
        var suma_inscritos = 0;
        var suma_no_aprobados = 0;
        var suma_aprobados = 0;

        for (var j = 0; j < respuesta.elements.length; j++) {
            data_labels.push(respuesta.elements[j].name);
            
            var approved_users = parseFloat(respuesta.elements[j].percentage);
            var not_approved = 100 - approved_users;
            var percentage_not_approved = not_approved.toFixed(2);
            
            arr_datasets_aprobados_percentage.push(approved_users);
            arr_datasets_no_aprobados_percentage.push(percentage_not_approved);
            arr_datasets_inscritos.push('100');
            
            suma_inscritos += parseInt(respuesta.elements[j].enrolled_users);
            suma_no_aprobados += parseInt(respuesta.elements[j].not_approved_users);
            suma_aprobados += parseInt(respuesta.elements[j].approved_users);
        }

        arr_total_inscritos.push(suma_inscritos);
        arr_total_no_aprobados.push(suma_no_aprobados);
        arr_total_aprobados.push(suma_aprobados);

        var total_inscritos = arr_total_inscritos.reduce((a, b) => a + b, 0);
        var total_no_aprobados = arr_total_no_aprobados.reduce((a, b) => a + b, 0);
        var total_aprobados = arr_total_aprobados.reduce((a, b) => a + b, 0);
        
        var porcentaje_aprobados = ((total_aprobados * 100) / total_inscritos) || 0;
        var porcentaje_noaprobados = ((total_no_aprobados * 100) / total_inscritos) || 0;

        var datasets_aprobados, datasets_no_aprobados;

        switch (respuesta.chart) {
            case 'bar-agrupadas':
                datasets_aprobados = { 
                    label: 'Completado', 
                    backgroundColor: '#1cc88a', 
                    stack: 'Stack 0', 
                    data: arr_datasets_aprobados_percentage 
                };
                datasets_no_aprobados = { 
                    label: 'No Completado', 
                    backgroundColor: '#e74a3b', 
                    stack: 'Stack 0', 
                    data: arr_datasets_no_aprobados_percentage 
                };
                dataset.push(datasets_aprobados);
                dataset.push(datasets_no_aprobados);
                break;
                
            case 'line':
                datasets_aprobados = { 
                    label: 'Completado', 
                    borderColor: "#1cc88a", 
                    backgroundColor: 'transparent', 
                    data: arr_datasets_aprobados_percentage 
                };
                datasets_no_aprobados = { 
                    label: 'No Completado', 
                    borderColor: "#e74a3b", 
                    backgroundColor: 'transparent', 
                    data: arr_datasets_no_aprobados_percentage 
                };
                dataset.push(datasets_aprobados);
                dataset.push(datasets_no_aprobados);
                break;
                
            case 'horizontalBar':
                datasets_aprobados = { 
                    label: 'Completado', 
                    backgroundColor: '#1cc88a', 
                    stack: 'Stack 0', 
                    data: arr_datasets_aprobados_percentage 
                };
                datasets_no_aprobados = { 
                    label: 'No Completado', 
                    backgroundColor: '#e74a3b', 
                    stack: 'Stack 0', 
                    data: arr_datasets_no_aprobados_percentage 
                };
                dataset.push(datasets_aprobados);
                dataset.push(datasets_no_aprobados);
                break;
        }

        return { labels: data_labels, datasets: dataset };
    }

    /**
     * Imprime información en las cards
     */
    function printInfoCards() {
        var requests = Ajax.call([{
            methodname: 'local_hoteles_city_dashboard_cards',
            args: {
                
            }
        }]);

        requests[0].done(function(data) {
            data = JSON.parse(data);
            var info_cards = data.data;
            var approved_users = parseFloat(info_cards.approved_users).toFixed(2);
            var not_approved_users = parseFloat(info_cards.not_approved_users).toFixed(2);

            $('#card_cantidad_usarios').html(info_cards.num_users);
            $('#card_no_aprobados').html(not_approved_users + '%');
            $('#progress_noaprobados').css("width", not_approved_users + "%");
            $('#card_aprobados').html(approved_users + "%");
            $('#progress_aprobados').css("width", approved_users + "%");
            $('#card_numero_hoteles').html(info_cards.num_institutions);

        }).fail(function(error) {
            Notification.exception(error);
        });
    }

    /**
     * Clase para manejar gráficas del dashboard
     */
    class GraphicsDashboard {
        constructor(div_print_card, title, type_graph, data_graph, col_size_graph, enlace) {
            this.div_print_card = div_print_card;
            this.title = title;
            this.div_graph = div_print_card + Date.now();
            this.type_graph = type_graph;
            this.data_graph = data_graph;
            this.col_size_graph = col_size_graph;
            this.enlace = enlace;
        }

        printCardAdvance() {
            $("#" + this.div_print_card).append(`
                <div class="col-sm-${this.col_size_graph}">
                    <div class="card shadow mb-4">
                        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                            <h6 class="m-0 font-weight-bold text-primary"><a href="#">${this.title}</a></h6>
                        </div>
                        <div class="card-body">
                            <div class="">
                                <canvas id="${this.div_graph}"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            `);
        }

        printCardSinInfo() {
            $("#" + this.div_print_card).append(`
                <div class="col-sm-${this.col_size_graph} col-md-6 col-lg-4">
                    <div class="card shadow mb-4">
                        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                            <h6 class="m-0 font-weight-bold text-primary"><a href="#">${this.title}</a></h6>
                        </div>
                        <div class="card-body">
                            <div class="">
                                <canvas id="${this.div_graph}" style="display: none"></canvas>
                                <h3 class="txt_sin_usuarios">No hay usuarios inscritos</h3>
                            </div>
                        </div>
                    </div>
                </div>
            `);
        }

        comparative_graph() {
            switch (this.type_graph) {
                case 'bar-agrupadas':
                case 'line':
                    var data_agrupadas = graph_data(this.data_graph);
                    var ctx = document.getElementById(this.div_graph);
                    
                    new Chart(ctx, {
                        type: this.type_graph === 'bar-agrupadas' ? 'bar' : this.type_graph,
                        data: data_agrupadas,
                        options: {}
                    });
                    break;

                case 'horizontalBar':
                    var data_agrupadas = graph_data(this.data_graph);
                    var ctx = document.getElementById(this.div_graph);
                    ctx.height = (data_agrupadas.labels.length * 20);
                    
                    // Corrección: usar 'bar' con indexAxis: 'y'
                    new Chart(ctx, {
                        type: 'bar',
                        data: data_agrupadas,
                        options: {
                            indexAxis: 'y', // Esto hace que sea horizontal
                        }
                    });
                    break;

                case 'burbuja':
                    if (this.data_graph.enrolled_users > 0) {
                        var ctx = document.getElementById(this.div_graph);
                        new Chart(ctx, {
                            type: 'bubble',
                            data: {
                                labels: "Africa",
                                datasets: [{
                                    label: ["Hotel 1"],
                                    backgroundColor: "rgba(255,221,50,0.2)",
                                    borderColor: "rgba(255,221,50,1)",
                                    data: [{ x: 212, y: 207, r: 5 }]
                                }]
                            },
                            options: {
                                title: {
                                    display: true,
                                    text: 'Predicted world population (millions) in 2050'
                                },
                                scales: {
                                    yAxes: [{
                                        scaleLabel: {
                                            display: true,
                                            labelString: "Happiness"
                                        }
                                    }],
                                    xAxes: [{
                                        scaleLabel: {
                                            display: true,
                                            labelString: "GDP (PPP)"
                                        }
                                    }]
                                }
                            }
                        });
                    } else {
                        var ctx = document.getElementById(this.div_graph);
                        if (ctx) ctx.innerHTML = "No existen usuarios inscritos";
                    }
                    break;
            }
        }

        individual_graph() {
            switch (this.type_graph) {
                case 'pie':
                    var d_graph = [];
                    var approved_percentage = parseFloat(this.data_graph.percentage);
                    var not_approved_percentage = 100 - approved_percentage;
                    
                    d_graph.push(approved_percentage);
                    d_graph.push(not_approved_percentage);

                    var ctx = document.getElementById(this.div_graph);
                    new Chart(ctx, {
                        type: 'pie',
                        data: {
                            labels: ["Completado", "No Completado"],
                            datasets: [{
                                backgroundColor: ["#1cc88a", "#e74a3b"],
                                data: d_graph
                            }]
                        },
                        options: {}
                    });
                    break;

                case 'bar':
                    var d_graph = [];
                    var approved_percentage = parseFloat(this.data_graph.percentage);
                    var not_approved_percentage = 100 - approved_percentage;
                    
                    d_graph.push(approved_percentage);
                    d_graph.push(not_approved_percentage);
                    d_graph.push('0');
                    d_graph.push('100');

                    var ctx = document.getElementById(this.div_graph);
                    new Chart(ctx, {
                        type: 'bar',
                        data: {
                            labels: ["Completado", "No Completado"],
                            datasets: [{
                                backgroundColor: ["#1cc88a", "#e74a3b"],
                                data: d_graph
                            }]
                        },
                        options: {
                            legend: { display: false }
                        }
                    });
                    break;
            }
        }
    }

    /**
     * Inicializa el módulo
     */
    function init() {
        $(document).ready(function() {
            
            regresaInfoByCurso();
        });
    }

    return {
        init: init
    };
});