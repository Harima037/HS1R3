/*=====================================

    # Nombre:
        vista-rendicion-cuentas.js

    # Módulo:
        seguimiento/editar-avance

    # Descripción:
        Para rendición de cuentas de proyectos

=====================================*/

// Inicialización General para casi cualquier módulo
var moduloResource = new RESTfulRequests(SERVER_HOST+'/v1/segui-proyectos-inst');

$('#btn-proyecto-cancelar').on('click',function(){
	
	if($('#id_clasificacion').val()=='1')
		window.location.href = SERVER_HOST+'/revision/segui-proyectos-inst';
	else if($('#id_clasificacion').val()=='2')
		window.location.href = SERVER_HOST+'/revision/segui-proyectos-inv';
});

var comentariosArray = [];

/********************************************************************************************************************************
        Inicio: Seguimiento de Metas
*********************************************************************************************************************************/
var accionesDatagrid = new Datagrid("#datagridAcciones",moduloResource,{ formatogrid:true, pagina: 1, idProyecto: $('#id').val(), grid:'rendicion-acciones'});
accionesDatagrid.init();
accionesDatagrid.actualizar({ _success: function(response){ llenar_grid_acciones(response); } });

function seguimiento_metas(e){
    var datos_id = e.split('-');
    if(datos_id[0] == '1'){
        var nivel = 'componente';
    }else{
        var nivel = 'actividad';
    }
    var parametros = {'mostrar':'datos-metas-avance','nivel':nivel};
    var id = datos_id[1];
    moduloResource.get(id,parametros,{
        _success: function(response){
            if(response.data.idComponente){
                $('#modalEditarAvance').find(".modal-title").html("Seguimiento de Metas de la Actividad");
                $('#nivel').val('actividad');
            }else{
                $('#modalEditarAvance').find(".modal-title").html("Seguimiento de Metas del Componente");
                $('#nivel').val('componente');
            }
			/*Poner los comentarios*/
			
			for(var cont in response.data.comentarios)
			{
				comentario = response.data.comentarios[cont];
				if(comentario.mes == $('#mes').val())
				{
					if(comentario.idCampo == 'avancesmetas')
					{
						var objetoAColorear = '#'+comentario.idCampo;
						$(objetoAColorear).removeClass('btn-default');
						$(objetoAColorear).addClass('btn-warning');
					}
					else
					{
						var objetoAColorear = '#lbl-'+comentario.idCampo;
						$(objetoAColorear).parent().parent().addClass('has-error has-feedback');
					}
					comentariosArray.push([comentario.id, comentario.idCampo, comentario.observacion, comentario.tipoElemento]);
				}
			}
			
			/*Termina de poner los comentarios*/

            $('#indicador').text(response.data.indicador);
            $('#unidad-medida').text(response.data.unidad_medida.descripcion);
            $('#meta-total').text((parseFloat(response.data.valorNumerador) || 0).format());
            
            $('#id-accion').val(response.data.id);

            var total_programado = 0;
            var total_acumulado = 0;
            var total_avance = 0;

            for(var i in response.data.metas_mes_jurisdiccion){
                var dato = response.data.metas_mes_jurisdiccion[i];
                var row = '#tabla-avances-metas > tbody > tr[data-clave-jurisdiccion="'+dato.claveJurisdiccion+'"]';

                var dato_meta = parseFloat(dato.meta) || 0;
                dato_meta = +dato_meta.toFixed(2);

                total_programado += parseFloat(dato_meta || 0);
                
                $(row + ' > td.meta-programada').attr('data-meta',dato_meta);
                $(row + ' > td.meta-programada').text(dato_meta);

                var dato_avance = parseFloat(dato.avance) || 0;

                if(dato.avance){
                    total_acumulado += dato_avance;
                    $(row + ' > td.avance-acumulado').text(dato_avance);
                    $(row + ' > td.avance-acumulado').attr('data-acumulado',dato_avance);
                    $(row + ' > td.avance-total').attr('data-avance-total',dato_avance);
                    $(row + ' > td.avance-total').text(dato_avance.format());
                }
            }

            var total_programado_mes = 0;
            for(var i in response.data.metas_mes){
                var dato = response.data.metas_mes[i];
                var row = '#tabla-avances-metas > tbody > tr[data-clave-jurisdiccion="'+dato.claveJurisdiccion+'"]';
                
                var dato_meta = parseFloat(dato.meta) || 0;
                dato_meta = +dato_meta.toFixed(2);

                $('#avance_'+dato.claveJurisdiccion).attr('data-meta-programada',dato_meta);				
				$('#avance_'+dato.claveJurisdiccion).attr('disabled',true);
                $(row + ' > td.meta-del-mes').text((dato_meta || 0));
                $(row + ' > td.meta-del-mes').attr('data-meta-mes',dato_meta);
                total_programado_mes += dato_meta || 0;

                var dato_avance = parseFloat(dato.avance) || 0;

                if(dato.avance != null){
                    $('#avance_'+dato.claveJurisdiccion).val(dato_avance);
                    total_avance += dato_avance;
                    total_acumulado -= dato_avance;
                    avance_jurisdiccion = parseFloat($(row + ' > td.avance-acumulado').attr('data-acumulado')) || 0;
                    $(row + ' > td.avance-acumulado').text((avance_jurisdiccion - dato_avance).format(2));
                    $(row + ' > td.avance-acumulado').attr('data-acumulado',(avance_jurisdiccion - dato_avance));
                }
            }
			
			$('#lbl-analisis-resultados').html('&nbsp;');
            $('#lbl-justificacion-acumulada').html('&nbsp;');

            if(response.data.registro_avance.length){
                $('#id-avance').val(response.data.registro_avance[0].id);
                $('#lbl-analisis-resultados').html(response.data.registro_avance[0].analisisResultados);
                $('#lbl-justificacion-acumulada').html(response.data.registro_avance[0].justificacionAcumulada);
            }
			
			$('#lbl-accion-mejora').html('&nbsp; ');
            $('#lbl-grupo-trabajo').html('&nbsp; ');
            $('#lbl-documentacion-comprobatoria').html('&nbsp; ');
            $('#lbl-fecha-inicio').html('&nbsp; ');
            $('#lbl-fecha-termino').html('&nbsp; ');
            $('#lbl-fecha-notificacion').html('&nbsp; ');

            if(response.data.plan_mejora.length){
                var plan_mejora = response.data.plan_mejora[0];
                $('#lbl-accion-mejora').html(plan_mejora.accionMejora);
                $('#lbl-grupo-trabajo').html(plan_mejora.grupoTrabajo);
                $('#lbl-documentacion-comprobatoria').html(plan_mejora.documentacionComprobatoria);
                $('#lbl-fecha-inicio').html(plan_mejora.fechaInicio);
                $('#lbl-fecha-termino').html(plan_mejora.fechaTermino);
                $('#lbl-fecha-notificacion').html(plan_mejora.fechaNotificacion);
            }

            //var total_porcentaje_acumulado = parseFloat((((total_acumulado + total_avance) * 100) / total_programado).toFixed(2)) || 0;
            $('#total-meta-programada').text(total_programado.format());
            $('#total-meta-programada').attr('data-total-programado',total_programado);
            $('#total-meta-mes').text(total_programado_mes.format());
            $('#total-avance-mes').text(total_avance.format());
            $('#total-avance-acumulado').text(total_acumulado.format());
            //$('#total-porcentaje').text(total_porcentaje_acumulado+'% ');
            $('.avance-mes').change();
			
			if(response.data.desglose_municipios){
                if(response.data.desglose_municipios.length){
                    asignar_municipios(response.data.desglose_municipios);
                    $('input.avance-mes').attr('disabled',true);
                }
            }
            $('#modalEditarAvance').modal('show');
        }
    });    
}

