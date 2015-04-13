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

var caratulaProyecto = {};

(function(context){

var formulario = 'form_caratula';
var id_form = '#form_caratula';

context.llenar_datos = function(datos){
	//
	$('#lbl-lider-proyecto').text(datos.lider_proyecto.nombre);
	$('#lbl-jefe-inmediato').text(datos.jefe_inmediato.nombre);
	$('#lbl-jefe-planeacion').text(datos.jefe_planeacion.nombre);
	$('#lbl-coordinador-grupo').text(datos.coordinador_grupo_estrategico.nombre);

    $('#nombretecnico').val(datos.nombreTecnico);
    $('#ejercicio').val(datos.ejercicio);
    $('#tipoaccion').val(datos.idTipoAccion);
    $('#vinculacionped').val(datos.idObjetivoPED);

    $('#unidadresponsable').val(datos.unidadResponsable);
    var funcion_gasto = datos.finalidad + '.' + datos.funcion + '.' + datos.subFuncion + '.' + datos.subSubFuncion;
    $('#funciongasto').val(funcion_gasto);
    $('#programasectorial').val(datos.programaSectorial);
    $('#programapresupuestario').val(datos.programaPresupuestario);
    $('#programaespecial').val(datos.programaEspecial);
    $('#actividadinstitucional').val(datos.actividadInstitucional);
    $('#proyectoestrategico').val(datos.proyectoEstrategico);
    $('#no_proyecto_estrategico').text(("000" + datos.numeroProyectoEstrategico).slice(-3));

    if($('input#numeroproyectoestrategico').length){
    	$('#numeroproyectoestrategico').val(datos.numeroProyectoEstrategico);
    }else{
    	$('#numeroproyectoestrategico').text(("000" + datos.numeroProyectoEstrategico).slice(-3));
    }

    $('#cobertura').val(datos.idCobertura);
    
    if(datos.claveMunicipio){
		$('#municipio').val(datos.claveMunicipio);
    }

    if(datos.claveRegion){
    	$('#region').val(datos.claveRegion);
    }
    
	$('.chosen-one').trigger('chosen:updated');
    $('.chosen-one').chosen().change();
}

context.init = function(){
	/***********************************************************************************************
					Inicialización de comportamiento de los elementos del formulario
	************************************************************************************************/
	$('#cobertura').on('change',function(){
		var id = $(this).val();
		if(id == 1){
			$('#select-estado-panel').show();
			$('#select-municipio-panel').hide();
			$('#select-region-panel').hide();
		}else if(id == 2){
			$('#select-estado-panel').hide();
			$('#select-municipio-panel').show();
			$('#select-region-panel').hide();
		}else if(id == 3){
			$('#select-estado-panel').hide();
			$('#select-municipio-panel').hide();
			$('#select-region-panel').show();
		}else{
			$('#select-estado-panel').hide();
			$('#select-municipio-panel').hide();
			$('#select-region-panel').hide();
		}
	});
	$('#cobertura').chosen().change();

	/** Selects para actualizar la Clave Presupuestaria **/
	$('#unidadresponsable').on('change',function(){
		actualiza_clave('unidad_responsable',$(this).val(),'--');
	});
	$('#funciongasto').on('change',function(){
		var funcion_gasto = ['','','',''];

		if($(this).val() != ''){
			 funcion_gasto = $(this).val().split('.');
		}
		
		actualiza_clave('finalidad', funcion_gasto[0],'-');
		actualiza_clave('funcion', funcion_gasto[1],'-');
		actualiza_clave('subfuncion', funcion_gasto[2],'-');
		actualiza_clave('subsubfuncion', funcion_gasto[3],'-');
	});
	$('#programasectorial').on('change',function(){
		actualiza_clave('programa_sectorial',$(this).val(),'-');
	});
	$('#programapresupuestario').on('change',function(){
		actualiza_clave('programa_presupuestario',$(this).val(),'---');
	});
	$('#programaespecial').on('change',function(){
		actualiza_clave('programa_especial',$(this).val(),'---');
	});
	$('#actividadinstitucional').on('change',function(){
		actualiza_clave('actividad_institucional',$(this).val(),'---');
	});
	$('#proyectoestrategico').on('change',function(){
		actualiza_clave('proyecto_estrategico',$(this).val(),'-');
	});
}

context.limpiar_errores = function(){
	Validation.cleanFormErrors(formulario);
}

/***********************************************************************************************
					Funciones Privadas
************************************************************************************************/
function sumar_valores(identificador,resultado){
	var sumatoria = 0;
	$(identificador).each(function(){
		sumatoria += parseFloat($(this).val()) || 0;
	});
	if($(resultado).is('input')){
		$(resultado).val(sumatoria.format()).change();
	}else{
		$(resultado).text(sumatoria.format());
	}
}

function actualiza_clave(id, clave, value){
	if(clave != ''){
		$('#'+id).text(clave);
	}else{
		$('#'+id).text(value);
	}	
}

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

})(caratulaProyecto);