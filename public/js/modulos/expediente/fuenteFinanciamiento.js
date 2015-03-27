/*=====================================

    # Nombre:
        fuenteFinanciamiento.js

    # Módulo:
        expediente/caratula
        expediente/caratula-inversion
	
    # Descripción:
        Comportamiendo y funciones del formulario de Fuentes y Subfuentes de Financiamientos

=====================================*/

var fuenteFinanciamiento = {};

(function(context){

var fuenteResourse = null;

var fuenteDatagrid;

var modal_fuente = '#modalFuenteFinanciamiento';

var form_fuente = '#form-fuente';
var form_subfuente = '#form-subfuente';

context.init = function(resource){
	fuenteResourse = resource;

	fuenteDatagrid = new Datagrid("#datagridFuenteFinanciamiento",fuenteResourse);
	fuenteDatagrid.init();
	llenar_datagrid_fuentes([]);

	$('#btn-agregar-fuente').on('click',function(){
		$(modal_fuente).find(".modal-title").html("Nueva Fuente de Financiamiento");
		$(modal_fuente).modal('show');
	});

	$(modal_fuente).on('hide.bs.modal',function(e){
		reset_modal_form(form_fuente);
	});
};

/***********************************************************************************************
					Funciones Privadas
************************************************************************************************/
function llenar_datagrid_fuentes(datos){
	var fuentes = [];
	$('#datagridFuenteFinanciamiento > table > tbody').empty();

	for(var indx in datos){
		
	}

	if(fuentes.length == 0){
		$('#datagridFuenteFinanciamiento > table > tbody').html('<tr><td colspan="3" style="text-align:left"><i class="fa fa-info-circle"></i> No se encontraron datos guardados</td></tr>');
	}else{
		fuenteDatagrid.cargarDatos(fuentes);
	}
}

function reset_modal_form(form){
    $(form).get(0).reset();
    Validation.cleanFormErrors(form);
    $(form + ' .alert').remove();
	$('#id-fuente').val('');
}

})(fuenteFinanciamiento);