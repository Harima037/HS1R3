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
      console.log(response);
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
        ['Porcentaje', 'Metas Cumplidas'],
        ['Metas Cumplidas',     total_metas.cumplidas],
        ['Metas No Cumplidas',  (total_metas.bajo + total_metas.alto)]
      ]);

      var options = {
        legend:{position:'top'}
      };

      var chart = new google.visualization.PieChart(document.getElementById('grafica-general'));

      chart.draw(data, options);
    }
  });
}