/*=====================================

    # Nombre:
        evaluacion-proyectos.js

    # Módulo:
        reportes/evaluacion-proyectos

    # Descripción:
        Para imprimir el libro de evaluacion de proyectos

=====================================*/

// Inicialización General para casi cualquier módulo
var moduleResource = new RESTfulRequests(SERVER_HOST+'/v1/reporte-proyectos-evaluacion');
var moduleDatagrid = new Datagrid("#datagridProyectos",moduleResource,{formatogrid:true,pagina:1,ejercicio:$('#ejercicio').val(),mes:$('#mes').val()});
moduleDatagrid.init();
moduleDatagrid.actualizar({
    _success: function(response){
        moduleDatagrid.limpiar();
        var datos_grid = [];
        for(var i in response.data){
            var item = {};

            item.id = response.data[i].id;
            item.clave = response.data[i].ClavePresupuestaria;
            item.nombre_tecnico = response.data[i].nombreTecnico;
            
            if(response.data[i].evaluacion_mes){
                if(response.data[i].evaluacion_mes.idEstatus == 4){
                    item.seguimientoMetas = '<span class="label label-primary">Registrado</span>';
                }else{
                    item.seguimientoMetas = '<span class="label label-success">Firmado</span>';
                }
            }else{
                item.seguimientoMetas = '<span class="text-muted">Inactivo</span>';
            }

            if(response.data[i].proyecto_evaluacion){
                item.evaluacion = '<span class="label label-info">Registrado</span>';
            }else{
                item.evaluacion = '<span class="text-muted">No Registrado</span>';
            }
            
            datos_grid.push(item);
        }
        moduleDatagrid.cargarDatos(datos_grid);                         
        moduleDatagrid.cargarTotalResultados(response.resultados,'<b>Proyecto(s)</b>');
        var total = parseInt(response.resultados/moduleDatagrid.rxpag); 
        var plus = parseInt(response.resultados)%moduleDatagrid.rxpag;
        if(plus>0) 
            total++;
        moduleDatagrid.paginacion(total);
    }
});
/*===================================*/
// Implementación personalizada del módulo

function editar (e){
    var parametros = {'mes':$('#mes').val(),'ejercicio':$('#ejercicio').val()};
    moduleResource.get(e,parametros,{
        _success: function(response){
            $('#tabla-lista-indicadores tbody').html('');
            var html_rows = '';

            $('#id').val('');
            $('#justificaciones').val('');
            $('#observaciones').val('');
            $('#id-proyecto').val(response.data.id);

            var suma_porcentajes = 0;
            var conteo_porcentajes = 0;
            var justificaciones = '';
            for(var j in response.data.componentes){
                var componente = response.data.componentes[j];
                var metas = 0;
                var avances = 0;
                var porcentaje = 0;
                for (var k in componente.metas_mes) {
                    metas += parseFloat(componente.metas_mes[k].meta) || 0;
                    avances += parseFloat(componente.metas_mes[k].avance) || 0;
                }
                if(metas > 0){
                    porcentaje += (avances*100)/metas;
                }else{
                    porcentaje += (avances*100);
                }
                suma_porcentajes += porcentaje;
                conteo_porcentajes += 1;

                if(componente.registro_avance[0]){
                    justificaciones += 'Componente ( '+componente.indicador+' ): '+componente.registro_avance[0].justificacionAcumulada + '\n\n';
                }

                html_rows += '<tr>';
                html_rows += '<th>Componente</th><td>'+componente.indicador+'</td>';
                html_rows += '<td>'+(metas.format(2))+'</td><td>'+(avances.format(2))+'</td>';
                html_rows += '<td>'+(porcentaje.format(2))+' %</td>';
                html_rows += '</tr>';

                for(var l in componente.actividades){
                    var actividad = componente.actividades[l];
                    var metas = 0;
                    var avances = 0;
                    var porcentaje = 0;
                    for (var k in actividad.metas_mes) {
                        metas += parseFloat(actividad.metas_mes[k].meta) || 0;
                        avances += parseFloat(actividad.metas_mes[k].avance) || 0;
                    }
                    if(metas > 0){
                        porcentaje += (avances*100)/metas;
                    }else{
                        porcentaje += (avances*100);
                    }
                    suma_porcentajes += porcentaje;
                    conteo_porcentajes += 1;

                    if(actividad.registro_avance[0]){
                        justificaciones += 'Actividad ( '+actividad.indicador+' ): '+actividad.registro_avance[0].justificacionAcumulada + '\n\n';
                    }

                    html_rows += '<tr>';
                    html_rows += '<th>Actividad</th><td>'+actividad.indicador+'</td>';
                    html_rows += '<td>'+(metas.format(2))+'</td><td>'+(avances.format(2))+'</td>';
                    html_rows += '<td>'+(porcentaje.format(2))+' %</td>';
                    html_rows += '</tr>';
                }
            }
            
            if(conteo_porcentajes > 0){
                //var promedio_porcentaje = suma_porcentajes/conteo_porcentajes;
                $('#tabla-lista-indicadores tbody').html(html_rows);
                //$('#tabla-lista-indicadores tfoot').html('<tr><th style="text-align:right;" colspan="4">Promedio de Avance Físico Alcanzado:</th><th>'+promedio_porcentaje.format(2)+' %</th></tr>')
            }
            
            $('#justificaciones').val('');
            $('#observaciones').val('');
            if(response.data.proyecto_evaluacion){
                $('#accion-observaciones').text('Editar');
                $('#id').val(response.data.proyecto_evaluacion.id);
                $('#justificaciones').val(response.data.proyecto_evaluacion.justificaciones);
                $('#observaciones').val(response.data.proyecto_evaluacion.observaciones);
            }else if(justificaciones){
                $('#justificaciones').val(justificaciones);
            }
            /*else if(response.data.analisis_funcional.length > 0){
                $('#accion-observaciones').text('Capturar');
                $('#observaciones').val(response.data.analisis_funcional[0].justificacionGlobal);
            }*/
            var modificado = parseFloat(response.data.presupuestoModificado) || 0;
            var ejercido = parseFloat(response.data.presupuestoEjercidoModificado) || 0;
            
            $('#presupuesto-autorizado').text('$ ' + modificado.format(2));
            $('#presupuesto-ejercido').text('$ ' + ejercido.format(2));
            
            $('#modalObservaciones').modal('show');
        }
    });
}

