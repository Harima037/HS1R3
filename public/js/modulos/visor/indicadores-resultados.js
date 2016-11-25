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

				tabla_body += '<tr><th colspan="5">'+tipo_de_proyecto+'</th></tr>';
				for(var id_proyecto in response.data[tipo_proyecto]){
					var proyecto = response.data[tipo_proyecto][id_proyecto];
					tabla_body += '<tr><th></th><th width="100">'+proyecto.clave+' - '+proyecto.nombre+'</th><th width="90"></th><th width="90"></th><th width="90"></th></tr>';

					for(var i in proyecto.componentes){
						tabla_body += '<tr><td></td><td width="100">'+proyecto.componentes[i].indicador+'</td><td width="90">'+proyecto.componentes[i].meta+'</td><td width="90">'+proyecto.componentes[i].avance+'</td><td width="90">'+proyecto.componentes[i].porcentaje+' %</td></tr>';
					}

					for(var i in proyecto.actividades){
						tabla_body += '<tr><td></td><td width="100">'+proyecto.actividades[i].indicador+'</td><td width="90">'+proyecto.actividades[i].meta+'</td><td width="90">'+proyecto.actividades[i].avance+'</td><td width="90">'+proyecto.actividades[i].porcentaje+' %</td></tr>';
					}
				}
			}
			$('#tbl-lista-proyectos > tbody').html(tabla_body);
		}
	});
}