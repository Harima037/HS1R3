
/*=====================================

    # Nombre:
        caratula.js

    # Módulo:
        expediente/caratula

    # Descripción:
        Para el formulario de captura (Caratula de captura) de un proyecto

=====================================*/
// Declaracion de variables
var proyectoResource = new RESTfulRequests(SERVER_HOST+'/v1/proyectos');
var programaResource = new RESTfulRequests(SERVER_HOST+'/v1/programa-presupuestario-clave');
var catalogosAlineacionResource = new RESTfulRequests(SERVER_HOST+'/v1/ped-estrategia-por-alineacion');

var componenteDatagrid = new Datagrid("#datagridComponentes",proyectoResource);
var actividadDatagrid = new Datagrid('#datagridActividades',proyectoResource);
var desgloseComponenteDatagrid = new Datagrid('#datagridDesgloseComponente',proyectoResource);
var beneficiarioDatagrid = new Datagrid("#datagridBeneficiarios",proyectoResource);

componenteDatagrid.init();
actividadDatagrid.init();
desgloseComponenteDatagrid.init();
beneficiarioDatagrid.init();

metasMesCSV.init();

var comentarios = { componentes:{}, actividades:{} };

var modal_componente = '#modalComponente';
var modal_actividad = '#modalActividad';
var modal_beneficiario = '#modalBeneficiario';
var modal_cancelar_proyecto = '#modalCancelarProyecto';

var grid_componentes = '#datagridComponentes';
var grid_actividades = '#datagridActividades';
var grid_desglose = '#datagridDesgloseComponente';
var grid_beneficiarios = '#datagridBeneficiarios';

var form_caratula = '#form_caratula';
var form_componente = '#form_componente';
var form_actividad = '#form_actividad';
var form_beneficiario = '#form_beneficiario';
var form_fuente_informacion = '#form_fuente_informacion';
var form_cancelacion_proyecto = '#form_cancelacion_proyecto';

$('.chosen-one').chosen({width:'100%',search_contains:true,enable_split_word_search:true,no_results_text: "No se econtraron resultados para "});

window.onload = function () { 
	$('#mensaje-espera').addClass('hidden');
	$('#panel-principal-formulario').removeClass('hidden');
};
//***********************     Funcionalidad ejecutada al cargar la página    ************************************
if($('#id').val()){
	//load data
	proyectoResource.get($('#id').val(),null,{
        _success: function(response){
        	inicializar_comportamiento_caratula();

        	if(response.data.lider_proyecto){
        		$('#lbl-lider-proyecto').html(response.data.lider_proyecto.nombre + '<br><small class="text-muted">'+response.data.lider_proyecto.cargo+'</small>');
        	}else{
        		$('#lbl-lider-proyecto').html('<span class="text-muted">No asignado</span>')
        	}
			if(response.data.jefe_inmediato){
				$('#lbl-jefe-inmediato').html(response.data.jefe_inmediato.nombre + '<br><small class="text-muted">'+response.data.jefe_inmediato.cargo+'</small>');
			}else{
				$('#lbl-jefe-inmediato').html('<span class="text-muted">No asignado</span>')
			}
			if(response.data.jefe_planeacion){
				$('#lbl-jefe-planeacion').html(response.data.jefe_planeacion.nombre + '<br><small class="text-muted">'+response.data.jefe_planeacion.cargo+'</small>');
			}else{
				$('#lbl-jefe-planeacion').html('<span class="text-muted">No asignado</span>')
			}
			if(response.data.coordinador_grupo_estrategico){
				$('#lbl-coordinador-grupo').html(response.data.coordinador_grupo_estrategico.nombre + '<br><small class="text-muted">'+response.data.coordinador_grupo_estrategico.cargo+'</small>');
			}else{
				$('#lbl-coordinador-grupo').html('<span class="text-muted">No asignado</span>')
			}

			if(response.data.cancelado){
				$('#btn-cancelar-proyecto').hide();
				$('#span-proyecto-cancelado').show();
				$('#datagridCaratulas').removeClass('panel-default');
				$('#datagridCaratulas').addClass('panel-danger');
			}

            $('#nombretecnico').val(response.data.nombreTecnico);
            $('#ejercicio').val(response.data.ejercicio);

			$('#tipoproyecto').val(response.data.idTipoProyecto);
			$('#fechainicio').val(response.data.fechaInicio);
			$('#fechatermino').val(response.data.fechaTermino);
			$('#finalidadproyecto').val(response.data.finalidadProyecto);

			$('#unidad_responsable').text(response.data.unidadResponsable);
            $('#finalidad').text(response.data.finalidad);
            $('#funcion').text(response.data.funcion);
            $('#subfuncion').text(response.data.subFuncion);
            $('#subsubfuncion').text(response.data.subSubFuncion);
            $('#programa_sectorial').text(response.data.programaSectorial);
            $('#programa_presupuestario').text(response.data.programaPresupuestario);
            //$('#programa_especial').text(response.data.programaEspecial);
            $('#origen_asignacion').text(response.data.origenAsignacion);
            $('#actividad_institucional').text(response.data.actividadInstitucional);
            $('#proyecto_estrategico').text(response.data.proyectoEstrategico);
            $('#no_proyecto_estrategico').text(("000" + response.data.numeroProyectoEstrategico).slice(-3));
            
            $('#unidadresponsable').val(response.data.unidadResponsable);
            $('#funciongasto').val(response.data.finalidad+ '.' + response.data.funcion+ '.' + response.data.subFuncion+ '.' + response.data.subSubFuncion);
            $('#programasectorial').val(response.data.programaSectorial);
            $('#programapresupuestario').val(response.data.programaPresupuestario);
			$('#programapresupuestario').change();
            //$('#programaespecial').val(response.data.programaEspecial);
            $('#origenasignacion').val(response.data.origenAsignacion);
            $('#actividadinstitucional').val(response.data.actividadInstitucional);
            $('#proyectoestrategico').val(response.data.proyectoEstrategico);

            if($('input#numeroproyectoestrategico').length){
            	$('#numeroproyectoestrategico').val(response.data.numeroProyectoEstrategico);
            }else{
            	$('#numeroproyectoestrategico').text(("000" + response.data.numeroProyectoEstrategico).slice(-3));
            }

            $('#cobertura').val(response.data.idCobertura);
            $('#cobertura').chosen().change();

            deshabilita_paneles($('#cobertura').val());

            if(response.data.claveMunicipio){
				$('#municipio').val(response.data.claveMunicipio);
            }

            if(response.data.claveRegion){
            	$('#region').val(response.data.claveRegion);
            }

            $('#tipoaccion').val(response.data.idTipoAccion);

            $('#vinculacionped').val(response.data.idObjetivoPED);
			$('#estrategiapnd').val(response.data.idEstrategiaNacional);
			$('#objetivoestrategico').val(response.data.idObjetivoEstrategico);
			$('#alineacion').val(response.data.idObjetivoPED);
			
			llenar_datos_objetico_ped(response.data.objetivo_ped);

			llenar_select_estrategias(response.data.estrategias);
			$('#estrategiaestatal').val(response.data.idEstrategiaEstatal);

            $(form_caratula + ' .chosen-one').trigger('chosen:updated');

            actualizar_grid_beneficiarios(response.data.beneficiarios);

            actualizar_tabla_metas('actividad',response.data.jurisdicciones);
            actualizar_tabla_metas('componente',response.data.jurisdicciones);

            $('#tablink-componentes').attr('data-toggle','tab');
			$('#tablink-componentes').parent().removeClass('disabled');
			$('#tablink-beneficiarios').attr('data-toggle','tab');
			$('#tablink-beneficiarios').parent().removeClass('disabled');
			$('#tablink-fuentes-financiamiento').attr('data-toggle','tab');
			$('#tablink-fuentes-financiamiento').parent().removeClass('disabled');
			$('#tablink-normatividad').attr('data-toggle','tab');
			$('#tablink-normatividad').parent().removeClass('disabled');

			$('#fuente-informacion').val(response.data.fuenteInformacion);
			llenar_responsables(response.data.responsables);
			$('#responsable').val(response.data.idResponsable);
			$('#responsable').change();
			$('#responsable').trigger('chosen:updated');
			
			fuenteFinanciamiento.init(proyectoResource,$('#id').val());
			if(response.data.fuentes_financiamiento.length){
				fuenteFinanciamiento.llenar_datagrid(response.data.fuentes_financiamiento);
			}

			caratulaArchivos.badgeElement = $('#tablink-normatividad > span');
			caratulaArchivos.cargarListadoArchivos();

			if(response.data.idEstatusProyecto != 1 && response.data.idEstatusProyecto != 3){
				bloquear_controles();
			}else if(response.data.idEstatusProyecto == 3){
				mostrar_comentarios(response.data.comentarios);
			}

			actualizar_grid_componentes(response.data.componentes);
        }
    });
}else{
	inicializar_comportamiento_caratula();
	deshabilita_paneles('');
}

