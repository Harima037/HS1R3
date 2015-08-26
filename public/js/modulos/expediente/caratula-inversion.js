/*=====================================

    # Nombre:
        caratula.js

    # Módulo:
        expediente/caratula

    # Descripción:
        Para el formulario de captura (Caratula de captura) de un proyecto

=====================================*/
// Declaracion de variables
var proyectoResource = new RESTfulRequests(SERVER_HOST+'/v1/inversion');

var comentarios = {
	componentes: {},
	actividades: {},
	desgloses: {}
};

var form_caratula = '#form_caratula';
var form_fibap = '#form-fibap-datos';
var form_fibap_antecentes = '#form-fibap-antecedentes';
var form_antecedente = '#form-antecedente';
var form_beneficiario = '#form_beneficiario';
var form_componente = '#form_componente';
var form_actividad = '#form_actividad';
var form_presupuesto = '#form-presupuesto';

var modal_antecedente = '#modal-antecedente';
var modal_presupuesto = '#modal-presupuesto';
var modal_accion  = '#modal-accion';
var modal_beneficiario = '#modalBeneficiario';
var modal_componente = '#modal-componente';
var modal_actividad = '#modal-actividad';

$('.chosen-one').chosen({width:'100%'});

window.onload = function () { 
	$('#mensaje-espera').addClass('hidden');
	$('#panel-principal-formulario').removeClass('hidden');
};

caratulaProyecto.init();

if($('#id').val()){
	var parametros = {'mostrar':'editar-proyecto'};
	
	proyectoResource.get($('#id').val(),parametros,{
        _success: function(response){

        	caratulaProyecto.llenar_datos(response.data);
        	caratulaBeneficiario.init(response.data.id,proyectoResource);

	       	cambiar_icono_tabs('#tab-link-caratula','fa-check-square-o');

            $('#tab-link-datos-fibap').attr('data-toggle','tab');
			$('#tab-link-datos-fibap').parent().removeClass('disabled');
			$('#tab-link-caratula-beneficiarios').attr('data-toggle','tab');
			$('#tab-link-caratula-beneficiarios').parent().removeClass('disabled');
			$('#tablink-fuentes-financiamiento').attr('data-toggle','tab');
			$('#tablink-fuentes-financiamiento').parent().removeClass('disabled');

			/*if(response.extras.municipios){
        		fibapAcciones.cargar_municipios(response.extras.municipios);
        	}*/
        	
        	if(response.extras.jurisdicciones){
        		fibapAcciones.cargar_jurisdicciones(response.extras.jurisdicciones);
        	}

			if(response.data.beneficiarios.length){
				caratulaBeneficiario.llenar_datagrid(response.data.beneficiarios);
				fibapAcciones.actualizar_lista_beneficiarios(response.data.beneficiarios);
				cambiar_icono_tabs('#tab-link-caratula-beneficiarios','fa-check-square-o');
			}

			fuenteFinanciamiento.init(proyectoResource,$('#id').val());
			if(response.data.fuentes_financiamiento.length){
				fuenteFinanciamiento.llenar_datagrid(response.data.fuentes_financiamiento);
				cambiar_icono_tabs('#tablink-fuentes-financiamiento','fa-check-square-o');
			}

			if(response.data.idEstatusProyecto != 1 && response.data.idEstatusProyecto != 3){
				bloquear_controles();
			}else if(response.data.idEstatusProyecto == 3){
				mostrar_comentarios(response.data.comentarios);
			}

			if(response.extras.responsables){
				fibapAcciones.init_responsables();
				fibapAcciones.llenar_select_responsables(response.extras.responsables);
				fibapAcciones.mostrar_datos_informacion(response.data);
			}

			if(response.data.fibap){
				fibapAcciones.init(response.data.fibap.id,proyectoResource);
				fibapAcciones.mostrarComentarios(comentarios);

				caratulaFibap.llenar_datos(response.data.fibap);
				cambiar_icono_tabs('#tab-link-datos-fibap','fa-check-square-o');

				if(response.data.fibap.resultadosObtenidos || response.data.fibap.antecedentes_financieros.length){
					cambiar_icono_tabs('#tab-link-antecedentes-fibap','fa-check-square-o');
				}

				fibapAntecedentes.init(response.data.fibap.id,proyectoResource);
				fibapAcciones.habilitar_meses(response.data.fibap.periodoEjecucionInicio, response.data.fibap.periodoEjecucionFinal);
				fibapAcciones.actualizar_total_presupuesto(response.data.fibap.presupuestoRequerido);
				fibapAcciones.actualizar_metas_mes(response.extras.jurisdicciones);

				if(response.data.fibap.antecedentes_financieros.length){
					fibapAntecedentes.llenar_datagrid(response.data.fibap.antecedentes_financieros);
				}

				if(response.data.fibap.distribucion_presupuesto_agrupado){
					fibapAcciones.llenar_tabla_distribucion_general(response.data.fibap.distribucion_presupuesto_agrupado);
				}

				if(response.data.fibap.acciones){
					cambiar_icono_tabs('#tab-link-acciones-fibap','fa-check-square-o');
					fibapAcciones.llenar_datagrid(response.data.fibap.acciones);
				}

				$('#tab-link-antecedentes-fibap').attr('data-toggle','tab');
				$('#tab-link-antecedentes-fibap').parent().removeClass('disabled');

				$('#tab-link-acciones-fibap').attr('data-toggle','tab');
				$('#tab-link-acciones-fibap').parent().removeClass('disabled');
			}
        }
    });
}

