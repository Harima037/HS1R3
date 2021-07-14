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
		sumar_valores('.benef-totales','#totalbeneficiarios');
	});
	$('.fem,.masc').on('keyup',function(){
		$(this).change();
	});
	$('.fem').on('change',function(){
		if($(this).hasClass('sub-total-zona')){
			sumar_valores('.sub-total-zona.fem','#total-zona-f');
		}else if($(this).hasClass('sub-total-poblacion')){
			sumar_valores('.sub-total-poblacion.fem','#total-poblacion-f');
		}else if($(this).hasClass('sub-total-marginacion')){
			sumar_valores('.sub-total-marginacion.fem','#total-marginacion-f');
		}
	});
	$('.masc').on('change',function(){
		if($(this).hasClass('sub-total-zona')){
			sumar_valores('.sub-total-zona.masc','#total-zona-m');
		}else if($(this).hasClass('sub-total-poblacion')){
			sumar_valores('.sub-total-poblacion.masc','#total-poblacion-m');
		}else if($(this).hasClass('sub-total-marginacion')){
			sumar_valores('.sub-total-marginacion.masc','#total-marginacion-m');
		}
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

	if($('#datagridBeneficiarios tr[data-id="'+datos[0].idTipoBeneficiario+'"]').attr('data-comentario')){
		var comentario = $('#datagridBeneficiarios tr[data-id="'+datos[0].idTipoBeneficiario+'"]').attr('data-comentario');
		MessageManager.show({data:comentario,container: modal_beneficiario + ' .modal-body',type:'ADV'});
	}

	$('#tipobeneficiario').val(datos[0].idTipoBeneficiario);
    $('#tipobeneficiario').trigger('chosen:updated');

	$('#tipocaptura').val(datos[0].idTipoCaptura);
    $('#tipocaptura').trigger('chosen:updated');

    $('#id-beneficiario').val(datos[0].idTipoBeneficiario);

    var sexo;
    var total = 0;

    var beneficiarios = {'f':{},'m':{}};

    for(var i in datos){
    	beneficiarios[datos[i].sexo] = datos[i];
    }
    
    for(var sexo in beneficiarios ){
        total += parseInt(beneficiarios[sexo].total) || 0;
        $('#totalbeneficiarios'+sexo).val(beneficiarios[sexo].total || 0);
        $('#urbana'+sexo).val(beneficiarios[sexo].urbana || 0);
        $('#rural'+sexo).val(beneficiarios[sexo].rural || 0);
        $('#mestiza'+sexo).val(beneficiarios[sexo].mestiza || 0);
        $('#indigena'+sexo).val(beneficiarios[sexo].indigena || 0);
        $('#muyalta'+sexo).val(beneficiarios[sexo].muyAlta || 0);
        $('#alta'+sexo).val(beneficiarios[sexo].alta || 0);
        $('#media'+sexo).val(beneficiarios[sexo].media || 0);
        $('#baja'+sexo).val(beneficiarios[sexo].baja || 0);
        $('#muybaja'+sexo).val(beneficiarios[sexo].muyBaja || 0);
    }
    $('#totalbeneficiarios').text(total.format());
    $('.fem,.masc').change();

	$(modal_beneficiario).modal('show');
}

context.llenar_datagrid = function(datos){
	llenar_datagrid_beneficiarios(datos);
};

context.checar_errores = function(){
	var errores = false;
	if(parseInt($('#totalbeneficiariosf').val()).format() != $('#total-zona-f').text()){
		Validation.printFieldsErrors('total-zona-f','Los subtotales de Zona no concuerdan.');
		errores = true;
	}else{
		Validation.cleanFieldErrors('total-zona-f');
	}
	if(parseInt($('#totalbeneficiariosm').val()).format() != $('#total-zona-m').text()){
		Validation.printFieldsErrors('total-zona-m','Los subtotales de Zona no concuerdan.');
		errores = true;
	}else{
		Validation.cleanFieldErrors('total-zona-m');
	}
	if(parseInt($('#totalbeneficiariosf').val()).format() != $('#total-poblacion-f').text()){
		Validation.printFieldsErrors('total-poblacion-f','Los subtotales de Población no concuerdan.');
		errores = true;
	}else{
		Validation.cleanFieldErrors('total-poblacion-f');
	}
	if(parseInt($('#totalbeneficiariosm').val()).format() != $('#total-poblacion-m').text()){
		Validation.printFieldsErrors('total-poblacion-m','Los subtotales de Población no concuerdan.');
		errores = true;
	}else{
		Validation.cleanFieldErrors('total-poblacion-m');
	}
	if(parseInt($('#totalbeneficiariosf').val()).format() != $('#total-marginacion-f').text()){
		Validation.printFieldsErrors('total-marginacion-f','Los subtotales de Marginación no concuerdan.');
		errores = true;
	}else{
		Validation.cleanFieldErrors('total-marginacion-f');
	}
	if(parseInt($('#totalbeneficiariosm').val()).format() != $('#total-marginacion-m').text()){
		Validation.printFieldsErrors('total-marginacion-m','Los subtotales de Marginación no concuerdan.');
		errores = true;
	}else{
		Validation.cleanFieldErrors('total-marginacion-m');
	}

	return errores;
}

/***********************************************************************************************
					Funciones Privadas
************************************************************************************************/
function llenar_datagrid_beneficiarios(datos){
	$('#datagridBeneficiarios > table > tbody').empty();
	
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

		if(datos[indx].sexo == 'f'){
			beneficiario.totalF = parseInt(datos[indx].total) || 0;
			beneficiario.total += parseInt(datos[indx].total) || 0;
		}else{
			beneficiario.totalM = parseInt(datos[indx].total) || 0;
			beneficiario.total += parseInt(datos[indx].total) || 0;
		}
		
		//beneficiarios.push(beneficiario);
		beneficiarios[datos[indx].idTipoBeneficiario] = beneficiario;
	}
	
	for(var i in beneficiarios){
		beneficiarios[i].totalM = beneficiarios[i].totalM.format();
		beneficiarios[i].totalF = beneficiarios[i].totalF.format();
		beneficiarios[i].total = beneficiarios[i].total.format();
		beneficiarios_grid.push(beneficiarios[i]);
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

function reset_modal_form(){
    $(form_beneficiario).get(0).reset();
    Validation.cleanFormErrors(form_beneficiario);
    $(modal_beneficiario + ' .alert').remove();
	$('#id-beneficiario').val('');
	$(modal_beneficiario + ' .chosen-one').trigger('chosen:updated');
	$(form_beneficiario + ' span.form-control').text('');
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