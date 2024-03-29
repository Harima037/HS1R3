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
var catalogosAlineacionResource = new RESTfulRequests(SERVER_HOST+'/v1/ped-estrategia-por-alineacion');

var comentarios = {
	componentes: {},
	actividades: {},
	desgloses: { actividades:{}, componentes:{} }
};

var form_caratula = '#form_caratula';
var form_fibap = '#form-fibap-datos';
var form_fibap_antecentes = '#form-fibap-antecedentes';
var form_antecedente = '#form-antecedente';
var form_beneficiario = '#form_beneficiario';
var form_accion = '#form_accion';
var form_presupuesto = '#form-presupuesto';
var form_cancelacion_proyecto = '#form_cancelacion_proyecto';

var modal_antecedente = '#modal-antecedente';
var modal_presupuesto = '#modal-presupuesto';
var modal_accion  = '#modal-accion';
var modal_beneficiario = '#modalBeneficiario';
var modal_cancelar_proyecto = '#modalCancelarProyecto';

var estrategia_seleccionada = '';

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

			estrategia_seleccionada = response.data.idEstrategiaEstatal;
			
	       	cambiar_icono_tabs('#tab-link-caratula','fa-check-square-o');

            $('#tab-link-datos-fibap').attr('data-toggle','tab');
			$('#tab-link-datos-fibap').parent().removeClass('disabled');
			$('#tab-link-caratula-beneficiarios').attr('data-toggle','tab');
			$('#tab-link-caratula-beneficiarios').parent().removeClass('disabled');
			$('#tablink-fuentes-financiamiento').attr('data-toggle','tab');
			$('#tablink-fuentes-financiamiento').parent().removeClass('disabled');
			$('#tab-link-normatividad').attr('data-toggle','tab');
			$('#tab-link-normatividad').parent().removeClass('disabled');

			/*if(response.extras.municipios){
        		fibapAcciones.cargar_municipios(response.extras.municipios);
        	}*/
			caratulaArchivos.init();
			caratulaArchivos.badgeElement = $('#tab-link-normatividad > span');
			caratulaArchivos.cargarListadoArchivos();

			if(response.data.cancelado){
				$('#btn-cancelar-proyecto').hide();
				$('#span-proyecto-cancelado').show();
				$('#datagridCaratulas').removeClass('panel-default');
				$('#datagridCaratulas').addClass('panel-danger');
			}
        	
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

$('#alineacion').on('change',function(){
	var clave = $('#alineacion').val();
	if(clave){
		catalogosAlineacionResource.get(clave,null,{
			_success: function(response){
				
				$('#estrategiaestatal').val('');
				llenar_select_estrategias(response.data.estrategias,estrategia_seleccionada)
				
				llenar_datos_objetico_ped(response.data.objetivo);
			}
		});
	}else{
		$('#estrategiaestatal').val('');
		$('#estrategiaestatal').html('<option value="">Selecciona una Alineación</option>');
		$('#estrategiaestatal').trigger('chosen:updated');
		///
		$('#panel-objetivo-ped').html('Selecciona una Alineación');
		$('#vinculacionped').val('');
	}
});

function llenar_datos_objetico_ped(objetivo){
	var html_objetivo_ped = '';
	html_objetivo_ped = '<ul style="list-style-type:none; padding-left: 0px;"><li style="font-weight: bold;font-size: large;">'+objetivo.padre.padre.padre.clave+' '+objetivo.padre.padre.padre.descripcion+'</li>';
	html_objetivo_ped += '<li><ul style="list-style-type:none; padding-left: 0px;"><li style="font-weight: bold;font-size: medium;">'+objetivo.padre.padre.clave+' '+objetivo.padre.padre.descripcion+'</li>';
	html_objetivo_ped += '<li><ul style="list-style-type:none; padding-left: 0px;"><li style="font-weight: bold;font-size: medium;font-style: italic;">'+objetivo.padre.clave+' '+objetivo.padre.descripcion+'</li>';
	html_objetivo_ped += '<li><ul style="list-style-type:none; padding-left: 0px;"><li style="font-size: large;">'+objetivo.clave+' '+objetivo.descripcion+'</li>';
	html_objetivo_ped += '</ul></li></ul></li></ul></li></li></ul>';

	$('#panel-objetivo-ped').html(html_objetivo_ped);
	$('#vinculacionped').val(objetivo.id);
}