$('#btn-proyecto-cancelar').on('click',function(){
	window.location.href = SERVER_HOST+'/expediente/inversion';
});

/***********************************************************************************************
							Funciones de Edición de datos de DataGrid
************************************************************************************************/
function editar_antecedente(e){
	var parametros = {'mostrar':'editar-antecedente'};
	proyectoResource.get(e,parametros,{
        _success: function(response){
            fibapAntecedentes.mostrar_datos(response.data);
        }
    });
}

function editar_beneficiario(e){
	var parametros = {'mostrar':'editar-beneficiario','id-proyecto':$('#id').val()};
	proyectoResource.get(e,parametros,{
		_success: function(response){
			caratulaBeneficiario.mostrar_datos(response.data);
		}
	});
}

function editar_accion(e){
	var parametros = {'mostrar':'editar-componente'};
	proyectoResource.get(e,parametros,{
		_success: function(response){
			fibapAcciones.mostrar_datos(response.data);
		}
	});
}

function editar_actividad(e){
	var parametros = {'mostrar':'editar-actividad'};
	proyectoResource.get(e,parametros,{
		_success: function(response){
			fibapAcciones.mostrar_datos_actividad(response.data);
		}
	});
}

function editar_presupuesto(e){
	if($(modal_presupuesto).find(".modal-title").html() == "Editar Presupuesto"){
		return;
	}
	$(modal_presupuesto).find(".modal-title").html("Editar Presupuesto");
	
	var parametros = {'mostrar':'editar-presupuesto','id-proyecto':$('#id').val()};
	proyectoResource.get(e,parametros,{
		_success: function(response){
			fibapAcciones.mostrar_datos_presupuesto(response.data);
		}
	});
}

