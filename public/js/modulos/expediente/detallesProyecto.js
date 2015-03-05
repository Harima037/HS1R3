/*=====================================

    # Nombre:
        detallesProyecto.js

    # Módulo:
    	expediente/proyectos
        expediente/inversion

    # Descripción:
        Muestra la información del proyecto

=====================================*/

var detallesProyecto = {};

(function(context){

var modal_detalles = '#modalDetalleProyecto';

context.mostrar_datos = function(datos){
	//
	var titulo_modal = datos.clasificacion_proyecto.descripcion + ' <small>' + datos.tipo_proyecto.descripcion + '</small>';
    $(modal_detalles).find(".modal-title").html(titulo_modal);

    $('#clasificacion_proyecto').val(datos.clasificacion_proyecto.id);
    $('#tipo_proyecto').val(datos.tipo_proyecto.id);

    $('#lbl_nombre_tecnico').text(datos.nombreTecnico);

    var clave = datos.unidadResponsable + datos.finalidad + datos.funcion + datos.subFuncion +
                datos.subSubFuncion + datos.programaSectorial + datos.programaPresupuestario +
                datos.programaEspecial + datos.actividadInstitucional + datos.proyectoEstrategico +
                ("000" + datos.numeroProyectoEstrategico).slice(-3);

    $('#lbl_clave_presupuestaria').text(clave);
    $('#lbl_unidad_responsable').text(datos.datos_unidad_responsable.clave + ' - ' + datos.datos_unidad_responsable.descripcion);
    $('#lbl_finalidad').text(datos.datos_finalidad.clave + ' - ' + datos.datos_finalidad.descripcion);
    $('#lbl_funcion').text(datos.datos_funcion.clave + ' - ' + datos.datos_funcion.descripcion);
    $('#lbl_sub_funcion').text(datos.datos_sub_funcion.clave + ' - ' + datos.datos_sub_funcion.descripcion);
    $('#lbl_sub_sub_funcion').text(datos.datos_sub_sub_funcion.clave + ' - ' + datos.datos_sub_sub_funcion.descripcion);
    $('#lbl_programa_sectorial').text(datos.datos_programa_sectorial.clave + ' - ' + datos.datos_programa_sectorial.descripcion);
    $('#lbl_programa_presupuestario').text(datos.datos_programa_presupuestario.clave + ' - ' + datos.datos_programa_presupuestario.descripcion);
    $('#lbl_programa_especial').text(datos.datos_programa_especial.clave + ' - ' + datos.datos_programa_especial.descripcion);
    $('#lbl_actividad_institucional').text(datos.datos_actividad_institucional.clave + ' - ' + datos.datos_actividad_institucional.descripcion);
    $('#lbl_proyecto_estrategico').text(datos.datos_proyecto_estrategico.clave + ' - ' + datos.datos_proyecto_estrategico.descripcion);

    var cobertura = datos.cobertura.descripcion;

    if(datos.claveMunicipio){
        cobertura = cobertura + ' <small class="text-capitalize">('+datos.municipio.nombre+')</small>';
    }else if(datos.claveRegion){
        cobertura = cobertura + ' <small class="text-capitalize">('+datos.region.nombre+')</small>';
    }

    $('#lbl_cobertura').html(cobertura);
    $('#lbl_tipo_accion').text(datos.tipo_accion.descripcion);

    $('#lbl_vinculacion_ped').text(datos.objetivo_ped.descripcion);

    $('#lbl_lider_proyecto').text((datos.lider_proyecto)?datos.lider_proyecto.nombre:'No asignado');
    $('#lbl_jefe_lider').text((datos.jefe_inmediato)?datos.jefe_inmediato.nombre:'No asignado');
    $('#lbl_jefe_ṕlaneacion').text((datos.jefe_planeacion)?datos.jefe_planeacion.nombre:'No asignado');
    $('#lbl_coordinador_grupo').text((datos.coordinador_grupo_estrategico)?datos.coordinador_grupo_estrategico.nombre:'No asignado');

    llenar_tabla_beneficiarios(datos.beneficiarios);

    $('#id').val(datos.id);

    if(datos.idClasificacionProyecto == 2){
        $('#tab-link-fibap').parent().removeClass('hidden');
        if(datos.fibap){
            $('#datos-capturados-fibap').show();
            $('#datos-alerta-fibap').hide();
            llenar_datos_fibap(datos.fibap);
        }else{
            $('#datos-capturados-fibap').hide();
            $('#datos-alerta-fibap').show();
            $('#proyecto-id').val(datos.id);
        }
    }else{
        $('#tab-link-fibap').parent().addClass('hidden');
    }

    construir_panel_componentes(datos.componentes);
    
    $('#datos-formulario').hide();
    $('#datos-proyecto').show();

    $('#proyecto-tab-panel-list a:first').tab('show');

    $('#btn-exportar-excel').off('click');
    $('#btn-exportar-excel').on('click',function(){
        window.open(SERVER_HOST+'/v1/reporteProyecto/'+datos.id);
    });

    $('#btn-editar-proyecto').attr('data-id-proyecto',datos.id);

    $(modal_detalles).modal('show');
};

/***********************************************************************************************
					Funciones Privadas
************************************************************************************************/
function llenar_tabla_beneficiarios(datos){
    $('#tabla_beneficiarios tbody').empty();
    var beneficiarios = {};
    for(var i in datos){
        if(!beneficiarios[datos[i].idTipoBeneficiario]){
            beneficiarios[datos[i].idTipoBeneficiario] = {
                id: datos[i].idTipoBeneficiario,
                tipo: datos[i].tipo_beneficiario.descripcion,
                total: 0,
                desglose: {'f':{},'m':{}}
            };
        }
        beneficiarios[datos[i].idTipoBeneficiario].total += datos[i].total;
        beneficiarios[datos[i].idTipoBeneficiario].desglose[datos[i].sexo] = {
            sexo: datos[i].sexo,
            total: datos[i].total,
            urbana: datos[i].urbana,
            rural: datos[i].rural,
            mestiza: datos[i].mestiza,
            indigena: datos[i].indigena,
            inmigrante: datos[i].inmigrante,
            otros: datos[i].otros,
            muyAlta: datos[i].muyAlta,
            alta: datos[i].alta,
            media: datos[i].media,
            baja: datos[i].baja,
            muyBaja: datos[i].muyBaja
        }
    }
    var rows = '';
    for(var i in beneficiarios){
        rows += '<tr>';
        rows += '<td rowspan="2">' + beneficiarios[i].tipo + '</td>';
        rows += '<td rowspan="2">' + beneficiarios[i].total + '</td>';
        rows += '<td>' + ( beneficiarios[i].desglose['f'].total || 0 ) + '</td>';
        rows += '<th>Femenino</th>';
        rows += '<td>' + ( beneficiarios[i].desglose['f'].urbana || 0 ) + '</td>';
        rows += '<td>' + ( beneficiarios[i].desglose['f'].rural || 0 ) + '</td>';
        rows += '<td>' + ( beneficiarios[i].desglose['f'].mestiza || 0 ) + '</td>';
        rows += '<td>' + ( beneficiarios[i].desglose['f'].indigena || 0 ) + '</td>';
        rows += '<td>' + ( beneficiarios[i].desglose['f'].inmigrante || 0 ) + '</td>';
        rows += '<td>' + ( beneficiarios[i].desglose['f'].otros || 0 ) + '</td>';
        rows += '<td>' + ( beneficiarios[i].desglose['f'].muyAlta || 0 ) + '</td>';
        rows += '<td>' + ( beneficiarios[i].desglose['f'].alta || 0 ) + '</td>';
        rows += '<td>' + ( beneficiarios[i].desglose['f'].media || 0 ) + '</td>';
        rows += '<td>' + ( beneficiarios[i].desglose['f'].baja || 0 ) + '</td>';
        rows += '<td>' + ( beneficiarios[i].desglose['f'].muyBaja || 0 ) + '</td>';
        rows += '</tr>';
        rows += '<tr>';
        rows += '<td>' + ( beneficiarios[i].desglose['m'].total || 0 ) + '</td>';
        rows += '<th>Masculino</th>';
        rows += '<td>' + ( beneficiarios[i].desglose['m'].urbana || 0 ) + '</td>';
        rows += '<td>' + ( beneficiarios[i].desglose['m'].rural || 0 ) + '</td>';
        rows += '<td>' + ( beneficiarios[i].desglose['m'].mestiza || 0 ) + '</td>';
        rows += '<td>' + ( beneficiarios[i].desglose['m'].indigena || 0 ) + '</td>';
        rows += '<td>' + ( beneficiarios[i].desglose['m'].inmigrante || 0 ) + '</td>';
        rows += '<td>' + ( beneficiarios[i].desglose['m'].otros || 0 ) + '</td>';
        rows += '<td>' + ( beneficiarios[i].desglose['m'].muyAlta || 0 ) + '</td>';
        rows += '<td>' + ( beneficiarios[i].desglose['m'].alta || 0 ) + '</td>';
        rows += '<td>' + ( beneficiarios[i].desglose['m'].media || 0 ) + '</td>';
        rows += '<td>' + ( beneficiarios[i].desglose['m'].baja || 0 ) + '</td>';
        rows += '<td>' + ( beneficiarios[i].desglose['m'].muyBaja || 0 ) + '</td>';
        rows += '</tr>';
    }
    $('#tabla_beneficiarios tbody').html(rows);
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

})(detallesProyecto);