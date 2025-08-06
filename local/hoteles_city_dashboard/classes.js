
function regresaInfoByCurso() {
    mostrarCargando()
    informacion = [];
    informacion.push({ name: 'request_type', value: 'dashboard' });
    $.ajax({
        type: "POST",
        url: "services.php",
        data: informacion,
        dataType: "json"
    })
        .done(function (data) {
            ocultarCargando()
            isCourseLoading = false;
            respuesta = JSON.parse(JSON.stringify(data));
            respuesta = respuesta.data;
            console.log(data);

            

            for (var i = 0; i < respuesta.length; i++) {
                resp = respuesta[i];
                if(respuesta.length == 1){
                    var curso = new GraphicsDashboard('contenedor_graficas', resp.name, resp.chart, resp, 6);
                }else{
                    var curso = new GraphicsDashboard('contenedor_graficas', resp.name, resp.chart, resp, 6);
                }
                if(resp.elements.length > 0){

                    curso.printCardAdvance();
                }else{
                    curso.printCardSinInfo();
                }

                if (resp.chart == 'bar-agrupadas') {
                    curso.comparative_graph();
                }
                if (resp.chart == 'line') {
                    curso.comparative_graph();
                }
                if (resp.chart == 'horizontalBar') {
                    curso.comparative_graph();
                }
                if (resp.chart == 'burbuja') {
                    curso.comparative_graph();
                }
                if (resp.chart == 'pie') {
                    curso.individual_graph();
                }
                if (resp.chart == 'bar') {
                    curso.individual_graph();
                }



            }
            printInfoCards();

        })
        .fail(function (error, error2) {
            isCourseLoading = false;
            console.log('Entra a fail');
            console.log(error);
            console.log(error2);
            showPage();
        });

}



var suma_inscritos = 0;
var suma_no_aprobados = 0;
var suma_aprobados = 0;
arr_total_inscritos = [];
arr_total_no_aprobados = [];
arr_total_aprobados = [];
var total_inscritos = 0;
var total_no_aprobados = 0;
var total_aprobados = 0;
var porcentaje_aprobados = 0;
var porcentaje_noaprobados = 0;
function graph_data(respuesta) {

    data_labels = [];
    resp = respuesta;
    arr_datasets_aprobados = [];
    arr_datasets_aprobados_percentage = [];
    arr_datasets_no_aprobados = [];
    arr_datasets_no_aprobados_percentage = [];
    arr_datasets_inscritos = [];
    if (resp.chart == 'bar-agrupadas') {
        datasets_aprobados = { label: 'Completado', backgroundColor: '#1cc88a', stack: 'Stack 0', data: arr_datasets_aprobados_percentage }
        datasets_no_aprobados = { label: 'No Completado', backgroundColor: '#e74a3b', stack: 'Stack 0', data: arr_datasets_no_aprobados_percentage }
        datasets_inscritos = { label: 'Inscritos', backgroundColor: '#858796', stack: 'Stack 1', data: arr_datasets_inscritos }
    }
    if (resp.chart == 'line') {
        datasets_aprobados = { label: 'Completado', borderColor: "#1cc88a", backgroundColor: 'transparent', data: arr_datasets_aprobados_percentage }
        datasets_no_aprobados = { label: 'No Completado', borderColor: "#e74a3b", backgroundColor: 'transparent', data: arr_datasets_no_aprobados_percentage }
        datasets_inscritos = { label: 'Inscritos', backgroundColor: '#858796', backgroundColor: 'transparent', data: arr_datasets_inscritos }
    }
    if (resp.chart == 'horizontalBar') {
        //datasets_aprobados = { label: 'Aprobados', backgroundColor: '#1cc88a', data: arr_datasets_aprobados }
        datasets_aprobados = { label: 'Completado', backgroundColor: '#1cc88a', stack: 'Stack 0', data: arr_datasets_aprobados_percentage }
        datasets_no_aprobados = { label: 'No Completado', backgroundColor: '#e74a3b', stack: 'Stack 0', data: arr_datasets_no_aprobados_percentage }
        //datasets_inscritos = { label: 'Inscritos', backgroundColor: '#858796', data: arr_datasets_inscritos }
    }
    dataset = [];


    for (j = 0; j < resp.elements.length; j++) {
        data_labels.push(resp.elements[j].name);
        //arr_datasets_aprobados.push(resp.elements[j].approved_users);
        arr_datasets_aprobados_percentage.push(resp.elements[j].percentage);
        approved_users = resp.elements[j].percentage;
        not_approved = 100 - approved_users;
        percentage_not_approved = not_approved.toFixed(2);
        arr_datasets_no_aprobados_percentage.push(percentage_not_approved);
        //arr_datasets_no_aprobados.push(resp.elements[j].not_approved_users);


        //arr_datasets_inscritos.push(resp.elements[j].enrolled_users);
        suma_inscritos = suma_inscritos + resp.elements[j].enrolled_users;
        suma_no_aprobados = suma_no_aprobados + resp.elements[j].not_approved_users;
        suma_aprobados = suma_aprobados + resp.elements[j].approved_users;
        arr_datasets_inscritos.push('100');
    }

    arr_total_inscritos.push(suma_inscritos);
    arr_total_no_aprobados.push(suma_no_aprobados);
    arr_total_aprobados.push(suma_aprobados);


    for (z = 0; z < arr_total_inscritos.length; z++) {
        total_inscritos = total_inscritos + arr_total_inscritos[z];
        total_no_aprobados = total_no_aprobados + arr_total_no_aprobados[z];
        total_aprobados = total_aprobados + arr_total_aprobados[z];
    }
    porcentaje_aprobados = (total_aprobados * 100) / total_inscritos;
    porcentaje_noaprobados = (total_no_aprobados * 100) / total_inscritos;

    if (resp.chart == 'bar-agrupadas') {
        dataset.push(datasets_aprobados);
        dataset.push(datasets_no_aprobados);
        //dataset.push(datasets_inscritos);
        d_graph = { labels: data_labels, datasets: dataset };

    }
    if (resp.chart == 'line') {
        dataset.push(datasets_aprobados);
        dataset.push(datasets_no_aprobados);
        d_graph = { labels: data_labels, datasets: dataset };
    }
    if (resp.chart == 'horizontalBar') {
        dataset.push(datasets_aprobados);
        dataset.push(datasets_no_aprobados);
        d_graph = { labels: data_labels, datasets: dataset };
    }

    return d_graph;

}

