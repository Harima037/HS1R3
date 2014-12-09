
/*=====================================

    # Nombre:
        caratula.js

    # Módulo:
        poa/caratula

    # Descripción:
        Para el formulario de captura (Caratula de captura) de un proyecto

=====================================*/
// Declaracion de variables
var proyectoResource = new RESTfulRequests(SERVER_HOST+'/v1/proyectos');
var componenteDatagrid = new Datagrid("#datagridComponentes",proyectoResource);
var actividadDatagrid = new Datagrid('#datagridActividades',proyectoResource);
componenteDatagrid.init();
actividadDatagrid.init();
var modal_componente = '#modalComponente';
var modal_actividad = '#modalActividad';
var grid_componentes = '#datagridComponentes';
var grid_actividades = '#datagridActividades';
var form_caratula = '#form_caratula';
var form_componente = '#form_componente';
var form_actividad = '#form_actividad';

//***********************     Funcionalidad ejecutada al cargar la página    ************************************
$('.control-espejo').each(function(){
	var control_id = $(this).data('espejo-id');
	$(control_id).on('change',function(){
		$('.control-espejo[data-espejo-id="'+control_id+'"]').text($(this).val());
	});
});

if($('#id').val()){
	//load data
	proyectoResource.get($('#id').val(),null,{
        _success: function(response){
            $('#nombretecnico').val(response.data.nombreTecnico);

			$('#unidad_responsable').text(response.data.datos_unidad_responsable.clave);
            $('#finalidad').text(response.data.datos_finalidad.clave.slice(-1));
            $('#funcion').text(response.data.datos_funcion.clave.slice(-1));
            $('#subfuncion').text(response.data.datos_sub_funcion.clave.slice(-1));
            $('#subsubfuncion').text(response.data.datos_sub_sub_funcion.clave.slice(-1));
            $('#programa_sectorial').text(response.data.datos_programa_sectorial.clave );
            $('#programa_presupuestario').text(response.data.datos_programa_presupuestario.clave );
            $('#programa_especial').text(response.data.datos_programa_especial.clave );
            $('#actividad_institucional').text(response.data.datos_actividad_institucional.clave );
            $('#proyecto_estrategico').text(response.data.datos_proyecto_estrategico.clave);
            $('#no_proyecto_estrategico').text(("000" + response.data.numeroProyectoEstrategico).slice(-3));

            $('#unidadresponsable').val(response.data.datos_unidad_responsable.clave);
            $('#unidadresponsable').change();
            $('#funciongasto').val(response.data.datos_sub_sub_funcion.clave);
            $('#funciongasto').change();
            $('#programasectorial').val(response.data.datos_programa_sectorial.clave);
            $('#programasectorial').change();
            $('#programapresupuestario').val(response.data.datos_programa_presupuestario.clave);
            $('#programapresupuestario').change();
            $('#programaespecial').val(response.data.datos_programa_especial.clave);
            $('#programaespecial').change();
            $('#actividadinstitucional').val(response.data.datos_actividad_institucional.clave);
            $('#actividadinstitucional').change();
            $('#proyectoestrategico').val(response.data.datos_proyecto_estrategico.clave);
            $('#proyectoestrategico').change();
            $('#numeroproyectoestrategico').text(("000" + response.data.numeroProyectoEstrategico).slice(-3));

            $('#cobertura').val(response.data.cobertura.id);
            $('#cobertura').change();

            deshabilita_paneles($('#cobertura').val());

            if(response.data.claveMunicipio){
				$('#municipio').val(response.data.claveMunicipio);
            	$('#municipio').change();
            }

            if(response.data.claveRegion){
            	$('#region').val(response.data.claveRegion);
            	$('#region').change();
            }

            $('#tipoaccion').val(response.data.tipo_accion.id);
            $('#tipoaccion').change();

            $('#vinculacionped').val(response.data.objetivo_ped.id);
            $('#vinculacionped').change();

            $('#tipobeneficiario').val(response.data.tipo_beneficiario.id);
            $('#tipobeneficiario').change();
            $('#totalbeneficiarios').text(response.data.totalBeneficiarios);
            $('#totalbeneficiariosf').val(response.data.totalBeneficiariosF);
            $('#totalbeneficiariosm').val(response.data.totalBeneficiariosM);

            var indx;
            var sexo;
            for( indx in response.data.beneficiarios ){
                sexo = response.data.beneficiarios[indx].sexo;
                $('#urbana'+sexo).val(response.data.beneficiarios[indx].urbana);
                $('#rural'+sexo).val(response.data.beneficiarios[indx].rural);
                $('#mestiza'+sexo).val(response.data.beneficiarios[indx].mestiza);
                $('#indigena'+sexo).val(response.data.beneficiarios[indx].indigena);
                $('#inmigrante'+sexo).val(response.data.beneficiarios[indx].inmigrante);
                $('#otros'+sexo).val(response.data.beneficiarios[indx].otros);
                $('#muyalta'+sexo).val(response.data.beneficiarios[indx].muyAlta);
                $('#alta'+sexo).val(response.data.beneficiarios[indx].alta);
                $('#media'+sexo).val(response.data.beneficiarios[indx].media);
                $('#baja'+sexo).val(response.data.beneficiarios[indx].baja);
                $('#muybaja'+sexo).val(response.data.beneficiarios[indx].muyBaja);
            }

            cargar_totales();

            actualizar_tabla_metas('actividad',response.data.jurisdicciones);
            actualizar_tabla_metas('componente',response.data.jurisdicciones);

            $('#tablink-componentes').attr('data-toggle','tab');
			$('#tablink-componentes').parent().removeClass('disabled');

			actualizar_grid_componentes(response.data.componentes);
        }
    });
}else{
	deshabilita_paneles('');
}

