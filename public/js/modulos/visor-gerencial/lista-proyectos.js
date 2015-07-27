/*=====================================

    # Nombre:
        lista-proyectos-rendicion.js

    # Módulos:
        visor-gerencial/proyectos-inst
        visor-gerencial/proyectos-inv

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
            
            var mes_inicia = ((trimestre - 1) * 3) + 1;
            var meses = [1,2,3,4,5,6,7,8,9,10,11,12];
            var estado_actual = 0;
            var meses_capturados = {'1':'','2':'','3':'','4':'','5':'','6':'','7':'','8':'','9':'','10':'','11':'','12':''};
            var estatus_meses = {};
            var meta_acumulado = {'componentes':{},'actividades':{}};
            var avance_acumulado = {'componentes':{},'actividades':{}};

            for(var j in response.data[i].componentes_metas_mes){
                var meta = response.data[i].componentes_metas_mes[j];
                var meta_mes = parseFloat(meta.meta) || 0;
                var avance_mes = parseFloat(meta.avance) || 0;
                if(!meta_acumulado['componentes'][meta.idComponente]){
                    meta_acumulado['componentes'][meta.idComponente] = 0;
                }
                if(!avance_acumulado['componentes'][meta.idComponente]){
                    avance_acumulado['componentes'][meta.idComponente] = 0;
                }
                meta_acumulado['componentes'][meta.idComponente] += meta_mes;
                avance_acumulado['componentes'][meta.idComponente] += avance_mes;
                var registro_mes = false;
                var porcentaje = 0;
                if( meta_mes > 0){
                    registro_mes = true;
                    meses_capturados[meta.mes] = 'style="background-color:#DDDDDD"';
                    porcentaje = parseFloat(((avance_acumulado['componentes'][meta.idComponente] * 100) / meta_acumulado['componentes'][meta.idComponente]).toFixed(2)) || 0;
                }else if(avance_mes > 0){
                    registro_mes = true;
                    if(meta_acumulado['componentes'][meta.idComponente] > 0){
                        porcentaje = parseFloat(((avance_acumulado['componentes'][meta.idComponente] * 100) / meta_acumulado['componentes'][meta.idComponente]).toFixed(2)) || 0;
                    }else{
                        porcentaje = 200;
                    }
                }
                if(registro_mes){
                    if(!estatus_meses[meta.mes]){
                        estatus_meses[meta.mes] = 1;
                    }
                    if(porcentaje > 110 || porcentaje < 90 ){
                        estatus_meses[meta.mes] = 2;
                    }
                }
            }

            for(var j in response.data[i].actividades_metas_mes){
                var meta = response.data[i].actividades_metas_mes[j];
                var meta_mes = parseFloat(meta.meta) || 0;
                var avance_mes = parseFloat(meta.avance) || 0;
                if(!meta_acumulado['actividades'][meta.idActividad]){
                    meta_acumulado['actividades'][meta.idActividad] = 0;
                }
                if(!avance_acumulado['actividades'][meta.idActividad]){
                    avance_acumulado['actividades'][meta.idActividad] = 0;
                }
                meta_acumulado['actividades'][meta.idActividad] += meta_mes;
                avance_acumulado['actividades'][meta.idActividad] += avance_mes;
                var registro_mes = false;
                var porcentaje = 0;
                if( meta_mes > 0){
                    registro_mes = true;
                    meses_capturados[meta.mes] = 'style="background-color:#DDDDDD"';
                    porcentaje = parseFloat(((avance_acumulado['actividades'][meta.idActividad] * 100) / meta_acumulado['actividades'][meta.idActividad]).toFixed(2)) || 0;
                }else if(avance_mes > 0){
                    registro_mes = true;
                    if(meta_acumulado['actividades'][meta.idActividad] > 0){
                        porcentaje = parseFloat(((avance_acumulado['actividades'][meta.idActividad] * 100) / meta_acumulado['actividades'][meta.idActividad]).toFixed(2)) || 0;
                    }else{
                        porcentaje = 200;
                    }
                }
                if(registro_mes){
                    if(!estatus_meses[meta.mes]){
                        estatus_meses[meta.mes] = 1;
                    }
                    if(porcentaje > 110 || porcentaje < 90 ){
                        estatus_meses[meta.mes] = 2;
                    }
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
            if(response.data[i].evaluacion_meses.length){
                for(var j in response.data[i].evaluacion_meses){
                    var evaluacion_mes = response.data[i].evaluacion_meses[j];
                    if(evaluacion_mes.mes == mes_activo){
                        if(evaluacion_mes.idEstatus == 2){
                            estado_actual = 1;
                        }else if(evaluacion_mes.idEstatus == 4){
                            estado_actual = 1;
                        }else if(evaluacion_mes.idEstatus == 5){
                            estado_actual = 1;
                        }
                    }
                    var color_circle = 'text-muted';
                    if(estatus_meses[evaluacion_mes.mes]){
                        if(estatus_meses[evaluacion_mes.mes] == 2){
                            color_circle = 'text-danger';
                        }else{
                            color_circle = 'text-success';
                        }
                    }
                    var clase_icono = (evaluacion_mes.mes != mes_activo)?'fa-circle':(estado_actual != 0)?'fa-lock':'fa-unlock';
                    item['mes_'+evaluacion_mes.mes] = '<div id="grid-mes-'+evaluacion_mes.mes+'" class="text-center '+color_circle+'" '+meses_capturados[evaluacion_mes.mes]+'><span class="fa '+clase_icono+'"></span></div>';
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
    window.location.href = SERVER_HOST+'/visor-gerencial/ver-avance/' + e;
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