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
	$('#btn-metas-cumplidas').on('click',function(){ cargarGrafica(); });
}

function cargarGrafica(tipo_grafica){
	if($('#jurisdiccion').val() != ''){
		graficaMetasJurisdiccion();
	}else{
		graficaMetasCumplidas()
	}
}

function graficaMetasJurisdiccion(){
	var parametros = {grafica:'metas_cumplidas',estatal:1,jurisdiccion:$('#jurisdiccion').val()};
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

function graficaMetasCumplidas(){
	var parametros = {grafica:'metas_cumplidas_jurisdiccion'};
	moduloResource.get(null,parametros,{
		_success: function(response){
			var data_unidades = [['Jurisdiccion','Porcentaje',{role:'annotation'},{role:'tooltip',p:{html:true}}]];
			for(var i in response.data){
                var dato = response.data[i];
                var clave = dato.clave;
                var porcentaje = (dato.cumplidas*100) / dato.totalMetas;
                data_unidades.push([
                    clave,porcentaje,(porcentaje.format(2)) + '%',
                    '<table border="0" cellpadding="0" cellspacing="0"><tr><th class="text-center" style="white-space:nowrap;" colspan="2"><big>'+clave+' '+dato.jurisdiccion+'</big></th></tr><tr><td style="white-space:nowrap;">Total Metas: </td><th class="text-center" style="font-weight:bold;">'+(dato.totalMetas.format(2))+'</th></tr><tr><td style="white-space:nowrap;">Metas Cumplidas: </td><th class="text-center" style="font-weight:bold;">'+(dato.cumplidas.format(2))+'</th></tr></table>'
                ]);
            }

			var data = new google.visualization.arrayToDataTable(data_unidades);
	        var options = {
	            title: 'Porcentaje de cumplimiento de metas por Jurisdicciones',
	            hAxis: { title: 'Jurisdicción' },
	            vAxis: { title: 'Porcentaje',maxValue:100,minValue:0},
	            legend:{ position:'none' },
	            annotations: { alwaysOutside:true },
	            tooltip: {isHtml: true}
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