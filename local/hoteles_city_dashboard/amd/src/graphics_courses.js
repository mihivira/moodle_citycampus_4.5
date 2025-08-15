define(['jquery', 'core/chartjs'], function($, Chart) {

    /**
     * Graphics Dashboard class
     */
    function GraphicsDashboard(containerId, title, chartType, data, colSize, id) {
        this.containerId = containerId;
        this.title = title;
        this.chartType = chartType;
        this.data = data;
        this.colSize = colSize;
        this.id = id;
        this.chartInstance = null;
    }

    /**
     * Print card with course information
     */
    GraphicsDashboard.prototype.printCardCourseInfo = function() {
        var html = '<div class="col-md-' + this.colSize + ' mb-4">' +
                  '<div class="card shadow">' +
                  '<div class="card-header py-3">' +
                  '<h6 class="m-0 font-weight-bold text-primary"><a href="detalle_curso.php?courseid=' + this.id + '">' + this.title + '</a></h6>' +
                  '</div>' +
                  '<div class="card-body">' +
                  '<div class="row">' +
                  '<div class="col-md-8">' +
                  '<canvas id="chart-' + this.id + '" width="400" height="200"></canvas>' +
                  '</div>' +
                  '<div class="col-md-4">' +
                  '<div class="stats-container">' +
                  '<h6>Estadísticas</h6>' +
                  '<p>Inscritos: <span id="inscritos' + this.id + '">0</span></p>' +
                  '<p>Aprobados: <span id="aprobados' + this.id + '">0</span>%</p>' +
                  '<p>No Aprobados: <span id="no_aprobados' + this.id + '">0</span>%</p>' +
                  '</div>' +
                  '</div>' +
                  '</div>' +
                  '</div>' +
                  '</div>' +
                  '</div>';
                  
        $('#' + this.containerId).append(html);
    };

    /**
     * Print card without information
     */
    GraphicsDashboard.prototype.printCardSinInfo = function() {
        var html = '<div class="col-md-' + this.colSize + ' mb-4">' +
                  '<div class="card shadow">' +
                  '<div class="card-header py-3">' +
                  '<h6 class="m-0 font-weight-bold text-primary">' + this.title + '</h6>' +
                  '</div>' +
                  '<div class="card-body">' +
                  '<p class="text-center text-muted">No hay datos disponibles para este curso</p>' +
                  '</div>' +
                  '</div>' +
                  '</div>';
                  
        $('#' + this.containerId).append(html);
    };

    /**
     * Comparative graph
     */
    GraphicsDashboard.prototype.comparative_graph = function() {
        var canvasId = 'chart-' + this.id;
        var ctx = document.getElementById(canvasId);
        if(!ctx) {
            console.error('Canvas element not found: ' + canvasId);
            return;
        }

        // Destruir instancia anterior si existe
        if(this.chartInstance) {
            this.chartInstance.destroy();
            this.chartInstance = null;
        }

        try {
            var chartData = {
                labels: ['Aprobados', 'No Aprobados'],
                datasets: [{
                    label: 'Porcentaje',
                    data: [this.data.percentage || 0, 
                           Math.max(0, 100 - (this.data.percentage || 0))],
                    backgroundColor: [
                        '#1cc88a',
                        '#e74a3b'
                    ],
                    borderColor: [
                        '#1cc88a',
                        '#e74a3b'
                    ],
                    borderWidth: 1
                }]
            };

            var chartConfig = {
                type: this.chartType === 'horizontalBar' ? 'bar' : 'bar',
                data: chartData,
                options: {
                    indexAxis: this.chartType === 'horizontalBar' ? 'y' : 'x',
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        x: {
                            beginAtZero: true,
                            max: 100
                        },
                        y: {
                            beginAtZero: true,
                            max: 100
                        }
                    }
                }
            };

            this.chartInstance = new Chart(ctx, chartConfig);
            console.log('Chart created successfully for ID: ' + this.id);
        } catch(e) {
            console.error('Error creating chart for ID: ' + this.id, e);
        }
    };

    /**
     * Individual graph (pie chart)
     */
    GraphicsDashboard.prototype.individual_graph = function() {
        var canvasId = 'chart-' + this.id;
        var ctx = document.getElementById(canvasId);
        if(!ctx) {
            console.error('Canvas element not found: ' + canvasId);
            return;
        }

        // Destruir instancia anterior si existe
        if(this.chartInstance) {
            this.chartInstance.destroy();
            this.chartInstance = null;
        }

        try {
            var chartData = {
                labels: ['Aprobados', 'No Aprobados'],
                datasets: [{
                    data: [Math.max(0, this.data.percentage || 0), 
                           Math.max(0, 100 - (this.data.percentage || 0))],
                    backgroundColor: [
                        '#1cc88a',
                        '#e74a3b'
                    ],
                    borderColor: [
                        '#ffffff'
                    ],
                    borderWidth: 2
                }]
            };

            var chartConfig = {
                type: this.chartType === 'pie' ? 'pie' : 'doughnut',
                data: chartData,
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom'
                        }
                    }
                }
            };

            this.chartInstance = new Chart(ctx, chartConfig);
            console.log('Pie chart created successfully for ID: ' + this.id);
        } catch(e) {
            console.error('Error creating pie chart for ID: ' + this.id, e);
        }
    };

    return GraphicsDashboard;
});