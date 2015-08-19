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
google.setOnLoadCallback(cargar_datos);

function cargar_datos() {
  var parametros = {grafica:'metas_cumplidas'};
  moduloResource.get(null,parametros,{
    _success: function(response){
      var data = google.visualization.arrayToDataTable([
        ['Tipo', 'Indicadores'],
        ['Metas Cumplidas',response.data.cumplidas],
        ['Metas No Cumplidas',(response.data.bajoAvance + response.data.altoAvance)]
      ]);

      var options = { 
        title:'Metas ( '+response.total.format(2)+' )',
        legend:{position:'bottom'},
        chartArea:{ width:'80%',height:'80%',left:10,right:0,top:60,bottom:0 }
      };

      var chart = new google.visualization.PieChart(document.getElementById('grafica-general'));
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
        chartArea:{ width:'80%',height:'80%',left:10,right:0,top:60,bottom:0 }
      };

      var chart = new google.visualization.PieChart(document.getElementById('grafica-avance'));
      google.visualization.events.addListener(chart, 'ready', function () {
        $('#imagen2').val(chart.getImageURI());
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