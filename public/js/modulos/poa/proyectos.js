
/*=====================================

    # Nombre:
        proyectos.js

    # Módulo:
        poa/proyectos

    # Descripción:
        Se utiliza para crear, editar y eliminar caratulas de captura para proyectos de inversión e institucionales

=====================================*/

// Inicialización General para casi cualquier módulo
var moduloResource = new RESTfulRequests(SERVER_HOST+'/v1/proyectos');
var moduloDatagrid = new Datagrid("#datagridCaratulas",moduloResource);
moduloDatagrid.init();
moduloDatagrid.actualizar();
var modal_name = '#modalCaratulas';
var form_name = '#form_caratula';
/*===================================*/
// Implementación personalizada del módulo
function editar (e){
    $(modal_name).find(".modal-title").html("Editar");
    $(modal_name).modal('show');
    /*moduloResource.get(e,null,{
        _success: function(response){
            $(modal_name).find(".modal-title").html("Editar");
            $(modal_name).modal('show');  
        }
    });*/
}

$('#clasificacion_proyecto').on('change',function(){
    if($(this).val() == 2){
        $('#orden_fibap label').removeClass('active');
        $('#fibap_despues').prop('checked',true);
        $('#fibap_despues').parent().addClass('active');
        $('#opciones_fibap').removeClass('hidden');
    }else{
        $('#opciones_fibap').addClass('hidden');
    }
});

/*===================================*/
// Configuración General para cualquier módulo

$(modal_name).on('shown.bs.modal', function () {
    $(modal_name).find('input').eq(0).focus();
});

$(modal_name).on('hide.bs.modal',function(e){ 
    resetModalModuloForm();
});

$('.btn-datagrid-agregar').on('click', function () {
    $(modal_name).find(".modal-title").html("Nuevo Proyecto");
    $(modal_name).modal('show');
});

$(modal_name+' .btn-guardar-continuar').on('click', function (e) {
    e.preventDefault();
    submitModulo(true);
});

$(modal_name+' .btn-guardar').on('click', function (e) {
    e.preventDefault();
    submitModulo();
});

function resetModalModuloForm(){
    $(form_name).get(0).reset();
    $('#opciones_fibap').addClass('hidden');
    $('#orden_fibap label').removeClass('active');
    $('#fibap_despues').parent().addClass('active');
    $(form_name +' #id').val("");
}

function submitModulo(save_next){
    if($('#clasificacion_proyecto').val() == 2){
        if($('#fibap_antes').prop('checked')){
            //TODO: Load FIBAP form -->
            return;
        }
    }
    $(form_name).attr('action',SERVER_HOST+'/poa/caratula');
    $(form_name).attr('method','POST');
    $(form_name).submit();
}