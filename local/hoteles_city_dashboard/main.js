var tab = 'avance';
var customHistory = [];
document.getElementById('exportpdf').addEventListener("click", function(e) {
  window.console.log(e.target.id);
  var titleReport = document.getElementById('tittable').innerHTML;
  exportPdf(titleReport);
});

document.getElementById('exportexcel').addEventListener("click", function(e) {
  window.console.log(e.target.id);
  var tabContent= document.getElementById('myTabContent');
  var titleReport = document.getElementById('tittable').innerHTML;
  exportExcel('tdata',titleReport);
});

document.getElementById('return').addEventListener("click", function(e) {
  window.console.log(e.target.id);
  if (customHistory.length > 0) {
    var currentRequest = customHistory.pop();
    var lastRequest = customHistory.pop();
    console.log("lastRequest: ",lastRequest)
     loadGraphics('{{uniqid}}',lastRequest.regions, lastRequest.type);
  }
  //loadGraphics('{{uniqid}}',history[index][0],history[index][1]);
});

loadGraphics('{{uniqid}}','','regions-avance');


// Agregar un 'escuchador de eventos' para el evento personalizado 'tablaLlena'
document.getElementById('tdata').addEventListener('tablaLlena', () => {
  console.log('La tabla se ha llenado con los datos');
  ocultarCargando();
  var tabContent= document.getElementById('myTabContent');
  if(tab == "users"){
    var titleReport = document.getElementById('tittable').innerHTML;
    exportExcel('tdata',titleReport);
    tabContent.style.display = 'none';
  } else {
    tabContent.style.display = 'block';
  }
  Array.prototype.forEach.call(document.getElementsByClassName("link-uo"), function(link) {
    link.addEventListener("click", function() {
      console.log(this.dataset.item);
      if(typeof this.dataset.type !== "undefined" && this.dataset.type === "regions-cert"){
        document.getElementById('return').style.display = 'inline-block';
        loadGraphics('{{uniqid}}', this.dataset.item, 'uos2-' + tab)
      } else {
        document.getElementById('return').style.display = 'inline-block';
        loadGraphics('{{uniqid}}', this.dataset.item, 'uos-' + tab)
      }
      
    });
  });
});


var date = new Date();
var current_date = date.getFullYear()+"-"+(date.getMonth()+1)+"-"+ date.getDate();

$('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
  e.target // newly activated tab
  e.relatedTarget // previous active tab
  console.log(e.target.id);
   
  switch (e.target.id){
    case 'cert-tab':
      tab = 'certificaciones';
      loadGraphics('{{uniqid}}','', 'regions-' + tab);
      
    break;
    case 'user-tab':
      tab = 'users'
      document.getElementById('tbody').innerHTML = "";
      document.getElementById("chart-cont").innerHTML = "";
      loadGraphics('{{uniqid}}','', 'usuarios-certificaciones');
    break;
    case 'home-tab':
      tab = 'avance';
      loadGraphics('{{uniqid}}','','regions-' + tab);
    break;
  }
  

})
 function mostrarCargando() {
    document.getElementById('overlay').style.display = 'flex';
  }

  // Función para ocultar el overlay y el mensaje de carga
  function ocultarCargando() {
    document.getElementById('overlay').style.display = 'none';
  }


function changeData(uniqid, regions, type) {
  document.getElementById('tbody').innerHTML = "";
  document.getElementById("chart-cont").innerHTML = "";
  getData(uniqid, regions, type);
}

function loadGraphics(uniqid, regions, type){
  console.log("Variable type loadGraphics: ",type);
  mostrarCargando();
  
  require(['local_hoteles_city_dashboard/reportes_uo', 'xlsx'], function(ReportesUo, XLSX){
    ReportesUo.init(uniqid, regions, type);
  });
  document.getElementById('tbody').innerHTML = "";
  document.getElementById("chart-cont").innerHTML = "";
  var request = { regions: regions, type: type };
  customHistory.push(request);
  console.log("customHistory: ",customHistory);
}

function exportExcel(tableid, titleReport){
  require(['xlsx'], function(XLSX){
    const table = document.getElementById(tableid);
      const wb = XLSX.utils.table_to_book(table, { sheet: 'Sheet1' });
      XLSX.writeFile(wb, titleReport + current_date + '.xlsx');
  });
}

function exportPdf(titleReport){
  require(["jspdf","html2canvas","autotable"], ({jsPDF, html2canvas, autotable}) => {
  

    if (window.jsPDF ) {
       var pdf = new jsPDF({
        orientation: "landscape",
      });
    } else {
      console.error("jsPDF no está disponible");
      return;
    }
   

    const table = document.getElementById('tdata');
    const headers = Array.from(table.querySelectorAll('th')).map(header => header.textContent.trim());
    const rows = Array.from(table.querySelectorAll('tbody tr')).map(row => Array.from(row.querySelectorAll('td')).map(cell => cell.textContent.trim()));

    //const tableWidth = pdf.internal.pageSize.width / 2;
    var marginLeft = '';
    pdf.autoTable({
      head: [headers],
      body: rows,
      startY: 20,
      theme: 'grid',
      margin: { top: 20, bottom: 10 },
      styles: {
        cellPadding: 3,
        fontSize: 10,
        valign: 'middle',
        halign: 'center'
      },
      columnStyles: {
        0: { fontStyle: 'bold' }
      },
      //tableWidth: tableWidth,
      didDrawPage: function (data) {
        marginLeft = data.settings.margin.left;
        pdf.setFontSize(12);
        pdf.text(titleReport, data.settings.margin.left, 10);
      }
    });

    pdf.addPage();
    pdf.setFontSize(12);
    pdf.text(titleReport, marginLeft, 10);
    const canvas = document.getElementsByClassName('chartjs-render-monitor');
    const dataURL = canvas[0].toDataURL("image/png", 1.0); // Convertir el canvas a una URL base64
    pdf.addImage(dataURL, 'PNG', 15, 15, 250, 125); // Insertar la imagen en el PDF
    pdf.save(titleReport + current_date + '.pdf'); // Guardar el archivo PDF
  });
}