
/*=====================================

    # Nombre:
        formulario-fibap.js

    # Módulo:
        expediente/formulario-fibap

    # Descripción:
        Para el formulario de captura del FIBAP de un proyecto

=====================================*/
// Declaracion de variables
var fibapResource = new RESTfulRequests(SERVER_HOST+'/v1/fibap');
//var componenteDatagrid = new Datagrid("#datagridComponentes",proyectoResource);
var presupuestoDatagrid = new Datagrid("#datagridPresupuesto",fibapResource);
var antecedenteDatagrid = new Datagrid("#datagridAntecedentes",fibapResource);
var accionesDatagrid = new Datagrid('#datagridAcciones',fibapResource);
var distribucionDataGrid = new Datagrid('#datagridDistribucion',fibapResource); //Este datagrid se ira moviendo por todas las acciones

antecedenteDatagrid.init();
presupuestoDatagrid.init();
accionesDatagrid.init();
distribucionDataGrid.init();

//Ventanas modales
var modal_antecedente = '#modal-antecedente';
var modal_presupuesto = '#modal-presupuesto';
var modal_accion  = '#modal-accion';

//Formularios de las ventanas modales
var form_antecedente = 'form-antecedente';
var form_presupuesto = 'form-presupuesto';
var form_accion = 'form-accion';

window.onload = function () {
	$('#mensaje-espera').addClass('hidden');
	$('#panel-principal-formulario').removeClass('hidden');
	$('[data-toggle="popover"]').popover();
};

deshabilita_paneles('');

