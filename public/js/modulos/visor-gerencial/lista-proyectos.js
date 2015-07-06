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
                    }else{
                        estatus_anteriores[evaluacion_mes.mes] = {idEstatus:evaluacion_mes.idEstatus,planMejora:parseInt(evaluacion_mes.planMejora)};
                    }
                }
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