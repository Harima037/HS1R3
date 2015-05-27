/*=====================================

    # Nombre:
        fibapAntecedentes.js

    # Módulo:
        expediente/caratula-inversion

    # Descripción:
        Comportamiendo y funciones del formulario de la FIBAP para los datos relacionados a los antecedentes de un proyecto de inversión

=====================================*/

var fibapAntecedentes = {};

(function(context){

var id_fibap;
var fibap_resource;
var antecedenteDatagrid;
var modal_antecedente = '#modal-antecedente';
var form_antecedente = '#form-antecedente';
var form_antecedente_fibap = '#form-fibap-antecedentes';

context.init = function(id,resource){
	id_fibap = id;
	fibap_resource = resource;

	//Inicializacion de los DataGrids
	antecedenteDatagrid = new Datagrid("#datagridAntecedentes",fibap_resource);
	antecedenteDatagrid.init();
	llenar_datagrid_antecedentes([]);

	$('#btn-agregar-antecedente').on('click',function(){
		$(modal_antecedente).find(".modal-title").html("Nuevo Antecedente");
		$(modal_antecedente).modal('show');
	});

	$(modal_antecedente).on('hide.bs.modal',function(e){
		reset_modal_form();
	});

	$("#datagridAntecedentes .btn-delete-rows").unbind('click');
	$("#datagridAntecedentes .btn-delete-rows").on('click',function(e){
		e.preventDefault();
		var rows = [];
		var contador= 0;
	    $("#datagridAntecedentes").find("tbody").find("input[type=checkbox]:checked").each(function () {
			contador++;
	        rows.push($(this).parent().parent().data("id"));
		});
		if(contador>0){
			Confirm.show({
					titulo:"Eliminar antecedente",
					mensaje: "¿Estás seguro que deseas eliminar los antecedentes seleccionados?",
					callback: function(){
						fibap_resource.delete(rows,{'rows': rows, 'eliminar': 'antecedente', 'id-fibap': $('#id-fibap').val(), 'id-proyecto': $('#id').val()},{
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
};

context.mostrar_datos = function(datos){
	$(modal_antecedente).find(".modal-title").html("Editar Antecedente");

    $('#anio-antecedente').val(datos.anio);
	$('#autorizado-antecedente').val(datos.autorizado);
	$('#ejercido-antecedente').val(datos.ejercido);
	$('#fecha-corte-antecedente').val(datos.fechaCorte);
	$('#id-antecedente').val(datos.id);

    $(modal_antecedente).modal('show');
}

context.llenar_datagrid = function(datos){
	llenar_datagrid_antecedentes(datos);
};

context.limpiar_errores = function(){
	Validation.cleanFormErrors(form_antecedente_fibap);
};

/***********************************************************************************************
					Funciones Privadas
************************************************************************************************/
function llenar_datagrid_antecedentes(datos){
	var antecedentes = [];
	$('#datagridAntecedentes > table > tbody').empty();

	for(var indx in datos){
		var antecedente = {};

		antecedente.id = datos[indx].id;
		antecedente.anio = datos[indx].anio;
		antecedente.autorizado = '$ ' + parseFloat(datos[indx].autorizado).format(2);
		antecedente.ejercido = '$ ' + parseFloat(datos[indx].ejercido).format(2);
		antecedente.porcentaje = +parseFloat(datos[indx].porcentaje).toFixed(2);
		antecedente.fechaCorte = datos[indx].fechaCorte;

		antecedentes.push(antecedente);
	}

	if(antecedentes.length == 0){
		$('#datagridAntecedentes > table > tbody').html('<tr><td colspan="6" style="text-align:left"><i class="fa fa-info-circle"></i> No se encontraron datos guardados</td></tr>');
	}else{
		antecedenteDatagrid.cargarDatos(antecedentes);
	}
}

function reset_modal_form(){
    $(form_antecedente).get(0).reset();
    Validation.cleanFormErrors(form_antecedente);
    $(modal_antecedente + ' .alert').remove();
	$('#id-antecedente').val('');
}

})(fibapAntecedentes);