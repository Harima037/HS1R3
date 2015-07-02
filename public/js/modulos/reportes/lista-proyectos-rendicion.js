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

            item.id = response.data[i].id;
            item.clave = response.data[i].clavePresup;
            item.nombre_tecnico = response.data[i].nombreTecnico;
            item.revisor = response.data[i].nombreRevisor || '<span class="text-muted">Sin revisor asignado</span>';
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
    moduloResource.get(e,null,{
        _success: function(response){
            $('#modalDatosSeguimiento').find(".modal-title").html('Clave: <small>' + response.data.ClavePresupuestaria + '</small>');

            $('#nombre-tecnico').text(response.data.nombreTecnico);
            $('#programa-presupuestario').text(response.data.datos_programa_presupuestario.clave + ' ' + response.data.datos_programa_presupuestario.descripcion);
            $('#funcion').text(response.data.datos_funcion.clave + ' ' + response.data.datos_funcion.descripcion);
            $('#subfuncion').text(response.data.datos_sub_funcion.clave + ' ' + response.data.datos_sub_funcion.descripcion);
            $('#id').val(response.data.id);

            for(var i in response.data.evaluacion_meses){
                var evaluacion = response.data.evaluacion_meses[i];
                var icono = 'fa-file-pdf-o';
                var clase = 'btn-default';
                if(evaluacion.idEstatus == 4){
                    icono = 'fa-check';
                    clase = 'btn-primary';
                }else{
                    icono = 'fa-pencil';
                    clase = 'btn-success';
                }
                $('#rep_metas_'+evaluacion.mes).html('<button onClick="cargarReporte(\'seg-metas\','+evaluacion.mes+')" class="btn '+clase+'" type="button"><span class="fa '+icono+'"></span></button>');
                if(evaluacion.mes%3 == 0){
                    $('#rep_benef_'+evaluacion.mes).html('<button onClick="cargarReporte(\'seg-beneficiarios\','+evaluacion.mes+')" class="btn '+clase+'" type="button"><span class="fa '+icono+'"></span></button>');
                    $('#rep_plan_'+evaluacion.mes).html('<button onClick="cargarReporte(\'plan-mejora\','+evaluacion.mes+')" class="btn '+clase+'" type="button"><span class="fa '+icono+'"></span></button>');
                    $('#rep_cuenta_'+evaluacion.mes).html('<button onClick="cargarReporte(\'analisis\','+evaluacion.mes+')" class="btn '+clase+'" type="button"><span class="fa '+icono+'"></span></button>');
                }
            }

            $('#modalDatosSeguimiento').modal('show');
        }
    });
}
$('#modalDatosSeguimiento').on('hide.bs.modal',function(e){
    $('#tabla-reportes tbody td.reporte-boton').html('<span class="fa fa-times"></span>');
});

function cargarReporte(tipo,mes){
    if($('#id').val()){
        var parametros = $('#id').val() + '?tipo='+tipo+'&mes='+mes;
        window.open(SERVER_HOST+'/v1/reporte-evaluacion/'+parametros);
    }
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