
/*=====================================

    # Nombre:
        revision-proyectos.js

    # Módulo:
        revision/proyectos

    # Descripción:
        Se utiliza para crear, editar y eliminar caratulas de captura para proyectos de inversión e institucionales

=====================================*/

// Inicialización General para casi cualquier módulo
//var fibapResource = new RESTfulRequests(SERVER_HOST+'/v1/fibap');
var moduloResource = new RESTfulRequests(SERVER_HOST+'/v1/revision-proyectos');
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

            $('#lbl_lider_proyecto').text((response.data.lider_proyecto)?response.data.lider_proyecto.nombre:'No asignado');
            $('#lbl_jefe_lider').text((response.data.jefe_inmediato)?response.data.jefe_inmediato.nombre:'No asignado');
            $('#lbl_jefe_ṕlaneacion').text((response.data.jefe_planeacion)?response.data.jefe_planeacion.nombre:'No asignado');
            $('#lbl_coordinador_grupo').text((response.data.coordinador_grupo_estrategico)?response.data.coordinador_grupo_estrategico.nombre:'No asignado');


            /*$('#lbl_tipo_beneficiario').text(response.data.tipo_beneficiario.descripcion);
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
            }*/

            $('#id').val(response.data.id);

            if(response.data.idClasificacionProyecto == 2){
                $('#tab-link-fibap').parent().removeClass('hidden');
                if(response.data.fibap){
                    $('#datos-capturados-fibap').show();
                    //$('#datos-alerta-fibap').hide();
                    llenar_datos_fibap(response.data.fibap);
                }else{
                    //$('#datos-capturados-fibap').hide();
                    //$('#datos-alerta-fibap').show();
                    //$('#proyecto-id').val(response.data.id);
                }
            }else{
                $('#tab-link-fibap').parent().addClass('hidden');
            }

            construir_panel_componentes(response.data.componentes);
            
            $('#datos-formulario').hide();
            $('#datos-proyecto').show();

            $('#proyecto-tab-panel-list a:first').tab('show');
            $(modal_name).modal('show');
        }
    });
}

function llenar_datos_fibap(fibap){
    $('#lbl-justificacion-proyecto').text(fibap.justificacionProyecto);
    $('#lbl-descripcion-proyecto').text(fibap.descripcionProyecto);
    $('#lbl-alineacion-especifica').html(fibap.alineacionEspecifica);
    $('#lbl-alineacion-general').html(fibap.alineacionGeneral || '&nbsp;');
    $('#lbl-organismo-publico').text(fibap.organismoPublico);
    $('#lbl-sector').text(fibap.sector);
    $('#lbl-subcomite').text(fibap.subcomite);
    $('#lbl-grupo-trabajo').text(fibap.grupoTrabajo);
    $('#lbl-resultados-obtenidos').text(fibap.resultadosObtenidos || '');
    $('#lbl-resultados-esperados').text(fibap.resultadosEsperados || '');
    var periodo_ejecucion = '';
    if(fibap.periodoEjecucionInicio){
        periodo_ejecucion = 'Del ' + fibap.periodoEjecucionInicio + ' al ' + fibap.periodoEjecucionFinal;
    }
    $('#lbl-periodo-ejecucion').text(periodo_ejecucion);

    var html_antecedentes = '';
    for(var i in fibap.antecedentes_financieros){
        var porcentaje = (fibap.antecedentes_financieros[i].ejercido * 100) / fibap.antecedentes_financieros[i].autorizado;
        html_antecedentes += '<tr>';
        html_antecedentes += '<td>' + fibap.antecedentes_financieros[i].anio + '</td>';
        html_antecedentes += '<td>' + fibap.antecedentes_financieros[i].autorizado.format() + '</td>';
        html_antecedentes += '<td>' + fibap.antecedentes_financieros[i].ejercido.format() + '</td>';
        html_antecedentes += '<td>' + parseFloat(porcentaje.toFixed(2)) + '% </td>';
        html_antecedentes += '<td>' + fibap.antecedentes_financieros[i].fechaCorte + '</td>';
        html_antecedentes += '</tr>';
    }
    $('#tabla-antecedentes > tbody').html(html_antecedentes);

    var presupuesto_requerido = fibap.presupuestoRequerido || 0;

    var html_distribucion = '';
    for(var i in fibap.distribucion_presupuesto_agrupado){
        var presupuesto = fibap.distribucion_presupuesto_agrupado[i];
        var porcentaje = (presupuesto.cantidad * 100) / presupuesto_requerido;
        html_distribucion += '<tr>';
        html_distribucion += '<td>' + presupuesto.objeto_gasto.clave + '</td>';
        html_distribucion += '<td>' + presupuesto.objeto_gasto.descripcion + '</td>';
        html_distribucion += '<td>' + presupuesto.cantidad.format() + '</td>';
        html_distribucion += '<td>' + parseFloat(porcentaje.toFixed(2)) + '% </td>';
        html_distribucion += '</tr>';
    }
    $('#tabla-distribucion > tbody').html(html_distribucion);

    $('.valores-origenes').text('0');

    for(var i in fibap.propuestas_financiamiento){
        $('#lbl-origen-'+fibap.propuestas_financiamiento[i].idOrigenFinanciamiento).text(fibap.propuestas_financiamiento[i].cantidad.format());
    }
        
    $('#lbl-presupuesto-requerido').text(presupuesto_requerido.format());
    var html_list = '';
    for(var indx in fibap.documentos){
        html_list += '<div class="col-sm-4"><span class="fa fa-file-o"></span> '+fibap.documentos[indx].descripcion+'</div>';
    }
    $('#lbl-lista-documentos').html(html_list);

}