//*********************************   Funcionalidad al cargar el formulario   *********************************
$('.origen-financiamiento').on('keyup',function(){
	sumar_valores('.origen-financiamiento','#presupuesto-requerido');
});
$('.accion-origen-financiamiento').on('keyup',function(){
	sumar_valores('.accion-origen-financiamiento','#accion-presupuesto-requerido');
});
$('.presupuesto-mes').on('keyup',function(){
	sumar_valores('.presupuesto-mes','#cantidad-presupuesto');
});
$('.meta-mes').on('keyup',function(){
	sumar_valores('.meta-mes','#cantidad-meta');
});
$('.benef-totales-accion').on('keyup',function(){
	sumar_valores('.benef-totales-accion','#total-beneficiarios-accion');
});
$('#cobertura').on('change',function(){
	deshabilita_paneles($(this).val());
});
$('#municipio-accion').on('change',function(){
	var json = $('#municipio-accion > option:selected').attr('data-localidades');
	if(json){
		json = json.split('|');
	}else{
		json = [];
	}
	
	var localidades = [];
	var localidad;
	for(var i in json){
		localidad = jQuery.parseJSON(json[i]);
		if(localidad){
			localidades.push(localidad);
		}
	}
	llenar_select_localidades(localidades);
});
$('.benef-totales').on('keyup',function(){
	var total = 0;
	$('.benef-totales').each(function(){
		total += parseInt($(this).val()) || 0;
	});
	$('#total-beneficiarios').text(total);
});
$('.control-espejo').each(function(){
	var control_id = $(this).data('espejo-id');
	$(control_id).on('change',function(){
		var id = $(this).attr('id');
		$('.control-espejo[data-espejo-id="#'+id+'"]').text($(this).val());
	});
});
//Si hay un id, entonces cargamos los datos para editar
if($('#id').val()){
	var parametros = {ver:'fibap'};
	fibapResource.get($('#id').val(),parametros,{
		_success:function(response){
			//
			$('#organismo-publico').val(response.data.organismoPublico);
			$('#sector').val(response.data.sector);
			$('#subcomite').val(response.data.subcomite);
			$('#grupo-trabajo').val(response.data.grupoTrabajo);
			$('#justificacion-proyecto').val(response.data.justificacionProyecto);
			$('#descripcion-proyecto').val(response.data.descripcionProyecto);
			$('#objetivo-proyecto').val(response.data.objetivoProyecto);
			$('#alineacion-especifica').val(response.data.alineacionEspecifica);
			$('#alineacion-general').val(response.data.alineacionGeneral);
			$('#resultados-obtenidos').val(response.data.resultadosObtenidos);
			$('#resultados-esperados').val(response.data.resultadosEsperados);
			$('#presupuesto-requerido').val(response.data.presupuestoRequerido);
			$('#presupuesto-requerido').change();
			$('#periodo-ejecucion-inicio').val(response.data.periodoEjecucionInicio);
			$('#periodo-ejecucion-final').val(response.data.periodoEjecucionFinal);

			$('#total-presupuesto-requerido').text('$ ' + response.data.presupuestoRequerido.format());
			$('#total-presupuesto-requerido').attr('data-valor',response.data.presupuestoRequerido);

			actualizar_tabla_meses(response.data.jurisdicciones);
			if(response.data.periodoEjecucionInicio){
				habilitar_meses(response.data.periodoEjecucionInicio,response.data.periodoEjecucionFinal);
			}

			var datosProyecto = null;

			if(response.data.proyecto){
				datosProyecto = response.data.proyecto;
				$('#proyecto-id').val(datosProyecto.id);
				//Marcamos los campos que vamos a bloquear
				$('#tipo-proyecto').addClass('control-bloqueado');
				$('#programa-presupuestal').addClass('control-bloqueado');
				$('#vinculacion-ped').addClass('control-bloqueado');
				$('#cobertura').addClass('control-bloqueado');
				$('#municipio').addClass('control-bloqueado');
				$('#region').addClass('control-bloqueado');
				$('#proyecto').addClass('control-bloqueado');
				$('#tipo-beneficiario').addClass('control-bloqueado');
				$('#total-beneficiarios-f').addClass('control-bloqueado');
				$('#total-beneficiarios-m').addClass('control-bloqueado');

				$('#clave-presupuestaria').text('[ '+response.clavePresupuestaria+' ]');
			}else{
				datosProyecto = response.data.datos_proyecto;
			}

			$('#tipo-proyecto').val(datosProyecto.idTipoProyecto);
			$('#tipo-proyecto').change();

			$('#programa-presupuestal').val(datosProyecto.programaPresupuestario);
            $('#programa-presupuestal').change();

			$('#vinculacion-ped').val(datosProyecto.idObjetivoPED);
            $('#vinculacion-ped').change();
			$('#cobertura').val(datosProyecto.idCobertura);
            $('#cobertura').change();

			if(datosProyecto.claveMunicipio){
				$('#municipio').val(datosProyecto.claveMunicipio);
            	$('#municipio').change();
            }

            if(datosProyecto.claveRegion){
            	$('#region').val(datosProyecto.claveRegion);
            	$('#region').change();
            }

			$('#proyecto').val(datosProyecto.nombreTecnico);

			$('#tipo-beneficiario').val(datosProyecto.idTipoBeneficiario);
            $('#tipo-beneficiario').change();
            $('#total-beneficiarios').text(datosProyecto.totalBeneficiarios);
            $('#total-beneficiarios-f').val(datosProyecto.totalBeneficiariosF);
            $('#total-beneficiarios-m').val(datosProyecto.totalBeneficiariosM);

            for(var indx in response.data.documentos){
            	$('#documento_'+response.data.documentos[indx].id).prop('checked',true);
            }

            for(var indx in response.data.propuestas_financiamiento){
            	var origen = response.data.propuestas_financiamiento[indx];
            	$('#origen_'+origen.idOrigenFinanciamiento).val(origen.cantidad);
            	$('#origen_'+origen.idOrigenFinanciamiento).attr('data-captura-id',origen.id);
            }

            habilitar_tabs();
            bloquear_controles();
            cambiar_icono_tabs('#tab-link-datos-fibap','fa-check-square-o');

            llenar_datagrid_antecedentes(response.data.antecedentes_financieros);
            llenar_datagrid_acciones(response.data.acciones);
            llenar_datagrid_presupuestos(response.data.distribucion_presupuesto_agrupado);

            llenar_select_jurisdicciones(response.data.jurisdicciones);
            llenar_select_municipios(response.data.municipios);
            llenar_select_localidades([]);

            if(response.data.resultadosObtenidos || response.data.resultadosEsperados){
            	cambiar_icono_tabs('#tab-link-antecedentes-fibap','fa-check-square-o');
            }

            if(response.data.presupuestoRequerido || response.data.periodoEjecucion){
            	cambiar_icono_tabs('#tab-link-presupuesto-fibap','fa-check-square-o');
            	cambiar_icono_tabs('#tab-link-acciones-fibap','fa-check-square-o');
            }
		}
	});
}else if($('#proyecto-id').val()){
	var parametros = {ver:'datos-proyecto'};
	fibapResource.get($('#proyecto-id').val(),parametros,{
		_success:function(response){
			//
			var datosProyecto = null;

			datosProyecto = response.data;
			//Marcamos los campos que vamos a bloquear
			$('#tipo-proyecto').addClass('control-bloqueado');
			$('#programa-presupuestal').addClass('control-bloqueado');
			$('#vinculacion-ped').addClass('control-bloqueado');
			$('#cobertura').addClass('control-bloqueado');
			$('#municipio').addClass('control-bloqueado');
			$('#region').addClass('control-bloqueado');
			$('#proyecto').addClass('control-bloqueado');
			$('#tipo-beneficiario').addClass('control-bloqueado');
			$('#total-beneficiarios-f').addClass('control-bloqueado');
			$('#total-beneficiarios-m').addClass('control-bloqueado');

			$('#tipo-proyecto').val(datosProyecto.idTipoProyecto);
			$('#tipo-proyecto').change();

			$('#programa-presupuestal').val(datosProyecto.programaPresupuestario);
            $('#programa-presupuestal').change();

			$('#vinculacion-ped').val(datosProyecto.idObjetivoPED);
            $('#vinculacion-ped').change();
			$('#cobertura').val(datosProyecto.idCobertura);
            $('#cobertura').change();

			if(datosProyecto.claveMunicipio){
				$('#municipio').val(datosProyecto.claveMunicipio);
            	$('#municipio').change();
            }

            if(datosProyecto.claveRegion){
            	$('#region').val(datosProyecto.claveRegion);
            	$('#region').change();
            }

			$('#proyecto').val(datosProyecto.nombreTecnico);

			$('#tipo-beneficiario').val(datosProyecto.idTipoBeneficiario);
            $('#tipo-beneficiario').change();
            $('#total-beneficiarios').text(datosProyecto.totalBeneficiarios);
            $('#total-beneficiarios-f').val(datosProyecto.totalBeneficiariosF);
            $('#total-beneficiarios-m').val(datosProyecto.totalBeneficiariosM);

            $('#clave-presupuestaria').text('[ '+response.clavePresupuestaria+' ]');

            bloquear_controles();
		}
	});
}