function asignar_municipios(datos){
    //datos
    var municipios = {};
    for(var i in datos){
        if(!municipios[datos[i].claveJurisdiccion]){
            municipios[datos[i].claveJurisdiccion] = {
                'claveJurisdiccion': datos[i].claveJurisdiccion,
                'municipios': []
            };
        }
        municipios[datos[i].claveJurisdiccion].municipios.push({
            'clave': datos[i].clave,
            'nombre': datos[i].nombre
        })
    }
    
    for(var i in municipios){
        var claveJurisdiccion = municipios[i].claveJurisdiccion;
        var row = '#tabla-avances-metas > tbody > tr[data-clave-jurisdiccion="'+claveJurisdiccion+'"]';
        
        $(row + ' td.accion-municipio').addClass('btn-link');
        $(row + ' td.accion-municipio span').addClass('caret');
        $(row + ' td.accion-municipio').on('click',function(){ 
            $('.lista-localidades-jurisdiccion .btn-ocultar-avance-localidades').click();
            $('#desglose-avance-'+$(this).parent().attr('data-clave-jurisdiccion')).removeClass('hidden'); 
        });

        $(row).after('<tr id="desglose-avance-'+claveJurisdiccion+'" class="lista-localidades-jurisdiccion hidden"><td colspan="7"><div id="panel-localidades-'+claveJurisdiccion+'" class="panel panel-primary" style="margin-bottom:0;"></div></td></tr>');

        var panel_id = '#panel-localidades-'+claveJurisdiccion;
        $(panel_id).html($('#panel-estructura-localidades').html());
        $(panel_id + ' .select-lista-municipios').attr('data-jurisdiccion',claveJurisdiccion);
        $(panel_id + ' .btn-guardar-avance-localidades').attr('data-jurisdiccion',claveJurisdiccion);
        $(panel_id + ' .btn-ocultar-avance-localidades').attr('data-jurisdiccion',claveJurisdiccion);

        var opciones = [];
        opciones.push('<option value="">Selecciona un municipio</option>');
        for(var j in municipios[i].municipios){
            var municipio = municipios[i].municipios[j];
            opciones.push('<option value="'+ municipio.clave +'">'+ municipio.nombre +'</option>');
        }
        $(panel_id + ' .select-lista-municipios').html(opciones.join(''));
    }

    $('.btn-ocultar-avance-localidades').on('click',function(){
        $('#desglose-avance-'+$(this).attr('data-jurisdiccion')+' .select-lista-municipios').val('');
        $('#panel-localidades-'+$(this).attr('data-jurisdiccion')+' table.tabla-avance-localidades tbody').empty();
        var tabla_totales = '#panel-localidades-'+$(this).attr('data-jurisdiccion')+' table.tabla-totales-municipio tfoot';
        $(tabla_totales + ' tr td.total-municipio-meta').text('0');
        $(tabla_totales + ' tr td.total-municipio-avance').text('0');
        $(tabla_totales + ' tr td.total-municipio-avance').attr('data-valor',0);
        $(tabla_totales + ' tr td.total-municipio-porcentaje').text('0');
        $(tabla_totales + ' tr td.total-municipio-porcentaje').attr('data-valor',0);
        $('#desglose-avance-'+$(this).attr('data-jurisdiccion')).addClass('hidden');
    });

    $('.btn-guardar-avance-localidades').on('click',function(){
        enviar_datos_municipio($(this).attr('data-jurisdiccion'));
    });

    $('.select-lista-municipios').on('change',function(){
        buscar_localidades($(this).attr('data-jurisdiccion'),$(this).val());
    });
}

