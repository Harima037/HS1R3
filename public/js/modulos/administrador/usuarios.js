/*=====================================

    # Nombre:
        usuarios.js

    # Módulo:
        administrador/usuarios

    # Descripción:
        Se utiliza para crear, editar y eliminar usuarios

=====================================*/

// Inicialización General para casi cualquier módulo

var permisos_individuales = [];
var moduloResource = new RESTfulRequests(SERVER_HOST+'/v1/usuarios');
var moduloDatagrid = new Datagrid("#datagridModulo",moduloResource);
moduloDatagrid.init();
moduloDatagrid.actualizar();

$('.chosen-one').chosen({width:'100%'});


/*===================================*/
// Configuración General para cualquier módulo
$('#modalModulo').on('show.bs.modal', function () {
    Validation.cleanFormErrors("#formModulo");
    MessageManager.dismissAlert('#modalModulo .modal-body');
});

$('#modalModulo').on('shown.bs.modal', function () {
    $('#modalModulo .nav-tabs a:first').tab('show');
    $('#modalModulo').find('input').eq(0).focus();
});

$('#modalModulo').on('hidden.bs.modal',function(){
    resetModalModuloForm();
});

$('#btnModuloAgregar').on('click', function () {
    resetModalModuloForm();
    $('#modalModulo').find(".modal-title").html("Nuevo Usuario");    
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



/*===================================*/
// Implementación personalizada del módulo

function editar (e){
    permisos_individuales = [];
    $('#modalModulo .nav-tabs a:first').tab('show');

    moduloResource.get(e,null,{
        _success: function(response){

            //$('#formModulo').data('bootstrapValidator').enableFieldValidators('password',false);
            //$('#formModulo').data('bootstrapValidator').enableFieldValidators('password_confirm',false);

            $('#formModulo #id').val(response.data.id);
            $('#formModulo #username').val(response.data.username);
            $('#formModulo #nombres').val(response.data.nombres);
            $('#formModulo #apellido-paterno').val(response.data.apellidoPaterno);
            $('#formModulo #apellido-materno').val(response.data.apellidoMaterno);
            $('#formModulo #cargo').val(response.data.cargo);
            $('#formModulo #departamento').val(response.data.idDepartamento);
            $('#formModulo #email').val(response.data.email);
            $('#formModulo #telefono').val(response.data.telefono);
            $('#formModulo #rol').val(response.data.roles);
            permisos_individuales = response.data.permissions;
            
            if(response.data.claveUnidad){
                var unidades = response.data.claveUnidad.split('|');
                $('#formModulo #unidad').val(unidades);
            }

            if(response.data.proyectos){
                if(response.data.proyectos.length){
                    llenar_lista_proyectos(response.data.proyectos);
                }else{
                    $('#conteo-proyectos-seleccionados').text('0');
                }
            }

            if(response.data.permissions.superuser){
                $("#btn-cargar-cat-permisos").hide();
                $("#btn-limpiar-permisos").hide();
                $("#nav-tab-seguridad").hide();
                $("#tab-seguridad").attr("style",'display:none');
            }else{
                var parametros =  {}                        
                parametros.user_id = $('#formModulo #id').val();
                parametros.roles = $('#formModulo #rol').val();
                
                catalogoPermisosResource.get(null,parametros,{
                    _success: function(response){                       
                            response.data.user= permisos_individuales;
                            cargarCatalogoPermisos(response);
                            permisos_individuales = parsePermisos();
                            buildPermissionPanel(permisos_individuales);

                        }
                },"Cargando permisos");

                $("#btn-cargar-cat-permisos").show();
                $("#btn-limpiar-permisos").show();
                $("#nav-tab-seguridad").show();
                $("#tab-seguridad").attr("style",'');
                $('#formModulo .chosen-one').trigger('chosen:updated');
            }

            $('#modalModulo').find(".modal-title").html("Editar Usuario");
            $('#modalModulo').modal('show');  
        }
    },"Cargando datos");
}
function submitModulo(){
    Validation.cleanFormErrors("#formModulo");
    MessageManager.dismissAlert('body');

    var parametros = $("#formModulo").serialize();
    parametros.rol = $('#formModulo #rol').val();
    if($('#formModulo #id').val()==""){
        moduloResource.post(parametros,{
                        _success: function(response){
                            moduloDatagrid.actualizar();
                            MessageManager.show({data:'Usuario creado con éxito',container: '#modalModulo .modal-body',type:'OK',timer:4});
                            $('#formModulo #id').val(response.data.id);
                        },
                        _error: function(response){
                            try{
                                var json = $.parseJSON(response.responseText);
                                if(!json.code)
                                    MessageManager.show({code:'S03',data:"Hubo un problema al realizar la transacción, inténtelo de nuevo o contacte con soporte técnico."});
                                else{
                                    json.container = '#modalModulo .modal-body';
                                    //json.timer = 4;
                                    MessageManager.show(json);
                                }
                                Validation.formValidate(json.data);
                            }catch(e){
                                console.log(e);
                            }                       
                        }
        });
    }else{
        moduloResource.put($('#formModulo #id').val(), parametros,{
                        _success: function(response){
                            moduloDatagrid.actualizar();
                            MessageManager.show({data:'Usuario actualizado con éxito', container: '#modalModulo .modal-body',type:'OK',timer:4});
                        },
                        _error: function(response){
                            try{
                                var json = $.parseJSON(response.responseText);
                                if(!json.code)
                                    MessageManager.show({code:'S03',data:"Hubo un problema al realizar la transacción, inténtelo de nuevo o contacte con soporte técnico."});
                                else{
                                    json.container = '#modalModulo .modal-body';
                                    //json.timer = 4;
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

$('#btnModuloBloquear').on('click',function () {
    row_ids = [];
    $(moduloDatagrid.selector).find("tbody").find("input[type=checkbox]:checked").each(function () {
        row_ids.push($(this).parent().parent().data("id"));
    });

    if (row_ids.length > 0) {
        var parametros = {'user-action-ban':true,'rows':row_ids};
        moduloResource.put(0, parametros,{
            _success: function(response){
                moduloDatagrid.actualizar();
                MessageManager.show({data:'Usuario(s) Bloqueado/Desbloqueado con éxito.',type:'OK',timer:4});
            }
        });
    }else {
        moduloDatagrid.alert("Seleccione al menos a un usuario.");
    }
});


function resetModalModuloForm(){
    $('#formModulo').get(0).reset();
    $('#formModulo #id').val("");
    permisos_individuales = {};
    $('#formModulo #pnlPermissions').html('<tr><td>Aún no hay permisos individuales asignados.</td></tr>');
    $('#formModulo .chosen-one').trigger('chosen:updated');
    limpiar_lista_proyectos();
}

// Funciones de permisos

$('#modalModulo #btn-limpiar-permisos').on('click',function(e){
    e.preventDefault();
    cleanPermissionPanel();
});

$('#modalModulo #btn-limpiar-proyectos').on('click',function(e){
    e.preventDefault();
    limpiar_lista_proyectos();
});

$('#btn-cargar-cat-permisos').click(function(){
    var parametros = {};
    if($('#formModulo #id').val() != ''){
        parametros.user_id = $('#formModulo #id').val();
    }
    if($('#formModulo #rol').val() != ''){
        parametros.roles = $('#formModulo #rol').val();
    }
    catalogoPermisosResource.get(null,parametros,{
        _success: function(response){                  
                response.data.user= permisos_individuales;
                cargarCatalogoPermisos(response);
                $('#modalCatalogoPermisos').modal('show');
            }
    },"Cargando permisos");
});

$('#modalCatalogoPermisos .btn-seleccionar').on('click',function(e){
    e.preventDefault();
    permisos_individuales = parsePermisos();
    buildPermissionPanel(permisos_individuales);
    $('#modalCatalogoPermisos').modal('hide');
});

$('#unidad').on('change',function(){
    var unidad_responsable = $(this).val();
    var condicion = '';
    for(var i in unidad_responsable){
        condicion += '[data-clave-unidad!="'+unidad_responsable[i]+'"]';
    }
    $('#tabla-lista-proyectos tbody tr'+condicion).remove();
    if(!$('#tabla-lista-proyectos tbody tr').length){
        $('#tabla-lista-proyectos tbody').html('<tr id="tr-proyectos-vacio"><td colspan="3"><span class="fa fa-info-circle"></span> No hay proyectos asignados</td></tr>');
        $('#conteo-proyectos-seleccionados').text('0');
    }else{
        $('#conteo-proyectos-seleccionados').text($('#tabla-lista-proyectos tbody tr').length);
    }
});

function cleanPermissionPanel(){
    permisos_individuales = {};
    $('#formModulo #pnlPermissions').html('<tr><td>Aún no hay permisos individuales asignados.</td></tr>');
}

function buildPermissionPanel(permisos){
    var html_hiddens = '';
    var html_permissions = '<tbody>';

    for(i in permisos){

        html_permissions += '<tr><th colspan="2">'+i+'</th></tr>';
        if(permisos[i]['R'] == 1){
            html_permissions += '<small> Acceso </small>';
            html_hiddens += '<input type="hidden" id="' + i + '.R" name="permissions[' + i + '.R]" value="1">';
        }
     
        if(permisos[i]){
            for (j in permisos[i]) {
                html_permissions +=         '<tr><td>'+j+"</td>";
                html_permissions +=         '<td style="text-align:right;">';

                for(k in permisos[i][j]){
                    label_class = 'default'; //inherit
                    if(permisos[i][j][k] != 0){ // 1 = allowed, 0 = inherit, -1 = deny
                        value = permisos[i][j][k];
                        if(value == 1){
                            label_class = 'success'; //allowed
                        }else{
                            label_class = 'danger'; //deny
                        }
                        
                        id = i + '.' + j + '.' + k; //SIS.MOD.[C|R|U|D]
                        html_hiddens += '<input type="hidden" id="'+id+'" name="permissions['+id+']" value="'+value+'">';
                    }
                    html_permissions += '<span class="label label-' + label_class + '">';
                    html_permissions += '<i class="'+icons[k]+'"></i></span> ';                 
                }
                html_permissions += '</td>';
                html_permissions += '</tr>';
            }
        }
        html_permissions += '</li>';
    }
    html_permissions +=  "</tbody>"+html_hiddens;
    
    $('#formModulo #pnlPermissions').html('<tr><td>Aún no hay permisos individuales asignados.</td></tr>');
    $('#formModulo #pnlPermissions').html(html_permissions);
}

/*

        Funciones para la busqueda de Proyectos por medio del suggester (Typeahead)

*/
var proyectoTypeAhead = new Bloodhound({
    datumTokenizer: function (d) { return Bloodhound.tokenizers.whitespace(d.text); },
    queryTokenizer: Bloodhound.tokenizers.whitespace,
    limit: 100,
    remote: {
        url: SERVER_HOST+'/v1/proyectos?typeahead=1&buscar=%QUERY',
        filter: function ( response ) {
            return crear_objeto_proyecto(response.data);
        },
        ajax:{
            beforeSend: function(jqXHR,settings){
                if($('#unidad').val()){
                    settings.url = settings.url + '&unidades='+$('#unidad').val();
                }
                $('#estatus-busqueda-proyecto').html('Buscando... <span class="fa fa-spinner fa-spin"></span>');
            },
            complete: function(jqXHR,textStatus){
                $('#estatus-busqueda-proyecto').html(jqXHR.responseJSON.resultados + ' Proyectos Encontrados');
            }
        }
    }
});
proyectoTypeAhead.initialize();

$('#buscar-proyecto').typeahead(null,{
    minLength: 3,
    displayKey: 'nombreTecnico',
    source: proyectoTypeAhead.ttAdapter(),
    templates: { 
        empty: '<div class="empty-result">No se encontraron Proyectos</div>',
        suggestion: obtener_template_proyecto()
    }
}).on('typeahead:selected', function (object, datum) {
    if(!$('#tabla-lista-proyectos tbody tr[data-id="'+datum.id+'"]').length){
        var html_row = obtener_html_tabla_proyectos(datum);
        if($('#tr-proyectos-vacio').length){
            $('#tr-proyectos-vacio').remove();
        }
        $('#tabla-lista-proyectos tbody').append(html_row);
        $('#conteo-proyectos-seleccionados').text($('#tabla-lista-proyectos tbody tr').length);
    }
    reset_busqueda();
    proyectoTypeAhead.clearRemoteCache();
}).on('typeahead:cursorchanged', function(object, datum){
    $('.tt-suggestion.tt-suggestion-selected').removeClass('tt-suggestion-selected');
    $('#suggest_'+datum.id).parents('.tt-suggestion').addClass('tt-suggestion-selected');
});

function quitar_proyecto(id){
    $('#tabla-lista-proyectos tbody tr[data-id="'+id+'"]').remove();
    if(!$('#tabla-lista-proyectos tbody tr').length){
        $('#tabla-lista-proyectos tbody').html('<tr id="tr-proyectos-vacio"><td colspan="3"><span class="fa fa-info-circle"></span> No hay proyectos asignados</td></tr>');
        $('#conteo-proyectos-seleccionados').text('0');
    }else{
        $('#conteo-proyectos-seleccionados').text($('#tabla-lista-proyectos tbody tr').length);
    }
}

function limpiar_lista_proyectos(){
    $('#tabla-lista-proyectos tbody').empty();
    $('#tabla-lista-proyectos tbody').html('<tr id="tr-proyectos-vacio"><td colspan="3"><span class="fa fa-info-circle"></span> No hay proyectos asignados</td></tr>');
    $('#conteo-proyectos-seleccionados').text('0');
}

function llenar_lista_proyectos(datos){
    var lista_proyectos = '';
    var proyectos = crear_objeto_proyecto(datos);
    for(var i in proyectos){
        lista_proyectos += obtener_html_tabla_proyectos(proyectos[i]);
    }
    if($('#tr-proyectos-vacio').length){
        $('#tr-proyectos-vacio').remove();
    }
    $('#tabla-lista-proyectos tbody').append(lista_proyectos);
    $('#conteo-proyectos-seleccionados').text(datos.length);
}

function obtener_html_tabla_proyectos(proyecto){
    var html_row = '<tr data-id="'+proyecto.id+'" data-clave-unidad="'+proyecto.claveUnidadResponsable+'">';
    html_row += '<td><input type="hidden" name="proyectos[]" value="'+proyecto.id+'">'+proyecto.clavePresupuestaria+'</td>';
    html_row += '<td>'+proyecto.nombreTecnico+'</td>';
    html_row += '<td><button type="button" class="btn btn-danger btn-block" onClick="quitar_proyecto('+proyecto.id+')"><span class="fa fa-trash"></span></button></td>';
    html_row += '</tr>';
    return html_row;
}

function proyecto_seleccionado(proyecto){
    var template = obtener_template_proyecto();
    return template(proyecto);
}

$('.popover-dismiss').popover({ placement:'top', trigger:'hover' });

function obtener_template_proyecto(){
    return Handlebars.compile('<div id="suggest_{{id}}" class="item">'+
            '<table width="100%" border="0" cellpadding="0" cellspacing="0">'+
                '<tr>'+
                    '<td class="text-{{claseEstatus}}" ><b>{{clavePresupuestaria}}</b> <small>({{clasificacion}})</small></td>'+
                    '<td width="1"><span class="label label-{{claseEstatus}}">{{estatusProyecto}}</span></td>'+
                '</tr>'+
                '<tr>'+
                    '<td class="text-{{claseEstatus}}" colspan="2"><big>{{nombreTecnico}}</big></td>'+
                '</tr>'+
                '<tr>'+
                    '<td colspan="2"><span class="text-muted"><small>{{unidadResponsable}}</small></span></td>'+
                '</tr>'+
            '</table>'+
        '</div>');
}

function crear_objeto_proyecto(datos){
    var clasesEstatus = {1:'info',2:'warning',3:'danger',4:'primary',5:'success'};
    return $.map(datos, function (object) {
        return {
            clavePresupuestaria: object.ClavePresupuestaria,
            nombreTecnico: object.nombreTecnico,
            estatusProyecto: object.estatusProyectoDescripcion,
            claseEstatus: clasesEstatus[object.idEstatusProyecto],
            unidadResponsable: object.unidadResponsable + ' ' + object.unidadResponsableDescripcion,
            claveUnidadResponsable: object.unidadResponsable,
            clasificacion: (object.idClasificacionProyecto == 1)?'Institucional':'Inversión',
            id: object.id
        };
    });
}

function reset_busqueda(){
    $('#buscar-proyecto').typeahead('val','');
    $('#estatus-busqueda-proyecto').html('');
}