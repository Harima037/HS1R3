/*=====================================

    # Nombre:
        modulos.js

    # Módulo:
        root/modulos

    # Descripción:
        Se utiliza para crear, editar y eliminar los modulos del sistema

=====================================*/

// Inicialización General para casi cualquier módulo

var moduleResource = new RESTfulRequests(SERVER_HOST+'/v1/admin-modulos');
var moduleDatagrid = new Datagrid("#datagridModulos",moduleResource);
moduleDatagrid.init();
moduleDatagrid.actualizar({
    _success: function(response){
        moduleDatagrid.limpiar();

        for(var i in response.data){

            response.data[i].grupo = '<span class="fa '+response.data[i].grupoIcono+'"></span> ' + response.data[i].grupo;
            response.data[i].modulo = '<span class="fa '+response.data[i].moduloIcono+'"></span> ' + response.data[i].modulo;

            if(response.data[i].visible){
                response.data[i].visible = '<span class="text-info fa fa-eye"></span>';
            }else{
                response.data[i].visible = '<span class="text-muted fa fa-eye-slash"></span>';
            }

            delete response.data[i].grupoIcono;
            delete response.data[i].moduloIcono;
        }
        moduleDatagrid.cargarDatos(response.data);
        moduleDatagrid.cargarTotalResultados(response.resultados,'<b>Modulo(s)</b>');
        var total = parseInt(response.resultados/moduleDatagrid.rxpag); 
        var plus = parseInt(response.resultados)%moduleDatagrid.rxpag;
        if(plus>0) 
            total++;
        moduleDatagrid.paginacion(total);
    },
    _error: function(jqXHR){
        //console.log('{ error => "'+jqXHR.status +' - '+ jqXHR.statusText +'", response => "'+ jqXHR.responseText +'" }'); 
        var json = $.parseJSON(jqXHR.responseText);
        if(json.code == "W00"){
            moduleDatagrid.limpiar();
            var colspan = $(moduleDatagrid.selector + " thead > tr th").length;
            $(moduleDatagrid.selector + " tbody").append("<tr><td colspan='"+colspan+"' style='text-align:left'><i class='fa fa-info-circle'></i> "+json.data+"</td></tr>");
            moduleDatagrid.cargarTotalResultados(0,'<b>Modulo(s)</b>');
        }else{
            json.type = 'ERR';
            MessageManager.show(json);
            moduleDatagrid.limpiar();
        }
        
    }
});

/*===================================*/
// Implementación personalizada del módulo

function editar (e){
    var parametros = {editar:'modulo'};
    moduleResource.get(e,parametros,{
        _success: function(response){
            $('#form_modulo #id').val(response.data.id);
            $('#form_modulo #grupo').val(response.data.idSysGrupoModulo);
            $('#form_modulo #permiso').val(response.data.idSysPermiso);
            $('#form_modulo #key').val(response.data.key);
            $('#form_modulo #nombre').val(response.data.nombre);
            $('#form_modulo #uri').val(response.data.uri);
            $('#form_modulo #icono').val(response.data.icono);
            $('#form_modulo #icono').change();
            if(response.data.visible){
                $('#visible').prop('checked',true);
            }
            $('#modalModulo').find(".modal-title").html("Editar Modulo");
            $('#modalModulo').modal('show');
        }
    }); 
}

$('#btn-editar-grupo').on('click',function (e){
    e.preventDefault();
    if($('#grupo').val()){
        var parametros = {editar:'grupo'};
        moduleResource.get($('#grupo').val(),parametros,{
            _success: function(response){
                $('#grupo-id').val(response.data.id);
                $('#grupo-key').val(response.data.key);
                $('#grupo-nombre').val(response.data.nombre);
                $('#grupo-uri').val(response.data.uri);
                $('#grupo-icono').val(response.data.icono);
                $('#grupo-icono').change();
                if(response.data.visible){
                    $('#grupo-visible').prop('checked',true);
                }
                $('#modalGrupoModulo').find(".modal-title").html("Editar Grupo");    
                $('#modalGrupoModulo').modal('show');
            }
        }); 
    }
});

/*===================================*/
// Configuración General para cualquier módulo

$('#modalModulo').on('shown.bs.modal', function () {
    $('#modalModulo').find('input').eq(0).focus();
});

