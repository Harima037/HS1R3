/*=====================================

    # Nombre:
        roles.js

    # Módulo:
        administrador/roles

    # Descripción:
        Se utiliza para crear, editar y eliminar roles de usuario

=====================================*/

// Inicialización General para casi cualquier módulo

var permisos = [];
var responsablesResource = new RESTfulRequests(SERVER_HOST+'/v1/lideres-proyectos');
var areasResource = new RESTfulRequests(SERVER_HOST+'/v1/buscar-responsables-area');
var responsablesDatagrid = new Datagrid("#datagridResponsables",responsablesResource);
responsablesDatagrid.init();
responsablesDatagrid.parametros.filtro_activos = $('#filtro_activos').val();
responsablesDatagrid.actualizar({
    _success: function(response){
        responsablesDatagrid.limpiar();
        var datos_grid = [];
        for(var i in response.data){
            var item = response.data[i];
            if(!item.fechaFin){
                item.fechaFin = '-';
            }
            datos_grid.push(item);
        }
        responsablesDatagrid.cargarDatos(datos_grid);                         
        responsablesDatagrid.cargarTotalResultados(response.resultados,'<b>Responsables(s)</b>');
        var total = parseInt(response.resultados/responsablesDatagrid.rxpag); 
        var plus = parseInt(response.resultados)%responsablesDatagrid.rxpag;
        if(plus>0) 
            total++;
        responsablesDatagrid.paginacion(total);
    }
});

$('#modalRol').on('shown.bs.modal', function () {
    $('#modalRol').find('input').eq(0).focus();
});
$('#modalRol').on('show.bs.modal', function () {
    Validation.cleanFormErrors("#formRol");
    MessageManager.dismissAlert('#modalRol .modal-body');
});

/*===================================*/
// Implementación personalizada del módulo

function editar (e){
    responsablesResource.get(e,null,{
        _success: function(response){

            $('#btn-nuevo-cargo').hide();
            $('#btn-terminar-cargo').hide();

            $('#formRol #id').val(response.data.id);
            $('#formRol #name').val(response.data.nombre);
            $('#formRol #email').val(response.data.email);
            $('#formRol #cargo').val(response.data.cargo);

            var extensiones = response.data.telefono.split(', ');
            var telefono = extensiones[0];
            extensiones.shift();
            
            $('#formRol #telefono').val(telefono);
            $('#formRol #extension').val(extensiones.join(', '));
            $('#formRol #area').val(response.data.idArea);
            $('#formRol #fecha_inicio').val(response.data.fechaInicio);

            if(response.data.fechaFin){
                $('#formRol #fecha_fin').val(response.data.fechaFin);
            }else{
                $('#formRol #fecha_fin').attr('disabled',true);
            }
            
            
            if(response.data.fechaFin){
                $('#btn-nuevo-cargo').show();
            }else{
                $('#btn-terminar-cargo').show();
            }

            $('#modalRol').find(".modal-title").html("Editar Líder de Proyecto");
            $('#modalRol').modal('show');
        }
    }); 
}

$('#area').on('change',function(){
    $('#panel-cargando-cargo-ocupado').removeClass('hidden');
    var area_id = $('#area').val();
    areasResource.get(area_id,null,{
        _success: function(response){
            console.log(response);
            var nombres = [];
            for (let index = 0; index < response.data.length; index++) {
                nombres.push(response.data[index].nombre);
            }
            $('#panel-cargando-cargo-ocupado').addClass('hidden');
            $('#nombre-persona-asignada').html(nombres.join(', '));
            $('#panel-cargo-ocupado').removeClass('hidden');
            //var extensiones = response.data.telefono.split(', ');
            
        }
    }); 
});

$("#datagridResponsables .txt-quick-search").off('keydown');
$("#datagridResponsables .txt-quick-search").on('keydown', function(event){
    if (event.which == 13) {
        realizar_busqueda();
    }
});

$('#datagridResponsables .btn-quick-search').off('click');
$('#datagridResponsables .btn-quick-search').on('click',function(){
    realizar_busqueda();
})

function realizar_busqueda(){
    responsablesDatagrid.setPagina(1);
    responsablesDatagrid.parametros.buscar = $('.txt-quick-search').val();
    responsablesDatagrid.parametros.filtro_activos = $('#filtro_activos').val();
    responsablesDatagrid.actualizar();
}

/*===================================*/
// Configuración General para cualquier módulo

$('#modalRol').on('shown.bs.modal', function () {
    $('#modalRol').find('input').eq(0).focus();
});

$('#modalRol').on('hide.bs.modal', function () {
    resetModalRolForm();
});