function llenar_select_estrategias(estrategias, selected = ''){
	var html_opciones = '<option value="">Selecciona una Estrategia</option>';
	for(var i in estrategias){
		var estrategia = estrategias[i];
		html_opciones += '<option value="'+estrategia.id+'">';
		html_opciones += estrategia.claveEstrategia + ' ' + estrategia.descripcion;
		html_opciones += '</option>';
	}
	$('#estrategiaestatal').html('');
	$('#estrategiaestatal').html(html_opciones);

	if(selected){
		$('#estrategiaestatal').val(selected);
	}

	$('#estrategiaestatal').trigger('chosen:updated');
}

function eliminarArchivo(id){
	caratulaArchivos.eliminarArchivo(id);
}

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
	var parametros = {'mostrar':'editar-accion'};
	proyectoResource.get(e,parametros,{
		_success: function(response){
			fibapAcciones.mostrar_datos(response.data);
		}
	});
}

function editar_presupuesto(e){
	if($(modal_presupuesto).find(".modal-title").html() == "Editar Presupuesto"){
		return;
	}
	$(modal_presupuesto).find(".modal-title").html("Editar Presupuesto");
	
	var parametros = {'mostrar':'editar-presupuesto','id-proyecto':$('#id').val(),'nivel':$('#nivel-desglose').val()};
	proyectoResource.get(e,parametros,{
		_success: function(response){
			fibapAcciones.mostrar_datos_presupuesto(response.data);
		}
	});
}

function editar_fuente_financiamiento(e){
	fuenteFinanciamiento.editar_fuente(e);
}

$('#btn-cancelar-proyecto').on('click',function(){
	if($('#id').val()){
		$('#motivos-cancelacion').prop('disabled',false);
		$('#motivos-cancelacion').val('');
		$('#fecha-cancelacion').prop('disabled',false);
		$('#fecha-cancelacion').val('');
		Validation.cleanFormErrors(form_cancelacion_proyecto);
		$(modal_cancelar_proyecto).modal('show');
	}else{
		MessageManager.show({code:'S03',data:"No se puede cancelar un proyecto que no se ha guardado.",timer:2});
	}
});
/***********************************************************************************************
								Acciones de Guardado
************************************************************************************************/

