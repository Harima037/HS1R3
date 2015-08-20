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
	$('#lnk-proy-unidad').on('click',function (e){ e.preventDefault(); cargarGrafica('proy_unidad'); });
	$('#lnk-proy-tipos').on('click',function (e){ e.preventDefault(); cargarGrafica('proy_tipos'); });
	$('#lnk-metas-unidad').on('click',function (e){ e.preventDefault(); cargarGrafica('metas_unidad'); });
	$('#lnk-presup-fuente').on('click',function (e){ e.preventDefault(); cargarGrafica('presup_fuente'); });
	$('#lnk-presup-ejercido').on('click',function (e){ e.preventDefault(); cargarGrafica('presup_ejercido'); });
	$('#lnk-presup-ejercido-capitulo').on('click',function (e){ e.preventDefault(); cargarGrafica('presup_ejercido_capitulo'); });
	$('#lnk-metas-cumplidas').on('click',function (e){ e.preventDefault(); cargarGrafica('metas_cumplidas'); });
}

function cargarGrafica(tipo_grafica){
	var titulo = '';
	$('#filtro-unidades').addClass('hidden');
	$('#filtro-jurisdicciones').addClass('hidden');
	$('#unidad').val('');
	$('#jurisdiccion').val('');
	$('#panel-btn-filtro').addClass('hidden');
	$('#btn-filtro').attr('data-grafica',tipo_grafica);
	$('#imagen').val('');
	$('#imagen2').val('');
	$('#titulo').val('');
	switch(tipo_grafica){
		case 'proy_unidad': 
			graficaProyectosDireccion(); 
			titulo = 'Proyectos por Dirección';
			break;
		case 'proy_tipos': 
			graficaProyectosTipo(); 
			titulo = 'Proyectos por Tipología';
			break;
		case 'metas_unidad': 
			graficaMetasDireccion(); 
			titulo = 'Metas Programadas por Dirección';
			break;
		case 'metas_cumplidas': 
			graficaMetasCumplidas(); 
			titulo = 'Porcentaje de Metas Cumplidas';
			if($('#unidad').val() != ''){
				titulo += '<br><small>Unidad Responsable:</small> <small><b>'+$('#unidad option:selected').text()+'</b></small>';
			}
			if($('#jurisdiccion').val() != ''){
				titulo += '<br><small>Jurisdicción:</small> <small><b>'+$('#jurisdiccion option:selected').text()+'</b></small>';
			}
			break;
		case 'presup_fuente': 
			graficaPresupuestoFuente(); 
			titulo = 'Presupuesto por Fuente de Financiamiento';
			break;
		case 'presup_ejercido': 
			graficaPresupuestoEjercido(); 
			titulo = 'Presupuesto Ejercido';
			break;
		case 'presup_ejercido_capitulo': 
			graficaPresupuestoEjercidoCapitulo(); 
			titulo = 'Presupuesto Ejercido por Capitulo';
			break;
	}
	if(titulo){
		$('#titulo').val(titulo);
		$('#titulo_grafica').html(titulo);
	}
}

$('#btn-filtro').on('click',function (e){
	var titulo = '';
	e.preventDefault();
	var tipo_grafica = $('#btn-filtro').attr('data-grafica');
	if(tipo_grafica){
		switch(tipo_grafica){
			case 'proy_unidad': graficaProyectosDireccion(); break;
			case 'proy_tipos': graficaProyectosTipo(); break;
			case 'metas_unidad': graficaMetasDireccion(); break;
			case 'metas_cumplidas': 
				graficaMetasCumplidas(); 
				titulo = 'Porcentaje de Metas Cumplidas';
				if($('#unidad').val() != ''){
					titulo += '<br><small>Unidad Responsable:</small> <small><b>'+$('#unidad option:selected').text()+'</b></small>';
				}
				if($('#jurisdiccion').val() != ''){
					titulo += '<br><small>Jurisdicción:</small> <small><b>'+$('#jurisdiccion option:selected').text()+'</b></small>';
				}
				break;
			case 'presup_fuente': 
				graficaPresupuestoFuente(); 
				if($('#unidad').val() != ''){
					titulo = 'Presupuesto por Fuente de Financiamiento<br><small>Unidad Responsable:</small> <small><b>'+$('#unidad option:selected').text()+'</b></small>';
				}
			break;
			case 'presup_ejercido': graficaPresupuestoEjercido(); break;
			case 'presup_ejercido_capitulo': graficaPresupuestoEjercidoCapitulo(); break;
		}
	}
	if(titulo){
		$('#titulo').val(titulo);
		$('#titulo_grafica').html(titulo);
	}
});