//*********************************   Funcionalidad de los DataGrids (Editar y Eliminar)   *********************************
function editar_antecedente(e){
	var parametros = {'ver':'antecedente'};
	fibapResource.get(e,parametros,{
        _success: function(response){
            $(modal_antecedente).find(".modal-title").html("Editar Antecedente");

            $('#anio-antecedente').val(response.data.anio);
			$('#autorizado-antecedente').val(response.data.autorizado);
			$('#ejercido-antecedente').val(response.data.ejercido);
			$('#fecha-corte-antecedente').val(response.data.fechaCorte);
			$('#id-antecedente').val(response.data.id);

            $(modal_antecedente).modal('show');
        }
    });
}

function editar_presupuesto(e){
	if($(modal_presupuesto).find(".modal-title").html() == "Editar Presupuesto"){
		return;
	}
	$(modal_presupuesto).find(".modal-title").html("Editar Presupuesto");
	var parametros = {'ver':'distribucion-presupuesto-metas'};
	fibapResource.get(e,parametros,{
        _success: function(response){
            //$(modal_presupuesto).find(".modal-title").html("Editar Presupuesto");
            $('#jurisdiccion-accion').val(response.calendarizado[0].claveJurisdiccion);
            $('#jurisdiccion-accion').change();

            var desglose = response.data.desglose_componente;
            $('#municipio-accion').val(desglose.claveMunicipio);
            $('#municipio-accion').change();

            //llenar_select_localidades
            $('#localidad-accion').val(desglose.claveLocalidad);
            $('#localidad-accion').change();

            $('#beneficiarios-f').val(desglose.beneficiariosF);
            $('#beneficiarios-m').val(desglose.beneficiariosM);
            $('#total-beneficiarios-accion').val(desglose.beneficiariosF + desglose.beneficiariosM).change();

            for(var indx in desglose.metas_mes){
            	var meta = desglose.metas_mes[indx];
            	$('#meta-mes-'+meta.mes).val(meta.meta);
            	$('#meta-mes-'+meta.mes).attr('data-meta-id',meta.id);
            }

            $('#trim1').val(desglose.trim1).change();
            $('#trim2').val(desglose.trim2).change();
            $('#trim3').val(desglose.trim3).change();
            $('#trim4').val(desglose.trim4).change();
            $('#cantidad-meta').val(desglose.meta).change();

            $('#cantidad-presupuesto').val(desglose.presupuesto);
			$('#cantidad-presupuesto').change();
			
			var calendarizacion = response.calendarizado;
			var partidas = [];
			partidas[1] = parseInt($('#datagridDistribucion').attr('data-partida-1-id'));
			partidas[2] = parseInt($('#datagridDistribucion').attr('data-partida-2-id'));
			for(var indx in calendarizacion){
				var partida = partidas.indexOf(calendarizacion[indx].idObjetoGasto);
				$('#mes-'+partida+'-'+calendarizacion[indx].mes).val(calendarizacion[indx].cantidad);
				$('#mes-'+partida+'-'+calendarizacion[indx].mes).attr('data-presupuesto-id',calendarizacion[indx].id);
			}

			$('#id-desglose').val(desglose.id);

            $(modal_presupuesto).modal('show');
        },
        _error: function(){
        	var json = $.parseJSON(jqXHR.responseText);
			
			if(!json.code)
				MessageManager.show({code:'S03',data:"Hubo un problema al realizar la transacción, inténtelo de nuevo o contacte con soporte técnico."});
			else
				MessageManager.show(json);
			$(modal_presupuesto).find(".modal-title").html("Error");
        }
    });
}

function ver_distribucion_partida(e){
	console.log(e);
}

function editar_accion(e){
	ocultar_detalles(true);
	var parametros = {'ver':'accion'};
	fibapResource.get(e,parametros,{
        _success: function(response){
            $(modal_accion).find(".modal-title").html("Editar Accion");
            
            for(var i in response.data.partidas){
            	var id = parseInt(i) + 1;
            	$('#objeto-gasto-presupuesto_' + id).val(response.data.partidas[i].id);
                $('#objeto-gasto-presupuesto_' + id).change();
            }
            //$('#objeto-gasto-presupuesto').selectpicker('refresh');

            $('#accion-presupuesto-requerido').val(response.data.presupuestoRequerido);
			$('#accion-presupuesto-requerido').change();
			for(var indx in response.data.propuestas_financiamiento){
            	var origen = response.data.propuestas_financiamiento[indx];
            	$('#accion-origen-'+origen.idOrigenFinanciamiento).val(origen.cantidad);
            	$('#accion-origen-'+origen.idOrigenFinanciamiento).attr('data-captura-id',origen.id);
            }

            $('#indicador').val(response.data.datos_componente.indicador);
            $('#unidad-medida').val(response.data.datos_componente.idUnidadMedida);
            $('#unidad-medida').change();
            $('#entregable').val(response.data.datos_componente.idEntregable);
            $('#entregable').change();
            $('#tipo-componente').val(response.data.datos_componente.idTipoComponente);
            $('#accion-componente').val(response.data.datos_componente.idAccionComponente);

            $('#id-accion').val(response.data.id);
            
            $(modal_accion).modal('show');
        }
    });
}

