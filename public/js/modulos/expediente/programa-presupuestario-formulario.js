/*=====================================

    # Nombre:
        programa-presupuestario-formulario.js

    # Módulo:
        expediente/programa-presupuestario-captura

    # Descripción:
        Se utiliza para crear, editar y eliminar los indicadores de los programas presupuestarios

=====================================*/

// Inicialización General para casi cualquier módulo
var moduloResource = new RESTfulRequests(SERVER_HOST+'/v1/programas-presupuestarios');
var moduloDatagrid = new Datagrid("#datagridIndicadores",moduloResource);
//moduloDatagrid.init();
//moduloDatagrid.actualizar();
var modal_indicador = '#modal_programa_indicador';
var modal_problema = '#modal_problema';
var modal_objetivo = '#modal_objetivo';
var form_programa = '#form_programa_datos';
var form_indicador = '#form_programa';

$('.chosen-one').chosen({width:'100%'});

/****************************************************************** Funciones de modales ********************************************************************/
$(modal_indicador).on('hide.bs.modal',function(e){
    reset_modal_form(form_indicador);
});

/****************************************************************** Funciones de datagrids ********************************************************************/
$('#datagridIndicadores .btn-datagrid-agregar').on('click', function () {
    $(modal_indicador).find(".modal-title").html("Nuevo Indicador");
    $(modal_indicador).modal('show');
});

$('#datagridProblemas .btn-datagrid-agregar').on('click', function () {
    $(modal_problema).find(".modal-title").html("Nueva Causa/Efecto");
    $(modal_problema).modal('show');
});

$('#datagridObjetivos .btn-datagrid-agregar').on('click', function () {
    $(modal_objetivo).find(".modal-title").html("Nuevo Medio/Fin");
    $(modal_objetivo).modal('show');
});

/****************************************************************** Funciones de Botones ********************************************************************/
$('#btn-programa-guardar').on('click',function(){
    Validation.cleanFormErrors(form_programa);
    var parametros = $(form_programa).serialize();
    parametros += '&guardar=programa';

    if($('#id').val()){
        moduloResource.put($('#id').val(),parametros,{
            _success: function(response){
                //
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
        moduloResource.post(parametros,{
            _success: function(response){
                //
                $('#id').val(response.id);
                $('#tab-link-diagnostico').attr('data-toggle','tab');
                $('#tab-link-diagnostico').parent().removeClass('disabled');
                $('#tab-link-indicadores').attr('data-toggle','tab');
                $('#tab-link-indicadores').parent().removeClass('disabled');
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

$('#btn-programa-cancelar').on('click',function(){
    window.location.href = SERVER_HOST+'/expediente/programas-presupuestarios';
});

function reset_modal_form(formulario){
    $(formulario).get(0).reset();
    $(formulario + ' input[type="hidden"]').val('');
    Validation.cleanFormErrors(formulario);
    $(formulario + ' .chosen-one').trigger('chosen:updated');

    if(formulario == form_indicador){
        $('#tipo-indicador').val('');
        $('#tipo-indicador').trigger('chosen:updated');
    }
}