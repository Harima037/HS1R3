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
google.setOnLoadCallback(datos_cargadoss);

function datos_cargadoss(){
	$('#mensaje-carga-librerias').html('<span class="fa fa-2x fa-check"></span> <big>Librerias cargadas con éxito.</big>');
	$('#lnk-proy-unidad').on('click',function(){ cargarGrafica('proy_unidad'); });
	$('#lnk-proy-tipos').on('click',function(){ cargarGrafica('proy_tipos'); });
	$('#lnk-metas-unidad').on('click',function(){ cargarGrafica('metas_unidad'); });
	$('#lnk-metas-cumplidas').on('click',function(){ cargarGrafica('metas_cumplidas'); });
	$('#lnk-presup-fuente').on('click',function(){ cargarGrafica('presup_fuente'); });
	$('#lnk-presup-ejercido').on('click',function(){ cargarGrafica('presup_ejercido'); });
	$('#lnk-presup-ejercido-capitulo').on('click',function(){ cargarGrafica('presup_ejercido_capitulo'); });
}

function cargarGrafica(tipo_grafica){
	switch(tipo_grafica){
		case 'proy_unidad': graficaProyectosDireccion(); break;
		case 'proy_tipos': graficaProyectosTipo(); break;
		case 'metas_unidad': graficaMetasDireccion(); break;
		case 'metas_cumplidas': graficaMetasCumplidas(); break;
		case 'presup_fuente': graficaPresupuestoFuente(); break;
		case 'presup_ejercido': graficaPresupuestoEjercido(); break;
		case 'presup_ejercido_capitulo': graficaPresupuestoEjercidoCapitulo(); break;
	}
}

function graficaMetasCumplidas(){
	var parametros = {grafica:'metas_cumplidas'};
	moduloResource.get(null,parametros,{
		_success: function(response){
			$('#area-graficas').empty();
			$('#area-graficas').html('<div class="row"><div class="col-sm-6"><div id="left-grafica" style="height:500px;"></div></div><div class="col-sm-6"><div id="right-grafica" style="height:500px;"></div></div></div>')
			var data = google.visualization.arrayToDataTable([
				['Tipo', 'Indicadores'],
				['Metas Cumplidas',response.data.cumplidas],
				['Metas No Cumplidas',(response.data.bajoAvance + response.data.altoAvance)]
			]);

			var options = { 
				title:'Metas ( '+response.total.format(2)+' )',
				legend:{position:'bottom'}
			};

			var chart = new google.visualization.PieChart(document.getElementById('left-grafica'));
			chart.draw(data, options);

			var data = google.visualization.arrayToDataTable([
				['Tipo', 'Indicadores'],
				['Alto Avance',response.data.altoAvance],
				['Bajo Avance',response.data.bajoAvance]
			]);

			var options = { 
				title:'Metas No Cumplidas ( '+(response.data.bajoAvance + response.data.altoAvance).format(2)+' )',
				legend:{position:'bottom'},
				slices:{
					0: {color:'#DC3912'},
					1: {color:'#AA1505'}
				}
			};

			var chart = new google.visualization.PieChart(document.getElementById('right-grafica'));
			chart.draw(data, options);
		}
	});
}

function graficaMetasDireccion(){
	var parametros = {grafica:'metas_unidad'};
	moduloResource.get(null,parametros,{
		_success: function(response){
			var elementos = [['Unidad Responsable', 'Metas']];
			for(var i in response.data){
				elementos.push(
					[
						response.data[i].clave + ' ' + response.data[i].unidad,
						+(parseFloat(response.data[i].noMetas) || 0)
					]
				);
			}
			var data = google.visualization.arrayToDataTable(elementos);

			var options = { 
				title:'Metas Programadas por Dirección ( Total de Metas : ' + (parseFloat(response.total)||0).format(2) + ' )',
				legend:{position:'right',alignment:'center'}
			};

			var chart = new google.visualization.PieChart(document.getElementById('area-graficas'));
			chart.draw(data, options);
		}
	});
}

