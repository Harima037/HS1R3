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
  moduloResource.get(null,{graficapresupuestometa:1},{
    _success: function(response){

      var total_metas = {cumplidas:0,noCumplidas:0};
      for(var i in response.data.componentes){
        var componente = response.data.componentes[i];
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
        }else{
          total_metas.noCumplidas++;
        }
      }

      for(var i in response.data.actividades){
        var actividad = response.data.actividades[i];
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
        }else{
          total_metas.noCumplidas++;
        }
      }

      var porcentaje_metas = (total_metas.cumplidas*100)/(total_metas.cumplidas+total_metas.noCumplidas);

      var presupuesto = response.data.presupuesto;
      var porcentaje_presupuesto = (parseFloat(presupuesto.presupuestoEjercido)*100)/parseFloat(presupuesto.presupuestoModificado);

      var porcentajes = [['Elemento','Porcentaje',{role:'style'},{role:'annotation'},{role:'tooltip',p:{html:true}}]];

      porcentajes.push([
          'Metas',porcentaje_metas,'#AADDAA',(porcentaje_metas.format(2)) + '%',
          '<table border="0" cellpadding="0" cellspacing="0"><tr><th class="text-center" style="white-space:nowrap;"><big>Metas '+(porcentaje_metas.format(2))+' %</big></th></tr><tr><td style="white-space:nowrap;">Metas Programadas: </td></tr><tr><th class="text-center" style="white-space:nowrap;font-weight:bold;">'+((total_metas.cumplidas+total_metas.noCumplidas).format(2))+'</th></tr><tr><td style="white-space:nowrap;">Metas Cumplidas: </td></tr><tr><th class="text-center" style="white-space:nowrap;font-weight:bold;">'+(total_metas.cumplidas.format(2))+'</th></tr></table>'
      ]);

      porcentajes.push([
        'Presupuesto',porcentaje_presupuesto,'#AAAAFF',(porcentaje_presupuesto.format(2)) + '%',
        '<table border="0" cellpadding="0" cellspacing="0"><tr><th class="text-center" style="white-space:nowrap;"><big>Presupuesto '+(porcentaje_presupuesto.format(2))+' %</big></th></tr><tr><td style="white-space:nowrap;">Presupuesto Modificado: </td></tr><tr><th class="text-center" style="white-space:nowrap;font-weight:bold;">$ '+(parseFloat(presupuesto.presupuestoModificado).format(2))+'</th></tr><tr><td style="white-space:nowrap;">Presupuesto Ejercido: </td></tr><tr><th class="text-center" style="white-space:nowrap;font-weight:bold;">$ '+(parseFloat(presupuesto.presupuestoEjercido).format(2))+'</th></tr></table>'
      ]);

      var data = google.visualization.arrayToDataTable(porcentajes);

      var options = {
        title: 'Presupuesto Ejercivo VS Cumplimiento de Metas',
        legend: { position:'none' },
        vAxis: {
          title: 'Porcentaje',
          maxValue:100,
          minValue:0
        },
        annotations: { alwaysOutside:true },
        tooltip: {isHtml: true}
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