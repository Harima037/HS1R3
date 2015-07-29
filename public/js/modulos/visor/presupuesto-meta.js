/*=====================================

    # Nombre:
        general.js

    # Módulo:
        visor-gerencial/general

    # Descripción:
        Para grafica de Desempeño General

=====================================*/

// Inicialización General para casi cualquier módulo
var moduloResource = new RESTfulRequests(SERVER_HOST+'/v1/visor');
google.load("visualization", "1", {packages:["corechart"]});
google.setOnLoadCallback(cargar_datos);

function cargar_datos() {
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
            clave,
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

      var chart = new google.visualization.ColumnChart(document.getElementById('grafica-presupuesto-meta'));
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