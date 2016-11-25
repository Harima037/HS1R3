/*=====================================

    # Nombre:
        indicadores-resultados.js

    # Módulo:
        visor/indicadores-resultados

    # Descripción:
        Lista los indicadores de resultados por jurisdicción
=====================================*/

// Inicialización General para casi cualquier módulo
var moduloResource = new RESTfulRequests(SERVER_HOST+'/v1/visor');

cambiarJurisdiccion();

function cambiarJurisdiccion(){
	var parametros = {tabla:'indicadores_resultados_jurisdiccion'};

	if($('#filtro-jurisdiccion').val() != ''){
		parametros.jurisdiccion = $('#filtro-jurisdiccion').val();
	}
	
	moduloResource.get(null,parametros,{
	    _success: function(response){
	    	var tabla_body = '';
			//console.log(response);
			for(var tipo_proyecto in response.data){
				var tipo_de_proyecto = '';
				if(tipo_proyecto == 1){
					tipo_de_proyecto = 'PROYECTOS INSTITUCIONALES';
				}else{
					tipo_de_proyecto = 'PROYECTOS DE INVERSIÓN';
				}

				tabla_body += '<tr style="background-color:#AAAAAA;"><th colspan="5">'+tipo_de_proyecto+'</th></tr>';
				for(var id_proyecto in response.data[tipo_proyecto]){
					var proyecto = response.data[tipo_proyecto][id_proyecto];
					tabla_body += '<tr style="background-color:#DDDDDD;"><th></th><th colspan="4" width="100">'+proyecto.clave+' - '+proyecto.nombre+'</th></tr>';

					for(var i in proyecto.componentes){
						tabla_body += '<tr><td></td><td width="100">'+proyecto.componentes[i].indicador+'</td><td width="100"  class="text-center">'+parseFloat(proyecto.componentes[i].meta).format(2)+'</td><td width="100"  class="text-center">'+parseFloat(proyecto.componentes[i].avance).format(2)+'</td><td width="100"  class="text-center">'+parseFloat(proyecto.componentes[i].porcentaje).format(2)+' %</td></tr>';
					}

					for(var i in proyecto.actividades){
						tabla_body += '<tr><td></td><td width="100">'+proyecto.actividades[i].indicador+'</td><td width="100"  class="text-center">'+parseFloat(proyecto.actividades[i].meta).format(2)+'</td><td width="100"  class="text-center">'+parseFloat(proyecto.actividades[i].avance).format(2)+'</td><td width="100"  class="text-center">'+parseFloat(proyecto.actividades[i].porcentaje).format(2)+' %</td></tr>';
					}
				}
			}
			$('#tbl-lista-proyectos > tbody').html(tabla_body);
		}
	});
}

/*             Extras               */
/**
 * Number.prototype.format(n, x)
 * 
 * @param integer n: length of decimal
 * @param integer x: length of sections
 */
Number.prototype.format = function(n, x) {
    var re = '\\d(?=(\\d{' + (x || 3) + '})+' + (n > 0 ? '\\.' : '$') + ')';
    return this.toFixed(Math.max(0, ~~n)).replace(new RegExp(re, 'g'), '$&,');
};