function llenar_responsables(datos){
	var html = '<option value="">Selecciona un responsable</option>';
	for(var i in datos){
		var responsable = datos[i];
		html += '<option value="'+responsable.id+'" data-cargo="'+responsable.cargo+'">';
		html += responsable.nombre;
		html += '</option>';
	}
	$('#responsable').html(html);
}

function inicializar_comportamiento_caratula(){
	$('#responsable').on('change',function(){
		if($(this).val()){
			var cargo = $('#responsable option:selected').attr('data-cargo');
			$('#ayuda-responsable').text(cargo);
		}else{
			$('#ayuda-responsable').text('');
		}
	});

	$('#entregable').on('change',function(){
		habilita_opciones('#tipo-entregable',$(this).val(),'NA');
		habilita_opciones('#accion-entregable',$(this).val());
	});
	$('.control-espejo').each(function(){
		var control_id = $(this).data('espejo-id');
		$(control_id).on('change',function(){
			$('.control-espejo[data-espejo-id="'+control_id+'"]').text($(this).val());
		});
	});
	$('#denominador-componente').on('keyup',function(){
		ejecutar_formula('componente');
	});
	$('#denominador-actividad').on('keyup',function(){
		ejecutar_formula('actividad');
	});
	$('.benef-totales').on('keyup',function(){ $(this).change() });
	$('.benef-totales').on('change',function(){
		if($(this).attr('id') == 'totalbeneficiariosf'){
			var totalm = parseInt($('#totalbeneficiariosm').val()) || 0;
			var total = totalm + (parseInt($(this).val()) || 0);
			$('#totalbeneficiarios').text(total.format());
		}
		if($(this).attr('id') == 'totalbeneficiariosm'){
			var totalf = parseInt($('#totalbeneficiariosf').val()) || 0;
			var total = totalf + (parseInt($(this).val()) || 0);
			$('#totalbeneficiarios').text(total.format());
		}
	});
	$('.fem,.masc').on('keyup',function(){ $(this).change(); });
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
	//fuenteFinanciamiento.init(proyectoResource,$('#id').val());
}

