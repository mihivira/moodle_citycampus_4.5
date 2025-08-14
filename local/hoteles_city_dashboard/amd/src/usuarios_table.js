define(['core/modal_factory','core/modal_events', 'core/modal_save_cancel'], function(ModalFactory, ModalEvents, ModalSaveCancel) {

    return {
        init: function(cont, requestType, printedRows, linkFields, containerId) {

            console.log('Initializing usuarios_table.js with parameters:', cont, requestType, printedRows, linkFields, containerId);

            linkFields = linkFields.split(',');
            printedRows = printedRows.split(',');
            console.log('linkFields:', linkFields);
            console.log('printedRows:', printedRows);
            const container = document.getElementById(containerId);
            if (!container) {
                console.error('Config container not found:', containerId);
                return;
            }

            
            let columns;
            try {
                columns = JSON.parse(container.dataset.columns);
            } catch (e) {
                console.error('Failed to parse columns JSON:', e);
                return;
            }
            console.log('columns2', columns);

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
                }
            });

          
            const table = $('#empTable').DataTable({
                processing: true,
                serverSide: true,
                serverMethod: 'post',
                ajax: {
                    url: 'services.php',
                    data: function(d) {
                        d.request_type = requestType;
                    }
                },
                lengthMenu: [[10, 15, 20, 100, -1], [10, 15, 20, 100, "Todos los registros"]],
                dom: 'Bfrtip',
                pageLength: 10,
                buttons: [
                    {
                        extend: 'excelHtml5',
                        text: '<i class="fa fa-file-excel-o"></i> Exportar a Excel',
                        exportOptions: {
                            modifier: { search: 'applied', order: 'applied' },
                            columns: printedRows
                        },
                        className: 'btn btn-outline-secondary'
                    },
                    {
                        extend: 'csvHtml5',
                        text: '<i class="fa fa-file-o"></i> Exportar a CSV',
                        exportOptions: {
                            modifier: { search: 'applied', order: 'applied' },
                            columns: printedRows
                        },
                        className: 'btn btn-outline-secondary'
                    },
                    {
                        extend: 'pageLength',
                        className: 'btn btn-outline-secondary'
                    }
                ],
                columns: columns,
                columnDefs: [
                    { targets: linkFields, orderable: false }
                ],
                language: {
                    url: "datatables/Spanish.json",
                    emptyTable: "No se encontró información",
                    loadingRecords: "Cargando...",
                    processing: "Procesando...",
                    search: "Búsqueda:",
                    paginate: {
                        first: "Primera",
                        last: "Última",
                        next: "Siguiente",
                        previous: "Anterior"
                    },
                    buttons: {
                        pageLength: {
                            _: "Mostrando %d filas",
                            '-1': "Mostrando todas las filas"
                        }
                    }
                },
                drawCallback: function() {
                    $('.confirm-action').off('click').on('click', function(e) {
                        e.preventDefault();

                        modSusp($(this).data('id'), $(this).data('texto'));
                    });
                },
                initComplete: function() {
                    // Aquí puedes agregar cualquier código que necesites ejecutar una vez que la tabla esté completamente inicializada.
                    console.log('DataTable initialized successfully.');
                }
            });

            // Function to handle suspension or activation of users
            modSusp = async function(id, texto) {
                 const modal = await ModalSaveCancel.create({
                    title: 'Confirmar acción',
                    body: `¿Estás seguro de que deseas ${texto} este usuario?`,
                });

                modal.getRoot().on(ModalEvents.save, (e) => {
                    e.preventDefault();
                    // Aquí puedes agregar la lógica para suspender o activar al usuario.
                    console.log(`User ${id} has been ${texto.toLowerCase()}.`);
                    // ir 
                    window.location.href = `administrar_usuarios.php?suspenduser=1&id=${id}`;
                    
                })

                modal.getRoot().on(ModalEvents.cancel, (e) => {
                    e.preventDefault();
                    modal.hide();
                });
                modal.show();
                
            }
            
        },

    };
});


    