//Funcion para imprimir datos en las 4 cards (hoteles, inscritos, aprobados, no aprobados)
function printInfoCards() {
    peticion = [];
    peticion.push({name: 'request_type', value: 'dashboard_cards'});
    $.ajax({
        type: "POST",
        url: "services.php",
        data: peticion,
        dataType: "json"
    })
        .done(function(data) {
            info_cards = JSON.parse(JSON.stringify(data));
            info_cards = info_cards.data;
            console.log(info_cards);
            approved_users = info_cards.approved_users;
            not_approved_users = info_cards.not_approved_users;
            approved_users = parseFloat(approved_users);
            approved_users = approved_users.toFixed(2);
            not_approved_users = parseFloat(not_approved_users);
            not_approved_users = not_approved_users.toFixed(2);

            $('#card_cantidad_usarios').html(info_cards.num_users);

            $('#card_no_aprobados').html(not_approved_users + '%');
            $('#progress_noaprobados').css("width", not_approved_users + "%")

            $('#card_aprobados').html(approved_users + "%");
            $('#progress_aprobados').css("width", approved_users + "%");

            $('#card_numero_hoteles').html(info_cards.num_institutions);
        })
        .fail(function (error, error2) {
            console.log('Error en printInfoCards');
            console.log(error);
            console.log(error2);
        });
}

class GraphicsDashboard {
    constructor(div_print_card, title, type_graph, data_graph, col_size_graph, enlace) {
        this.div_print_card = div_print_card, // Div padre
        this.title = title, //Título de la gráfica
        this.div_graph = div_print_card + Date.now(); //Div donde se imprime la card con la gráfica
        this.type_graph = type_graph, // Tipo de gráfica
        this.data_graph = data_graph, //Datos de la gráfica
        this.col_size_graph = col_size_graph, //Tamaño de la col donde se imprime la card
        //this.enrolled_users = enrolled_users
        this.enlace = enlace

    }