/***********************************           Comportamiento de los datagrids          ***********************************/
function editar_fuente_financiamiento(e){
	fuenteFinanciamiento.editar_fuente(e);
}

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
 			$('#denominador-componente').val(response.data.valorDenominador).change();
 			$('#linea-base-componente').val(response.data.lineaBase);
 			$('#anio-base-componente').val(response.data.anioBase);
 			$('#formula-componente').val(response.data.idFormula);
			$('#dimension-componente').val(response.data.idDimensionIndicador);
			$('#frecuencia-componente').val(response.data.idFrecuenciaIndicador);
			$('#tipo-ind-componente').val(response.data.idTipoIndicador);
			$('#unidad-medida-componente').val(response.data.idUnidadMedida);
			$('#comportamiento-componente').val(response.data.idComportamientoAccion);
			$('#tipo-valor-meta-componente').val(response.data.idTipoValorMeta);

			$('#formula-componente').trigger('chosen:updated');
			$('#dimension-componente').trigger('chosen:updated');
			$('#frecuencia-componente').trigger('chosen:updated');
			$('#tipo-ind-componente').trigger('chosen:updated');
			$('#unidad-medida-componente').trigger('chosen:updated');
			$('#comportamiento-componente').trigger('chosen:updated');
			$('#tipo-valor-meta-componente').trigger('chosen:updated');

			$('#entregable').val(response.data.idEntregable);
			$('#entregable').chosen().change();
 			$('#tipo-entregable').val(response.data.idEntregableTipo || 'NA');
 			$('#accion-entregable').val(response.data.idEntregableAccion);

			$('#entregable').trigger('chosen:updated');
			$('#tipo-entregable').trigger('chosen:updated');
 			$('#accion-entregable').trigger('chosen:updated');

            $('#id-componente').val(response.data.id);
    		$('#tablink-componente-actividades').attr('data-toggle','tab');
			$('#tablink-componente-actividades').parent().removeClass('disabled');

			actualizar_metas('componente',response.data.metas_mes);
			
			var tab_id = cargar_formulario_componente_actividad('componente',response.data);

			$(tab_id).tab('show');

			actualizar_grid_actividades(response.data.actividades);

			if(comentarios.componentes[response.data.id]){
				mostrar_comentarios_alt(comentarios.componentes[response.data.id]);
			}

			if(response.data.idEntregable){
				busqueda_rapida_desglose(e,{});
			}else{
				actualizar_grid_desglose([]);
				$(modal_componente).modal('show');
			}
        }
    },'Cargando los datos del componente');
}

///*****  Comportamiento_del_grid_de_Desglose
$(grid_desglose + " .btn-next-rows").off('click');
$(grid_desglose + " .btn-next-rows").on('click', function(e) {           
	e.preventDefault();	
	var pagina  = parseInt(desgloseComponenteDatagrid.getPagina())+1;

    if(desgloseComponenteDatagrid.getMaxPagina()>=pagina){
    	buscar_pagina_desglose(pagina);
    }
});
$(grid_desglose + " .btn-back-rows").off('click');
$(grid_desglose + " .btn-back-rows").on('click',function(e){
	e.preventDefault();	
	var pagina  = parseInt(desgloseComponenteDatagrid.getPagina())-1;
    if(pagina<1)
      pagina = 1;
  	
	buscar_pagina_desglose(pagina);
});
$(grid_desglose + " .btn-go-first-rows").off('click');
$(grid_desglose + " .btn-go-first-rows").on('click',function(e){           
	e.preventDefault();
	buscar_pagina_desglose(1);
});
$(grid_desglose + " .btn-go-last-rows").off('click');
$(grid_desglose + " .btn-go-last-rows").on('click',function(e){
	e.preventDefault();
	buscar_pagina_desglose(desgloseComponenteDatagrid.getMaxPagina() || 1);
});
$(grid_desglose + " .txt-go-page").off('keydown');
$(grid_desglose + " .txt-go-page").on('keydown', function(event){
	if (event.which == 13) {
		buscar_pagina_desglose($(this.selector + " .txt-go-page").val());
   	}
});
$(grid_desglose + " .txt-quick-search").off('keydown');
$(grid_desglose + " .txt-quick-search").on('keydown', function(event){
	if (event.which == 13) {
		busqueda_rapida_desglose($('#id-componente').val(),{buscar:$(this).val()});
   	}
});
$(grid_desglose + " .btn-quick-search").off('click');
$(grid_desglose + " .btn-quick-search").on('click',function(e){
	e.preventDefault();		
	busqueda_rapida_desglose($('#id-componente').val(),{buscar:$(grid_desglose + " .txt-quick-search").val()});
});
function buscar_pagina_desglose(pagina){
	var max_pagina = desgloseComponenteDatagrid.getMaxPagina() || 1;
	if(pagina > max_pagina){
		pagina = max_pagina;
	}else if(pagina <= 0){
		pagina = 1;
	}
	busqueda_rapida_desglose($('#id-componente').val(),{pagina:pagina})
}
function busqueda_rapida_desglose(id_componente,parametros){
	var param_buscar = {ver:'lista-desglose',pagina:1};
	if(parametros.buscar){
		param_buscar.buscar = parametros.buscar;
	}
	if(parametros.pagina){
		param_buscar.pagina = parametros.pagina;
	}
	proyectoResource.get(id_componente,param_buscar,{
		_success: function(response){
			actualizar_grid_desglose(response.data, response.total, param_buscar.pagina);
			$(modal_componente).modal('show');
		}
	},'Cargando el desglose del componente...');
}
///***** Termina Comportamiento_del_grid_de_Desglose