$('#modalModulo').on('hide.bs.modal', function () {
    Validation.cleanFormErrors("#form_modulo");
    $('#form_modulo').get(0).reset();
    $('#form_modulo #id').val("");
    $('#form_modulo #icono').change();
});

$('#modalGrupoModulo').on('hide.bs.modal', function () {
    Validation.cleanFormErrors("#form_grupo");
    $('#form_grupo').get(0).reset();
    $('#form_grupo #grupo-id').val("");
    $('#form_grupo #grupo-icono').change();
});

$('#btnModuloAgregar').on('click', function () {
    $('#modalModulo').find(".modal-title").html("Nuevo Modulo");    
    $('#modalModulo').modal('show');
});

$('#btn-agregar-grupo').on('click',function(){
    $('#modalGrupoModulo').find(".modal-title").html("Nuevo Grupo");    
    $('#modalGrupoModulo').modal('show');
});
$('#icono,#grupo-icono').on('keyup',function(){ $(this).change(); });
$('#icono,#grupo-icono').on('change',function(){
    var nivel = $(this).attr('data-nivel');
    if($(this).val()){
        $('#icono-'+nivel).attr('class','fa fa-5x '+$(this).val());
    }else{
        $('#icono-'+nivel).attr('class','fa fa-5x fa-square');
    }
});

$('#btn-guardar-modulo').on('click', function (e) {
    e.preventDefault();
    Validation.cleanFormErrors("#form_modulo");

    var parametros = $("#form_modulo").serialize();
    parametros += '&guardar=modulo';
    if($('#form_modulo #id').val()==""){
        moduleResource.post(parametros,{
            _success: function(response){
                moduleDatagrid.actualizar();
                MessageManager.show({data:'Modulo creado con éxito',type:'OK',timer:4});
                $('#form_modulo #id').val(response.data.id);
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
    }else{
        moduleResource.put($('#form_modulo #id').val(), parametros,{
            _success: function(response){
                moduleDatagrid.actualizar();
                MessageManager.show({data:'Modulo actualizado con éxito',type:'OK',timer:4});
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
    }
});

$('#btn-guardar-grupo').on('click', function (e) {
    e.preventDefault();
    Validation.cleanFormErrors("#form_grupo");

    var parametros = $("#form_grupo").serialize();
    parametros += '&guardar=grupo';
    if($('#form_grupo #grupo-id').val()==""){
        moduleResource.post(parametros,{
            _success: function(response){
                moduleDatagrid.actualizar();
                llenar_select_grupos(response.data);
                $('#modalGrupoModulo').modal('hide');
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
    }else{
        moduleResource.put($('#form_grupo #grupo-id').val(), parametros,{
            _success: function(response){
                moduleDatagrid.actualizar();
                llenar_select_grupos(response.data);
                $('#modalGrupoModulo').modal('hide');
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
    }
});

$('#btn-eliminar-modulo').on('click',function (e){
    e.preventDefault();
    if($('#id').val()){
        Validation.cleanFormErrors("#form_modulo");
        moduleResource.delete($('#id').val(),{eliminar:'modulo'},{
            _success: function(response){
                moduleDatagrid.actualizar();
                $('#modalModulo').modal('hide');
                MessageManager.show({data:'Modulo eliminado con éxito',type:'OK',timer:4});
            }
        });
    }
});

$('#btn-eliminar-grupo').on('click',function (e){
    e.preventDefault();
    if($('#grupo-id').val()){
        Validation.cleanFormErrors("#form_grupo");
        moduleResource.delete($('#grupo-id').val(),{eliminar:'grupo'},{
            _success: function(response){
                moduleDatagrid.actualizar();
                $('#modalGrupoModulo').modal('hide');
                llenar_select_grupos(response.elementos);
                MessageManager.show({data:'Grupo eliminado con éxito',type:'OK',timer:4});
            }
        });
    }
});
/*===================================*/
// Funciones adicionales por módulo

function llenar_select_grupos(datos){
    var seleccionado = $('#grupo').val();
    var hmtl_options = '<option value="">Selecciona un grupo</option>';
    for(var i in datos){
        hmtl_options += '<option value="'+datos[i].id+'">'+datos[i].key+'::'+datos[i].nombre+'</option>';
    }
    $('#grupo').html(hmtl_options);
    $('#grupo').val(seleccionado);
}