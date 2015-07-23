/*=====================================

    # Nombre:
        general.js

    # Módulo:
        visor-gerencial/general

    # Descripción:
        Para grafica de Desempeño General

=====================================*/

// Inicialización General para casi cualquier módulo
var moduloResource = new RESTfulRequests(SERVER_HOST+'/v1/visor-gerencial');
google.load("visualization", "1", {packages:["corechart"]});
google.setOnLoadCallback(cargar_datos);

function cargar_datos() {
  moduloResource.get(null,{graficageneral:1},{
    _success: function(response){
      var total_metas = {cumplidas:0,alto:0,bajo:0};
      for(var i in response.componentes){
        var componente = response.componentes[i];
        var meta = parseFloat(componente.meta)||0;
        var avance = parseFloat(componente.avance)||0;

        if(meta > 0){
          var porcentaje = (avance*100) / meta;
        }else{
          var porcentaje = (avance*100);
        }

        ultimo_estatus = 1;
        if(!(meta == 0 && avance == 0)){
          if(porcentaje > 110){
            ultimo_estatus = 3;
          }else if(porcentaje < 90){
            ultimo_estatus = 2;
          }else if(porcentaje > 0 && meta == 0){
            ultimo_estatus = 3;
          }
        }

        if(ultimo_estatus == 1){
          total_metas.cumplidas++;
        }else if(ultimo_estatus == 2){
          total_metas.bajo++;
        }else{
          total_metas.alto++;
        }
      }

      for(var i in response.actividades){
        var actividad = response.actividades[i];
        var meta = parseFloat(actividad.meta)||0;
        var avance = parseFloat(actividad.avance)||0;

        if(meta > 0){
          var porcentaje = (avance*100) / meta;
        }else{
          var porcentaje = (avance*100);
        }

        ultimo_estatus = 1;
        if(!(meta == 0 && avance == 0)){
          if(porcentaje > 110){
            ultimo_estatus = 3;
          }else if(porcentaje < 90){
            ultimo_estatus = 2;
          }else if(porcentaje > 0 && meta == 0){
            ultimo_estatus = 3;
          }
        }

        if(ultimo_estatus == 1){
          total_metas.cumplidas++;
        }else if(ultimo_estatus == 2){
          total_metas.bajo++;
        }else{
          total_metas.alto++;
        }
      }

      var data = google.visualization.arrayToDataTable([
        ['Tipo', 'Indicadores'],
        ['Metas No Cumplidas',(total_metas.bajo + total_metas.alto)],
        ['Metas Cumplidas',total_metas.cumplidas]
      ]);

      var options = { 
        title:'Metas ( '+(total_metas.bajo + total_metas.alto + total_metas.cumplidas).format(2)+' )',
        legend:{position:'bottom'},
        slices:{
          0:{color:'#A94442'},1:{color:'#4B804C'}
        }
      };

      var chart = new google.visualization.PieChart(document.getElementById('grafica-general'));
      chart.draw(data, options);

      var data = google.visualization.arrayToDataTable([
        ['Tipo', 'Indicadores'],
        ['Alto Avance', total_metas.alto],
        ['Bajo Avance', total_metas.bajo]
      ]);
      //var view = new google.visualization.DataView(subData);
      var options = {
        title:'Metas No Cumplidas ( '+(total_metas.bajo + total_metas.alto).format(2)+' )',
        legend:{position:'bottom'},
        slices:{
          0:{color:'#FF4442'},1:{color:'#A94442'}
        }
      };
      var chart = new google.visualization.PieChart(document.getElementById('grafica-avance'));
      chart.draw(data,options);
      /*
      // Set up data for visible chart.
      var primaryData = [
        ['NBA Finals', 22.4],
        ['NFL Super Bowl', 111.3],
        ['MLB World Series', 19.2],
        ['UEFA Champions League Final', 1.9],
        ['NHL Stanley Cup Finals', 6.4],
        ['Wimbledon Men\'s Championship', 2.4]
      ];

      // Set up data for your tooltips.
      var tooltipData = [
        ['Year', 'NBA Finals', 'NFL Super Bowl', 'MLB World Series',
        'UEFA Champions League Final', 'NHL Stanley Cup Finals',
        'Wimbledon Men\'s Championship'],
        ['2005', 12.5, 98.7, 25.3, 0.6, 3.3, 2.8],
        ['2006', 13.0, 90.7, 17.1, 0.8, 2.8, 3.4],
        ['2007', 9.3, 93.0, 15.8, 0.9, 1.8, 3.8],
        ['2008', 14.9, 97.5, 17.1, 1.3, 4.4, 5.1],
        ['2009', 14.3, 98.7, 13.6, 2.1, 4.9, 5.7],
        ['2010', 18.2, 106.5, 19.4, 2.2, 5.2, 2.3],
        ['2011', 17.4, 111.0, 14.3, 4.2, 4.6, 2.7],
        ['2012', 16.8, 111.3, 16.6, 2.0, 2.9, 3.9],
        ['2013', 16.6, 108.7, 12.7, 1.4, 5.8, 2.5],
        ['2014', 15.7, 111.3, 15.0, 1.9, 4.7, 2.4]
      ];

      var primaryOptions = {
        title: 'Highest U.S. Viewership for Most Recent Event (in millions)',
        legend: 'none',
        tooltip: {isHtml: true} // This MUST be set to true for your chart to show.
      };

      var tooltipOptions = {
        title: 'U.S. Viewership Over The Last 10 Years (in millions)',
        legend: 'none'
      };

      // Draws your charts to pull the PNGs for your tooltips.
      //function drawTooltipCharts() {
        var data = new google.visualization.arrayToDataTable(tooltipData);
        var view = new google.visualization.DataView(data);

        // For each row of primary data, draw a chart of its tooltip data.
        for (var i = 0; i < primaryData.length; i++) {

          // Set the view for each event's data
          view.setColumns([0, i + 1]);

          var hiddenDiv = document.getElementById('hidden_div');
          var tooltipChart = new google.visualization.LineChart(hiddenDiv);

          google.visualization.events.addListener(tooltipChart, 'ready', function() {

            // Get the PNG of the chart and set is as the src of an img tag.
            var tooltipImg = '<img src="' + tooltipChart.getImageURI() + '">';

            // Add the new tooltip image to your data rows.
            primaryData[i][2] = tooltipImg;

          });
          tooltipChart.draw(view, tooltipOptions);
        }
        //drawPrimaryChart();
      //}

      //function drawPrimaryChart() {

        var data = new google.visualization.DataTable();
        data.addColumn('string', 'Event');
        data.addColumn('number', 'Highest Recent Viewership');

        // Add a new column for your tooltips.
        data.addColumn({
          type: 'string',
          label: 'Tooltip Chart',
          role: 'tooltip',
          'p': {'html': true}
        });

        // Add your data (with the newly added tooltipImg).
        data.addRows(primaryData);

        var visibleDiv = document.getElementById('visible_div');
        var primaryChart = new google.visualization.ColumnChart(visibleDiv);
        primaryChart.draw(data, primaryOptions);
        */
      //}
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