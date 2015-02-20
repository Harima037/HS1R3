
/*=====================================

    # Nombre:
        inversion.js

    # Módulo:
        expediente/inversion

    # Descripción:
        Se utiliza para crear, editar y eliminar caratulas de captura para proyectos de inversión

=====================================*/

// Inicialización General para casi cualquier módulo
var fibapResource = new RESTfulRequests(SERVER_HOST+'/v1/fibap');
var moduloResource = new RESTfulRequests(SERVER_HOST+'/v1/inversion');
var moduloDatagrid = new Datagrid("#datagridCaratulas",moduloResource);
moduloDatagrid.init();
moduloDatagrid.actualizar();
var modal_name = '#modalNuevoProyecto';
var form_name = '#form_proyecto';

/*===================================*/
// Configuración General para cualquier módulo

$(modal_name).on('shown.bs.modal', function () {
    $(modal_name).find('input').eq(0).focus();
});

$(modal_name).on('hide.bs.modal',function(e){ 
    resetModalModuloForm();
});

$('.btn-datagrid-agregar').on('click', function () {
    $(modal_name).find(".modal-title").html("Nuevo Proyecto de Inversión");
    $(modal_name).modal('show');
});

$('#btn-editar-proyecto').on('click',function(){
    window.location.href = SERVER_HOST+'/expediente/caratula-inversion/' + $('#btn-editar-proyecto').attr('data-id-proyecto');
});

$(modal_name+' .btn-guardar').on('click', function (e) {
    e.preventDefault();
    $(form_name).attr('action',SERVER_HOST+'/expediente/caratula-inversion');
    $(form_name).attr('method','POST');
    $(form_name).submit();
});

function cargar_datos_proyecto(e){
    $('#btn-editar-proyecto').attr('data-id-proyecto',e);
    $('#modalEditarProyecto').modal('show');
}

function resetModalModuloForm(){
    Validation.cleanFormErrors(form_name);
    $(form_name).get(0).reset();
    $(form_name +' #id').val("");
}