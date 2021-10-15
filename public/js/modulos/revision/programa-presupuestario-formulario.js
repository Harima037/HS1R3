/*=====================================

    # Nombre:
        programa-presupuestario-formulario.js

    # Módulo:
        revision/programa-presupuestario-captura

    # Descripción:
        Se utiliza para comentar los indicadores de los programas presupuestarios

=====================================*/

// Inicialización General para casi cualquier módulo
var moduloResource = new RESTfulRequests(SERVER_HOST+'/v1/revision-programas');

var comentariosArray = [];

var indicadoresDatagrid;
var problemasDatagrid;
var objetivosDatagrid;

var modal_indicador = '#modal_programa_indicador';
var modal_problema = '#modal_problema';
var modal_objetivo = '#modal_objetivo';

var form_programa = '#form_programa_datos';
var form_indicador = '#form_programa';
var form_problema = '#form_problema';
var form_objetivo = '#form_objetivo';
var form_causa_efecto = '#form_causa_efecto';
var form_medio_fin = '#form_medio_fin';

$('.chosen-one').chosen({width:'100%'});

$(form_indicador + ' #formula-programa').on('change',function(){ ejecutar_formula(); });
$(form_indicador + ' .valor-trimestre').on('change',function(){ sumar_trimestres(); });
$(form_indicador + ' .valor-trimestre').on('keyup',function(){ $(this).change(); });
$(form_indicador + ' #denominador-programa').on('change',function(){ ejecutar_formula(); });
$(form_indicador + ' #denominador-programa').on('keyup',function(){ $(this).change(); });