    printCard() {

        $("#" + this.div_print_card).append(`
                <div class="col-sm-${this.col_size_graph} col-md-6">
                <div class="card shadow mb-4">
                    <!-- Card Header - Dropdown -->
                    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                        <h6 class="m-0 font-weight-bold text-primary"><a href="">${this.title}</a></h6>
                        <div class="dropdown no-arrow">

                        </div>
                    </div>
                    <!-- Card Body -->
                    <div class="card-body">
                        <div class="">
                            <canvas id="${this.div_graph}"></canvas>
                        </div>
                    </div>
                </div>
        </div>
        `);
    }

    printCardAdvance() {

        $("#" + this.div_print_card).append(`
                <div class="col-sm-${this.col_size_graph}">
                <div class="card shadow mb-4">
                    <!-- Card Header - Dropdown -->
                    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                        <h6 class="m-0 font-weight-bold text-primary"><a href="#">${this.title}</a></h6>
                        <div class="dropdown no-arrow">

                        </div>
                    </div>
                    <!-- Card Body -->
                    <div class="card-body">
                        <div class="">
                            <canvas id="${this.div_graph}"></canvas>
                        </div>
                    </div>
                </div>
        </div>
        `);
    }

    printCardCourse() {

        $("#" + this.div_print_card).append(`
                <div class="col-sm-${this.col_size_graph} col-md-6 col-lg-4">
                <div class="card shadow mb-4">
                    <!-- Card Header - Dropdown -->
                    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                        <h6 class="m-0 font-weight-bold text-primary"><a href="detalle_curso.php?courseid=${this.enlace}" onclick="top.window.location.href='detalle_curso.php?courseid=${this.enlace}'">${this.title}</a></h6>
                        <div class="dropdown no-arrow">


                        </div>
                    </div>
                    <!-- Card Body -->
                    <div class="card-body">
                        <div class="">
                            <canvas id="${this.div_graph}"></canvas>
                        </div>
                    </div>
                </div>
        </div>
        `);
    }

    printCardCourseInfo() {

        $("#" + this.div_print_card).append(`
                <div class="col-sm-${this.col_size_graph} col-md-6">
                <div class="card shadow mb-4">
                    <!-- Card Header - Dropdown -->
                    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                        <h6 class="m-0 font-weight-bold text-primary"><a href="detalle_curso.php?courseid=${this.enlace}" onclick="top.window.location.href='detalle_curso.php?courseid=${this.enlace}'">${this.title}</a></h6>
                    </div>
                    <div class="col-12 row" style="text-align: center;">
                            <div class="col-4">Completado <p id="aprobados${this.enlace}"></p></div>
                            <div class="col-4">No Completado <p id="no_aprobados${this.enlace}"></p></div>
                            <div class="col-4">Total Inscritos <p id="inscritos${this.enlace}"p></div>
                    </div>
                    <!-- Card Body -->
                    <div class="card-body">
                        <div class="">
                            <canvas id="${this.div_graph}"></canvas>
                        </div>
                    </div>
                </div>
        </div>
        `);
    }

    printCardCourseDetail() {

        $("#" + this.div_print_card).append(`
                <div class="col-sm-${this.col_size_graph}">
                <div class="card shadow mb-4">
                    <!-- Card Header - Dropdown -->
                    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                        <h6 class="m-0 font-weight-bold text-primary"><a href="detalle_curso.php?courseid=${this.enlace}" onclick="top.window.location.href='detalle_curso.php?courseid=${this.enlace}'">${this.title}</a></h6>
                        <div class="dropdown no-arrow">


                        </div>
                    </div>
                    <!-- Card Body -->
                    <div class="card-body">
                        <div class="">
                            <canvas id="${this.div_graph}"></canvas>
                        </div>
                    </div>
                </div>
        </div>
        `);
    }