$('#denominador-componente').on('keyup',function(){
	ejecutar_formula('componente');
});

$('#denominador-actividad').on('keyup',function(){
	ejecutar_formula('actividad');
});

$('.benef-totales').on('keyup',function(){
	if($(this).attr('id') == 'totalbeneficiariosf'){
		var totalm = parseInt($('#totalbeneficiariosm').val());
		$('#totalbeneficiarios').text(totalm + parseInt($(this).val()));
	}
	if($(this).attr('id') == 'totalbeneficiariosm'){
		var totalf = parseInt($('#totalbeneficiariosf').val());
		$('#totalbeneficiarios').text(totalf + parseInt($(this).val()));
	}
});

$('.sub-total-zona').on('change',function(){
	if($(this).hasClass('fem')){
		sumar_totales('.sub-total-zona.fem','total-zona-f','totalbeneficiariosf','Los subtotales de Zona no concuerdan.');
	}
	if($(this).hasClass('masc')){
		sumar_totales('.sub-total-zona.masc','total-zona-m','totalbeneficiariosm','Los subtotales de Zona no concuerdan.');
	}
});

$('.sub-total-poblacion').on('change',function(){
	if($(this).hasClass('fem')){
		sumar_totales('.sub-total-poblacion.fem','total-poblacion-f','totalbeneficiariosf','Los subtotales de Población no concuerdan.');
	}
	if($(this).hasClass('masc')){
		sumar_totales('.sub-total-poblacion.masc','total-poblacion-m','totalbeneficiariosm','Los subtotales de Población no concuerdan.');
	}
});

$('.sub-total-marginacion').on('change',function(){
	if($(this).hasClass('fem')){
		sumar_totales('.sub-total-marginacion.fem','total-marginacion-f','totalbeneficiariosf','Los subtotales de Marginación no concuerdan.');
	}
	if($(this).hasClass('masc')){
		sumar_totales('.sub-total-marginacion.masc','total-marginacion-m','totalbeneficiariosm','Los subtotales de Marginación no concuerdan.');
	}
});

function cargar_totales(){
	sumar_totales('.sub-total-zona.fem','total-zona-f','totalbeneficiariosf','Los subtotales de Zona no concuerdan.');
	sumar_totales('.sub-total-zona.masc','total-zona-m','totalbeneficiariosm','Los subtotales de Zona no concuerdan.');
	sumar_totales('.sub-total-poblacion.fem','total-poblacion-f','totalbeneficiariosf','Los subtotales de Población no concuerdan.');
	sumar_totales('.sub-total-poblacion.masc','total-poblacion-m','totalbeneficiariosm','Los subtotales de Población no concuerdan.');
	sumar_totales('.sub-total-marginacion.fem','total-marginacion-f','totalbeneficiariosf','Los subtotales de Marginación no concuerdan.');
	sumar_totales('.sub-total-marginacion.masc','total-marginacion-m','totalbeneficiariosm','Los subtotales de Marginación no concuerdan.');
}