$('#btnRolAgregar').on('click', function () {
    $('#modalRol').find(".modal-title").html("Nuevo Rol");    
    $('#modalRol').modal('show');
});

$('#modalRol .btn-guardar').on('click', function (e) {
    e.preventDefault();
    submitModulo();
});   

$("#formRol").on('submit',function(e){
    e.preventDefault();
    submitModulo();
});

function submitModulo(){
    
    Validation.cleanFormErrors("#formRol");
    MessageManager.dismissAlert('body');

    var parametros = $("#formRol").serialize();
    if($('#formRol #id').val()==""){
        responsablesResource.post(parametros,{
                        _success: function(response){
                            responsablesDatagrid.actualizar();
                            MessageManager.show({data:'Rol creado con éxito',container:'#modalRol .modal-body',type:'OK',timer:4});
                            $('#formRol #id').val(response.data.id);
                        },
                        _error: function(response){
                            try{
                                var json = $.parseJSON(response.responseText);
                                if(!json.code)
                                    MessageManager.show({code:'S03',data:"Hubo un problema al realizar la transacción, inténtelo de nuevo o contacte con soporte técnico."});
                                else{
                                    json.container = '#modalRol .modal-body';
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
        responsablesResource.put($('#formRol #id').val(), parametros,{
                        _success: function(response){
                            responsablesDatagrid.actualizar();
                            MessageManager.show({data:'Líder de proyecto actualizado con éxito',container:'#modalRol .modal-body',type:'OK',timer:4});
                        },
                        _error: function(response){
                            try{
                                var json = $.parseJSON(response.responseText);
                                if(!json.code)
                                    MessageManager.show({code:'S03',data:"Hubo un problema al realizar la transacción, inténtelo de nuevo o contacte con soporte técnico."});
                                else{
                                    json.container = '#modalRol .modal-body';
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

$('#btn-show-usuarios').click(function(){
    if($('span',this).hasClass('glyphicon-chevron-down')){
        $('span',this).removeClass('glyphicon-chevron-down');
        $('span',this).addClass('glyphicon-chevron-up');
    }else{
        $('span',this).removeClass('glyphicon-chevron-up');
        $('span',this).addClass('glyphicon-chevron-down');
    }
    $('table','#pnlUsuarios').toggle();
});

function resetModalRolForm(){
    $('#formRol').get(0).reset();
    $('#formRol #id').val("");
    $('#pnlPermissions').empty();
    $('#pnlUsuarios').parent().parent().addClass('hidden');
}

// Funciones de permisos

$('#btn-cargar-cat-permisos').click(function(){
    var parametros = {};
    if($('#formRol #id').val() != ''){
        parametros.roles = [$('#formRol #id').val()];
    }
    catalogoPermisosResource.get(null,parametros,{
        _success: function(response){
                // Cambiamos roles a usuario para formatear colores y no confundir al usuario
                // Debido a que el color cyan y rojo tienen otro comportamiento en el modulo de usuarios              
                response.data.rol = {};
                response.data.user = permisos;
              
                cargarCatalogoPermisos(response);
                $('#modalCatalogoPermisos').modal('show');
            }
    });
});

$('#modalRol #btn-limpiar-permisos').on('click',function(e){
    e.preventDefault();
    cleanPermissionPanel();
});

function cleanPermissionPanel(){
    permisos = {};
    $('#formRol #pnlPermissions').html('<tr><td>Aún no hay permisos asignados.</td></tr>');
}

$('#modalCatalogoPermisos .btn-seleccionar').on('click',function(e){
    e.preventDefault();
    permisos = parsePermisos();
    buildPermissionPanel(permisos);
    $('#modalCatalogoPermisos').modal('hide');
});


function buildUsuariosPanel(usuarios){
    var html_usuarios = '';

    for(i in usuarios){
        html_usuarios += '<tr><td>'+usuarios[i].username+'</td>';
        html_usuarios += '<td>'+usuarios[i].nombres + ' ' + usuarios[i].apellidoPaterno + ' ' + usuarios[i].apellidoMaterno +'</td>';
        html_usuarios += '</tr>';
    }

    if(html_usuarios != ''){
        $('#pnlUsuarios').parent().parent().removeClass('hidden');
        $('table tbody','#pnlUsuarios').empty();
        $('table tbody','#pnlUsuarios').html(html_usuarios);
    }else{
        $('table tbody','#pnlUsuarios').empty();
        $('#pnlUsuarios').parent().parent().addClass('hidden');
    }
    
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
    
    $('#formRol #pnlPermissions').html('<tr><td>Aún no hay permisos individuales asignados.</td></tr>');
    $('#formRol #pnlPermissions').html(html_permissions);
}