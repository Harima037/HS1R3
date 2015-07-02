/*=====================================

    # Nombre:
        lista-proyectos-rendicion.js

    # Módulos:
        seguimiento/seguimiento-inst
        seguimiento/seguimiento-inv

    # Descripción:
        Funciones para seguimiento de metas de proyectos institucionales y de inversión

=====================================*/
moduloDatagrid.init();
moduloDatagrid.actualizar({
    _success: function(response){
        moduloDatagrid.limpiar();
        var datos_grid = [];
        var mes_activo = parseInt($('#datagridProyectos').attr('data-mes-activo'));
        var mes_actual = parseInt($('#datagridProyectos').attr('data-mes-actual'));

        var trimestre = $('#datagridProyectos').attr('data-trim-activo');

        for(var i in response.data){
            var item = {};

            //var mes_activo = $('#datagridProyectos').attr('data-mes-activo'); 
            
            var mes_inicia = ((trimestre - 1) * 3) + 1;
            var meses = [1,2,3,4,5,6,7,8,9,10,11,12];
            var estado_actual = 0;
            var meses_capturados = {'1':'','2':'','3':'','4':'','5':'','6':'','7':'','8':'','9':'','10':'','11':'','12':''};

            for(var j in response.data[i].componentes_metas_mes){
                var meta = response.data[i].componentes_metas_mes[j];
                if(parseFloat(meta.totalMeta) > 0){
                    meses_capturados[meta.mes] = 'style="background-color:#DDDDDD"';
                }
            }

            for(var j in response.data[i].actividades_metas_mes){
                var meta = response.data[i].actividades_metas_mes[j];
                if(parseFloat(meta.totalMeta) > 0){
                    meses_capturados[meta.mes] = 'style="background-color:#DDDDDD"';
                }
            }

            item.id = response.data[i].id;
            item.clave = response.data[i].clavePresup;
            item.nombre_tecnico = response.data[i].nombreTecnico;

            for(var j in meses){
                if(meses[j] == mes_activo){
                    item['mes_'+meses[j]] = '<div id="grid-mes-'+meses[j]+'" class="text-center" '+meses_capturados[meses[j]]+'><span class="fa fa-unlock"></span></div>';
                }else if(meses[j] < mes_actual){
                    item['mes_'+meses[j]] = '<div id="grid-mes-'+meses[j]+'" class="text-center text-muted" '+meses_capturados[meses[j]]+'><span class="fa fa-times"></span></div>';
                }else{
                    item['mes_'+meses[j]] = '<div id="grid-mes-'+meses[j]+'" class="text-center" '+meses_capturados[meses[j]]+'><span class="fa fa-lock"></span></div>';
                }
            }
            var estatus_anteriores = {};
            item.estado = '<span class="text-muted">Inactivo</span>';
            if(response.data[i].evaluacion_meses.length){
                for(var j in response.data[i].evaluacion_meses){
                    var evaluacion_mes = response.data[i].evaluacion_meses[j];
                    if(evaluacion_mes.mes == mes_activo){
                        if(evaluacion_mes.idEstatus == 1){
                            item.estado = '<span class="label label-info">En Trámite</span>';
                        }else if(evaluacion_mes.idEstatus == 2){
                            item.estado = '<span class="label label-warning">En Revisión</span>';
                            estado_actual = 1;
                        }else if(evaluacion_mes.idEstatus == 3){
                            item.estado = '<span class="label label-danger">En Correción</span>';
                        }else if(evaluacion_mes.idEstatus == 4){
                            item.estado = '<span class="label label-primary">Registrado</span>';
                            estado_actual = 1;
                        }else if(evaluacion_mes.idEstatus == 5){
                            item.estado = '<span class="label label-success">Firmado</span>';
                            estado_actual = 1;
                        }
                    }else{
                        estatus_anteriores[evaluacion_mes.mes] = {idEstatus:evaluacion_mes.idEstatus,planMejora:parseInt(evaluacion_mes.planMejora)};
                    }
                }
            }else{
                item.estado = '<span class="text-muted">Inactivo</span>';
            }

            if(estatus_anteriores){
                for(var j in estatus_anteriores){
                    if(estatus_anteriores[j].idEstatus == 6){
                        if(parseInt(estatus_anteriores[j].planMejora) > 0){
                            item['mes_'+j] = '<div id="grid-mes-'+j+'" class="text-center text-danger" '+meses_capturados[j]+'><span class="fa fa-circle-o"></span></div>';
                        }else{
                            item['mes_'+j] = '<div id="grid-mes-'+j+'" class="text-center text-success" '+meses_capturados[j]+'><span class="fa fa-circle-o"></span></div>';
                        }
                    }
                }
            }

            for(var j in response.data[i].registro_avance){
                var avance = response.data[i].registro_avance[j];
                var clase_icono = (avance.mes != mes_activo)?'fa-circle':(estado_actual != 0)?'fa-lock':'fa-unlock';
                if(parseInt(avance.planMejora) > 0){
                    item['mes_'+avance.mes] = '<div id="grid-mes-'+avance.mes+'" class="text-center text-danger" '+meses_capturados[meses[j]]+'><span class="fa '+clase_icono+'"></span></div>';
                }else{
                    item['mes_'+avance.mes] = '<div id="grid-mes-'+avance.mes+'" class="text-center text-success" '+meses_capturados[meses[j]]+'><span class="fa '+clase_icono+'"></span></div>';
                }
            }
            datos_grid.push(item);
        }
        moduloDatagrid.cargarDatos(datos_grid);                         
        moduloDatagrid.cargarTotalResultados(response.resultados,'<b>Proyecto(s)</b>');
        var total = parseInt(response.resultados/moduloDatagrid.rxpag); 
        var plus = parseInt(response.resultados)%moduloDatagrid.rxpag;
        if(plus>0) 
            total++;
        moduloDatagrid.paginacion(total);
    }
});

