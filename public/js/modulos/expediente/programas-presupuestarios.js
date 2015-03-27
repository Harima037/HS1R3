/*=====================================

    # Nombre:
        programas-presupuestarios.js

    # Módulo:
        expediente/programas-presupuestarios

    # Descripción:
        Se utiliza para crear, editar y eliminar los indicadores de los programas presupuestarios

=====================================*/

// Inicialización General para casi cualquier módulo
var moduloResource = new RESTfulRequests(SERVER_HOST+'/v1/programas-presupuestarios');
var moduloDatagrid = new Datagrid("#datagridProgramas",moduloResource);
//moduloDatagrid.init();
//moduloDatagrid.actualizar();
var modal_name = '#modal_programa_datos';
var form_name = '#form_programa_datos';

$('.btn-datagrid-agregar').on('click', function () {
    window.location.href = SERVER_HOST+'/expediente/editar-programa';
});
