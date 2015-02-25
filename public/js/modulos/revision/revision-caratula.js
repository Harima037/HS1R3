
/*=====================================

    # Nombre:
        revision-caratula.js

    # Módulo:
        revision/revision-caratula

    # Descripción:
        Para el formulario de revisión de captura (Caratula de captura) de un proyecto

=====================================*/
// Declaracion de variables
var proyectoResource = new RESTfulRequests(SERVER_HOST+'/v1/revision-proyectos');

var componenteDatagrid = new Datagrid("#datagridComponentes",proyectoResource);
var actividadDatagrid = new Datagrid('#datagridActividades',proyectoResource);
var desgloseComponenteDatagrid = new Datagrid('#datagridDesgloseComponente',proyectoResource);

var comentariosArray = [];

componenteDatagrid.init();
actividadDatagrid.init();
desgloseComponenteDatagrid.init();

var modal_componente = '#modalComponente';
var modal_actividad = '#modalActividad';

var grid_componentes = '#datagridComponentes';
var grid_actividades = '#datagridActividades';
var grid_desglose = '#datagridDesgloseComponente';

var form_caratula = '#form_caratula';
var form_componente = '#form_componente';
var form_actividad = '#form_actividad';

$('.chosen-one').chosen({width:'100%'});

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

        	$('#lbl-lider-proyecto').text(response.data.lider_proyecto.nombre);
			$('#lbl-jefe-inmediato').text(response.data.jefe_inmediato.nombre);
			$('#lbl-jefe-planeacion').text(response.data.jefe_planeacion.nombre);
			$('#lbl-coordinador-grupo').text(response.data.coordinador_grupo_estrategico.nombre);

			$('#lbl-nombretecnico').text(response.data.nombreTecnico);
            $('#lbl-ejercicio').text(response.data.ejercicio);
			
			$('#lbl-tipoaccion').text(response.data.tipo_accion.descripcion);
            $('#lbl-vinculacionped').text(response.data.objetivo_ped.clave+' - '+response.data.objetivo_ped.descripcion);
			
			//Clave presupuestaria
			$('#unidad_responsable').text(response.data.datos_unidad_responsable.clave);
            $('#finalidad').text(response.data.datos_finalidad.clave.slice(-1));
            $('#funcion').text(response.data.datos_funcion.clave.slice(-1));
            $('#subfuncion').text(response.data.datos_sub_funcion.clave.slice(-1));
            $('#subsubfuncion').text(response.data.datos_sub_sub_funcion.clave.slice(-1));
            $('#programa_sectorial').text(response.data.datos_programa_sectorial.clave );
            $('#programa_presupuestario').text(response.data.datos_programa_presupuestario.clave );
            $('#programa_especial').text(response.data.datos_programa_especial.clave );
            $('#actividad_institucional').text(response.data.datos_actividad_institucional.clave );
            $('#proyecto_estrategico').text(response.data.datos_proyecto_estrategico.clave);
            $('#no_proyecto_estrategico').text(("000" + response.data.numeroProyectoEstrategico).slice(-3));
			//Termina Clave presupuestaria
            
			$('#lbl-unidadresponsable').text(response.data.datos_unidad_responsable.clave+' - '+response.data.datos_unidad_responsable.descripcion);
			$('#lbl-funciongasto').text(response.data.datos_sub_sub_funcion.clave+' - '+response.data.datos_sub_sub_funcion.descripcion);
			$('#lbl-programasectorial').text(response.data.datos_programa_sectorial.clave+' - '+response.data.datos_programa_sectorial.descripcion);			
			$('#lbl-programapresupuestario').text(response.data.datos_programa_presupuestario.clave+' - '+response.data.datos_programa_presupuestario.descripcion);
			$('#lbl-programaespecial').text(response.data.datos_programa_especial.clave+' - '+response.data.datos_programa_especial.descripcion);			
			$('#lbl-actividadinstitucional').text(response.data.datos_actividad_institucional.clave+' - '+response.data.datos_actividad_institucional.descripcion);			
			$('#lbl-proyectoestrategico').text(response.data.datos_proyecto_estrategico.clave+' - '+response.data.datos_proyecto_estrategico.descripcion);           
            $('#lbl-numeroproyectoestrategico').text(("000" + response.data.numeroProyectoEstrategico).slice(-3));
			
            $('#lbl-cobertura').text(response.data.cobertura.descripcion);
						
			if(response.data.cobertura.id == 1){
				$('#select-estado-panel').show();
				$('#select-municipio-panel').hide();
				$('#select-region-panel').hide();
			}else if(response.data.cobertura.id == 2){
				$('#select-estado-panel').hide();
				$('#select-municipio-panel').show();
				$('#select-region-panel').hide();
				$('#lbl-municipio').text(response.data.municipio.nombre);
			}else if(response.data.cobertura.id == 3){
				$('#select-estado-panel').hide();
				$('#select-municipio-panel').hide();
				$('#select-region-panel').show();
				$('#lbl-region').text(response.data.region.nombre);
			}else{
				$('#select-estado-panel').hide();
				$('#select-municipio-panel').hide();
				$('#select-region-panel').hide();
			}			
			
			var cuantasFilasBeneficiarios = response.data.beneficiarios.length;			
			var cadenaHTML = '<table width=100% class="table table-bordered table-condensed"><tr>';
			var encabezado = 0;
			var sePusoTotales = 0;
			var banderaCelda = 0;
			
			for(var cuentabeneficia in response.data.beneficiarios)
			{
				if(encabezado==0)
				{
					cadenaHTML = cadenaHTML + '<td colspan="3"><strong> &nbsp;Tipo de beneficiario: </strong></td><td colspan="11">&nbsp;'+response.data.beneficiarios[cuentabeneficia].tipo_beneficiario.descripcion+'</td><td>&nbsp;</td></tr><tr><td colspan="2" rowspan="2" align="center"><strong>Total</strong></td><td rowspan="2" align="center"><strong>Género</strong></td><td colspan="2" align="center"><strong>Zona</strong></td><td colspan="4" align="center"><strong>Población</strong></td><td colspan="5" align="center"><strong>Marginación</strong></td><td rowspan="3" align="center"> <button type="button" class="btn btn-default" id="beneficiario'+response.data.beneficiarios[cuentabeneficia].idTipoBeneficiario+'" onclick="escribirComentario(\'beneficiario'+response.data.beneficiarios[cuentabeneficia].idTipoBeneficiario+'\',\'Tipo de beneficiario\',\''+response.data.beneficiarios[cuentabeneficia].tipo_beneficiario.descripcion+'\')" ><i class="fa fa-pencil-square-o"></i>Comentar</button></td></tr><tr><td align="center"><strong>Urbana</strong></td><td align="center"><strong>Rural</strong></td><td align="center"><strong>Mestiza</strong></td><td align="center"><strong>Indígena</strong></td><td align="center"><strong>Inmigrante</strong></td><td align="center"><strong>Otros</strong></td><td align="center"><strong>Muy Alta</strong></td><td align="center"><strong>Alta</strong></td><td align="center"><strong>Media</strong></td><td align="center"><strong>Baja</strong></td><td align="center"><strong>Muy Baja</strong></td></tr><tr>';					
					encabezado = 1;
				}
																		
				if((parseInt(cuentabeneficia,10)+2)<=cuantasFilasBeneficiarios)
				{
					var siguienteIndice = parseInt(cuentabeneficia,10)+1;										
					if(response.data.beneficiarios[cuentabeneficia].idTipoBeneficiario == response.data.beneficiarios[siguienteIndice].idTipoBeneficiario)
					{
						var sumaTotales = response.data.beneficiarios[cuentabeneficia].total + response.data.beneficiarios[siguienteIndice].total;
						cadenaHTML = cadenaHTML +'<td rowspan="2" align="right">'+sumaTotales+'</td>';
						sePusoTotales = 1;
					}
					else
					{
						if(sePusoTotales == 0)
						{
							var sumaTotales = response.data.beneficiarios[cuentabeneficia].total;
							cadenaHTML = cadenaHTML +'<td align="right">'+sumaTotales+'</td>';
						}
						else
						{
							sePusoTotales = 0;
							banderaCelda = 1;
						}
						encabezado = 0;
					}
				}
				else
				{					
					if(sePusoTotales == 0)
					{
						var sumaTotales = response.data.beneficiarios[cuentabeneficia].total;	
						cadenaHTML = cadenaHTML +'<td align="right">'+sumaTotales+'</td>';
					}
					else
					{
						banderaCelda = 1;
					}
					encabezado = 0;
				}
				
				cadenaHTML = cadenaHTML +'<td align="right">'+response.data.beneficiarios[cuentabeneficia].total+'</td>';
				if(response.data.beneficiarios[cuentabeneficia].sexo == 'f')
					cadenaHTML = cadenaHTML +'<td align="center"><span class="fa fa-female"></span> Femenino</td>';
				else
					cadenaHTML = cadenaHTML +'<td align="center"><span class="fa fa-male"></span> Masculino</td>';
				cadenaHTML = cadenaHTML +'<td align="right">'+response.data.beneficiarios[cuentabeneficia].urbana+'</td>';
				cadenaHTML = cadenaHTML +'<td align="right">'+response.data.beneficiarios[cuentabeneficia].rural+'</td>';
				cadenaHTML = cadenaHTML +'<td align="right">'+response.data.beneficiarios[cuentabeneficia].mestiza+'</td>';
				cadenaHTML = cadenaHTML +'<td align="right">'+response.data.beneficiarios[cuentabeneficia].indigena+'</td>';
				cadenaHTML = cadenaHTML +'<td align="right">'+response.data.beneficiarios[cuentabeneficia].inmigrante+'</td>';
				cadenaHTML = cadenaHTML +'<td align="right">'+response.data.beneficiarios[cuentabeneficia].otros+'</td>';
				cadenaHTML = cadenaHTML +'<td align="right">'+response.data.beneficiarios[cuentabeneficia].muyAlta+'</td>';
				cadenaHTML = cadenaHTML +'<td align="right">'+response.data.beneficiarios[cuentabeneficia].alta+'</td>';
				cadenaHTML = cadenaHTML +'<td align="right">'+response.data.beneficiarios[cuentabeneficia].media+'</td>';
				cadenaHTML = cadenaHTML +'<td align="right">'+response.data.beneficiarios[cuentabeneficia].baja+'</td>';
				cadenaHTML = cadenaHTML +'<td align="right">'+response.data.beneficiarios[cuentabeneficia].muyBaja+'</td>';
				
				if(banderaCelda==1)
				{
					banderaCelda=0;
					cadenaHTML = cadenaHTML + '<td>&nbsp;</td>';	
				}
				
				cadenaHTML = cadenaHTML + '</tr>';
				
				
			}
			cadenaHTML = cadenaHTML + '</table>';		
			
			$('#tabla-beneficiarios').html(cadenaHTML);		
			
			for(var contador in response.data.comentarios)
			{
				var idCampo = response.data.comentarios[contador]['idCampo'];
				if(idCampo.substr(0,12)=='beneficiario')
				{
					var objetoAColorear = '#'+idCampo;
					$(objetoAColorear).removeClass('btn-default');
					$(objetoAColorear).addClass('btn-warning');					
				}
				else
				{
					var objetoAColorear = '#lbl-'+idCampo;
					$(objetoAColorear).parent().parent().addClass('has-error has-feedback');
				}
				comentariosArray.push([response.data.comentarios[contador]['id'],response.data.comentarios[contador]['idCampo'], response.data.comentarios[contador]['observacion']]);	
			}
			
			
			            
            cargar_totales();

            actualizar_tabla_metas('actividad',response.data.jurisdicciones);
            actualizar_tabla_metas('componente',response.data.jurisdicciones);

            $('#tablink-componentes').attr('data-toggle','tab');
			$('#tablink-componentes').parent().removeClass('disabled');

			actualizar_grid_componentes(response.data.componentes);
        }
    });
}else if($('#id-fibap').val()){
	/*var parametros = {ver:'datos-fibap'};
	proyectoResource.get($('#id-fibap').val(),parametros,{
		_success:function(response){
			inicializar_comportamiento_caratula();
			
			//.addClass('control-bloqueado');
			$('#nombretecnico').addClass('control-bloqueado');
			$('#vinculacionped').addClass('control-bloqueado');
			$('#programapresupuestario').addClass('control-bloqueado');
			$('#cobertura').addClass('control-bloqueado');
			if(response.data.claveMunicipio){
				$('#municipio').addClass('control-bloqueado');
			}
			if(response.data.claveRegion){
				$('#region').addClass('control-bloqueado');
			}
			$('#tipobeneficiario').addClass('control-bloqueado');
			$('#totalbeneficiariosf').addClass('control-bloqueado');
			$('#totalbeneficiariosm').addClass('control-bloqueado');

			$('#nombretecnico').val(response.data.nombreTecnico);
			$('#vinculacionped').val(response.data.idObjetivoPED);
			$('#vinculacionped').trigger('chosen:updated');
			$('#programa_presupuestario').text(response.data.programaPresupuestario);
			$('#programapresupuestario').val(response.data.programaPresupuestario);
            $('#programapresupuestario').trigger('chosen:updated');
			$('#cobertura').val(response.data.idCobertura);
			$('#cobertura').trigger('chosen:updated');
			$('#cobertura').chosen().change();
			deshabilita_paneles($('#cobertura').val());
			if(response.data.claveMunicipio){
				$('#municipio').val(response.data.claveMunicipio);
				$('#municipio').trigger('chosen:updated');
			}
			if(response.data.claveRegion){
				$('#region').val(response.data.claveRegion);
				$('#region').trigger('chosen:updated');
			}
			$('#tipobeneficiario').val(response.data.idTipoBeneficiario);
			$('#tipobeneficiario').trigger('chosen:updated');
			$('#totalbeneficiarios').text(response.data.totalBeneficiarios);
			$('#totalbeneficiariosf').val(response.data.totalBeneficiariosF);
			$('#totalbeneficiariosm').val(response.data.totalBeneficiariosM);

			bloquear_controles();
			$('.chosen-one.control-bloqueado').trigger('chosen:updated');
		}
	});*/
}else{
	/*inicializar_comportamiento_caratula();
	deshabilita_paneles('');*/
}

