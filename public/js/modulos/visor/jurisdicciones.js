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
	var titulo = '';
	$('#imagen').val('');
	$('#imagen2').val('');
	$('#titulo').val('');
	if($('#jurisdiccion').val() != ''){
		graficaMetasJurisdiccion();
		titulo = 'Porcentaje de cumplimiento de metas<br> <small>Jurisdicción:</small> <small><b>'+$('#jurisdiccion option:selected').text()+'</b></small>';
	}else{
		graficaMetasCumplidas()
		titulo = 'Porcentaje de cumplimiento de metas por Jurisdicciones';
	}
	if(titulo){
		$('#titulo').val(titulo);
		$('#titulo_grafica').html(titulo);
	}
}

function graficaMetasJurisdiccion(){
	var parametros = {grafica:'metas_cumplidas',estatal:1,jurisdiccion:$('#jurisdiccion').val()};
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
	            hAxis: { title: 'Jurisdicción' },
	            vAxis: { title: 'Porcentaje',maxValue:100,minValue:0},
	            legend:{ position:'none' },
	            annotations: { alwaysOutside:true },
	            tooltip: {isHtml: true},
	            chartArea:{ width:'100%',left:60}
	        };
	        var chart = new google.visualization.ColumnChart(document.getElementById('area-graficas'));
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