/***********************************           Comportamiento de los datagrids          ***********************************/
function editar_componente(e){
	var parametros = {'ver':'componente'};
	proyectoResource.get(e,parametros,{
        _success: function(response){
            var titulo_modal = 'Editar Componente';
            $(modal_componente).find(".modal-title").html(titulo_modal);

 			$('#descripcion-obj-componente').val(response.data.objetivo);
 			$('#verificacion-componente').val(response.data.mediosVerificacion);
 			$('#supuestos-componente').val(response.data.supuestos);
 			$('#descripcion-ind-componente').val(response.data.indicador);
 			$('#numerador-ind-componente').val(response.data.numerador);
 			$('#denominador-ind-componente').val(response.data.denominador);
 			$('#interpretacion-componente').val(response.data.interpretacion);
 			$('#meta-componente').val(response.data.metaIndicador).trigger('change');
 			$('#trim1-componente').val(response.data.numeroTrim1).change();
 			$('#trim2-componente').val(response.data.numeroTrim2).change();
 			$('#trim3-componente').val(response.data.numeroTrim3).change();
 			$('#trim4-componente').val(response.data.numeroTrim4).change();
 			$('#numerador-componente').val(response.data.valorNumerador).trigger('change');
 			$('#denominador-componente').val(response.data.valorDenominador).trigger('change');
 			$('#linea-base-componente').val(response.data.lineaBase);
 			$('#anio-base-componente').val(response.data.anioBase);
 			$('#tipo-obj-componente').val(response.data.tipo);
 			$('#accion-componente').val(response.data.accion);

 			$('#formula-componente').val(response.data.idFormula);
			$('#dimension-componente').val(response.data.idDimensionIndicador);
			$('#frecuencia-componente').val(response.data.idFrecuenciaIndicador);
			$('#tipo-ind-componente').val(response.data.idTipoIndicador);
			$('#unidad-medida-componente').val(response.data.idUnidadMedida);
			$('#entregable-componente').val(response.data.idEntregable);

			$('#formula-componente').change();
			$('#dimension-componente').change();
			$('#frecuencia-componente').change();
			$('#tipo-ind-componente').change();
			$('#unidad-medida-componente').change();
			$('#entregable-componente').change();

            $('#id-componente').val(response.data.id);
    		$('#tablink-componente-actividades').attr('data-toggle','tab');
			$('#tablink-componente-actividades').parent().removeClass('disabled');
			$('#tablink-componente-actividades').tab('show');

			$(form_componente + ' .metas-mes').attr('data-meta-id','');
			
			actualizar_metas('componente',response.data.metas_mes);

			actualizar_grid_actividades(response.data.actividades);

            $(modal_componente).modal('show');
        }
    });
}

function editar_actividad(e){
	var parametros = {'ver':'actividad'};
	proyectoResource.get(e,parametros,{
        _success: function(response){
            var titulo_modal = 'Editar Actividad';
            $(modal_actividad).find(".modal-title").html(titulo_modal);

            $('#id-actividad').val(response.data.id);
 			$('#descripcion-obj-actividad').val(response.data.objetivo);
 			$('#verificacion-actividad').val(response.data.mediosVerificacion);
 			$('#supuestos-actividad').val(response.data.supuestos);
 			$('#descripcion-ind-actividad').val(response.data.indicador);
 			$('#numerador-ind-actividad').val(response.data.numerador);
 			$('#denominador-ind-actividad').val(response.data.denominador);
 			$('#interpretacion-actividad').val(response.data.interpretacion);
 			$('#meta-actividad').val(response.data.metaIndicador).change();
 			$('#trim1-actividad').val(response.data.numeroTrim1).change();
 			$('#trim2-actividad').val(response.data.numeroTrim2).change();
 			$('#trim3-actividad').val(response.data.numeroTrim3).change();
 			$('#trim4-actividad').val(response.data.numeroTrim4).change();
 			$('#numerador-actividad').val(response.data.valorNumerador).change();
 			$('#denominador-actividad').val(response.data.valorDenominador).change();
 			$('#linea-base-actividad').val(response.data.lineaBase);
 			$('#anio-base-actividad').val(response.data.anioBase);

 			$('#formula-actividad').val(response.data.idFormula);
			$('#dimension-actividad').val(response.data.idDimensionIndicador);
			$('#frecuencia-actividad').val(response.data.idFrecuenciaIndicador);
			$('#tipo-ind-actividad').val(response.data.idTipoIndicador);
			$('#unidad-medida-actividad').val(response.data.idUnidadMedida);

			$('#formula-actividad').change();
			$('#dimension-actividad').change();
			$('#frecuencia-actividad').change();
			$('#tipo-ind-actividad').change();
			$('#unidad-medida-actividad').change();

			$(form_actividad + ' .metas-mes').attr('data-meta-id','');

			actualizar_metas('actividad',response.data.metas_mes);

            $(modal_actividad).modal('show');
        }
    });
}

//***********************     Funcionalidad de botones y elementos del formulario ++++++++++++++++++++++++++++++++++++
$('#btn-componente-guardar-salir').on('click',function(){
	guardar_datos_componente(true);
});

$('#btn-componente-guardar').on('click',function(){
	guardar_datos_componente(false);
});

