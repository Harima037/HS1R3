/*=====================================

    # Nombre:
        modulo.js

    # Módulo:
        demo/modulo

    # Descripción:
        Se utiliza para crear, editar y eliminar elementos del modulo

=====================================*/

// Inicialización General para casi cualquier módulo
var moduloResource = new RESTfulRequests(SERVER_HOST+'/v1/modulo');
var moduloDatagrid = new Datagrid("#datagridModulo",moduloResource);
moduloDatagrid.init();
moduloDatagrid.actualizar();
$('#modalModulo').on('hide.bs.modal',function(e){ resetModalModuloForm(); });

moduloDatagrid.initBootstrapValidator(
    '#formModulo',
    {
        nombre: { validators:{notEmpty:{}} },
        catalogo: { validators:{notEmpty:{}} }
    }
);

/*===================================*/
// Implementación personalizada del módulo

function editar (e){

    $('#modalModulo').modal('show');
    moduloResource.get(e,null,{
                    _success: function(response){

                        $('#formModulo #id').val(response.data.id);
                        $('#formModulo #nombre').val(response.data.nombre);
                        $('#formModulo #datos').val(response.data.datos);
                        $('#formModulo #catalogo').val(response.data.idCatalogo);

                        //console.log(response.data);
                        $('#modalModulo').find(".modal-title").html("Editar Elemento");
                        $('#modalModulo').modal('show');  
                    }
    }); 
}

/*===================================*/
// Configuración General para cualquier módulo

$('#modalModulo').on('shown.bs.modal', function () {
    $('#modalModulo').find('input').eq(0).focus();
});

$('#btnModuloAgregar').on('click', function () {
    //resetModalModuloForm();
    $('#modalModulo').find(".modal-title").html("Nuevo Elemento");
    $('#modalModulo').modal('show');
});

$('#modalModulo .btn-guardar').on('click', function (e) {
    e.preventDefault();
    submitModulo();
});

$("#formModulo").on('submit',function(e){
    e.preventDefault();
    submitModulo();
});

function resetModalModuloForm(){
    $('#formModulo').get(0).reset();
    $('#formModulo #id').val("");
    moduloDatagrid.cleanFormErrors('#formModulo',true);
}

function submitModulo(){
    if(!moduloDatagrid.isFormValid('#formModulo')){
        return false;
    }

    moduloDatagrid.cleanFormErrors('#formModulo');

    var parametros = $("#formModulo").serialize();
    if($('#formModulo #id').val()==""){
        moduloResource.post(parametros,{
                        _success: function(response){
                            moduloDatagrid.actualizar();
                            MessageManager.show({data:'Elemento creado con éxito'});
                        }
        });
    }else{
        moduloResource.put($('#formModulo #id').val(), parametros,{
                        _success: function(response){
                            moduloDatagrid.actualizar();
                            MessageManager.show({data:'Elemento actualizado con éxito'});
                        }
        });
    }
}