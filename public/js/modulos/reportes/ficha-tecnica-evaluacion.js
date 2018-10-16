/*=====================================

    # Nombre:
        ficha-tecnica-evaluacion.js

    # Módulo:
        reportes/ficha-tecnica-evaluacion

    # Descripción:
        Imprimir reporte de Seguimiento de Planes de Mejora

=====================================*/

// Inicialización General para casi cualquier módulo
var moduleResource = new RESTfulRequests(SERVER_HOST+'/v1/ficha-tecnica-evaluacion');
var moduleDatagrid = new Datagrid("#datagridPlanesMejora",moduleResource,{formatogrid:true,pagina:1,ejercicio:$('#ejercicio').val(),trimestre:$('#trimestre').val()});

var planesDeAccionDeMejora = {};

moduleDatagrid.init();
moduleDatagrid.actualizar({
    _success: function(response){
        moduleDatagrid.limpiar();
        var datos_grid = [];
        for(var i in response.data){
            var item = {};

            item.id = response.data[i].idProyecto;
            item.clave = response.data[i].clave;
            item.nombreTecnico = response.data[i].nombreTecnico;
            item.recomendacion = (response.data[i].recomendacion)?response.data[i].recomendacion:'-';
            //item.porcentaje = (parseFloat(response.data[i].porcentaje) || 0).format(2) + ' %';
            //item.botonGenerarFicha = '<button type="button" onClick="descargar_reporte('+response.data[i].idProyecto+')" class="btn btn-info btn-block"><span class="fa fa-file-excel-o"></span></button>'
            item.botonGenerarFicha = (response.data[i].recomendacion)?'<button type="button" onClick="descargar_reporte('+response.data[i].idProyecto+')" class="btn btn-info btn-block"><span class="fa fa-file-excel-o"></span></button>':'<button type="button" class="btn btn-default btn-block disabled"><span class="fa fa-file-excel-o"></span></button>';
            datos_grid.push(item);
        }
        moduleDatagrid.cargarDatos(datos_grid);                         
        moduleDatagrid.cargarTotalResultados(response.resultados,'<b>Plan(es) de Mejora</b>');
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
    //$('#mes-razones').val($('#mes').val());
	//$('#id').val(e);
	//$('#razones').val('');
    //$('#razones2').val('');
    $('#justificacion').text('');
    $('#plan_accion_mejora').text('');
    $('#recomendacion').val('');
    planesDeAccionDeMejora = {};

    $('#variable_tipo').val('');
    $('#variable_id_plan').val('');
    $('#variable_id').val('');

	var parametros = {'trimestre':$('#trimestre').val(),'ejercicio':$('#ejercicio').val()};
	
	/*for(var i in presupuestosIguales)
		if(presupuestosIguales[i].id == e)
		{
			MessageManager.show({data:'No es posible escribir razones si los presupuestos aprobado y modificado son iguales.',type:'OK',timer:3});
			return;
		}*/
    moduleResource.get(e,parametros,{
        _success: function(response){
            console.log(response);
            $('#modalDatosProyecto').modal('show');
            $('#clave').text(response.datos.proyecto.clave);
            $('#nombre').text(response.datos.proyecto.nombre);
            $('#programa').text(response.datos.proyecto.programa);
            $('#finalidad').text(response.datos.proyecto.finalidad);

            $('#autorizado').text(response.datos.proyecto.presupuesto_autorizado);
            $('#modificado').text(response.datos.proyecto.presupuesto_modificado);
            $('#devengado').text(response.datos.proyecto.presupuesto_devengado);
            $('#fuente_financiamiento').text(response.datos.proyecto.fuente_financiamiento);

            $('#evaluacion').text(response.datos.proyecto.evaluacion);

            var indicadores = '';
            for(var i in response.datos.proyecto.componentes){
                var componente = response.datos.proyecto.componentes[i];

                if(componente.tiene_plan_mejora){
                    planesDeAccionDeMejora['C-'+componente.id] = {
                        'id_plan': componente.plan_mejora[0].id,
                        'accion_mejora': componente.plan_mejora[0].accionMejora,
                        'justificacion': componente.registro_avance[0].justificacionAcumulada
                    }

                    indicadores += '<tr onClick="seleccionar(\'componente\','+componente.id+')">';
                    indicadores += '<td><input type="radio" name="seleccion" id="seleccion_componente_'+componente.id+'" value="componente.'+componente.id+'"></td>';
                }else{
                    indicadores += '<tr>';
                    indicadores += '<td></td>';
                }
                

                indicadores += '<td> Componente '+componente.nivel+'</td>';
                indicadores += '<td>'+componente.indicador+'</td>';
                indicadores += '<td>'+parseFloat(componente.programado_numerador).format(2)+'</td>';
                indicadores += '<td>'+parseFloat(componente.programado).format(2)+'</td>';
                indicadores += '<td>'+parseFloat(componente.alcanzado).format(2)+'</td>';
                indicadores += '<td>'+(parseFloat(componente.porcentaje_anual).format(2))+' %</td>';
                indicadores += '<td style="font-weight:bold; background-color:'+componente.auxiliar_fondo+'; color:'+componente.auxiliar_color+'">'+(parseFloat(componente.porcentaje_meta).format(2))+' %</td>';
                indicadores += '</tr>';

                for(var j in componente.actividades){
                    var actividad = componente.actividades[j];

                    if(actividad.tiene_plan_mejora){
                        planesDeAccionDeMejora['A-'+actividad.id] = {
                            'id_plan': actividad.plan_mejora[0].id,
                            'accion_mejora': actividad.plan_mejora[0].accionMejora,
                            'justificacion': actividad.registro_avance[0].justificacionAcumulada
                        }

                        indicadores += '<tr onClick="seleccionar(\'actividad\','+actividad.id+')">';
                        indicadores += '<td><input type="radio" name="seleccion" id="seleccion_actividad_'+actividad.id+'" value="actividad.'+actividad.id+'"></td>';
                    }else{
                        indicadores += '<tr>';
                        indicadores += '<td></td>';
                    }

                    indicadores += '<td> Actividad '+actividad.nivel+'</td>';
                    indicadores += '<td>'+actividad.indicador+'</td>';
                    indicadores += '<td>'+parseFloat(actividad.programado_numerador).format(2)+'</td>';
                    indicadores += '<td>'+parseFloat(actividad.programado).format(2)+'</td>';
                    indicadores += '<td>'+parseFloat(actividad.alcanzado).format(2)+'</td>';
                    indicadores += '<td>'+(parseFloat(actividad.porcentaje_anual).format(2))+' %</td>';
                    indicadores += '<td style="font-weight:bold; background-color:'+actividad.auxiliar_fondo+'; color:'+actividad.auxiliar_color+'">'+(parseFloat(actividad.porcentaje_meta).format(2))+' %</td>';
                    indicadores += '</tr>';
                }
            }

            $('#indicadores').html(indicadores);

            if(response.datos.proyecto.variable.tipo != 'NULL'){
                $('#recomendacion').val(response.datos.proyecto.variable.recomendacion);
                if(response.datos.proyecto.variable.tipo == 'C'){
                    tipo = 'componente';
                }else{
                    tipo = 'actividad';
                }
                seleccionar(tipo,response.datos.proyecto.variable.id);
                $('#seleccion_'+tipo+'_'+response.datos.proyecto.variable.id).prop('checked',true);
            }

            $('#id').val(response.datos.proyecto.id);
        }
    });	
}

$("#datagridPlanesMejora .txt-quick-search").off('keydown');
$("#datagridPlanesMejora .txt-quick-search").on('keydown', function(event){
    if (event.which == 13) {
        realizar_busqueda();
    }
});

$('#datagridPlanesMejora .btn-quick-search').off('click');
$('#datagridPlanesMejora .btn-quick-search').on('click',function(){
    realizar_busqueda();
})

function seleccionar(elemento,id){
    $('#seleccion_'+elemento+'_'+id).prop('checked',true);

    var llave = null;

    if(elemento == 'componente' && planesDeAccionDeMejora['C-'+id]){
        llave = 'C-'+id;
        $('#variable_tipo').val('C');
    }else if(planesDeAccionDeMejora['A-'+id]){
        llave = 'A-'+id;
        $('#variable_tipo').val('A');
    }

    if(llave){
        $('#justificacion').text(planesDeAccionDeMejora[llave].justificacion);
        $('#plan_accion_mejora').text(planesDeAccionDeMejora[llave].accion_mejora);

        $('#variable_id_plan').val(planesDeAccionDeMejora[llave].id_plan);
    }
    
    $('#variable_id').val(id);
}

function realizar_busqueda(){
    moduleDatagrid.setPagina(1);
    if($('.txt-quick-search').val() != ''){
        moduleDatagrid.parametros.buscar = $('.txt-quick-search').val();
    }else{
        delete moduleDatagrid.parametros.buscar;
    }
    moduleDatagrid.parametros.ejercicio = $('#ejercicio').val();
    moduleDatagrid.parametros.trimestre = $('#trimestre').val();
    moduleDatagrid.actualizar();
}

$('#btn-guardar').on('click',function (e) {
    e.preventDefault();

    var parametros = {'trimestre':$('#trimestre').val(),'ejercicio':$('#ejercicio').val(),'id_plan':$('#variable_id_plan').val(),'tipo':$('#variable_tipo').val(),'recomendacion':$('#recomendacion').val(),'variable_id':$('#variable_id').val()};
    
    if(!parametros.variable_id){
        console.log('seleccionar variable');
        MessageManager.show({data:'Selecciona un indicador',type:'ERR',timer:5}); 
        return false;
    }

    if(!parametros.recomendacion.trim()){
        MessageManager.show({data:'Escriba una recomendación',type:'ERR',timer:5}); 
        return false;
    }

    moduleResource.put($('#id').val(), parametros,{
        _success: function(response){
            moduleDatagrid.actualizar();
            
            MessageManager.show({data:'Ficha actualizada con éxito.',type:'OK',timer:4});
        }
    });
});

/*===================================*/
// Configuración General para cualquier módulo
function descargar_reporte(id_proyecto){
    var parametros = '?mes='+$('#mes').val()+'&ejercicio='+$('#ejercicio').val()+'&trimestre='+$('#trimestre').val();
    window.open(SERVER_HOST+'/v1/ficha-tecnica-evaluacion-excel/'+id_proyecto+parametros);
}
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