$('#btn-actividad-guardar').on('click',function(){
	Validation.cleanFormErrors(form_actividad);
	var parametros = $(form_actividad).serialize();
	parametros = parametros + '&guardar=actividad&id-componente=' + $('#id-componente').val();

	if($('#id-actividad').val()){
		var cadena_metas = '';
		$(form_actividad + ' .metas-mes').each(function(){
			if($(this).data('meta-id')){
				cadena_metas = cadena_metas + '&mes-actividad-id['+$(this).data('meta-jurisdiccion')+']['+$(this).data('meta-mes')+']='+$(this).data('meta-id');
			}
		});
		parametros = parametros + cadena_metas;
		proyectoResource.put($('#id-actividad').val(),parametros,{
	        _success: function(response){
	            MessageManager.show({data:'Datos de la actividad almacenados con éxito',type:'OK',timer:3});
	            $(modal_actividad).modal('hide');
				actualizar_grid_actividades(response.actividades);
				//actualizar_metas('actividad',response.metas);
	        },
	        _error: function(response){
	            try{
	                var json = $.parseJSON(response.responseText);
	                if(!json.code)
	                    MessageManager.show({code:'S03',data:"Hubo un problema al realizar la transacción, inténtelo de nuevo o contacte con soporte técnico."});
	                else{
	                	json.container = modal_actividad + ' .modal-body';
	                    MessageManager.show(json);
	                }
	                Validation.formValidate(json.data);
	            }catch(e){
	                console.log(e);
	            }                       
	        }
	    });
	}else{
		proyectoResource.post(parametros,{
	        _success: function(response){
	            MessageManager.show({data:'Datos de la actividad almacenados con éxito',type:'OK',timer:3});
	            $(form_actividad + ' #id-actividad').val(response.data.id);
	            $(modal_actividad).modal('hide');
				actualizar_grid_actividades(response.actividades);
				//actualizar_metas('actividad',response.metas);
	        },
	        _error: function(response){
	            try{
	                var json = $.parseJSON(response.responseText);
	                if(!json.code)
	                    MessageManager.show({code:'S03',data:"Hubo un problema al realizar la transacción, inténtelo de nuevo o contacte con soporte técnico."});
	                else{
	                	json.container = modal_actividad + ' .modal-body';
	                    MessageManager.show(json);
	                }
	                Validation.formValidate(json.data);
	            }catch(e){
	                console.log(e);
	            }                       
	        }
	    });
	}
});

$('#btn-proyecto-guardar').on('click',function(){
	Validation.cleanFormErrors(form_caratula);

	if(checar_error_totales()){
		return false;
	}

	var parametros = $(form_caratula).serialize();
	parametros = parametros + '&guardar=proyecto';
	
	if($('#id').val()){
		proyectoResource.put($('#id').val(),parametros,{
	        _success: function(response){
	            MessageManager.show({data:'Datos del proyecto almacenados con éxito',type:'OK',timer:3});
	            if(response.data.jurisdicciones){
	            	actualizar_tabla_metas('actividad',response.data.jurisdicciones);
            		actualizar_tabla_metas('componente',response.data.jurisdicciones);
	            }
	        },
	        _error: function(response){
	            try{
	                var json = $.parseJSON(response.responseText);
	                if(!json.code)
	                    MessageManager.show({code:'S03',data:"Hubo un problema al realizar la transacción, inténtelo de nuevo o contacte con soporte técnico."});
	                else{
	                    MessageManager.show(json);
	                }
	                Validation.formValidate(json.data);
	            }catch(e){
	                console.log(e);
	            }                       
	        }
	    });
	}else{
		proyectoResource.post(parametros,{
	        _success: function(response){
	            MessageManager.show({data:'Datos del proyecto almacenados con éxito',type:'OK',timer:3});
	            $(form_caratula + ' #id').val(response.data.id);
	            $(form_caratula + ' #no_proyecto_estrategico').text(("000" + response.data.numeroProyectoEstrategico).slice(-3));
	            $(form_caratula + ' #numeroproyectoestrategico').text(("000" + response.data.numeroProyectoEstrategico).slice(-3));

	            actualizar_tabla_metas('actividad',response.data.jurisdicciones);
            	actualizar_tabla_metas('componente',response.data.jurisdicciones);

	            $('#tablink-componentes').attr('data-toggle','tab');
				$('#tablink-componentes').parent().removeClass('disabled');
	        },
	        _error: function(response){
	            try{
	                var json = $.parseJSON(response.responseText);
	                if(!json.code)
	                    MessageManager.show({code:'S03',data:"Hubo un problema al realizar la transacción, inténtelo de nuevo o contacte con soporte técnico."});
	                else{
	                    MessageManager.show(json);
	                }
	                Validation.formValidate(json.data);
	            }catch(e){
	                console.log(e);
	            }                       
	        }
	    });
	}
});

