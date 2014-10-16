/*=====================================

    # Nombre:
        catalogo.js

    # Módulo:
        demo/catalogo

    # Descripción:
        Se utiliza para crear, editar y eliminar elementos del catalogo

=====================================*/

// Inicialización General para casi cualquier módulo
var moduloResource = new RESTfulRequests(SERVER_HOST+'/v1/catalogo');
var moduloDatagrid = new Datagrid("#datagridCatalogo",moduloResource);
moduloDatagrid.init();
moduloDatagrid.actualizar();
$('#modalCatalogo').on('hide.bs.modal',function(e){ resetModalModuloForm(); });
/*===================================*/
// Implementación personalizada del módulo
moduloDatagrid.initBootstrapValidator(
    '#formCatalogo',
    {
        descripcion: { validators:{notEmpty:{}} }
    }
);

function editar (e){

    $('#modalCatalogo').modal('show');
    moduloResource.get(e,null,{
                    _success: function(response){

                        $('#formCatalogo #id').val(response.data.id);
                        $('#formCatalogo #descripcion').val(response.data.descripcion);
                        
                        //console.log(response.data);
                        $('#modalCatalogo').find(".modal-title").html("Editar Elemento");
                        $('#modalCatalogo').modal('show');  
                    }
    },'Cargando los datos a editar.');
}

/*===================================*/
// Configuración General para cualquier módulo

$('#modalCatalogo').on('shown.bs.modal', function () {
    $('#modalCatalogo').find('input').eq(0).focus();
});

$('#btnCatalogoAgregar').on('click', function () {
    $('#modalCatalogo').find(".modal-title").html("Nuevo Elemento");
    $('#modalCatalogo').modal('show');
});

$('#modalCatalogo .btn-guardar').on('click', function (e) {
    e.preventDefault();
    submitModulo();
});

$("#formCatalogo").on('submit',function(e){
    e.preventDefault();
    submitModulo();
});

function resetModalModuloForm(){
    $('#formCatalogo').get(0).reset();
    $('#formCatalogo #id').val("");
    moduloDatagrid.cleanFormErrors('#formCatalogo',true);
}

function submitModulo(){
    if(!moduloDatagrid.isFormValid('#formCatalogo')){
        return false;
    }

    moduloDatagrid.cleanFormErrors('#formCatalogo');

    var parametros = $("#formCatalogo").serialize();
    if($('#formCatalogo #id').val()==""){
        moduloResource.post(parametros,{
                        _success: function(response){
                            moduloDatagrid.actualizar();
                            MessageManager.show({data:'Elemento creado con éxito'});
                        }
        },'Guardando');
    }else{
        moduloResource.put($('#formCatalogo #id').val(), parametros,{
                        _success: function(response){
                            moduloDatagrid.actualizar();
                            MessageManager.show({data:'Elemento actualizado con éxito'});
                        }
        },'Guardando');
    }
}