function escribirComentario(idcampo,nombrecampo,objetoconinformacion)
{	
	$('#modalComentario').find(".modal-title").html("<i class=\"fa fa-pencil-square-o\"></i> Escribir comentario");    
	$('#lbl-nombredelcampo').html(nombrecampo);
	$('#idcampo').val(idcampo);
	
	if(idcampo.substr(0,12) == 'beneficiario')
		$('#lbl-informacioncampo').text(objetoconinformacion);
	else
		$('#lbl-informacioncampo').text($('#'+objetoconinformacion).text());
	var borrarAreaText = 1;
	
	for(var i = 0; i < comentariosArray.length; i++)
	{
		var arrayTemporal = comentariosArray[i];
		if(arrayTemporal[1]==idcampo)
		{
			$('#idproyectocomentarios').val(arrayTemporal[0]);
			//$('#comentario').text(arrayTemporal[2]);
			$('#comentario').val(arrayTemporal[2]);
			borrarAreaText = 0;
		}
	}
	if(borrarAreaText)
	{
		//$('#comentario').text('');
		$('#comentario').val('');
		$('#idproyectocomentarios').val('');
	}
    $('#modalComentario').modal('show');
}

$('#btnGuardarComentario').on('click',function(){
	
	var parametros = $(formComentario).serialize();
	parametros = parametros + '&idproyecto=' + $('#id').val();
		
	if($('#comentario').val()=="")
	{
		MessageManager.show({data:'Debe escribir un comentario antes de guardar',type:'ADV',timer:3});		
	}
	else
	{
		if($('#idproyectocomentarios').val()=='')//Nuevo comentario
		{
			proyectoResource.post(parametros,{
		        _success: function(response){
	    	        MessageManager.show({data:'Datos del comentario almacenados con éxito',type:'OK',timer:3});	
					if($('#idcampo').val().substr(0,12)=='beneficiario')
					{
						var objetoAColorear = '#'+$('#idcampo').val();
						$(objetoAColorear).removeClass('btn-default');
						$(objetoAColorear).addClass('btn-warning');	
					}
					else
					{
						var objetoAColorear = '#lbl-'+$('#idcampo').val();
						$(objetoAColorear).parent().parent().addClass('has-error has-feedback');
					}
					
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
			proyectoResource.put($('#idproyectocomentarios').val(),parametros,{
	        	_success: function(response){
	    	        MessageManager.show({data:'Datos del comentario almacenados con éxito',type:'OK',timer:3});					
					for(var i = 0; i < comentariosArray.length; i++)
						if(comentariosArray[i][0]==$('#idproyectocomentarios').val())
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
	if($('#idproyectocomentarios').val()=='')//Nunca se guardó, por eso, no se borrará, solamente se cierra la ventana del modal
	{
		MessageManager.show({data:'Debe almacenar un comentario, antes de eliminarlo',type:'ADV',timer:3});
		$('#modalComentario').modal('hide');
	}
	else
	{
		Confirm.show({
			titulo:"Eliminar actividad",
			mensaje: "¿Estás seguro de eliminar el comentario seleccionado?",
				//si: 'Actualizar',
				//no: 'No, gracias',
				callback: function(){
					proyectoResource.delete($('#idproyectocomentarios').val(),null,{
                        _success: function(response){ 
                        	MessageManager.show({data:'Comentario eliminado con éxito.',type:'ADV',timer:3});

							var arrayTemporal = [];
							
							for(var i = 0; i < comentariosArray.length; i++)
								if(comentariosArray[i][0]!=$('#idproyectocomentarios').val())
									arrayTemporal.push([comentariosArray[i][0],comentariosArray[i][1],comentariosArray[i][2]]);

							comentariosArray.length=0;
							comentariosArray = arrayTemporal;							
							//console.log(comentariosArray);
							
							if($('#idcampo').val().substr(0,12)=='beneficiario')
							{
								var objetoADesColorear = '#'+$('#idcampo').val();
								$(objetoADesColorear).removeClass('btn-warning');
								$(objetoADesColorear).addClass('btn-default');	
							}
							else
							{
								var objetoADesColorear = '#lbl-'+$('#idcampo').val();
								$(objetoADesColorear).parent().parent().removeClass('has-error has-feedback');
							}
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


function inicializar_comportamiento_caratula(){
	/*$('#entregable').on('change',function(){
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
	$('.benef-totales').on('keyup',function(){
		if($(this).attr('id') == 'totalbeneficiariosf'){
			var totalm = parseInt($('#totalbeneficiariosm').val());
			$('#totalbeneficiarios').text(totalm + parseInt($(this).val()));
		}
		if($(this).attr('id') == 'totalbeneficiariosm'){
			var totalf = parseInt($('#totalbeneficiariosf').val());
			$('#totalbeneficiarios').text(totalf + parseInt($(this).val()));
		}
	});
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
	});*/
}

/***********************************           Comportamiento de los datagrids          ***********************************/
function editar_componente(e){
	/*var parametros = {'ver':'componente'};
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

			$('#formula-componente').trigger('chosen:updated');
			$('#dimension-componente').trigger('chosen:updated');
			$('#frecuencia-componente').trigger('chosen:updated');
			$('#tipo-ind-componente').trigger('chosen:updated');
			$('#unidad-medida-componente').trigger('chosen:updated');

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

			$(form_componente + ' .metas-mes').attr('data-meta-id','');
			
			actualizar_metas('componente',response.data.metas_mes);
			
			var tab_id = cargar_formulario_componente_actividad('componente',response.data);

			$(tab_id).tab('show');

			actualizar_grid_actividades(response.data.actividades);

			if(response.data.idEntregable){
				busqueda_rapida_desglose(e,{});
			}else{
				actualizar_grid_desglose([]);
				$(modal_componente).modal('show');
			}
        }
    },'Cargando los datos del componente');*/
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
	if($('#trim1-'+identificador).val() != datos.numeroTrim1 && datos.numeroTrim1 != null){
		Validation.printFieldsErrors('trim1-'+identificador,'Valor anterior de '+datos.numeroTrim1+'.');
		errores_metas = true;
	}
	if($('#trim2-'+identificador).val() != datos.numeroTrim2 && datos.numeroTrim2 != null){
		Validation.printFieldsErrors('trim2-'+identificador,'Valor anterior de '+datos.numeroTrim2+'.');
		errores_metas = true;
	}
	if($('#trim3-'+identificador).val() != datos.numeroTrim3 && datos.numeroTrim3 != null){
		Validation.printFieldsErrors('trim3-'+identificador,'Valor anterior de '+datos.numeroTrim3+'.');
		errores_metas = true;
	}
	if($('#trim4-'+identificador).val() != datos.numeroTrim4 && datos.numeroTrim4 != null){
		Validation.printFieldsErrors('trim4-'+identificador,'Valor anterior de '+datos.numeroTrim4+'.');
		errores_metas = true;
	}
	if($('#numerador-'+identificador).val() != datos.valorNumerador && datos.valorNumerador != null){
		Validation.printFieldsErrors('numerador-'+identificador,'Valor anterior de '+datos.valorNumerador+'.');
		errores_metas = true;
	}
	if($('#meta-'+identificador).val() != datos.metaIndicador && datos.metaIndicador != null){
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

			$('#formula-actividad').trigger('chosen:updated');
			$('#dimension-actividad').trigger('chosen:updated');
			$('#frecuencia-actividad').trigger('chosen:updated');
			$('#tipo-ind-actividad').trigger('chosen:updated');
			$('#unidad-medida-actividad').trigger('chosen:updated');

			$(form_actividad + ' .metas-mes').attr('data-meta-id','');

			actualizar_metas('actividad',response.data.metas_mes);

			var tab_id = cargar_formulario_componente_actividad('actividad',response.data);

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

$('#btn-actividad-guardar').on('click',function(){
	Validation.cleanFormErrors(form_actividad);
	var parametros = $(form_actividad).serialize();
	parametros = parametros + '&guardar=actividad&id-componente=' + $('#id-componente').val();

	if($('#id-actividad').val()){
		var cadena_metas = '';
		$(form_actividad + ' .metas-mes').each(function(){
			if($(this).data('meta-id')){
				cadena_metas = cadena_metas + '&mes-actividad-id['+$(this).data('meta-jurisdiccion')+']['+$(this).data('meta-mes')+']='+$(this).data('meta-id');
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

$('#btn-proyecto-guardar').on('click',function(){

	/*Validation.cleanFormErrors(form_caratula);
	if(checar_error_totales()){
		return false;
	}

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
	            $(form_caratula + ' #no_proyecto_estrategico').text(("000" + response.data.numeroProyectoEstrategico).slice(-3));
	            $(form_caratula + ' #numeroproyectoestrategico').text(("000" + response.data.numeroProyectoEstrategico).slice(-3));

	            if(response.data.componentes){
	            	actualizar_grid_componentes(response.data.componentes);
	            }

	            actualizar_tabla_metas('actividad',response.data.jurisdicciones);
            	actualizar_tabla_metas('componente',response.data.jurisdicciones);

	            $('#tablink-componentes').attr('data-toggle','tab');
				$('#tablink-componentes').parent().removeClass('disabled');
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
	}*/
});

$('#btn-proyecto-cancelar').on('click',function(){
	window.location.href = "revision-proyectos";
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

	if(parseInt(componentes[0]) >= parseInt(componentes[1])){
		MessageManager.show({code:'S03',data:"Los componentes para este proyecto ya estan completos.",timer:2});
	}else{
		$('#tablink-componente-actividades').attr('data-toggle','');
		$('#tablink-componente-actividades').parent().addClass('disabled');
		$('#lista-tabs-componente a:first').tab('show');
		$(modal_componente).find(".modal-title").html("Nuevo Componente");
		$(modal_componente).modal('show');
	}
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
$('#programapresupuestario').on('change',function(){
	actualiza_clave('programa_presupuestario',$(this).val(),'---');
});
$('#programaespecial').on('change',function(){
	actualiza_clave('programa_especial',$(this).val(),'---');
});
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
			if($(this).data('meta-id')){
				cadena_metas = cadena_metas + '&mes-componente-id['+$(this).data('meta-jurisdiccion')+']['+$(this).data('meta-mes')+']='+$(this).data('meta-id');
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
				}
				actualizar_grid_componentes(response.componentes);
				actualizar_metas('componente',response.metas);
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
				}
				actualizar_grid_componentes(response.componentes);
				actualizar_metas('componente',response.metas);
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
					proyectoResource.delete(rows,{'rows': rows, 'eliminar': 'actividad', 'id-componente': $('#id-componente').val()},{
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
	var tabla_id = '#tabla-'+identificador+'-metas-mes';
	var meses = ['ENE','FEB','MAR','ABR','MAY','JUN','JUL','AGO','SEP','OCT','NOV','DIC'];

	var html = '';
	var indx,idx;
	var llaves = Object.keys(jurisdicciones).sort(); //Se ordenan las llaves

	for(var index in llaves){
		indx = llaves[index];
		html += '<tr>';
		html += '<th>'+jurisdicciones[indx]+'</th>';
		for(idx in meses){
			id_mes = parseInt(idx) + 1;
			html += '<td><input id="mes-'+identificador+'-'+indx+'-'+id_mes+'" name="mes-'+identificador+'['+indx+']['+id_mes+']" type="number" class="form-control input-sm metas-mes" data-meta-mes="'+id_mes+'" data-meta-jurisdiccion="'+indx+'" data-meta-identificador="'+identificador+'" data-meta-id=""></td>';
		}
		html += '</tr>';
	}

	$(tabla_id + ' tbody').empty();
	$(tabla_id + ' tbody').html(html);
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
		actividad.creadoPor = datos[indx].usuario.username;
		actividad.creadoAl = datos[indx].creadoAl.substring(0,11);

		actividades.push(actividad);
	}

	$('#conteo-actividades').text(' ' + actividades.length + ' / 5 ');

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
		componente.creadoPor = datos[indx].usuario.username;
		componente.creadoAl = datos[indx].creadoAl.substring(0,11);

		componentes.push(componente);
	}

	$('#tablink-componentes > span').text(componentes.length + ' / 2');

	if(componentes.length == 0){
		$(grid_componentes + ' > table > tbody').append('<tr><td colspan="6" style="text-align:left"><i class="fa fa-info-circle"></i> No hay datos</td></tr>');
	}else{
		componenteDatagrid.cargarDatos(componentes);
	}
}

function reset_modal_form(formulario){
    $(formulario).get(0).reset();
    $(formulario + ' input[type="hidden"]').val('');
    $(formulario + ' input[type="hidden"]').change();
    
    $(formulario + ' .chosen-one').trigger('chosen:updated');
    Validation.cleanFormErrors(formulario);
    if(formulario == form_componente){
    	$(modal_componente + ' .alert').remove();
    	$('#id-componente').val('');
    	//$(grid_actividades + ' > table > tbody').empty();
    	$('#conteo-actividades').text(' 0 / 5 ');
    	$(grid_actividades + ' > table > tbody').html('<tr><td colspan="6" style="text-align:left"><i class="fa fa-info-circle"></i> No hay datos</td></tr>');
    }
    if(formulario == form_actividad){
    	$('#id-actividad').val('');
    	$('#lista-tabs-actividad a:first').tab('show');
    	$(modal_actividad + ' .alert').remove();
    }
}

//
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

//
function sumar_totales(tipo,campo_suma,campo_total,mensaje){
	var sub_total = 0;
	$(tipo).each(function(){
		sub_total += parseInt($(this).val()) || 0;
	});
	$('#'+campo_suma).text(sub_total);
	if(parseInt($('#'+campo_total).val()) != sub_total){
		Validation.printFieldsErrors(campo_suma,mensaje);
	}else{
		Validation.cleanFieldErrors(campo_suma);
	}
}

function ejecutar_formula(identificador){	
	var numerador = parseInt($('#numerador-'+identificador).val()) || 0;
	var denominador = parseInt($('#denominador-'+identificador).val()) || 1;
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
	$('#meta-'+identificador).val(total).change();
}

function actualizar_eventos_metas(){
	$('.metas-mes').on('change',function(){
		var mes = $(this).data('meta-mes');
		var trimestre = Math.ceil(mes/3);
		var identificador = $(this).data('meta-identificador');
		
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
				suma += parseInt($(this).val()) || 0;
			});
		}
		
		$('#trim'+trimestre+'-'+identificador).val(suma).change();

		var trim1 = parseInt($('#trim1-'+identificador).val()) || 0;
		var trim2 = parseInt($('#trim2-'+identificador).val()) || 0;
		var trim3 = parseInt($('#trim3-'+identificador).val()) || 0;
		var trim4 = parseInt($('#trim4-'+identificador).val()) || 0;

		suma = trim1 + trim2 + trim3 + trim4;

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
	$('.control-bloqueado').each(function(){
		$(this).prop('disabled',true);
		$('label[for="' + $(this).attr('id') + '"]').prepend('<span class="fa fa-lock"></span> ');
	});
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