$('#btn-proyecto-cancelar').on('click',function(){
	window.location.href = "proyectos";
});

$('#cobertura').on('change',function(){
	deshabilita_paneles($(this).val());
});

$('#formula-componente').on('change',function(){
	ejecutar_formula('componente');
});

$('#formula-actividad').on('change',function(){
	ejecutar_formula('actividad');
});

/** Botones para mostrar los modales de Componente y Actividad **/
$('.btn-agregar-actividad').on('click',function(){
	var actividades = $('#conteo-actividades').text().split('/');

	if(parseInt(actividades[0]) >= parseInt(actividades[1])){
		MessageManager.show({code:'S03',data:"Las actividades para este componente ya estan completas.",timer:2});
	}else{
		$(modal_actividad).find(".modal-title").html("Nueva Actividad");
		$(modal_actividad).modal('show');
	}
});

$('.btn-agregar-componente').on('click',function(){
	var componentes = $('#tablink-componentes > span').text().split('/');

	if(parseInt(componentes[0]) >= parseInt(componentes[1])){
		MessageManager.show({code:'S03',data:"Los componentes para este proyecto ya estan completos.",timer:2});
	}else{
		$('#tablink-componente-actividades').attr('data-toggle','');
		$('#tablink-componente-actividades').parent().addClass('disabled');
		$('#lista-tabs-componente a:first').tab('show');
		$(modal_componente).find(".modal-title").html("Nuevo Componente");
		$(modal_componente).modal('show');
	}
});

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

/*******************************      Funcionalidad de los lementos de la pag     ********************************************/
$(modal_componente + ' #lista-tabs-componente').on('show.bs.tab',function(event){
	var id = event.target.id;
	if(id == 'tablink-componente-actividades'){
		$('.btn-grupo-guardar').hide();
	}else{
		$('.btn-grupo-guardar').show();
	}
});

$(modal_componente).on('hide.bs.modal',function(e){
	reset_modal_form(form_componente);
});

$(modal_actividad).on('hide.bs.modal',function(e){
	reset_modal_form(form_actividad);
});

//***********************     Funciones             +++++++++++++++++++++++++++++++++
function guardar_datos_componente(cerrar){
	Validation.cleanFormErrors(form_componente);
	var parametros = $(form_componente).serialize();
	parametros = parametros + '&guardar=componente';
	parametros = parametros + '&id-proyecto='+$('#id').val();
	parametros = parametros + '&clasificacion='+$('#clasificacionproyecto').val();

	if($('#id-componente').val()){
		var cadena_metas = '';
		$(form_componente + ' .metas-mes').each(function(){
			if($(this).data('meta-id')){
				cadena_metas = cadena_metas + '&mes-componente-id['+$(this).data('meta-jurisdiccion')+']['+$(this).data('meta-mes')+']='+$(this).data('meta-id');
			}
		});
		parametros = parametros + cadena_metas;
		proyectoResource.put($('#id-componente').val(),parametros,{
	        _success: function(response){
	            MessageManager.show({data:'Datos del componente almacenados con éxito',type:'OK',timer:3});
	            if(cerrar){
					$(modal_componente).modal('hide');
				}else{
					$('#tablink-componente-actividades').tab('show');
				}
				actualizar_grid_componentes(response.componentes);
				actualizar_metas('componente',response.metas);
	        },
	        _error: function(response){
	            try{
	                var json = $.parseJSON(response.responseText);
	                if(!json.code)
	                    MessageManager.show({code:'S03',data:"Hubo un problema al realizar la transacción, inténtelo de nuevo o contacte con soporte técnico."});
	                else{
	                    MessageManager.show(json);
	                }
	                Validation.formValidate(json.data);
	            }catch(e){
	                console.log(e);
	            }                       
	        }
	    });
	}else{
		proyectoResource.post(parametros,{
	        _success: function(response){
	            MessageManager.show({data:'Datos del componente almacenados con éxito',type:'OK',timer:3});
	            $(form_componente + ' #id-componente').val(response.data.id);
	            
	            if(cerrar){
					$(modal_componente).modal('hide');
				}else{
					$('#tablink-componente-actividades').attr('data-toggle','tab');
					$('#tablink-componente-actividades').parent().removeClass('disabled');
					$('#tablink-componente-actividades').tab('show');
				}
				actualizar_grid_componentes(response.componentes);
				actualizar_metas('componente',response.metas);
	        },
	        _error: function(response){
	            try{
	                var json = $.parseJSON(response.responseText);
	                if(!json.code)
	                    MessageManager.show({code:'S03',data:"Hubo un problema al realizar la transacción, inténtelo de nuevo o contacte con soporte técnico."});
	                else{
	                    MessageManager.show(json);
	                }
	                Validation.formValidate(json.data);
	            }catch(e){
	                console.log(e);
	            }                       
	        }
	    });
	}
}

