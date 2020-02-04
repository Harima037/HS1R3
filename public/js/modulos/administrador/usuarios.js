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

var ids_proyectos_seleccionados = {};
var ids_caratulas_seleccionados = {};
var ids_programas_seleccionados = {};
var ids_indicadores_seleccionados = {};
var ids_estrategias_seleccionados = {};

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

$('#modalReporte').on('hidden.bs.modal',function(){
    resetModalReporteForm();
});

$('#btnReporteUsuarios').on('click',function(e){
    e.preventDefault();
    $('#modalReporte').find(".modal-title").html("Imprimir Reporte");    
    $('#modalReporte').modal('show');
})

$('#btnModuloAgregar').on('click', function (e) {
    e.preventDefault();
    //resetModalModuloForm();
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

$('#btn-imprimir-reporte').on('click',function(e){
    e.preventDefault();
    //var parametros = $("#formReporte").serialize();
    $("#formReporte").attr('action',SERVER_HOST+'/v1/reporte-usuarios');
    $("#formReporte").submit();
    //window.open(SERVER_HOST+'/v1/reporte-usuarios/' + parametros);
})

/*===================================*/
// Implementación personalizada del módulo

function editar (e){
    permisos_individuales = [];
    $('#modalModulo .nav-tabs a:first').tab('show');

    moduloResource.get(e,null,{
        _success: function(response){
            $('#formModulo #id').val(response.data.id);
            $('#formModulo #username').val(response.data.username);
            $('#formModulo #nombres').val(response.data.nombres);
            $('#formModulo #apellido-paterno').val(response.data.apellidoPaterno);
            $('#formModulo #apellido-materno').val(response.data.apellidoMaterno);
            $('#formModulo #cargo').val(response.data.cargo);
            $('#formModulo #departamento').val(response.data.idDepartamento);
            $('#formModulo #jurisdiccion').val(response.data.claveJurisdiccion);
            $('#formModulo #email').val(response.data.email);
            $('#formModulo #telefono').val(response.data.telefono);
            $('#formModulo #rol').val(response.data.roles);
            permisos_individuales = response.data.permissions;
            
            if(response.data.filtrarCaratulas){
                $('#filtrar-caratulas').prop('checked',true);
            }else{
                $('#filtrar-caratulas').prop('checked',false);
            }
            
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

            if(response.data.caratulas){
                if(response.data.caratulas.length){
                    llenar_lista_caratulas(response.data.caratulas);
                }else{
                    $('#conteo-caratulas-seleccionados').text('0');
                }
            }
            
            if(response.data.programas){
                if(response.data.programas.length){
                    llenar_lista_programas(response.data.programas);
                }else{
                    $('#conteo-programas-seleccionados').text('0');
                }
            }
            
            if(response.data.indicadores){
                if(response.data.indicadores.length){
                    llenar_lista_indicadores(response.data.indicadores);
                }else{
                    $('#conteo-indicadores-seleccionados').text('0');
                }
            }

            if(response.data.estrategias){
                if(response.data.estrategias.length){
                    llenar_lista_estrategias(response.data.estrategias);
                }else{
                    $('#conteo-estrategias-seleccionados').text('0');
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
            }
            $('#formModulo .chosen-one').trigger('chosen:updated');

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

$('#btnModuloBloquear').on('click',function (e) {
    e.preventDefault();
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
        MessageManager.show({code:'W00',data:"Seleccione al menos a un usuario.",timer:5});
    }
});

function resetModalReporteForm(){
    $('#formReporte').get(0).reset();
    $('#formReporte .chosen-one').trigger('chosen:updated');
}

function resetModalModuloForm(){
    $('#formModulo').get(0).reset();
    $('#formModulo #id').val("");
    permisos_individuales = {};
    $('#formModulo #pnlPermissions').html('<tr><td>Aún no hay permisos individuales asignados.</td></tr>');
    $('#formModulo .chosen-one').trigger('chosen:updated');
    limpiar_lista_proyectos();
    limpiar_lista_caratulas();
    limpiar_lista_programas();
    limpiar_lista_indicadores();
    limpiar_lista_estrategias();
    reset_busqueda();
    proyectoTypeAhead.clearRemoteCache();
    caratulaTypeAhead.clearRemoteCache();
    programaTypeAhead.clearRemoteCache();
    indicadorTypeAhead.clearRemoteCache();
    estrategiaTypeAhead.clearRemoteCache();
    ids_proyectos_seleccionados = {};
    ids_caratulas_seleccionados = {};
    ids_programas_seleccionados = {};
    ids_indicadores_seleccionados = {};
    ids_estrategias_seleccionados = {};
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

$('#modalModulo #btn-limpiar-caratulas').on('click',function(e){
    e.preventDefault();
    limpiar_lista_caratulas();
});

$('#modalModulo #btn-limpiar-programas').on('click',function(e){
    e.preventDefault();
    limpiar_lista_programas();
});

$('#modalModulo #btn-limpiar-indicadores').on('click',function(e){
    e.preventDefault();
    limpiar_lista_indicadores();
});

$('#modalModulo #btn-limpiar-estrategias').on('click',function(e){
    e.preventDefault();
    limpiar_lista_estrategias();
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
    $('#tabla-lista-proyectos tbody tr'+condicion).each(function(){
        var id = $(this).attr('data-id');
        delete ids_proyectos_seleccionados[id];
    });
    $('#tabla-lista-caratulas tbody tr'+condicion).each(function(){
        var id = $(this).attr('data-id');
        delete ids_caratulas_seleccionados[id];
    });
    $('#tabla-lista-programas tbody tr'+condicion).each(function(){
        var id = $(this).attr('data-id');
        delete ids_programas_seleccionados[id];
    });
    $('#tabla-lista-proyectos tbody tr'+condicion).remove();
    $('#tabla-lista-caratulas tbody tr'+condicion).remove();
    $('#tabla-lista-programas tbody tr'+condicion).remove();
    
    if(!$('#tabla-lista-proyectos tbody tr').length){
        $('#tabla-lista-proyectos tbody').html('<tr id="tr-proyectos-vacio"><td colspan="3"><span class="fa fa-info-circle"></span> No hay proyectos asignados</td></tr>');
        $('#conteo-proyectos-seleccionados').text('0');
    }else{
        $('#conteo-proyectos-seleccionados').text($('#tabla-lista-proyectos tbody tr').length);
    }
    
    if(!$('#tabla-lista-caratulas tbody tr').length){
        $('#tabla-lista-caratulas tbody').html('<tr id="tr-caratulas-vacio"><td colspan="3"><span class="fa fa-info-circle"></span> No hay caratulas asignadas</td></tr>');
        $('#conteo-caratulas-seleccionados').text('0');
    }else{
        $('#conteo-caratulas-seleccionados').text($('#tabla-lista-caratulas tbody tr').length);
    }
    
    if(!$('#tabla-lista-programas tbody tr').length){
        $('#tabla-lista-programas tbody').html('<tr id="tr-programas-vacio"><td colspan="3"><span class="fa fa-info-circle"></span> No hay programas asignados</td></tr>');
        $('#conteo-programas-seleccionados').text('0');
    }else{
        $('#conteo-programas-seleccionados').text($('#tabla-lista-programas tbody tr').length);
    }
});

function cleanPermissionPanel(){
    permisos_individuales = {};
    $('#formModulo #pnlPermissions').html('<tr><td>Aún no hay permisos individuales asignados.</td></tr>');
}

function buildPermissionPanel(permisos){
    var html_hiddens = '';
    var html_permissions = '<tbody>';

    for(var i in permisos){

        html_permissions += '<tr><th colspan="2">'+i+'</th></tr>';
        if(permisos[i]['R'] == 1){
            html_permissions += '<small> Acceso </small>';
            html_hiddens += '<input type="hidden" id="' + i + '.R" name="permissions[' + i + '.R]" value="1">';
        }
     
        if(permisos[i]){
            for (var j in permisos[i]) {
                html_permissions +=         '<tr><td>'+j+"</td>";
                html_permissions +=         '<td style="text-align:right;">';

                for(var k in permisos[i][j]){
                    var label_class = 'default'; //inherit
                    if(permisos[i][j][k] != 0){ // 1 = allowed, 0 = inherit, -1 = deny
                        var value = permisos[i][j][k];
                        if(value == 1){
                            label_class = 'success'; //allowed
                        }else{
                            label_class = 'danger'; //deny
                        }
                        
                        var id = i + '.' + j + '.' + k; //SIS.MOD.[C|R|U|D]
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
        url: SERVER_HOST+'/v1/proyectos?typeahead=1&tipo=proyecto&buscar=%QUERY',
        filter: function ( response ) {
            return crear_objeto_proyecto(response.data);
        },
        ajax:{
            beforeSend: function(jqXHR,settings){
                if($('#unidad').val()){
                    settings.url = settings.url + '&unidades='+$('#unidad').val();
                }
                if($('#departamento').val()){
                    settings.url = settings.url + '&departamento='+$('#departamento').val();
                }
                if($('#id').val()){
                    settings.url = settings.url + '&usuario='+$('#id').val();
                }
                $('#estatus-busqueda-proyecto').html('Buscando... <span class="fa fa-spinner fa-spin"></span>');
            },
            complete: function(jqXHR,textStatus){
                $('#estatus-busqueda-proyecto').html(jqXHR.responseJSON.resultados + ' Proyectos Encontrados');
                proyectoTypeAhead.clearRemoteCache();
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
    ids_proyectos_seleccionados[datum.id] = 1;
}).on('typeahead:cursorchanged', function (object, datum){
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
    delete ids_proyectos_seleccionados[id];
}

function limpiar_lista_proyectos(){
    $('#tabla-lista-proyectos tbody').empty();
    $('#tabla-lista-proyectos tbody').html('<tr id="tr-proyectos-vacio"><td colspan="3"><span class="fa fa-info-circle"></span> No hay proyectos asignados</td></tr>');
    $('#conteo-proyectos-seleccionados').text('0');
    ids_proyectos_seleccionados = {};
}

function llenar_lista_proyectos(datos){
    var lista_proyectos = '';
    var proyectos = crear_objeto_proyecto(datos);
    for(var i in proyectos){
        lista_proyectos += obtener_html_tabla_proyectos(proyectos[i]);
        ids_proyectos_seleccionados[proyectos[i].id] = 1;
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

function obtener_template_proyecto(){
    return Handlebars.compile('<div id="suggest_{{id}}" class="item">'+
            '<table width="100%" border="0" cellpadding="0" cellspacing="0">'+
                '<tr>'+
                    '<td class="text-{{claseEstatus}}" ><b>{{clavePresupuestaria}}</b> <small>({{clasificacion}})</small></td>'+
                    '<td width="1" class="label-{{claseEstatus}}"><span class="label">{{estatusProyecto}}</span></td>'+
                '</tr>'+
                '<tr>'+
                    '<td class="text-{{claseEstatus}}" colspan="2"><big>{{nombreTecnico}}</big></td>'+
                '</tr>'+
                '<tr>'+
                    '<td colspan="2"><span class="text-muted"><small>{{unidadResponsable}}</small></span>'+
                    '<span class="pull-right"><small><span class="{{seleccionado}}"></span> {{seleccionadoLabel}}</small></span>'+
                    '</td>'+
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
            seleccionado: (ids_proyectos_seleccionados[object.id])?'fa fa-check':'',
            seleccionadoLabel: (ids_proyectos_seleccionados[object.id])?'Seleccionado':'',
            id: object.id
        };
    });
}

function reset_busqueda(){
    $('#buscar-proyecto').typeahead('val','');
    $('#estatus-busqueda-proyecto').html('');
    $('#buscar-caratula').typeahead('val','');
    $('#estatus-busqueda-caratula').html('');
    $('#buscar-programa').typeahead('val','');
    $('#estatus-busqueda-programa').html('');
    $('#buscar-indicador').typeahead('val','');
    $('#estatus-busqueda-indicador').html('');
    //$('#buscar-estrategia').typeahead('val','');
    $('#estatus-busqueda-estrategia').html('');
}

/*

        Funciones para la busqueda de Caratulas por medio del suggester (Typeahead)

*/
var caratulaTypeAhead = new Bloodhound({
    datumTokenizer: function (d) { return Bloodhound.tokenizers.whitespace(d.text); },
    queryTokenizer: Bloodhound.tokenizers.whitespace,
    limit: 100,
    remote: {
        url: SERVER_HOST+'/v1/proyectos?typeahead=1&tipo=caratula&buscar=%QUERY',
        filter: function ( response ) {
            return crear_objeto_caratula(response.data);
        },
        ajax:{
            beforeSend: function(jqXHR,settings){
                if($('#unidad').val()){
                    settings.url = settings.url + '&unidades='+$('#unidad').val();
                }
                if($('#departamento').val()){
                    settings.url = settings.url + '&departamento='+$('#departamento').val();
                }
                if($('#id').val()){
                    settings.url = settings.url + '&usuario='+$('#id').val();
                }
                $('#estatus-busqueda-caratula').html('Buscando... <span class="fa fa-spinner fa-spin"></span>');
            },
            complete: function(jqXHR,textStatus){
                $('#estatus-busqueda-caratula').html(jqXHR.responseJSON.resultados + ' Caratulas Encontradas');
                caratulaTypeAhead.clearRemoteCache();
            }
        }
    }
});
caratulaTypeAhead.initialize();

$('#buscar-caratula').typeahead(null,{
    minLength: 3,
    displayKey: 'nombreTecnico',
    source: caratulaTypeAhead.ttAdapter(),
    templates: { 
        empty: '<div class="empty-result">No se encontraron Caratulas</div>',
        suggestion: obtener_template_proyecto()
    }
}).on('typeahead:selected', function (object, datum) {
    if(!$('#tabla-lista-caratulas tbody tr[data-id="'+datum.id+'"]').length){
        var html_row = obtener_html_tabla_caratulas(datum);
        if($('#tr-caratulas-vacio').length){
            $('#tr-caratulas-vacio').remove();
        }
        $('#tabla-lista-caratulas tbody').append(html_row);
        $('#conteo-caratulas-seleccionados').text($('#tabla-lista-caratulas tbody tr').length);
    }
    reset_busqueda();
    caratulaTypeAhead.clearRemoteCache();
    ids_caratulas_seleccionados[datum.id] = 1;
}).on('typeahead:cursorchanged', function (object, datum){
    $('.tt-suggestion.tt-suggestion-selected').removeClass('tt-suggestion-selected');
    $('#suggest_'+datum.id).parents('.tt-suggestion').addClass('tt-suggestion-selected');
});

function quitar_caratula(id){
    $('#tabla-lista-caratulas tbody tr[data-id="'+id+'"]').remove();
    if(!$('#tabla-lista-caratulas tbody tr').length){
        $('#tabla-lista-caratulas tbody').html('<tr id="tr-caratulas-vacio"><td colspan="3"><span class="fa fa-info-circle"></span> No hay caratulas asignados</td></tr>');
        $('#conteo-caratulas-seleccionados').text('0');
    }else{
        $('#conteo-caratulas-seleccionados').text($('#tabla-lista-caratulas tbody tr').length);
    }
    delete ids_caratulas_seleccionados[id];
}

function limpiar_lista_caratulas(){
    $('#tabla-lista-caratulas tbody').empty();
    $('#tabla-lista-caratulas tbody').html('<tr id="tr-caratulas-vacio"><td colspan="3"><span class="fa fa-info-circle"></span> No hay caratulas asignados</td></tr>');
    $('#conteo-caratulas-seleccionados').text('0');
    ids_caratulas_seleccionados = {};
}

function llenar_lista_caratulas(datos){
    var lista_proyectos = '';
    var proyectos = crear_objeto_caratula(datos);
    for(var i in proyectos){
        lista_proyectos += obtener_html_tabla_caratulas(proyectos[i]);
        ids_caratulas_seleccionados[proyectos[i].id] = 1;
    }
    if($('#tr-caratulas-vacio').length){
        $('#tr-caratulas-vacio').remove();
    }
    $('#tabla-lista-caratulas tbody').append(lista_proyectos);
    $('#conteo-caratulas-seleccionados').text(datos.length);
}

function obtener_html_tabla_caratulas(proyecto){
    var html_row = '<tr data-id="'+proyecto.id+'" data-clave-unidad="'+proyecto.claveUnidadResponsable+'">';
    html_row += '<td><input type="hidden" name="caratulas[]" value="'+proyecto.id+'">'+proyecto.clavePresupuestaria+'</td>';
    html_row += '<td>'+proyecto.nombreTecnico+'</td>';
    html_row += '<td><button type="button" class="btn btn-danger btn-block" onClick="quitar_caratula('+proyecto.id+')"><span class="fa fa-trash"></span></button></td>';
    html_row += '</tr>';
    return html_row;
}

function caratula_seleccionado(proyecto){
    var template = obtener_template_caratula();
    return template(proyecto);
}

function obtener_template_caratula(){
    return Handlebars.compile('<div id="suggest_{{id}}" class="item">'+
            '<table width="100%" border="0" cellpadding="0" cellspacing="0">'+
                '<tr>'+
                    '<td class="text-{{claseEstatus}}" ><b>{{clavePresupuestaria}}</b> <small>({{clasificacion}})</small></td>'+
                    '<td width="1" class="label-{{claseEstatus}}"><span class="label">{{estatusProyecto}}</span></td>'+
                '</tr>'+
                '<tr>'+
                    '<td class="text-{{claseEstatus}}" colspan="2"><big>{{nombreTecnico}}</big></td>'+
                '</tr>'+
                '<tr>'+
                    '<td colspan="2"><span class="text-muted"><small>{{unidadResponsable}}</small></span>'+
                    '<span class="pull-right"><small><span class="{{seleccionado}}"></span> {{seleccionadoLabel}}</small></span>'+
                    '</td>'+
                '</tr>'+
            '</table>'+
        '</div>');
}

function crear_objeto_caratula(datos){
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
            seleccionado: (ids_caratulas_seleccionados[object.id])?'fa fa-check':'',
            seleccionadoLabel: (ids_caratulas_seleccionados[object.id])?'Seleccionado':'',
            id: object.id
        };
    });
}

/*

        Funciones para la busqueda de Programas Presupuestarios por medio del suggester (Typeahead)

*/
var programaTypeAhead = new Bloodhound({
    datumTokenizer: function (d) { return Bloodhound.tokenizers.whitespace(d.text); },
    queryTokenizer: Bloodhound.tokenizers.whitespace,
    limit: 100,
    remote: {
        url: SERVER_HOST+'/v1/programas-presupuestarios?typeahead=1&buscar=%QUERY',
        filter: function ( response ) {
            return crear_objeto_programa(response.data);
        },
        ajax:{
            beforeSend: function(jqXHR,settings){
                if($('#unidad').val()){
                    settings.url = settings.url + '&unidades='+$('#unidad').val();
                }
                if($('#departamento').val()){
                    settings.url = settings.url + '&departamento='+$('#departamento').val();
                }
                if($('#id').val()){
                    settings.url = settings.url + '&usuario='+$('#id').val();
                }
                $('#estatus-busqueda-programa').html('Buscando... <span class="fa fa-spinner fa-spin"></span>');
            },
            complete: function(jqXHR,textStatus){
                $('#estatus-busqueda-programa').html(jqXHR.responseJSON.resultados + ' Programas Encontrados');
                programaTypeAhead.clearRemoteCache();
            }
        }
    }
});
programaTypeAhead.initialize();

$('#buscar-programa').typeahead(null,{
    minLength: 3,
    displayKey: 'programaPresupuestario',
    source: programaTypeAhead.ttAdapter(),
    templates: { 
        empty: '<div class="empty-result">No se encontraron Programas</div>',
        suggestion: obtener_template_programa()
    }
}).on('typeahead:selected', function (object, datum) {
    if(!$('#tabla-lista-programas tbody tr[data-id="'+datum.id+'"]').length){
        var html_row = obtener_html_tabla_programas(datum);
        if($('#tr-programas-vacio').length){
            $('#tr-programas-vacio').remove();
        }
        $('#tabla-lista-programas tbody').append(html_row);
        $('#conteo-programas-seleccionados').text($('#tabla-lista-programas tbody tr').length);
    }
    reset_busqueda();
    programaTypeAhead.clearRemoteCache();
    ids_programas_seleccionados[datum.id] = 1;
}).on('typeahead:cursorchanged', function (object, datum){
    $('.tt-suggestion.tt-suggestion-selected').removeClass('tt-suggestion-selected');
    $('#suggest_'+datum.id).parents('.tt-suggestion').addClass('tt-suggestion-selected');
});

function quitar_programa(id){
    $('#tabla-lista-programas tbody tr[data-id="'+id+'"]').remove();
    if(!$('#tabla-lista-programas tbody tr').length){
        $('#tabla-lista-programas tbody').html('<tr id="tr-programas-vacio"><td colspan="3"><span class="fa fa-info-circle"></span> No hay programas asignados</td></tr>');
        $('#conteo-programas-seleccionados').text('0');
    }else{
        $('#conteo-programas-seleccionados').text($('#tabla-lista-programas tbody tr').length);
    }
    delete ids_programas_seleccionados[id];
}

function limpiar_lista_programas(){
    $('#tabla-lista-programas tbody').empty();
    $('#tabla-lista-programas tbody').html('<tr id="tr-programas-vacio"><td colspan="3"><span class="fa fa-info-circle"></span> No hay programas asignados</td></tr>');
    $('#conteo-programas-seleccionados').text('0');
    ids_programas_seleccionados = {};
}

function llenar_lista_programas(datos){
    var lista_programas = '';
    var programas = crear_objeto_programa(datos);
    for(var i in programas){
        lista_programas += obtener_html_tabla_programas(programas[i]);
        ids_programas_seleccionados[programas[i].id] = 1;
    }
    if($('#tr-programas-vacio').length){
        $('#tr-programas-vacio').remove();
    }
    $('#tabla-lista-programas tbody').append(lista_programas);
    $('#conteo-programas-seleccionados').text(datos.length);
}

function obtener_html_tabla_programas(programa){
    var html_row = '<tr data-id="'+programa.id+'" data-clave-unidad="'+programa.claveUnidadResponsable+'">';
    html_row += '<td><input type="hidden" name="programas[]" value="'+programa.id+'">'+programa.programaPresupuestario+'</td>';
    html_row += '<td><button type="button" class="btn btn-danger btn-block" onClick="quitar_programa('+programa.id+')"><span class="fa fa-trash"></span></button></td>';
    html_row += '</tr>';
    return html_row;
}

function programa_seleccionado(programa){
    var template = obtener_template_programa();
    return template(programa);
}

function obtener_template_programa(){
    return Handlebars.compile('<div id="suggest_{{id}}" class="item">'+
            '<table width="100%" border="0" cellpadding="0" cellspacing="0">'+
                '<tr>'+
                    '<td class="text-{{claseEstatus}}" ></td>'+
                    '<td width="100px" class="label-{{claseEstatus}}"><span class="label">{{estatus}}</span></td>'+
                '</tr>'+
                '<tr>'+
                    '<td colspan="2" class="text-{{claseEstatus}}" ><b>{{programaPresupuestario}}</b></td>'+
                '</tr>'+
                '<tr>'+
                    '<td colspan="2"><span class="text-muted"><small>{{unidadResponsable}}</small></span>'+
                    '<span class="pull-right"><small><span class="{{seleccionado}}"></span> {{seleccionadoLabel}}</small></span>'+
                    '</td>'+
                '</tr>'+
            '</table>'+
        '</div>');
}

function crear_objeto_programa(datos){
    var clasesEstatus = {1:'info',2:'warning',3:'danger',4:'primary',5:'success'};
    return $.map(datos, function (object) {
        return {
            claveProgramaPresupuestario: object.claveProgramaPresupuestario,
            claveUnidadResponsable: object.claveUnidadResponsable,
            programaPresupuestario: object.programaPresupuestario,
            unidadResponsable: object.unidadResponsable,
            claseEstatus: clasesEstatus[object.idEstatus],
            estatus: object.estatus,
            seleccionado: (ids_programas_seleccionados[object.id])?'fa fa-check':'',
            seleccionadoLabel: (ids_programas_seleccionados[object.id])?'Seleccionado':'',
            id: object.id
        };
    });
}

/*

        Funciones para la busqueda de Indicadores de FASSA por medio del suggester (Typeahead)

*/

var indicadorTypeAhead = new Bloodhound({
    datumTokenizer: function (d) { return Bloodhound.tokenizers.whitespace(d.text); },
    queryTokenizer: Bloodhound.tokenizers.whitespace,
    limit: 100,
    remote: {
        url: SERVER_HOST+'/v1/indicadores-fassa?typeahead=1&buscar=%QUERY',
        filter: function ( response ) {
            return crear_objeto_indicador(response.data);
        },
        ajax:{
            beforeSend: function(jqXHR,settings){
                if($('#departamento').val()){
                    settings.url = settings.url + '&departamento='+$('#departamento').val();
                }
                if($('#id').val()){
                    settings.url = settings.url + '&usuario='+$('#id').val();
                }
                $('#estatus-busqueda-indicador').html('Buscando... <span class="fa fa-spinner fa-spin"></span>');
            },
            complete: function(jqXHR,textStatus){
                $('#estatus-busqueda-indicador').html(jqXHR.responseJSON.resultados + ' Indicadores Encontrados');
                indicadorTypeAhead.clearRemoteCache();
            }
        }
    }
});
indicadorTypeAhead.initialize();

$('#buscar-indicador').typeahead(null,{
    minLength: 3,
    displayKey: 'indicador',
    source: indicadorTypeAhead.ttAdapter(),
    templates: { 
        empty: '<div class="empty-result">No se encontraron Indicadores</div>',
        suggestion: obtener_template_indicador()
    }
}).on('typeahead:selected', function (object, datum) {
    if(!$('#tabla-lista-indicadores tbody tr[data-id="'+datum.id+'"]').length){
        var html_row = obtener_html_tabla_indicadores(datum);
        if($('#tr-indicadores-vacio').length){
            $('#tr-indicadores-vacio').remove();
        }
        $('#tabla-lista-indicadores tbody').append(html_row);
        $('#conteo-indicadores-seleccionados').text($('#tabla-lista-indicadores tbody tr').length);
    }
    reset_busqueda();
    indicadorTypeAhead.clearRemoteCache();
    ids_indicadores_seleccionados[datum.id] = 1;
}).on('typeahead:cursorchanged', function (object, datum){
    $('.tt-suggestion.tt-suggestion-selected').removeClass('tt-suggestion-selected');
    $('#suggest_'+datum.id).parents('.tt-suggestion').addClass('tt-suggestion-selected');
});

function quitar_indicador(id){
    $('#tabla-lista-indicadores tbody tr[data-id="'+id+'"]').remove();
    if(!$('#tabla-lista-indicadores tbody tr').length){
        $('#tabla-lista-indicadores tbody').html('<tr id="tr-indicadores-vacio"><td colspan="3"><span class="fa fa-info-circle"></span> No hay indicadores asignados</td></tr>');
        $('#conteo-indicadores-seleccionados').text('0');
    }else{
        $('#conteo-indicadores-seleccionados').text($('#tabla-lista-indicadores tbody tr').length);
    }
    delete ids_indicadores_seleccionados[id];
}

function limpiar_lista_indicadores(){
    $('#tabla-lista-indicadores tbody').empty();
    $('#tabla-lista-indicadores tbody').html('<tr id="tr-indicadores-vacio"><td colspan="3"><span class="fa fa-info-circle"></span> No hay indicadores asignados</td></tr>');
    $('#conteo-indicadores-seleccionados').text('0');
    ids_indicadores_seleccionados = {};
}

function llenar_lista_indicadores(datos){
    var lista_indicadores = '';
    var indicadores = crear_objeto_indicador(datos);
    for(var i in indicadores){
        lista_indicadores += obtener_html_tabla_indicadores(indicadores[i]);
        ids_indicadores_seleccionados[indicadores[i].id] = 1;
    }
    if($('#tr-indicadores-vacio').length){
        $('#tr-indicadores-vacio').remove();
    }
    $('#tabla-lista-indicadores tbody').append(lista_indicadores);
    $('#conteo-indicadores-seleccionados').text(datos.length);
}

function obtener_html_tabla_indicadores(indicador){
    var html_row = '<tr data-id="'+indicador.id+'" data-clave-nivel="'+indicador.claveNivel+'">';
    html_row += '<td><input type="hidden" name="indicadores[]" value="'+indicador.id+'">'+indicador.nivel+'</td>';
    html_row += '<td>'+indicador.indicador+'</td>';
    html_row += '<td><button type="button" class="btn btn-danger btn-block" onClick="quitar_indicador('+indicador.id+')"><span class="fa fa-trash"></span></button></td>';
    html_row += '</tr>';
    return html_row;
}

function indicador_seleccionado(indicador){
    var template = obtener_template_indicador();
    return template(indicador);
}

function obtener_template_indicador(){
    return Handlebars.compile('<div id="suggest_{{id}}" class="item">'+
            '<table width="100%" border="0" cellpadding="0" cellspacing="0">'+
                '<tr>'+
                    '<td class="text-{{claseEstatus}}" ></td>'+
                    '<td width="100px" class="label-{{claseEstatus}}"><span class="label">{{estatus}}</span></td>'+
                '</tr>'+
                '<tr>'+
                    '<td colspan="2" class="text-{{claseEstatus}}" ><b>{{indicador}}</b></td>'+
                '</tr>'+
                '<tr>'+
                    '<td colspan="2"><span class="text-muted"><small>{{nivel}}</small></span>'+
                    '<span class="pull-right"><small><span class="{{seleccionado}}"></span> {{seleccionadoLabel}}</small></span>'+
                    '</td>'+
                '</tr>'+
            '</table>'+
        '</div>');
}

function crear_objeto_indicador(datos){
    var clasesEstatus = {1:'info',2:'warning',3:'danger',4:'primary',5:'success'};
    var niveles =  {'F':'Fin','P':'Propósito','C':'Componente','A':'Actividad'};
    return $.map(datos, function (object) {
        return {
            claveNivel: object.claveNivel,
            nivel: niveles[object.claveNivel],
            indicador: object.indicador,
            claseEstatus: clasesEstatus[object.idEstatus],
            estatus: object.estatus,
            seleccionado: (ids_indicadores_seleccionados[object.id])?'fa fa-check':'',
            seleccionadoLabel: (ids_indicadores_seleccionados[object.id])?'Seleccionado':'',
            id: object.id
        };
    });
}

/*

        Funciones para la busqueda de Estrategias Institucionales por medio del suggester (Typeahead)

*/

var estrategiaTypeAhead = new Bloodhound({
    datumTokenizer: function (d) { return Bloodhound.tokenizers.whitespace(d.text); },
    queryTokenizer: Bloodhound.tokenizers.whitespace,
    limit: 100,
    remote: {
        url: SERVER_HOST+'/v1/estrategia-institucional?typeahead=1&buscar=%QUERY',
        filter: function ( response ) {
            return crear_objeto_estrategia(response.data);
        },
        ajax:{
            beforeSend: function(jqXHR,settings){
                if($('#departamento').val()){
                    settings.url = settings.url + '&departamento='+$('#departamento').val();
                }
                if($('#id').val()){
                    settings.url = settings.url + '&usuario='+$('#id').val();
                }
                $('#estatus-busqueda-estrategia').html('Buscando... <span class="fa fa-spinner fa-spin"></span>');
            },
            complete: function(jqXHR,textStatus){
                $('#estatus-busqueda-estrategia').html(jqXHR.responseJSON.resultados + ' Indicadores Encontrados');
                estrategiaTypeAhead.clearRemoteCache();
            }
        }
    }
});
estrategiaTypeAhead.initialize();

$('#buscar-estrategia').typeahead(null,{
    minLength: 3,
    displayKey: 'estrategia',
    source: estrategiaTypeAhead.ttAdapter(),
    templates: { 
        empty: '<div class="empty-result">No se encontraron Indicadores</div>',
        suggestion: obtener_template_estrategia()
    }
}).on('typeahead:selected', function (object, datum) {
    if(!$('#tabla-lista-estrategias tbody tr[data-id="'+datum.id+'"]').length){
        var html_row = obtener_html_tabla_estrategias(datum);
        if($('#tr-estrategias-vacio').length){
            $('#tr-estrategias-vacio').remove();
        }
        $('#tabla-lista-estrategias tbody').append(html_row);
        $('#conteo-estrategias-seleccionados').text($('#tabla-lista-estrategias tbody tr').length);
    }
    reset_busqueda();
    estrategiaTypeAhead.clearRemoteCache();
    ids_estrategias_seleccionados[datum.id] = 1;
}).on('typeahead:cursorchanged', function (object, datum){
    $('.tt-suggestion.tt-suggestion-selected').removeClass('tt-suggestion-selected');
    $('#suggest_'+datum.id).parents('.tt-suggestion').addClass('tt-suggestion-selected');
});

function quitar_estrategia(id){
    $('#tabla-lista-estrategias tbody tr[data-id="'+id+'"]').remove();
    if(!$('#tabla-lista-estrategias tbody tr').length){
        $('#tabla-lista-estrategias tbody').html('<tr id="tr-estrategias-vacio"><td colspan="3"><span class="fa fa-info-circle"></span> No hay indicadores asignados</td></tr>');
        $('#conteo-estrategias-seleccionados').text('0');
    }else{
        $('#conteo-estrategias-seleccionados').text($('#tabla-lista-estrategias tbody tr').length);
    }
    delete ids_estrategias_seleccionados[id];
}

function limpiar_lista_estrategias(){
    $('#tabla-lista-estrategias tbody').empty();
    $('#tabla-lista-estrategias tbody').html('<tr id="tr-estrategias-vacio"><td colspan="3"><span class="fa fa-info-circle"></span> No hay indicadores asignados</td></tr>');
    $('#conteo-estrategias-seleccionados').text('0');
    ids_estrategias_seleccionados = {};
}

function llenar_lista_estrategias(datos){
    var lista_estrategias = '';
    var estrategias = crear_objeto_estrategia(datos);
    for(var i in estrategias){
        lista_estrategias += obtener_html_tabla_estrategias(estrategias[i]);
        ids_estrategias_seleccionados[estrategias[i].id] = 1;
    }
    if($('#tr-estrategias-vacio').length){
        $('#tr-estrategias-vacio').remove();
    }
    $('#tabla-lista-estrategias tbody').append(lista_estrategias);
    $('#conteo-estrategias-seleccionados').text(datos.length);
}

function obtener_html_tabla_estrategias(estrategia){
    console.log(estrategia);
    var html_row = '<tr data-id="'+estrategia.id+'" >';//data-clave-nivel="'+estrategia.claveNivel+'"
    html_row += '<td><input type="hidden" name="estrategias[]" value="'+estrategia.id+'">'+estrategia.descripcionIndicador+'</td>';
    html_row += '<td><button type="button" class="btn btn-danger btn-block" onClick="quitar_estrategia('+estrategia.id+')"><span class="fa fa-trash"></span></button></td>';
    html_row += '</tr>';
    return html_row;
}

function estrategia_seleccionado(estrategia){
    var template = obtener_template_estrategia();
    return template(estrategia);
}

function obtener_template_estrategia(){
    return Handlebars.compile('<div id="suggest_{{id}}" class="item">'+
            '<table width="100%" border="0" cellpadding="0" cellspacing="0">'+
                '<tr>'+
                    '<td rowspan="2" class="text-{{claseEstatus}}"><b>{{descripcionIndicador}}</b></td>'+
                    '<td width="100px" class="label-{{claseEstatus}}"><span class="label">{{estatus}}</span></td>'+
                '</tr>'+
                '<tr>'+
                    '<td>'+
                    '<span class="pull-right"><small><span class="{{seleccionado}}"></span> {{seleccionadoLabel}}</small>&nbsp;</span>'+
                    '</td>'+
                '</tr>'+
            '</table>'+
        '</div>');
}

function crear_objeto_estrategia(datos){
    console.log(datos);
    var clasesEstatus = {1:'info',2:'warning',3:'danger',4:'primary',5:'success'};
    return $.map(datos, function (object) {
        return {
            descripcionIndicador: object.descripcionIndicador,
            claseEstatus: clasesEstatus[object.idEstatus],
            estatus: object.estatus,
            seleccionado: (ids_estrategias_seleccionados[object.id])?'fa fa-check':'',
            seleccionadoLabel: (ids_estrategias_seleccionados[object.id])?'Seleccionado':'',
            id: object.id
        };
    });
}

$('.popover-dismiss').popover({ placement:'top', trigger:'hover' });