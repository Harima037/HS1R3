
/*=====================================

    # Nombre:
        proyectos.js

    # Módulo:
        poa/proyectos

    # Descripción:
        Se utiliza para crear, editar y eliminar caratulas de captura para proyectos de inversión e institucionales

=====================================*/

// Inicialización General para casi cualquier módulo
var moduloResource = new RESTfulRequests(SERVER_HOST+'/v1/proyectos');
var moduloDatagrid = new Datagrid("#datagridCaratulas",moduloResource);
moduloDatagrid.init();
moduloDatagrid.actualizar();
var modal_name = '#modalCaratulas';
var form_name = '#form_caratula';
/*===================================*/
// Implementación personalizada del módulo
function editar (e){
    moduloResource.get(e,null,{
        _success: function(response){
            var titulo_modal = response.data.clasificacion_proyecto.descripcion + ' <small>' + response.data.tipo_proyecto.descripcion + '</small>';
            $(modal_name).find(".modal-title").html(titulo_modal);

            $('#clasificacion_proyecto').val(response.data.clasificacion_proyecto.id);
            $('#tipo_proyecto').val(response.data.tipo_proyecto.id);

            $('#lbl_nombre_tecnico').text(response.data.nombreTecnico);

            var clave = response.data.unidadResponsable + response.data.finalidad + response.data.funcion + response.data.subFuncion +
                        response.data.subSubFuncion + response.data.programaSectorial + response.data.programaPresupuestario +
                        response.data.programaEspecial + response.data.actividadInstitucional + response.data.proyectoEstrategico +
                        response.data.numeroProyectoEstrategico;

            $('#lbl_clave_presupuestaria').text(clave);
            $('#lbl_unidad_responsable').text(response.data.datos_unidad_responsable.clave + ' - ' + response.data.datos_unidad_responsable.descripcion);
            $('#lbl_finalidad').text(response.data.datos_finalidad.clave + ' - ' + response.data.datos_finalidad.descripcion);
            $('#lbl_funcion').text(response.data.datos_funcion.clave + ' - ' + response.data.datos_funcion.descripcion);
            $('#lbl_sub_funcion').text(response.data.datos_sub_funcion.clave + ' - ' + response.data.datos_sub_funcion.descripcion);
            $('#lbl_sub_sub_funcion').text(response.data.datos_sub_sub_funcion.clave + ' - ' + response.data.datos_sub_sub_funcion.descripcion);
            $('#lbl_programa_sectorial').text(response.data.datos_programa_sectorial.clave + ' - ' + response.data.datos_programa_sectorial.descripcion);
            $('#lbl_programa_presupuestario').text(response.data.datos_programa_presupuestario.clave + ' - ' + response.data.datos_programa_presupuestario.descripcion);
            $('#lbl_programa_especial').text(response.data.datos_programa_especial.clave + ' - ' + response.data.datos_programa_especial.descripcion);
            $('#lbl_actividad_institucional').text(response.data.datos_actividad_institucional.clave + ' - ' + response.data.datos_actividad_institucional.descripcion);
            $('#lbl_proyecto_estrategico').text(response.data.datos_proyecto_estrategico.clave + ' - ' + response.data.datos_proyecto_estrategico.descripcion + response.data.numeroProyectoEstrategico);

            $('#lbl_cobertura').text(response.data.cobertura.descripcion);
            $('#lbl_tipo_accion').text(response.data.tipo_accion.descripcion);

            $('#lbl_vinculacion_ped').text(response.data.objetivo_ped.descripcion);

            $('#lbl_tipo_beneficiario').text(response.data.tipo_beneficiario.descripcion);
            $('#lbl_total_beneficiarios').text(response.data.totalBeneficiarios);
            $('#lbl_beneficiarios_f').text(response.data.totalBeneficiariosF);
            $('#lbl_beneficiarios_m').text(response.data.totalBeneficiariosM);

            var indx;
            var sexo;
            for( indx in response.data.beneficiarios ){
                sexo = response.data.beneficiarios[indx].sexo;
                $('#lbl_benef_urbana_'+sexo).text(response.data.beneficiarios[indx].urbana);
                $('#lbl_benef_rural_'+sexo).text(response.data.beneficiarios[indx].rural);
                $('#lbl_benef_mestiza_'+sexo).text(response.data.beneficiarios[indx].mestiza);
                $('#lbl_benef_indigena_'+sexo).text(response.data.beneficiarios[indx].indigena);
                $('#lbl_benef_inmigrante_'+sexo).text(response.data.beneficiarios[indx].inmigrante);
                $('#lbl_benef_otros_'+sexo).text(response.data.beneficiarios[indx].otros);
                $('#lbl_benef_muy_alta_'+sexo).text(response.data.beneficiarios[indx].muyAlta);
                $('#lbl_benef_alta_'+sexo).text(response.data.beneficiarios[indx].alta);
                $('#lbl_benef_media_'+sexo).text(response.data.beneficiarios[indx].media);
                $('#lbl_benef_baja_'+sexo).text(response.data.beneficiarios[indx].baja);
                $('#lbl_benef_muy_baja_'+sexo).text(response.data.beneficiarios[indx].muyBaja);
            }

            $('#id').val(response.data.id);

            $(modal_name).modal('show');
            $('#datos-formulario').hide();
            $('#datos-proyecto').show();
        }
    });
}

$('#clasificacion_proyecto').on('change',function(){
    if($(this).val() == 2){
        $('#orden_fibap label').removeClass('active');
        $('#fibap_despues').prop('checked',true);
        $('#fibap_despues').parent().addClass('active');
        $('#opciones_fibap').removeClass('hidden');
    }else{
        $('#opciones_fibap').addClass('hidden');
    }
});

/*$('#btn-mostrar-desgloce').on('click',function(){
    //$('#clave-desgloce').show();
});*/

/*===================================*/
// Configuración General para cualquier módulo

$(modal_name).on('shown.bs.modal', function () {
    $(modal_name).find('input').eq(0).focus();
});

$(modal_name).on('hide.bs.modal',function(e){ 
    resetModalModuloForm();
});

$('.btn-datagrid-agregar').on('click', function () {
    $(modal_name).find(".modal-title").html("Nuevo Proyecto");
    $(modal_name).modal('show');
    $('#datos-formulario').show();
    $('#datos-proyecto').hide();
});

$(modal_name+' .btn-guardar-continuar').on('click', function (e) {
    e.preventDefault();
    submitModulo(true);
});

$(modal_name+' .btn-guardar').on('click', function (e) {
    e.preventDefault();
    submitModulo();
});

function resetModalModuloForm(){
    $(form_name).get(0).reset();
    $('#opciones_fibap').addClass('hidden');
    $('#orden_fibap label').removeClass('active');
    $('#fibap_despues').parent().addClass('active');
    $(form_name +' #id').val("");
}

function submitModulo(save_next){
    if($('#clasificacion_proyecto').val() == 2){
        if($('#fibap_antes').prop('checked')){
            //TODO: Load FIBAP form -->
            return;
        }
    }
    $(form_name).attr('action',SERVER_HOST+'/poa/caratula');
    $(form_name).attr('method','POST');
    $(form_name).submit();
}