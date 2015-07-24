/*=====================================

    # Nombre:
        vista-rendicion-cuentas.js

    # Módulo:
        seguimiento/editar-avance

    # Descripción:
        Para rendición de cuentas de proyectos

=====================================*/

// Inicialización General para casi cualquier módulo
var moduloResource = new RESTfulRequests(SERVER_HOST+'/v1/visor-directivo');

$('#btn-proyecto-cancelar').on('click',function(){
    if($(this).attr('data-clase-proyecto') == 1){
        window.location.href = SERVER_HOST+'/visor-directivo/proyectos-inst';
    }else if($(this).attr('data-clase-proyecto') == 2){
        window.location.href = SERVER_HOST+'/visor-directivo/proyectos-inv';
    }
});

var google_api = false;
var charts = {};
var charts_data = {};

// Load the Visualization API and the piechart package.
google.load('visualization', '1.0', {'packages':['corechart']});
// Set a callback to run when the Google Visualization API is loaded.
google.setOnLoadCallback(inicializar);

/********************************************************************************************************************************
        Inicio: Seguimiento de Metas
*********************************************************************************************************************************/
var accionesDatagrid = new Datagrid("#datagridAcciones",moduloResource,{ formatogrid:true, pagina: 1, idProyecto: $('#id').val(), grid:'rendicion-acciones'});
accionesDatagrid.init();
accionesDatagrid.actualizar({ 
    _success: function(response){ 
        accionesDatagrid.limpiar();
        var datos_grid = [];
        var contador_componente = 0;
        for(var i in response.data.componentes){
            var contador_actividad = 0;
            contador_componente++;
            var componente = response.data.componentes[i];

            var item = {};
            item.id = '1-' + componente.id;
            item.nivel = 'C ' + contador_componente;
            item.indicador = componente.indicador;
            item.meta = (parseFloat(componente.valorNumerador) || 0).format(2);
            item.meta_acumulada = 0;
            item.avances_acumulados = 0;
            item.avances_mes = 0;

            var mes = $('#mes').val();
            
            if(componente.registro_avance.length){
                for(var j in componente.registro_avance){
                    var avance = componente.registro_avance[j];
                    item.avances_acumulados += parseFloat(avance.avanceMes);
                    if(avance.mes == mes){
                        item.avances_mes += parseFloat(avance.avanceMes);                        
                    }
                }
            }
            item.avances_acumulados = item.avances_acumulados.format(2);
            item.avances_mes = item.avances_mes.format(2);
            datos_grid.push(item);

            for(var j in componente.actividades){
                contador_actividad++;
                var actividad = componente.actividades[j];
                var item = {};
                item.id = '2-' + actividad.id;
                item.nivel = 'A ' + contador_componente + '.' + contador_actividad;
                item.indicador = actividad.indicador;
                item.meta = (parseFloat(actividad.valorNumerador) || 0).format(2);
                item.meta_acumulada = 0;
                item.avances_acumulados = 0;
                item.avances_mes = 0;
                
                if(actividad.registro_avance.length){
                    for(var j in actividad.registro_avance){
                        var avance = actividad.registro_avance[j];
                        item.avances_acumulados += parseFloat(avance.avanceMes);
                        if(avance.mes == mes){
                            item.avances_mes += parseFloat(avance.avanceMes);
                        }
                    }
                }
                item.avances_acumulados = item.avances_acumulados.format(2);
                item.avances_mes = item.avances_mes.format(2);
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
});

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

            $('#indicador').text(response.data.indicador);
            $('#unidad-medida').text(response.data.unidad_medida.descripcion);
            $('#meta-total').text((parseFloat(response.data.valorNumerador) || 0).format(2));
            
            $('#id-accion').val(response.data.id);

            var total_meta_mes = 0;
            var total_meta_acumulada = 0;
            var total_avance_acumulado = 0;
            var total_avance_mes = 0;
            var total_avance_total = 0;

            var rendicion = {};

            if(response.data.metas_mes_jurisdiccion.length == 0){
                var avances_jurisdicciones = false;
                $('#grafica_cumplimiento_jurisdiccion').html('<div class="alert alert-info">No se tienen avances resgistrados para ninguna Jurisdicción</div>');
            }else{
                var avances_jurisdicciones = [['Jurisdiccion','Porcentaje',{role:'style'},{role:'annotation'},{role:'tooltip',p:{html:true}}]];
            }

            for(var i in response.data.metas_mes_jurisdiccion){
                var dato = response.data.metas_mes_jurisdiccion[i];

                if(dato.claveJurisdiccion == 'OC'){
                    dato.jurisdiccion = 'OFICINA CENTRAL';
                }

                var meta = parseFloat(dato.meta) || 0;
                var avance = parseFloat(dato.avance) || 0;

                var objeto = {
                    metaMes:0,
                    metaAcumulada:meta,
                    avanceAcumulado:avance,
                    avanceMes:0,
                    avanceTotal:avance,
                    porcentaje:0,
                    estatus:1
                };

                total_meta_acumulada += objeto.metaAcumulada;
                total_avance_total += objeto.avanceTotal;

                if(objeto.metaAcumulada > 0){
                    var porcentaje = (objeto.avanceTotal*100) / objeto.metaAcumulada;
                }else{
                    var porcentaje = (objeto.avanceTotal*100);
                }
                
                objeto.porcentaje = porcentaje;
                
                var estatus = '#4B804C';
                if(!(objeto.metaAcumulada == 0 && objeto.avanceTotal == 0)){
                    if(porcentaje > 110){
                        estatus = '#A94442';
                        objeto.estatus = 3;
                    }else if(porcentaje < 90){
                        estatus = '#A94442';
                        objeto.estatus = 2;
                    }else if(porcentaje > 0 && objeto.metaAcumulada == 0){
                        estatus = '#A94442';
                        objeto.estatus = 3;
                    }
                }

                avances_jurisdicciones.push([
                    dato.claveJurisdiccion,porcentaje,estatus,(porcentaje.format(2)) + '%',
                    '<table border="0" cellpadding="0" cellspacing="0"><tr><th class="text-center" style="white-space:nowrap;" colspan="2"><big>'+dato.jurisdiccion+'</big></th></tr><tr><td style="white-space:nowrap;">Avance: </td><th class="text-center" style="color:'+estatus+';font-weight:bold;">'+(porcentaje.format(2))+'%</th></tr></table>'
                ]);

                rendicion[dato.claveJurisdiccion] = objeto;
            }

            charts_data['jurisdiccion'] = avances_jurisdicciones;

            var metas = {};
            for(var i in response.data.metas_mes_agrupado){
                var programado = response.data.metas_mes_agrupado[i];
                /*
                meta += parseFloat(programado.meta) || 0;
                avance += parseFloat(programado.avance) || 0;

                var porcentaje = 0;
                if(meta > 0){
                    porcentaje = (avance*100) / meta;
                }else{
                    porcentaje = (avance*100);
                }
                
                var estatus = 1;
                if(!(meta == 0 && avance == 0)){
                    if(porcentaje > 110){
                        estatus = 3;
                    }else if(porcentaje < 90){
                        estatus = 2;
                    }else if(porcentaje > 0 && meta == 0){
                        estatus = 3;
                    }
                }*/

                metas[programado.mes] = {
                    metaMes:parseFloat(programado.meta) || 0,
                    avanceMes:parseFloat(programado.avance) || 0
                }
            }

            var avance_acumulado_mes = [];
            var meses = ['Enero','Febrero','Marzo','Abril','Mayo','Junio','Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre'];
            var meta_acumulada = 0;
            var avance_acumulado = 0;
            var mes_actual = $('#mes').val();
            var meta_total = parseFloat(response.data.valorNumerador) || 0;

            for (var i = 1; i <= 12; i++){
                var mes = [];
                mes.push(meses[i-1].substring(0,3));

                var meta_actual = 0;
                var avance_actual = 0;

                if(metas[i]){
                    meta_acumulada += metas[i].metaMes;
                    avance_acumulado += metas[i].avanceMes;
                    meta_actual = metas[i].metaMes;
                    avance_actual = metas[i].avanceMes;
                }

                //mes.push(meta_acumulada);
                mes.push((meta_acumulada*100)/meta_total);
                mes.push('<table border="0" cellpadding="0" cellspacing="0"><tr><th class="text-center" style="white-space:nowrap;" colspan="2"><big>'+meses[i-1]+' '+((meta_acumulada*100)/meta_total).format(2)+'%</big></th></tr><tr><td style="white-space:nowrap;">Meta Acumulada: </td><th class="text-center text-info">'+meta_acumulada.format(2)+'</th></tr></table>');

                if(meta_actual > 0){
                    mes.push((meta_acumulada*100)/meta_total);
                    mes.push('<table border="0" cellpadding="0" cellspacing="0"><tr><th class="text-center" style="white-space:nowrap;" colspan="2"><big>'+meses[i-1]+' '+((meta_acumulada*100)/meta_total).format(2)+'%</big></th></tr><tr><td style="white-space:nowrap;">Meta del Mes: </td><th class="text-center text-primary">'+metas[i].metaMes.format(2)+'</th></tr><tr><td style="white-space:nowrap;">Meta Acumulada: </td><th class="text-center text-info">'+meta_acumulada.format(2)+'</th></tr></table>');
                }else{
                    mes.push(null);mes.push(null);
                }

                if(i <= mes_actual){
                    if(meta_acumulada > 0){
                        var porcentaje = (avance_acumulado*100) / meta_acumulada;
                    }else{
                        var porcentaje = (avance_acumulado*100);
                    }

                    //mes.push(avance_acumulado);
                    var porcentaje_del_mes = (avance_acumulado*100)/meta_total;
                    mes.push(porcentaje_del_mes);
                    mes.push('<table border="0" cellpadding="0" cellspacing="0"><tr><th class="text-center" colspan="2"><big>'+meses[i-1]+' '+porcentaje_del_mes.format(2)+'%</big></th></tr><tr><td style="white-space:nowrap;">Meta Acumulada: </td><th class="text-center text-info"> '+meta_acumulada.format(2)+'</th></tr><tr><td style="white-space:nowrap;">Avance Acumulado: </td><th class="text-center text-primary"> '+avance_acumulado.format(2)+'</th></tr><tr><td style="white-space:nowrap;">Porcentaje del Mes: </td><th class="text-center text-success"> '+(porcentaje.format(2))+'%</th></tr></table>');

                    var clase = 'text-success';
                    var icono = '';
                    var estatus = 1;
                    if(!(meta_acumulada == 0 && avance_acumulado == 0)){
                        if(porcentaje > 110){
                            clase = 'text-danger';
                            estatus = 3;
                            icono = 'fa-arrow-up';
                        }else if(porcentaje < 90){
                            clase = 'text-danger';
                            estatus = 2;
                            icono = 'fa-arrow-down';
                        }else if(porcentaje > 0 && meta_acumulada == 0){
                            clase = 'text-danger';
                            estatus = 3;
                            icono = 'fa-arrow-up';
                        }
                    }

                    if(estatus == 3){
                        mes.push(null);mes.push(null); //Avance Normal
                        mes.push(porcentaje_del_mes);
                        mes.push('<table border="0" cellpadding="0" cellspacing="0"><tr><th class="text-center" colspan="2"><big>'+meses[i-1]+' '+porcentaje_del_mes.format(2)+'%</big></th></tr><tr><td style="white-space:nowrap;">Meta Acumulada: </td><th class="text-center text-info"> '+meta_acumulada.format(2)+'</th></tr><tr><td style="white-space:nowrap;">Avance Acumulado: </td><th class="text-center text-primary"> '+avance_acumulado.format(2)+'</th></tr><tr><td style="white-space:nowrap;">Porcentaje de Avance: </td><th class="text-center text-danger"> '+(porcentaje.format(2))+'%</th></tr></table>');
                        mes.push(null);mes.push(null); //Bajo Avance
                    }else if(estatus == 2){
                        mes.push(null);mes.push(null); //Avance Normal
                        mes.push(null);mes.push(null); //Alto Avance
                        mes.push(porcentaje_del_mes);
                        mes.push('<table border="0" cellpadding="0" cellspacing="0"><tr><th class="text-center" colspan="2"><big>'+meses[i-1]+' '+porcentaje_del_mes.format(2)+'%</big></th></tr><tr><td style="white-space:nowrap;">Meta Acumulada: </td><th class="text-center text-info"> '+meta_acumulada.format(2)+'</th></tr><tr><td style="white-space:nowrap;">Avance Acumulado: </td><th class="text-center text-primary"> '+avance_acumulado.format(2)+'</th></tr><tr><td style="white-space:nowrap;">Porcentaje de Avance: </td><th class="text-center text-danger"> '+(porcentaje.format(2))+'%</th></tr></table>');
                    }else if(estatus == 1){
                        mes.push(porcentaje_del_mes);
                        mes.push('<table border="0" cellpadding="0" cellspacing="0"><tr><th class="text-center" colspan="2"><big>'+meses[i-1]+' '+porcentaje_del_mes.format(2)+'%</big></th></tr><tr><td style="white-space:nowrap;">Avance del Mes: </td><th class="text-center text-success"> '+avance_actual.format(2)+'</th></tr><tr><td style="white-space:nowrap;">Meta Acumulada: </td><th class="text-center text-info"> '+meta_acumulada.format(2)+'</th></tr><tr><td style="white-space:nowrap;">Avance Acumulado: </td><th class="text-center text-primary"> '+avance_acumulado.format(2)+'</th></tr><tr><td style="white-space:nowrap;">Porcentaje de Avance: </td><th class="text-center text-success"> '+(porcentaje.format(2))+'%</th></tr></table>');
                        mes.push(null);mes.push(null); //Alto Avance
                        mes.push(null);mes.push(null); //Bajo Avance
                    }else{
                        mes.push(null);mes.push(null); //Avance Normal
                        mes.push(null);mes.push(null); //Alto Avance
                        mes.push(null);mes.push(null); //Bajo Avance
                    }
                }else{
                    mes.push(null);mes.push(null); //Avance Acumulado
                    mes.push(null);mes.push(null); //Avance del Mes
                    mes.push(null);mes.push(null); //Alto Avance
                    mes.push(null);mes.push(null); //Bajo Avance
                }
                avance_acumulado_mes.push(mes);
            }
            
            charts_data['mensual'] = avance_acumulado_mes;
            
            if(google_api){
                generateCharts();
            }else{
                $('#grafica_cumplimiento_mensual,#grafica_cumplimiento_jurisdiccion').html('<div class="alert alert-info"><span class="fa fa-spinner fa-spin"></span> Cargando Librerias... Por favor espere... </div>');
            }

            for(var i in response.data.metas_mes){
                var dato = response.data.metas_mes[i];

                rendicion[dato.claveJurisdiccion].metaMes = parseFloat(dato.meta) || 0;
                total_meta_mes += rendicion[dato.claveJurisdiccion].metaMes;

                rendicion[dato.claveJurisdiccion].avanceMes = parseFloat(dato.avance) || 0;
                rendicion[dato.claveJurisdiccion].avanceAcumulado -= rendicion[dato.claveJurisdiccion].avanceMes;
                
                total_avance_acumulado += rendicion[dato.claveJurisdiccion].avanceAcumulado;
                total_avance_mes += rendicion[dato.claveJurisdiccion].avanceMes;
            }

            for(var i in rendicion){
                $('#meta-mes-'+i).text(rendicion[i].metaMes.format(2));
                $('#meta-acumulada-'+i).text(rendicion[i].metaAcumulada.format(2));
                $('#avance-acumulado-'+i).text(rendicion[i].avanceAcumulado.format(2));
                $('#avance-mes-'+i).text(rendicion[i].avanceMes.format(2));
                $('#avance-total-'+i).text(rendicion[i].avanceTotal.format(2));

                var clase = 'text-success';
                var icono = '';
                if(rendicion[i].estatus == 2){
                    clase = 'text-danger';
                    icono = '<span class="fa fa-arrow-down"></span> ';
                }else if(rendicion[i].estatus == 3){
                    clase = 'text-danger';
                    icono = '<span class="fa fa-arrow-up"></span> ';
                }

                $('#porcentaje-mes-'+i).html('<span class="'+clase+'">'+icono+rendicion[i].porcentaje.format(2)+' %</span>');
            }

            if(response.data.registro_avance.length){
                $('#analisis-resultados').text(response.data.registro_avance[0].analisisResultados);
                $('#justificacion-acumulada').text(response.data.registro_avance[0].justificacionAcumulada);
            }

            if(response.data.plan_mejora.length){
                $('#tab-link-plan-mejora').removeClass('hidden');
                var plan_mejora = response.data.plan_mejora[0];
                $('#accion-mejora').text(plan_mejora.accionMejora);
                $('#grupo-trabajo').text(plan_mejora.grupoTrabajo);
                $('#documentacion-comprobatoria').text(plan_mejora.documentacionComprobatoria);
                $('#fecha-inicio').text(plan_mejora.fechaInicio);
                $('#fecha-termino').text(plan_mejora.fechaTermino);
                $('#fecha-notificacion').text(plan_mejora.fechaNotificacion);
            }else{
                $('#tab-link-plan-mejora').addClass('hidden');
            }

            $('#total-meta-mes').text(total_meta_mes);
            $('#total-meta-acumulada').text(total_meta_acumulada);
            $('#total-avance-acumulado').text(total_avance_acumulado);
            $('#total-avance-mes').text(total_avance_mes);
            $('#total-avance-total').text(total_avance_total);

            if(total_meta_acumulada > 0){
                var porcentaje = (total_avance_total*100) / total_meta_acumulada;
            }else{
                var porcentaje = (total_avance_total*100);
            }
            var clase = 'text-success';
            var icono = '';
            if(!(total_meta_acumulada == 0 && total_avance_total == 0)){
                if(porcentaje > 110){
                    clase = 'text-danger';
                    icono = '<span class="fa fa-arrow-up"></span> ';
                }else if(porcentaje < 90){
                    clase = 'text-danger';
                    icono = '<span class="fa fa-arrow-down"></span> ';
                }else if(porcentaje > 0 && total_meta_acumulada == 0){
                    clase = 'text-danger';
                    icono = '<span class="fa fa-arrow-up"></span> ';
                }
            }
            $('#total-porcentaje-mes').html('<span class="'+clase+'">'+icono+porcentaje.format(2)+' %</span>');

            if(response.data.desglose_municipios){
                if(response.data.desglose_municipios.length){
                    asignar_municipios(response.data.desglose_municipios);
                    $('input.avance-mes').attr('disabled',true);
                }
            }

            $('#tabs-seguimiento-metas a:first').tab('show');
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
                html_rows += '<tr data-clave-localidad="'+dato.claveLocalidad+'">'+
                                '<td>' + dato.claveLocalidad + ' - ' + dato.localidad + '</td>' +
                                '<td class="localidad-metas-acumuladas bg-success" data-metas-acumuladas="'+datos_acumulado.meta+'">'+datos_acumulado.meta.format(2)+'</td>' + 
                                '<td class="localidad-meta-mes" data-meta-mes="'+datos_meta.meta+'">'+datos_meta.meta.format(2)+'</td>' +
                                '<td><div class="form-group" style="margin:0;"><input name="localidad-avance-mes['+dato.claveLocalidad+']" id="localidad_avance_mes_'+dato.claveLocalidad+'" type="number" class="form-control localidad-avance" value="'+datos_meta.avance+'" data-localidad="'+dato.claveLocalidad+'" data-jurisdiccion="'+jurisdiccion+'"></div></td>' + 
                                '<td class="localidad-avance-acumulado" data-avance-acumulado="'+avance_anterior+'">'+avance_anterior.format(2)+'</td>' +
                                '<td class="localidad-total-avance bg-info" data-avance-total="'+datos_acumulado.avance+'">'+datos_acumulado.avance.format(2)+'</td>'+
                                '</tr>';
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

$('#modalEditarAvance').on('hide.bs.modal',function(e){
    $('#modalEditarAvance .alert').remove();
    $('.valores').html('<span class="text-muted">0</span>');
});

$('#tablink-cumplimiento-mensual').on('shown.bs.tab',function (e){ resizeCharts('mensual'); });
$('#tablink-cumplimiento-jurisdiccion').on('shown.bs.tab',function (e){ resizeCharts('jurisdiccion'); });
/********************************************************************************************************************************
        Fin: Seguimiento de Metas
*********************************************************************************************************************************/

/********************************************************************************************************************************
        Inicio: Funciones para gráficas
*********************************************************************************************************************************/
function inicializar(){
    google_api = true;
    if (document.addEventListener) {
        window.addEventListener('resize', resizeCharts);
    }
    else if (document.attachEvent) {
        window.attachEvent('onresize', resizeCharts);
    }
    else {
        window.resize = resizeCharts;
    }
    generateCharts();
}

function resizeCharts (key) {
    if(key){
        if(charts[key]){
            var chart = charts[key].chart;
            var data = charts[key].data;
            var options = charts[key].options;
            chart.draw(data, options);
        }
    }else{
        for(var i in charts){
            var chart = charts[i].chart;
            var data = charts[i].data;
            var options = charts[i].options;
            chart.draw(data, options);
        }
    }
}
function generateCharts(){
    if(charts_data['mensual']){
        var data = new google.visualization.DataTable();
        data.addColumn('string', 'X');
        data.addColumn('number', 'Meta Acumulada');
        data.addColumn({type:'string',role:'tooltip',p:{html:true}});
        data.addColumn('number', 'Meta del Mes');
        data.addColumn({type:'string',role:'tooltip',p:{html:true}});
        data.addColumn('number', 'Avance Acumulado');
        data.addColumn({type:'string',role:'tooltip',p:{html:true}});
        data.addColumn('number', 'Avance del Mes');
        data.addColumn({type:'string',role:'tooltip',p:{html:true}});
        data.addColumn('number', 'Alto Avance');
        data.addColumn({type:'string',role:'tooltip',p:{html:true}});
        data.addColumn('number', 'Bajo Avance');
        data.addColumn({type:'string',role:'tooltip',p:{html:true}});
        data.addRows(charts_data['mensual']);
        // Set chart options
        var options = {
            title:'Porcentaje de cumplimiento de metas por mes',
            hAxis: { title: 'Meses' },
            vAxis: {
              title: 'Porcentaje',
              maxValue:100,
              minValue:0
            },
            series: {
              0: {curveType:'none',color:'#7CB5EC',pointSize:0},
              1: {lineWidth:0,color:'#7CB5EC'},
              2: {curveType:'none',color:'#4B804C',pointSize:0},
              3: {lineWidth:0,color:'#4B804C',pointShape:'square',pointSize:10},
              4: {
                    lineWidth:0,
                    color:'#A94442',
                    pointShape: { type: 'triangle' },
                    pointSize:15
                },
              5: {
                    lineWidth:0,
                    color:'#A94442',
                    pointShape: { type: 'triangle',rotation: 180 },
                    pointSize:15
                }
            },
            legend:{ position:'none' },
            tooltip: {isHtml: true},
            pointSize:6
        };
        // Instantiate and draw our chart, passing in some options.
        var chart = new google.visualization.LineChart(document.getElementById('grafica_cumplimiento_mensual'));
        charts['mensual']={chart:chart,data:data,options:options};
    }
    
    if(charts_data['jurisdiccion']){
        var data = new google.visualization.arrayToDataTable(charts_data['jurisdiccion']);
        var options = {
            title: 'Porcentaje de cumplimiento de metas por Jurisdicción con corte al mes actual',
            hAxis: { title: 'Jurisdicciones' },
            vAxis: { title: 'Porcentaje',maxValue:100,minValue:0},
            legend:{ position:'none' },
            annotations: { textStyle: { fontSize:10 }, alwaysOutside:true },
            tooltip: {isHtml: true}
        };
        var chart = new google.visualization.ColumnChart(document.getElementById('grafica_cumplimiento_jurisdiccion'));
        charts['jurisdiccion']={chart:chart,data:data,options:options};
    }else{
        $('#grafica_cumplimiento_jurisdiccion').html('<div class="alert alert-info">No se tienen avances resgistrados o programados para ninguna Jurisdicción</div>');
    }
    resizeCharts();
}
/********************************************************************************************************************************
        Fin: Funciones para gráficas
*********************************************************************************************************************************/

/********************************************************************************************************************************
        Inicio: Funciones extras de utileria
*********************************************************************************************************************************/

/**
 * Number.prototype.format(n, x)
 * 
 * @param integer n: length of decimal
 * @param integer x: length of sections
 */
Number.prototype.format = function(n, x) {
    var re = '\\d(?=(\\d{' + (x || 3) + '})+' + (n > 0 ? '\\.' : '$') + ')';
    //return this.toFixed(Math.max(0, ~~n)).replace(new RegExp(re, 'g'), '$&,');
    var formateado = this.toFixed(Math.max(0, ~~n)).replace(new RegExp(re, 'g'), '$&,');
    var partes = formateado.split('.');
    if(parseInt(partes[1]) == 0){
        return partes[0];
    }else{
        return formateado;
    }
};


/********************************************************************************************************************************
        Fin: Funciones extras de utileria
*********************************************************************************************************************************/