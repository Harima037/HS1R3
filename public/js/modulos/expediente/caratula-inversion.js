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

var form_caratula = '#form_caratula';
var form_fibap = '#form-fibap-datos';
var form_fibap_antecentes = '#form-fibap-antecedentes';
var form_antecedente = '#form-antecedente';
var form_beneficiario = '#form_beneficiario';
var form_componente = '#form_componente';
var form_actividad = '#form_actividad';

var modal_antecedente = '#modal-antecedente';
var modal_presupuesto = '#modal-presupuesto';
var modal_accion  = '#modal-accion';
var modal_beneficiario = '#modalBeneficiario';
var modal_componente = '#modalComponente';
var modal_actividad = '#modalActividad';

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

			if(response.data.beneficiarios){
				caratulaBeneficiario.llenar_datagrid(response.data.beneficiarios);
				cambiar_icono_tabs('#tab-link-caratula-beneficiarios','fa-check-square-o');
			}

			if(response.data.fibap){
				caratulaFibap.llenar_datos(response.data.fibap);
				cambiar_icono_tabs('#tab-link-datos-fibap','fa-check-square-o');

				if(response.data.fibap.resultadosObtenidos || response.data.fibap.antecedentes_financieros){
					cambiar_icono_tabs('#tab-link-antecedentes-fibap','fa-check-square-o');
				}

				fibapAntecedentes.init(response.data.fibap.id,proyectoResource);
				fibapAcciones.init(response.data.fibap.id,proyectoResource);
				fibapAcciones.actualizar_total_presupuesto(response.data.fibap.presupuestoRequerido);
				fibapAcciones.actualizar_metas_mes('componente',response.extras.jurisdicciones);

				if(response.data.fibap.antecedentes_financieros){
					fibapAntecedentes.llenar_datagrid(response.data.fibap.antecedentes_financieros);
				}

				if(response.data.fibap.acciones){

				}

				$('#tab-link-antecedentes-fibap').attr('data-toggle','tab');
				$('#tab-link-antecedentes-fibap').parent().removeClass('disabled');

				$('#tab-link-acciones-fibap').attr('data-toggle','tab');
				$('#tab-link-acciones-fibap').parent().removeClass('disabled');
			}

            /*
            actualizar_tabla_metas('actividad',response.data.jurisdicciones);
            actualizar_tabla_metas('componente',response.data.jurisdicciones);
            */
        }
    });
}

$('#btn-proyecto-cancelar').on('click',function(){
	window.location.href = SERVER_HOST+'/expediente/inversion';
});

/***********************************************************************************************
							Funcionamiento de Modales
************************************************************************************************/

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
	var parametros = {'mostrar':'editar-beneficiario','id-proyecto':$('#id').val()}
	proyectoResource.get(e,parametros,{
		_success: function(response){
			caratulaBeneficiario.mostrar_datos(response.data);
		}
	});
}

/***********************************************************************************************
								Acciones de Guardado
************************************************************************************************/
$('#btn-componente-guardar-salir').on('click',function(){
	guardar_datos_componente(true);
});

$('#btn-componente-guardar').on('click',function(){
	guardar_datos_componente(false);
});

function guardar_datos_componente(cerrar){
	Validation.cleanFormErrors(form_componente);
	var parametros = $(form_componente).serialize();
	parametros = parametros + '&guardar=componente';
	parametros = parametros + '&id-proyecto='+$('#id').val();
	//parametros = parametros + '&clasificacion='+$('#clasificacionproyecto').val();

	if($('#id-componente').val()){
		/*var cadena_metas = '';
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
	    });*/
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

$('#btn-proyecto-guardar').on('click',function(){
	caratulaProyecto.limpiar_errores();

	/*if(caratulaProyecto.checar_errores()){
		return false;
	}*/

	var parametros = $(form_caratula).serialize();
	parametros = parametros + '&guardar=proyecto';
	
	if($('#id').val()){
		proyectoResource.put($('#id').val(),parametros,{
	        _success: function(response){
	            MessageManager.show({data:'Datos del proyecto almacenados con éxito',type:'OK',timer:3});
	            if(response.extras){
	            	if(response.extras.municipios){
	            		fibapAcciones.cargar_municipios(response.extras.municipios);
	            	}
	            	if(response.extras.jurisdicciones){
	            		fibapAcciones.cargar_jurisdicciones(response.extras.jurisdicciones);
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
	            MessageManager.show({data:'Datos del proyecto almacenados con éxito',type:'OK',timer:3});
	            $(form_caratula + ' #id').val(response.data.id);
	            caratulaBeneficiario.init(response.data.id,proyectoResource);
				cambiar_icono_tabs('#tab-link-caratula','fa-check-square-o');
	            $('#tab-link-datos-fibap').attr('data-toggle','tab');
				$('#tab-link-datos-fibap').parent().removeClass('disabled');
				$('#tab-link-caratula-beneficiarios').attr('data-toggle','tab');
				$('#tab-link-caratula-beneficiarios').parent().removeClass('disabled');
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
	            //Deshabilitar los meses que queden fuera del periodo de ejecución
	            /*
	            if(response.data.distribucion_presupuesto_agrupado){
	            	llenar_datagrid_presupuestos(response.data.distribucion_presupuesto_agrupado);
	            }
	            //actualizar_tabla_meses(response.data.jurisdicciones);
	            if(response.data.periodoEjecucionInicio){
	            	habilitar_meses(response.data.periodoEjecucionInicio,response.data.periodoEjecucionFinal);
	            }
	            */
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
	            /*
				if(response.data.periodoEjecucionInicio){
	            	habilitar_meses(response.data.periodoEjecucionInicio,response.data.periodoEjecucionFinal);
	            }
	            */
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