function buscar_localidades(jurisdiccion,municipio){
    //sconsole.log('Jurisdiccion = '+jurisdiccion+'| Municipio = '+municipio);
    if(municipio == ''){
        $('#panel-localidades-'+jurisdiccion+' table.tabla-avance-localidades tbody').empty();
        var tabla_totales = '#panel-localidades-'+jurisdiccion+' table.tabla-totales-municipio tfoot';
        $(tabla_totales + ' tr td.total-municipio-meta').text('0');
        $(tabla_totales + ' tr td.total-municipio-meta').attr('data-valor',0);
        $(tabla_totales + ' tr td.total-municipio-avance').text('0');
        $(tabla_totales + ' tr td.total-municipio-avance').attr('data-valor',0);
        $(tabla_totales + ' tr td.total-municipio-porcentaje').text('0');
        return false;
    }
    var parametros = {
        'mostrar':'datos-municipio-avance',
        'nivel':$('#nivel').val(),
        'clave-municipio':municipio
    };
    moduloResource.get($('#id-accion').val(),parametros,{
        _success: function(response){
            //
            var tabla = '#panel-localidades-'+jurisdiccion+' table.tabla-avance-localidades tbody';
            $(tabla).empty();
            var html_rows = '';
            var suma_metas = 0;
            var suma_avances = 0;
            for(var i in response.data){
                var dato = response.data[i];
                if(dato.metas_mes.length > 0){
                    var datos_meta = {
                        meta: parseFloat(dato.metas_mes[0].meta) || 0,
                        avance: parseFloat(dato.metas_mes[0].avance),
                    };
                }else{
                    var datos_meta = {meta:0,avance:0};
                }
                if(dato.metas_mes_acumuladas){
                    var datos_acumulado = {
                        meta: parseFloat(dato.metas_mes_acumuladas.meta) || 0,
                        avance: parseFloat(dato.metas_mes_acumuladas.avance) || 0
                    }
                }else{
                    var datos_acumulado = {meta:0, avance:0}
                }
                var avance_anterior = datos_acumulado.avance - (datos_meta.avance || 0); 
                html_rows += '<tr data-clave-localidad="'+dato.claveLocalidad+'"><td>' + dato.localidad + '</td>' +
                                '<td class="localidad-metas-acumuladas bg-success" data-metas-acumuladas="'+datos_acumulado.meta+'">'+datos_acumulado.meta.format(2)+'</td>' + 
                                '<td class="localidad-meta-mes" data-meta-mes="'+datos_meta.meta+'">'+datos_meta.meta.format(2)+'</td>' +
                                '<td><div class="form-group" style="margin:0;"><input name="localidad-avance-mes['+dato.claveLocalidad+']" id="localidad_avance_mes_'+dato.claveLocalidad+'" type="number" class="form-control localidad-avance" value="'+datos_meta.avance+'" data-localidad="'+dato.claveLocalidad+'" data-jurisdiccion="'+jurisdiccion+'"></div></td>' + 
                                '<td class="localidad-avance-acumulado" data-avance-acumulado="'+avance_anterior+'">'+avance_anterior.format(2)+'</td>' +
                                '<td class="localidad-total-avance bg-info" data-avance-total="'+datos_acumulado.avance+'">'+datos_acumulado.avance.format(2)+'</td></tr>';
                suma_metas += datos_acumulado.meta;
                suma_avances += datos_acumulado.avance;
            }
            $(tabla).html(html_rows);

            var tabla_totales = '#panel-localidades-'+jurisdiccion+' table.tabla-totales-municipio tfoot';
            $(tabla_totales + ' tr td.total-municipio-meta').attr('data-valor',suma_metas);
            $(tabla_totales + ' tr td.total-municipio-meta').text(suma_metas.format(2));
            $(tabla_totales + ' tr td.total-municipio-avance').attr('data-valor',suma_avances);
            $(tabla_totales + ' tr td.total-municipio-avance').text(suma_avances.format(2));
            var porcentaje = parseFloat(((suma_avances * 100) / suma_metas).toFixed(2)) || 0;
            $(tabla_totales + ' tr td.total-municipio-porcentaje').text(porcentaje.format(2) + ' %');

            $('.localidad-avance').on('keyup',function(){ $(this).change() });
            $('.localidad-avance').on('change',function(){
                var jurisdiccion = $(this).attr('data-jurisdiccion');
                var localidad = $(this).attr('data-localidad');

                var row = '#panel-localidades-'+jurisdiccion+' table.tabla-avance-localidades tbody tr[data-clave-localidad="'+localidad+'"]';

                var avance_acumulado = parseFloat($(row + ' td.localidad-avance-acumulado').attr('data-avance-acumulado')) || 0;
                avance_acumulado += parseFloat($(this).val()) || 0;

                var avance_anterior = parseFloat($(row + ' td.localidad-total-avance').attr('data-avance-total')) || 0;

                $(row + ' td.localidad-total-avance').attr('data-avance-total',avance_acumulado);
                $(row + ' td.localidad-total-avance').text(avance_acumulado.format(2));

                var tabla_totales = '#panel-localidades-'+jurisdiccion+' table.tabla-totales-municipio tfoot tr';

                var total_metas = parseFloat($(tabla_totales + ' td.total-municipio-meta').attr('data-valor')) || 0;
                var total_avance = parseFloat($(tabla_totales + ' td.total-municipio-avance').attr('data-valor')) || 0;

                total_avance -= avance_anterior;
                total_avance += avance_acumulado;

                $(tabla_totales + ' td.total-municipio-avance').attr('data-valor',total_avance);
                $(tabla_totales + ' td.total-municipio-avance').text(total_avance.format(2));

                var porcentaje = parseFloat(((total_avance * 100) / total_metas).toFixed(2)) || 0;
                $(tabla_totales + ' td.total-municipio-porcentaje').text(porcentaje.format(2) + ' %');
            });
        }
    });
}