function cargar_formulario_componente_actividad(identificador,datos){
	var errores_metas = false;
	if($('#trim1-'+identificador).val() != parseFloat(datos.numeroTrim1) && datos.numeroTrim1 != null){
		Validation.printFieldsErrors('trim1-'+identificador,'Valor anterior de '+datos.numeroTrim1+'.');
		errores_metas = true;
	}
	if($('#trim2-'+identificador).val() != parseFloat(datos.numeroTrim2) && datos.numeroTrim2 != null){
		Validation.printFieldsErrors('trim2-'+identificador,'Valor anterior de '+datos.numeroTrim2+'.');
		errores_metas = true;
	}
	if($('#trim3-'+identificador).val() != parseFloat(datos.numeroTrim3) && datos.numeroTrim3 != null){
		Validation.printFieldsErrors('trim3-'+identificador,'Valor anterior de '+datos.numeroTrim3+'.');
		errores_metas = true;
	}
	if($('#trim4-'+identificador).val() != parseFloat(datos.numeroTrim4) && datos.numeroTrim4 != null){
		Validation.printFieldsErrors('trim4-'+identificador,'Valor anterior de '+datos.numeroTrim4+'.');
		errores_metas = true;
	}
	if($('#numerador-'+identificador).val() != parseFloat(datos.valorNumerador) && datos.valorNumerador != null){
		Validation.printFieldsErrors('numerador-'+identificador,'Valor anterior de '+datos.valorNumerador+'.');
		errores_metas = true;
	}
	if($('#meta-'+identificador).val() != parseFloat(datos.metaIndicador) && datos.metaIndicador != null){
		Validation.printFieldsErrors('meta-'+identificador,'Valor anterior de '+datos.metaIndicador+'.');
		errores_metas = true;
	}

	if(errores_metas){
		if(identificador == 'actividad'){
			var modal_identificador = modal_actividad;
		}else{
			var modal_identificador = modal_componente;
		}
		MessageManager.show({data:'Se ha detectado una irregularidad en los totales de los trimestres, esto puede deberse a que las jurisdicciones pertenecientes a la cobertura del proyecto cambiaron, por favor corrobore que la información sea correcta y de ser necesario actualize los valores requeridos para poder resolver el conflicto.',container:modal_identificador + ' .modal-body',type:'ADV'});
		return '#tablink-'+identificador+'-desgloce-metas';
	}else{
		return '#tablink-'+identificador+'-actividades';
	}
}