/**                            Reescribiendo comportamiento del datagrid                                 **/
$(grid_componentes + " .btn-delete-rows").unbind('click');
$(grid_componentes + " .btn-delete-rows").on('click',function(e){
	e.preventDefault();
	var rows = [];
	var contador= 0;
    
    $(this).parents(".datagrid").find("tbody").find("input[type=checkbox]:checked").each(function () {
		contador++;
        rows.push($(this).parent().parent().data("id"));
	});

	if(contador>0){
		Confirm.show({
				titulo:"Eliminar componente",
				//botones:[], 
				mensaje: "¿Estás seguro que deseas eliminar los componentes seleccionados?",
				//si: 'Actualizar',
				//no: 'No, gracias',
				callback: function(){
					proyectoResource.delete(rows,{'rows': rows, 'eliminar': 'componente', 'id-proyecto': $('#id').val()},{
                        _success: function(response){ 
                        	actualizar_grid_componentes(response.componentes);
                        	MessageManager.show({data:'Componente eliminado con éxito.',timer:3});
                        },
                        _error: function(jqXHR){ 
                        	MessageManager.show(jqXHR.responseJSON);
                        }
        			});
				}
		});
	}else{
		MessageManager.show({data:'No has seleccionado ningún registro.',type:'ADV',timer:3});
	}
});

$(grid_actividades + " .btn-delete-rows").unbind('click');
$(grid_actividades + " .btn-delete-rows").on('click',function(e){
	e.preventDefault();
	var rows = [];
	var contador= 0;
    
    $(this).parents(".datagrid").find("tbody").find("input[type=checkbox]:checked").each(function () {
		contador++;
        rows.push($(this).parent().parent().data("id"));
	});

	if(contador>0){
		Confirm.show({
				titulo:"Eliminar actividad",
				//botones:[], 
				mensaje: "¿Estás seguro que deseas eliminar las actividades seleccionadas?",
				//si: 'Actualizar',
				//no: 'No, gracias',
				callback: function(){
					proyectoResource.delete(rows,{'rows': rows, 'eliminar': 'actividad', 'id-componente': $('#id-componente').val()},{
                        _success: function(response){ 
                        	actualizar_grid_actividades(response.actividades);
                        	MessageManager.show({data:'Actividad eliminada con éxito.',timer:3});
                        },
                        _error: function(jqXHR){ 
                        	MessageManager.show(jqXHR.responseJSON);
                        }
        			});
				}
		});
	}else{
		MessageManager.show({data:'No has seleccionado ningún registro.',type:'ADV',timer:3});
	}
});

/** Actualiza el span correspondiente al select actualizado, para ir construyendo la Clave Presupuestaria **/
function actualiza_clave(id, clave, value){
	if(clave != ''){
		$('#'+id).text(clave);
	}else{
		$('#'+id).text(value);
	}	
}

function deshabilita_paneles(id){
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
}

function actualizar_tabla_metas(identificador,jurisdicciones){
	var tabla_id = '#tabla-'+identificador+'-metas-mes';
	var meses = ['ENE','FEB','MAR','ABR','MAY','JUN','JUL','AGO','SEP','OCT','NOV','DIC'];

	var html = '';
	var indx,idx;
	var llaves = Object.keys(jurisdicciones).sort(); //Se ordenan las llaves
	for(var index in llaves){
		indx = llaves[index];
		html += '<tr>';
		html += '<th>'+jurisdicciones[indx]+'</th>';
		for(idx in meses){
			html += '<td><input id="mes-'+identificador+'-'+indx+'-'+meses[idx]+'" name="mes-'+identificador+'['+indx+']['+meses[idx]+']" type="number" class="form-control input-sm metas-mes" data-meta-mes="'+meses[idx]+'" data-meta-jurisdiccion="'+indx+'" data-meta-identificador="'+identificador+'" data-meta-id=""></td>';
		}
		html += '</tr>';
	}

	$(tabla_id + ' tbody').empty();
	$(tabla_id + ' tbody').html(html);
	actualizar_eventos_metas();
}

function actualizar_metas(identificador,metas){
	var indx;
	for(indx in metas){
		$('#mes-'+identificador+'-'+metas[indx].claveJurisdiccion+'-'+metas[indx].mes).val(metas[indx].meta);
		$('#mes-'+identificador+'-'+metas[indx].claveJurisdiccion+'-'+metas[indx].mes).attr('data-meta-id',metas[indx].id);
	}
}