$('.avance-mes').on('keyup',function(){ $(this).change() });
$('.avance-mes').on('change',function(){
    var jurisdiccion = $(this).attr('data-jurisdiccion');
    //Actualiza la columna de avance acumulado
    var row = '#tabla-avances-metas > tbody > tr[data-clave-jurisdiccion="'+jurisdiccion+'"]';

    var avance = parseFloat($(this).val()) || 0;
    //Actualiza la columna de porcentaje acumulado
    var acumulado = parseFloat($(row +' > td.avance-acumulado').attr('data-acumulado')) || 0;
    acumulado += avance;
    $(row +' > td.avance-total').text(acumulado);
    $(row +' > td.avance-total').attr('data-avance-total',acumulado);

    var total_programado = $(row +' > td.meta-programada').attr('data-meta');

    if(acumulado == 0 && total_programado == 0){
        $(row +' > td.avance-mes').html('<small class="text-success">0%</small>');
    }else{
        if(total_programado > 0){
            var avance_mes = parseFloat(((acumulado * 100) / total_programado).toFixed(2))||0;
        }else if(acumulado > 0){
            var avance_mes = 100;
        }else{
            var avance_mes = 0;
        }

        if(avance_mes > 110){
            $(row +' > td.avance-mes').html('<small class="text-danger"><span class="fa fa-arrow-up"></span> '+avance_mes+'%</small>');
        }else if(avance_mes < 90){
            $(row +' > td.avance-mes').html('<small class="text-danger"><span class="fa fa-arrow-down"></span> '+avance_mes+'%</small>');
        }else if(total_programado == 0 && avance_mes > 0){
            $(row +' > td.avance-mes').html('<small class="text-info"><span class="fa fa-arrow-up"></span> '+avance_mes+'%</small>');
        }else{
            $(row +' > td.avance-mes').html('<small class="text-success">'+avance_mes+'%</small>');
        }
    }

    var suma = 0;
    $('.avance-mes').each(function(){
        suma += parseFloat($(this).val()) || 0;
    });
    suma = +suma.toFixed(2);
    $('#total-avance-mes').text(suma.format(2));

    var suma = 0;
    $('.avance-total').each(function(){
        suma += parseFloat($(this).attr('data-avance-total')) || 0;
    });
    suma = +suma.toFixed(2);
    $('#total-avance-total').attr('data-total-avance',suma);
    $('#total-avance-total').text(suma.format(2));

    total_programado = parseFloat($('#total-meta-programada').attr('data-total-programado'));
    total_acumulado = parseFloat($('#total-avance-total').attr('data-total-avance'));
    

    if(total_programado == 0 && total_acumulado ==  0){
        total_porcentaje_acumulado = '<small class="text-success">0%</small>';
        $('#total-porcentaje').attr('data-estado-avance','');
    }else{
        if(total_programado > 0){
            var total_porcentaje_acumulado = parseFloat(((total_acumulado * 100) / total_programado).toFixed(2))||0;
        }else{
            if(total_acumulado > 0){
                var total_porcentaje_acumulado = 100;
            }else{
                var total_porcentaje_acumulado = 0;
            }
        }
        if(total_porcentaje_acumulado > 110){
            total_porcentaje_acumulado = '<small class="text-danger"><span class="fa fa-arrow-up"></span> '+total_porcentaje_acumulado+'%</small>';
            $('#total-porcentaje').attr('data-estado-avance','1');
        }else if(total_porcentaje_acumulado < 90){
            total_porcentaje_acumulado = '<small class="text-danger"><span class="fa fa-arrow-down"></span> '+total_porcentaje_acumulado+'%</small>';
            $('#total-porcentaje').attr('data-estado-avance','1');
        }else if(total_programado == 0 && total_porcentaje_acumulado > 0){
            total_porcentaje_acumulado = '<small class="text-info"><span class="fa fa-arrow-up"></span> '+total_porcentaje_acumulado+'%</small>';
            $('#total-porcentaje').attr('data-estado-avance','1');
        }else{
            total_porcentaje_acumulado = '<small class="text-success">'+total_porcentaje_acumulado+'%</small>';
            $('#total-porcentaje').attr('data-estado-avance','');
        }
    }

    $('#total-porcentaje').html(total_porcentaje_acumulado);

    if($('#total-porcentaje').attr('data-estado-avance')){
        $('#justificacion-acumulada').attr('disabled',false);
        if($('#justificacion-acumulada').val() == 'El avance se encuentra dentro de los parametros establecidos'){
            $('#justificacion-acumulada').val('');
        }
        $('#tab-link-plan-mejora').attr('data-toggle','tab');
        $('#tab-link-plan-mejora').parent().removeClass('disabled');
    }else{
        $('#justificacion-acumulada').attr('disabled',true);
        $('#tab-link-plan-mejora').attr('data-toggle','');
        $('#tab-link-plan-mejora').parent().addClass('disabled');
    }
});

$('#modalEditarAvance').on('hide.bs.modal',function(e){
    $('#modalEditarAvance .alert').remove();
    $('#form_avance').get(0).reset();
    $('#form_avance input[type="hidden"]').val('');
    //$('#form_avance input[type="number"]').attr('disabled',true);
    $('#form_avance input[type="number"]').attr('data-meta-programada','');
    $('td.avance-mes').text('');
    //$('td.avance-mes').attr('data-estado-avance','');
    $('td.avance-acumulado').attr('data-acumulado','');
    $('td.avance-acumulado').text('0');
    $('td.meta-programada').attr('data-meta','0');
    $('td.meta-programada').text('0');
    $('td.meta-del-mes').attr('data-meta-mes','');
    $('td.meta-del-mes').text('0');
    $('td.avance-total').attr('data-avance-total','');
    $('td.avance-total').text('0');
    $('#total-avance-total').attr('data-total-avance','');
    $('#total-avance-total').text('0');
    $('#total-porcentaje').attr('data-estado-avance','');
    $('#total-porcentaje').text('0%');
    //$('span.nueva-cantidad').text('');
    //$('span.vieja-cantidad').text('0');
    $('#justificacion-acumulada').attr('disabled',true);
    $('#tab-link-plan-mejora').attr('data-toggle','');
    $('#tab-link-plan-mejora').parent().addClass('disabled');
    $('#tabs-seguimiento-metas a:first').tab('show');
    Validation.cleanFormErrors('#form_avance');
	
	var arrayTemporal = [];
	
	for(var i = 0; i < comentariosArray.length; i++)
		if(comentariosArray[i][3]!='2' && comentariosArray[i][3]!='3')
			arrayTemporal.push([comentariosArray[i][0],comentariosArray[i][1],comentariosArray[i][2],comentariosArray[i][3]]);
	
	comentariosArray.length = 0;
	comentariosArray = arrayTemporal;
	
	$('#avancesmetas').removeClass('btn-warning');
	$('#avancesmetas').addClass('btn-default');
	
	$('#form_avance .has-error.has-feedback').removeClass('has-error has-feedback');
	/*	$('#lbl-analisis-resultados').parent().parent().removeClass('has-error has-feedback');
	$('#lbl-justificacion-acumulada').parent().parent().removeClass('has-error has-feedback');
	$('#lbl-accion-mejora').parent().parent().removeClass('has-error has-feedback');
	$('#lbl-grupo-trabajo').parent().parent().removeClass('has-error has-feedback');
	$('#lbl-fecha-inicio').parent().parent().removeClass('has-error has-feedback');
	$('#lbl-fecha-termino').parent().parent().removeClass('has-error has-feedback');
	$('#lbl-fecha-notificacion').parent().parent().removeClass('has-error has-feedback');
	$('#lbl-documentacion-comprobatoria').parent().parent().removeClass('has-error has-feedback');*/
});

