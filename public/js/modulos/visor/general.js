/*=====================================

    # Nombre:
        general.js

    # Módulo:
        visor/general

    # Descripción:
        Para grafica de Desempeño General

=====================================*/

// Inicialización General para casi cualquier módulo
var moduloResource = new RESTfulRequests(SERVER_HOST+'/v1/visor');
google.load("visualization", "1", {packages:["corechart"]});
google.setOnLoadCallback(datos_cargados);

function datos_cargados(){
  $('#mensaje-carga-librerias').html('<span class="fa fa-2x fa-check"></span> <big>Librerias cargadas con éxito.</big>');
  $('#lnk-metas-juris').on('click',function (e){ e.preventDefault(); cargarGrafica('metas_cumplidas_jurisdicciones'); });
  $('#lnk-metas-juri-asig').on('click',function (e){ e.preventDefault(); cargarGrafica('metas_cumplidas_jurisdiccion'); });
  $('#lnk-proy-unidad').on('click',function (e){ e.preventDefault(); cargarGrafica('proy_unidad'); });
  $('#lnk-proy-tipos').on('click',function (e){ e.preventDefault(); cargarGrafica('proy_tipos'); });
  $('#lnk-metas-unidad').on('click',function (e){ e.preventDefault(); cargarGrafica('metas_unidad'); });
  $('#lnk-metas-cumplidas').on('click',function (e){ e.preventDefault(); cargarGrafica('metas_cumplidas'); });
}

function cargarGrafica(tipo_grafica){
  var titulo = '';
  if(tipo_grafica != 'resumen_presupuesto'){
    $('#tipo').val('grafica');
  }else{
    $('#tipo').val('tabla');
  }
  $('#area-tablas').empty();
  $('#area-graficas').removeClass('hidden');
  $('#filtro-unidades').addClass('hidden');
  $('#filtro-jurisdicciones').addClass('hidden');
  $('#unidad').val('');
  $('#panel-btn-filtro').addClass('hidden');
  $('#btn-filtro').attr('data-grafica',tipo_grafica);
  $('#imagen').val('');
  $('#imagen2').val('');
  $('#titulo').val('');
  switch(tipo_grafica){
    case 'metas_cumplidas_jurisdicciones':
      graficaMetasCumplidasJurisdicciones();
      titulo = 'Porcentaje de cumplimiento de metas por jurisdicciones';
      break;
    case 'metas_cumplidas_jurisdiccion':
      graficaMetasCumplidas();
      $('#filtro-unidades').addClass('hidden');
      $('#filtro-jurisdicciones').addClass('hidden');
      $('#panel-btn-filtro').addClass('hidden');
      titulo = 'Porcentaje de Metas Cumplidas';
      titulo += '<br><small>Jurisdicción:</small> <small><b>'+$('#filtro-jurisdicciones span').text()+'</b></small>';
      break;
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
      titulo += '<br><small>Jurisdicción:</small> <small><b>'+$('#filtro-jurisdicciones span').text()+'</b></small>';
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
        titulo += '<br><small>Jurisdicción:</small> <small><b>'+$('#filtro-jurisdicciones span').text()+'</b></small>';
        break;
    }
  }
  if(titulo){
    $('#titulo').val(titulo);
    $('#titulo_grafica').html(titulo);
  }
});

function graficaMetasCumplidasJurisdicciones(){
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

function graficaMetasCumplidas(){
  $('#filtro-unidades').removeClass('hidden');
  $('#filtro-jurisdicciones').removeClass('hidden');
  $('#panel-btn-filtro').removeClass('hidden');
  var parametros = {grafica:'metas_cumplidas'};
  if($('#unidad').val() != ''){
    parametros.unidad = $('#unidad').val();
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
        slices:{
          0: {color:'#109618'},
          1: {color:'#DC3912'},
          2: {color:'#FF9900'}
        },
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

function tablaResumenPresupuesto(){
  var parametros = {tabla:'resumen_presupuesto'};
  moduloResource.get(null,parametros,{
    _success: function(response){
      $('#area-graficas').empty();
      $('#area-graficas').addClass('hidden');
      $('#area-tablas').empty();
      var html = '<table class="table table-condensed table-hover table-bordered table-striped" style="font-size:small;"><thead><tr><th rowspan="2">Dirección ó Unidad</th><th colspan="8">PRESUPUESTO</th></tr><tr><th>Modificado</th><th>Liberado</th><th>Ministrado</th><th>Comprometido</th><th>Devengado</th><th>Ejercido</th><th>Pagado</th><th>Disponible</th></tr></thead>';
      html += '<tbody>';
      for(var i in response.data){
        var datos = response.data[i];
        var row = '<tr>';
        row += '<td>'+datos.unidadResponsable+'</td>';
        row += '<td>'+parseFloat(datos.presupuestoModificado).format(2)+'</td>';
        row += '<td>'+parseFloat(datos.presupuestoLiberado).format(2)+'</td>';
        row += '<td>'+parseFloat(datos.presupuestoMinistrado).format(2)+'</td>';
        row += '<td>'+parseFloat(datos.presupuestoComprometidoModificado).format(2)+'</td>';
        row += '<td>'+parseFloat(datos.presupuestoDevengadoModificado).format(2)+'</td>';
        row += '<td>'+parseFloat(datos.presupuestoEjercidoModificado).format(2)+'</td>';
        row += '<td>'+parseFloat(datos.presupuestoPagadoModificado).format(2)+'</td>';
        row += '<td>'+parseFloat(datos.disponiblePresupuestarioModificado).format(2)+'</td>';
        row += '</tr>';
        html += row;
      }
      html += '</tbody>';

      html += '<tfoot>';
      html += '<tr>';
      html += '<th>TOTAL</th>';
      html += '<th>'+parseFloat(response.total.presupuestoModificado).format(2)+'</th>';
      html += '<th>'+parseFloat(response.total.presupuestoLiberado).format(2)+'</th>';
      html += '<th>'+parseFloat(response.total.presupuestoMinistrado).format(2)+'</th>';
      html += '<th>'+parseFloat(response.total.presupuestoComprometidoModificado).format(2)+'</th>';
      html += '<th>'+parseFloat(response.total.presupuestoDevengadoModificado).format(2)+'</th>';
      html += '<th>'+parseFloat(response.total.presupuestoEjercidoModificado).format(2)+'</th>';
      html += '<th>'+parseFloat(response.total.presupuestoPagadoModificado).format(2)+'</th>';
      html += '<th>'+parseFloat(response.total.disponiblePresupuestarioModificado).format(2)+'</th>';
      html += '</tr>';
      html += '</tfoot>';

      html += '</table>';
      $('#area-tablas').html(html);
      $('#imagen').val('lol');
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