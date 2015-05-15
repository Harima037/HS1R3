/*=====================================

    # Nombre:
        configurar-seguimiento-metas.js

    # Módulo:
        administrador/configurar-seguimiento-metas

    # Descripción:
        Se utiliza para editar la configuracion del avance de metas.

=====================================*/

var moduleResource = new RESTfulRequests(SERVER_HOST+'/v1/config-seg-metas');

$('#btn-guardar-configuracion').on('click', function (e) {
    e.preventDefault();
    Validation.cleanFormErrors("#form_configuracion");
    MessageManager.dismissAlert('body');

    var parametros = $("#form_configuracion").serialize();

    moduleResource.post(parametros,{
        _success: function(response){
            MessageManager.show({data:'Datos almacenados con éxito',type:'OK',timer:4});
        },
        _error: function(response){
            try{
                var json = $.parseJSON(response.responseText);
                if(!json.code)
                    MessageManager.show({code:'S03',data:"Hubo un problema al realizar la transacción, inténtelo de nuevo o contacte con soporte técnico."});
                else{
                    MessageManager.show(json);
                }
                Validation.formValidate(json.data);
            }catch(e){
                console.log(e);
            }                       
        }
    });
});