function editar_fuente_financiamiento(e){
	fuenteFinanciamiento.editar_fuente(e);
}
/***********************************************************************************************
								Acciones de Guardado
************************************************************************************************/
$('#btn-enviar-proyecto').on('click',function(){
	Validation.cleanFormErrors(form_caratula);
	parametros = 'guardar=validar-proyecto';

	if($('#id').val()){
		Confirm.show({
			titulo:"Enviar proyecto a Validación",
			mensaje: "¿Estás seguro que deseas enviar este proyecto para su validación? <br><b>IMPORTANTE:</b> Mientras el proyecto este en validación no se podra editar ningún elemento del mismo.",
			si: '<span class="fa fa-send"></span> Enviar',
			no: 'Cancelar',
			callback: function(){
				proyectoResource.put($('#id').val(),parametros,{
			        _success: function(response){
			            MessageManager.show({data:response.data,type:'OK',timer:6});
			            bloquear_controles();
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
	}
});

$('#btn-componente-guardar-salir').on('click',function(){
	guardar_datos_componente(true,'componente');
});

$('#btn-componente-guardar').on('click',function(){
	guardar_datos_componente(false,'componente');
});

$('#btn-actividad-guardar').on('click',function(){
	guardar_datos_componente(true,'actividad');
});

function guardar_datos_componente(cerrar,selector){
	if(selector == 'actividad'){
		var formulario = form_actividad;
		var modal = modal_actividad;
	}else{
		var formulario = form_componente;
		var modal = modal_componente;
	}
	
	Validation.cleanFormErrors(formulario);
	var parametros = $(formulario).serialize();
	parametros += '&guardar='+selector;
	parametros += '&id-fibap=' + $('#id-fibap').val();
	parametros += '&id-proyecto=' + $('#id').val();
	if(selector == 'actividad'){
		parametros += '&id-componente=' + $('#id-componente').val();
	}
	//parametros = parametros + '&clasificacion='+$('#clasificacionproyecto').val();

	if($('#id-'+selector).val()){
		var cadena_metas = '';
		$(formulario + ' .metas-mes').each(function(){
			if($(this).attr('data-meta-id')){
				cadena_metas = cadena_metas + '&mes-'+selector+'-id['+$(this).attr('data-meta-jurisdiccion')+']['+$(this).attr('data-meta-mes')+']='+$(this).attr('data-meta-id');
			}
		});
		parametros += cadena_metas;

		if(selector == 'componente'){
			var lista_origenes_id = '';
			$('.accion-origen-financiamiento').each(function(){
				if($(this).attr('data-captura-id')){
					lista_origenes_id += '&origen-captura-id[' + $(this).attr('data-origen-id') + ']=' + $(this).attr('data-captura-id');
				}
			});
			parametros += lista_origenes_id;
		}

		proyectoResource.put($('#id-'+selector).val(),parametros,{
	        _success: function(response){
	            MessageManager.show({data:'Datos de la acción almacenados con éxito',type:'OK',timer:3});
	            if(selector == 'actividad'){
	            	fibapAcciones.llenar_datagrid_actividades(response.extras.actividades);
	            }else{
	            	fibapAcciones.llenar_datagrid(response.acciones);
		            if(response.distribucion_total){
						fibapAcciones.llenar_tabla_distribucion_general(response.distribucion_total);
					}
	            }

	            if(cerrar){
					$(modal).modal('hide');
				}else{
					if(selector == 'componente'){
						$('#tablink-componente-actividades').tab('show');
					}
					
					if(response.metas){
						fibapAcciones.actualizar_metas_ids(selector,response.metas);
					}
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
	            MessageManager.show({data:'Datos de la acción almacenados con éxito',type:'OK',timer:3});
	            if(selector == 'actividad'){
	            	fibapAcciones.llenar_datagrid_actividades(response.extras.actividades);
	            }else{
	            	fibapAcciones.llenar_datagrid(response.acciones);
	            }
	            
	            if(cerrar){
					$(modal).modal('hide');
				}else{
					if(selector == 'actividad'){
						$(formulario + ' #id-actividad').val(response.data.id);
					}else{
						$('#lnk-descarga-archivo-presupuesto').attr('href',SERVER_HOST+'/expediente/descargar-archivo-municipios/'+$('#id').val()+'?tipo-carga=presupuesto&id-accion='+response.data.id);
    					$('#lnk-descarga-archivo-presupuesto').removeClass('disabled');
						$(formulario + ' #id-componente').val(response.data.idComponente);
						$(formulario + ' #id-accion').val(response.data.id);
						$('#tablink-componente-actividades').attr('data-toggle','tab');
						$('#tablink-componente-actividades').parent().removeClass('disabled');
						$('#tablink-componente-actividades').tab('show');
					}

					if(response.metas){
						fibapAcciones.actualizar_metas_ids(selector,response.metas);
					}
				}
				cambiar_icono_tabs('#tab-link-acciones-fibap','fa-check-square-o');
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

$('#btn-fuente-informacion-guardar').on('click',function(){
	Validation.cleanFormErrors('#form_fuente_informacion');

	var parametros = $('#form_fuente_informacion').serialize();
	parametros = parametros + '&guardar=fuenteinformacion';

	if($('#id').val()){
		proyectoResource.put($('#id').val(),parametros,{
	        _success: function(response){
	            MessageManager.show({data:'Datos almacenados con éxito',type:'OK',timer:3});
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

$('#btn-proyecto-guardar').on('click',function(){
	caratulaProyecto.limpiar_errores();

	var parametros = $(form_caratula).serialize();
	parametros = parametros + '&guardar=proyecto';
	
	if($('#id').val()){
		proyectoResource.put($('#id').val(),parametros,{
	        _success: function(response){
	            MessageManager.show({data:'Datos del proyecto almacenados con éxito',type:'OK',timer:3});
	            if(response.extras){
	            	if(response.extras.jurisdicciones){
	            		fibapAcciones.actualizar_metas_mes(response.extras.jurisdicciones);
	            		fibapAcciones.cargar_jurisdicciones(response.extras.jurisdicciones);
	            	}

	            	if(response.data.fibap){
	            		if(response.data.fibap.distribucion_presupuesto_agrupado){
							fibapAcciones.llenar_tabla_distribucion_general(response.data.fibap.distribucion_presupuesto_agrupado);
						}
						if(response.data.fibap.acciones){
							cambiar_icono_tabs('#tab-link-acciones-fibap','fa-check-square-o');
							fibapAcciones.llenar_datagrid(response.data.fibap.acciones);
						}
	            	}

	            	if(response.extras.responsables){
	            		fibapAcciones.llenar_select_responsables(response.extras.responsables);
	            	}
	            }
	            $('#lbl-lider-proyecto').text(response.data.liderProyecto);
	            var no_proyecto = ("000" + (response.data.numeroProyectoEstrategico || 0)).slice(-3);
	            $(form_caratula + ' #no_proyecto_estrategico').text(no_proyecto);
	            if($('input#numeroproyectoestrategico').length){
	            	$(form_caratula + ' #numeroproyectoestrategico').val(response.data.numeroProyectoEstrategico);
	            }else{
	            	$(form_caratula + ' #numeroproyectoestrategico').text(no_proyecto);
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
	            
	            $('#lbl-lider-proyecto').text(response.data.liderProyecto);
	            var no_proyecto = ("000" + (response.data.numeroProyectoEstrategico || 0)).slice(-3);
	            $(form_caratula + ' #no_proyecto_estrategico').text(no_proyecto);
	            if($('input#numeroproyectoestrategico').length){
	            	$(form_caratula + ' #numeroproyectoestrategico').val(response.data.numeroProyectoEstrategico);
	            }else{
	            	$(form_caratula + ' #numeroproyectoestrategico').text(no_proyecto);
	            }

	            caratulaBeneficiario.init(response.data.id,proyectoResource);
				cambiar_icono_tabs('#tab-link-caratula','fa-check-square-o');
	            $('#tab-link-datos-fibap').attr('data-toggle','tab');
				$('#tab-link-datos-fibap').parent().removeClass('disabled');
				$('#tab-link-caratula-beneficiarios').attr('data-toggle','tab');
				$('#tab-link-caratula-beneficiarios').parent().removeClass('disabled');
				$('#tablink-fuentes-financiamiento').attr('data-toggle','tab');
				$('#tablink-fuentes-financiamiento').parent().removeClass('disabled');
				if(response.extras){
					if(response.extras.jurisdicciones){
						fibapAcciones.actualizar_metas_mes(response.extras.jurisdicciones);
						fibapAcciones.cargar_jurisdicciones(response.extras.jurisdicciones);
					}
					if(response.extras.responsables){
	            		fibapAcciones.init_responsables();
	            		fibapAcciones.llenar_select_responsables(response.extras.responsables);
	            	}
				}
				fuenteFinanciamiento.init(proyectoResource,$('#id').val());
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

$('#btn-fibap-guardar').on('click',function(){
	var parametros = $(form_fibap).serialize() + '&guardar=datos-fibap&id-proyecto=' + $('#id').val();

	Validation.cleanFormErrors(form_fibap);

	if($('#id-fibap').val()){
		proyectoResource.put($('#id-fibap').val(),parametros,{
	        _success: function(response){
	            MessageManager.show({data:'Datos del proyecto actualizados con éxito',type:'OK',timer:3});
	            fibapAcciones.actualizar_total_presupuesto(parseFloat(response.data.presupuestoRequerido));
				fibapAcciones.llenar_tabla_distribucion_general(response.data.distribucion_presupuesto_agrupado);
				fibapAcciones.habilitar_meses(response.data.periodoEjecucionInicio, response.data.periodoEjecucionFinal);
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
	            MessageManager.show({data:'Datos de la FIBAP almacenados con éxito',type:'OK',timer:3});

	            cambiar_icono_tabs('#tab-link-datos-fibap','fa-check-square-o');

	            $('#tab-link-antecedentes-fibap').attr('data-toggle','tab');
				$('#tab-link-antecedentes-fibap').parent().removeClass('disabled');
				$('#tab-link-acciones-fibap').attr('data-toggle','tab');
				$('#tab-link-acciones-fibap').parent().removeClass('disabled');
	            
				$(form_fibap + ' #id-fibap').val(response.data.id);

				fibapAntecedentes.init(response.data.id,proyectoResource);
				fibapAcciones.init(response.data.id,proyectoResource);
				fibapAcciones.actualizar_total_presupuesto(parseFloat(response.data.presupuestoRequerido));
				fibapAcciones.habilitar_meses(response.data.periodoEjecucionInicio, response.data.periodoEjecucionFinal);
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

$('#btn-presupuesto-guardar').on('click',function(){
	var parametros = $(form_presupuesto).serialize();
	parametros += '&guardar=desglose-presupuesto&id-fibap=' + $('#id-fibap').val();
	parametros += '&id-proyecto=' + $('#id').val();
	//Obtenemos el id de la Acción, la cual esta en un atributo en el datagrid
	var accion_id = $('#datagridDistribucion').attr('data-selected-id');
	parametros += '&id-accion='+accion_id;

	Validation.cleanFormErrors(form_presupuesto);
	
	if($('#id-desglose').val()){
		var meses_capturados = '';
		$('.presupuesto-mes').each(function(){
			if($(this).attr('data-presupuesto-id')){
				var partida = $(this).attr('data-presupuesto-partida');
				var mes = $(this).attr('data-presupuesto-mes');
				meses_capturados += '&meses-capturados['+mes+']['+partida+']='+$(this).attr('data-presupuesto-id');
			}
		});
		parametros += meses_capturados;

		var metas_capturadas = '';
		$('.meta-mes').each(function(){
			if($(this).attr('data-meta-id')){
				metas_capturadas += '&metas-capturadas['+$(this).attr('data-meta-mes')+']='+$(this).attr('data-meta-id');
			}
		});
		parametros += metas_capturadas;

		proyectoResource.put($('#id-desglose').val(),parametros,{
	        _success: function(response){
	            MessageManager.show({data:'Cambios almacenados con éxito',type:'OK',timer:3});
	            //fibapAcciones.llenar_datagrid_distribucion(response.data.desglose_presupuesto,response.data.presupuestoRequerido);
	            fibapAcciones.llenar_datagrid_distribucion(response.data.idComponente,response.data.presupuestoRequerido);
	            fibapAcciones.llenar_tabla_distribucion_general(response.extras.distribucion_total);
	            $(modal_presupuesto).modal('hide');
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
	            MessageManager.show({data:'Presupuesto almacenado con éxito',type:'OK',timer:3});
	            //fibapAcciones.llenar_datagrid_distribucion(response.data.desglose_presupuesto,response.data.presupuestoRequerido);
	            fibapAcciones.llenar_datagrid_distribucion(response.data.idComponente,response.data.presupuestoRequerido);
	            fibapAcciones.llenar_tabla_distribucion_general(response.extras.distribucion_total);
	            $(modal_presupuesto).modal('hide');
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

$('#btn-fibap-antecedente-guardar').on('click',function(){
	var parametros = $(form_fibap_antecentes).serialize() + '&guardar=datos-fibap-antecedentes';
	parametros += '&id-proyecto=' + $('#id').val();

	Validation.cleanFormErrors(form_fibap_antecentes);

	if($('#id-fibap').val()){
		proyectoResource.put($('#id-fibap').val(),parametros,{
	        _success: function(response){
	            MessageManager.show({data:'Datos del proyecto actualizados con éxito',type:'OK',timer:3});	            
	            cambiar_icono_tabs('#tab-link-antecedentes-fibap','fa-check-square-o');
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

$('#btn-antecedente-guardar').on('click',function(){
	var parametros = $(form_antecedente).serialize();
	parametros += '&guardar=datos-antecedente&id-fibap=' + $('#id-fibap').val();
	parametros += '&id-proyecto=' + $('#id').val();
	
	Validation.cleanFormErrors(form_antecedente);
	if($('#id-antecedente').val()){
		proyectoResource.put($('#id-antecedente').val(),parametros,{
	        _success: function(response){
	            MessageManager.show({data:'Cambios almacenados con éxito',type:'OK',timer:3});
	            fibapAntecedentes.llenar_datagrid(response.antecedentes);
	            $(modal_antecedente).modal('hide');
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
	            MessageManager.show({data:'Antecedente almacenado con éxito',type:'OK',timer:3});
	            fibapAntecedentes.llenar_datagrid(response.antecedentes);
	            cambiar_icono_tabs('#tab-link-antecedentes-fibap','fa-check-square-o');
	            $(modal_antecedente).modal('hide');
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

$('#btn-beneficiario-guardar').on('click',function(){
	Validation.cleanFormErrors(form_beneficiario);

	if(caratulaBeneficiario.checar_errores()){
		return false;
	}

	var parametros = $(form_beneficiario).serialize();
	parametros = parametros + '&guardar=proyecto-beneficiario&id-proyecto=' + $('#id').val();

	if($(form_beneficiario + ' #id-beneficiario').val()){
		proyectoResource.put($(form_beneficiario + ' #id-beneficiario').val(),parametros,{
			_success: function(response){
				MessageManager.show({data:'Datos de los beneficiarios almacenados con éxito',type:'OK',timer:3});
	            $(modal_beneficiario).modal('hide');
				caratulaBeneficiario.llenar_datagrid(response.data);
				fibapAcciones.actualizar_lista_beneficiarios(response.data);
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
				MessageManager.show({data:'Datos de los beneficiarios almacenados con éxito',type:'OK',timer:3});
	            $(modal_beneficiario).modal('hide');
	            caratulaBeneficiario.llenar_datagrid(response.data);
	            fibapAcciones.actualizar_lista_beneficiarios(response.data);
	            cambiar_icono_tabs('#tab-link-caratula-beneficiarios','fa-check-square-o');
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

function cambiar_icono_tabs(tab_id,icono){
	$(tab_id + ' span.fa').removeClass(function (index, css) {
    	return (css.match (/(^|\s)fa-\S+/g) || []).join(' ');
	});
	$(tab_id + ' span.fa').addClass(icono);
}

function bloquear_controles(){
	$('input,textarea,select').each(function(){
		$(this).prop('disabled',true);
		$('label[for="' + $(this).attr('id') + '"]').prepend('<span class="fa fa-lock"></span> ');
		if($(this).hasClass('chosen-one')){
			$(this).trigger('chosen:updated');
		}
	});
}

function mostrar_comentarios(datos){
	for(var i in datos){
		var id_campo = datos[i].idCampo;
		var observacion = datos[i].observacion;
		var tipo_comentario = datos[i].tipoComentario;
		
		if(tipo_comentario == 1){
			if(id_campo.substring(0,12) == 'beneficiario'){
				$('#datagridBeneficiarios tr[data-id="'+id_campo.substring(12)+'"]').addClass('text-warning');
				$('#datagridBeneficiarios tr[data-id="'+id_campo.substring(12)+'"] td:eq(1)').prepend('<span class="fa fa-warning"></span> ');
				$('#datagridBeneficiarios tr[data-id="'+id_campo.substring(12)+'"]').attr('data-comentario',observacion);
			}else if(id_campo.substring(0,14) == 'financiamiento'){
				$('#datagridFuenteFinanciamiento tr[data-id="'+id_campo.substring(14)+'"]').addClass('text-warning');
				$('#datagridFuenteFinanciamiento tr[data-id="'+id_campo.substring(14)+'"] td:eq(1)').prepend('<span class="fa fa-warning"></span> ');
				$('#datagridFuenteFinanciamiento tr[data-id="'+id_campo.substring(14)+'"]').attr('data-comentario',observacion);
			}else if(id_campo.substring(0,10) == 'documentos'){
				$('#listado_documentos').parent('.panel').addClass('has-warning');
				$('#listado_documentos').prepend('<p class="help-block proyecto-comentario">'+observacion+'</p>');
			}else if(id_campo.substring(0,12) == 'antecedentes'){
				//$('#datagridAntecedentes table').parent().addClass('has-warning');
				$('#datagridAntecedentes table').after('<div class="alert alert-warning"><span class="fa fa-warning"></span> '+observacion+'</div>');
			}else{
				if($('#'+id_campo).length){
					$('#'+id_campo).parent('.form-group').addClass('has-warning');
					var texto_lbl = $('label[for="' + id_campo + '"]').text();
					$('label[for="' + id_campo + '"]').html('<span class="proyecto-comentario" data-placement="auto top" data-toggle="popover" data-trigger="click" data-content="'+observacion+'">'+texto_lbl+'</span>');
					$('label[for="' + id_campo + '"]').prepend('<span class="fa fa-warning"></span> ');
				}
			}
		}else{
			id_campo = id_campo.split('|'); // El id campo viene en dos o mas partes: field | id - extra_data
			//

			var comentario = {};

			if(id_campo[0] == 'desglose'){
				var id_desglose = id_campo[1].split('-'); //ids en desglose son idComponente - idDesglose
				comentarios.desgloses[id_desglose[1]] = {
					tipoComentario: 1,
					idCampo: id_campo[0],
					observacion: observacion
				};
			}else{
				if(tipo_comentario == 2){
					if(!comentarios.componentes[id_campo[1]]){
						comentarios.componentes[id_campo[1]] = [];
					}
					comentario.tipoComentario = 1;
					if(id_campo[0].indexOf('entregable') > -1){
						comentario.idCampo = id_campo[0];
					}else{
						comentario.idCampo = id_campo[0] + '-componente';
					}
					comentario.observacion = observacion;
					comentarios.componentes[id_campo[1]].push(comentario);
				}else if(tipo_comentario == 3){
					if(!comentarios.actividades[id_campo[1]]){
						comentarios.actividades[id_campo[1]] = [];
					}
					comentario.tipoComentario = 1;
					comentario.idCampo = id_campo[0] + '-actividad';
					comentario.observacion = observacion;
					comentarios.actividades[id_campo[1]].push(comentario);
				}
			}
		}
	}
	$('.proyecto-comentario').popover();
}