function llenar_grid_acciones(response){
    accionesDatagrid.limpiar();
    var datos_grid = [];
    var contador_componente = 0;
	
	//console.log(response.data);
	
	if(response.data.fuenteInformacion)
		$('#lbl-fuente-informacion').text(response.data.fuenteInformacion);
	if(response.data.idResponsable)
	{
		$('#lbl-responsable').text(response.data.responsable_informacion.nombre);
		$('#ayuda-responsable').html(response.data.responsable_informacion.cargo);		
	}
	
	
    for(var i in response.data.componentes){
        var contador_actividad = 0;
        contador_componente++;
        var componente = response.data.componentes[i];

        var item = {};
        item.id = '1-' + componente.id;
        item.nivel = 'Componente ' + contador_componente;
        item.indicador = componente.indicador;
        item.meta = (parseFloat(componente.valorNumerador) || 0).format(2);
        item.avances_acumulados = 0;
        item.avances_mes = 0;
        item.justificacion = '';

        var fecha = new Date();
        var mes = $('#mes').val();
        
        if(componente.registro_avance.length){
            
            for(var j in componente.registro_avance){
                var avance = componente.registro_avance[j];
                item.avances_acumulados += parseFloat(avance.avanceMes);
                if(avance.mes == mes){
                    item.justificacion += '<span class="fa fa-floppy-o"></span> ';
                    item.avances_mes += parseFloat(avance.avanceMes);
                    if(avance.planMejora){
                        item.justificacion += '<span class="fa fa-align-left"></span>';
                    }
                }
            }
        }
        datos_grid.push(item);

        for(var j in componente.actividades){
            contador_actividad++;
            var actividad = componente.actividades[j];
            var item = {};
            item.id = '2-' + actividad.id;
            item.nivel = 'Actividad ' + contador_componente + '.' + contador_actividad;
            item.indicador = actividad.indicador;
            item.meta = (parseFloat(actividad.valorNumerador) || 0).format(2);
            item.avances_acumulados = 0;
            item.avances_mes = 0;
            item.justificacion = '';
            
            if(actividad.registro_avance.length){
                for(var j in actividad.registro_avance){
                    var avance = actividad.registro_avance[j];
                    item.avances_acumulados += parseFloat(avance.avanceMes);
                    if(avance.mes == mes){
                        item.justificacion += '<span class="fa fa-floppy-o"></span> ';
                        item.avances_mes += parseFloat(avance.avanceMes);
                        if(avance.planMejora){
                            item.justificacion += '<span class="fa fa-align-left"></span>';
                        }
                    }
                }
            }
            datos_grid.push(item);
        }
    }
    accionesDatagrid.cargarDatos(datos_grid);                         
    var total = parseInt(response.resultados/accionesDatagrid.rxpag); 
    var plus = parseInt(response.resultados)%accionesDatagrid.rxpag;
    if(plus>0) 
        total++;
    accionesDatagrid.paginacion(total);
}
/********************************************************************************************************************************
        Fin: Seguimiento de Metas
*********************************************************************************************************************************/

/********************************************************************************************************************************
        Inicio: Seguimiento de Beneficiarios
*********************************************************************************************************************************/
var beneficiariosDatagrid = new Datagrid("#datagridBeneficiarios",moduloResource,{ formatogrid:true, pagina: 1, idProyecto: $('#id').val(), grid:'rendicion-beneficiarios'});
beneficiariosDatagrid.init();
beneficiariosDatagrid.actualizar({ _success: function(response){ llenar_grid_beneficiarios(response); } });
function seguimiento_beneficiarios(e){
    var parametros = {'mostrar':'datos-beneficiarios-avance','id-proyecto':$('#id').val()};
    moduloResource.get(e,parametros,{
        _success: function(response){
            $('#form_beneficiario input.masc').attr('disabled',true);
            $('#form_beneficiario input.fem').attr('disabled',true);

            $('#modalBeneficiario').find(".modal-title").html('Seguimiento de Beneficiarios');
            var beneficiario = response.data.beneficiario[0].tipo_beneficiario;
            $('#tipo-beneficiario').text(beneficiario.descripcion);
            $('#id-beneficiario').val(beneficiario.id);
            var suma = 0;
            for(var i in response.data.beneficiario){
                var beneficiario = response.data.beneficiario[i];
                $('#total-'+beneficiario.sexo).text(beneficiario.total.format());
                $('#total-'+beneficiario.sexo).attr('data-valor',beneficiario.total)
                suma += beneficiario.total;

                /*if(beneficiario.sexo == 'f'){
                    $('#form_beneficiario input.fem').attr('disabled',false);
                }else{
                    $('#form_beneficiario input.masc').attr('disabled',false);
                }*/
				
				$('#form_beneficiario input.fem').attr('disabled',true);
				$('#form_beneficiario input.masc').attr('disabled',true);

                if(beneficiario.registro_avance.length){
                    $('#hay-avance').val(1);
                }

                for(var j in beneficiario.registro_avance){
                    var avance = beneficiario.registro_avance[j];

                    $('#muyalta'+avance.sexo).val(avance.muyAlta);
                    $('#alta'+avance.sexo).val(avance.alta);
                    $('#media'+avance.sexo).val(avance.media);
                    $('#baja'+avance.sexo).val(avance.baja);
                    $('#muybaja'+avance.sexo).val(avance.muyBaja);

                    $('#indigena'+avance.sexo).val(avance.indigena);
                    $('#inmigrante'+avance.sexo).val(avance.inmigrante);
                    $('#mestiza'+avance.sexo).val(avance.mestiza);
                    $('#otros'+avance.sexo).val(avance.otros);

                    $('#rural'+avance.sexo).val(avance.rural);
                    $('#urbana'+avance.sexo).val(avance.urbana);
                }
            }

            $('#muybajaf').change();
            $('#otrosf').change();
            $('#urbanaf').change();
            $('#muybajam').change();
            $('#otrosm').change();
            $('#urbanam').change();

            var suma_acumulados = 0;
            for(var i in response.data.acumulado){
                $('#acumulado-'+response.data.acumulado[i].sexo).text(parseInt(response.data.acumulado[i].total).format());
                $('#acumulado-'+response.data.acumulado[i].sexo).attr('data-valor',response.data.acumulado[i].total);
                suma_acumulados += parseInt(response.data.acumulado[i].total);
            }

            $('#acumulado-beneficiario').text(suma_acumulados.format());
            $('#acumulado-beneficiario').attr('data-valor',suma_acumulados);

            $('#total-beneficiario').text(suma.format());
            $('#total-beneficiario').attr('data-valor',suma);

            $('#modalBeneficiario').modal('show');
        }
    });
}