$('#btn-guardar-cancelar-proyecto').on('click',function(){
	Validation.cleanFormErrors(form_cancelacion_proyecto);

	var parametros = $(form_cancelacion_proyecto).serialize();
	parametros = parametros + '&guardar=cancelacionproyecto&id-proyecto=' + $('#id').val();

	if($('#id').val()){
		proyectoResource.put($('#id').val(),parametros,{
	        _success: function(response){
				$(modal_cancelar_proyecto).modal('hide');
				$('#btn-cancelar-proyecto').hide();
				$('#span-proyecto-cancelado').show();
				$('#datagridCaratulas').removeClass('panel-default');
				$('#datagridCaratulas').addClass('panel-danger');
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
	guardar_datos_accion(true);
});

$('#btn-componente-guardar').on('click',function(){
	guardar_datos_accion(false);
});

function guardar_datos_accion(cerrar){
	var nivel = $('#nivel-accion').val();
	
	Validation.cleanFormErrors(form_accion);
	var parametros = $(form_accion).serialize();
	parametros += '&guardar=accion';
	parametros += '&id-fibap=' + $('#id-fibap').val();
	parametros += '&id-proyecto=' + $('#id').val();
	parametros += '&nivel='+nivel;
	if(nivel == 'actividad'){
		parametros += '&id-nuevo-componente=' + $('#componente-seleccionado').val();
	}
	//parametros = parametros + '&clasificacion='+$('#clasificacionproyecto').val();

	if($('#id-accion').val()){
		var cadena_metas = '';
		$(form_accion + ' .metas-mes').each(function(){
			if($(this).attr('data-meta-id')){
				cadena_metas = cadena_metas + '&mes-accion-id['+$(this).attr('data-meta-jurisdiccion')+']['+$(this).attr('data-meta-mes')+']='+$(this).attr('data-meta-id');
			}
		});
		parametros += cadena_metas;

		//if(selector == 'componente'){
		var lista_origenes_id = '';
		$('.accion-origen-financiamiento').each(function(){
			if($(this).attr('data-captura-id')){
				lista_origenes_id += '&origen-captura-id[' + $(this).attr('data-origen-id') + ']=' + $(this).attr('data-captura-id');
			}
		});
		parametros += lista_origenes_id;
		//}

		proyectoResource.put($('#id-accion').val(),parametros,{
	        _success: function(response){
	            MessageManager.show({data:'Datos de la acción almacenados con éxito',type:'OK',timer:3});
				fibapAcciones.llenar_datagrid(response.acciones);
				if(response.distribucion_total){
					fibapAcciones.llenar_tabla_distribucion_general(response.distribucion_total);
				}
				
	            if(cerrar){
					$(modal_accion).modal('hide');
				}else{
					if(response.metas){
						fibapAcciones.actualizar_metas_ids('accion',response.metas);
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
				fibapAcciones.llenar_datagrid(response.acciones);
	            if(cerrar){
					$(modal_accion).modal('hide');
				}else{
					$('#lnk-descarga-archivo-presupuesto').attr('href',SERVER_HOST+'/expediente/descargar-archivo-municipios/'+$('#id').val()+'?tipo-carga=presupuesto&id-accion='+response.data.id);
					$('#lnk-descarga-archivo-presupuesto').removeClass('disabled');
					if(response.data.idComponente){
						$(form_accion + ' #id-componente').val(response.data.idComponente);
					}else{
						$(form_accion + ' #id-actividad').val(response.data.idActividad);
					}
					$(form_accion + ' #id-accion').val(response.data.id);
					
					if(response.metas){
						fibapAcciones.actualizar_metas_ids('accion',response.metas);
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
				$('#tab-link-normatividad').attr('data-toggle','tab');
				$('#tab-link-normatividad').parent().removeClass('disabled');

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

				caratulaArchivos.init();
				caratulaArchivos.badgeElement = $('#tab-link-normatividad > span');
				caratulaArchivos.cargarListadoArchivos();
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
				if(response.data.distribucion_presupuesto_agrupado){
					fibapAcciones.llenar_tabla_distribucion_general(response.data.distribucion_presupuesto_agrupado);
				}
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
	parametros += '&nivel-desglose=' + $('#nivel-desglose').val();
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
				var nivel = '';
				if(response.data.idComponente){
					nivel = 'componente';
				}else{
					nivel = 'actividad';
				}
	            fibapAcciones.llenar_datagrid_distribucion(response.data.id,response.data.presupuestoRequerido,nivel);
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
				var nivel = '';
				if(response.data.idComponente){
					nivel = 'componente';
				}else{
					nivel = 'actividad';
				}
	            fibapAcciones.llenar_datagrid_distribucion(response.data.id,response.data.presupuestoRequerido,nivel);
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
			}else if(id_campo.substring(0,12) == 'normatividad'){
				$('#normatividad-revision').removeClass('hidden');
				$('#normatividad-revision-comentario').text(observacion);
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
				var id_desglose = id_campo[1].split('-'); //ids en desglose son idComponente|idActividad - idDesglose
				if(tipo_comentario == 2){
					comentarios.desgloses.componentes[id_desglose[1]] = {
						tipoComentario: 1,
						idCampo: id_campo[0],
						observacion: observacion
					};
				}else if(tipo_comentario == 3){
					comentarios.desgloses.actividades[id_desglose[1]] = {
						tipoComentario: 1,
						idCampo: id_campo[0],
						observacion: observacion
					};
				}
				/*comentarios.desgloses[id_desglose[1]] = {
					tipoComentario: 1,
					idCampo: id_campo[0],
					observacion: observacion
				};*/
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