function graficaPresupuestoEjercido(){
	var parametros = {grafica:'presupuesto_ejercido'};
	moduloResource.get(null,parametros,{
		_success: function(response){
			var elementos = [['Presupuesto', 'Total']];
			
			elementos.push(
				[
					'Ejercido',
					+(parseFloat(response.data.presupuestoEjercido) || 0)
				]
			);

			elementos.push(
				[
					'No Ejercido',
					(+(parseFloat(response.data.presupuestoModificado) || 0)) - (+(parseFloat(response.data.presupuestoEjercido) || 0))
				]
			);
			
			var data = google.visualization.arrayToDataTable(elementos);

			var formatter = new google.visualization.NumberFormat( {pattern: '$ #,###,###,###.##'} );
			formatter.format(data, 1);

			var options = { 
				title:'Presupuesto : $ '+(parseFloat(response.data.presupuestoModificado)||0).format(2),
				legend:{position:'bottom',alignment:'center'}
			};

			var chart = new google.visualization.PieChart(document.getElementById('area-graficas'));
			chart.draw(data, options);
		}
	});
}

function graficaPresupuestoEjercidoCapitulo(){
	var parametros = {grafica:'presupuesto_ejercido_capitulo'};
	moduloResource.get(null,parametros,{
		_success: function(response){
			var elementos = [['Capitulo', 'Presupuesto Ejercido']];
			for(var i in response.data){
				elementos.push(
					[
						response.data[i].capitulo,
						+(parseFloat(response.data[i].presupuestoEjercido) || 0)
					]
				);
			}
			var data = google.visualization.arrayToDataTable(elementos);

			var formatter = new google.visualization.NumberFormat( {pattern: '$ #,###,###,###.##'} );
			formatter.format(data, 1);

			var options = { 
				title:'Presupuesto : $ '+(parseFloat(response.total)||0).format(2),
				legend:{position:'right',alignment:'center'}
			};

			var chart = new google.visualization.PieChart(document.getElementById('area-graficas'));
			chart.draw(data, options);
		}
	});
}

function graficaPresupuestoFuente(){
	var parametros = {grafica:'presupuesto_fuente'};
	moduloResource.get(null,parametros,{
		_success: function(response){
			var elementos = [['Fuente Financiamiento', 'Presupuesto Modificado']];
			for(var i in response.data){
				elementos.push(
					[
						response.data[i].fuenteFinanciamiento,
						+(parseFloat(response.data[i].presupuestoModificado) || 0)
					]
				);
			}
			var data = google.visualization.arrayToDataTable(elementos);

			var formatter = new google.visualization.NumberFormat( {pattern: '$ #,###,###,###.##'} );
			formatter.format(data, 1);

			var options = { 
				title:'Presupuesto : $ '+(parseFloat(response.total)||0).format(2),
				legend:{position:'right',alignment:'center'}
			};

			var chart = new google.visualization.PieChart(document.getElementById('area-graficas'));
			chart.draw(data, options);
		}
	});
}

function graficaProyectosDireccion(){
	var parametros = {grafica:'proyectos_direccion'};
	moduloResource.get(null,parametros,{
		_success: function(response){
			var elementos = [['Unidad Responsable', 'Proyectos']];
			for(var i in response.data){
				elementos.push(
					[
						response.data[i].clave + ' ' + response.data[i].unidad,
						+(parseFloat(response.data[i].noProyectos) || 0)
					]
				);
			}
			var data = google.visualization.arrayToDataTable(elementos);

			var options = { 
				title:'Proyectos Autorizados por Dirección ( Total de Proyectos : ' + (parseFloat(response.total)||0).format(2) + ' )',
				legend:{position:'right',alignment:'center'}
			};

			var chart = new google.visualization.PieChart(document.getElementById('area-graficas'));
			chart.draw(data, options);
		}
	});
}

function graficaProyectosTipo(){
	var parametros = {grafica:'proyectos_tipo'};
	moduloResource.get(null,parametros,{
		_success: function(response){
			var elementos = [['Clasificacion de Proyecto', 'Proyectos']];
			for(var i in response.data){
				elementos.push(
					[
						response.data[i].tipoProyecto,
						+(parseFloat(response.data[i].noProyectos) || 0)
					]
				);
			}
			var data = google.visualization.arrayToDataTable(elementos);

			var options = { 
				title:'Proyectos Autorizados por Tipo ( Total de Proyectos : ' + (parseFloat(response.total)||0).format(2) + ' )',
				legend:{position:'right',alignment:'center'}
			};

			var chart = new google.visualization.PieChart(document.getElementById('area-graficas'));
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