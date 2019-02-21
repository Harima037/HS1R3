/*=====================================

    # Nombre:
        vista-rendicion-programas.js

    # Módulo:
        seguimiento/avance-programa

    # Descripción:
        Para rendición de cuentas de Programas Presupuestales

=====================================*/

// Inicialización General para casi cualquier módulo
var moduloResource = new RESTfulRequests(SERVER_HOST+'/v1/seguimiento-estrategia');

var comentariosArray = [];

$('#btn-estrategia-cancelar').on('click',function(){
    window.location.href = SERVER_HOST+'/revision/seguimiento-estrategia';
});

$('#btn-enviar-programa').on('click',function(){
    parametros = 'guardar=validar-avance';

    moduloResource.put($('#id').val(),parametros,{
        _success: function(response){
            MessageManager.show({data:response.data,type:'OK',timer:6});
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
});

if($('#id').val()){
    var parametros = {mostrar:'datos-programa-presupuestario'};
    moduloResource.get($('#id').val(),parametros,{
        _success: function (response){
            $('#lbl-programa-presup').text(response.data.claveProgramaPresupuestario + ' ' + response.data.programaPresupuestario);
            $('#lbl-unidad-responsable').text(response.data.claveUnidadResponsable + ' ' + response.data.unidadResponsable);
        }
    });
}

/********************************************************************************************************************************
        Inicio: Seguimiento de Metas
*********************************************************************************************************************************/
var indicadoresDatagrid = new Datagrid("#datagridIndicadores",moduloResource,{ formatogrid:true, pagina: 1, idEstrategia: $('#id').val(), grid:'rendicion-indicadores'});
indicadoresDatagrid.init();
indicadoresDatagrid.actualizar({ _success: function(response){ llenar_grid_indicadores(response); } });

$('#btn-guardar-avance').on('click',function(){
    if($('#trimestre-porcentaje').attr('data-estado-avance')){
        if(($('#analisis-resultados').val().trim() == '') || ($('#justificacion-acumulada').val().trim() == '')){
            MessageManager.show({data:'Es necesario capturar una justificacion en base al porcentaje de avance del mes.',container:'#modalEditarAvance .modal-body',type:'ADV'});
            return;
        }
    }

    var parametros = $('#form_avance').serialize();
    parametros += '&guardar=avance-metas&id-programa='+$('#id').val();

    Validation.cleanFormErrors('#form_avance');

    if($('#id-avance').val()){
        moduloResource.put($('#id-avance').val(),parametros,{
            _success: function(response){
                MessageManager.show({data:'Datos del proyecto almacenados con éxito',type:'OK',timer:4});
                //accionesDatagrid.actualizar({ _success: function(response){ llenar_grid_acciones(response); } });
                indicadoresDatagrid.actualizar();
                $('#modalEditarAvance').modal('hide');
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
        moduloResource.post(parametros,{
            _success: function(response){
                MessageManager.show({data:'Datos del proyecto almacenados con éxito',type:'OK',timer:4});
                //accionesDatagrid.actualizar({ _success: function(response){ llenar_grid_acciones(response); } });
                indicadoresDatagrid.actualizar();
                $('#id-avance').val(response.data.id);
                $('#modalEditarAvance').modal('hide');
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

function seguimiento_metas(e){
    var parametros = {'mostrar':'datos-metas-avance'};
    moduloResource.get(e,parametros,{
        _success: function(response){
            if(response.data.claveTipoIndicador == 'F'){
                $('#modalEditarAvance').find(".modal-title").html("Seguimiento de Metas del Fin");
            }else{
                $('#modalEditarAvance').find(".modal-title").html("Seguimiento de Metas del Proposito");
            }

            $('#indicador').text(response.data.descripcionIndicador);
            $('#unidad-medida').text(response.data.unidadMedida);
            $('#meta-total').text(parseFloat(response.data.valorNumerador).format(2));
            $('#id-indicador').val(response.data.id);

            var trimestre = $('#trimestre').val();
            var acumulado = 0;
            for(var i = 1; i <= trimestre ; i++){
                acumulado += (parseFloat(response.data['trim'+i]) || 0);
            }

            var valor_trimestre = (parseFloat(response.data['trim'+trimestre]) || 0);

            $('#trimestre-meta').text((valor_trimestre+0).format(2));
                
            $('#trimestre-acumulada').attr('data-valor',acumulado);
            $('#trimestre-acumulada').text(parseFloat(acumulado).format(2));

            var avance_trimestre = null;
            var avance_acumulado = 0;
            if(response.data.registro_avance.length){
                for(var i in response.data.registro_avance){
                    var avance = response.data.registro_avance[i];
                    if(avance.trimestre == trimestre){
                        avance_trimestre = parseFloat(avance.avance);
                        $('#id-avance').val(avance.id);
						$('#lbl-analisis-resultados').text(avance.analisisResultados);
						$('#lbl-justificacion-acumulada').text(avance.justificacionAcumulada);
						
						for(var j in avance.comentarios)
						{
							var comen = avance.comentarios[j];
							var NombreIdCampo = comen['idCampo'];
							var idCampo = '';
							for(var i=0; i<NombreIdCampo.length; i++)
								if(NombreIdCampo.substr(i,1)!='|')
									idCampo += NombreIdCampo.substr(i,1);
									
							var objetoAColorear = '#lbl-'+idCampo;
							$(objetoAColorear).parent().parent().addClass('has-error has-feedback');
							comentariosArray.push([comen['id'],comen['idCampo'],comen['comentario']]);
						}
                    }else{
                        avance_acumulado += parseFloat(avance.avance);
                    }
                }
            }

            //$('#avance-trimestre').val(avance_trimestre);
			$('#lbl-avance-trimestre').text(avance_trimestre);

            $('#trimestre-avance').attr('data-valor',avance_acumulado);
            $('#trimestre-avance').text(avance_acumulado.format());

            $('#trimestre-total').attr('data-valor',avance_acumulado + avance_trimestre);
            $('#trimestre-total').text((avance_acumulado + avance_trimestre).format());

            //calcular_porcentaje((avance_acumulado+avance_trimestre),acumulado);
            calcular_porcentaje(avance_trimestre,valor_trimestre)

			$('#modalEditarAvance').modal('show');
        }
    });
}

function calcular_porcentaje(total_acumulado, total_programado){

    var necesita_justificar = false;
    if(total_programado == 0 && total_acumulado ==  0){
        total_porcentaje_acumulado = '<small class="text-success">0%</small>';
    }else{
        if(total_programado > 0){
            var total_porcentaje_acumulado = parseFloat(((total_acumulado * 100) / total_programado).toFixed(2))||0;
        }else{
            if(total_acumulado > 0){
                var total_porcentaje_acumulado = 100;
            }else{
                var total_porcentaje_acumulado = 0;
            }
        }
        if(total_porcentaje_acumulado > 110){
            total_porcentaje_acumulado = '<small class="text-danger"><span class="fa fa-arrow-up"></span> '+total_porcentaje_acumulado+'%</small>';
        }else if(total_porcentaje_acumulado < 90){
            total_porcentaje_acumulado = '<small class="text-danger"><span class="fa fa-arrow-down"></span> '+total_porcentaje_acumulado+'%</small>';
        }else if(total_programado == 0 && total_porcentaje_acumulado > 0){
            total_porcentaje_acumulado = '<small class="text-info"><span class="fa fa-arrow-up"></span> '+total_porcentaje_acumulado+'%</small>';
        }else{
            total_porcentaje_acumulado = '<small class="text-success">'+total_porcentaje_acumulado+'%</small>';
        }
    }

    $('#trimestre-porcentaje').html(total_porcentaje_acumulado);
}

$('#modalEditarAvance').on('hide.bs.modal',function(e){
    $('#modalEditarAvance .alert').remove();
    $('#form_avance').get(0).reset();
    $('#form_avance input[type="hidden"]').val('');
    $('#justificacion-acumulada').attr('disabled',true);
    $('#trimestre-porcentaje').attr('data-estado-avance','');
    $('#trimestre-porcentaje').text('0%');
    $('#id-avance').val('');
/*
    $('#form_avance .texto-comentario').remove();
    $('#form_avance .has-warning').removeClass('has-warning');
*/
	$('#form_avance .has-error.has-feedback').removeClass('has-error has-feedback');
	comentariosArray.length = 0;
	
    Validation.cleanFormErrors('#form_avance');
});

function llenar_grid_indicadores(response){
    indicadoresDatagrid.limpiar();
    var trimestre = parseInt($('#trimestre').val());
    var datos_grid = [];
    for(var i in response.data){
        var indicador = response.data[i];

        var item = {};

        item.id = indicador.id;
        /*if(indicador.claveTipoIndicador == 'F'){
            item.nivel = 'Fin';
        }else{
            item.nivel = 'Proposito';
        }*/
        item.nivel = indicador.tipo_indicador.descripcion;
        item.indicador = indicador.descripcionIndicador;
        item.meta = indicador.valorNumerador;
        item.avances_acumulados = 0;
        item.avances_mes = 0;
        item.justificacion = '';

        if(indicador.registro_avance.length){
            for(var j in indicador.registro_avance){
                var avance = indicador.registro_avance[j];
                item.avances_acumulados += parseFloat(avance.avance);
                if(avance.trimestre == trimestre){
                    item.justificacion += '<span class="fa fa-floppy-o"></span> ';
                    item.avances_mes += parseFloat(avance.avance);
                    if(avance.justificacion){
                        item.justificacion += '<span class="fa fa-align-left"></span>';
                    }
                }
            }
        }

        datos_grid.push(item);
    }
    //indicadoresDatagrid.cargarDatos(datos_grid);    
	//this.cargarDatos = function(datos_grid){
		//alert(objJSON[0]['fecha_inicio']);
	if(datos_grid.length>0){
		for(var i=0; i<datos_grid.length; i++){
			var row = "<tr>";
			if(datos_grid[i].data_obj!=null){
				row = "<tr ";
				for(var key in datos_grid[i].data_obj)
				{
					row += ' data-'+key+'="'+datos_grid[i].data_obj[key]+'"';					
				}
				row += '>';					
			}else{
				row = 	'<tr data-id="'+datos_grid[i].id+'"><td>';
			}
				
			for(var key in datos_grid[i])
				if(key!="id" && key!="data_obj")
					if (datos_grid[i].hasOwnProperty(key))
				        row += '<td>'+datos_grid[i][key]+"</td>";

			row += 	'</tr>'; 
			$(indicadoresDatagrid.selector + " tbody").append(row);
                
		}
	}else{
		var conteo_columnas = $(this.selector + " thead tr th").length;
		$(indicadoresDatagrid.selector + " tbody").html('<tr><td></td><td colspan="'+(conteo_columnas - 1)+'"><i class="fa fa-info-circle"></i> No hay datos</td></tr>');	
	}
	           
    var total = parseInt(response.resultados/indicadoresDatagrid.rxpag); 
    var plus = parseInt(response.resultados)%indicadoresDatagrid.rxpag;
    if(plus>0) 
        total++;
    indicadoresDatagrid.paginacion(total);
}
/********************************************************************************************************************************
        Fin: Seguimiento de Metas
*********************************************************************************************************************************/

function escribirComentario(idcampo,nombrecampo,objetoconinformacion)
{	
	$('#modalComentario').find(".modal-title").html("<i class=\"fa fa-pencil-square-o\"></i> Escribir comentario");    
	$('#lbl-nombredelcampo').html(nombrecampo);
	$('#idcampo').val(idcampo);
	
	$('#lbl-informacioncampo').text($('#'+objetoconinformacion).text());
	var borrarAreaText = 1;
	
	$('#idregistroavance').val($('#id-avance').val());
	
	for(var i = 0; i < comentariosArray.length; i++)
	{
		var arrayTemporal = comentariosArray[i];
		if(arrayTemporal[1]==idcampo)
		{
			$('#idregistroavancecomentario').val(arrayTemporal[0]);			
			$('#comentario').val(arrayTemporal[2]);
			borrarAreaText = 0;
		}
	}
	if(borrarAreaText)
	{
		$('#comentario').val('');
		$('#idregistroavancecomentario').val('');
	}
    $('#modalComentario').modal('show');
}

$('#btnGuardarComentario').on('click',function(){
	
	var parametros = $(formComentario).serialize();	
	var objetoQueSeColorea = '';
	
	for(var i=0; i<$('#idcampo').val().length; i++)
		if($('#idcampo').val().substr(i,1)!='|')
			objetoQueSeColorea += $('#idcampo').val().substr(i,1);
	
	if($('#comentario').val()=="")
		MessageManager.show({data:'Debe escribir un comentario antes de guardar',type:'ADV',timer:3});		
	else
	{
		if($('#idregistroavancecomentario').val()=='')//Nuevo comentario
		{
			moduloResource.post(parametros,{
		        _success: function(response){
	    	        MessageManager.show({data:'Datos del comentario almacenados con éxito',type:'OK',timer:3});
					var objetoAColorear = '#lbl-'+objetoQueSeColorea;
					$(objetoAColorear).parent().parent().addClass('has-error has-feedback');
					comentariosArray.push([response.data.id, $('#idcampo').val(), $('#comentario').val()]);
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
			moduloResource.put($('#idregistroavancecomentario').val(),parametros,{
	        	_success: function(response){
	    	        MessageManager.show({data:'Datos del comentario almacenados con éxito',type:'OK',timer:3});					
					for(var i = 0; i < comentariosArray.length; i++)
						if(comentariosArray[i][0]==$('#idregistroavancecomentario').val())
							comentariosArray[i][2]=$('#comentario').val();
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
	}	
});


$('#btnQuitarComentario').on('click',function(){
	if($('#idregistroavancecomentario').val()=='')//Nunca se guardó, por eso, no se borrará, solamente se cierra la ventana del modal
	{
		MessageManager.show({data:'Debe almacenar un comentario, antes de eliminarlo',type:'ADV',timer:3});
		$('#modalComentario').modal('hide');
	}
	else
	{
		Confirm.show({
			titulo:"Eliminar comentario",
			mensaje: "¿Estás seguro de eliminar el comentario seleccionado?",
				//si: 'Actualizar',
				//no: 'No, gracias',
				callback: function(){
					moduloResource.delete($('#idregistroavancecomentario').val(),null,{
                        _success: function(response){ 
                        	MessageManager.show({data:'Comentario eliminado con éxito.',type:'ADV',timer:3});

							var arrayTemporal = [];
							
							for(var i = 0; i < comentariosArray.length; i++)
								if(comentariosArray[i][0]!=$('#idregistroavancecomentario').val())
									arrayTemporal.push([comentariosArray[i][0],comentariosArray[i][1],comentariosArray[i][2]]);

							comentariosArray.length=0;
							comentariosArray = arrayTemporal;							
							
							var objetoADesColorear = '';
							for(var i=0; i<$('#idcampo').val().length; i++)
								if($('#idcampo').val().substr(i,1)!='|')
									objetoADesColorear += $('#idcampo').val().substr(i,1);
							
							objetoADesColorear = '#lbl-'+objetoADesColorear;
							$(objetoADesColorear).parent().parent().removeClass('has-error has-feedback');							
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




$('#btnAprobarProyecto').on('click',function(){
	
	var parametros = 'actualizarestrategia=aprobar';	
	Confirm.show({
				titulo:"¿Aprobar el avance de este trimestre?",
				mensaje: "¿Estás seguro de aprobar el avance del trimestre? Una vez realizado esto, no es posible comentar o corregir por el mes corriente",
				callback: function(){
					moduloResource.put($('#id').val(),parametros,{
			        	_success: function(response){
			    	        MessageManager.show({data:'Se ha aprobado el avance trimestral del proyecto',type:'OK',timer:3});
							window.location.href = SERVER_HOST+'/revision/seguimiento-estrategia';
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
});



$('#btnRegresarCorregir').on('click',function(){
	var parametros = 'actualizarestrategia=regresar';	
	Confirm.show({
				titulo:"¿Regresar avance del trimestre a corrección?",
				mensaje: "¿Estás seguro de enviar el avance del trimestre a corregir? Debe de haber escrito al menos una observación",
				callback: function(){
					moduloResource.put($('#id').val(),parametros,{
			        	_success: function(response){
			    	        MessageManager.show({data:'Se ha regresado para corregir el avance trimestral del programa',type:'OK',timer:3});
							window.location.href = SERVER_HOST+'/revision/seguimiento-estrategia';							
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
	
});







/********************************************************************************************************************************
        Inicio: Funciones extras de utileria
*********************************************************************************************************************************/
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

/********************************************************************************************************************************
        Fin: Funciones extras de utileria
*********************************************************************************************************************************/