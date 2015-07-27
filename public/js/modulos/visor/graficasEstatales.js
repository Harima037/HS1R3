/*=====================================

    # Nombre:
        graficasEstatales.js

    # Módulo:
        visor/estatal

    # Descripción:
        Objeto encargado de crear las graficas para el modulo de graficas Estatales

=====================================*/

var graficasEstatales = {};

(function(context){

var recurso; //recurso asignado al objeto
var graficas_creadas; //lista de graficas creadas, que se pueden volver a pintar
var datos_asignados;

context.init = function(resource){
	recurso = resource;
	graficas_creadas = {};
	datos_asignados = {};
};

context.datosProyectosDireccion = function(datos){
	var elementos = [['Fuente Financiamiento', 'Presupuesto Modificado']];
	for(var i in datos){
		elementos.push(
			[
				datos[i].clave + ' ' + datos[i].unidad,
				+(parseFloat(datos[i].noProyectos) || 0)
			]
		);
	}
}

function generarGraficas(){
	if(datos_asignados['grafica']){ //Preguntar por cada elemento creado
		//Creacion de data, options y chart => function
		//Asignación a graficas_creadas
	}
}

function mostrarGrafica(grafica) {
    if(grafica){
        if(graficas_creadas[grafica]){
            var chart = graficas_creadas[grafica].chart;
            var data = graficas_creadas[grafica].data;
            var options = graficas_creadas[grafica].options;
            chart.draw(data, options);
        }
    }else{
        for(var i in graficas_creadas){
            var chart = graficas_creadas[i].chart;
            var data = graficas_creadas[i].data;
            var options = graficas_creadas[i].options;
            chart.draw(data, options);
        }
    }
}

context.llenar_datagrid = function(datos){
	llenar_datagrid_beneficiarios(datos);
};

})(graficasEstatales);