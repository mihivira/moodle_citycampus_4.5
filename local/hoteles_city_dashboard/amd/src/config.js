define([], function() {
    window.requirejs.config({
        paths: {
            "jspdf": M.cfg.wwwroot + '/local/hoteles_city_dashboard/js/jspdf/jspdf.umd.min',
            "html2canvas": M.cfg.wwwroot + '/local/hoteles_city_dashboard/js/html2canvas/html2canvas.min',
            "autotable": M.cfg.wwwroot + '/local/hoteles_city_dashboard/js/jspdf/autotable',
            "xlsx": M.cfg.wwwroot + '/local/hoteles_city_dashboard/js/sheetsjs/xlsx.full.min',
        },
        shim: {
            'jspdf': {exports: 'jsPDF'},
            'html2canvas': {exports: 'html2canvas'},
            'autotable': {exports: 'autotable'},
            'xlsx': {exports: 'xlsx'},

        }
    });
});