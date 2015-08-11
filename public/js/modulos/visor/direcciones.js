/*=====================================

    # Nombre:
        estatal.js

    # Módulo:
        visor/estatal

    # Descripción:
        Visor con opciones para cargar las graficas de datos Estatales

=====================================*/

// Inicialización General para casi cualquier módulo
var moduloResource = new RESTfulRequests(SERVER_HOST+'/v1/visor');
google.load("visualization", "1", {packages:["corechart"]});
google.setOnLoadCallback(datos_cargados);

function datos_cargados(){
	$('#mensaje-carga-librerias').html('<span class="fa fa-2x fa-check"></span> <big>Librerias cargadas con éxito.</big>');
	$('#btn-metas-cumplidas').on('click',function(){ cargarGrafica('unidad_metas_cumplidas'); });
	$('#btn-presupuesto-ejercido').on('click',function(){ cargarGrafica('unidad_persupuesto_ejercido'); });
	$('#btn-metas-presupuesto').on('click',function(){ cargarGrafica('unidad_metas_presupuesto'); });
}

function cargarGrafica(tipo_grafica){
	switch(tipo_grafica){
		case 'unidad_metas_cumplidas': graficaMetasCumplidas(); break;
		case 'unidad_persupuesto_ejercido': graficaPresupuestoEjercido(); break;
		case 'unidad_metas_presupuesto': graficaMetasPresupuesto(); break;
	}
}

function graficaMetasCumplidas(){
	var parametros = {grafica:'metas_cumplidas_unidad'};
	moduloResource.get(null,parametros,{
		_success: function(response){
			var data_unidades = [['Unidad Responsable','Porcentaje',{role:'annotation'},{role:'tooltip',p:{html:true}}]];
			for(var clave in response.data){
                var dato = response.data[clave];
                var porcentaje = (dato.cumplidas*100) / dato.totalMetas;
                data_unidades.push([
                    dato.unidadAbreviacion,porcentaje,(porcentaje.format(2)) + '%',
                    '<table border="0" cellpadding="0" cellspacing="0"><tr><th class="text-center" style="white-space:nowrap;" colspan="2"><big>'+clave+' '+dato.unidadResponsable+'</big></th></tr><tr><td style="white-space:nowrap;">Total Metas: </td><th class="text-center" style="font-weight:bold;">'+(dato.totalMetas.format(2))+'</th></tr><tr><td style="white-space:nowrap;">Metas Cumplidas: </td><th class="text-center" style="font-weight:bold;">'+(dato.cumplidas.format(2))+'</th></tr></table>'
                ]);
            }

			var data = new google.visualization.arrayToDataTable(data_unidades);
	        var options = {
	            title: 'Porcentaje de cumplimiento de metas por Unidad Responsable',
	            hAxis: { title: 'Unidad Responsable' },
	            vAxis: { title: 'Porcentaje',maxValue:100,minValue:0},
	            legend:{ position:'none' },
	            annotations: { textStyle: { fontSize:10 }, alwaysOutside:true },
	            tooltip: {isHtml: true}
	        };
	        var chart = new google.visualization.ColumnChart(document.getElementById('area-graficas'));
	        chart.draw(data, options);
		}
	});
}

function graficaPresupuestoEjercido(){
	var parametros = {grafica:'presupuesto_ejercido_unidad'};
	moduloResource.get(null,parametros,{
		_success: function(response){
			var data_unidades = [['Unidad Responsable','Porcentaje',{role:'annotation'},{role:'tooltip',p:{html:true}}]];
			for(var i in response.data){
                var dato = response.data[i];
                var porcentaje = (parseFloat(dato.presupuestoEjercido)*100) / parseFloat(dato.presupuestoModificado);
                data_unidades.push([
                    dato.unidadAbreviacion,porcentaje,(porcentaje.format(2)) + '%',
                    '<table border="0" cellpadding="0" cellspacing="0"><tr><th class="text-center" style="white-space:nowrap;" colspan="2"><big>'+dato.clave+' '+dato.unidadResponsable+'</big></th></tr><tr><td style="white-space:nowrap;">Presupuesto Modificado: </td><th class="text-center" style="font-weight:bold;">$ '+(parseFloat(dato.presupuestoModificado).format(2))+'</th></tr><tr><td style="white-space:nowrap;">Presupuesto Ejercido: </td><th class="text-center" style="font-weight:bold;">$ '+(parseFloat(dato.presupuestoEjercido).format(2))+'</th></tr></table>'
                ]);
            }

			var data = new google.visualization.arrayToDataTable(data_unidades);
	        var options = {
	            title: 'Porcentaje de Presupuseto Ejercido por Unidad Responsable',
	            hAxis: { title: 'Unidad Responsable' },
	            vAxis: { title: 'Porcentaje',maxValue:100,minValue:0},
	            legend:{ position:'none' },
	            annotations: { textStyle: { fontSize:10 }, alwaysOutside:true },
	            tooltip: {isHtml: true}
	        };
	        var chart = new google.visualization.ColumnChart(document.getElementById('area-graficas'));
	        chart.draw(data, options);
		}
	});
}