function editar_beneficiario(e){
	var parametros = {ver:'beneficiario','id-proyecto':$('#id').val()}
	proyectoResource.get(e,parametros,{
		_success: function(response){
			$(modal_beneficiario).find('.modal-title').html('Editar Beneficiario');

			if($('#datagridBeneficiarios tr[data-id="'+response.data[0].idTipoBeneficiario+'"]').attr('data-comentario')){
				var comentario = $('#datagridBeneficiarios tr[data-id="'+response.data[0].idTipoBeneficiario+'"]').attr('data-comentario');
				MessageManager.show({data:comentario,container: modal_beneficiario + ' .modal-body',type:'ADV'});
			}

			$('#tipobeneficiario').val(response.data[0].idTipoBeneficiario);
            $('#tipobeneficiario').trigger('chosen:updated');

			$('#tipocaptura').val(response.data[0].idTipoCaptura);
            $('#tipocaptura').trigger('chosen:updated');

            $('#id-beneficiario').val(response.data[0].idTipoBeneficiario);

            var sexo;
            var total = 0;
            var beneficiarios = {'f':{},'m':{}};

		    for(var i in response.data){
		    	beneficiarios[response.data[i].sexo] = response.data[i];
		    }
		    
		    for(var sexo in beneficiarios ){
		        total += parseInt(beneficiarios[sexo].total) || 0;
		        $('#totalbeneficiarios'+sexo).val(beneficiarios[sexo].total || 0);
		        $('#urbana'+sexo).val(beneficiarios[sexo].urbana || 0);
		        $('#rural'+sexo).val(beneficiarios[sexo].rural || 0);
		        $('#mestiza'+sexo).val(beneficiarios[sexo].mestiza || 0);
		        $('#indigena'+sexo).val(beneficiarios[sexo].indigena || 0);
		        //$('#inmigrante'+sexo).val(beneficiarios[sexo].inmigrante || 0);
		        //$('#otros'+sexo).val(beneficiarios[sexo].otros || 0);
		        $('#muyalta'+sexo).val(beneficiarios[sexo].muyAlta || 0);
		        $('#alta'+sexo).val(beneficiarios[sexo].alta || 0);
		        $('#media'+sexo).val(beneficiarios[sexo].media || 0);
		        $('#baja'+sexo).val(beneficiarios[sexo].baja || 0);
		        $('#muybaja'+sexo).val(beneficiarios[sexo].muyBaja || 0);
		    }
            $('#totalbeneficiarios').text(total.format());
            cargar_totales();
			$(modal_beneficiario).modal('show');
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
 			$('#denominador-actividad').val(response.data.valorDenominador).change();
 			$('#linea-base-actividad').val(response.data.lineaBase);
 			$('#anio-base-actividad').val(response.data.anioBase);

 			$('#meta-actividad').val(response.data.metaIndicador).change();
 			$('#trim1-actividad').val(response.data.numeroTrim1).change();
 			$('#trim2-actividad').val(response.data.numeroTrim2).change();
 			$('#trim3-actividad').val(response.data.numeroTrim3).change();
 			$('#trim4-actividad').val(response.data.numeroTrim4).change();
 			$('#numerador-actividad').val(response.data.valorNumerador).change();

 			$('#formula-actividad').val(response.data.idFormula);
			$('#dimension-actividad').val(response.data.idDimensionIndicador);
			$('#frecuencia-actividad').val(response.data.idFrecuenciaIndicador);
			$('#tipo-ind-actividad').val(response.data.idTipoIndicador);
			$('#unidad-medida-actividad').val(response.data.idUnidadMedida);
			$('#comportamiento-actividad').val(response.data.idComportamientoAccion);
			$('#tipo-valor-meta-actividad').val(response.data.idTipoValorMeta);

			$('#formula-actividad').trigger('chosen:updated');
			$('#dimension-actividad').trigger('chosen:updated');
			$('#frecuencia-actividad').trigger('chosen:updated');
			$('#tipo-ind-actividad').trigger('chosen:updated');
			$('#unidad-medida-actividad').trigger('chosen:updated');
			$('#comportamiento-actividad').trigger('chosen:updated');
			$('#tipo-valor-meta-actividad').trigger('chosen:updated');

			$(form_actividad + ' .metas-mes').attr('data-meta-id','');

			actualizar_metas('actividad',response.data.metas_mes);

			var tab_id = cargar_formulario_componente_actividad('actividad',response.data);

			if(comentarios.actividades[response.data.id]){
				mostrar_comentarios_alt(comentarios.actividades[response.data.id]);
			}

			$(tab_id).tab('show');

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
$('#btn-beneficiario-guardar').on('click',function(){
	Validation.cleanFormErrors(form_beneficiario);
	var parametros = $(form_beneficiario).serialize();
	parametros = parametros + '&guardar=beneficiario&id-proyecto=' + $('#id').val();

	if($(form_beneficiario + ' #id-beneficiario').val()){
		proyectoResource.put($(form_beneficiario + ' #id-beneficiario').val(),parametros,{
			_success: function(response){
				MessageManager.show({data:'Datos de los beneficiarios almacenados con éxito',type:'OK',timer:3});
	            $(modal_beneficiario).modal('hide');
				actualizar_grid_beneficiarios(response.data);
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
				MessageManager.show({data:'Datos de los beneficiarios almacenados con éxito',type:'OK',timer:3});
	            $(modal_beneficiario).modal('hide');
				actualizar_grid_beneficiarios(response.data);
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
$('#btn-actividad-guardar').on('click',function(){
	Validation.cleanFormErrors(form_actividad);
	var parametros = $(form_actividad).serialize();
	parametros += '&guardar=actividad&id-componente=' + $('#id-componente').val();
	parametros += '&id-proyecto='+$('#id').val();

	if($('#id-actividad').val()){
		var cadena_metas = '';
		$(form_actividad + ' .metas-mes').each(function(){
			if($(this).attr('data-meta-id')){
				cadena_metas = cadena_metas + '&mes-actividad-id['+$(this).attr('data-meta-jurisdiccion')+']['+$(this).attr('data-meta-mes')+']='+$(this).attr('data-meta-id');
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
	if($('#id').val()){
		Validation.cleanFormErrors(form_caratula);
		var parametros = 'guardar=validar-proyecto';

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

$('#btn-fuente-informacion-guardar').on('click',function(){
	Validation.cleanFormErrors(form_fuente_informacion);

	var parametros = $(form_fuente_informacion).serialize();
	parametros = parametros + '&guardar=fuenteinformacion&id-proyecto='+$('#id').val();

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
	Validation.cleanFormErrors(form_caratula);

	/*if(checar_error_totales()){
		return false;
	}*/

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
	            //$('#lbl-lider-proyecto').text(response.data.liderProyecto);
	            $('#lbl-lider-proyecto').html(response.data.liderProyecto + '<br><small class="text-muted">'+response.data.liderProyectoCargo+'</small>');

	            if(response.data.responsables){
	            	llenar_responsables(response.data.responsables);
					$('#responsable').val(response.data.idResponsable);
					$('#responsable').change();
					$('#responsable').trigger('chosen:updated');
	            }

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
	            var no_proyecto = ("000" + (response.data.numeroProyectoEstrategico || 0)).slice(-3);
	            $(form_caratula + ' #no_proyecto_estrategico').text(no_proyecto);

	            if($('input#numeroproyectoestrategico').length){
	            	$(form_caratula + ' #numeroproyectoestrategico').val(response.data.numeroProyectoEstrategico);
	            }else{
	            	$(form_caratula + ' #numeroproyectoestrategico').text(no_proyecto);
	            }

	            fuenteFinanciamiento.init(proyectoResource,$('#id').val());

				caratulaArchivos.badgeElement = $('#tablink-normatividad > span');
				caratulaArchivos.cargarListadoArchivos();

	            if(response.data.componentes){
	            	actualizar_grid_componentes(response.data.componentes);
	            }

	            actualizar_grid_beneficiarios(response.data.beneficiarios);

	            actualizar_tabla_metas('actividad',response.data.jurisdicciones);
            	actualizar_tabla_metas('componente',response.data.jurisdicciones);

            	if(response.data.responsables){
	            	llenar_responsables(response.data.responsables);
					$('#responsable').trigger('chosen:updated');
	            }

            	//$('#lbl-lider-proyecto').text(response.data.liderProyecto);
            	$('#lbl-lider-proyecto').html(response.data.liderProyecto + '<br><small class="text-muted">'+response.data.liderProyectoCargo+'</small>');

	            $('#tablink-componentes').attr('data-toggle','tab');
				$('#tablink-componentes').parent().removeClass('disabled');
				$('#tablink-beneficiarios').attr('data-toggle','tab');
				$('#tablink-beneficiarios').parent().removeClass('disabled');
				$('#tablink-fuentes-financiamiento').attr('data-toggle','tab');
				$('#tablink-fuentes-financiamiento').parent().removeClass('disabled');
				$('#tablink-normatividad').attr('data-toggle','tab');
				$('#tablink-normatividad').parent().removeClass('disabled');
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
	window.location.href = SERVER_HOST+'/expediente/proyectos';
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
$('.btn-agregar-beneficiario').on('click',function(){
	$(modal_beneficiario).find(".modal-title").html("Nuevo Beneficiario");
	$(modal_beneficiario).modal('show');
});

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

	/*if(parseInt(componentes[0]) >= parseInt(componentes[1])){
		MessageManager.show({code:'S03',data:"Los componentes para este proyecto ya estan completos.",timer:2});
	}else{*/
		$('#tablink-componente-actividades').attr('data-toggle','');
		$('#tablink-componente-actividades').parent().addClass('disabled');
		$('#lista-tabs-componente a:first').tab('show');
		$(modal_componente).find(".modal-title").html("Nuevo Componente");
		$(modal_componente).modal('show');
	//}
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

$('#alineacion').on('change',function(){
	var clave = $('#alineacion').val();
	if(clave){
		catalogosAlineacionResource.get(clave,null,{
			_success: function(response){
				
				$('#estrategiaestatal').val('');
				llenar_select_estrategias(response.data.estrategias)
				
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

function llenar_select_estrategias(estrategias){
	var html_opciones = '<option value="">Selecciona una Estrategia</option>';
	for(var i in estrategias){
		var estrategia = estrategias[i];
		html_opciones += '<option value="'+estrategia.id+'">';
		html_opciones += estrategia.claveEstrategia + ' ' + estrategia.descripcion;
		html_opciones += '</option>';
	}
	$('#estrategiaestatal').html('');
	$('#estrategiaestatal').html(html_opciones);
	$('#estrategiaestatal').trigger('chosen:updated');
}

function eliminarArchivo(id){
	caratulaArchivos.eliminarArchivo(id);
}

$('#programapresupuestario').on('change',function(){
	actualiza_clave('programa_presupuestario',$(this).val(),'---');
	if($(this).val()){
		programaResource.get($(this).val(),null,{
			_success: function(response){
				$('#titulo-programa-presupuestario').text(response.data.programa_presupuestario.descripcion);
				$('#panel-programa-seleccionado').show();
				$('#tabla-indicadores-programa-presupuestario tbody').html('');
				var html_indicadores= '';
				for(var i in response.data.indicadores_descripcion){
					var indicador = response.data.indicadores_descripcion[i];
					html_indicadores += '<tr>';
					html_indicadores += '<td>'+indicador.claveTipoIndicador+'</td>';
					html_indicadores += '<td>'+indicador.descripcionIndicador+'</td>';
					html_indicadores += '<td>'+indicador.unidadMedida+'</td>';
					html_indicadores += '</tr>';
				}
				$('#tabla-indicadores-programa-presupuestario tbody').html(html_indicadores);
			}
		});	
	}else{
		$('#panel-programa-seleccionado').hide();
		$('#tabla-indicadores-programa-presupuestario tbody').html('');
		$('#titulo-programa-presupuestario').text('');
	}
});

$('#origenasignacion').on('change',function(){
	actualiza_clave('origen_asignacion',$(this).val(),'--');
});
/*$('#programaespecial').on('change',function(){
	actualiza_clave('programa_especial',$(this).val(),'---');
});*/
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

$(modal_beneficiario).on('hide.bs.modal',function(e){
	reset_modal_form(form_beneficiario);
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
			if($(this).attr('data-meta-id')){
				cadena_metas = cadena_metas + '&mes-componente-id['+$(this).attr('data-meta-jurisdiccion')+']['+$(this).attr('data-meta-mes')+']='+$(this).attr('data-meta-id');
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
					actualizar_metas('componente',response.metas);
				}
				actualizar_grid_componentes(response.componentes);
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
					actualizar_metas('componente',response.metas);
				}
				actualizar_grid_componentes(response.componentes);
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
					proyectoResource.delete(rows,{'rows': rows, 'eliminar': 'actividad', 'id-componente': $('#id-componente').val(), 'id-proyecto': $('#id').val()},{
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


$(grid_beneficiarios + " .btn-delete-rows").unbind('click');
$(grid_beneficiarios + " .btn-delete-rows").on('click',function(e){
	e.preventDefault();
	var rows = [];
	var contador= 0;
    
    $(this).parents(".datagrid").find("tbody").find("input[type=checkbox]:checked").each(function () {
		contador++;
        rows.push($(this).parent().parent().data("id"));
	});

	if(contador>0){
		Confirm.show({
				titulo:"Eliminar beneficiario",
				//botones:[], 
				mensaje: "¿Estás seguro que deseas eliminar el(los) beneficiario(s) seleccionado(s)?",
				//si: 'Actualizar',
				//no: 'No, gracias',
				callback: function(){
					proyectoResource.delete(rows,{'rows': rows, 'eliminar': 'beneficiario', 'id-proyecto': $('#id').val()},{
                        _success: function(response){ 
                        	actualizar_grid_beneficiarios(response.beneficiarios);
                        	MessageManager.show({data:'Beneficiario(s) eliminado(s) con éxito.',timer:3});
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
	var meses = [];
	meses[1] = ['ENE','FEB','MAR'];
	meses[2] = ['ABR','MAY','JUN'];
	meses[3] = ['JUL','AGO','SEP'];
	meses[4] = ['OCT','NOV','DIC'];

	var llaves = Object.keys(jurisdicciones).sort(); //Se ordenan las llaves

	for (var i = 1; i <= 4; i++) {
		var html = '';
		var indx,idx;
		var tabla_id = '#tabla-'+identificador+'-metas-mes-'+i;
		for(var index in llaves){
			indx = llaves[index];
			html += '<tr>';
			html += '<th>'+jurisdicciones[indx]+'</th>';
			for(idx in meses[i]){
				id_mes = parseInt(((i-1)*3)+parseInt(idx)) + 1;
				html += '<td><input id="mes-'+identificador+'-'+indx+'-'+id_mes+'" name="mes-'+identificador+'['+indx+']['+id_mes+']" type="number" class="form-control input-sm metas-mes" data-meta-mes="'+id_mes+'" data-meta-jurisdiccion="'+indx+'" data-meta-identificador="'+identificador+'" data-meta-id="" min="0"></td>';
			}
			html += '</tr>';
		}
		$(tabla_id + ' tbody').empty();
		$(tabla_id + ' tbody').html(html);
	}
	actualizar_eventos_metas();
}

function actualizar_metas(identificador,metas){
	var indx;
	for(indx in metas){
		$('#mes-'+identificador+'-'+metas[indx].claveJurisdiccion+'-'+metas[indx].mes).val(metas[indx].meta);
		$('#mes-'+identificador+'-'+metas[indx].claveJurisdiccion+'-'+metas[indx].mes).attr('data-meta-id',metas[indx].id);
	}
	$('.metas-mes[data-meta-jurisdiccion="OC"][data-meta-identificador="'+identificador+'"]').change();
}

function actualizar_grid_desglose(datos,total_resultados,pagina){
	$(grid_desglose + ' > table > tbody').empty();
	var desglose_componente = [];
	for(indx in datos){
		var desglose = {};

		desglose.id = datos[indx].id;
		desglose.localidad = datos[indx].localidad || 'OFICINA CENTRAL';
		desglose.municipio = datos[indx].municipio || 'OFICINA CENTRAL';
		desglose.jurisdiccion = datos[indx].jurisdiccion || 'OFICINA CENTRAL';
		desglose.meta = datos[indx].meta;

		desglose_componente.push(desglose);
	}

	$('#conteo-desglose').text(desglose_componente.length);

	if(desglose_componente.length == 0){
		$(grid_desglose + ' > table > tbody').html('<tr><td colspan="5" style="text-align:left"><i class="fa fa-info-circle"></i> No hay datos</td></tr>');
		desgloseComponenteDatagrid.paginacion(1);
	}else{
		desgloseComponenteDatagrid.cargarDatos(desglose_componente);
		var total = parseInt(total_resultados/10); 
        if((parseInt(total_resultados)%10) > 0) 
        	total++;
        desgloseComponenteDatagrid.paginacion(total);
        desgloseComponenteDatagrid.setPagina(pagina);
	}
}

function actualizar_grid_actividades(datos){
	$(grid_actividades + ' > table > tbody').empty();
	var actividades = [];
	for(indx in datos){
		var actividad = {};

		actividad.id = datos[indx].id;
		actividad.indicador = datos[indx].indicador;
		actividad.interpretacion = datos[indx].interpretacion;
		actividad.unidad_medida = datos[indx].unidad_medida.descripcion;
		actividad.creadoPor = (datos[indx].usuario)?datos[indx].usuario.username:'';
		actividad.creadoAl = datos[indx].creadoAl.substring(0,11);

		if(comentarios.actividades[datos[indx].id]){
			actividad.indicador = '<span class="text-warning fa fa-warning"></span> ' + actividad.indicador;
		}

		actividades.push(actividad);
	}

	$('#conteo-actividades').text(' ' + actividades.length + ' ');

	if(actividades.length == 0){
		$(grid_actividades + ' > table > tbody').html('<tr><td colspan="6" style="text-align:left"><i class="fa fa-info-circle"></i> No hay datos</td></tr>');
	}else{
		actividadDatagrid.cargarDatos(actividades);
	}
}

function actualizar_grid_componentes(datos){
	$(grid_componentes + ' > table > tbody').empty();
	var componentes = [];
	for(indx in datos){
		var componente = {};

		componente.id = datos[indx].id;
		componente.indicador = datos[indx].indicador;
		componente.interpretacion = datos[indx].interpretacion || '---';
		componente.unidad_medida = datos[indx].unidad_medida.descripcion;
		componente.creadoPor = (datos[indx].usuario)?datos[indx].usuario.username:'';
		componente.creadoAl = datos[indx].creadoAl.substring(0,11);

		if(comentarios.componentes[datos[indx].id]){
			componente.indicador = '<span class="text-warning fa fa-warning"></span> ' + componente.indicador;
		}

		componentes.push(componente);
	}

	$('#tablink-componentes > span').text(componentes.length);

	if(componentes.length == 0){
		$(grid_componentes + ' > table > tbody').append('<tr><td colspan="6" style="text-align:left"><i class="fa fa-info-circle"></i> No hay datos</td></tr>');
	}else{
		componenteDatagrid.cargarDatos(componentes);
	}
}

function actualizar_grid_beneficiarios(datos){
	$(grid_beneficiarios + ' > table > tbody').empty();
	var beneficiarios_grid = [];
	var beneficiarios = [];
	var beneficiario;
	for(var indx in datos){
		if(beneficiarios[datos[indx].idTipoBeneficiario]){
			beneficiario = beneficiarios[datos[indx].idTipoBeneficiario];
		}else{
			beneficiario = {};
			beneficiario.id = datos[indx].idTipoBeneficiario;
			beneficiario.tipoBeneficiario = datos[indx].tipo_beneficiario.descripcion;
			beneficiario.totalF = 0;
			beneficiario.totalM = 0;
			beneficiario.total = 0;
		}

		datos[indx].total = parseInt(datos[indx].total) || 0;

		if(datos[indx].sexo == 'f'){
			beneficiario.totalF = datos[indx].total;
			beneficiario.total += datos[indx].total;
		}else{
			beneficiario.totalM = datos[indx].total;
			beneficiario.total += datos[indx].total;
		}
		
		//beneficiarios.push(beneficiario);
		beneficiarios[datos[indx].idTipoBeneficiario] = beneficiario;
	}
	for(var i in beneficiarios){
		beneficiarios_grid.push(beneficiarios[i]);
	}
	
	$('#tablink-beneficiarios > span').text(beneficiarios_grid.length);

	if(beneficiarios_grid.length == 0){
		$(grid_beneficiarios + ' > table > tbody').append('<tr><td colspan="5" style="text-align:left"><i class="fa fa-info-circle"></i> No hay datos</td></tr>');
	}else{
		beneficiarioDatagrid.cargarDatos(beneficiarios_grid);
	}
}

function reset_modal_form(formulario){
    $(formulario).get(0).reset();
    $(formulario + ' input[type="hidden"]').val('');
    $(formulario + ' input[type="hidden"]').change();
    
    $(formulario + ' .chosen-one').trigger('chosen:updated');
    $(formulario + ' .texto-comentario').remove();
    $(formulario + ' .has-warning').removeClass('has-warning');

    Validation.cleanFormErrors(formulario);
    if(formulario == form_componente){
    	$(form_componente + ' .metas-mes').attr('data-meta-id','');
    	$(modal_componente + ' .alert').remove();
    	$('#id-componente').val('');
    	$('#conteo-actividades').text(' 0 ');
    	$(grid_actividades + ' > table > tbody').html('<tr><td colspan="6" style="text-align:left"><i class="fa fa-info-circle"></i> No hay datos</td></tr>');
    }else if(formulario == form_actividad){
    	$(form_actividad + ' .metas-mes').attr('data-meta-id','');
    	$('#id-actividad').val('');
    	$('#lista-tabs-actividad a:first').tab('show');
    	$(modal_actividad + ' .alert').remove();
    }else if(formulario == form_beneficiario){
    	$(form_beneficiario + ' span.form-control').text('');
    	$(modal_beneficiario + ' .alert').remove();
    }
}

/*
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
*/

//
function sumar_totales(tipo,campo_suma,campo_total,mensaje){
	var sub_total = 0;
	$(tipo).each(function(){
		sub_total += parseInt($(this).val().replace(',','')) || 0;
	});
	$('#'+campo_suma).text(sub_total.format());
	if(parseInt($('#'+campo_total).val()) != sub_total){
		Validation.printFieldsErrors(campo_suma,mensaje);
	}else{
		Validation.cleanFieldErrors(campo_suma);
	}
}

function ejecutar_formula(identificador){	
	var numerador = parseFloat($('#numerador-'+identificador).val()) || 0;
	var denominador = parseFloat($('#denominador-'+identificador).val()) || 1;
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
			break;
		default:
			total = '';
			break;
	}
	if(total != ''){
		total = parseFloat(total.toFixed(2));
	}
	$('#meta-'+identificador).val(total).change();
}

function actualizar_eventos_metas(){
	$('.metas-mes').on('change',function(){
		var mes = $(this).attr('data-meta-mes');
		var trimestre = Math.ceil(mes/3);
		var identificador = $(this).attr('data-meta-identificador');
		
		var suma = 0;
		var mes_inicio = 0;
		var mes_fin = 0;

		if(trimestre == 1){
			mes_inicio = 1;
			mes_fin = 3;
		}else if(trimestre == 2){
			mes_inicio = 4;
			mes_fin = 6;
		}else if(trimestre == 3){
			mes_inicio = 7;
			mes_fin = 9;
		}else if(trimestre == 4){
			mes_inicio = 10;
			mes_fin = 12;
		}

		for(var i = mes_inicio; i <= mes_fin; i++) {
			$('.metas-mes[data-meta-mes="' + i + '"][data-meta-identificador="' + identificador + '"]').each(function(){
				suma += parseFloat($(this).val()) || 0;
			});
		}
		suma = parseFloat(suma.toFixed(2));
		
		$('#trim'+trimestre+'-'+identificador).val(suma).change();

		var trim1 = parseFloat($('#trim1-'+identificador).val()) || 0;
		var trim2 = parseFloat($('#trim2-'+identificador).val()) || 0;
		var trim3 = parseFloat($('#trim3-'+identificador).val()) || 0;
		var trim4 = parseFloat($('#trim4-'+identificador).val()) || 0;

		suma = trim1 + trim2 + trim3 + trim4;
		suma = suma.toFixed(2);
		$('#numerador-'+identificador).val(suma).change();
		ejecutar_formula(identificador);
	});
}

function cargar_totales(){
	sumar_totales('.sub-total-zona.fem','total-zona-f','totalbeneficiariosf','Los subtotales de Zona no concuerdan.');
	sumar_totales('.sub-total-zona.masc','total-zona-m','totalbeneficiariosm','Los subtotales de Zona no concuerdan.');
	sumar_totales('.sub-total-poblacion.fem','total-poblacion-f','totalbeneficiariosf','Los subtotales de Población no concuerdan.');
	sumar_totales('.sub-total-poblacion.masc','total-poblacion-m','totalbeneficiariosm','Los subtotales de Población no concuerdan.');
	sumar_totales('.sub-total-marginacion.fem','total-marginacion-f','totalbeneficiariosf','Los subtotales de Marginación no concuerdan.');
	sumar_totales('.sub-total-marginacion.masc','total-marginacion-m','totalbeneficiariosm','Los subtotales de Marginación no concuerdan.');
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

function mostrar_comentarios_alt(datos){
	for(var i in datos){
        var id_campo = datos[i].idCampo;
        var observacion = datos[i].observacion;
        var tipo_comentario = datos[i].tipoComentario;
        
        if(tipo_comentario == 1){
        	if($('#'+id_campo).length){
                $('label[for="' + id_campo + '"]').prepend('<span class="fa fa-warning texto-comentario"></span> ');
                $('#'+id_campo).parent('.form-group').addClass('has-warning');
                $('#'+id_campo).parent('.form-group').append('<p class="texto-comentario has-warning help-block"> '+observacion+'</p>');
            }
        }
    }
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
			}else{
				$('#'+id_campo).parent('.form-group').addClass('has-warning');
				var texto_lbl = $('label[for="' + id_campo + '"]').text();
				$('label[for="' + id_campo + '"]').html('<span class="proyecto-comentario" style="cursor:pointer" data-placement="auto top" data-toggle="popover" data-trigger="click" data-content="'+observacion+'">'+texto_lbl+'</span>');
				$('label[for="' + id_campo + '"]').prepend('<span class="fa fa-warning"></span> ');
				$('.proyecto-comentario').popover();
			}
		}else{
			id_campo = id_campo.split('|'); // El id campo viene en dos o mas partes: field | id - extra_data
			var comentario = {};
			if(tipo_comentario == 2){
				if(!comentarios.componentes[id_campo[1]]){
					comentarios.componentes[id_campo[1]] = [];
				}
				comentario.tipoComentario = 1;
				comentario.idCampo = id_campo[0] + '-componente';
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

function habilita_opciones(selector,habilitar_id,default_id){
	var suma = $(selector + ' option[data-habilita-id="' + habilitar_id + '"]').length;

	$(selector + ' option[data-habilita-id]').attr('disabled',true).addClass('hidden');
	$(selector + ' option[data-habilita-id="' + habilitar_id + '"]').attr('disabled',false).removeClass('hidden');

	if(suma == 0 && default_id){
		$(selector + ' option[data-habilita-id="' + default_id + '"]').attr('disabled',false).removeClass('hidden');
	}

	$(selector).val('');
	$(selector).change();

	if($(selector).hasClass('chosen-one')){
		$(selector).trigger("chosen:updated");
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