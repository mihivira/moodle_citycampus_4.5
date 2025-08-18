define([ 'local_hoteles_city_dashboard/graphics_courses', 'local_hoteles_city_dashboard/utils'], function(graphicsCourses, utils) {

    var CourseDetail = {
        datatable: null,
        reportCourses: '',
        config: {},
        intervals: 0,
        columns: [],
        id_div_grafica: 'curso_graficas'
    };

    CourseDetail.init = function(config) {
        this.config = config;
        this.reportCourses = config.courseid != -1 ? config.courseid : "";
        
        console.log('CourseDetail init with config:', this.config);
        this.config.ajax_code = this.parseAjaxCode(this.config.ajax_code || []);
        
        this.initEvents();
        this.initializeChoices();
        this.initDataTable();
        
    };

     /**
     * Initialize Choices.js for multiselect elements
     */
    CourseDetail.initializeChoices = function() {
        $('.multiselect-setting').each(function(index, element) {
            // You'll need to include Choices.js as a dependency or use Moodle's built-in select components
            var multipleCancelButton = new Choices( '#' + element.id, { removeItemButton: true, searchEnabled: true,
                    placeholderValue: 'Presione aquí para agregar un filtro', searchPlaceholderValue: 'Buscar filtro',
                    placeholder: true,
                } );
        });
    }


    CourseDetail.initEvents = function() {
        console.log('Inicializando eventos para CourseDetail');

        var self = this;
        
        $(document).on('click', '#search_btn', function() {
            self.reportCourses = $('#report_courses').val();
            if (self.datatable) {
                self.datatable.ajax.reload();
            }
        });

        $(document).on('click', '#quitar_cursos', function() {
            self.quitarCursos();
        });

        $(document).on('click', '#todos_cursos, #descargar_cursos', function() {
            self.todosCursos();
        });
    };

    CourseDetail.initDataTable = function() {
        console.log('Inicializando DataTable para CourseDetail');
        var self = this;
        
        if ($('#empTable').length === 0) {
            console.log('No se encontró la tabla #empTable');
            return;
        }

        // Destruir DataTable existente si ya existe
        if ($.fn.DataTable.isDataTable('#empTable')) {
            console.log('Destruyendo DataTable existente');
            $('#empTable').DataTable().destroy();
        }
        
        this.datatable = $('#empTable').DataTable({
            'processing': true,
            'serverSide': true,
            'serverMethod': 'post',
            'initComplete': function() {
                console.log('DataTable inicializado');
                $('.dataTables_filter input').off('keyup.DT input.DT');
                $('.dataTables_filter input').on('keyup', function(e){
                    var code = e.keyCode || e.which;
                    if (code == 13) {
                        self.datatable.search(this.value).draw();
                    }
                });
            },
            'ajax': {
                'url': self.config.wwwroot + '/local/hoteles_city_dashboard/services.php',
                'data': function(d) {
                    d['request_type'] = 'course_users_pagination';
                    d['reportCourses'] = self.reportCourses || '';
                    console.log('Enviando datos AJAX:', d);
                    self.mostrarGraficaDeCursos();
                    
                    if(self.reportCourses && self.reportCourses > 0){
                        self.selectCurso(self.reportCourses);
                    }
                },
                 'dataSrc': function(json) {
                    console.log('Respuesta AJAX recibida:', json);
                    if (json.error) {
                        console.error('Error del servidor:', json.error);
                        return [];
                    }
                    // Asegurarse de que json.data existe
                    var data = json.data || json.aaData || [];
                    console.log('Datos procesados:', data);
                    return data;
                }
            },
            'lengthMenu': [
                [ -1],
                ["Todos los registros"]
            ],
            'dom': 'Bfrtip',
            'pageLength': -1,
            'buttons': [{
                extend: 'excelHtml5',
                text: '<span class="fa fa-file-excel-o"></span> Exportar a excel',
                exportOptions: {
                    modifier: {
                        search: 'applied',
                        order: 'applied'
                    },
                    columns: self.parseColumns(self.config.ajax_printed_rows)
                }
            }, {
                extend: 'csvHtml5',
                text: '<span class="fa fa-file-o"></span> Exportar a CSV',
                exportOptions: {
                    modifier: {
                        search: 'applied',
                        order: 'applied'
                    },
                    columns: self.parseColumns(self.config.ajax_printed_rows)
                }
            }, 'pageLength'],
            'columns': self.parseAjaxCode(self.config.ajax_code),
            'language': {
                "url": "datatables/Spanish.json",
                "emptyTable": "No se encontró información",
                "loadingRecords": "Cargando...",
                "processing": "<div class='spinner'></div>",
                "search": "Búsqueda:",
                "paginate": {
                    "first": "Primera",
                    "last": "Última",
                    "next": "Siguiente",
                    "previous": "Anterior"
                },
                "buttons": {
                    "pageLength": {
                        "_": "Mostrando %d filas",
                        "-1": "Mostrando todas las filas"
                    }
                }
            },
            "columnDefs": [{
                "targets": self.parseColumns(self.config.ajax_link_fields),
                "orderable": false
            }]
        });

        $.fn.dataTable.ext.errMode = 'throw';
        console.log('DataTable inicializado correctamente');
    };

    CourseDetail.parseColumns = function(columnString) {
        if (!columnString) return [];
        return columnString.split(',').map(Number);
    };

    CourseDetail.parseAjaxCode = function(ajaxCode) {
        

        const columns = ajaxCode;

        //recorrer columns
        columns.forEach((column, index) => {
            if (column.data === 'link_edit_user') {
                column.render = function(data, type, row) {
                        return `<a target="_self" class="btn btn-primary" href="administrar_usuarios.php?id=${data}">Editar usuario</a>`;
                    };
                } else if (column.data === 'name') {
                    column.render = function(data, type, row) {
                        const parts = data.split('||'); // nombre||id
                        return `<a class="_blank" href="administrar_usuarios.php?id=${parts[1]}">${parts[0]}</a>`;
                    };
                } else if (column.data === 'link_suspend_user') {
                    column.render = function(data, type, row) {
                        const arrSusp = data.split('||'); // estado||id
                        const clase = arrSusp[1] === '1' ? 'btn btn-danger' : 'btn btn-success';
                        const texto = arrSusp[1] === '1' ?  'Activar' : 'Suspender';
                        const id = arrSusp[0]; // Assuming 'id' is the second part
                        return `<button class="confirm-action ${clase}" data-id="${id}" data-texto="${texto}">${texto}</button>`;
                    };
                } else if (column.data === 'link_libro_calificaciones') {
                    column.render = function(data, type, row) {
                        const parts = data.split('||'); // id||courseid  
                        return `<a target="_blank" class="btn btn-primary" href="${this.config.wwwroot}/grade/report/user/index.php?id=${parts[1]}&userid=${parts[0]}">Libro de calificaciones</a>`;
                    }.bind(this);
                }
            });

            console.log('Parsed columns:', columns);
            
            return columns;
        
    };

    CourseDetail.mostrarGraficaDeCursos = function() {
        var self = this;
        var courseId = this.reportCourses;

        console.log('Mostrando curso(s): ', courseId);

        //reviar si courseId es un array o un string
        if (Array.isArray(courseId)) {
            courseId = courseId.toString();
        } 

        
        
        
        var peticion = {
            request_type: 'course_completion',
            courseid: courseId || ''
        };

        console.log('Enviando petición para mostrar gráfica de cursos:', peticion);

        $.ajax({
            type: "POST",
            url: this.config.wwwroot + "/local/hoteles_city_dashboard/services.php",
            data: peticion,
            dataType: "json"
        })
        .done(function(data) {
            if (data && data.data) {
                var informacion = data.data;
                
                self.cleanDiv();
                    
                
                
                // Aquí iría la lógica de GraphicsDashboard
                console.log('Información recibida:', informacion);

                informacion = utils.getPercentages(informacion);
                informacion.title = informacion.title || 'Gráfica de Cursos';
                var course = new graphicsCourses(self.id_div_grafica, informacion.title, informacion.chart, informacion, 12, informacion.id);
                if(informacion.enrolled_users > 0) {
                    course.printCardCourseInfo();
                    // Renderizar la gráfica después de crear el elemento
                    setTimeout((function(course_instance, course_info) {
                        return function() {
                            utils.renderCourseChart(course_instance, course_info);
                        };
                    })(course, informacion), 100);
                } else {
                    course.printCardSinInfo();
                }
                self.showPage();

            }
        })
        .fail(function(xhr, status, error) {
            console.log('Error en mostrarGraficaDeCursos:', error);
            self.showPage();
        });
    };

    CourseDetail.cleanDiv = function() {
        var graficaDiv = document.getElementById(this.id_div_grafica);
        if (graficaDiv) {
            graficaDiv.innerHTML = '';
        }
    };

    CourseDetail.showPage = function() {
        // Lógica para mostrar la página si es necesario
    };

    CourseDetail.quitarCursos = function() {
        $('#report_courses').val(null);
        this.reportCourses = "";
        if (this.datatable) {
            this.datatable.ajax.reload();
        }
    };

    CourseDetail.todosCursos = function() {
        var allCourses = [];
        $('#report_courses option').map(function() {
            allCourses.push(this.value);
        }).get();
        $('#report_courses').val(allCourses);
        this.reportCourses = allCourses.join(',');
        if (this.datatable) {
            this.datatable.ajax.reload();
        }
    };

    CourseDetail.selectCurso = function(courseid) {
        $('#report_courses').val([courseid]);
    };

    CourseDetail.descargarReporte = function() {
        var courseId = this.reportCourses || this.config.default_courses;
        var url = this.config.wwwroot + "/local/hoteles_city_dashboard/descargar_reporte.php?course=" + courseId;
        alert("Este proceso demora varios minutos, por lo tanto no abandones la sesión, ni vuelvas a dar clic en el botón hasta que se descargue el documento.");
        window.location.href = url;
        $("#exampleModal").modal('show');
    };

    // Función para redimensionar iframe (manteniendo compatibilidad)
    CourseDetail.iResize = function(frame_id) {
        if(this.intervals > 60){
            return;
        }
        this.intervals++;
        var element = document.getElementById(frame_id);
        if(element != null){
            if(element.contentWindow != null){
                if(element.contentWindow.document != null){
                    if(element.contentWindow.document.body != null){
                        if(element.contentWindow.document.body.offsetHeight != null){
                            var size = (element.contentWindow.document.body.offsetHeight + 500 ) + 'px';
                            document.getElementById(frame_id).style.height = "1500px";
                        }
                    }
                }
            }
        }
    };

    

    return CourseDetail;
});