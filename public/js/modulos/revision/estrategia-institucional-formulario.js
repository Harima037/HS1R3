/*=====================================

    # Nombre:
       estrategia-institucional-formulario.js

    # Módulo:
        revision/estrategia-institucional-formulario

    # Descripción:
        Se utiliza para comentar la captura del formulario de la estrategia institucional y validarla

=====================================*/

// Inicialización General para casi cualquier módulo
var moduloResource = new RESTfulRequests(SERVER_HOST+'/v1/revision-estrategia');

var comentariosArray = [];



/****************************************************************** Carga de datos (Editar) ********************************************************************/
if($('#id').val()){
    var parametros = {'mostrar':'editar-estrategia'};

    moduloResource.get($('#id').val(),parametros,{
        _success:function(response){
			
			//console.log(response.data);
            
            
            $('#lbl-ejercicio').text(response.data.ejercicio);
            $('#lbl-mision').text(response.data.mision);
            $('#lbl-vision').text(response.data.vision);
            $('#lbl-descripcion-indicador').text(response.data.descripcionIndicador);
            $('#lbl-numerador').text(response.data.numerador);
            $('#lbl-denominador').text(response.data.denominador);
            $('#lbl-interpretacion').text(response.data.interpretacion);
            $('#lbl-meta').text(response.data.metaIndicador);
            $('#lbl-linea-base').text(response.data.lineaBase);
            $('#lbl-anio-base').text(response.data.anioBase);
            $('#lbl-trim1').text(response.data.trim1);
            $('#lbl-trim2').text(response.data.trim2);
            $('#lbl-trim3').text(response.data.trim3);
            $('#lbl-trim4').text(response.data.trim4);
            $('#lbl-valorNumerador').text(response.data.valorNumerador);
            $('#lbl-valorDenominador').text(response.data.valorDenominador);
            $('#lbl-fuente-informacion').text(response.data.fuenteInformacion);
            
            $('#lbl-programa-presupuestario').text(response.data.programa_presupuestario.descripcion);
            $('#lbl-formula').text(response.data.formula.descripcion);
            $('#lbl-frecuencia').text(response.data.frecuencia.descripcion);
            $('#lbl-unidad-responsable').text(response.data.unidad_responsable.descripcion);
            $('#lbl-programa-sectorial').text(response.data.programa_sectorial.descripcion);
			if(response.data.odm)
				$('#lbl-odm').text(response.data.odm.descripcion);
			else
				$('#lbl-odm').text("SIN DATO");
			if(response.data.ped)	
				$('#lbl-vinculacion-ped').text(response.data.ped.descripcion);
			else
				$('#lbl-vinculacion-ped').text("SIN DATO");
				
            $('#lbl-tipo-ind').text(response.data.tipo_indicador.descripcion);
            $('#lbl-dimension').text(response.data.dimension.descripcion);
			$('#lbl-unidad-medida').text(response.data.unidad_medida.descripcion);
			
			$('#lbl-responsable').text(response.data.responsable.nombre);			
			$('#ayuda-responsable').html(response.data.responsable.cargo);
            
			
			var alMenosUnElementoBorraron = 0;
			var elementosBorradosHTML = '';

			for(var i in response.data.comentario)
			{
				var comen = response.data.comentario[i];
				
				var NombreIdCampo = comen['idCampo'];
				var idCampo = '';
				var nombreDelCampo = '';
				var objetoAColorear = '';
				//console.log(NombreIdCampo);
				for(var i=0; i<NombreIdCampo.length; i++)
					if(NombreIdCampo.substr(i,1)!='|')
						idCampo += NombreIdCampo.substr(i,1);
					else 
						nombreDelCampo = idCampo;

				if(comen['tipoComentario']=='1')//Tipo 1 = Programa
				{					
                    objetoAColorear = '#lbl-'+idCampo;
                    $(objetoAColorear).parent().parent().addClass('has-error has-feedback');
     				comentariosArray.push([comen['id'],comen['idCampo'], comen['observacion'],'1']);
				}
				
				if ( $(objetoAColorear).length ) { // hacer algo aquí si el elemento existe
					objetoAColorear = '';
				}
				else
				{
					alMenosUnElementoBorraron++;
					elementosBorradosHTML += '<div class="row" id="borrados'+alMenosUnElementoBorraron+'"><div class="col-sm-2">';
					elementosBorradosHTML += 'Datos de estrategia</div><div class="col-sm-2">'+nombreDelCampo+'</div>';
					
					elementosBorradosHTML += '<div class="col-sm-6">'+comen['observacion']+'</div>';
                   	elementosBorradosHTML += '<div class="col-sm-2"><button type="button" class="btn btn-danger" onclick="elementoBorrado(\''+comen['id']+'\', \''+nombreDelCampo+'\', \''+comen['observacion']+'\', \'borrados'+alMenosUnElementoBorraron+'\');"><i class="fa fa-trash-o"></i> Eliminar</button></div>';
					elementosBorradosHTML += '</div>';
				}
				
				
			}
			
			if(alMenosUnElementoBorraron>0)
			{
				var insertarHTML = '<div class="row"><div class="col-sm-2"><strong>Comentario de:</strong></div>';
				insertarHTML += '<div class="col-sm-2"><strong>Campo</strong></div>';
				insertarHTML += '<div class="col-sm-6"><strong>Observación</strong></div>';
				insertarHTML += '<div class="col-sm-2"><strong>Descartar comentario</strong></div></div>';

				insertarHTML += elementosBorradosHTML;

				$('#elementos-borrados').html(insertarHTML);
			}		
			else
			{
				$('#mensajes-sin-duenio').addClass('hidden');
			}
			
			console.log(response);
			if(response.data.idEstatus != 4)
				$('#btnFirmarEstrategia').hide();
            
        }
    });
}

