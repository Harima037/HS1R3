
/*=====================================

    # Nombre:
        fibap.js

    # Módulo:
        poa/fibap

    # Descripción:
        Para el formulario de captura de la Ficha de Información Básica del Proyecto de Inversión

=====================================*/
// Declaracion de variables
var moduloResource = new RESTfulRequests(SERVER_HOST+'/v1/fibap');
var moduloDatagrid = new Datagrid("#datagridFibaps",moduloResource);
moduloDatagrid.init();
moduloDatagrid.actualizar();
var modal_name = '#modal-fibap';
var form_name = '#form-fibap';

/*===================================*/
// Configuración General para cualquier módulo

$(modal_name).on('shown.bs.modal', function () {
    $(modal_name).find('input').eq(0).focus();
});

$(modal_name).on('hide.bs.modal',function(e){ 
    resetModalModuloForm();
});

$('.btn-datagrid-agregar').on('click', function () {
    $(modal_name).find(".modal-title").html("Nuevo FIBAP");
    $(modal_name).modal('show');
});

$(modal_name+' .btn-guardar').on('click', function (e) {
    e.preventDefault();
    submitModulo();
});

function resetModalModuloForm(){
    //Validation.cleanFormErrors(form_name);
    $(form_name).get(0).reset();
    //$(form_name +' #id').val("");
}

function submitModulo(save_next){
    $(form_name).attr('action',SERVER_HOST+'/poa/formulario-fibap');
    $(form_name).attr('method','POST');
    $(form_name).submit();
}