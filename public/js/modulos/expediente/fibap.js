
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
    moduloResource.get(e,null,{
        _success: function(response){
            console.log(response);


            $('#lbl-tipo-proyecto').text(response.data.datos_proyecto_completo.tipo_proyecto.descripcion);
            $('#lbl-proyecto').text(response.data.datos_proyecto_completo.nombreTecnico);
            $('#lbl-justificacion-proyecto').text(response.data.justificacionProyecto);
            $('#lbl-descripcion-proyecto').text(response.data.descripcionProyecto);
            $('#lbl-programa-presupuestario').text(response.data.datos_proyecto_completo.datos_programa_presupuestario.descripcion);
            $('#lbl-alineacion-ped').text(response.data.datos_proyecto_completo.objetivo_ped.descripcion);
            $('#lbl-alineacion-especifica').html(response.data.alineacionEspecifica);
            $('#lbl-alineacion-general').html(response.data.alineacionGeneral || '&nbsp;');
            $('#lbl-organismo-publico').text(response.data.organismoPublico);
            $('#lbl-sector').text(response.data.sector);
            $('#lbl-subcomite').text(response.data.subcomite);
            $('#lbl-grupo-trabajo').text(response.data.grupoTrabajo);
            $('#lbl-cobertura-municipio').text(response.data.datos_proyecto_completo.cobertura.descripcion + '/moa');
            $('#lbl-tipo-beneficiario').text(response.data.datos_proyecto_completo.tipo_beneficiario.descripcion);
            $('#lbl-beneficiario-f').text(response.data.datos_proyecto_completo.totalBeneficiariosF);
            $('#lbl-beneficiario-m').text(response.data.datos_proyecto_completo.totalBeneficiariosM);
            $('#lbl-total-beneficiario').text(response.data.datos_proyecto_completo.totalBeneficiarios);

            //$('#lbl-lista-documentos').text(response.data);

            $('#id').val(response.data.id);
            $(modal_name).find(".modal-title").html("Editar FIBAP");
            $(modal_name).modal('show');
        }
    });
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
    });
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
    $(form_name +' #id').val("");
}

function submitModulo(save_next){
    $(form_name).attr('action',SERVER_HOST+'/expediente/formulario-fibap');
    $(form_name).attr('method','POST');
    $(form_name).submit();
}