function actualizar_grid_actividades(datos){
	$(grid_actividades + ' > table > tbody').empty();
	var actividades = [];
	for(indx in datos){
		var actividad = {};

		actividad.id = datos[indx].id;
		actividad.descripcion = datos[indx].objetivo;
		actividad.mediosVerificacion = datos[indx].mediosVerificacion;
		actividad.supuestos = datos[indx].supuestos;
		actividad.creadoPor = datos[indx].usuario.username;
		actividad.creadoAl = datos[indx].creadoAl;

		actividades.push(actividad);
	}

	$('#conteo-actividades').text(' ' + actividades.length + ' / 5 ');

	if(actividades.length == 0){
		$(grid_actividades + ' > table > tbody').append('<tr><td colspan="6" style="text-align:left"><i class="fa fa-info-circle"></i> No hay datos</td></tr>');
	}

	actividadDatagrid.cargarDatos(actividades);
}

function actualizar_grid_componentes(datos){
	$(grid_componentes + ' > table > tbody').empty();
	var componentes = [];
	for(indx in datos){
		var componente = {};

		componente.id = datos[indx].id;
		componente.descripcion = datos[indx].objetivo;
		componente.mediosVerificacion = datos[indx].mediosVerificacion;
		componente.supuestos = datos[indx].supuestos;
		componente.creadoPor = datos[indx].usuario.username;
		componente.creadoAl = datos[indx].creadoAl;

		componentes.push(componente);
	}

	$('#tablink-componentes > span').text(componentes.length + ' / 2');

	if(componentes.length == 0){
		$(grid_componentes + ' > table > tbody').append('<tr><td colspan="6" style="text-align:left"><i class="fa fa-info-circle"></i> No hay datos</td></tr>');
	}

	componenteDatagrid.cargarDatos(componentes);
}

function reset_modal_form(formulario){
    $(formulario).get(0).reset();
    $(formulario + ' input[type="hidden"]').val('');
    $(formulario + ' input[type="hidden"]').change();
    $(formulario + ' .selectpicker').change();
    Validation.cleanFormErrors(formulario);
    if(formulario == form_componente){
    	$('#id-componente').val('');
    	//$(grid_actividades + ' > table > tbody').empty();
    	$('#conteo-actividades').text(' 0 / 5 ');
    	$(grid_actividades + ' > table > tbody').html('<tr><td colspan="6" style="text-align:left"><i class="fa fa-info-circle"></i> No hay datos</td></tr>');
    }
    if(formulario == form_actividad){
    	$('#id-actividad').val('');
    	$('#lista-tabs-actividad a:first').tab('show');
    	$(modal_actividad + ' .alert').remove();
    }
}

//
function checar_error_totales(){
	var errores = false;
	if(parseInt($('#totalbeneficiariosf').val()) != parseInt($('#total-zona-f').text())){
		Validation.printFieldsErrors('total-zona-f','Los subtotales de Zona no concuerdan.');
		errores = true;
	}
	if(parseInt($('#totalbeneficiariosm').val()) != parseInt($('#total-zona-m').text())){
		Validation.printFieldsErrors('total-zona-m','Los subtotales de Zona no concuerdan.');
		errores = true;
	}
	if(parseInt($('#totalbeneficiariosf').val()) != parseInt($('#total-poblacion-f').text())){
		Validation.printFieldsErrors('total-poblacion-f','Los subtotales de Población no concuerdan.');
		errores = true;
	}
	if(parseInt($('#totalbeneficiariosm').val()) != parseInt($('#total-poblacion-m').text())){
		Validation.printFieldsErrors('total-poblacion-m','Los subtotales de Población no concuerdan.');
		errores = true;
	}
	if(parseInt($('#totalbeneficiariosf').val()) != parseInt($('#total-marginacion-f').text())){
		Validation.printFieldsErrors('total-marginacion-f','Los subtotales de Marginación no concuerdan.');
		errores = true;
	}
	if(parseInt($('#totalbeneficiariosm').val()) != parseInt($('#total-marginacion-m').text())){
		Validation.printFieldsErrors('total-marginacion-m','Los subtotales de Marginación no concuerdan.');
		errores = true;
	}
	return errores;
}

//
function sumar_totales(tipo,campo_suma,campo_total,mensaje){
	var sub_total = 0;
	$(tipo).each(function(){
		sub_total += parseInt($(this).val());
	});
	$('#'+campo_suma).text(sub_total);
	if(parseInt($('#'+campo_total).val()) != sub_total){
		Validation.printFieldsErrors(campo_suma,mensaje);
	}else{
		Validation.cleanFieldErrors(campo_suma);
	}
}