function graficaMetasPresupuesto(){
	var parametros = {grafica:'presupuesto_vs_metas_unidad'};
	moduloResource.get(null,parametros,{
		_success: function(response){
			var data = new google.visualization.DataTable();
			data.addColumn('string', 'Unidad Responsable');
			data.addColumn('number', 'Metas');
			data.addColumn({type:'string',role:'annotation'});
			data.addColumn({type:'string',role:'tooltip',p:{html:true}});
			data.addColumn('number', 'Presupuesto');
			data.addColumn({type:'string',role:'annotation'});
			data.addColumn({type:'string',role:'tooltip',p:{html:true}});

			var datos = [];
			for(var clave in response.data){
				var dato = response.data[clave];
				var porcentajeMetas = (dato.metasCumplidas*100) / dato.metasTotal;
				var porcentajePresupuesto = (dato.presupuestoEjercido*100) / dato.presupuestoModificado;
				datos.push(
					[
						dato.abreviacion,
						porcentajeMetas,porcentajeMetas.format(2)+'%',
						'<table border="0" cellpadding="0" cellspacing="0"><tr><th class="text-center" style="white-space:nowrap;" colspan="2"><big>'+clave+' '+dato.unidadResponsable+'</big></th></tr><tr><td style="white-space:nowrap;">Total Metas: </td><th class="text-center" style="font-weight:bold;">'+(dato.metasTotal.format(2))+'</th></tr><tr><td style="white-space:nowrap;">Metas Cumplidas: </td><th class="text-center" style="font-weight:bold;">'+(dato.metasCumplidas.format(2))+'</th></tr></table>',
						porcentajePresupuesto,porcentajePresupuesto.format(2)+'%',
						'<table border="0" cellpadding="0" cellspacing="0"><tr><th class="text-center" style="white-space:nowrap;" colspan="2"><big>'+clave+' '+dato.unidadResponsable+'</big></th></tr><tr><td style="white-space:nowrap;">Presupuesto Modificado: </td><th class="text-center" style="font-weight:bold;">$ '+(dato.presupuestoModificado.format(2))+'</th></tr><tr><td style="white-space:nowrap;">Presupuesto Ejercido: </td><th class="text-center" style="font-weight:bold;">$ '+(dato.presupuestoEjercido.format(2))+'</th></tr></table>'
					]
				);
			}
			data.addRows(datos);

			var options = {
				title: 'Metas VS Presupuesto',
				hAxis: { title: 'Unidad Responsable' },
				vAxis: {
					title: 'Porcentaje',
					maxValue:100,
					minValue:0
				},
				tooltip: {isHtml: true},
				annotations: { alwaysOutside:true }
			};

			var chart = new google.visualization.ColumnChart(document.getElementById('area-graficas'));
			chart.draw(data, options);
		}
	});
}

/**
 * Number.prototype.format(n, x)
 * 
 * @param integer n: length of decimal
 * @param integer x: length of sections
 */
Number.prototype.format = function(n, x) {
    var re = '\\d(?=(\\d{' + (x || 3) + '})+' + (n > 0 ? '\\.' : '$') + ')';
    //return this.toFixed(Math.max(0, ~~n)).replace(new RegExp(re, 'g'), '$&,');
    var formateado = this.toFixed(Math.max(0, ~~n)).replace(new RegExp(re, 'g'), '$&,');
    var partes = formateado.split('.');
    if(parseInt(partes[1]) == 0){
        return partes[0];
    }else{
        return formateado;
    }
};