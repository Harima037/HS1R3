/*=====================================

    # Nombre:
        institucional.js

    # Módulo:
        seguimiento/seguimiento-inst

    # Descripción:
        Para seguimiento de metas de proyectos institucionales

=====================================*/

// Inicialización General para casi cualquier módulo
var moduloResource = new RESTfulRequests(SERVER_HOST+'/v1/seguimiento');
var moduloDatagrid = new Datagrid("#datagridProyectos",moduloResource,{ formatogrid:true, pagina: 1, clasificacionProyecto: 1});
moduloDatagrid.init();
moduloDatagrid.actualizar({
    _success: function(response){
        moduloDatagrid.limpiar();
        var datos_grid = [];
        var mes_activo = $('#datagridProyectos > table').attr('data-mes-activo');
        for(var i in response.data){
            var item = {};

            /*var clase_label = 'label-default';
            var clase_icon = 'fa-lock';
            if(response.data[i].mes == 2){
                clase_label = 'label-warning';
            }else if(response.data[i].idEstatusProyecto == 3){
                clase_label = 'label-danger';
            }else if(response.data[i].idEstatusProyecto == 4){
                clase_label = 'label-primary';
            }*/

            item.id = response.data[i].id;
            item.clave = response.data[i].clavePresup;
            item.nombre_tecnico = response.data[i].nombreTecnico;

            item.primerMes = '<span class="label label-default"><span class="fa fa-lock"></span></span>';
            item.segundoMes = '<span class="label label-default"><span class="fa fa-lock"></span></span>';
            item.tercerMes = '<span class="label label-default"><span class="fa fa-lock"></span></span>';
            
            item.estatus = '<span class="label label-default"><span class="fa fa-square-o"></span></span>';

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
            $('#modalDatosSeguimiento').find(".modal-title").html("Avance del Proyecto Institucional");

            var html_tbody = '';
            var contador_componente = 0;
            var contador_actividad = 0;
            for(var i in response.data.componentes){
                contador_componente++;
                contador_actividad = 0;
                var componente = response.data.componentes[i];
                html_tbody += '<tr class="bg-primary" data-nivel="1" data-id="'+componente.id+'">';
                html_tbody += '<td>Componente '+contador_componente+'</td>'
                html_tbody += '<td>'+componente.indicador+'</td>'
                html_tbody += '<td data-trim-mes="1">0/0</td>';
                html_tbody += '<td data-trim-mes="2">0/0</td>';
                html_tbody += '<td data-trim-mes="3">0/0</td>';
                html_tbody += '<td data-total-id="'+componente.id+'">0</td>';
                html_tbody += '</tr>';
                for(var j in componente.actividades){
                    contador_actividad++;
                    var actividad = componente.actividades[j];
                    html_tbody += '<tr class="bg-info" data-nivel="2" data-id="'+actividad.id+'">';
                    html_tbody += '<td>Actividad '+contador_componente+'.'+contador_actividad+'</td>'
                    html_tbody += '<td>'+componente.indicador+'</td>'
                    html_tbody += '<td data-trim-mes="1">0/0</td>';
                    html_tbody += '<td data-trim-mes="2">0/0</td>';
                    html_tbody += '<td data-trim-mes="3">0/0</td>';
                    html_tbody += '<td data-total-id="'+actividad.id+'">0</td>';
                    html_tbody += '</tr>';
                }
            }
            $('.tabla-avance-trim > tbody').empty();
            $('.tabla-avance-trim > tbody').append(html_tbody);

            for(var i in response.data.componentes){
                var componente = response.data.componentes[i];
                for(var j in componente.metas_mes_agrupado){
                    var metas_mes = componente.metas_mes_agrupado[j];
                    var trimestre = Math.ceil(parseFloat(metas_mes.mes/3));
                    var ajuste = (trimestre - 1) * 3;
                    var mes_del_trimestre = metas_mes.mes - ajuste;
                    $('#avance-trim-'+trimestre+' > tbody > tr[data-nivel="1"][data-id="'+componente.id+'"] > td[data-trim-mes="'+mes_del_trimestre+'"]').text(metas_mes.meta + ' / ' + (metas_mes.avance || 0));
                }
                for(var k in componente.actividades){
                    var actividad = componente.actividades[k];
                    for(var j in actividad.metas_mes_agrupado){
                        var metas_mes = actividad.metas_mes_agrupado[j];
                        var trimestre = Math.ceil(parseFloat(metas_mes.mes/3));
                        var ajuste = (trimestre - 1) * 3;
                        var mes_del_trimestre = metas_mes.mes - ajuste;
                        $('#avance-trim-'+trimestre+' > tbody > tr[data-nivel="2"][data-id="'+actividad.id+'"] > td[data-trim-mes="'+mes_del_trimestre+'"]').text(metas_mes.meta + ' / ' + (metas_mes.avance || 0));
                    }
                }
            }

            $('#btn-editar-avance').attr('data-id-proyecto',e);

            $('#modalDatosSeguimiento').modal('show');
        }
    });
}

//rend-cuenta-inst-editar
$('#btn-editar-avance').on('click',function(){
    window.location.href = SERVER_HOST+'/rendicion-cuentas/editar-avance/' + $('#btn-editar-avance').attr('data-id-proyecto');
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