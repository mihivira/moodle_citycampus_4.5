define([
    'jquery',
    'core/chart_builder',
    'core/chart_output_chartjs',
    'core/ajax',
    'local_hoteles_city_dashboard/jspdf',

], function($, Builder, Output, ajax, jspdf) {

    var getData = function(uniqid, regions, type) {
      ajax.call([{
         methodname: 'local_hoteles_city_dashboard_reports_uo',
         args: {
           data: {
               'regions': regions,
               'type': type,
           },
         },
      }])[0]
      .done(function(response) {

        var data = JSON.parse(response);

        window.console.log("data:  ", data);

        var table = document.getElementById("tbody");
        var thead = document.getElementById("hdata");
        document.getElementById("tittable").innerHTML = data.graph.title;
        if (data.type == "users-download") {
          document.getElementById("cont-table").className = "col-12";
          document.getElementById("cont-graph").className = "d-none";
          // Obtener el thead donde se agregarán los encabezados
          thead.innerHTML = "";
          // Crear una fila de encabezado <tr>
          var filaEncabezado = document.createElement("tr");


        } else {
          document.getElementById("cont-table").className = "col-12";
          document.getElementById("cont-graph").className = "col-12";
          // Obtener el thead donde se agregarán los encabezados
          thead.innerHTML = "";
          // Crear una fila de encabezado <tr>
          var filaEncabezado = document.createElement("tr");

          var heads = ['Nombre', '% Completado', '% No completado'];

          heads.forEach(function(head) {
            var celdaEncabezado = document.createElement("th");
            celdaEncabezado.textContent = head;
            filaEncabezado.appendChild(celdaEncabezado);
          });

          // Agregar la fila de encabezado al thead
          thead.appendChild(filaEncabezado);

        }

        data.table.elements.forEach(function(element, index) {
          //window.console.log(element);

            if (data.type == "users-download") {
              window.console.log("users-download");
              var fila = document.createElement("tr");
              // Iterar sobre los datos y crear celdas en la fila
              if (index < 1) {
                for (var head in element) {
                  var celdaEncabezado = document.createElement("th");
                  celdaEncabezado.textContent = head;
                  filaEncabezado.appendChild(celdaEncabezado);
                }
              }
              // Agregar la fila de encabezado al thead
              thead.appendChild(filaEncabezado);

              for (var campo in element) {

                var celda = document.createElement("td");
                celda.textContent = element[campo];
                fila.appendChild(celda);
                table.appendChild(fila);
              }
            } else {
              window.console.log("table");
              var row = table.insertRow(index);

              // Insert new cells (<td> elements) at the 1st and 2nd position of the "new" <tr> element:
              var cell1 = row.insertCell(0);
              var cell2 = row.insertCell(1);
              var cell3 = row.insertCell(2);


              // Add some text to the new cells:
              if (element.type === 'courses') {
                cell1.innerHTML = '<a class="link-uo" href="#" data-item="' + element.id + '">' + element.name + '</a>';
              } else if (element.type === 'regions-cert') {
                cell1.innerHTML = '<a class="link-uo" href="#" data-item="' + element.name + "," + element.course
                + '" data-type="' + element.type + '">'
                + element.name + '</a>';
              } else if(element.type === 'uos'){
                cell1.innerHTML = element.name;
              } else {
                cell1.innerHTML = '<a class="link-uo" href="#" data-item="' + element.name + '">' + element.name + '</a>';
              }
              cell2.innerHTML = element.percentage;
              
              if(element.percentage > 0){
                cell3.innerHTML = (100 - Number(element.percentage)).toFixed(2);
              } else {
                cell3.innerHTML = "N/A";
                cell2.innerHTML = "N/A";
              }
              
            }
        });

        var tablaLlenaEvent = new Event('tablaLlena', {bubbles: true, cancelable: true});
        document.getElementById('tdata').dispatchEvent(tablaLlenaEvent);

        var chartArea = $('#chart-area-' + uniqid);
        var chartImage = chartArea.find('.chart-image');

        Builder.make(data.graph)
        .then(function(ChartInst) {
          new Output(chartImage, ChartInst);
          return;
        })
        .fail(function(err) {
          window.console.log(err);
            return;
        });

      });
    };

    return {

      init: function(uniqid, regions, type) {
        getData(uniqid, regions, type);

      },
    };
});