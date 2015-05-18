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

$('#modalModulo').on('shown.bs.modal', function () {
    $('#modalModulo').find('input').eq(0).focus();
});
$('#modalModulo').on('show.bs.modal', function () {
    Validation.cleanFormErrors("#formModulo");
    MessageManager.dismissAlert('#modalModulo .modal-body');
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
// Configuración General para cualquier módulo

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
}

// Funciones de permisos

$('#modalModulo #btn-limpiar-permisos').on('click',function(e){
    e.preventDefault();
    cleanPermissionPanel();
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