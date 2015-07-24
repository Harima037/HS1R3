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