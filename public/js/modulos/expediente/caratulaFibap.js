/*=====================================

    # Nombre:
        caratulaProyecto.js

    # Módulo:
        expediente/caratula
        expediente/caratula-inversion

    # Descripción:
        Comportamiendo y funciones del formulario de la caratula de captura para los datos generales de un proyecto 
        tanto de inversión como institucional

=====================================*/

var caratulaFibap = {};

(function(context){

var formulario = 'form-fibap-datos';
var id_form = '#form-fibap-datos';

context.llenar_datos = function(datos){
	$('#organismo-publico').val(datos.organismoPublico);
	$('#sector').val(datos.sector);
	$('#subcomite').val(datos.subcomite);
	$('#grupo-trabajo').val(datos.grupoTrabajo);
	$('#justificacion-proyecto').val(datos.justificacionProyecto);
	$('#descripcion-proyecto').val(datos.descripcionProyecto);
	$('#objetivo-proyecto').val(datos.objetivoProyecto);
	$('#alineacion-especifica').val(datos.alineacionEspecifica);
	$('#alineacion-general').val(datos.alineacionGeneral);
	$('#resultados-obtenidos').val(datos.resultadosObtenidos);
	$('#resultados-esperados').val(datos.resultadosEsperados);
	$('#presupuesto-requerido').val(datos.presupuestoRequerido);
	$('#presupuesto-requerido').change();
	$('#periodo-ejecucion-inicio').val(datos.periodoEjecucionInicio);
	$('#periodo-ejecucion-final').val(datos.periodoEjecucionFinal);
	
	for(var indx in datos.documentos){
    	$('#documento_'+datos.documentos[indx].id).prop('checked',true);
    }

    $('#id-fibap').val(datos.id);
}

})(caratulaFibap);