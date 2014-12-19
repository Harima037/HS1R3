
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
    var parametros = {ver:'proyecto'};
    moduloResource.get(e,parametros,{
        _success: function(response){
            var titulo_modal = response.data.clasificacion_proyecto.descripcion + ' <small>' + response.data.tipo_proyecto.descripcion + '</small>';
            $(modal_name).find(".modal-title").html(titulo_modal);

            $('#clasificacion_proyecto').val(response.data.clasificacion_proyecto.id);
            $('#tipo_proyecto').val(response.data.tipo_proyecto.id);

            $('#lbl_nombre_tecnico').text(response.data.nombreTecnico);

            var clave = response.data.unidadResponsable + response.data.finalidad + response.data.funcion + response.data.subFuncion +
                        response.data.subSubFuncion + response.data.programaSectorial + response.data.programaPresupuestario +
                        response.data.programaEspecial + response.data.actividadInstitucional + response.data.proyectoEstrategico +
                        ("000" + response.data.numeroProyectoEstrategico).slice(-3);

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
            $('#lbl_proyecto_estrategico').text(response.data.datos_proyecto_estrategico.clave + ' - ' + response.data.datos_proyecto_estrategico.descripcion);

            var cobertura = response.data.cobertura.descripcion;

            if(response.data.claveMunicipio){
                cobertura = cobertura + ' <small class="text-capitalize">('+response.data.municipio.nombre+')</small>';
            }else if(response.data.claveRegion){
                cobertura = cobertura + ' <small class="text-capitalize">('+response.data.region.nombre+')</small>';
            }

            $('#lbl_cobertura').html(cobertura);
            $('#lbl_tipo_accion').text(response.data.tipo_accion.descripcion);

            $('#lbl_vinculacion_ped').text(response.data.objetivo_ped.descripcion);

            $('#lbl_tipo_beneficiario').text(response.data.tipo_beneficiario.descripcion);
            $('#lbl_total_beneficiarios').text(response.data.totalBeneficiarios.format());
            $('#lbl_beneficiarios_f').text(response.data.totalBeneficiariosF.format());
            $('#lbl_beneficiarios_m').text(response.data.totalBeneficiariosM.format());

            var indx;
            var sexo;
            for( indx in response.data.beneficiarios ){
                sexo = response.data.beneficiarios[indx].sexo;
                $('#lbl_benef_urbana_'+sexo).text(response.data.beneficiarios[indx].urbana.format());
                $('#lbl_benef_rural_'+sexo).text(response.data.beneficiarios[indx].rural.format());
                $('#lbl_benef_mestiza_'+sexo).text(response.data.beneficiarios[indx].mestiza.format());
                $('#lbl_benef_indigena_'+sexo).text(response.data.beneficiarios[indx].indigena.format());
                $('#lbl_benef_inmigrante_'+sexo).text(response.data.beneficiarios[indx].inmigrante.format());
                $('#lbl_benef_otros_'+sexo).text(response.data.beneficiarios[indx].otros.format());
                $('#lbl_benef_muy_alta_'+sexo).text(response.data.beneficiarios[indx].muyAlta.format());
                $('#lbl_benef_alta_'+sexo).text(response.data.beneficiarios[indx].alta.format());
                $('#lbl_benef_media_'+sexo).text(response.data.beneficiarios[indx].media.format());
                $('#lbl_benef_baja_'+sexo).text(response.data.beneficiarios[indx].baja.format());
                $('#lbl_benef_muy_baja_'+sexo).text(response.data.beneficiarios[indx].muyBaja.format());
            }

            $('#id').val(response.data.id);

            construir_panel_componentes(response.data.componentes);
            
            $('#datos-formulario').hide();
            $('#datos-proyecto').show();

            $(modal_name).modal('show');
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

$(modal_name+' .btn-guardar').on('click', function (e) {
    e.preventDefault();
    submitModulo();
});

function resetModalModuloForm(){
    Validation.cleanFormErrors(form_name);
    $(form_name).get(0).reset();
    $('#opciones_fibap').addClass('hidden');
    $('#orden_fibap label').removeClass('active');
    $('#fibap_despues').parent().addClass('active');
    $(form_name +' #id').val("");
}

function submitModulo(){
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

function construir_panel_componentes(componentes){
    var html = '<br><div class="panel-group" id="grupo_componentes" role="tablist" aria-multiselectable="true">'; //inico del panel
    for(indx in componentes){
        var actividades = '<div class="panel-group" id="grupo_actividades_'+componentes[indx].id+'" role="tablist" aria-multiselectable="true">'
        for(idx in componentes[indx].actividades){
            actividades += constructor_grupo_acordiones('grupo_actividades_'+componentes[indx].id,componentes[indx].actividades[idx],'actividad');
        }
        actividades += '</div>';

        var elemento_componente = constructor_grupo_acordiones('grupo_componentes',componentes[indx],'componente',actividades);
        html += elemento_componente;
    }
    html += '</div>'; //Fin del panel

    $('#tab-componente').html(html);
}

function constructor_grupo_acordiones(padre,item,tipo,contenido_extra){ //tipo = 'componente' o 'actividad', contenido = contenido extra (grupo de actividades)
    var clase_panel = 'success';
    if(tipo == 'componente'){
        clase_panel = 'info';
    }

    var contenido = '<div class="panel panel-'+clase_panel+'">';
    
    contenido += '<div class="panel-heading" role="tab" id="cabecera_'+tipo+'_'+item.id+'">';
    contenido += '<h4 class="panel-title">';
    contenido += '<a data-toggle="collapse" data-parent="#'+padre+'" href="#contenido_'+tipo+'_'+item.id+'" aria-controls="contenido_'+tipo+'_'+item.id+'">';
    contenido += item.objetivo;
    contenido += '</a>';
    contenido += '</h4>';
    contenido += '</div>';
    contenido += '<div id="contenido_'+tipo+'_'+item.id+'" class="panel-collapse collapse" role="tabpanel" aria-labelledby="cabecera_'+tipo+'_'+item.id+'">';
    contenido += '<div class="panel-body">';

    contenido += '<address>';
    contenido += '<div class="row">';
    contenido += '<div class="col-sm-7">';
    contenido += '<strong>Medios de Verificación:</strong> '+item.mediosVerificacion+'<br>';
    contenido += '<strong>Supuestos:</strong> '+item.supuestos;
    contenido += '</div>';
    if(item.accion){
        contenido += '<div class="col-sm-5">';
        contenido += '<strong>Entregable:</strong> '+item.entregable.descripcion+'<br>';
        contenido += '<strong>Tipo:</strong> '+item.tipo+'<br>';
        contenido += '<strong>Accion:</strong> '+item.accion;
        contenido += '</div>';
    }
    contenido += '</div>';

    contenido += '<hr style="margin-bottom:5px; margin-top:5px;">';

    contenido += '<div class="row">';
    contenido += '<div class="col-sm-6">';
    contenido += '<strong>Indicador:</strong> '+item.indicador+'<br>';
    contenido += '<strong>Numerador:</strong> '+item.numerador+'<br>';
    contenido += '<strong>Denominador:</strong> '+item.denominador+'<br>';
    contenido += '<strong>Interpretación:</strong> '+item.interpretacion+'<br>';
    contenido += '<strong>Meta Indicador:</strong> '+item.metaIndicador+'<br>';
    contenido += '</div>';
    contenido += '<div class="col-sm-6">';
    contenido += '<strong>Formula:</strong> '+item.formula.descripcion+'<br>';
    contenido += '<strong>Dimensión:</strong> '+item.dimension.descripcion+'<br>';
    contenido += '<strong>Frecuencia:</strong> '+item.frecuencia.descripcion+'<br>';
    contenido += '<strong>Tipo:</strong> '+item.tipo_indicador.descripcion+'<br>';
    contenido += '<strong>Unidad de Medida:</strong> '+item.unidad_medida.descripcion+'<br>';    
    contenido += '</div>';
    contenido += '</div>';

    contenido += '<hr style="margin-bottom:5px; margin-top:5px;">';

    contenido += '<div class="row">';
    contenido += '<div class="col-sm-3 col-xs-6">';
    contenido += '<strong>Trimestre 1:</strong> <br>'+item.numeroTrim1;
    contenido += '</div>';
    contenido += '<div class="col-sm-3 col-xs-6">';
    contenido += '<strong>Trimestre 2:</strong> <br>'+item.numeroTrim2;
    contenido += '</div>';
    contenido += '<div class="col-sm-3 col-xs-6">';
    contenido += '<strong>Trimestre 3:</strong> <br>'+item.numeroTrim3;
    contenido += '</div>';
    contenido += '<div class="col-sm-3 col-xs-6">';
    contenido += '<strong>Trimestre 4:</strong> <br>'+item.numeroTrim4;
    contenido += '</div>';
    contenido += '</div>';

    contenido += '<div class="row">';
    contenido += '<div class="col-sm-3 col-xs-6">';
    contenido += '<strong>Numerador:</strong> <br>'+item.valorNumerador;
    contenido += '</div>';
    contenido += '<div class="col-sm-3 col-xs-6">';
    contenido += '<strong>Denominador:</strong> <br>'+item.valorDenominador;
    contenido += '</div>';
    contenido += '<div class="col-sm-3 col-xs-6">';
    contenido += '<strong>Linea Base:</strong> <br>'+item.lineaBase;
    contenido += '</div>';
    contenido += '<div class="col-sm-3 col-xs-6">';
    contenido += '<strong>Año Base:</strong> <br>'+item.anioBase;
    contenido += '</div>';
    contenido += '</div>';

    contenido += '</address>';

    if(contenido_extra){
        contenido += '<br><strong>Actividades:</strong><br>';
        contenido += contenido_extra;
    }

    contenido += '</div>';
    contenido += '</div>';

    contenido += '</div>';

    return contenido;
}

/*             Extras               */
/**
 * Number.prototype.format(n, x)
 * 
 * @param integer n: length of decimal
 * @param integer x: length of sections
 */
Number.prototype.format = function(n, x) {
    var re = '\\d(?=(\\d{' + (x || 3) + '})+' + (n > 0 ? '\\.' : '$') + ')';
    return this.toFixed(Math.max(0, ~~n)).replace(new RegExp(re, 'g'), '$&,');
};