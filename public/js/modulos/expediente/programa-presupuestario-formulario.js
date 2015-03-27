/*=====================================

    # Nombre:
        programa-presupuestario-formulario.js

    # Módulo:
        expediente/programa-presupuestario-captura

    # Descripción:
        Se utiliza para crear, editar y eliminar los indicadores de los programas presupuestarios

=====================================*/

// Inicialización General para casi cualquier módulo
var moduloResource = new RESTfulRequests(SERVER_HOST+'/v1/programas-presupuestarios');
var moduloDatagrid = new Datagrid("#datagridIndicadores",moduloResource);
//moduloDatagrid.init();
//moduloDatagrid.actualizar();
var modal_name = '#modal_programa_indicador';
var modal_problema = '#modal_problema';
var modal_objetivo = '#modal_objetivo';
var form_name = '#form_programa_indicador';

$('#datagridIndicadores .btn-datagrid-agregar').on('click', function () {
    $(modal_name).find(".modal-title").html("Nuevo Indicador");
    $(modal_name).modal('show');
});

$('#datagridProblemas .btn-datagrid-agregar').on('click', function () {
    $(modal_problema).find(".modal-title").html("Nueva Causa/Efecto");
    $(modal_problema).modal('show');
});

$('#datagridObjetivos .btn-datagrid-agregar').on('click', function () {
    $(modal_objetivo).find(".modal-title").html("Nuevo Medio/Fin");
    $(modal_objetivo).modal('show');
});

$('#btn-programa-cancelar').on('click',function(){
    window.location.href = SERVER_HOST+'/expediente/programas-presupuestarios';
});