function cargar_datos_proyecto(e){
    var parametros = {'mostrar':'datos-proyecto-avance'};
    moduloResource.get(e,parametros,{
        _success: function(response){
            $('#modalDatosSeguimiento').find(".modal-title").html('Clave: <small>' + response.data.ClavePresupuestaria + '</small>');

            $('#nombre-tecnico').text(response.data.nombreTecnico);
            $('#programa-presupuestario').text(response.data.datos_programa_presupuestario.clave + ' ' + response.data.datos_programa_presupuestario.descripcion);
            $('#funcion').text(response.data.datos_funcion.clave + ' ' + response.data.datos_funcion.descripcion);
            $('#subfuncion').text(response.data.datos_sub_funcion.clave + ' ' + response.data.datos_sub_funcion.descripcion);
            var html_tbody = '';
            var contador_componente = 0;
            var contador_actividad = 0;
            for(var i in response.data.componentes){
                contador_componente++;
                contador_actividad = 0;
                var componente = response.data.componentes[i];
                html_tbody += '<tr data-nivel="1" data-id="'+componente.id+'">';
                html_tbody += '<td>C '+contador_componente+'</td>'
                html_tbody += '<td>'+componente.indicador+'</td>'
                html_tbody += '<td data-trim-mes="1">-</td>';
                html_tbody += '<td data-trim-mes="2">-</td>';
                html_tbody += '<td data-trim-mes="3">-</td>';
                html_tbody += '<td class="bg-success" data-total-id="'+componente.id+'">0</td>';
                html_tbody += '</tr>';
                for(var j in componente.actividades){
                    contador_actividad++;
                    var actividad = componente.actividades[j];
                    html_tbody += '<tr data-nivel="2" data-id="'+actividad.id+'">';
                    html_tbody += '<td>A '+contador_componente+'.'+contador_actividad+'</td>'
                    html_tbody += '<td>'+actividad.indicador+'</td>'
                    html_tbody += '<td data-trim-mes="1">-</td>';
                    html_tbody += '<td data-trim-mes="2">-</td>';
                    html_tbody += '<td data-trim-mes="3">-</td>';
                    html_tbody += '<td class="bg-success" data-total-id="'+actividad.id+'">0</td>';
                    html_tbody += '</tr>';
                }
            }
            $('.tabla-avance-trim > tbody').empty();
            $('.tabla-avance-trim > tbody').append(html_tbody);

            var total_trimestres = {1:{},2:{},3:{},4:{}};
            for(var i in response.data.componentes){
                var componente = response.data.componentes[i];
                var sumatoria_componente = {1:0,2:0,3:0,4:0};
                //var sumatorias_trimestres = ;
                for(var j in componente.registro_avance){
                    var avance = componente.registro_avance[j];
                    var trimestre = Math.ceil(parseFloat(avance.mes/3));
                    var ajuste = (trimestre - 1) * 3;
                    var mes_del_trimestre = avance.mes - ajuste;
                    if(avance.planMejora){
                        var colo_texto = 'text-danger';
                    }else{
                        var colo_texto = 'text-primary';
                    }
                    var celda = '<span class="'+colo_texto+'">'+parseFloat(avance.avanceMes).format(2)+'</span>';
                    $('#avance-trim-'+trimestre+' > tbody > tr[data-nivel="1"][data-id="'+componente.id+'"] > td[data-trim-mes="'+mes_del_trimestre+'"]').html(celda);
                    sumatoria_componente[trimestre] += parseFloat(avance.avanceMes);
                    total_trimestres[trimestre][avance.mes] = (parseFloat(total_trimestres[trimestre][avance.mes]) || 0) + parseFloat(avance.avanceMes);
                }
                for(var j in sumatoria_componente){
                    if(j > 1){
                        sumatoria_componente[j] = sumatoria_componente[j] + sumatoria_componente[j-1];
                    }
                    $('#avance-trim-'+j+' > tbody > tr[data-nivel="1"][data-id="'+componente.id+'"] > td[data-total-id="'+componente.id+'"]').html(sumatoria_componente[j].format(2));
                }
                for(var k in componente.actividades){
                    var actividad = componente.actividades[k];
                    var sumatoria_actividad = {1:0,2:0,3:0,4:0};
                    for(var j in actividad.registro_avance){
                        var avance = actividad.registro_avance[j];
                        var trimestre = Math.ceil(parseFloat(avance.mes/3));
                        var ajuste = (trimestre - 1) * 3;
                        var mes_del_trimestre = avance.mes - ajuste;
                        if(avance.planMejora){
                            var colo_texto = 'text-danger';
                        }else{
                            var colo_texto = 'text-primary';
                        }
                        var celda = '<span class="'+colo_texto+'">'+parseFloat(avance.avanceMes).format(2)+'</span>';
                        $('#avance-trim-'+trimestre+' > tbody > tr[data-nivel="2"][data-id="'+actividad.id+'"] > td[data-trim-mes="'+mes_del_trimestre+'"]').html(celda);
                        sumatoria_actividad[trimestre] += parseFloat(avance.avanceMes);
                        total_trimestres[trimestre][avance.mes] = (parseFloat(total_trimestres[trimestre][avance.mes]) || 0) + parseFloat(avance.avanceMes);
                    }
                    for(var j in sumatoria_actividad){
                        if(j > 1){
                            sumatoria_actividad[j] = sumatoria_actividad[j] + sumatoria_actividad[j-1];
                        }
                        $('#avance-trim-'+j+' > tbody > tr[data-nivel="2"][data-id="'+actividad.id+'"] > td[data-total-id="'+actividad.id+'"]').html(sumatoria_actividad[j].format(2));
                    }
                }
            }

            /*for(var i in total_trimestres){
                // i = trimestre
                var meses = total_trimestres[i];
                var suma = 0;
                for(var j in meses){
                    $('#total-mes-'+j).text(meses[j]);
                    suma += meses[j];
                }
                $('#total-trim-'+i).text(suma);
            }*/

            if(response.data.beneficiarios.length){
                var beneficiarios = response.data.beneficiarios;
                var lista_beneficiarios = {};
                for(var j in beneficiarios){
                    if(!lista_beneficiarios[beneficiarios[j].idTipoBeneficiario]){
                        lista_beneficiarios[beneficiarios[j].idTipoBeneficiario] = {
                            id: beneficiarios[j].idTipoBeneficiario,
                            tipo: beneficiarios[j].tipo_beneficiario.descripcion,
                            total: 0,
                            desglose: {'f':{},'m':{}}
                        };
                    }
                    lista_beneficiarios[beneficiarios[j].idTipoBeneficiario].total += beneficiarios[j].total;
                    lista_beneficiarios[beneficiarios[j].idTipoBeneficiario].desglose[beneficiarios[j].sexo] = {
                        sexo: beneficiarios[j].sexo,
                        total: beneficiarios[j].total,
                        urbana: beneficiarios[j].urbana,
                        rural: beneficiarios[j].rural,
                        mestiza: beneficiarios[j].mestiza,
                        indigena: beneficiarios[j].indigena,
                        inmigrante: beneficiarios[j].inmigrante,
                        otros: beneficiarios[j].otros,
                        muyAlta: beneficiarios[j].muyAlta,
                        alta: beneficiarios[j].alta,
                        media: beneficiarios[j].media,
                        baja: beneficiarios[j].baja,
                        muyBaja: beneficiarios[j].muyBaja
                    }
                }
                var rows = '';
                for(var j in lista_beneficiarios){
                    rows += '<tr>';
                    rows += '<td rowspan="2">' + lista_beneficiarios[j].tipo + '</td>';
                    rows += '<td rowspan="2">' + lista_beneficiarios[j].total.format() + '</td>';
                    rows += '<td>' + ( lista_beneficiarios[j].desglose['f'].total || 0 ).format() + '</td>';
                    rows += '<th>Femenino</th>';
                    rows += '<td>' + ( lista_beneficiarios[j].desglose['f'].urbana || 0 ).format() + '</td>';
                    rows += '<td>' + ( lista_beneficiarios[j].desglose['f'].rural || 0 ).format() + '</td>';
                    rows += '<td>' + ( lista_beneficiarios[j].desglose['f'].mestiza || 0 ).format() + '</td>';
                    rows += '<td>' + ( lista_beneficiarios[j].desglose['f'].indigena || 0 ).format() + '</td>';
                    rows += '<td>' + ( lista_beneficiarios[j].desglose['f'].inmigrante || 0 ).format() + '</td>';
                    rows += '<td>' + ( lista_beneficiarios[j].desglose['f'].otros || 0 ).format() + '</td>';
                    rows += '<td>' + ( lista_beneficiarios[j].desglose['f'].muyAlta || 0 ).format() + '</td>';
                    rows += '<td>' + ( lista_beneficiarios[j].desglose['f'].alta || 0 ).format() + '</td>';
                    rows += '<td>' + ( lista_beneficiarios[j].desglose['f'].media || 0 ).format() + '</td>';
                    rows += '<td>' + ( lista_beneficiarios[j].desglose['f'].baja || 0 ).format() + '</td>';
                    rows += '<td>' + ( lista_beneficiarios[j].desglose['f'].muyBaja || 0 ).format() + '</td>';
                    rows += '</tr>';
                    rows += '<tr>';
                    rows += '<td>' + ( lista_beneficiarios[j].desglose['m'].total || 0 ).format() + '</td>';
                    rows += '<th>Masculino</th>';
                    rows += '<td>' + ( lista_beneficiarios[j].desglose['m'].urbana || 0 ).format() + '</td>';
                    rows += '<td>' + ( lista_beneficiarios[j].desglose['m'].rural || 0 ).format() + '</td>';
                    rows += '<td>' + ( lista_beneficiarios[j].desglose['m'].mestiza || 0 ).format() + '</td>';
                    rows += '<td>' + ( lista_beneficiarios[j].desglose['m'].indigena || 0 ).format() + '</td>';
                    rows += '<td>' + ( lista_beneficiarios[j].desglose['m'].inmigrante || 0 ).format() + '</td>';
                    rows += '<td>' + ( lista_beneficiarios[j].desglose['m'].otros || 0 ).format() + '</td>';
                    rows += '<td>' + ( lista_beneficiarios[j].desglose['m'].muyAlta || 0 ).format() + '</td>';
                    rows += '<td>' + ( lista_beneficiarios[j].desglose['m'].alta || 0 ).format() + '</td>';
                    rows += '<td>' + ( lista_beneficiarios[j].desglose['m'].media || 0 ).format() + '</td>';
                    rows += '<td>' + ( lista_beneficiarios[j].desglose['m'].baja || 0 ).format() + '</td>';
                    rows += '<td>' + ( lista_beneficiarios[j].desglose['m'].muyBaja || 0 ).format() + '</td>';
                    rows += '</tr>';
                }
                $('.tabla-avance-beneficiarios tbody').empty();
                $('.tabla-avance-beneficiarios tbody').html(rows);
            }

            $('#btn-editar-avance').attr('data-id-proyecto',e);

            if(response.data.evaluacion_meses.length){
                if(response.data.evaluacion_meses[0].idEstatus == 4 || response.data.evaluacion_meses[0].idEstatus == 5){
                    $('#btn-reporte').removeClass('hidden');
                }else{
                    $('#btn-reporte').addClass('hidden');
                }
            }else{
                $('#btn-reporte').addClass('hidden');
            }

            $('#modalDatosSeguimiento').modal('show');
        }
    });
}

