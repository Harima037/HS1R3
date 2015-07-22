/*=====================================

    # Nombre:
        vista-rendicion-cuentas.js

    # Módulo:
        seguimiento/editar-avance

    # Descripción:
        Para rendición de cuentas de proyectos

=====================================*/

// Inicialización General para casi cualquier módulo
if($('#btn-proyecto-cancelar').attr('data-clase-proyecto') == 1){
    var moduloResource = new RESTfulRequests(SERVER_HOST+'/v1/visor-gerencial-inst');
}else if($('#btn-proyecto-cancelar').attr('data-clase-proyecto') == 2){
    var moduloResource = new RESTfulRequests(SERVER_HOST+'/v1/visor-gerencial-inv');
}

$('#btn-proyecto-cancelar').on('click',function(){
    if($(this).attr('data-clase-proyecto') == 1){
        window.location.href = SERVER_HOST+'/visor-gerencial/proyectos-inst';
    }else if($(this).attr('data-clase-proyecto') == 2){
        window.location.href = SERVER_HOST+'/visor-gerencial/proyectos-inv';
    }
});

var google_api = false;

// Load the Visualization API and the piechart package.
google.load('visualization', '1.0', {'packages':['corechart']});
// Set a callback to run when the Google Visualization API is loaded.
google.setOnLoadCallback(inicializar);

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

