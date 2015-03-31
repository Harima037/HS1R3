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
var modal_indicador = '#modal_programa_indicador';
var modal_problema = '#modal_problema';
var modal_objetivo = '#modal_objetivo';
var form_indicador = '#form_programa';

$('.chosen-one').chosen({width:'100%'});

/**
    Funciones de modales
**/
$('#datagridIndicadores .btn-datagrid-agregar').on('click', function () {
    $(modal_indicador).find(".modal-title").html("Nuevo Indicador");
    $(modal_indicador).modal('show');
});

$(modal_indicador).on('hide.bs.modal',function(e){
    reset_modal_form(form_indicador);
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

function reset_modal_form(formulario){
    $(formulario).get(0).reset();
    $(formulario + ' input[type="hidden"]').val('');
    Validation.cleanFormErrors(formulario);
    $(formulario + ' .chosen-one').trigger('chosen:updated');

    if(formulario == form_indicador){
        $('#tipo-indicador').val('');
        $('#tipo-indicador').trigger('chosen:updated');
    }
}