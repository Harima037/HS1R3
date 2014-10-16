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
var rolesResource = new RESTfulRequests(SERVER_HOST+'/v1/roles');
var rolesDatagrid = new Datagrid("#datagridRoles",rolesResource);
rolesDatagrid.init();
rolesDatagrid.actualizar();

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

    $('#modalRol').modal('show');
    rolesResource.get(e,null,{
                    _success: function(response){

                        $('#formRol #id').val(response.data.id);
                        $('#formRol #name').val(response.data.name);

                        permisos = response.data.permisos_array;

                        var parametros = {};
                        if($('#formRol #id').val() != ''){
                            parametros.roles = [$('#formRol #id').val()];
                        }
                        catalogoPermisosResource.get(null,parametros,{
                            _success: function(response){
                                    
                                    // Cambiamos roles a usuario para formatear colores y no confundir al usuario
                                    // Debido a que el color cyan y rojo tienen otro comportamiento en el modulo de usuarios
                                    permisos = response.data.rol;
                                    response.data.rol = {};
                                    response.data.user = permisos;
                                  
                                    cargarCatalogoPermisos(response);
                                    permisos = parsePermisos();                                    
                                    buildPermissionPanel(permisos);
                                }
                        });                       

                        buildUsuariosPanel(response.data.usuarios_array);

                        $('#modalRol').find(".modal-title").html("Editar Rol");
                        $('#modalRol').modal('show');
                    }
    }); 
}

/*===================================*/
// Configuración General para cualquier módulo

$('#modalRol').on('shown.bs.modal', function () {
    $('#modalRol').find('input').eq(0).focus();
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
        rolesResource.post(parametros,{
                        _success: function(response){
                            rolesDatagrid.actualizar();
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
        rolesResource.put($('#formRol #id').val(), parametros,{
                        _success: function(response){
                            rolesDatagrid.actualizar();
                            MessageManager.show({data:'Rol actualizado con éxito',container:'#modalRol .modal-body',type:'OK',timer:4});
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
        html_usuarios += '<td>'+usuarios[i].first_name+'</td>';
        html_usuarios += '<td>'+usuarios[i].last_name+'</td></tr>';
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