$('.fem,.masc').on('keyup',function(){ $(this).change(); });
$('.fem').on('change',function(){
    if($(this).hasClass('sub-total-zona')){
        sumar_valores('.sub-total-zona.fem','#total-zona-f');
    }else if($(this).hasClass('sub-total-poblacion')){
        sumar_valores('.sub-total-poblacion.fem','#total-poblacion-f');
    }else if($(this).hasClass('sub-total-marginacion')){
        sumar_valores('.sub-total-marginacion.fem','#total-marginacion-f');
    }
});
$('.masc').on('change',function(){
    if($(this).hasClass('sub-total-zona')){
        sumar_valores('.sub-total-zona.masc','#total-zona-m');
    }else if($(this).hasClass('sub-total-poblacion')){
        sumar_valores('.sub-total-poblacion.masc','#total-poblacion-m');
    }else if($(this).hasClass('sub-total-marginacion')){
        sumar_valores('.sub-total-marginacion.masc','#total-marginacion-m');
    }
});

$('#modalBeneficiario').on('hide.bs.modal',function(e){
    $('#modalBeneficiario .alert').remove();
    $('#form_beneficiario').get(0).reset();
    $('#form_beneficiario input[type="hidden"]').val('');
    $('#form_beneficiario .cant-benficiarios').text('0');
    $('#form_beneficiario .cant-benficiarios').attr('data-valor','0');
    Validation.cleanFormErrors('#form_beneficiario');
});

function llenar_grid_beneficiarios(response){
    beneficiariosDatagrid.limpiar();
    var datos_grid = {};

    for(var i in response.data){
        var beneficiario = response.data[i];
		//console.log(beneficiario);

        if(!datos_grid[beneficiario.idTipoBeneficiario]){
            datos_grid[beneficiario.idTipoBeneficiario] = {
                'id': beneficiario.idTipoBeneficiario,
                'tipoBeneficiario': beneficiario.tipo_beneficiario.descripcion,
                'f': 0,
                'f-avance':0,
                'm': 0,
                'm-avance':0,
                'total': 0,
                'total-avance':0,
				'comentario':'<button type="button" class="btn btn-default" onclick="escribirComentario(\'beneficiario'+beneficiario.idTipoBeneficiario+'\',\'Tipo de beneficiario:\',\''+beneficiario.tipo_beneficiario.descripcion+'\',\'1\',\''+beneficiario.id+'\');" id="beneficiario'+beneficiario.idTipoBeneficiario+'" name="beneficiario'+beneficiario.idTipoBeneficiario+'"><span class="fa fa-edit"></span> Comentar</button>'
            };
        }

        if(beneficiario.registro_avance.length){
            var avance = beneficiario.registro_avance[0];
            datos_grid[beneficiario.idTipoBeneficiario][beneficiario.sexo+'-avance'] += parseInt(avance.total) || 0;
            datos_grid[beneficiario.idTipoBeneficiario]['total-avance'] += parseInt(avance.total) || 0;
        }

        datos_grid[beneficiario.idTipoBeneficiario][beneficiario.sexo] += beneficiario.total;
        datos_grid[beneficiario.idTipoBeneficiario]['total'] += beneficiario.total;
    }
	
    var datos = [];

    for(var i in datos_grid){
        datos_grid[i].f = datos_grid[i].f.format();
        datos_grid[i].m = datos_grid[i].m.format();
        datos_grid[i].total = datos_grid[i].total.format();
        datos_grid[i]['f-avance'] = datos_grid[i]['f-avance'].format();
        datos_grid[i]['m-avance'] = datos_grid[i]['m-avance'].format();
        datos_grid[i]['total-avance'] = datos_grid[i]['total-avance'].format();

        datos.push(datos_grid[i]);
    }
    
    beneficiariosDatagrid.cargarDatos(datos);                         
	
	for(var cont in response.data)
	{
		var coment = response.data[cont];
		var cuantos = coment.comentarios.length;
		
		//console.log(coment);
		
		for(var j =0; j<cuantos; j++ )
		{
			if(coment.comentarios[j].mes == $('#mes').val())
			{
				var objetoAColorear = '#'+coment.comentarios[j].idCampo;
				$(objetoAColorear).removeClass('btn-default');
				$(objetoAColorear).addClass('btn-warning');					
				//console.log(objetoAColorear);
				comentariosArray.push([coment.comentarios[j].id,coment.comentarios[j].idCampo, coment.comentarios[j].observacion,'1']);
			}
		}
		//console.log(comentariosArray);
	}
		
    var total = parseInt(datos.length/beneficiariosDatagrid.rxpag); 
    var plus = parseInt(datos.length)%beneficiariosDatagrid.rxpag;
    if(plus>0) 
        total++;
    beneficiariosDatagrid.paginacion(total);
}
/********************************************************************************************************************************
        Fin: Seguimiento de Beneficiarios
*********************************************************************************************************************************/

