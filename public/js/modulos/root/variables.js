/*=====================================

    # Nombre:
        variables.js

    # Módulo:
        root/variables

    # Descripción:
        Se utiliza para crear, editar y eliminar variables del sistema

=====================================*/

// Inicialización General para casi cualquier módulo

var moduleResource = new RESTfulRequests(SERVER_HOST+'/v1/admin-variables');
var moduleDatagrid = new Datagrid("#datagridVariables",moduleResource);
moduleDatagrid.init();
moduleDatagrid.actualizar();

/*===================================*/
// Implementación personalizada del módulo

function editar (e){
    $('#modalVariable').modal('show');
    moduleResource.get(e,null,{
        _success: function(response){
            $('#form_variable #id').val(response.data.id);
            $('#form_variable #variable').val(response.data.variable);
            $('#form_variable #valor').val(response.data.valor);
            $('#modalVariable').find(".modal-title").html("Editar Variable");
            $('#modalVariable').modal('show');
        }
    }); 
}

/*===================================*/
// Configuración General para cualquier módulo

$('#modalVariable').on('shown.bs.modal', function () {
    $('#modalVariable').find('input').eq(0).focus();
});

$('#modalVariable').on('hide.bs.modal', function () {
    Validation.cleanFormErrors("#form_variable");
    resetmodalVariableForm();
});

$('#btnVariableAgregar').on('click', function () {
    $('#modalVariable').find(".modal-title").html("Nueva Variable");    
    $('#modalVariable').modal('show');
});

$('#modalVariable .btn-guardar').on('click', function (e) {
    e.preventDefault();
    submitModulo();
});

function submitModulo(){
    Validation.cleanFormErrors("#form_variable");
    MessageManager.dismissAlert('body');

    var parametros = $("#form_variable").serialize();
    if($('#form_variable #id').val()==""){
        moduleResource.post(parametros,{
            _success: function(response){
                moduleDatagrid.actualizar();
                MessageManager.show({data:'Variable creada con éxito',container:'#modalVariable .modal-body',type:'OK',timer:4});
                $('#form_variable #id').val(response.data.id);
            },
            _error: function(response){
                try{
                    var json = $.parseJSON(response.responseText);
                    if(!json.code)
                        MessageManager.show({code:'S03',data:"Hubo un problema al realizar la transacción, inténtelo de nuevo o contacte con soporte técnico."});
                    else{
                        json.container = '#modalVariable .modal-body';
                        MessageManager.show(json);
                    }
                    Validation.formValidate(json.data);
                }catch(e){
                    console.log(e);
                }                       
            }
        });
    }else{
        moduleResource.put($('#form_variable #id').val(), parametros,{
            _success: function(response){
                moduleDatagrid.actualizar();
                MessageManager.show({data:'Variable actualizada con éxito',container:'#modalVariable .modal-body',type:'OK',timer:4});
            },
            _error: function(response){
                try{
                    var json = $.parseJSON(response.responseText);
                    if(!json.code)
                        MessageManager.show({code:'S03',data:"Hubo un problema al realizar la transacción, inténtelo de nuevo o contacte con soporte técnico."});
                    else{
                        json.container = '#modalVariable .modal-body';
                        MessageManager.show(json);
                    }
                    Validation.formValidate(json.data);
                }catch(e){
                    console.log(e);
                }                       
            }
        });
    }
}
/*===================================*/
// Funciones adicionales por módulo

function resetmodalVariableForm(){
    $('#form_variable').get(0).reset();
    $('#form_variable #id').val("");
}