/**                            Reescribiendo comportamiento del datagrid                                 **/
$("#datagridAntecedentes .btn-delete-rows").unbind('click');
$("#datagridAntecedentes .btn-delete-rows").on('click',function(e){
	e.preventDefault();
	var rows = [];
	var contador= 0;
    $(this).parents(".datagrid").find("tbody").find("input[type=checkbox]:checked").each(function () {
		contador++;
        rows.push($(this).parent().parent().data("id"));
	});
	if(contador>0){
		Confirm.show({
				titulo:"Eliminar antecedente",
				mensaje: "¿Estás seguro que deseas eliminar los antecedentes seleccionados?",
				callback: function(){
					fibapResource.delete(rows,{'rows': rows, 'eliminar': 'antecedente', 'id-fibap': $('#id').val()},{
                        _success: function(response){
                        	llenar_datagrid_antecedentes(response.antecedentes);
                        	MessageManager.show({data:'Antecedente eliminado con éxito.',timer:3});
                        },
                        _error: function(jqXHR){  MessageManager.show(jqXHR.responseJSON); }
        			});
				}
		});
	}else{ MessageManager.show({data:'No has seleccionado ningún registro.',type:'ADV',timer:3}); }
});

$("#datagridDistribucion .btn-delete-rows").unbind('click');
$("#datagridDistribucion .btn-delete-rows").on('click',function(e){
	e.preventDefault();
	var rows = [];
	var contador= 0;
    $("#datagridDistribucion").find("tbody").find("input[type=checkbox]:checked").each(function () {
		contador++;
        rows.push($(this).parent().parent().data("id"));
	});
	if(contador>0){
		var accion_id = $('#datagridDistribucion').attr('data-selected-id');
		Confirm.show({
				titulo:"Eliminar presupuesto",
				mensaje: "¿Estás seguro que deseas eliminar los presupuestos seleccionados?",
				callback: function(){
					fibapResource.delete(rows,{'rows': rows, 'eliminar': 'presupuesto', 'id-accion': accion_id},{
                        _success: function(response){
                        	llenar_datagrid_distribucion(response.accion.distribucion_presupuesto_agrupado,response.accion.presupuestoRequerido);
                        	MessageManager.show({data:'Presupuesto eliminado con éxito.',timer:3});
                        },
                        _error: function(jqXHR){  MessageManager.show(jqXHR.responseJSON); }
        			});
				}
		});
	}else{ MessageManager.show({data:'No has seleccionado ningún registro.',type:'ADV',timer:3}); }
});

$("#datagridAcciones .btn-delete-rows").unbind('click');
$("#datagridAcciones .btn-delete-rows").on('click',function(e){
	e.preventDefault();
	var rows = [];
	var contador= 0;
    $(this).parents("#datagridAcciones").find("tbody").find("input[type=checkbox]:checked").each(function () {
		contador++;
        rows.push($(this).parent().parent().data("id"));
	});
	if(contador>0){
		Confirm.show({
				titulo:"Eliminar Acción",
				mensaje: "¿Estás seguro que deseas eliminar la(s) acción(es) seleccionada(s)?",
				callback: function(){
					fibapResource.delete(rows,{'rows': rows, 'eliminar': 'accion', 'id-fibap': $('#id').val()},{
                        _success: function(response){
                        	llenar_datagrid_acciones(response.acciones);
                        	MessageManager.show({data:'Acción(es) eliminada(s) con éxito.',timer:3});
                        },
                        _error: function(jqXHR){  MessageManager.show(jqXHR.responseJSON); }
        			});
				}
		});
	}else{ MessageManager.show({data:'No has seleccionado ningún registro.',type:'ADV',timer:3}); }
});