/********************************************************************************************************************************
        Inicio: Formulario de Analisis Funcional
*********************************************************************************************************************************/
if($('#id-analisis').val()){
    var parametros = 'mostrar=analisis-funcional';
    moduloResource.get($('#id-analisis').val(),parametros,{
        _success: function(response){
            $('#lbl-finalidad').html(response.data.finalidadProyecto);
            $('#lbl-analisis-resultado').html(response.data.analisisResultado);
            $('#lbl-analisis-beneficiarios').html(response.data.beneficiarios);
            $('#lbl-justificacion-global').html(response.data.justificacionGlobal);
			for(var coment in response.data.comentarios)
			{
				var idCampo = response.data.comentarios[coment].idCampo;
				var objetoAColorear = '#lbl-'+idCampo;
				$(objetoAColorear).parent().parent().addClass('has-error has-feedback');
				
				comentariosArray.push([response.data.comentarios[coment]['id'],response.data.comentarios[coment]['idCampo'], response.data.comentarios[coment]['observacion'],'4']);
			}
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
else
{
	//console.log($('#id').val());
	//console.log($('#mes').val());
	var parametros = {'mostrar':'comentarios-proyecto-mes','mes':$('#mes').val()};
    moduloResource.get($('#id').val(),parametros,{
        _success: function(response){
			//console.log(response.data);            
			for(var coment in response.data)
			{
				var idCampo = response.data[coment].idCampo;
				var objetoAColorear = '#lbl-'+idCampo;
				$(objetoAColorear).parent().parent().addClass('has-error has-feedback');				
				comentariosArray.push([response.data[coment]['id'],response.data[coment]['idCampo'], response.data[coment]['observacion'],'4']);
			}
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
/********************************************************************************************************************************
        Fin: Formulario de Analisis Funcional
*********************************************************************************************************************************/

/********************************************************************************************************************************
        Inicio: Funciones extras de utileria
*********************************************************************************************************************************/
function sumar_valores(identificador,resultado){
    var sumatoria = 0;
    $(identificador).each(function(){
        sumatoria += parseFloat($(this).val()) || 0;
    });
    if($(resultado).is('input')){
        $(resultado).val(sumatoria.format()).change();
    }else{
        $(resultado).text(sumatoria.format());
    }
}

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

/********************************************************************************************************************************
        Fin: Funciones extras de utileria
*********************************************************************************************************************************/

/********************************************************************************************************************************
        Inicio: Funciones de comentarios
*********************************************************************************************************************************/

function escribirComentario(idcampo,nombrecampo,objetoconinformacion,tipocomentario,iddelelemento)
{	
	$('#modalComentario').find(".modal-title").html("<i class=\"fa fa-pencil-square-o\"></i> Escribir comentario");    
	$('#lbl-nombredelcampo').html(nombrecampo);
	$('#idcampo').val(idcampo);
	
	if(tipocomentario=='nivel')
	{
		if($('#nivel').val()=='actividad')
			$('#tipocomentario').val('3');
		else if($('#nivel').val()=='componente')
			$('#tipocomentario').val('2');
			
		$('#idelemento').val($('#id-accion').val());
	}
	else
	{
		$('#tipocomentario').val(tipocomentario);
	
		if(idcampo.substr(0,12)=='beneficiario')
			$('#idelemento').val(iddelelemento);
		else
			$('#idelemento').val($('#'+iddelelemento).val());	
	}
		
	if(idcampo.substr(0,12) == 'beneficiario')
		$('#lbl-informacioncampo').text(objetoconinformacion);
	/*else if(idcampo.substr(0,10) == 'documentos')
		$('#lbl-informacioncampo').text(objetoconinformacion);
	else if(idcampo.substr(0,12) == 'antecedentes')
		$('#lbl-informacioncampo').text(objetoconinformacion);*/
	else
		$('#lbl-informacioncampo').text($('#'+objetoconinformacion).text());
	var borrarAreaText = 1;
	
	for(var i = 0; i < comentariosArray.length; i++)
	{
		var arrayTemporal = comentariosArray[i];
		if(arrayTemporal[1]==idcampo)
		{
			$('#idproyectocomentarios').val(arrayTemporal[0]);
			$('#comentario').val(arrayTemporal[2]);
			borrarAreaText = 0;
		}
	}
	if(borrarAreaText)
	{
		$('#comentario').val('');
		$('#idproyectocomentarios').val('');
	}
    $('#modalComentario').modal('show');
}

$('#btnGuardarComentario').on('click',function(){
	
	var parametros = $(formComentario).serialize();
	parametros = parametros + '&idproyecto=' + $('#id').val()+'&tipocomentario='+$('#tipocomentario').val();
	
	var objetoQueSeColorea = '';
	
	for(var i=0; i<$('#idcampo').val().length; i++)
		if($('#idcampo').val().substr(i,1)!='|')
			objetoQueSeColorea += $('#idcampo').val().substr(i,1);
	
	if($('#comentario').val()=="")
	{
		MessageManager.show({data:'Debe escribir un comentario antes de guardar',type:'ADV',timer:3});		
	}
	else
	{
		if($('#idproyectocomentarios').val()=='')//Nuevo comentario
		{
			moduloResource.post(parametros,{
		        _success: function(response){
	    	        MessageManager.show({data:'Datos del comentario almacenados con éxito',type:'OK',timer:3});	
					if($('#idcampo').val().substr(0,12)=='beneficiario')
					{
						var objetoAColorear = '#'+objetoQueSeColorea;
						$(objetoAColorear).removeClass('btn-default');
						$(objetoAColorear).addClass('btn-warning');	
					}
					else if($('#idcampo').val().substr(0,10)=='documentos')
					{
						var objetoAColorear = '#'+objetoQueSeColorea;
						$(objetoAColorear).removeClass('btn-default');
						$(objetoAColorear).addClass('btn-warning');
					}
					else if($('#idcampo').val().substr(0,12)=='antecedentes')
					{
						var objetoAColorear = '#'+objetoQueSeColorea;
						$(objetoAColorear).removeClass('btn-default');
						$(objetoAColorear).addClass('btn-warning');
					}
					else if($('#idcampo').val().substr(0,8)=='partidas')
					{
						var objetoAColorear = '#'+objetoQueSeColorea;
						$(objetoAColorear).removeClass('btn-default');
						$(objetoAColorear).addClass('btn-warning');
					}
					else if($('#idcampo').val().substr(0,8)=='desglose')
					{
						var objetoAColorear = '#'+objetoQueSeColorea;
						$(objetoAColorear).removeClass('btn-default');
						$(objetoAColorear).addClass('btn-warning');
					}
					else if(objetoQueSeColorea=='avancesmetas')
					{
						$('#'+objetoQueSeColorea).removeClass('btn-default');
						$('#'+objetoQueSeColorea).addClass('btn-warning');
					}
					else
					{
						var objetoAColorear = '#lbl-'+objetoQueSeColorea;
						$(objetoAColorear).parent().parent().addClass('has-error has-feedback');
					}
					
					comentariosArray.push([response.data.id, $('#idcampo').val(), $('#comentario').val(), $('#tipocomentario').val()]);
	            	$('#modalComentario').modal('hide');
	    	    },
	        	_error: function(response){
	            	try{
	                	var json = $.parseJSON(response.responseText);
		                if(!json.code)
		                    MessageManager.show({code:'S03',data:"Hubo un problema al realizar la transacción, inténtelo de nuevo o contacte con soporte técnico."});
	    	            else{
	        	        	//json.container = modal_actividad + ' .modal-body';
	            	        MessageManager.show(json);
		                }
		                Validation.formValidate(json.data);
	    	        }catch(e){
	        	        console.log(e);
	            	}
				}
		    });
		}
		else //Modificar comentario
		{
			moduloResource.put($('#idproyectocomentarios').val(),parametros,{
	        	_success: function(response){
	    	        MessageManager.show({data:'Datos del comentario almacenados con éxito',type:'OK',timer:3});					
					for(var i = 0; i < comentariosArray.length; i++)
						if(comentariosArray[i][0]==$('#idproyectocomentarios').val())
							comentariosArray[i][2]=$('#comentario').val();
		            $('#modalComentario').modal('hide');
	        	},
	    	    _error: function(response){
		            try{
		                var json = $.parseJSON(response.responseText);
	                	if(!json.code)
	            	        MessageManager.show({code:'S03',data:"Hubo un problema al realizar la transacción, inténtelo de nuevo o contacte con soporte técnico."});
	        	        else{
	    	            	//json.container = modal_actividad + ' .modal-body';
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
});


$('#btnQuitarComentario').on('click',function(){
	if($('#idproyectocomentarios').val()=='')//Nunca se guardó, por eso, no se borrará, solamente se cierra la ventana del modal
	{
		MessageManager.show({data:'Debe almacenar un comentario, antes de eliminarlo',type:'ADV',timer:3});
		$('#modalComentario').modal('hide');
	}
	else
	{
		Confirm.show({
			titulo:"Eliminar comentario",
			mensaje: "¿Estás seguro de eliminar el comentario seleccionado?",
				//si: 'Actualizar',
				//no: 'No, gracias',
				callback: function(){
					moduloResource.delete($('#idproyectocomentarios').val(),null,{
                        _success: function(response){ 
                        	MessageManager.show({data:'Comentario eliminado con éxito.',type:'ADV',timer:3});

							var arrayTemporal = [];
							
							for(var i = 0; i < comentariosArray.length; i++)
								if(comentariosArray[i][0]!=$('#idproyectocomentarios').val())
									arrayTemporal.push([comentariosArray[i][0],comentariosArray[i][1],comentariosArray[i][2],comentariosArray[i][3]]);

							comentariosArray.length=0;
							comentariosArray = arrayTemporal;							
							//console.log(comentariosArray);
							
							var objetoADesColorear = '';
							for(var i=0; i<$('#idcampo').val().length; i++)
								if($('#idcampo').val().substr(i,1)!='|')
									objetoADesColorear += $('#idcampo').val().substr(i,1);
							
							if($('#idcampo').val().substr(0,12)=='beneficiario')
							{
								objetoADesColorear = '#'+objetoADesColorear;
								$(objetoADesColorear).removeClass('btn-warning');
								$(objetoADesColorear).addClass('btn-default');	
							}
							else if($('#idcampo').val().substr(0,10)=='documentos')
							{
								objetoADesColorear = '#'+objetoADesColorear;
								$(objetoADesColorear).removeClass('btn-warning');
								$(objetoADesColorear).addClass('btn-default');	
							}
							else if($('#idcampo').val().substr(0,12)=='antecedentes')
							{
								objetoADesColorear = '#'+objetoADesColorear;
								$(objetoADesColorear).removeClass('btn-warning');
								$(objetoADesColorear).addClass('btn-default');	
							}
							else if($('#idcampo').val().substr(0,8)=='partidas')
							{
								objetoADesColorear = '#'+objetoADesColorear;
								$(objetoADesColorear).removeClass('btn-warning');
								$(objetoADesColorear).addClass('btn-default');	
							}
							else if($('#idcampo').val().substr(0,8)=='desglose')
							{
								objetoADesColorear = '#'+objetoADesColorear;
								$(objetoADesColorear).removeClass('btn-warning');
								$(objetoADesColorear).addClass('btn-default');	
							}
							else if(objetoADesColorear=='avancesmetas')
							{
								objetoADesColorear = '#'+objetoADesColorear;
								$(objetoADesColorear).removeClass('btn-warning');
								$(objetoADesColorear).addClass('btn-default');
							}
							else
							{
								objetoADesColorear = '#lbl-'+objetoADesColorear;
								$(objetoADesColorear).parent().parent().removeClass('has-error has-feedback');
							}
							$('#modalComentario').modal('hide');							
                        },
                        _error: function(jqXHR){ 
                        	MessageManager.show(jqXHR.responseJSON);
                        }
        			});
				}
		});
	}
});

/********************************************************************************************************************************
        Fin: Funciones de comentarios
*********************************************************************************************************************************/

$('#btnAprobarProyecto').on('click',function(){
	
	var parametros = 'actualizarproyecto=aprobar';	
	Confirm.show({
				titulo:"¿Aprobar el avance de este mes?",
				mensaje: "¿Estás seguro de aprobar el avance del mes? Una vez realizado esto, no es posible comentar o corregir por el mes corriente",
				callback: function(){
					moduloResource.put($('#id').val(),parametros,{
			        	_success: function(response){
			    	        MessageManager.show({data:'Se ha aprobado el avance mensual del proyecto',type:'OK',timer:3});
							
							if($('#id_clasificacion').val()=='1')
								window.location.href = SERVER_HOST+'/revision/segui-proyectos-inst';
							else if($('#id_clasificacion').val()=='2')
								window.location.href = SERVER_HOST+'/revision/segui-proyectos-inv';
							

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
});



$('#btnRegresarCorregir').on('click',function(){
	
	var parametros = 'actualizarproyecto=regresar';	
	Confirm.show({
				titulo:"¿Regresar avance del mes a corrección?",
				mensaje: "¿Estás seguro de enviar el avance del mes a corregir? Debe de haber escrito al menos una observación",
				callback: function(){
					moduloResource.put($('#id').val(),parametros,{
			        	_success: function(response){
			    	        MessageManager.show({data:'Se ha regresado para corregir el avance mensual del proyecto',type:'OK',timer:3});
							if($('#id_clasificacion').val()=='1')
								window.location.href = SERVER_HOST+'/revision/segui-proyectos-inst';
							else if($('#id_clasificacion').val()=='2')
								window.location.href = SERVER_HOST+'/revision/segui-proyectos-inv';
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
	
});