var charts = {};
var charts_data = {};

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
        var mes_actual = $('#mes').val();
        for(var i in response.data.componentes){
            var contador_actividad = 0;
            contador_componente++;
            var componente = response.data.componentes[i];
            var item = {};
            item.id = '1-' + componente.id;
            item.nivel = 'C ' + contador_componente;
            item.indicador = componente.indicador;
            item.meta = 0;
            item.metaAcumulada = 0;
            item.avanceAcumulado = 0;
            item.avanceMes = 0;

            for(var j in componente.metas_mes){
                item.meta += parseFloat(componente.metas_mes[j].meta);
                if(componente.metas_mes[j].mes <= mes_actual){
                    item.metaAcumulada += parseFloat(componente.metas_mes[j].meta) || 0;
                    item.avanceAcumulado += parseFloat(componente.metas_mes[j].avance) || 0;
                }
                if(componente.metas_mes[j].mes == mes_actual){
                    item.avanceMes = parseFloat(componente.metas_mes[j].avance) || 0;
                }
            }

            item.meta = item.meta.format(2);
            item.metaAcumulada = item.metaAcumulada.format(2);
            item.avanceAcumulado = item.avanceAcumulado.format(2);
            item.avanceMes = item.avanceMes.format(2);

            datos_grid.push(item);

            for(var j in componente.actividades){
                contador_actividad++;
                var actividad = componente.actividades[j];
                var item = {};
                item.id = '2-' + actividad.id;
                item.nivel = 'A ' + contador_componente + '.' + contador_actividad;
                item.indicador = actividad.indicador;
                item.meta = 0;
                item.metaAcumulada = 0;
                item.avanceAcumulado = 0;
                item.avanceMes = 0;

                for(var k in actividad.metas_mes){
                    item.meta += parseFloat(actividad.metas_mes[k].meta);
                    if(actividad.metas_mes[k].mes <= mes_actual){
                        item.metaAcumulada += parseFloat(actividad.metas_mes[k].meta) || 0;
                        item.avanceAcumulado += parseFloat(actividad.metas_mes[k].avance) || 0;
                    }
                    if(actividad.metas_mes[k].mes == mes_actual){
                        item.avanceMes = parseFloat(actividad.metas_mes[k].avance) || 0;
                    }
                }

                item.meta = item.meta.format(2);
                item.metaAcumulada = item.metaAcumulada.format(2);
                item.avanceAcumulado = item.avanceAcumulado.format(2);
                item.avanceMes = item.avanceMes.format(2);
                
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

$('#tablink-cumplimiento-mensual').on('shown.bs.tab',function (e){ resizeCharts('mensual'); });
$('#tablink-cumplimiento-jurisdiccion').on('shown.bs.tab',function (e){ resizeCharts('jurisdiccion'); });

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
            //$('#meta-total').text((parseFloat(response.data.valorNumerador) || 0).format());
            
            $('#id-accion').val(response.data.id);

            var mes_actual = parseInt($('#mes').val());
            var meta_total = 0;
            var metas = {};
            for(var i in response.data.metas_mes){
                var programado = response.data.metas_mes[i];

                metas[programado.mes] = {
                    metaMes:parseFloat(programado.meta)||0,
                    avanceMes:parseFloat(programado.avance)||0
                }
                meta_total += metas[programado.mes].metaMes;
            }

            var meta_acumulada = 0;
            var avance_acumulado = 0;
            var ultimo_estatus = 0;

            var avance_acumulado_mes = [];
            var meses = ['Enero','Febrero','Marzo','Abril','Mayo','Junio','Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre'];

            for (var i = 1; i <= 12; i++) {
                var mes = [];
                mes.push(meses[i-1].substring(0,3));

                if(metas[i]){
                    meta_acumulada += metas[i].metaMes;
                    avance_acumulado += metas[i].avanceMes;
                }

                //mes.push(meta_acumulada);
                mes.push((meta_acumulada*100)/meta_total);
                mes.push('<table border="0" cellpadding="0" cellspacing="0"><tr><th class="text-center" style="white-space:nowrap;" colspan="2"><big>'+meses[i-1]+' '+((meta_acumulada*100)/meta_total).format(2)+'%</big></th></tr><tr><td style="white-space:nowrap;">Meta Acumulada: </td><th class="text-center text-info">'+meta_acumulada.format(2)+'</th></tr></table>');

                $('#meta-acumulada-'+i).text(meta_acumulada.format(2));

                if(metas[i]){
                    $('#meta-mes-'+i).text(metas[i].metaMes.format(2));
                }else{
                    $('#meta-mes-'+i).text('0.00');
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
                    mes.push('<table border="0" cellpadding="0" cellspacing="0"><tr><th class="text-center" colspan="2"><big>'+meses[i-1]+' '+porcentaje_del_mes.format(2)+'%</big></th></tr><tr><td style="white-space:nowrap;">Meta Acumulada: </td><th class="text-center text-info"> '+meta_acumulada.format(2)+'</th></tr><tr><td style="white-space:nowrap;">Avance Acumulado: </td><th class="text-center text-primary"> '+avance_acumulado.format(2)+'</th></tr><tr><td style="white-space:nowrap;">Avance del Mes: </td><th class="text-center text-success"> '+(+porcentaje.format(2))+'%</th></tr></table>');

                    var clase = 'text-success';
                    var icono = '';
                    ultimo_estatus = 1;
                    if(!(meta_acumulada == 0 && avance_acumulado == 0)){
                        if(porcentaje > 110){
                            clase = 'text-danger';
                            ultimo_estatus = 3;
                            icono = 'fa-arrow-up';
                        }else if(porcentaje < 90){
                            clase = 'text-danger';
                            ultimo_estatus = 2;
                            icono = 'fa-arrow-down';
                        }else if(porcentaje > 0 && meta_acumulada == 0){
                            clase = 'text-danger';
                            ultimo_estatus = 3;
                            icono = 'fa-arrow-up';
                        }
                    }

                    if(ultimo_estatus == 3){
                        mes.push(porcentaje_del_mes);
                        mes.push('<table border="0" cellpadding="0" cellspacing="0"><tr><th class="text-center" colspan="2"><big>'+meses[i-1]+' '+porcentaje_del_mes.format(2)+'%</big></th></tr><tr><td style="white-space:nowrap;">Meta Acumulada: </td><th class="text-center text-info"> '+meta_acumulada.format(2)+'</th></tr><tr><td style="white-space:nowrap;">Avance Acumulado: </td><th class="text-center text-primary"> '+avance_acumulado.format(2)+'</th></tr><tr><td style="white-space:nowrap;">Alto Avance: </td><th class="text-center text-danger"> '+(+porcentaje.format(2))+'%</th></tr></table>');
                        mes.push(null);
                        mes.push(null);
                    }else if(ultimo_estatus == 2){
                        mes.push(null);
                        mes.push(null);
                        mes.push(porcentaje_del_mes);
                        mes.push('<table border="0" cellpadding="0" cellspacing="0"><tr><th class="text-center" colspan="2"><big>'+meses[i-1]+' '+porcentaje_del_mes.format(2)+'%</big></th></tr><tr><td style="white-space:nowrap;">Meta Acumulada: </td><th class="text-center text-info"> '+meta_acumulada.format(2)+'</th></tr><tr><td style="white-space:nowrap;">Avance Acumulado: </td><th class="text-center text-primary"> '+avance_acumulado.format(2)+'</th></tr><tr><td style="white-space:nowrap;">Bajo Avance: </td><th class="text-center text-danger"> '+(+porcentaje.format(2))+'%</th></tr></table>');
                    }else{
                        mes.push(null);
                        mes.push(null);
                        mes.push(null);
                        mes.push(null);
                    }

                    $('#avance-total-'+i).text(avance_acumulado.format(2));
                    $('#porcentaje-acumulado-'+i).html('<small><span class="fa '+icono+'"></span> '+porcentaje.format(2) + ' %</small>');
                    $('#porcentaje-acumulado-'+i).addClass(clase);
                    if(metas[i]){
                        $('#avance-acumulado-'+i).text((avance_acumulado-metas[i].avanceMes).format(2));
                        $('#avance-mes-'+i).text(metas[i].avanceMes.format(2));
                    }else{
                        $('#avance-acumulado-'+i).text(avance_acumulado.format(2));
                        $('#avance-mes-'+i).text('0.00');
                    }
                }else{
                    mes.push(null);
                    mes.push(null);
                    mes.push(null);
                    mes.push(null);
                    mes.push(null);
                    mes.push(null);
                    $('#avance-acumulado-'+i).text('');
                    $('#avance-mes-'+i).text('');
                    $('#avance-total-'+i).text('');
                    $('#porcentaje-acumulado-'+i).text('');
                }
                avance_acumulado_mes.push(mes);
            };

            $('#meta-total').text(meta_acumulada.format(2));

            if(ultimo_estatus > 1){
                $('#mensaje-alerta').removeClass('hidden');
            }

            var avances_jurisdicciones = [['Jurisdiccion','Porcentaje',{role:'style'},{role:'annotation'},{role:'tooltip',p:{html:true}}]];
            
            if(response.data.metas_mes_jurisdiccion.length == 0){
                avances_jurisdicciones = false;
                $('#grafica_cumplimiento_jurisdiccion').html('<div class="alert alert-info">No se tienen avances resgistrados para ninguna Jurisdicción</div>');
            }

            for(var i in response.data.metas_mes_jurisdiccion){

                var metas_mes = response.data.metas_mes_jurisdiccion[i];

                if(metas_mes.claveJurisdiccion == 'OC'){
                    metas_mes.jurisdiccion = 'OFICINA CENTRAL';
                }

                var meta = parseFloat(metas_mes.meta)||0;
                var avance = parseFloat(metas_mes.avance)||0;

                if(meta > 0){
                    var porcentaje = (avance*100) / meta;
                }else{
                    var porcentaje = (avance*100);
                }

                var estatus = '#4B804C';
                if(!(meta_acumulada == 0 && avance_acumulado == 0)){
                    if(porcentaje > 110){
                        estatus = '#A94442';
                    }else if(porcentaje < 90){
                        estatus = '#A94442';
                    }else if(porcentaje > 0 && meta_acumulada == 0){
                        estatus = '#A94442';
                    }
                }

                avances_jurisdicciones.push(
                    [ 
                        metas_mes.claveJurisdiccion,porcentaje,estatus,(+porcentaje.toFixed(2)) + '%',
                        '<b>'+metas_mes.jurisdiccion + '</b><br>' + ((porcentaje > 110)?'Alto ':(porcentaje < 90)?'Bajo ':'') + 'Avance: <span style="color:'+estatus+';font-weight:bold;">'+(+porcentaje.toFixed(2)) + '%</span>'
                    ]
                );
            }
            
            charts_data['mensual'] = avance_acumulado_mes;
            charts_data['jurisdiccion'] = avances_jurisdicciones;

            if(google_api){
                generateCharts();
            }else{
                $('#grafica_cumplimiento_mensual,#grafica_cumplimiento_jurisdiccion').html('<div class="alert alert-info"><span class="fa fa-spinner fa-spin"></span> Cargando Librerias... Por favor espere... </div>');
            }

            $('#tabs-seguimiento-metas a:first').tab('show');
            $('#modalEditarAvance').modal('show');
        }
    });    
}

