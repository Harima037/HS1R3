/*=====================================

    # Nombre:
        control-archivos-poblacion.js

    # Módulos:
        administrador/archivos-estadistica-poblacion

    # Descripción:
        Funciones para cargar archivos PDF a usar como referencia de estadisticas de poblacion en el apartado de Benerificarios de los proyectos

=====================================*/

var moduloResource = new RESTfulRequests(SERVER_HOST+'/v1/archivos-estadistica-poblacion');

cargarListadoArchivos();

function cargarListadoArchivos(){
    moduloResource.get(null,{},{
        _success: function(response){
            console.log(response);
            if(response.data.length > 0){
                var html_body = '';
                for(var i in response.data){
                    var archivo = response.data[i];
                    html_body += '<tr><td>'+archivo.titulo+'</td><td><a href="'+SERVER_HOST+'/ver-archivo/'+archivo.id+'" target="_blank">'+archivo.nombre+'</a></td><td align="center"><button type="button" class="btn btn-danger" onClick="eliminarArchivo('+archivo.id+')"><span class="fa fa-trash"></span></button></td></tr>';
                }
                $('#tabla-lista-archivos tbody').html(html_body);
            }else{
                $('#tabla-lista-archivos tbody').html('<tr><td colspan="3" style="text-align:center;">No se encontraron archivos</td></tr>')
            }
            //MessageManager.show({data:'Se guardaron correctamente los datos.',timer:5,type:'INF'});
        },
        _error: function( response ){
            try{
                var json = $.parseJSON(response.responseText);
                if(!json.code)
                    MessageManager.show({code:'S03',timer:10,data:"Hubo un problema al realizar la transacción, inténtelo de nuevo o contacte con soporte técnico."});
                else{
                    MessageManager.show(json);
                }
                Validation.formValidate(json.data);
            }catch(e){
                console.log(e);
            }
        }
    });
}

$('#btn-subir-archivo').on('click',function(){
    Validation.cleanFormErrors('#form_archivos');

    var cuantosArchivos = document.getElementById("archivo-poblacion").files.length;  
    if(cuantosArchivos > 0){
        subirArchivoConDatos();         
    }else{
        MessageManager.show({data:'Debe seleccionar un archivo a subir.',timer:10,type:'ERR'});
    }
});

function subirArchivoConDatos(){
    var data  = new FormData();
    
    var archivo = document.getElementById("archivo-poblacion").files;
    
    if(archivo[0] && archivo[0].size > 5242880) { // 5 MB (this size is in bytes)
        //Prevent default and display error 
        MessageManager.show({code:'S03',timer:10,data:"El archivo a subir no puede pesar mas de 5MB."});
        return false;
    }

    $("#loading").fadeIn();
    data.append('archivo-poblacion', archivo[0]); 
    data.append('titulo', $('#titulo').val());
    
    $.ajax({
        url: SERVER_HOST+'/v1/archivos-estadistica-poblacion', //Url a donde la enviaremos
        type:'POST', //Metodo que usaremos
        dataType:'json',
        contentType:false, //Debe estar en false para que pase el objeto sin procesar
        data:data, //Le pasamos el objeto que creamos con los archivos
        processData:false, //Debe estar en false para que JQuery no procese los datos a enviar
        cache:false, //Para que el formulario no guarde cache,
        success: function(response){ 
            $("#loading").fadeOut();            
            MessageManager.show({data:'Archivo subido correctamente.',timer:5,type:'INF'});
            $("#form_archivos").trigger('reset');
            cargarListadoArchivos();
        },
        error: function( response ){
            $("#loading").fadeOut(function(){ 
                try{
                    var json = $.parseJSON(response.responseText);
                    if(!json.code)
                        MessageManager.show({code:'S03',timer:10,data:"Hubo un problema al realizar la transacción, inténtelo de nuevo o contacte con soporte técnico."});
                    else{
                        MessageManager.show(json);
                    }
                    Validation.formValidate(json.data);
                }catch(e){
                    console.log(e);
                }
            });
        }   
    });
}

function eliminarArchivo(id){
    Confirm.show({
        titulo:"Eliminar Archivo",
        //botones:[], 
        mensaje: "¿Estás seguro que deseas eliminar el archivo seleccionado?",
        //si: 'Actualizar',
        //no: 'No, gracias',
        callback: function(){
            moduloResource.delete(id,{},{
                _success: function(response){
                    cargarListadoArchivos();
                    MessageManager.show({data:'Archivo eliminado con éxito.',timer:5,type:'INF'});
                },
                _error: function( response ){
                    try{
                        var json = $.parseJSON(response.responseText);
                        if(!json.code)
                            MessageManager.show({code:'S03',timer:10,data:"Hubo un problema al realizar la transacción, inténtelo de nuevo o contacte con soporte técnico."});
                        else{
                            MessageManager.show(json);
                        }
                        Validation.formValidate(json.data);
                    }catch(e){
                        console.log(e);
                    }
                }
            });
        }
    });    
}