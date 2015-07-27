/*=====================================

    # Nombre:
        general.js

    # Módulo:
        visor-gerencial/general

    # Descripción:
        Para grafica de Desempeño General

=====================================*/

// Inicialización General para casi cualquier módulo
var moduloResource = new RESTfulRequests(SERVER_HOST+'/v1/visor-directivo');
google.load("visualization", "1", {packages:["corechart"]});
google.setOnLoadCallback(cargar_datos);

function cargar_datos() {
  moduloResource.get(null,{graficapresupuesto:1},{
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

      var formatter = new google.visualization.NumberFormat(
        {pattern: '$ #,###,###,###.##'}
      );
      formatter.format(data, 1);

      var options = { 
        title:'Presupuesto ( Modificado ) : $ '+(parseFloat(response.total)||0).format(2),
        legend:{position:'right',alignment:'center'}
      };

      var chart = new google.visualization.PieChart(document.getElementById('grafica-presupuesto'));
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