function graficaMetasCumplidas(){
	$('#filtro-unidades').removeClass('hidden');
	$('#filtro-jurisdicciones').removeClass('hidden');
	$('#panel-btn-filtro').removeClass('hidden');
	var parametros = {grafica:'metas_cumplidas'};
	if($('#unidad').val() != ''){
		parametros.unidad = $('#unidad').val();
	}
	if($('#jurisdiccion').val() != ''){
		parametros.jurisdiccion = $('#jurisdiccion').val();
	}
	moduloResource.get(null,parametros,{
		_success: function(response){
			$('#area-graficas').empty();
			$('#area-graficas').html('<div style="width:100%"><div style="width:60%;float:left;"><div id="left-grafica" style="height:530px;"></div></div><div style="width:40%;float:left;"><div id="right-grafica" style="height:380px;"></div></div></div>')
			
			var data = google.visualization.arrayToDataTable([
				['Tipo', 'Indicadores'],
				['Metas Cumplidas',response.data.cumplidas],
				['Metas No Cumplidas',(response.data.bajoAvance + response.data.altoAvance)],
				['Metas Programadas en Meses Posteriores',response.data.posteriores]
			]);

			var options = { 
				title:'Metas ( '+response.total.format(2)+' )',
				legend:{position:'right',maxLines:5},
				chartArea:{ width:'100%',left:5,right:0,top:60,bottom:0 }
			};

			var chart = new google.visualization.PieChart(document.getElementById('left-grafica'));
			google.visualization.events.addListener(chart, 'ready', function () {
		      $('#imagen').val(chart.getImageURI());
		    });
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
				},
				chartArea:{ width:'100%',left:10,right:0,top:60,bottom:0 }
			};

			var chart2 = new google.visualization.PieChart(document.getElementById('right-grafica'));
			google.visualization.events.addListener(chart2, 'ready', function () {
		      $('#imagen2').val(chart2.getImageURI());
		    });
			chart2.draw(data, options);
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
				title:'Total de Metas : ' + (parseFloat(response.total)||0).format(2),
				legend:{position:'right',alignment:'center'},
				chartArea:{ width:'100%',height:'100%',left:0,right:0,top:60,bottom:0 }
			};

			var chart = new google.visualization.PieChart(document.getElementById('area-graficas'));
			google.visualization.events.addListener(chart, 'ready', function () {
		      $('#imagen').val(chart.getImageURI());
		    });
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
				title:'Total Presupuesto Aprobado : $ '+(parseFloat(response.data.presupuestoModificado)||0).format(2),
				legend:{position:'right',alignment:'center'},
				chartArea:{ width:'100%',height:'100%',left:0,right:0,top:60,bottom:0 }
			};

			var chart = new google.visualization.PieChart(document.getElementById('area-graficas'));
			google.visualization.events.addListener(chart, 'ready', function () {
		      $('#imagen').val(chart.getImageURI());
		    });
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
						response.data[i].clave + ' - ' + response.data[i].capitulo,
						+(parseFloat(response.data[i].presupuestoEjercido) || 0)
					]
				);
			}
			var data = google.visualization.arrayToDataTable(elementos);

			var formatter = new google.visualization.NumberFormat( {pattern: '$ #,###,###,###.##'} );
			formatter.format(data, 1);

			var options = { 
				title:'Total Presupuesto Ejercido : $ '+(parseFloat(response.total)||0).format(2),
				legend:{position:'right',alignment:'center'},
				chartArea:{ width:'100%',height:'100%',left:0,right:0,top:60,bottom:0 }
			};

			var chart = new google.visualization.PieChart(document.getElementById('area-graficas'));
			google.visualization.events.addListener(chart, 'ready', function () {
		      $('#imagen').val(chart.getImageURI());
		    });
			chart.draw(data, options);
		}
	});
}

function graficaPresupuestoFuente(){
	$('#filtro-unidades').removeClass('hidden');
	$('#panel-btn-filtro').removeClass('hidden');
	var parametros = {grafica:'presupuesto_fuente'};
	if($('#unidad').val() != ''){
		parametros.unidad = $('#unidad').val();
	}
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
				title:'Total Presupuesto Aprobado : $ '+(parseFloat(response.total)||0).format(2),
				legend:{position:'right',alignment:'center'},
				chartArea:{ width:'100%',height:'100%',left:0,right:0,top:60,bottom:0 }
			};

			var chart = new google.visualization.PieChart(document.getElementById('area-graficas'));
			google.visualization.events.addListener(chart, 'ready', function () {
		      $('#imagen').val(chart.getImageURI());
		    });
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
				title:'Total de Proyectos : ' + (parseFloat(response.total)||0).format(2),
				legend:{position:'right',alignment:'center'},
				chartArea:{ width:'100%',height:'100%',left:0,right:0,top:60,bottom:0 }
			};

			var chart = new google.visualization.PieChart(document.getElementById('area-graficas'));
			
			google.visualization.events.addListener(chart, 'ready', function () {
		      $('#imagen').val(chart.getImageURI());
		    });

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
				title:'Total de Proyectos : ' + (parseFloat(response.total)||0).format(2),
				legend:{position:'right',alignment:'center'},
				chartArea:{
					width:'100%',height:'100%',left:0,right:0,top:60,bottom:0
				}
			};

			var chart = new google.visualization.PieChart(document.getElementById('area-graficas'));
			google.visualization.events.addListener(chart, 'ready', function () {
		      $('#imagen').val(chart.getImageURI());
		    });
			chart.draw(data, options);
		}
	});
}

$('#btn-imprimir-grafica').on('click',function(){
	if($('#imagen').val()){
		$('#form-grafica').attr('action',SERVER_HOST+'/visor/imprimir-grafica');
		$('#form-grafica').submit();
	}
});

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