$('#btnGuardarObservaciones').on('click',function(){
    var parametros = $('#formEvaluacion').serialize()+'&mes='+$('#mes').val();
    if($('#id').val()){
        moduleResource.put($('#id').val(),parametros,{
            _success: function(response){                           
                MessageManager.show({data:'Las observaciones han sido guardadas con éxito',type:'OK',timer:3});
                $('#modalObservaciones').modal('hide');
                moduleDatagrid.actualizar();
            },
            _error: function(response){
                try{
                    MessageManager.show({code:'S03',data:"Hubo un problema al realizar la transacción, inténtelo de nuevo o contacte con soporte técnico."});
                }catch(e){
                    console.log(e);
                }
            }
        });
    }else{
        moduleResource.post(parametros,{
            _success: function(response){                           
                MessageManager.show({data:'Las observaciones han sido guardadas con éxito',type:'OK',timer:3});
                $('#modalObservaciones').modal('hide');
                moduleDatagrid.actualizar();
            },
            _error: function(response){
                try{
                    MessageManager.show({code:'S03',data:"Hubo un problema al realizar la transacción, inténtelo de nuevo o contacte con soporte técnico."});
                }catch(e){
                    console.log(e);
                }
            }
        });
    }
});

$("#datagridProyectos .txt-quick-search").off('keydown');
$("#datagridProyectos .txt-quick-search").on('keydown', function(event){
    if (event.which == 13) {
        realizar_busqueda();
    }
});

$('#datagridProyectos .btn-quick-search').off('click');
$('#datagridProyectos .btn-quick-search').on('click',function(){
    realizar_busqueda();
})

function realizar_busqueda(){
    moduleDatagrid.setPagina(1);
    moduleDatagrid.parametros.buscar = $('.txt-quick-search').val();
    moduleDatagrid.parametros.ejercicio = $('#ejercicio').val();
    moduleDatagrid.parametros.mes = $('#mes').val();
    moduleDatagrid.actualizar();
}

/*===================================*/
// Configuración General para cualquier módulo
$('#btn-ver-reporte').on('click',function(){
    var parametros = '?mes='+$('#mes').val()+'&ejercicio='+$('#ejercicio').val();
    if($('.txt-quick-search').val()){
        parametros += '&buscar='+$('.txt-quick-search').val();
    }
    window.open(SERVER_HOST+'/v1/evaluacion-proyectos-reporte'+parametros);
});
/*===================================*/
// Funciones adicionales por módulo

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
