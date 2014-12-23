
/*=====================================

    # Nombre:
        fibap.js

    # Módulo:
        expediente/fibap

    # Descripción:
        Para el formulario de captura de la Ficha de Información Básica del Proyecto de Inversión

=====================================*/
// Declaracion de variables
var moduloResource = new RESTfulRequests(SERVER_HOST+'/v1/fibap');
var proyectoResource = new RESTfulRequests(SERVER_HOST+'/v1/proyectos');
var moduloDatagrid = new Datagrid("#datagridFibaps",moduloResource);
moduloDatagrid.init();
moduloDatagrid.actualizar();
var modal_name = '#modal-fibap';
var form_name = '#form-fibap';

/*===================================*/
// Configuración General para cualquier módulo
function editar(e){
    $('#opciones_fibap').hide();
    $('#editar_fibap').show();
    $('#id').val(e);
    $(modal_name).find(".modal-title").html("Editar FIBAP");
    $(modal_name).modal('show');
};

$(modal_name).on('shown.bs.modal', function () {
    $(modal_name).find('input').eq(0).focus();
});

$(modal_name).on('hide.bs.modal',function(e){ 
    resetModalModuloForm();
});

$('#btn-cargar-proyectos').on('click',function(){
    var parametros = {proyectos_inversion:1};
    proyectoResource.get(null,parametros,{
        _success: function(response){
            $('#lista-proyectos').empty();
            var lista_radios = '';
            for(var i in response.data){
                lista_radios += '<div><label><input type="radio" name="proyecto-id" value="'+response.data[i].id+'" > <span class="fa fa-file"></span> ' + response.data[i].nombreTecnico + ' ['+response.data[i].clavePresup+']</label></div>';
            }
            $('#lista-proyectos').html(lista_radios);
        }
    })
});

$('.btn-datagrid-agregar').on('click', function () {
    $('#opciones_fibap').show();
    $('#editar_fibap').hide();
    $(modal_name).find(".modal-title").html("Nuevo FIBAP");
    $(modal_name).modal('show');
});

$(modal_name+' .btn-guardar').on('click', function (e) {
    e.preventDefault();
    submitModulo();
});

function resetModalModuloForm(){
    //Validation.cleanFormErrors(form_name);
    $(form_name).get(0).reset();
    //$(form_name +' #id').val("");
}

function submitModulo(save_next){
    $(form_name).attr('action',SERVER_HOST+'/expediente/formulario-fibap');
    $(form_name).attr('method','POST');
    $(form_name).submit();
}