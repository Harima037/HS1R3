/*=====================================

    # Nombre:
        caratulaBeneficiario.js

    # Módulo:
        expediente/caratula-inversion

    # Descripción:
        CRUD de Beneficiarios de un proyecto

=====================================*/

var caratulaBeneficiario = {};

(function(context){

var id_proyecto;
var proyecto_resource;
var beneficiariosDatagrid;
var modal_beneficiario = '#modalBeneficiario';
var form_beneficiario = '#form_beneficiario';

context.init = function(id,resource){
	id_proyecto = id;
	proyecto_resource = resource;

	//Inicializacion de los DataGrids
	beneficiariosDatagrid = new Datagrid("#datagridBeneficiarios",proyecto_resource);
	beneficiariosDatagrid.init();
	llenar_datagrid_beneficiarios([]);

	$('.btn-agregar-beneficiario').on('click',function(){
		$(modal_beneficiario).find(".modal-title").html("Nuevo Beneficiario");
		$(modal_beneficiario).modal('show');
	});

	$(modal_beneficiario).on('hide.bs.modal',function(e){
		reset_modal_form();
	});

	$('.benef-totales').on('keyup',function(){
		$(this).change();
	});
	$('.benef-totales').on('change',function(){
		/*sumar_valores('.benef-totales','#totalbeneficiarios');*/
	});
	$('.benef-desglose').on('keyup',function(){ $(this).change() });
	$('.benef-desglose').on('change',function(){
		if($(this).hasClass('sub-total-zona')){
			sumar_valores('.sub-total-zona','#total-zona');
		}else if($(this).hasClass('sub-total-poblacion')){
			sumar_valores('.sub-total-poblacion','#total-poblacion');
		}else if($(this).hasClass('sub-total-marginacion')){
			sumar_valores('.sub-total-marginacion','#total-marginacion');
		}
	});

	$('#tipobeneficiariocabecera').on('change',function(){
		var id = $(this).val();
		if(id){
			$('#tipobeneficiario option.option-default-label').text('Seleccione una opción');
		}else{
			$('#tipobeneficiario option.option-default-label').text('Seleccione primero un Tipo de Beneficiario');
		}
		habilita_opciones('#tipobeneficiario',$(this).val());
	});

	$("#datagridBeneficiarios .btn-delete-rows").unbind('click');
	$("#datagridBeneficiarios .btn-delete-rows").on('click',function(e){
		e.preventDefault();
		var rows = [];
		var contador= 0;
	    
	    $("#datagridBeneficiarios").find("tbody").find("input[type=checkbox]:checked").each(function () {
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
						proyectoResource.delete(rows,{'rows': rows, 'eliminar': 'proyecto-beneficiario', 'id-proyecto': $('#id').val()},{
	                        _success: function(response){ 
	                        	llenar_datagrid_beneficiarios(response.beneficiarios);
	                        	if(fibapAcciones){
	                        		fibapAcciones.actualizar_lista_beneficiarios(response.beneficiarios);
	                        	}
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
};

context.mostrar_datos = function(datos){
	$(modal_beneficiario).find('.modal-title').html('Editar Beneficiario');

	if($('#datagridBeneficiarios tr[data-id="'+datos.id+'"]').attr('data-comentario')){
		var comentario = $('#datagridBeneficiarios tr[data-id="'+datos.id+'"]').attr('data-comentario');
		MessageManager.show({data:comentario,container: modal_beneficiario + ' .modal-body',type:'ADV'});
	}

	if(datos.tipo_beneficiario){
		$('#tipobeneficiariocabecera').val(datos.tipo_beneficiario.clave_grupo);
		$('#tipobeneficiariocabecera').trigger('chosen:updated');
		$('#tipobeneficiariocabecera').change();
	}

	$('#tipobeneficiario').val(datos.idTipoBeneficiario);
    $('#tipobeneficiario').trigger('chosen:updated');

	$('#tipocaptura').val(datos.idTipoCaptura);
    $('#tipocaptura').trigger('chosen:updated');

    $('#id-beneficiario').val(datos.id);
    
	$('#totalbeneficiarios').val(datos.total || 0);
	$('#urbana').val(datos.urbana || 0);
	$('#rural').val(datos.rural || 0);
	$('#mestiza').val(datos.mestiza || 0);
	$('#indigena').val(datos.indigena || 0);
	$('#muyalta').val(datos.muyAlta || 0);
	$('#alta').val(datos.alta || 0);
	$('#media').val(datos.media || 0);
	$('#baja').val(datos.baja || 0);
	$('#muybaja').val(datos.muyBaja || 0);
    
	$('#urbana').change();
	$('#mestiza').change();
	$('#muyalta').change();
	$(modal_beneficiario).modal('show');
}

context.llenar_datagrid = function(datos){
	llenar_datagrid_beneficiarios(datos);
};

context.checar_errores = function(){
	var errores = false;
	if(parseInt($('#totalbeneficiarios').val()).format() != $('#total-zona').text()){
		Validation.printFieldsErrors('total-zona','Los subtotales de Zona no concuerdan.');
		errores = true;
	}else{
		Validation.cleanFieldErrors('total-zona');
	}
	if(parseInt($('#totalbeneficiarios').val()).format() != $('#total-poblacion').text()){
		Validation.printFieldsErrors('total-poblacion','Los subtotales de Población no concuerdan.');
		errores = true;
	}else{
		Validation.cleanFieldErrors('total-poblacion');
	}
	if(parseInt($('#totalbeneficiarios').val()).format() != $('#total-marginacion').text()){
		Validation.printFieldsErrors('total-marginacion','Los subtotales de Marginación no concuerdan.');
		errores = true;
	}else{
		Validation.cleanFieldErrors('total-marginacion');
	}
	return errores;
}

/***********************************************************************************************
					Funciones Privadas
************************************************************************************************/
function llenar_datagrid_beneficiarios(datos){
	$('#datagridBeneficiarios > table > tbody').empty();

	var beneficiarios_grid = [];
	var beneficiario;
	for(var indx in datos){
		beneficiario = {};

		beneficiario.id = datos[indx].id;
		beneficiario.tipo_captura = (datos[indx].tipo_captura)?datos[indx].tipo_captura.descripcion:'--';
		beneficiario.clave = (datos[indx].tipo_beneficiario)?datos[indx].tipo_beneficiario.clave:'--';
		beneficiario.grupo = (datos[indx].tipo_beneficiario)?datos[indx].tipo_beneficiario.grupo:'---';
		beneficiario.tipoBeneficiario = (datos[indx].tipo_beneficiario)?datos[indx].tipo_beneficiario.descripcion:'No Encontrado';
		beneficiario.total = (parseInt(datos[indx].total) || 0).format();
		beneficiarios_grid.push(beneficiario);
	}
	
	$('#tab-link-caratula-beneficiarios > span.badge').text(beneficiarios_grid.length);

	if(beneficiarios_grid.length == 0){
		$('#datagridBeneficiarios > table > tbody').html('<tr><td colspan="5" style="text-align:left"><i class="fa fa-info-circle"></i> No se encontraron datos guardados</td></tr>');
	}else{
		beneficiariosDatagrid.cargarDatos(beneficiarios_grid);
	}
}

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

function reset_modal_form(){
    $(form_beneficiario).get(0).reset();
    Validation.cleanFormErrors(form_beneficiario);
    $(modal_beneficiario + ' .alert').remove();
	$('#id-beneficiario').val('');
	$(modal_beneficiario + ' .chosen-one').trigger('chosen:updated');
	$(form_beneficiario + ' span.form-control').text('');
	$('#tipobeneficiariocabecera').change();
}

/**
 * Number.prototype.format(n, x)
 * 
 * @param integer n: length of decimal
 * @param integer x: length of sections
 */
 Number.prototype.format = function(n, x) {
    var re = '\\d(?=(\\d{' + (x || 3) + '})+' + (n > 0 ? '\\.' : '$') + ')';
    //return this.toFixed(Math.max(0, ~~n)).replace(new RegExp(re, 'g'), '$&,');
    var formateado = this.toFixed(Math.max(0, ~~n)).replace(new RegExp(re, 'g'), '$&,');
    var partes = formateado.split('.');
    if(parseInt(partes[1]) == 0){
        return partes[0];
    }else{
        return formateado;
    }
};

})(caratulaBeneficiario);