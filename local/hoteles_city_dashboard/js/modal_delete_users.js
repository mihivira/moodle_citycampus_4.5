YUI().use('node-base', function(Y) {
    function init() {
        Y.all("#id_deleteusers").on('click', function(e) {
            var args = {'url':e.currentTarget.get('href'),
                'message':'¿Está seguro de borrar 50 usuarios que no han ingresado antes de la fecha seleccionada y' + 
                ' que tienen el estatus de suspendido? Todos los datos almacenados relacionados se perderán.'};
            M.util.show_confirm_dialog(e, args);
            return false;
        });
    }

 Y.on("domready", init);


});