/****************************************************************** Funciones de modales ********************************************************************/

function elementoBorrado(id, campo, observacion, fila){
	
	Confirm.show({
        titulo:"Eliminar comentario del campo "+campo,
        mensaje: "¿Estás seguro de eliminar el comentario seleccionado?: "+observacion,
            callback: function(){
                moduloResource.delete(id,null,{
                    _success: function(response){ 
                        MessageManager.show({data:'Comentario eliminado con éxito.',type:'ADV',timer:3});
                        var arrayTemporal = [];							
                        for(var i = 0; i < comentariosArray.length; i++)
                            if(comentariosArray[i][0]!=id)
                                arrayTemporal.push([comentariosArray[i][0],comentariosArray[i][1],comentariosArray[i][2],comentariosArray[i][3]]);
                        comentariosArray.length=0;
                        comentariosArray = arrayTemporal;
                        $('#'+fila).addClass('hidden');
                    },
                    _error: function(jqXHR){ 
                        MessageManager.show(jqXHR.responseJSON);
                    }
                });
            }
    });	
}


function escribirComentario(idcampo,nombrecampo,objetoconinformacion)
{	
	$('#modalComentario').find(".modal-title").html("<i class=\"fa fa-pencil-square-o\"></i> Escribir comentario");    
	$('#lbl-nombredelcampo').html(nombrecampo);
	$('#idcampo').val(idcampo);
	$('#tipocomentario').val('1');
	
	if(idcampo == 'mision' || idcampo == 'vision')
		$('#lbl-informacioncampo').text($('#'+objetoconinformacion).val());
	else
		$('#lbl-informacioncampo').text($('#'+objetoconinformacion).text());
	
		
	var borrarAreaText = 1;
	
	for(var i = 0; i < comentariosArray.length; i++)
	{
		var arrayTemporal = comentariosArray[i];
		if(arrayTemporal[1]==idcampo)
		{
			$('#idestrategiacomentarios').val(arrayTemporal[0]);
			$('#comentario').val(arrayTemporal[2]);
			borrarAreaText = 0;
		}
	}
	if(borrarAreaText)
	{
		$('#comentario').val('');
		$('#idproyectocomentarios').val('');
	}
    $('#modalComentario').modal('show');
}