    printCardSinInfo(){
        $("#" + this.div_print_card).append(`
                <div class="col-sm-${this.col_size_graph} col-md-6 col-lg-4">
                <div class="card shadow mb-4">
                    <!-- Card Header - Dropdown -->
                    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                        <h6 class="m-0 font-weight-bold text-primary"><a href="#">${this.title}</a></h6>
                        <div class="dropdown no-arrow">


                        </div>
                    </div>
                    <!-- Card Body -->
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
        // console.log("comparative_graph")

        switch (this.type_graph) {
            case 'bar-agrupadas':

                var data_agrupadas = graph_data(this.data_graph);


                var ctx = document.getElementById(this.div_graph);
                // console.log('DIV')
                // console.log(this.div_graph)
                var chart = new Chart(ctx, {
                    // The type of chart we want to create
                    type: 'bar',

                    // The data for our dataset
                    data: data_agrupadas,

                    // Configuration options go here
                    options: {

                    }
                });

                break;

            case 'line': //Tendencia
                var data_agrupadas = graph_data(this.data_graph);
                var ctx = document.getElementById(this.div_graph);
                var chart = new Chart(ctx, {
                    // The type of chart we want to create
                    type: 'line',

                    // The data for our dataset
                    data: data_agrupadas,

                    // Configuration options go here
                    options: {

                    }
                });

                break;

            case 'horizontalBar':
                var data_agrupadas = graph_data(this.data_graph);
                console.log('DATA AGRUPADA');
                console.log(data_agrupadas);
                var ctx = document.getElementById(this.div_graph);
                ctx.height = (data_agrupadas.labels.length * 20);
                var chart = new Chart(ctx, {
                    // The type of chart we want to create
                    type: 'horizontalBar',

                    // The data for our dataset
                    data: data_agrupadas,

                    // Configuration options go here

                });

                break;

            case 'burbuja':
                if (this.data_graph.enrolled_users > 0) {
                    var ctx = document.getElementById(this.div_graph);
                    var chart = new Chart(ctx, {
                        type: 'bubble',
                        data: {
                            labels: "Africa",
                            datasets: [
                                {
                                    label: ["Hotel 1"],
                                    backgroundColor: "rgba(255,221,50,0.2)",
                                    borderColor: "rgba(255,221,50,1)",
                                    data: [{
                                        x: 212,//inscritos
                                        y: 207,//aprobados
                                        r: 5 //no aprobados
                                    }]
                                }, {
                                    label: ["Hotel 2"],
                                    backgroundColor: "rgba(60,186,159,0.2)",
                                    borderColor: "rgba(60,186,159,1)",
                                    data: [{
                                        x: 258,
                                        y: 726,
                                        r: 10
                                    }]
                                }, {
                                    label: ["Hotel 3"],
                                    backgroundColor: "rgba(0,0,0,0.2)",
                                    borderColor: "#000",
                                    data: [{
                                        x: 397,
                                        y: 994,
                                        r: 15
                                    }]
                                }
                            ]
                        },
                        options: {
                            title: {
                                display: true,
                                text: 'Predicted world population (millions) in 2050'
                            }, scales: {
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
                }
                else {
                    var ctx = document.getElementById(this.div_graph)
                    ctx.innerHTML = "No existen usuarios inscritos";
                }

                break;



            default:
                break;
        }
    }
    individual_graph() {
        switch (this.type_graph) {
            case 'pie':
                var d_graph = Array();
                d_graph.push(this.data_graph.percentage);
                var approved_users = info.percentage;
                var not_approved = 100 - approved_users;
                percentage_not_approved = not_approved.toFixed(2);
                //d_graph.push(percentage_not_approved);
                // console.log('PRUEBA')
                // console.log(percentage_not_approved)
                if(percentage_not_approved == 100.00){
                    percentage_not_approved = 100;
                    d_graph.push(percentage_not_approved);
                }
                else if(percentage_not_approved == 90.00){
                    percentage_not_approved = 90;
                    d_graph.push(percentage_not_approved);
                }
                else if(percentage_not_approved == 80.00){
                    percentage_not_approved = 80;
                    d_graph.push(percentage_not_approved);
                }
                else if(percentage_not_approved == 70.00){
                    percentage_not_approved = 70;
                    d_graph.push(percentage_not_approved);
                }
                else if(percentage_not_approved == 60.00){
                    percentage_not_approved = 60;
                    d_graph.push(percentage_not_approved);
                }
                else if(percentage_not_approved == 50.00){
                    percentage_not_approved = 50;
                    d_graph.push(percentage_not_approved);
                }
                else if(percentage_not_approved == 40.00){
                    percentage_not_approved = 40;
                    d_graph.push(percentage_not_approved);
                }
                else if(percentage_not_approved == 30.00){
                    percentage_not_approved = 30;
                    d_graph.push(percentage_not_approved);
                }
                else if(percentage_not_approved == 20.00){
                    percentage_not_approved = 20;
                    d_graph.push(percentage_not_approved);
                }
                else if(percentage_not_approved == 10.00){
                    percentage_not_approved = 10;
                    d_graph.push(percentage_not_approved);
                }
                else{
                    d_graph.push(percentage_not_approved);
                }
                // var dec = percentage_not_approved.split(".");
                // if(dec[1] == 00){
                //     percentage_not_approved = not_approved;
                //     d_graph.push(percentage_not_approved);
                //     console.log("Es entero")
                // }else{
                //     d_graph.push(percentage_not_approved);
                //     console.log("No es entero")
                // }


                    var ctx = document.getElementById(this.div_graph);
                    var chart = new Chart(ctx, {
                        type: 'pie',
                        data: {
                            labels: ["Completado", "No Completado"],
                            datasets: [{
                                backgroundColor: ["#1cc88a", "#e74a3b"],
                                data: d_graph
                            }]
                        },
                        options: {

                        }
                    });
                break;

            case 'bar':
                var d_graph = Array();
                d_graph.push(this.data_graph.percentage);
                var percentage_not_approved = 100 - this.data_graph.percentage;
                // console.log('INFO')
                // console.log(percentage_not_approved)
                // d_graph.push(this.data_graph.approved_users);
                // d_graph.push(this.data_graph.not_approved_users);
                d_graph.push(percentage_not_approved);
                d_graph.push('0');
                d_graph.push('100');
                    var ctx = document.getElementById(this.div_graph);
                    var chart = new Chart(ctx, {
                        // The type of chart we want to create
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



            default:
                break;
        }

    }

    individual_graph_report() {
        switch (this.type_graph) {
            case 'pie':

                var d_graph = Array();
                d_graph.push(this.data_graph.percentage);
                var approved_users = informacion.percentage;
                var not_approved = 100 - approved_users;
                var percentage_not_approved = not_approved.toFixed(2);
                // d_graph.push(percentage_not_approved);
                if(percentage_not_approved == 100.00){
                    percentage_not_approved = 100;
                    d_graph.push(percentage_not_approved);
                }
                else if(percentage_not_approved == 90.00){
                    percentage_not_approved = 90;
                    d_graph.push(percentage_not_approved);
                }
                else if(percentage_not_approved == 80.00){
                    percentage_not_approved = 80;
                    d_graph.push(percentage_not_approved);
                }
                else if(percentage_not_approved == 70.00){
                    percentage_not_approved = 70;
                    d_graph.push(percentage_not_approved);
                }
                else if(percentage_not_approved == 60.00){
                    percentage_not_approved = 60;
                    d_graph.push(percentage_not_approved);
                }
                else if(percentage_not_approved == 50.00){
                    percentage_not_approved = 50;
                    d_graph.push(percentage_not_approved);
                }
                else if(percentage_not_approved == 40.00){
                    percentage_not_approved = 40;
                    d_graph.push(percentage_not_approved);
                }
                else if(percentage_not_approved == 30.00){
                    percentage_not_approved = 30;
                    d_graph.push(percentage_not_approved);
                }
                else if(percentage_not_approved == 20.00){
                    percentage_not_approved = 20;
                    d_graph.push(percentage_not_approved);
                }
                else if(percentage_not_approved == 10.00){
                    percentage_not_approved = 10;
                    d_graph.push(percentage_not_approved);
                }
                else{
                    d_graph.push(percentage_not_approved);
                }

                    var ctx = document.getElementById(this.div_graph);
                    var chart = new Chart(ctx, {
                        type: 'pie',
                        data: {
                            labels: ["Completado", "No Completado"],
                            datasets: [{
                                backgroundColor: ["#1cc88a", "#e74a3b"],
                                data: d_graph
                            }]
                        },
                        options: {


                        }
                    });
                break;
            default:
                break;
        }

    }

}
