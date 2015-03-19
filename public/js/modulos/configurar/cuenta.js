/*=====================================

    # Nombre:
        cuenta.js

    # Módulo:
        configurar/cuenta

    # Descripción:
        Se utiliza para configurar la cuenta del usuario, cambiar contraseña, actualizar datos, etc

=====================================*/

// Inicialización General para casi cualquier módulo
var moduloResource = new RESTfulRequests(SERVER_HOST+'/v1/cuenta');

$('#btnCambiarMes').on('click', function (e){
    e.preventDefault();
    var parametros = $('#formCambiarMes').serialize();
    parametros += '&formulario=mes';
    moduloResource.put($('#id').val(), parametros,{
                    _success: function(response){
                        $('#formCambiarMes').append('<div class="alert alert-info">Mes actualizado</div>');
                    }
    });
});

$('#btnGuardarContrasena').on('click', function (e) {
	e.preventDefault();
    var parametros = $('#formCambiarPass').serialize();
    moduloResource.put($('#formCambiarPass #id').val(), parametros,{
                    _success: function(response){
                    	$('#formCambiarPass').empty();
                    	$('#formCambiarPass').append('<div class="alert alert-info">Contraseña actualizada</div>');
                    }
    });
});