//rend-cuenta-inst-editar
$('#btn-editar-avance').on('click',function(){
    window.location.href = SERVER_HOST+'/rendicion-cuentas/editar-avance/' + $('#btn-editar-avance').attr('data-id-proyecto');
});

$('#btn-reporte-general').off('click');
$('#btn-reporte-general').on('click',function(){
    var parametros = $('#btn-editar-avance').attr('data-id-proyecto') + '|seg-metas';
    window.open(SERVER_HOST+'/v1/reporte-evaluacion/'+parametros);
});
$('#btn-reporte-beneficiarios').off('click');
$('#btn-reporte-beneficiarios').on('click',function(){
    var parametros = $('#btn-editar-avance').attr('data-id-proyecto') + '|seg-beneficiarios';
    window.open(SERVER_HOST+'/v1/reporte-evaluacion/'+parametros);
});
$('#btn-reporte-plan-mejora').off('click');
$('#btn-reporte-plan-mejora').on('click',function(){
    var parametros = $('#btn-editar-avance').attr('data-id-proyecto') + '|plan-mejora';
    window.open(SERVER_HOST+'/v1/reporte-evaluacion/'+parametros);
});

$('#btn-reporte-analisis').off('click');
$('#btn-reporte-analisis').on('click',function(){
    var parametros = $('#btn-editar-avance').attr('data-id-proyecto') + '|analisis';
    window.open(SERVER_HOST+'/v1/reporte-evaluacion/'+parametros);
});
/*
$('#btn-reporte').on('click',function(){
    window.open(SERVER_HOST+'/v1/reporte-evaluacion/' + );
});*/

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