/****************************************************************** Carga de datos (Editar) ********************************************************************/
if($('#id').val()){
    var parametros = {'mostrar':'editar-programa'};

    moduloResource.get($('#id').val(),parametros,{
        _success:function(response){
			
			//console.log(response.data);
			if(response.data.idEstatus >= 4){
                $('#btn-exportar-pdf').show();
                $('#btn-exportar-pdf').off('click');
                $('#btn-exportar-pdf').on('click',function(){
                    window.open(SERVER_HOST+'/v1/reporte-programa-presupuestario/'+response.data.id);
                });
            }else{
                $('#btn-exportar-pdf').off('click');
                $('#btn-exportar-pdf').hide();
            }
			
			$('#idEstatusPrograma').val(response.data.idEstatus);
			
			$('#lbl-programa-sectorial').text(response.data.programaSectorial);
			$('#lbl-vinculacion-ped').text(response.data.objetivoPED);
			$('#lbl-vinculacion-pnd').text(response.data.objetivoPND);
					
            $('#lbl-programa-presupuestario').text(response.data.programaPresupuestarioDescripcion);
            $('#lbl-unidad-responsable').text(response.data.unidadResponsable);
            $('#lbl-ejercicio').text(response.data.ejercicio);
            $('#lbl-ods').text(response.data.ODS);
            $('#lbl-modalidad').text(response.data.claveModalidad+' '+response.data.modalidad);
            $('#lbl-fecha-inicio').text(response.data.fechaInicio);
            $('#lbl-fecha-termino').text(response.data.fechaTermino);
            $('#lbl-resultados-esperados').text(response.data.resultadosEsperados);
            $('#lbl-enfoque-potencial').text(response.data.areaEnfoquePotencial);
            $('#lbl-enfoque-objetivo').text(response.data.areaEnfoqueObjetivo);
            $('#lbl-cuantificacion-potencial').text(response.data.cuantificacionEnfoquePotencial);
            $('#lbl-cuantificacion-objetivo').text(response.data.cuantificacionEnfoqueObjetivo);
            $('#lbl-justificacion-programa').text(response.data.justificacionPrograma);

            $('#descripcion-problema').val(response.data.arbolProblema);
            $('#descripcion-objetivo').val(response.data.arbolObjetivo);
			
            $('#lbl-fuente-informacion').text(response.data.fuenteInformacion);			
			$('#lbl-responsable').text(response.data.nombreResponsable);			
			$('#ayuda-responsable').html(response.data.cargoResponsable);
			
			$('#descripcion-problema').attr('disabled', 'disabled');
			$('#descripcion-objetivo').attr('disabled', 'disabled');
			
			var parametrosProblemas = {'listar':'problemas','id-programa':$('#id').val(),'formatogrid':'true'};			
			moduloResource.get(null,parametrosProblemas,{
				_success:function(response){
					var causasefectosHTML = '';
					for(var i in response.data)
						causasefectosHTML += '<tr><td>'+response.data[i].causa+'</td><td>'+response.data[i].efecto+'</td></tr>';
					$('#tablaCausasEfectos tbody').html(causasefectosHTML);
				}
			});
			
			var parametrosObjetivos = {'listar':'objetivos','id-programa':$('#id').val(),'formatogrid':'true'};			
			moduloResource.get(null,parametrosObjetivos,{
				_success:function(response){
					var mediosfinesHTML = '';
					for(var i in response.data)
						mediosfinesHTML += '<tr><td>'+response.data[i].medio+'</td><td>'+response.data[i].fin+'</td></tr>';
					$('#tablaMediosFines tbody').html(mediosfinesHTML);
				}
			});
			
			var parametrosIndicadores = {'listar':'indicadores','id-programa':$('#id').val(),'formatogrid':'true'};
			moduloResource.get(null,parametrosIndicadores,{
				_success:function(response){
					
					var indicaHTML ='';
					for(var i in response.data)
					{
						var indicador = response.data[i];
						var parametrosIndicador = {'mostrar':'editar-indicador'};
						moduloResource.get(indicador.id,parametrosIndicador,{
							_success:function(response){
								var clv = response.data.claveTipoIndicador;
								console.log(response.data);
								//OBJETIVO
								$('#lbl-descripcion-obj-'+clv).text(response.data.descripcionObjetivo);								
								if(response.data.claveAmbito=='E')
									$('#lbl-ambito-'+clv).text('E Estatal');
								else
									$('#lbl-ambito-'+clv).text('F Federal');								
								$('#lbl-verificacion-'+clv).text(response.data.mediosVerificacion);
								$('#lbl-supuestos-'+clv).text(response.data.supuestos);
								//INDICADOR
								$('#lbl-descripcion-ind-'+clv).text(response.data.descripcionIndicador);
								$('#lbl-numerador-ind-'+clv).text(response.data.numerador);
								$('#lbl-denominador-ind-'+clv).text(response.data.denominador);
								$('#lbl-interpretacion-ind-'+clv).text(response.data.interpretacion);
								$('#lbl-dimension-'+clv).text(response.data.dimensionIndicador);
								$('#lbl-tipo-ind-'+clv).text(response.data.tipoIndicador);
								$('#lbl-unidad-medida-'+clv).text(response.data.unidadMedida);
								//METAS
								
								$('#lbl-linea-base-'+clv).text(response.data.lineaBase);
								$('#lbl-anio-base-'+clv).text(response.data.anioBase);
								$('#lbl-formula-'+clv).text(response.data.formula);
								$('#lbl-frecuencia-'+clv).text(response.data.frecuencia);
								$('#lbl-trim1-'+clv).text(response.data.trim1);
								$('#lbl-trim2-'+clv).text(response.data.trim2);
								$('#lbl-trim3-'+clv).text(response.data.trim3);
								$('#lbl-trim4-'+clv).text(response.data.trim4);
								$('#lbl-numerador-'+clv).text(response.data.valorNumerador);
								$('#lbl-denominador-'+clv).text(response.data.valorDenominador);
								$('#lbl-meta-'+clv).text(response.data.metaIndicador);
							}
						});
					}
				}
			});
			
			var alMenosUnElementoBorraron = 0;
			var elementosBorradosHTML = '';

			for(var i in response.data.comentario)
			{
				var comen = response.data.comentario[i];
				
				var NombreIdCampo = comen['idCampo'];
				var idCampo = '';
				var nombreDelCampo = '';
				var objetoAColorear = '';
				
				for(var i=0; i<NombreIdCampo.length; i++)
					if(NombreIdCampo.substr(i,1)!='|')
						idCampo += NombreIdCampo.substr(i,1);
					else 
						nombreDelCampo = idCampo;

				if(comen['tipoComentario']=='1')//Tipo 1 = Programa
				{					
					if(idCampo.substr(0,5)=='arbol')
					{
						objetoAColorear = '#btn'+idCampo;
						$(objetoAColorear).removeClass('btn-default');
						$(objetoAColorear).addClass('btn-warning');					
					}					
					else
					{
						objetoAColorear = '#lbl-'+idCampo;
						$(objetoAColorear).parent().parent().addClass('has-error has-feedback');
					}
					comentariosArray.push([comen['id'],comen['idCampo'], comen['observacion'],'1']);
				}
				
				if ( $(objetoAColorear).length ) { // hacer algo aquí si el elemento existe
					objetoAColorear = '';
				}
				else
				{
					alMenosUnElementoBorraron++;
					elementosBorradosHTML += '<div class="row" id="borrados'+alMenosUnElementoBorraron+'"><div class="col-sm-2">';
					elementosBorradosHTML += 'Datos de programa</div><div class="col-sm-2">'+nombreDelCampo+'</div>';
					
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
			
			
			if(response.data.idEstatus != 4)
				$('#btnFirmarPrograma').hide();


            $('#tab-link-diagnostico').attr('data-toggle','tab');
            $('#tab-link-diagnostico').parent().removeClass('disabled');
            $('#tab-link-indicadores').attr('data-toggle','tab');
            $('#tab-link-indicadores').parent().removeClass('disabled');
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
	
	if(idcampo == 'arbolproblema' || idcampo == 'arbolobjetivo')
		$('#lbl-informacioncampo').text($('#'+objetoconinformacion).val());
	else
		$('#lbl-informacioncampo').text($('#'+objetoconinformacion).text());
	
		
	var borrarAreaText = 1;
	
	for(var i = 0; i < comentariosArray.length; i++)
	{
		var arrayTemporal = comentariosArray[i];
		if(arrayTemporal[1]==idcampo)
		{
			$('#idprogramacomentarios').val(arrayTemporal[0]);
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
	parametros = parametros + '&idprograma=' + $('#id').val()+'&tipocomentario='+$('#tipocomentario').val();
	
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
		if($('#idprogramacomentarios').val()=='')//Nuevo comentario
		{
			moduloResource.post(parametros,{
		        _success: function(response){
	    	        MessageManager.show({data:'Datos del comentario almacenados con éxito',type:'OK',timer:3});	
					
					if($('#idcampo').val().substr(0,5)=='arbol')
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
			moduloResource.put($('#idprogramacomentarios').val(),parametros,{
	        	_success: function(response){
	    	        MessageManager.show({data:'Datos del comentario almacenados con éxito',type:'OK',timer:3});					
					for(var i = 0; i < comentariosArray.length; i++)
						if(comentariosArray[i][0]==$('#idprogramacomentarios').val())
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
	
	if($('#idprogramacomentarios').val()=='')//Nunca se guardó, por eso, no se borrará, solamente se cierra la ventana del modal
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
					moduloResource.delete($('#idprogramacomentarios').val(),null,{
                        _success: function(response){ 
                        	MessageManager.show({data:'Comentario eliminado con éxito.',type:'ADV',timer:3});

							var arrayTemporal = [];
							
							for(var i = 0; i < comentariosArray.length; i++)
								if(comentariosArray[i][0]!=$('#idprogramacomentarios').val())
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
							$('#idprogramacomentarios').val('');
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
$('#btn-programa-cancelar').on('click',function(){
    window.location.href = SERVER_HOST+'/revision/revision-programas';
});

$('#btnRegresarCorregir').on('click',function(){
	if(comentariosArray.length>0)
	{
		Confirm.show({
				titulo:"Regresar el proyecto para correcciones",
				mensaje: "¿Estás seguro que desea devolver el proyecto para que éste sea corregido?",
				callback: function(){
					var parametros = 'actualizarprograma=regresar';					
					moduloResource.put($('#id').val(),parametros,{
						_success: function(response){
							window.location = "../revision-programas";
							MessageManager.show({data:'El programa ha sido devuelto para correcciones',type:'OK',timer:3});					
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



$('#btnAprobarPrograma').on('click',function(){
	if(comentariosArray.length>0)
	{
		MessageManager.show({data:'Existen comentarios sobre el programa, si desea autorizarlo, por favor, elimine los comentarios',type:'ADV',timer:3});		
	}
	else
	{
		Confirm.show({
				titulo:"Aprobar el programa",
				mensaje: "¿Estás seguro que deseas aprobar toda la información del programa?",
				callback: function(){
					var parametros = 'actualizarprograma=aprobar';					
					moduloResource.put($('#id').val(),parametros,{
						_success: function(response){
							window.location = "../revision-programas";
							MessageManager.show({data:'El programa ha sido validado en la información con que cuenta',type:'OK',timer:3});					
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

$('#btnFirmarPrograma').on('click',function(){
	if(comentariosArray.length>0)
	{
		MessageManager.show({data:'Existen comentarios sobre el programa, si desea autorizarlo con firma, por favor, elimine los comentarios',type:'ADV',timer:3});		
	}
	else
	{
		Confirm.show({
				titulo:"¿Poner el programa en el estatus de firma?",
				mensaje: "¿Estás seguro que desea poner el estatus de firma? Una vez hecho esto, el programa ya no es modificable, y se entiende que se aprobó y firmó.",
				callback: function(){
					var parametros = 'actualizarprograma=firmar';					
					moduloResource.put($('#id').val(),parametros,{
						_success: function(response){
							window.location = "../revision-programas";
							MessageManager.show({data:'El programa ha sido ha sido puesto en el estatus de firma',type:'OK',timer:3});					
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

function reset_modal_form(formulario){
    $(formulario).get(0).reset();
    $(formulario + ' input[type="hidden"]').val('');
    Validation.cleanFormErrors(formulario);
    $(formulario + ' .chosen-one').trigger('chosen:updated');

    if(formulario == form_indicador){
        $('#id-indicador').val('');
        Validation.cleanFieldErrors('tipo-indicador');
        $('#tipo-indicador').val('');
        $('#tipo-indicador').trigger('chosen:updated');
        $('#lbl-numerador-programa').text('');
        $('#lbl-meta-programa').text('');
    }else if(formulario == form_causa_efecto){
        $('#id-causa-efecto').val('');
    }else if(formulario == form_medio_fin){
        $('#id-medio-fin').val('');
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