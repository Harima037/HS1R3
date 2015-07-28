/*=====================================

    # Nombre:
        vista-avance-indicadores.js

    # Módulo:
        visor/avance-indicadores

    # Descripción:
        Para ver los avances de indicadores de un proyecto

=====================================*/

// Inicialización General para casi cualquier módulo
var moduloResource = new RESTfulRequests(SERVER_HOST+'/v1/visor');

$('#btn-proyecto-cancelar').on('click',function(){
    if($(this).attr('data-clase-proyecto') == 1){
        window.location.href = SERVER_HOST+'/visor/proyectos-inst';
    }else if($(this).attr('data-clase-proyecto') == 2){
        window.location.href = SERVER_HOST+'/visor/proyectos-inv';
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
var accionesDatagrid = new Datagrid("#datagridAcciones",moduloResource,{ formatogrid:true, pagina: 1, idProyecto: $('#id').val(), avancesIndicadores:true});
accionesDatagrid.init();
accionesDatagrid.actualizar({ 
    _success: function(response){ 
        accionesDatagrid.limpiar();
        var datos_grid = [];
        for(var i in response.data){
            var elemento = response.data[i];

            var item = {};
            item.id = elemento.tipo + '-' + elemento.id;
            item.nivel = elemento.nivel;
            item.indicador = elemento.indicador;
            item.meta = elemento.metaAnual.format(2);
            item.meta_acumulada = elemento.metaMes.format(2);
            item.avances_acumulados = elemento.avanceAcumulado.format(2);
            item.avances_mes = elemento.avanceMes.format(2);

            datos_grid.push(item);
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
    var parametros = {'mostrar':'detalles-avance-indicador','nivel':nivel};
    var id = datos_id[1];
    moduloResource.get(id,parametros,{
        _success: function(response){
            var meses = ['Enero','Febrero','Marzo','Abril','Mayo','Junio','Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre'];
            $('#modalEditarAvance').find(".modal-title").html("Estado del Avance de Metas al mes de "+meses[$('#mes').val()-1]);

            $('#indicador').text(response.data.indicador);
            $('#unidad-medida').text(response.data.unidadMedida);
            $('#meta-total').text(response.data.metaTotal.format(2));
            $('#analisis-resultados').text(response.data.analisisResultados);
            $('#justificacion-acumulada').text(response.data.justificacion);
            
            if(response.data.planMejora){
                $('#tab-link-plan-mejora').removeClass('hidden');
                var plan_mejora = response.data.planMejora;
                $('#accion-mejora').text(plan_mejora.accionMejora);
                $('#grupo-trabajo').text(plan_mejora.grupoTrabajo);
                $('#documentacion-comprobatoria').text(plan_mejora.documentacionComprobatoria);
                $('#fecha-inicio').text(plan_mejora.fechaInicio);
                $('#fecha-termino').text(plan_mejora.fechaTermino);
                $('#fecha-notificacion').text(plan_mejora.fechaNotificacion);
            }else{
                $('#tab-link-plan-mejora').addClass('hidden');
            }

            var total_meta_mes = 0;
            var total_meta_acumulada = 0;
            var total_avance_acumulado = 0;
            var total_avance_mes = 0;
            var total_avance_total = 0;
            
            if(response.data.jurisdicciones.length == 0){
                var datos_jurisdicciones = false;
                $('#grafica_cumplimiento_jurisdiccion').html('<div class="alert alert-info">No se tienen avances resgistrados para ninguna Jurisdicción</div>');
            }else{
                var datos_jurisdicciones = [['Jurisdiccion','Porcentaje',{role:'style'},{role:'annotation'},{role:'tooltip',p:{html:true}}]];
            }

            for(var clave in response.data.jurisdicciones){
                if(clave == 'OC'){
                    response.data.jurisdicciones[clave].nombre = 'OFICINA CENTRAL';
                }
                var jurisdiccion = response.data.jurisdicciones[clave];

                $('#meta-mes-'+clave).text(jurisdiccion.metaMes.format(2));
                $('#meta-acumulada-'+clave).text(jurisdiccion.metaAcumulada.format(2));
                $('#avance-acumulado-'+clave).text(jurisdiccion.avanceAcumulado.format(2));
                $('#avance-mes-'+clave).text(jurisdiccion.avanceMes.format(2));
                $('#avance-total-'+clave).text(jurisdiccion.avanceTotal.format(2));

                total_meta_mes += jurisdiccion.metaMes;
                total_meta_acumulada += jurisdiccion.metaAcumulada;
                total_avance_acumulado += jurisdiccion.avanceAcumulado;
                total_avance_mes += jurisdiccion.avanceMes;
                total_avance_total += jurisdiccion.avanceTotal;

                var clase = 'text-danger';
                var icono = '';
                var estatus = '#A94442';
                if(jurisdiccion.estatus == 2){
                    icono = '<span class="fa fa-arrow-down"></span> ';
                }else if(jurisdiccion.estatus == 3){
                    icono = '<span class="fa fa-arrow-up"></span> ';
                }else{
                    clase = 'text-success';
                    estatus = '#4B804C';
                }

                $('#porcentaje-mes-'+clave).html('<span class="'+clase+'">'+icono+jurisdiccion.porcentaje.format(2)+' %</span>');

                if(clave != 'OC'){
                    indice = parseInt(clave);
                }else{
                    indice = response.data.jurisdicciones.length + 1;
                }
                
                datos_jurisdicciones[indice] = [
                    clave,jurisdiccion.porcentaje,estatus,(jurisdiccion.porcentaje.format(2)) + '%',
                    '<table border="0" cellpadding="0" cellspacing="0"><tr><th class="text-center" style="white-space:nowrap;" colspan="2"><big>'+jurisdiccion.nombre+'</big></th></tr><tr><td style="white-space:nowrap;">Avance: </td><th class="text-center" style="color:'+estatus+';font-weight:bold;">'+(jurisdiccion.porcentaje.format(2))+'%</th></tr></table>'
                ];
            }
            charts_data['jurisdiccion'] = datos_jurisdicciones;

            $('#total-meta-mes').text(total_meta_mes.format(2));
            $('#total-meta-acumulada').text(total_meta_acumulada.format(2));
            $('#total-avance-acumulado').text(total_avance_acumulado.format(2));
            $('#total-avance-mes').text(total_avance_mes.format(2));
            $('#total-avance-total').text(total_avance_total.format(2));

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

            var avance_acumulado_mes = [];
            var meta_acumulada = 0;
            var avance_acumulado = 0;
            var mes_actual = $('#mes').val();
            var meta_total = response.data.metaTotal;

            for (var i = 1; i <= 12; i++){
                
                var datos_mes = null;
                if(response.data.meses[i]){
                    var datos_mes = response.data.meses[i];
                }

                var mes = [];
                mes.push(meses[i-1].substring(0,3));

                var meta_actual = 0;
                var avance_actual = 0;
                var estatus = 1;
                var porcentaje = 0;
                if(datos_mes){
                    meta_acumulada = datos_mes.metaAcumulada;
                    meta_actual = datos_mes.meta;
                    estatus = datos_mes.estatus;
                    porcentaje = datos_mes.porcentaje;
                    if(datos_mes.activo){
                        avance_actual = datos_mes.avance;
                        avance_acumulado = datos_mes.avanceAcumulado;
                    }
                }

                var porcentaje_meta = (meta_acumulada*100)/meta_total;

                mes.push(porcentaje_meta);
                mes.push('<table border="0" cellpadding="0" cellspacing="0"><tr><th class="text-center" style="white-space:nowrap;" colspan="2"><big>'+meses[i-1]+' '+porcentaje_meta.format(2)+'%</big></th></tr><tr><td style="white-space:nowrap;">Meta Acumulada: </td><th class="text-center text-info">'+meta_acumulada.format(2)+'</th></tr></table>');

                if(meta_actual > 0){
                    mes.push(porcentaje_meta);
                    mes.push('<table border="0" cellpadding="0" cellspacing="0"><tr><th class="text-center" style="white-space:nowrap;" colspan="2"><big>'+meses[i-1]+' '+porcentaje_meta.format(2)+'%</big></th></tr><tr><td style="white-space:nowrap;">Meta del Mes: </td><th class="text-center text-primary">'+meta_actual.format(2)+'</th></tr><tr><td style="white-space:nowrap;">Meta Acumulada: </td><th class="text-center text-info">'+meta_acumulada.format(2)+'</th></tr></table>');
                }else{
                    mes.push(null);mes.push(null);
                }

                if(i <= mes_actual){

                    var porcentaje_del_mes = (avance_acumulado*100)/meta_total;

                    mes.push(porcentaje_del_mes);
                    mes.push('<table border="0" cellpadding="0" cellspacing="0"><tr><th class="text-center" colspan="2"><big>'+meses[i-1]+' '+porcentaje_del_mes.format(2)+'%</big></th></tr><tr><td style="white-space:nowrap;">Meta Acumulada: </td><th class="text-center text-info"> '+meta_acumulada.format(2)+'</th></tr><tr><td style="white-space:nowrap;">Avance Acumulado: </td><th class="text-center text-primary"> '+avance_acumulado.format(2)+'</th></tr><tr><td style="white-space:nowrap;">Porcentaje del Mes: </td><th class="text-center text-success"> '+(porcentaje.format(2))+'%</th></tr></table>');

                    var clase = 'text-danger';
                    var icono = '';
                    if(estatus == 2){
                        icono = 'fa-arrow-down';
                    }else if(estatus == 3){
                        icono = 'fa-arrow-up';
                    }else{
                        clase = 'text-success';
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