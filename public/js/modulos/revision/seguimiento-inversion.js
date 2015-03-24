/*=====================================

    # Nombre:
        seguimiento-institucional.js

    # Módulo:
        revision/segui-proyectos-inst

    # Descripción:
        Para revisión del seguimiento de metas de proyectos institucionales

=====================================*/

// Inicialización General para casi cualquier módulo
var moduloResource = new RESTfulRequests(SERVER_HOST+'/v1/segui-proyectos-inv');
var moduloDatagrid = new Datagrid("#datagridProyectos",moduloResource,{ formatogrid:true, pagina: 1, clasificacionProyecto: 2});
moduloDatagrid.init();
moduloDatagrid.actualizar({
    _success: function(response){
        moduloDatagrid.limpiar();
        var datos_grid = [];
        var mes_activo = $('#datagridProyectos > table').attr('data-mes-activo');
        for(var i in response.data){
            var item = {};

            var mes_activo = $('#datagridProyectos').attr('data-mes-activo'); 
            var trimestre = $('#datagridProyectos').attr('data-trim-activo');
            var mes_inicia = ((trimestre - 1) * 3) + 1;
            var meses = [1,2,3,4,5,6,7,8,9,10,11,12];


            item.id = response.data[i].id;
            item.clave = response.data[i].clavePresup;
            item.nombre_tecnico = response.data[i].nombreTecnico;

            for(var j in meses){
                if(meses[j] == mes_activo){
                    item['mes_'+meses[j]] = '<span id="grid-mes-'+meses[j]+'" class=""><span class="fa fa-unlock"></span></span>';
                }else if(meses[j] < mes_activo){
                    item['mes_'+meses[j]] = '<span id="grid-mes-'+meses[j]+'" class="text-muted"><span class="fa fa-times"></span></span>';
                }else{
                    item['mes_'+meses[j]] = '<span id="grid-mes-'+meses[j]+'" class=""><span class="fa fa-lock"></span></span>';
                }
            }

            for(var j in response.data[i].registro_avance){
                var avance = response.data[i].registro_avance[j];
                if(parseInt(avance.planMejora) > 0){
                    var clase_icono = (avance.mes == mes_activo)?'fa-unlock':'fa-circle';
                    item['mes_'+meses[j]] = '<span id="grid-mes-'+meses[j]+'" class="text-danger"><span class="fa '+clase_icono+'"></span></span>';
                }else{
                    var clase_icono = (avance.mes == mes_activo)?'fa-unlock':'fa-circle';
                    item['mes_'+meses[j]] = '<span id="grid-mes-'+meses[j]+'" class="text-success"><span class="fa '+clase_icono+'"></span></span>';
                }
            }
            datos_grid.push(item);
        }
        moduloDatagrid.cargarDatos(datos_grid);                         
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
                html_tbody += '<td data-total-id="'+componente.id+'">0</td>';
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
                    html_tbody += '<td data-total-id="'+actividad.id+'">0</td>';
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
                    var celda = '<span class="'+colo_texto+'">'+avance.avanceMes+'</span>';
                    $('#avance-trim-'+trimestre+' > tbody > tr[data-nivel="1"][data-id="'+componente.id+'"] > td[data-trim-mes="'+mes_del_trimestre+'"]').html(celda);
                    sumatoria_componente[trimestre] += avance.avanceMes;
                    total_trimestres[trimestre][avance.mes] = (parseFloat(total_trimestres[trimestre][avance.mes]) || 0) + avance.avanceMes;
                }
                for(var j in sumatoria_componente){
                    $('#avance-trim-'+j+' > tbody > tr[data-nivel="1"][data-id="'+componente.id+'"] > td[data-total-id="'+componente.id+'"]').html(sumatoria_componente[j]);
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
                        var celda = '<span class="'+colo_texto+'">'+avance.avanceMes+'</span>';
                        $('#avance-trim-'+trimestre+' > tbody > tr[data-nivel="2"][data-id="'+actividad.id+'"] > td[data-trim-mes="'+mes_del_trimestre+'"]').html(celda);
                        sumatoria_actividad[trimestre] += avance.avanceMes;
                        total_trimestres[trimestre][avance.mes] = (parseFloat(total_trimestres[trimestre][avance.mes]) || 0) + avance.avanceMes;
                    }
                    for(var j in sumatoria_actividad){
                        $('#avance-trim-'+j+' > tbody > tr[data-nivel="2"][data-id="'+actividad.id+'"] > td[data-total-id="'+actividad.id+'"]').html(sumatoria_actividad[j]);
                    }
                }
            }

            for(var i in total_trimestres){
                // i = trimestre
                var meses = total_trimestres[i];
                var suma = 0;
                for(var j in meses){
                    $('#total-mes-'+j).text(meses[j]);
                    suma += meses[j];
                }
                $('#total-trim-'+i).text(suma);
            }

            $('#btn-comentar-avance').attr('data-id-proyecto',e);

            $('#modalDatosSeguimiento').modal('show');
        }
    });
}

//rend-cuenta-inst-editar
$('#btn-comentar-avance').on('click',function(){
    window.location.href = SERVER_HOST+'/revision/comentar-avance/' + $('#btn-comentar-avance').attr('data-id-proyecto');
});

/*
$('#modalDatosSeguimiento').on('shown.bs.modal', function () {
    $('#modalDatosSeguimiento').find('input').eq(0).focus();
});
*/
/*
$('#modalDatosSeguimiento').on('hide.bs.modal',function(e){ 
    resetModalModuloForm();
});
*/