function ejecutar_formula(identificador){
	var numerador = parseInt($('#numerador-'+identificador).val()) || 0;
	var denominador = parseInt($('#denominador-'+identificador).val()) || 1;
	var total;
	var id_formula = $('#formula-'+identificador).val();
	switch(id_formula){
		case '1':
			//(Numerador / Denominador) * 100
			total = (numerador/denominador)*100;
			break;
		case '2':
			//((Numerador / Denominador) - 1) * 100
			total = ((numerador/denominador)-1)*100;
			break;
		case '3':
			//(Numerador / Denominador)
			total = (numerador/denominador);
			break;
		case '4':
			//(Numerador - 1,000) / Denominador
			total = (numerador*1000)/denominador;
			break;
		case '5':
			//(Numerador / 10,000) / Denominador
			total = (numerador*10000)/denominador;
			break;
		case '6':
			//(Numerador / 100,000) / Denominador
			total = (numerador*100000)/denominador;
			break;
		case '7':
			//Indicador simple
			total = numerador;
		default:
			total = '';
			break;
	}
	$('#meta-'+identificador).val(total).change();
}

function actualizar_eventos_metas(){
	$('.metas-mes').on('change',function(){
		var meses = {'ENE':1,'FEB':2,'MAR':3,'ABR':4,'MAY':5,'JUN':6,'JUL':7,'AGO':8,'SEP':9,'OCT':10,'NOV':11,'DIC':12};
		var mes = $(this).data('meta-mes');
		var trimestre = Math.ceil(meses[mes]/3);
		var identificador = $(this).data('meta-identificador');

		var valor_1 = 0;
		var valor_2 = 0;
		var valor_3 = 0;

		if(trimestre == 1){
			$('.metas-mes[data-meta-mes="ENE"][data-meta-identificador="'+identificador+'"]').each(function(){
				valor_1 += parseInt($(this).val()) || 0;
			});
			$('.metas-mes[data-meta-mes="FEB"][data-meta-identificador="'+identificador+'"]').each(function(){
				valor_2 += parseInt($(this).val()) || 0;
			});
			$('.metas-mes[data-meta-mes="MAR"][data-meta-identificador="'+identificador+'"]').each(function(){
				valor_3 += parseInt($(this).val()) || 0;
			});
		}else if(trimestre == 2){
			$('.metas-mes[data-meta-mes="ABR"][data-meta-identificador="'+identificador+'"]').each(function(){
				valor_1 += parseInt($(this).val()) || 0;
			});
			$('.metas-mes[data-meta-mes="MAY"][data-meta-identificador="'+identificador+'"]').each(function(){
				valor_2 += parseInt($(this).val()) || 0;
			});
			$('.metas-mes[data-meta-mes="JUN"][data-meta-identificador="'+identificador+'"]').each(function(){
				valor_3 += parseInt($(this).val()) || 0;
			});
		}else if(trimestre == 3){
			$('.metas-mes[data-meta-mes="JUL"][data-meta-identificador="'+identificador+'"]').each(function(){
				valor_1 += parseInt($(this).val()) || 0;
			});
			$('.metas-mes[data-meta-mes="AGO"][data-meta-identificador="'+identificador+'"]').each(function(){
				valor_2 += parseInt($(this).val()) || 0;
			});
			$('.metas-mes[data-meta-mes="SEP"][data-meta-identificador="'+identificador+'"]').each(function(){
				valor_3 += parseInt($(this).val()) || 0;
			});
		}else if(trimestre == 4){
			$('.metas-mes[data-meta-mes="OCT"][data-meta-identificador="'+identificador+'"]').each(function(){
				valor_1 += parseInt($(this).val()) || 0;
			});
			$('.metas-mes[data-meta-mes="NOV"][data-meta-identificador="'+identificador+'"]').each(function(){
				valor_2 += parseInt($(this).val()) || 0;
			});
			$('.metas-mes[data-meta-mes="DIC"][data-meta-identificador="'+identificador+'"]').each(function(){
				valor_3 += parseInt($(this).val()) || 0;
			});
		}

		var suma = valor_1 + valor_2 + valor_3;
		$('#trim'+trimestre+'-'+identificador).val(suma).change();

		var trim1 = parseInt($('#trim1-'+identificador).val()) || 0;
		var trim2 = parseInt($('#trim2-'+identificador).val()) || 0;
		var trim3 = parseInt($('#trim3-'+identificador).val()) || 0;
		var trim4 = parseInt($('#trim4-'+identificador).val()) || 0;

		suma = trim1 + trim2 + trim3 + trim4;

		$('#numerador-'+identificador).val(suma).change();

		if($('#denominador-'+identificador).val()){
			ejecutar_formula(identificador);
		}
	});
}