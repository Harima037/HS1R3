
/*=====================================

    # Nombre:
        formulario-fibap.js

    # Módulo:
        poa/formulario-fibap

    # Descripción:
        Para el formulario de captura del FIBAP de un proyecto

=====================================*/
// Declaracion de variables
var fibapResource = new RESTfulRequests(SERVER_HOST+'/v1/fibap');
//var componenteDatagrid = new Datagrid("#datagridComponentes",proyectoResource);
var presupuestoDatagrid = new Datagrid("#datagridPresupuesto",fibapResource);
var antecedenteDatagrid = new Datagrid("#datagridAntecedentes",fibapResource);

//componenteDatagrid.init();
//var modal_componente = '#modalComponente';
//var grid_componentes = '#datagridComponentes';

//Formularios
//var form_fibap_datos = '#form-fibap-datos';
//var form_fibap_antecedentes = '#form-fibap-antecedentes';
//var form_fibap_presupuesto = '#form-fibap-presupuesto';

//Ventanas modales
var modal_antecedente = '#modal-antecedente';
var modal_presupuesto = '#modal-presupuesto';

//Formularios de las ventanas modales
var form_antecedente = 'form-antecedente';
var form_presupuesto = '#form-presupuesto';

deshabilita_paneles('');

//*********************************   Funcionalidad al cargar el formulario   *********************************
$('#cobertura').on('change',function(){
	deshabilita_paneles($(this).val());
});

$('.benef-totales').on('keyup',function(){
	var total = 0;
	$('.benef-totales').each(function(){
		total += parseInt($(this).val()) || 0;
	});
	$('#total-beneficiarios').text(total);
});

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
			$('#alineacion-especifica').val(response.data.alineacionEspecifica);
			$('#alineacion-general').val(response.data.alineacionGeneral);
			$('#resultados-obtenidos').val(response.data.resultadosObtenidos);
			$('#resultados-esperados').val(response.data.resultadosEsperados);
			$('#presupuesto-requerido').val(response.data.presupuestoRequerido);
			$('#periodo-ejecucion').val(response.data.periodoEjecucion);

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

            if(response.data.resultadosObtenidos || response.data.resultadosEsperados){
            	cambiar_icono_tabs('#tab-link-antecedentes-fibap','fa-check-square-o');
            }

            if(response.data.presupuestoRequerido || response.data.periodoEjecucion){
            	cambiar_icono_tabs('#tab-link-presupuesto-fibap','fa-check-square-o');
            }
		}
	});
}

//*********************************   Funcionalidad de Botones principales (Guardar y Cancelar)   *********************************
$('#btn-antecedente-guardar').on('click',function(){
	var parametros = $('#'+form_antecedente).serialize();
	parametros += '&formulario='+form_antecedente + '&fibap-id=' + $('#id').val();
	if($('#antecedente-id').val()){

	}else{
		fibapResource.post(parametros,{
	        _success: function(response){
	            MessageManager.show({data:'Antecedente almacenado con éxito',type:'OK',timer:3});
	            response.antecedentes.push(response.data);
	            llenar_datagrid_antecedentes(response.antecedentes);
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
$('#btn-fibap-guardar').on('click',function(){
	var formulario = $('#fibap-grupo-formularios > .tab-pane.active').data('form-id');
	var parametros = $('#'+formulario).serialize() + '&id=' + $('#id').val() + '&formulario=' + formulario;

	if($('#proyecto-id').val()){
		parametros += '&proyecto-id=' + $('#proyecto-id').val();
	}

	Validation.cleanFormErrors(formulario);

	if($('#id').val()){
		if(formulario == 'form-fibap-presupuesto'){
			var lista_origenes_id = '';
			$('.origen-financiamiento').each(function(){
				if($(this).data('captura-id')){
					lista_origenes_id += '&origen-captura-id[' + $(this).data('origen-id') + ']=' + $(this).data('captura-id');
				}
			});
			parametros += lista_origenes_id;
		}

		fibapResource.put($('#id').val(),parametros,{
	        _success: function(response){
	            MessageManager.show({data:'Datos del proyecto actualizados con éxito',type:'OK',timer:3});
	            var tab_panel_id = $('#fibap-grupo-formularios > .tab-pane.active').attr('id');
	            var tab_link_id = $('.nav-tabs a[href="#'+tab_panel_id+'"]').attr('id');
	            cambiar_icono_tabs('#'+tab_link_id,'fa-check-square-o');
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
	            cambiar_icono_tabs('#tab-link-datos-fibap','fa-check-square-o');
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
	$(modal_presupuesto).find(".modal-title").html("Nuevo Elemento");
	$(modal_presupuesto).modal('show');
});

//********************************************   Funciones   ********************************************
function llenar_datagrid_antecedentes(datos){
	var antecedentes = [];
	$('#datagridAntecedentes > table > tbody').empty();

	for(var indx in datos){
		var antecedente = {};

		antecedente.id = datos[indx].id;
		antecedente.anio = datos[indx].anio;
		antecedente.autorizado = datos[indx].autorizado;
		antecedente.ejercido = datos[indx].ejercido;
		antecedente.porcentaje = datos[indx].porcentaje;
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