$('#btnGuardarComentario').on('click',function(){
	
	var parametros = $(formComentario).serialize();
	parametros = parametros + '&idestrategia=' + $('#id').val()+'&tipocomentario='+$('#tipocomentario').val();
	
	var objetoQueSeColorea = '';
	
	for(var i=0; i<$('#idcampo').val().length; i++)
		if($('#idcampo').val().substr(i,1)!='|')
			objetoQueSeColorea += $('#idcampo').val().substr(i,1);
	
	if($('#comentario').val()=="")
	{
		MessageManager.show({data:'Debe escribir un comentario antes de guardar',type:'ADV',timer:3});		
	}
	else
	{
		if($('#idestrategiacomentarios').val()=='')//Nuevo comentario
		{
			moduloResource.post(parametros,{
		        _success: function(response){
	    	        MessageManager.show({data:'Datos del comentario almacenados con éxito',type:'OK',timer:3});	
                    
                    if($('#idcampo').val().substr(0,5)=='mision')
					{
						var objetoAColorear = '#btn'+objetoQueSeColorea;
						$(objetoAColorear).removeClass('btn-default');
						$(objetoAColorear).addClass('btn-warning');					
					}
					if($('#idcampo').val().substr(0,5)=='vision')
					{
						var objetoAColorear = '#btn'+objetoQueSeColorea;
						$(objetoAColorear).removeClass('btn-default');
						$(objetoAColorear).addClass('btn-warning');					
					}
					else
					{
						var objetoAColorear = '#lbl-'+objetoQueSeColorea;
						$(objetoAColorear).parent().parent().addClass('has-error has-feedback');
					}
					comentariosArray.push([response.data.id, $('#idcampo').val(), $('#comentario').val(), $('#tipocomentario').val()]);
	            	$('#modalComentario').modal('hide');
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
		else //Modificar comentario
		{
			moduloResource.put($('#idestrategiacomentarios').val(),parametros,{
	        	_success: function(response){
	    	        MessageManager.show({data:'Datos del comentario almacenados con éxito',type:'OK',timer:3});					
					for(var i = 0; i < comentariosArray.length; i++)
						if(comentariosArray[i][0]==$('#idestrategiacomentarios').val())
							comentariosArray[i][2]=$('#comentario').val();
		            $('#modalComentario').modal('hide');
	        	},
	    	    _error: function(response){
		            try{
		                var json = $.parseJSON(response.responseText);
	                	if(!json.code)
	            	        MessageManager.show({code:'S03',data:"Hubo un problema al realizar la transacción, inténtelo de nuevo o contacte con soporte técnico."});
	        	        else{
	    	            	//json.container = modal_actividad + ' .modal-body';
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
});

$('#btnQuitarComentario').on('click',function(){
	
	if($('#idestrategiacomentarios').val()=='')//Nunca se guardó, por eso, no se borrará, solamente se cierra la ventana del modal
	{
		MessageManager.show({data:'Debe almacenar un comentario, antes de eliminarlo',type:'ADV',timer:3});
		$('#modalComentario').modal('hide');
	}
	else
	{
		Confirm.show({
			titulo:"Eliminar comentario",
			mensaje: "¿Estás seguro de eliminar el comentario seleccionado?",
				callback: function(){
					moduloResource.delete($('#idestrategiacomentarios').val(),null,{
                        _success: function(response){ 
                        	MessageManager.show({data:'Comentario eliminado con éxito.',type:'ADV',timer:3});

							var arrayTemporal = [];
							
							for(var i = 0; i < comentariosArray.length; i++)
								if(comentariosArray[i][0]!=$('#idestrategiacomentarios').val())
									arrayTemporal.push([comentariosArray[i][0],comentariosArray[i][1],comentariosArray[i][2],'1']);

							comentariosArray.length=0;
							comentariosArray = arrayTemporal;		
							
							//console.log(comentariosArray);					
							
							var objetoADesColorear = '';
							for(var i=0; i<$('#idcampo').val().length; i++)
								if($('#idcampo').val().substr(i,1)!='|')
									objetoADesColorear += $('#idcampo').val().substr(i,1);
							
							if($('#idcampo').val().substr(0,5)=='arbol')
							{
								var objetoADesColorear = '#btn'+objetoADesColorear;
								$(objetoADesColorear).removeClass('btn-warning');
								$(objetoADesColorear).addClass('btn-default');					
							}
							else
							{
								objetoADesColorear = '#lbl-'+objetoADesColorear;
								$(objetoADesColorear).parent().parent().removeClass('has-error has-feedback');
							}
							$('#comentario').val('');
							$('#idestrategiacomentarios').val('');
							$('#modalComentario').modal('hide');							
                        },
                        _error: function(jqXHR){ 
                        	MessageManager.show(jqXHR.responseJSON);
                        }
        			});
				}
		});
	}
});

/****************************************************************** Funciones de Botones ********************************************************************/
$('#btn-estrategia-cancelar').on('click',function(){
    window.location.href = SERVER_HOST+'/revision/revision-estrategia-institucional';
});

$('#btnRegresarCorregir').on('click',function(){
	if(comentariosArray.length>0)
	{
		Confirm.show({
				titulo:"Regresar el proyecto para correcciones",
				mensaje: "¿Estás seguro que desea devolver el proyecto para que éste sea corregido?",
				callback: function(){
					var parametros = 'actualizarestrategia=regresar';					
					moduloResource.put($('#id').val(),parametros,{
						_success: function(response){
							window.location = "../revision-estrategia-institucional";
							MessageManager.show({data:'La estrategia ha sido devuelto para correcciones',type:'OK',timer:3});					
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
	}
	else
	{
		MessageManager.show({data:'Debe escribir al menos un comentario en algún elemento del proyecto, para devolver a corrección',type:'ADV',timer:3});
	}
});



$('#btnAprobarEstrategia').on('click',function(){
	if(comentariosArray.length>0)
	{
		MessageManager.show({data:'Existen comentarios sobre la estrategia, si desea autorizarlo, por favor, elimine los comentarios',type:'ADV',timer:3});		
	}
	else
	{
		Confirm.show({
				titulo:"Aprobar la estrategia",
				mensaje: "¿Estás seguro que deseas aprobar toda la información de la estrategia institucional?",
				callback: function(){
					var parametros = 'actualizarestrategia=aprobar';					
					moduloResource.put($('#id').val(),parametros,{
						_success: function(response){
							window.location = "../revision-estrategia-institucional";
							MessageManager.show({data:'La estrategia institucional ha sido validado en la información con que cuenta',type:'OK',timer:3});					
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
	}
});

$('#btnFirmarEstrategia').on('click',function(){
	if(comentariosArray.length>0)
	{
		MessageManager.show({data:'Existen comentarios sobre la estrategia institucional, si desea autorizarlo con firma, por favor, elimine los comentarios',type:'ADV',timer:3});		
	}
	else
	{
		Confirm.show({
				titulo:"¿Poner la estrategia institucional en el estatus de firma?",
				mensaje: "¿Estás seguro que desea poner el estatus de firma? Una vez hecho esto, la estrategia institucional ya no es modificable, y se entiende que se aprobó y firmó.",
				callback: function(){
					var parametros = 'actualizarestrategia=firmar';					
					moduloResource.put($('#id').val(),parametros,{
						_success: function(response){
							window.location = "../revision-estrategia-institucional";
							MessageManager.show({data:'La Estrategia institucional ha sido ha sido puesto en el estatus de firma',type:'OK',timer:3});					
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
	}
});

/****************************************************************** Funciones de cambio de accion para borrar ********************************************************************/


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