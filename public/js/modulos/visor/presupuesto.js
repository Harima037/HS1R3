/*=====================================

    # Nombre:
        presupuesto.js

    # Módulo:
        visor/presupuesto

    # Descripción:
        Para grafica de Presupuesto por Fuente de Financiamiento

=====================================*/

// Inicialización General para casi cualquier módulo
var moduloResource = new RESTfulRequests(SERVER_HOST+'/v1/visor');
google.load("visualization", "1", {packages:["corechart"]});
google.setOnLoadCallback(cargar_datos);

function cargar_datos() {
  var parametros = {grafica:'presupuesto_fuente'};
  moduloResource.get(null,parametros,{
    _success: function(response){
      $('#titulo').val('Presupuesto por Fuente de Financiamiento');
      $('#titulo_grafica').text('Presupuesto por Fuente de Financiamiento');

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
        title:'Total Presupuesto Autorizado : $ '+(parseFloat(response.total)||0).format(2),
        legend:{position:'right',alignment:'center'},
        chartArea:{ width:'100%',height:'100%',left:0,right:0,top:60,bottom:0 }
      };

      var chart = new google.visualization.PieChart(document.getElementById('grafica-presupuesto'));
      google.visualization.events.addListener(chart, 'ready', function () {
        $('#imagen').val(chart.getImageURI());
      });
      chart.draw(data, options);
    }
  });
  var parametros = {tabla:'resumen_presupuesto'};
  moduloResource.get(null,parametros,{
    _success: function(response){
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
      $('#tabla-presupuesto').html(html);
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