/*=====================================

    # Nombre:
        lista-programas-rendicion.js

    # Módulos:
        seguimiento/seguimiento-prog

    # Descripción:
        Funciones para seguimiento de metas de Programas Presupuestales

=====================================*/

var moduloResource = new RESTfulRequests(SERVER_HOST+'/v1/rend-cuenta-prog');
var moduloDatagrid = new Datagrid("#datagridProgramas",moduloResource,{ formatogrid:true, pagina: 1, clasificacionProyecto: 1});

moduloDatagrid.init();
moduloDatagrid.actualizar({
    _success: function(response){
        moduloDatagrid.limpiar();
        var datos_grid = [];
        var trimestre = $('#datagridProgramas').attr('data-trim-activo');
        var label_lock = '<span class=""><span class="fa fa-lock"></span></span>';
        var label_miss = '<span class="text-muted"><span class="fa fa-times"></span></span>';
        for(var i in response.data){
            var item = {
                'id':       response.data[i].id,
                'programa': response.data[i].clave + ' ' + response.data[i].programa,
                'trim_1':   (trimestre > 1)?label_miss:label_lock,
                'trim_2':   (trimestre > 2)?label_miss:label_lock,
                'trim_3':   (trimestre > 3)?label_miss:label_lock,
                'trim_4':   (trimestre > 4)?label_miss:label_lock,
                'estado':   '<span class="text-muted">Inactivo</span>'
            };

            if(response.data[i].evaluacion_trimestre.length){
                if(response.data[i].evaluacion_trimestre[0].idEstatus == 1){
                    item['estado'] = '<span class="label label-info">En Trámite</span>';
                }else if(response.data[i].evaluacion_trimestre[0].idEstatus == 2){
                    item['estado'] = '<span class="label label-warning">En Revisión</span>';
                }else if(response.data[i].evaluacion_trimestre[0].idEstatus == 3){
                    item['estado'] = '<span class="label label-danger">En Correción</span>';
                }else if(response.data[i].evaluacion_trimestre[0].idEstatus == 4){
                    item['estado'] = '<span class="label label-primary">Registrado</span>';
                }else if(response.data[i].evaluacion_trimestre[0].idEstatus == 5){
                    item['estado'] = '<span class="label label-success">Firmado</span>';
                }
            }

            if(trimestre > 0){
                item['trim_'+trimestre] = '<span id="grid-trim-'+trimestre+'" class=""><span class="fa fa-unlock"></span></span>'; 
            }

            for(var j in response.data[i].registro_avance){
                var avance = response.data[i].registro_avance[j];
                var clase_icono = (avance.trimestre == trimestre)?'fa-unlock':'fa-circle';
                if(parseInt(avance.justificacion) > 0){
                    item['trim_'+avance.trimestre] = '<span id="grid-trim-'+avance.trimestre+'" class="text-danger"><span class="fa '+clase_icono+'"></span></span>';
                }else{
                    item['trim_'+avance.trimestre] = '<span id="grid-trim-'+avance.trimestre+'" class="text-success"><span class="fa '+clase_icono+'"></span></span>';
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

function cargar_datos_programa(e){
    var parametros = {'mostrar':'datos-programa-avance'};
    moduloResource.get(e,parametros,{
        _success: function(response){
            $('#modalDatosSeguimiento').find(".modal-title").html('Detalles del avance');

            $('#programa-presupuestario').text(response.data.claveProgramaPresupuestario + ' ' +response.data.programaPresupuestario);
            $('#unidad-responsable').text(response.data.claveUnidadResponsable + ' ' + response.data.unidadResponsable);
            
            var datos_programa = {};

            for(var i in response.data.indicadores){
                var indicador = response.data.indicadores[i];

                datos_programa[indicador.id] = {
                    'id': indicador.id,
                    'claveTipo': indicador.claveTipoIndicador,
                    'nombre': indicador.descripcionIndicador,
                    'metas': {
                        1: (parseFloat(indicador.trim1) || 0) + 0,
                        2: (parseFloat(indicador.trim1) || 0) + (parseFloat(indicador.trim2) || 0) + 0,
                        3: (parseFloat(indicador.trim1) || 0) + (parseFloat(indicador.trim2) || 0) + (parseFloat(indicador.trim3) || 0) + 0,
                        4: (parseFloat(indicador.trim1) || 0) + (parseFloat(indicador.trim2) || 0) + (parseFloat(indicador.trim3) || 0) + (parseFloat(indicador.trim4) || 0) + 0
                    },
                    'avances': {
                        1:0,
                        2:0,
                        3:0,
                        4:0
                    }
                }

                if(indicador.registro_avance.length){
                    for(var j in indicador.registro_avance){
                        var avance = indicador.registro_avance[j];
                        datos_programa[indicador.id]['avances'][avance.trimestre] = +parseFloat(avance.avance).toFixed(2);
                    }
                }
            }
            var total_avance_acumulado = {};
            for(var i = 1; i <= 4; i++){
                var html_tbody = '';
                for(var j in datos_programa){
                    var indicador = datos_programa[j];

                    if(!total_avance_acumulado[j]){
                        total_avance_acumulado[j] = 0;
                    }

                    total_avance_acumulado[j] += indicador['avances'][i];

                    html_tbody += '<tr data-clave="'+indicador['claveTipo']+'" data-id="'+indicador['id']+'">';
                    html_tbody += '<td>'+indicador['claveTipo']+'</td>'
                    html_tbody += '<td>'+indicador['nombre']+'</td>'
                    html_tbody += '<td class="meta-acumulada" data-meta="'+indicador['metas'][i]+'">'+indicador['metas'][i].format(2)+'</td>';
                    html_tbody += '<td class="avance-acumulado" data-avance="'+indicador['avances'][i]+'">'+indicador['avances'][i].format(2)+'</td>';
                    html_tbody += '<td class="bg-success">'+total_avance_acumulado[j].format(2)+'</td>';
                    html_tbody += '</tr>';
                }
                $('#avance-trim-'+i+' > tbody').empty();
                $('#avance-trim-'+i+' > tbody').append(html_tbody);
            };

            $('#btn-editar-avance').attr('data-id-programa',e);
            
            if(response.data.evaluacion_trimestre.length){
                if(response.data.evaluacion_trimestre[0].idEstatus == 4 || response.data.evaluacion_trimestre[0].idEstatus == 5){
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
    window.location.href = SERVER_HOST+'/rendicion-cuentas/avance-programa/' + $('#btn-editar-avance').attr('data-id-programa');
});

$('#btn-reporte').on('click',function(){
    window.open(SERVER_HOST+'/v1/reporte-programa/' + $('#btn-editar-avance').attr('data-id-programa'));
});

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