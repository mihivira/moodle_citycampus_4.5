/**
 * Módulo AMD para el dashboard de reportes por UO (sin jQuery).
 *
 * @module local_hoteles_city_dashboard/reportes_uo_main
 * @copyright  2025
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define([
    'core/log',
    'local_hoteles_city_dashboard/xlsx',
    'local_hoteles_city_dashboard/jspdf',
    'local_hoteles_city_dashboard/html2canvas',
    'local_hoteles_city_dashboard/autotable',
    'local_hoteles_city_dashboard/config',
    'local_hoteles_city_dashboard/reportes_uo',
], function(Log, XLSX, jsPDF, html2canvas, autoTable, unused, ReportesUo) {

    // Estado interno
    let tab = 'avance';
    let customHistory = [];
    let uniqid = null;

    /**
     * Inicializa el módulo.
     *
     * @param {string} uniqueId
     * @param {string} regions
     * @param {string} type
     */
    function init(uniqueId, regions, type) {
        uniqid = uniqueId;
        window.console.log('[ReportesUO] Inicializando con:', { uniqid, regions, type });

        // Esperar a que el DOM esté listo
       // if (document.readyState === 'loading') {
         //   document.addEventListener('DOMContentLoaded', setupEventListeners);
        //} else {
        setupEventListeners(uniqid);
        //}

        // Cargar contenido inicial
        loadGraphics(uniqid,regions, type);
    }

    /**
     * Configura todos los eventos del DOM.
     */
    function setupEventListeners(uniqid) {
        window.console.log('[ReportesUO] Configurando eventos (sin jQuery)');

        // === 1. Botones de exportación ===
        const exportPdfBtn = document.getElementById('exportpdf');
        const exportExcelBtn = document.getElementById('exportexcel');
        const returnBtn = document.getElementById('return');

        if (exportPdfBtn) {
            exportPdfBtn.addEventListener('click', () => {
                window.console.log('Exportar a PDF');
                const titleReport = document.getElementById('tittable')?.textContent.trim() || 'Reporte';
                exportPdf(titleReport);
            });
        }

        if (exportExcelBtn) {
            exportExcelBtn.addEventListener('click', () => {
                window.console.log('Exportar a Excel');
                const titleReport = document.getElementById('tittable')?.textContent.trim() || 'Reporte';
                exportExcel('tdata', titleReport);
            });
        }

        if (returnBtn) {
            returnBtn.addEventListener('click', () => {
                window.console.log('Regresar en historial');
                if (customHistory.length > 1) {
                    customHistory.pop(); // actual
                    const lastRequest = customHistory.pop();
                    loadGraphics(lastRequest.regions, lastRequest.type);
                } else {
                    returnBtn.style.display = 'none';
                }
            });
        }

        // === 2. Pestañas (Bootstrap 4 usa eventos nativos) ===
        document.querySelectorAll('a[data-toggle="tab"]').forEach(tabEl => {
            window.console.log('Pestaña encontrada:', tabEl.id);
            tabEl.addEventListener('click', (e) => {
                const tabId = e.target.id;
                window.console.log('Pestaña activada:', tabId);

                switch (tabId) {
                    case 'cert-tab':
                        tab = 'certificaciones';
                        loadGraphics(uniqid, '', 'regions-' + tab);
                        break;

                    case 'user-tab':
                        tab = 'users';
                        const tbody = document.getElementById('tbody');
                        const chartCont = document.getElementById('chart-cont');
                        if (tbody) tbody.innerHTML = '';
                        if (chartCont) chartCont.innerHTML = '';
                        loadGraphics(uniqid, '', 'usuarios-certificaciones');
                        break;

                    case 'home-tab':
                        tab = 'avance';
                        loadGraphics(uniqid, '', 'regions-' + tab);
                        break;

                    default:
                        window.console.log('Pestaña no manejada:', tabId);
                }
            });
        });

        // === 3. Evento personalizado: tablaLlena ===
        const tdata = document.getElementById('tdata');
        if (tdata) {
            tdata.addEventListener('tablaLlena', () => {
                window.console.log('Evento tablaLlena recibido');
                hideLoading();

                const tabContent = document.getElementById('myTabContent');
                if (!tabContent) return;

                if (tab === 'users') {
                    const titleReport = document.getElementById('tittable')?.textContent.trim() || 'Reporte';
                    exportExcel('tdata', titleReport);
                    tabContent.style.display = 'none';
                } else {
                    tabContent.style.display = 'block';
                }

                // Volver a asignar eventos a enlaces .link-uo (por si se regeneran)
                setupLinkUoEvents();
            });
        }
    }

    /**
     * Asigna eventos a los enlaces de UO.
     */
    function setupLinkUoEvents() {
        document.querySelectorAll('.link-uo').forEach(link => {
            link.addEventListener('click', function() {
                const item = this.dataset.item;
                const type = this.dataset.type === 'regions-cert'
                    ? 'uos2-' + tab
                    : 'uos-' + tab;

                const returnBtn = document.getElementById('return');
                if (returnBtn) returnBtn.style.display = 'inline-block';

                loadGraphics(item, type);
            });
        });
    }

    /**
     * Carga gráficos y datos.
     *
     * @param {string} regions
     * @param {string} type
     */
    function loadGraphics(uniqid, regions, type) {
        window.console.log('Cargando gráficos:', { regions, type });
        showLoading();

        ReportesUo.init(uniqid, regions, type);

        customHistory.push({ regions, type });
        cleanPage();

    }

    /**
     * Muestra el overlay de carga.
     */
    function showLoading() {
        const overlay = document.getElementById('overlay');
        if (overlay) overlay.style.display = 'flex';
    }

    /**
     * Oculta el overlay de carga.
     */
    function hideLoading() {
        const overlay = document.getElementById('overlay');
        if (overlay) overlay.style.display = 'none';
    }

    function cleanPage() {
        const tbody = document.getElementById('tbody');
        const chartCont = document.getElementById('chart-cont');
        if (tbody) tbody.innerHTML = '';
        if (chartCont) chartCont.innerHTML = '';
    }

    /**
     * Exporta tabla a Excel.
     *
     * @param {string} tableId
     * @param {string} titleReport
     */
    function exportExcel(tableId, titleReport) {
        const table = document.getElementById(tableId);
        if (!table) return;

        const wb = XLSX.utils.table_to_book(table, { sheet: 'Sheet1' });
        const date = new Date();
        const filename = `${titleReport}_${date.getFullYear()}-${date.getMonth()+1}-${date.getDate()}.xlsx`;
        XLSX.writeFile(wb, filename);
    }

    /**
     * Exporta tabla y gráfico a PDF.
     *
     * @param {string} titleReport
     */
    function exportPdf(titleReport) {
         
           
        const table = document.getElementById('tdata');
        if (!table) return;

        const headers = Array.from(table.querySelectorAll('thead th, thead td'))
            .map(th => th.textContent.trim());

        const rows = Array.from(table.querySelectorAll('tbody tr'))
            .map(tr => Array.from(tr.querySelectorAll('td')).map(td => td.textContent.trim()));
        
        var pdf = new window.jspdf.jsPDF({
            orientation: 'landscape'
        });
        
        

        let marginLeft = 15;
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
            didDrawPage: (data) => {
                marginLeft = data.settings.margin.left;
                pdf.setFontSize(12);
                pdf.text(titleReport, marginLeft, 10);
            }
        });

        // Agregar gráfico (canvas)
        pdf.addPage();
        pdf.setFontSize(12);
        pdf.text(titleReport, marginLeft, 10);

        /* const canvas = document.getElementById('chart-cont');
        if (canvas) {
            const imgData = canvas.toDataURL('image/png');
            pdf.addImage(imgData, 'PNG', 15, 15, 250, 125);
        }

        const date = new Date();
        const filename = `${titleReport}_${date.getFullYear()}-${date.getMonth()+1}-${date.getDate()}.pdf`;
        pdf.save(filename); */


        // === 3. Buscar el canvas de Chart.js ===
        const chartContainer = document.getElementById('chart-cont');
        if (!chartContainer) {
            console.warn('No se encontró #chart-cont');
            pdf.save(`${titleReport}.pdf`);
            return;
        }

        const canvas = chartContainer.querySelector('canvas');
        if (!canvas) {
            console.warn('No se encontró el <canvas> dentro de #chart-cont');
            pdf.save(`${titleReport}.pdf`);
            return;
        }

        // === 4. Esperar a que el canvas tenga dimensiones válidas ===
        if (canvas.width === 0 || canvas.height === 0) {
            console.warn('El canvas tiene tamaño 0x0. Esperando...');
            // Opcional: reintentar después de un breve retraso
            setTimeout(() => {
                exportPdf(titleReport); // Reintentar (uso cuidadoso)
            }, 500);
            return;
        }

        // === 5. Usar html2canvas===
        window.html2canvas(canvas, {
            scale: 2, // Mejor calidad
            useCORS: true,
            logging: false,
            backgroundColor: '#ffffff'
        }).then(function(imgData) {
            // Calcular proporciones
            const imgWidth = 250;
            const originalRatio = canvas.height / canvas.width;
            const imgHeight = imgWidth * originalRatio;

            // Añadir imagen al PDF
            pdf.addImage(imgData, 'PNG', 15, 15, imgWidth, imgHeight);

            // Guardar
            const date = new Date();
            const filename = `${titleReport}_${date.getFullYear()}-${date.getMonth()+1}-${date.getDate()}.pdf`;
            pdf.save(filename);
        }).catch(err => {
            console.error('Error al capturar canvas:', err);
            pdf.save(`${titleReport}.pdf`); // Guardar sin gráfica
        });
    }

    // --- Exportar funciones públicas ---
    return {
        init: init
    };
});