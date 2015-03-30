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
var idProyecto = 0;
var fuenteDatagrid;

var modal_fuente = '#modalFuenteFinanciamiento';

var form_fuente = '#form-fuente';
var form_subfuente = '#form-subfuente';

context.init = function(resource,id_proyecto){
	fuenteResourse = resource;
	idProyecto = id_proyecto;
	fuenteDatagrid = new Datagrid("#datagridFuenteFinanciamiento",fuenteResourse);
	fuenteDatagrid.init();
	llenar_datagrid_fuentes([]);

	$('#btn-agregar-fuente').on('click',function(){
		$(modal_fuente).find(".modal-title").html("Nueva Fuente de Financiamiento");
		$(modal_fuente).modal('show');
	});

	$('#fuente-financiamiento').on('change',function(){
        var habilitar_id = $(this).val();

        $('#destino-gasto option[data-id-fuente]').attr('disabled',true).addClass('hidden');
        $('#destino-gasto option[data-id-fuente="' + habilitar_id + '"]').attr('disabled',false).removeClass('hidden');

        $('#destino-gasto').val('');
        $('#destino-gasto').change();

        if($('#destino-gasto').hasClass('chosen-one')){
            $('#destino-gasto').trigger("chosen:updated");
        }
	});

	$(modal_fuente).on('hide.bs.modal',function(e){
		reset_modal_form(form_fuente);
	});

	$('#btn-fuente-guardar').on('click',function(){
		Validation.cleanFormErrors(form_fuente);

		var parametros = $(form_fuente).serialize();
		parametros += '&guardar=financiamiento&id-proyecto='+idProyecto;
		
		if($('#id-financiamiento').val()){
			proyectoResource.put($('#id-financiamiento').val(),parametros,{
		        _success: function(response){
		            MessageManager.show({data:'Datos del proyecto almacenados con éxito',type:'OK',timer:3});
		            llenar_datagrid_fuentes(response.data);
		            $(modal_fuente).modal('hide');
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
		            llenar_datagrid_fuentes(response.data);
		            $(modal_fuente).modal('hide');
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

	$("#datagridFuenteFinanciamiento .btn-delete-rows").unbind('click');
	$("#datagridFuenteFinanciamiento .btn-delete-rows").on('click',function(e){
		e.preventDefault();
		var rows = [];
		var contador= 0;
	    
	    $("#datagridFuenteFinanciamiento").find("tbody").find("input[type=checkbox]:checked").each(function () {
			contador++;
	        rows.push($(this).parent().parent().data("id"));
		});

		if(contador>0){
			Confirm.show({
					titulo:"Eliminar beneficiario",
					//botones:[], 
					mensaje: "¿Estás seguro que deseas eliminar la(s) fuente(s) de financiamiento seleccionada(s)?",
					//si: 'Actualizar',
					//no: 'No, gracias',
					callback: function(){
						proyectoResource.delete(rows,{'rows': rows, 'eliminar': 'financiamiento', 'id-proyecto': idProyecto},{
	                        _success: function(response){ 
	                        	llenar_datagrid_fuentes(response.financiamiento);
	                        	MessageManager.show({data:'Fuente(s) de Financiamiento eliminada(s) con éxito.',timer:3});
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

context.llenar_datagrid = function(datos){
	llenar_datagrid_fuentes(datos);
}

context.editar_fuente = function(e){
	var parametros = {'ver':'financiamiento'};
	proyectoResource.get(e,parametros,{
        _success: function(response){
        	$(modal_fuente).find(".modal-title").html("Editar Fuente de Financiamiento");

        	$('#fuente-financiamiento').val(response.data.idFuenteFinanciamiento);
        	$('#fuente-financiamiento').chosen().change();
        	$('#destino-gasto').val(response.data.idDestinoGasto);
        	$('#id-financiamiento').val(response.data.id);

        	for(var i in response.data.sub_fuentes_financiamiento){
        		var sub_fuente = response.data.sub_fuentes_financiamiento[i];
        		$('#subfuente_'+sub_fuente.id).prop('checked',true);
        	}
        	
        	$('.chosen-one').trigger('chosen:updated');

			$(modal_fuente).modal('show');
        }
    });
}

/***********************************************************************************************
					Funciones Privadas
************************************************************************************************/
function llenar_datagrid_fuentes(datos){
	var fuentes = [];
	$('#datagridFuenteFinanciamiento > table > tbody').empty();
	
	for(var i in datos){
		var lista_subfuentes = '';
		for(var j in datos[i].sub_fuentes_financiamiento){
			var subfuente = datos[i].sub_fuentes_financiamiento[j];
			lista_subfuentes += subfuente.clave + ' ';
		}

		var fuente = {
			'id': datos[i].id,
			'clave':datos[i].fuente_financiamiento.clave,
			'fuenteFinanciamiento': datos[i].fuente_financiamiento.descripcion,
			'destino': datos[i].destino_gasto.descripcion,
			'subfuentes': lista_subfuentes
		};
		fuentes.push(fuente);
	}
	if(fuentes.length == 0){
		$('#datagridFuenteFinanciamiento > table > tbody').html('<tr><td colspan="5" style="text-align:left"><i class="fa fa-info-circle"></i> No se encontraron datos guardados</td></tr>');
	}else{
		fuenteDatagrid.cargarDatos(fuentes);
	}
}

function reset_modal_form(form){
    $(form).get(0).reset();
    Validation.cleanFormErrors(form);
    $(form + ' input[type="checkbox"]').prop('checked',false);
    $(form + ' .alert').remove();
    $('#id-financiamiento').val('');
	$('#fuente-financiamiento').chosen().change();
	$('.chosen-one').trigger('chosen:updated');
}

})(fuenteFinanciamiento);