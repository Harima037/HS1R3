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
        window.location.href = SERVER_HOST+'/visor-gerencial/rend-cuenta-inst';
    }else if($(this).attr('data-clase-proyecto') == 2){
        window.location.href = SERVER_HOST+'/visor-gerencial/rend-cuenta-inv';
    }
});

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
            item.meta = (parseFloat(componente.metaAnual) || 0).format(2);
            item.metaAcumulada = (parseFloat(componente.metaMes) || 0).format(2);
            item.avanceAcumulado = (parseFloat(componente.avanceMes) || 0).format(2);
            item.avanceMes = 0;

            var fecha = new Date();
            var mes = $('#mes').val();

            datos_grid.push(item);

            for(var j in componente.actividades){
                contador_actividad++;
                var actividad = componente.actividades[j];
                var item = {};
                item.id = '2-' + actividad.id;
                item.nivel = 'A ' + contador_componente + '.' + contador_actividad;
                item.indicador = actividad.indicador;
                item.meta = (parseFloat(actividad.metaAnual) || 0).format(2);
                item.metaAcumulada = (parseFloat(actividad.metaMes) || 0).format(2);
                item.avanceAcumulado = (parseFloat(actividad.avanceMes) || 0).format(2);
                item.avanceMes = 0;
                
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
            $('#meta-total').text((parseFloat(response.data.valorNumerador) || 0).format());
            
            $('#id-accion').val(response.data.id);

            var mes_actual = parseInt($('#mes').val());

            var metas = {};
            for(var i in response.data.metas_mes){
                var programado = response.data.metas_mes[i];

                metas[programado.mes] = {
                    metaMes:parseFloat(programado.meta)||0,
                    avanceMes:parseFloat(programado.avance)||0
                }
            }

            var meta_acumulada = 0;
            var avance_acumulado = 0;
            var ultimo_estatus = 0;
            for (var i = 1; i <= 12; i++) {
                if(metas[i]){
                    meta_acumulada += metas[i].metaMes;
                    avance_acumulado += metas[i].avanceMes;
                }

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

                    var clase = 'text-success';
                    var icono = '';
                    ultimo_estatus = 1;
                    if(!(meta_acumulada == 0 && avance_acumulado == 0)){
                        if(porcentaje > 110){
                            clase = 'text-danger';
                            ultimo_estatus = 2;
                            icono = 'fa-arrow-up';
                        }else if(porcentaje < 90){
                            clase = 'text-danger';
                            ultimo_estatus = 2;
                            icono = 'fa-arrow-down';
                        }else if(porcentaje > 0 && meta_acumulada == 0){
                            clase = 'text-danger';
                            ultimo_estatus = 2;
                            icono = 'fa-arrow-up';
                        }
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
                    $('#avance-acumulado-'+i).text('');
                    $('#avance-mes-'+i).text('');
                    $('#avance-total-'+i).text('');
                    $('#porcentaje-acumulado-'+i).text('');
                }
            };

            if(ultimo_estatus == 2){
                $('#mensaje-alerta').removeClass('hidden');
            }

            $('#grafica-cumplimiento-mensual').highcharts({
                title: {
                    text: 'Cumplimiento Mensual',
                    x: -20 //center
                },
                subtitle: {
                    text: 'Indicador',
                    x: -20
                },
                xAxis: {
                    categories: ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun',
                        'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic']
                },
                yAxis: {
                    title: {
                        text: 'Porcentaje ( % )'
                    },
                    max:100,
                    min:0,
                    plotLines: [{
                        value: 0,
                        width: 1,
                        color: '#808080'
                    }]
                },
                tooltip: {
                    valueSuffix: '%'
                },
                legend: {
                    layout: 'vertical',
                    align: 'right',
                    verticalAlign: 'middle',
                    borderWidth: 0
                },
                series: [
                    {
                        name: 'Meta Acumulada',
                        data: [7.0, 6.9, 9.5, 14.5, 18.2, 21.5, 25.2, 26.5, 23.3, 18.3, 13.9, 9.6]
                    }, 
                    {
                        name: 'Total Avance',
                        data: [0, 0.8, 5.7, 11.3, {y:17.0,marker:{fillColor: '#FF0000',lineWidth: 2,lineColor:'#FF0000'}}, 22.0, 24.8, 24.1, null, null, null, null]
                    }
                ]
            });

            $('#modalEditarAvance').modal('show');
        }
    });    
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
    return this.toFixed(Math.max(0, ~~n)).replace(new RegExp(re, 'g'), '$&,');
};

/********************************************************************************************************************************
        Fin: Funciones extras de utileria
*********************************************************************************************************************************/