$('#clasificacion_proyecto').on('change',function(){
    if($(this).val() == 2){
        $('#opciones_fibap').removeClass('hidden');
        $('#lista_fibap').addClass('hidden');
        $('#lista_fibap').empty();
    }else{
        $('#opciones_fibap').addClass('hidden');
        $('#lista_fibap').addClass('hidden');
        $('#lista_fibap').empty();
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
    $(modal_name).find(".modal-title").html("Revisión de proyecto");
    $(modal_name).modal('show');
    $('#datos-formulario').show();
    $('#datos-proyecto').hide();
});

/*$('#btn-capturar-nuevo-fibap').on('click',function(){
    window.location.href = "formulario-fibap";
});*/

$(modal_name+' .btn-guardar').on('click', function (e) {
    e.preventDefault();
    submitModulo();
});

/*$('#btn-capturar-fibap').on('click',function(){
    $('#nuevo-fibap-proyecto').attr('action',SERVER_HOST+'/expediente/formulario-fibap');
    $('#nuevo-fibap-proyecto').attr('method','POST');
    $('#nuevo-fibap-proyecto').submit();
});

$('#btn-seleccionar-fibap').on('click',function(){
    var parametros = {lista_fibap:1};
    fibapResource.get(null,parametros,{
        _success: function(response){
            $('#lista_fibap').empty();
            var lista_radios = '';
            for(var i in response.data){
                lista_radios += '<div><label><input type="radio" onChange="cambiar_tipo_proyecto('+response.data[i].idTipoProyecto+')" name="fibap-id" value="'+response.data[i].id+'" > <span class="fa fa-file"></span> ' + response.data[i].nombreTecnico + ' ['+response.data[i].tipoProyecto+'] <br><small>'+response.data[i].descripcionProyecto+'</small></label></div>';
            }
            if(lista_radios == ''){
                lista_radios += '<div><label>No se encontraron Fichas capturadas.</label></div>';
            }
            $('#lista_fibap').html(lista_radios);
            $('#opciones_fibap').addClass('hidden');
            $('#lista_fibap').removeClass('hidden');
        }
    })
});

function cambiar_tipo_proyecto(tipo_proyecto){
    $('#tipo_proyecto').val(tipo_proyecto);
}*/

function resetModalModuloForm(){
    Validation.cleanFormErrors(form_name);
    $(form_name).get(0).reset();
    $('#opciones_fibap').addClass('hidden');
    $('#lista_fibap').addClass('hidden');
    $('#lista_fibap').empty();
    $(form_name +' #id').val("");
}

function submitModulo(){
    $(form_name).attr('action',SERVER_HOST+'/revision/revision-caratula');
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
    contenido += '<span class="label label-default">Medios de Verificación :</span> '+item.mediosVerificacion+'<br>';
    contenido += '<span class="label label-default">Supuestos :</span> '+item.supuestos;
    contenido += '</div>';

    if(item.entregable){
        contenido += '<div class="col-sm-5">';
        contenido += '<span class="label label-default">Entregable :</span> '+item.entregable.descripcion+'<br>';
        
        if(item.entregable_tipo){
            contenido += '<span class="label label-default">Tipo :</span> '+item.entregable_tipo.descripcion+'<br>';
        }else{
            contenido += '<span class="label label-default">Tipo :</span> N / A <br>';
        }
        
        contenido += '<span class="label label-default">Acción :</span> '+item.entregable_accion.descripcion;
        contenido += '</div>';
    }

    contenido += '</div>';

    contenido += '<hr style="margin-bottom:5px; margin-top:5px;">';

    contenido += '<div class="row">';
    contenido += '<div class="col-sm-6">';
    contenido += '<span class="label label-default">Indicador :</span> '+item.indicador+'<br>';
    contenido += '<span class="label label-default">Numerador :</span> '+item.numerador+'<br>';
    contenido += '<span class="label label-default">Denominador :</span> '+item.denominador+'<br>';
    contenido += '<span class="label label-default">Interpretación :</span> '+item.interpretacion+'<br>';
    contenido += '<span class="label label-default">Meta Indicador :</span> '+item.metaIndicador+'<br>';
    contenido += '</div>';
    contenido += '<div class="col-sm-6">';
    contenido += '<span class="label label-default">Formula :</span> '+ ((item.formula)?item.formula.descripcion:'') +'<br>';
    contenido += '<span class="label label-default">Dimensión :</span> '+((item.dimension)?item.dimension.descripcion:'')+'<br>';
    contenido += '<span class="label label-default">Frecuencia :</span> '+((item.frecuencia)?item.frecuencia.descripcion:'')+'<br>';
    contenido += '<span class="label label-default">Tipo :</span> '+((item.tipo_indicador)?item.tipo_indicador.descripcion:'')+'<br>';
    contenido += '<span class="label label-default">Unidad de Medida :</span> '+((item.unidad_medida)?item.unidad_medida.descripcion:'')+'<br>';    
    contenido += '</div>';
    contenido += '</div>';

    contenido += '<hr style="margin-bottom:5px; margin-top:5px;">';

    contenido += '<div class="row">';
    contenido += '<div class="col-sm-3 col-xs-6">';
    contenido += '<span class="label label-default">Trimestre 1 :</span> <br>' + (item.numeroTrim1 || 0);
    contenido += '</div>';
    contenido += '<div class="col-sm-3 col-xs-6">';
    contenido += '<span class="label label-default">Trimestre 2 :</span> <br>' + (item.numeroTrim2 || 0);
    contenido += '</div>';
    contenido += '<div class="col-sm-3 col-xs-6">';
    contenido += '<span class="label label-default">Trimestre 3 :</span> <br>' + (item.numeroTrim3 || 0);
    contenido += '</div>';
    contenido += '<div class="col-sm-3 col-xs-6">';
    contenido += '<span class="label label-default">Trimestre 4 :</span> <br>' + (item.numeroTrim4 || 0);
    contenido += '</div>';
    contenido += '</div>';

    contenido += '<div class="row">';
    contenido += '<div class="col-sm-3 col-xs-6">';
    contenido += '<span class="label label-default">Numerador :</span> <br>'+item.valorNumerador;
    contenido += '</div>';
    contenido += '<div class="col-sm-3 col-xs-6">';
    contenido += '<span class="label label-default">Denominador :</span> <br>'+ (item.valorDenominador || 0);
    contenido += '</div>';
    contenido += '<div class="col-sm-3 col-xs-6">';
    contenido += '<span class="label label-default">Linea Base :</span> <br>' + (item.lineaBase || 0);
    contenido += '</div>';
    contenido += '<div class="col-sm-3 col-xs-6">';
    contenido += '<span class="label label-default">Año Base :</span> <br>' + (item.anioBase || 0);
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