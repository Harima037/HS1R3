/*=====================================

    # Nombre:
        proyectos.js

    # Módulo:
        poa/cproyectos

    # Descripción:
        Se utiliza para crear, editar y eliminar caratulas de captura para proyectos de inversión e institucionales

=====================================*/

// Inicialización General para casi cualquier módulo
var moduloResource = new RESTfulRequests(SERVER_HOST+'/v1/proyectos');
var moduloDatagrid = new Datagrid("#datagridCaratulas",moduloResource);
moduloDatagrid.init();
moduloDatagrid.actualizar();
var modal_name = '#modalCaratulas';
var form_name = '#formCaratula';
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

/*===================================*/
// Configuración General para cualquier módulo

$(modal_name).on('shown.bs.modal', function () {
    $(modal_name).find('input').eq(0).focus();
});

$(modal_name).on('hide.bs.modal',function(e){ 
    resetModalModuloForm();
});

$('.btn-datagrid-agregar').on('click', function () {
    $(modal_name).find(".modal-title").html("Nuevo");
    $(modal_name).modal('show');
});

$(modal_name+' .btn-guardar-continuar').on('click', function (e) {
    e.preventDefault();
    submitModulo(true);
});

$(modal_name+' .btn-guardar-cerrar').on('click', function (e) {
    e.preventDefault();
    submitModulo();
});

$(form_name).on('submit',function(e){
    e.preventDefault();
    submitModulo();
});

function resetModalModuloForm(){
    $(form_name).get(0).reset();
    $(form_name +' #id').val("");
    moduloDatagrid.cleanFormErrors(form_name);
}

function submitModulo(save_next){
    moduloDatagrid.cleanFormErrors(form_name);

    var parametros = $(form_name).serialize();
    if($(form_name +' #id').val()==""){
        moduloResource.post(parametros,{
                        _success: function(response){
                            moduloDatagrid.actualizar();
                            if(!save_next){
                                $(modal_name).modal('hide');
                            }
                            MessageManager.show({data:'Elemento creado con éxito',timer:5});
                        }
        },'Guardando');
    }else{
        moduloResource.put($(form_name +' #id').val(), parametros,{
                        _success: function(response){
                            moduloDatagrid.actualizar();
                            if(!save_next){
                                $(modal_name).modal('hide');
                            }
                            MessageManager.show({data:'Elemento actualizado con éxito',timer:5});
                        }
        },'Guardando');
    }
}