//*********************************   Funcionalidad de Botones principales (Guardar y Cancelar)   *********************************
$('#btn-accion-guardar').on('click',function(){
	var parametros = $('#'+form_accion).serialize();
	parametros += '&formulario='+form_accion + '&fibap-id=' + $('#id').val();
	Validation.cleanFormErrors('#'+form_accion);

	if($('#id-accion').val()){
		var lista_origenes_id = '';
		$('.accion-origen-financiamiento').each(function(){
			if($(this).attr('data-captura-id')){
				lista_origenes_id += '&origen-captura-id[' + $(this).data('origen-id') + ']=' + $(this).attr('data-captura-id');
			}
		});
		parametros += lista_origenes_id;

		fibapResource.put($('#id-accion').val(),parametros,{
			_success: function(response){
				MessageManager.show({data:'Cambios almacenados con éxito',type:'OK',timer:3});
				llenar_datagrid_acciones(response.acciones);
				$(modal_accion).modal('hide');
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
		fibapResource.post(parametros,{
			_success: function(response){
				MessageManager.show({data:'Acción almacenada con éxito',type:'OK',timer:3});
				llenar_datagrid_acciones(response.acciones);
				cambiar_icono_tabs('#tab-link-presupuesto-fibap','fa-check-square-o');
				cambiar_icono_tabs('#tab-link-acciones-fibap-fibap','fa-check-square-o');
				$(modal_accion).modal('hide');
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
	var parametros = $('#'+form_presupuesto).serialize();
	parametros += '&formulario='+form_presupuesto + '&fibap-id=' + $('#id').val();
	//Obtenemos el id de la Acción, la cual esta en un atributo en el datagrid
	var accion_id = $('#datagridDistribucion').attr('data-selected-id');
	parametros += '&accion-id='+accion_id;

	//Obtenemos los ids de las Partidas capturadas
	var partida_1_id = $('#datagridDistribucion').attr('data-partida-1-id');
	var partida_2_id = $('#datagridDistribucion').attr('data-partida-2-id');
	parametros += '&partidas[1]='+partida_1_id+'&partidas[2]='+partida_2_id;

	Validation.cleanFormErrors('#'+form_presupuesto);
	
	if($('#id-desglose').val()){
		var meses_capturados = '';
		var metas_capturadas = '';
		$('.presupuesto-mes').each(function(){
			if($(this).attr('data-presupuesto-id')){
				if($(this).hasClass('valor-partida-1')){
					meses_capturados += '&meses-capturados[1]['+$(this).attr('data-presupuesto-mes')+']='+$(this).attr('data-presupuesto-id');
				}else{
					meses_capturados += '&meses-capturados[2]['+$(this).attr('data-presupuesto-mes')+']='+$(this).attr('data-presupuesto-id');
				}
				
			}
		});
		parametros += meses_capturados;
		$('.meta-mes').each(function(){
			if($(this).attr('data-meta-id')){
				metas_capturadas += '&metas-capturadas['+$(this).attr('data-meta-mes')+']='+$(this).attr('data-meta-id');
			}
		});
		parametros += metas_capturadas;
				
		fibapResource.put($('#id-desglose').val(),parametros,{
	        _success: function(response){
	            MessageManager.show({data:'Cambios almacenados con éxito',type:'OK',timer:3});
	            llenar_datagrid_distribucion(response.data.distribucion_presupuesto_agrupado,response.data.presupuestoRequerido);
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
		fibapResource.post(parametros,{
	        _success: function(response){
	            MessageManager.show({data:'Presupuesto almacenado con éxito',type:'OK',timer:3});
	            llenar_datagrid_distribucion(response.data.distribucion_presupuesto_agrupado,response.data.presupuestoRequerido);
	            cambiar_icono_tabs('#tab-link-presupuesto-fibap','fa-check-square-o');
	            cambiar_icono_tabs('#tab-link-acciones-fibap','fa-check-square-o');
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

$('#btn-antecedente-guardar').on('click',function(){
	var parametros = $('#'+form_antecedente).serialize();
	parametros += '&formulario='+form_antecedente + '&fibap-id=' + $('#id').val();
	Validation.cleanFormErrors('#'+form_antecedente);
	if($('#id-antecedente').val()){
		fibapResource.put($('#id-antecedente').val(),parametros,{
	        _success: function(response){
	            MessageManager.show({data:'Cambios almacenados con éxito',type:'OK',timer:3});
	            llenar_datagrid_antecedentes(response.antecedentes);
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
		fibapResource.post(parametros,{
	        _success: function(response){
	            MessageManager.show({data:'Antecedente almacenado con éxito',type:'OK',timer:3});
	            llenar_datagrid_antecedentes(response.antecedentes);
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

$('#btn-fibap-guardar').on('click',function(){
	var formulario = $('#fibap-grupo-formularios > .tab-pane.active').data('form-id');
	var parametros = $('#'+formulario).serialize() + '&id=' + $('#id').val() + '&formulario=' + formulario;

	if($('#proyecto-id').val()){
		parametros += '&proyecto-id=' + $('#proyecto-id').val();
	}

	Validation.cleanFormErrors('#'+formulario);

	if($('#id').val()){
		fibapResource.put($('#id').val(),parametros,{
	        _success: function(response){
	            MessageManager.show({data:'Datos del proyecto actualizados con éxito',type:'OK',timer:3});
	            var tab_panel_id = $('#fibap-grupo-formularios > .tab-pane.active').attr('id');
	            var tab_link_id = $('.nav-tabs a[href="#'+tab_panel_id+'"]').attr('id');
	            cambiar_icono_tabs('#'+tab_link_id,'fa-check-square-o');
	            if(response.distribucion){
	            	llenar_datagrid_presupuestos(response.distribucion);
	            }
	            //actualizar_tabla_meses(response.data.jurisdicciones);
	            if(response.data.periodoEjecucionInicio){
	            	habilitar_meses(response.data.periodoEjecucionInicio,response.data.periodoEjecucionFinal);
	            }
	            if(response.extras){
	            	llenar_select_municipios(response.extras.municipios);
            		llenar_select_localidades([]);
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
		fibapResource.post(parametros,{
	        _success: function(response){
	            MessageManager.show({data:'Datos del proyecto almacenados con éxito',type:'OK',timer:3});
	            habilitar_tabs();
	            //actualizar_tabla_meses(response.data.jurisdicciones);
	            cambiar_icono_tabs('#tab-link-datos-fibap','fa-check-square-o');
	            $('#id').val(response.data.id);
	            if(response.extras){
		            llenar_select_municipios(response.extras.municipios);
	            	llenar_select_localidades([]);
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
	}
});

$('#btn-fibap-cancelar').on('click',function(){
	window.location.href = "fibap";
});

//********************************************   Funcionalidad de Elementos   ********************************************
$('#btn-agregar-antecedente').on('click',function(){
	$(modal_antecedente).find(".modal-title").html("Nuevo Antecedente");
	$(modal_antecedente).modal('show');
});

$('#btn-agregar-presupuesto').on('click',function(){
	$(modal_presupuesto).find(".modal-title").html("Nuevo Presupuesto");
	$(modal_presupuesto).modal('show');
});

$('#btn-agregar-distribucion').on('click',function(){
	$(modal_presupuesto).find(".modal-title").html("Nuevo Presupuesto");
	$(modal_presupuesto).modal('show');
});

$('#btn-agregar-accion').on('click',function(){
	$(modal_accion).find(".modal-title").html("Nueva Acción");
	$(modal_accion).modal('show');
});

$(modal_antecedente).on('hide.bs.modal',function(e){
	reset_modal_form(form_antecedente);
});

$(modal_presupuesto).on('hide.bs.modal',function(e){
	$(modal_presupuesto).find(".modal-title").html("Modal");
	reset_modal_form(form_presupuesto);
});

$(modal_accion).on('hide.bs.modal',function(e){
	reset_modal_form(form_accion);
});

//********************************************   Funciones   ********************************************
function llenar_datagrid_acciones(datos){
	$('#datagridAcciones > table > tbody').empty();
	var acciones = [];
	var sumas_origenes = [];
	var suma_total = 0;
	for(var indx in datos){
		var accion = {};

		var presupuesto = parseFloat(datos[indx].presupuestoRequerido);

		accion.id = datos[indx].id;
		accion.entregable = datos[indx].datos_componente.idEntregable;
		accion.tipo = datos[indx].datos_componente.idTipoComponente;
		accion.accion = datos[indx].datos_componente.idAccionComponente;
		accion.modalidad = 'pendiente';//datos[indx].cantidad;
		accion.presupuesto = '$ ' + parseFloat(presupuesto.toFixed(2)).format();
		accion.boton = '<span class="btn-link text-info boton-detalle" onClick="mostrar_detalles(' + datos[indx].id + ')"><span class="fa fa-plus-square-o"></span></span>'

		acciones.push(accion);

		for(var i in datos[indx].propuestas_financiamiento){
			var origen = datos[indx].propuestas_financiamiento[i];
			if(!sumas_origenes[origen.idOrigenFinanciamiento]){
				sumas_origenes[origen.idOrigenFinanciamiento] = 0;
			}
			sumas_origenes[origen.idOrigenFinanciamiento] += origen.cantidad;
			suma_total += origen.cantidad;
		}
	}
	
	$('.totales-financiamiento').each(function(){
		var id_origen = $(this).data('total-origen-id');
		if(sumas_origenes[id_origen]){
			$(this).text('$ ' + sumas_origenes[id_origen].format());
		}
	});

	$('#total-presupuesto-distribuido').attr('data-valor',suma_total);
	$('#total-presupuesto-distribuido').text('$ ' + suma_total.format());

	if(datos.length == 0){
		$('#datagridAcciones > table > tbody').html('<tr><td colspan="5" style="text-align:left"><i class="fa fa-info-circle"></i> No hay datos</td></tr>');
	}else{
		accionesDatagrid.cargarDatos(acciones);
	}
}

function llenar_select_jurisdicciones(datos){
	var options = $('#jurisdiccion-accion');
	options.html('<option value="">Selecciona una Jurisdicción</option>')
	$.each(datos, function() {
		options.append($("<option />").attr('data-jurisdiccion-id',this.id).val(this.clave).text(this.nombre));
	});
	options.selectpicker('refresh');
}

function llenar_select_municipios(datos){
	var options = $("#municipio-accion");
	options.html('<option value="">Selecciona un Municipio</option>')
	$.each(datos, function() {
		var localidades = '';
		for(var i in this.localidades){
			localidades += '{"clave":"' + this.localidades[i].clave + '","nombre":"' + this.localidades[i].nombre + '"}|';
		}
		localidades += '{}';
		options.append($("<option />").attr('data-localidades',localidades).val(this.clave).text(this.nombre));
	});
	options.selectpicker('refresh');
}

function llenar_select_localidades(datos){
	var options = $("#localidad-accion");
	if(datos.length){
		options.html('<option value="">Selecciona una Localidad</option>')
	}else{
		options.html('<option value="">Selecciona un Municipio</option>')
	}
	
	$.each(datos, function() {
		options.append($("<option />").val(this.clave).text(this.nombre));
	});
	options.selectpicker('refresh');
}

function llenar_datagrid_presupuestos(datos){
	var distribucion = [];
	$('#datagridPresupuesto > table > tbody').empty();
	var total_porcentaje = 0;
	var total_presup = $('#total-presupuesto-requerido').attr('data-valor');
	for(var indx in datos){
		var presupuesto = {};
		var porcentaje = (datos[indx].cantidad * 100) / parseFloat(total_presup);

		presupuesto.id = datos[indx].id;
		presupuesto.partida = datos[indx].objeto_gasto.clave;
		presupuesto.descripcion = datos[indx].objeto_gasto.descripcion;
		presupuesto.cantidad = '$ ' + datos[indx].cantidad.format();
		presupuesto.porcentaje = parseFloat(porcentaje.toFixed(2));

		total_porcentaje += parseFloat(porcentaje.toFixed(2));

		distribucion.push(presupuesto);
	}

	if(distribucion.length == 0){
		actualiza_porcentaje('#porcentaje_completo',0);
		$('#datagridPresupuesto > table > tbody').html('<tr><td colspan="5" style="text-align:left"><i class="fa fa-info-circle"></i> No hay datos</td></tr>');
	}else{
		actualiza_porcentaje('#porcentaje_completo',total_porcentaje);
		presupuestoDatagrid.cargarDatos(distribucion);
	}
}

function llenar_datagrid_distribucion(datos,total_presupuesto){
	var distribucion = [];
	$('#datagridDistribucion > table > tbody').empty();
	var total_porcentaje = 0;
	for(var indx in datos){
		var presupuesto = {};

		var porcentaje = (datos[indx].cantidad * 100) / parseInt(total_presupuesto);

		presupuesto.id = datos[indx].id;
		presupuesto.jurisdiccion = datos[indx].jurisdiccion.nombre;
		presupuesto.municipio = datos[indx].municipio;
		presupuesto.localidad = datos[indx].localidad;
		presupuesto.monto = '$ ' + datos[indx].cantidad.format();

		total_porcentaje += parseFloat(porcentaje.toFixed(2));

		distribucion.push(presupuesto);
	}

	if(distribucion.length == 0){
		actualiza_porcentaje('#porcentaje_accion',0);
		$('#datagridDistribucion > table > tbody').html('<tr><td colspan="5" style="text-align:left"><i class="fa fa-info-circle"></i> No hay datos</td></tr>');
	}else{
		actualiza_porcentaje('#porcentaje_accion',total_porcentaje);
		distribucionDataGrid.cargarDatos(distribucion);
	}

}

function ocultar_detalles(remover_contendor){
	$('#datagridAcciones > table > tbody > tr > td > span.boton-detalle > span.fa-minus-square-o').addClass('fa-plus-square-o');
	$('#datagridAcciones > table > tbody > tr > td > span.boton-detalle > span.fa-minus-square-o').removeClass('fa-minus-square-o');
	$('#datagridAcciones > table > tbody > tr.bg-info').removeClass('text-primary');
	$('#datagridAcciones > table > tbody > tr.bg-info').removeClass('bg-info');
	$('#datagridDistribucion').appendTo('#datagrid-contenedor');
	if(remover_contendor){
		$('#datagridAcciones > table > tbody > tr.contendor-desechable').remove();
	}
}

function mostrar_detalles(id){
	ocultar_detalles(false);

	if($('#datagrid-contenedor-' + id).length){
		$('#datagridAcciones > table > tbody > tr.contendor-desechable').remove();
		llenar_datagrid_distribucion([],0);
	}else{
		var parametros = {ver:'distribucion-presupuesto'};
		fibapResource.get(id,parametros,{
			_success:function(response){
				//
				$('#datagridAcciones > table > tbody > tr.contendor-desechable').remove();
				llenar_datagrid_distribucion(response.data.distribucion_presupuesto_agrupado,response.data.presupuestoRequerido);

				$('#datagridAcciones > table > tbody > tr[data-id="' +  id+ '"]').addClass('bg-info');
				$('#datagridAcciones > table > tbody > tr[data-id="' +  id+ '"]').addClass('text-primary');
				$('#datagridAcciones > table > tbody > tr[data-id="' +  id+ '"] > td > span.boton-detalle > span.fa-plus-square-o').addClass('fa-minus-square-o');
				$('#datagridAcciones > table > tbody > tr[data-id="' +  id+ '"] > td > span.boton-detalle > span.fa-plus-square-o').removeClass('fa-plus-square-o');
				
				$('#datagridAcciones > table > tbody > tr[data-id="' +  id+ '"]').after('<tr class="contendor-desechable disabled"><td class="disabled" colspan="7" id="datagrid-contenedor-' + id + '"></td></tr>');
				$('#datagridDistribucion').appendTo('#datagrid-contenedor-' + id);
				$('#datagridDistribucion').attr('data-selected-id',id);
				
				actualizar_claves_presupuesto(response.data.partidas);
			}
		});
	}
}

function actualiza_porcentaje(id,porcentaje){
	$(id).text(porcentaje + ' %');
	$(id).attr('aria-valuenow',porcentaje);
	$(id).attr('style','width:'+porcentaje + '%;');
	if(porcentaje > 100){
		$(id).addClass('progress-bar-danger');
		MessageManager.show({
			data:'El porcentaje se exedio, por favor modifique la propuesta de financiamiento o elimine uno o varios elementos en la distribución del presupuesto para corregir esto.',
			type:'ERR',
			container: '#grid_distribucion_presupuesto'
		});
	}else{
		$(id).removeClass('progress-bar-danger');
	}
}

function llenar_datagrid_antecedentes(datos){
	var antecedentes = [];
	$('#datagridAntecedentes > table > tbody').empty();

	for(var indx in datos){
		var antecedente = {};

		antecedente.id = datos[indx].id;
		antecedente.anio = datos[indx].anio;
		antecedente.autorizado = datos[indx].autorizado;
		antecedente.ejercido = datos[indx].ejercido;
		antecedente.porcentaje = parseFloat(datos[indx].porcentaje.toFixed(2));
		antecedente.fechaCorte = datos[indx].fechaCorte;

		antecedentes.push(antecedente);
	}

	if(antecedentes.length == 0){
		$('#datagridAntecedentes > table > tbody').html('<tr><td colspan="6" style="text-align:left"><i class="fa fa-info-circle"></i> No hay datos</td></tr>');
	}else{
		antecedenteDatagrid.cargarDatos(antecedentes);
	}

}

function habilitar_tabs(){
	$('#tab-link-antecedentes-fibap').attr('data-toggle','tab');
	$('#tab-link-antecedentes-fibap').parent().removeClass('disabled');
	$('#tab-link-presupuesto-fibap').attr('data-toggle','tab');
	$('#tab-link-presupuesto-fibap').parent().removeClass('disabled');
	$('#tab-link-acciones-fibap').attr('data-toggle','tab');
	$('#tab-link-acciones-fibap').parent().removeClass('disabled');
}

function habilitar_meses(fechaInicio,fechaFinal){
	if(typeof(fechaInicio) == 'string'){
		var inicio = fechaInicio;
		var fin = fechaFinal;
	}else{
		var inicio = fechaInicio.date;
		var fin = fechaFinal.date;
	}

	var primer_mes = parseInt(inicio.substring(5,7))
	var ultimo_mes = parseInt(fin.substring(5,7))
	$('.presupuesto-mes').each(function(){
		if($(this).attr('data-presupuesto-mes') < primer_mes){
			$(this).prop('disabled',true);
		}else if($(this).attr('data-presupuesto-mes') > ultimo_mes){
			$(this).prop('disabled',true);
		}else{
			$(this).prop('disabled',false);
		}
	});
}

function actualizar_claves_presupuesto(datos){
	for(var i in datos){
		var id = parseInt(i) + 1;
		$('#calendarizado-presupuesto .clave-partida-'+id).text(datos[i].clave);
		$('#calendarizado-presupuesto .clave-partida-'+id).attr('title',datos[i].descripcion);
		$('#datagridDistribucion').attr('data-partida-' + id + '-id',datos[i].id);
	}
}

function actualizar_tabla_meses(jurisdicciones){
	var tabla_id = '#tabla-distribucion-mes';
	var meses = ['ENE','FEB','MAR','ABR','MAY','JUN','JUL','AGO','SEP','OCT','NOV','DIC'];

	var html = '';
	var indx,idx;
	var llaves = Object.keys(jurisdicciones).sort(); //Se ordenan las llaves
	for(var index in llaves){
		indx = llaves[index];
		html += '<tr>';
		html += '<th>'+jurisdicciones[indx]+'</th>';
		for(idx in meses){
			id_mes = parseInt(idx) + 1;
			html += '<td><input id="mes-distribucion-'+indx+'-'+id_mes+'" name="mes-distribucion['+indx+']['+id_mes+']" type="number" class="form-control input-sm presupuesto-mes" data-presupuesto-mes="'+id_mes+'" data-presupuesto-jurisdiccion="'+indx+'" data-presupuesto-id=""></td>';
		}
		html += '</tr>';
	}

	$(tabla_id + ' tbody').empty();
	$(tabla_id + ' tbody').html(html);
	//actualizar_eventos_metas();
}

function bloquear_controles(){
	//$('.control-bloqueado').prop('disabled',true);
	$('.control-bloqueado').each(function(){
		$(this).prop('disabled',true);
		$('label[for="' + $(this).attr('id') + '"]').prepend('<span class="fa fa-lock"></span> ');
	});
}

function cambiar_icono_tabs(tab_id,icono){
	$(tab_id + ' span.fa').removeClass(function (index, css) {
    	return (css.match (/(^|\s)fa-\S+/g) || []).join(' ');
	});
	$(tab_id + ' span.fa').addClass(icono);
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

function reset_modal_form(formulario){
    $('#'+formulario).get(0).reset();
    Validation.cleanFormErrors('#'+formulario);
    if(formulario == form_antecedente){
    	$(modal_antecedente + ' .alert').remove();
    	$('#id-antecedente').val('');
    }
    if(formulario == form_presupuesto){
    	$('#' + formulario + ' input[type="hidden"]').val('');
    	$('#' + formulario + ' input[type="hidden"]').change();
    	$('#' + formulario + ' .selectpicker').change();
    	$('#municipio-accion').change();
    	$('#localidad-accion').change();

    	$('.presupuesto-mes').each(function(){
			$(this).attr('data-presupuesto-id','');
		});
		$('.meta-mes').each(function(){
			$(this).attr('data-meta-id','');
		});

    	//$('#id-presupuesto').val('');
    	$(modal_presupuesto + ' .alert').remove();
    }
    if(formulario == form_accion){
    	$('#' + formulario + ' .selectpicker').change();
    	$('#' + formulario + ' input[type="hidden"]').val('');
    	$('#' + formulario + ' input[type="hidden"]').change();
    	$('.accion-origen-financiamiento').each(function(){
    		$(this).attr('data-captura-id','');
    	});
    	//$('#objeto-gasto-presupuesto').selectpicker('refresh');
    }
}

function sumar_valores(identificador,resultado){
	var sumatoria = 0;
	$(identificador).each(function(){
		sumatoria += parseFloat($(this).val()) || 0;
	});
	$(resultado).val(sumatoria).change();
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