function generateCharts(){
    if(charts_data['mensual']){
        var data = new google.visualization.DataTable();
        data.addColumn('string', 'X');
        data.addColumn('number', 'Meta Acumulada');
        data.addColumn({type:'string',role:'tooltip',p:{html:true}});
        data.addColumn('number', 'Avance Acumulado');
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
              0: {curveType:'none',color:'#7CB5EC'},
              1: {curveType:'none',color:'#4B804C'},
              2: {
                    lineWidth:0,
                    color:'#A94442',
                    pointShape: { type: 'triangle' },
                    pointSize:15
                },
              3: {
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
    }
    resizeCharts();
}

/*
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
*/
/*
function enviar_datos_municipio(jurisdiccion){
    var municipio = $('#panel-localidades-'+jurisdiccion+' select.select-lista-municipios[data-jurisdiccion="'+jurisdiccion+'"]').val();
    if(municipio == ''){
        return false;
    }

    var parametros = $('#form_avance').serialize();
    parametros += '&guardar=avance-localidad-metas&clave-municipio='+municipio+'&id-proyecto='+$('#id').val();

    Validation.cleanFormErrors('#form_avance');

    moduloResource.post(parametros,{
        _success: function(response){
            MessageManager.show({data:'Datos del proyecto almacenados con éxito',type:'OK',timer:4});
            var jurisdiccion = response.data.claveJurisdiccion;
            var avance = response.data.avance;
            $('#avance_'+jurisdiccion).val(avance).change();
            accionesDatagrid.actualizar({ _success: function(response){ llenar_grid_acciones(response); } });
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
*/
/*
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
*/
/*
$('.avance-mes').on('keyup',function(){ $(this).change() });
$('.avance-mes').on('change',function(){
    var jurisdiccion = $(this).attr('data-jurisdiccion');
    //Actualiza la columna de avance acumulado
    var row = '#tabla-avances-metas > tbody > tr[data-clave-jurisdiccion="'+jurisdiccion+'"]';

    var avance = parseFloat($(this).val()) || 0;
    //Actualiza la columna de porcentaje acumulado
    var acumulado = parseFloat($(row +' > td.avance-acumulado').attr('data-acumulado')) || 0;
    acumulado += avance;
    $(row +' > td.avance-total').text(acumulado.format(2));
    $(row +' > td.avance-total').attr('data-avance-total',acumulado);

    var total_programado = $(row +' > td.meta-programada').attr('data-meta');

    if(acumulado == 0 && total_programado == 0){
        $(row +' > td.avance-mes').html('<small class="text-success">0%</small>');
    }else{
        if(total_programado > 0){
            var avance_mes = ((acumulado * 100) / total_programado);
        }else if(acumulado > 0){
            if(acumulado > 999){
                var avance_mes = 999;
            }else if(acumulado > 100){
                var avance_mes = acumulado;
            }else if(acumulado > 10){
                var avance_mes = 10 * acumulado;
            }else{
                var avance_mes = 100 * acumulado;
            }
        }else{
            var avance_mes = 0;
        }
        avance_mes = +avance_mes.toFixed(2);
        if(avance_mes > 110){
            $(row +' > td.avance-mes').html('<small class="text-danger"><span class="fa fa-arrow-up"></span> '+avance_mes+'%</small>');
        }else if(avance_mes < 90){
            $(row +' > td.avance-mes').html('<small class="text-danger"><span class="fa fa-arrow-down"></span> '+avance_mes+'%</small>');
        }else if(total_programado == 0 && avance_mes > 0){
            $(row +' > td.avance-mes').html('<small class="text-danger"><span class="fa fa-arrow-up"></span> '+avance_mes+'%</small>');
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
    $('#total-avance-mes-analisis').text(suma.format(2));

    var suma = 0;
    $('.avance-total').each(function(){
        suma += parseFloat($(this).attr('data-avance-total')) || 0;
    });
    suma = +suma.toFixed(2);
    $('#total-avance-total').attr('data-total-avance',suma);
    $('#total-avance-total').text(suma.format(2));
    $('#total-avance-total-analisis').text(suma.format(2));

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
                if(total_acumulado > 999){
                    var total_porcentaje_acumulado = 999;
                }else if(total_acumulado > 100){
                    var total_porcentaje_acumulado = total_acumulado;
                }else if(total_acumulado > 10){
                    var total_porcentaje_acumulado = 10 * total_acumulado;
                }else{
                    var total_porcentaje_acumulado = 100 * total_acumulado;
                }
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
            total_porcentaje_acumulado = '<small class="text-danger"><span class="fa fa-arrow-up"></span> '+total_porcentaje_acumulado+'%</small>';
            $('#total-porcentaje').attr('data-estado-avance','1');
        }else{
            total_porcentaje_acumulado = '<small class="text-success">'+total_porcentaje_acumulado+'%</small>';
            $('#total-porcentaje').attr('data-estado-avance','');
        }
    }

    $('#total-porcentaje').html(total_porcentaje_acumulado);
    $('#total-porcentaje-analisis').html(total_porcentaje_acumulado);

    if($('#total-porcentaje').attr('data-estado-avance')){
        $('#justificacion-acumulada').attr('disabled',false);
        if($('#justificacion-acumulada').val() == 'El avance se encuentra dentro de lo programado'){
            $('#justificacion-acumulada').val('');
        }
        $('#tab-link-plan-mejora').attr('data-toggle','tab');
        $('#tab-link-plan-mejora').parent().removeClass('disabled');
    }else{
        $('#justificacion-acumulada').attr('disabled',true);
        $('#tab-link-plan-mejora').attr('data-toggle','');
        $('#tab-link-plan-mejora').parent().addClass('disabled');
    }
});*/

$('#modalEditarAvance').on('hide.bs.modal',function(e){
    $('#modalEditarAvance .alert').remove();
    $('.valores').empty();
    $('.valores').removeClass('text-danger').removeClass('text-success');
    $('#mensaje-alerta').addClass('hidden');
    /*
    $('#form_avance').get(0).reset();
    $('#form_avance input[type="hidden"]').val('');
    $('#form_avance .texto-comentario').remove();
    $('#form_avance .has-warning').removeClass('has-warning');
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
    $('.accion-municipio').removeClass('btn-link');
    $('.accion-municipio span').removeClass('caret');
    $('input.avance-mes').attr('disabled',false);
    //$('span.nueva-cantidad').text('');
    //$('span.vieja-cantidad').text('0');
    $('#justificacion-acumulada').attr('disabled',true);
    $('#tab-link-plan-mejora').attr('data-toggle','');
    $('#tab-link-plan-mejora').parent().addClass('disabled');
    $('#tabs-seguimiento-metas a:first').tab('show');
    $('.lista-localidades-jurisdiccion').remove();
    Validation.cleanFormErrors('#form_avance');*/
});

/********************************************************************************************************************************
        Fin: Seguimiento de Metas
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
        $(resultado).val(sumatoria.format(2)).change();
    }else{
        $(resultado).text(sumatoria.format(2));
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