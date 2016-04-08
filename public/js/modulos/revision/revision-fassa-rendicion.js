/*=====================================

    # Nombre:
        lista-fassa-rendicion.js

    # Módulos:
        rend-cuenta-fassa/rend-cuenta-fassa

    # Descripción:
        Funciones para captura de la rendicion de cuentas de los indicadores del FASSA

=====================================*/

var moduloResource = new RESTfulRequests(SERVER_HOST+'/v1/revision-rendicion-fassa');
var moduloDatagrid = new Datagrid("#datagridIndicadores",moduloResource,{ formatogrid:true, pagina: 1});
var tipoRevision = 'none';
var comentariosArray = [];

moduloDatagrid.init();
//moduloDatagrid.actualizar();
moduloDatagrid.actualizar({
    _success: function(response){
        moduloDatagrid.limpiar();
        var datos_grid = [];
        for(var i in response.data){
            var item = {};

            item.id = response.data[i].id;
            item.indicador = response.data[i].indicador;
            //item.otro = response.data[i].unidadResponsable;

            var trim_cerrado = '<div class="text-center"><span class="fa fa-lock"></span></div>';

            if(response.data[i].idEstatus >= 4){
                var trim_actual = response.mes_actual/3;
                var trim_abierto = '<div class="text-center"><span class="fa fa-unlock"></span></div>';
                var trim_perdido = '<div class="text-center text-muted"><span class="fa fa-times"></span></div>';

                if(response.data[i].claveFrecuencia == 'A'){
                    item.trim1 = '<div class="text-center text-muted"><span class="fa fa-minus"></span></div>';
                    item.trim2 = '<div class="text-center text-muted"><span class="fa fa-minus"></span></div>';
                    item.trim3 = '<div class="text-center text-muted"><span class="fa fa-minus"></span></div>';
                    item.trim4 = (trim_actual == 4)?trim_abierto:(trim_actual > 4)?trim_perdido:trim_cerrado;
                    /*if(response.mes_actual == 12 && (response.data[i].idEstatus == 4 || response.data[i].idEstatus == 5)){
                        item.trim4 = '<div class="text-center"><span class="fa fa-unlock"></span></div>';
                    }else{
                        item.trim4 = '<div class="text-center"><span class="fa fa-lock"></span></div>';
                    }*/
                }else if(response.data[i].claveFrecuencia == 'S'){
                    item.trim1 = '<div class="text-center text-muted"><span class="fa fa-minus"></span></div>';
                    item.trim2 = (trim_actual == 2)?trim_abierto:(trim_actual > 2)?trim_perdido:trim_cerrado;
                    item.trim3 = '<div class="text-center text-muted"><span class="fa fa-minus"></span></div>';
                    item.trim4 = (trim_actual == 4)?trim_abierto:(trim_actual > 4)?trim_perdido:trim_cerrado;
                }else{
                    item.trim1 = (trim_actual == 1)?trim_abierto:(trim_actual > 1)?trim_perdido:trim_cerrado;
                    item.trim2 = (trim_actual == 2)?trim_abierto:(trim_actual > 2)?trim_perdido:trim_cerrado;
                    item.trim3 = (trim_actual == 3)?trim_abierto:(trim_actual > 3)?trim_perdido:trim_cerrado;
                    item.trim4 = (trim_actual == 4)?trim_abierto:(trim_actual > 4)?trim_perdido:trim_cerrado;
                }
            }else{
                item.trim1 = trim_cerrado;
                item.trim2 = trim_cerrado;
                item.trim3 = trim_cerrado;
                item.trim4 = trim_cerrado;
            }

            var clase_estatus = '';
            switch(response.data[i].idEstatus){
                case 1: clase_estatus = 'label-info'; break;
                case 2: clase_estatus = 'label-warning'; break;
                case 3: clase_estatus = 'label-danger'; break;
                case 4: clase_estatus = 'label-primary'; break;
                case 5: clase_estatus = 'label-success'; break;
                default: clase_estatus = 'label-default'; break;
            }
            item.metas = '<span class="label '+clase_estatus+'">'+response.data[i].estatus+'</span>';
            item.avance = '<span class="text-muted">Inactivo</span>';

            if(response.data[i].registro_avance.length){
                for(var j in response.data[i].registro_avance){
                    var avance = response.data[i].registro_avance[j];
                    var clase_desempenio = '';
                    var clase_candado = '';
                    var clase_estatus = '';

                    if(avance.justificacion){
                        clase_desempenio = 'text-danger';
                    }else{
                        clase_desempenio = 'text-success';
                    }

                    if(response.mes_actual == avance.mes){
                        if(avance.idEstatus == 2 || avance.idEstatus == 4 || avance.idEstatus == 5){
                            clase_candado = 'fa-lock';
                        }else{
                            clase_candado = 'fa-unlock';
                        }

                        switch(avance.idEstatus){
                            case 1: clase_estatus = 'label-info'; break;
                            case 2: clase_estatus = 'label-warning'; break;
                            case 3: clase_estatus = 'label-danger'; break;
                            case 4: clase_estatus = 'label-primary'; break;
                            case 5: clase_estatus = 'label-success'; break;
                        }
                        item.avance = '<span class="label '+clase_estatus+'">'+avance.estatus+'</span>';
                    }else{
                        clase_candado = 'fa-circle';
                    }

                    var trim = parseInt(avance.mes/3);
                    item['trim'+trim] = '<div class="text-center '+clase_desempenio+'"><span class="fa '+clase_candado+'"></span></div>';
                }
            }

            datos_grid.push(item);
        }
        moduloDatagrid.cargarDatos(datos_grid);
        var total = parseInt(response.resultados/moduloDatagrid.rxpag); 
        var plus = parseInt(response.resultados)%moduloDatagrid.rxpag;
        if(plus>0) 
            total++;
        moduloDatagrid.paginacion(total);
    },
    _error: function(jqXHR){
        //console.log('{ error => "'+jqXHR.status +' - '+ jqXHR.statusText +'", response => "'+ jqXHR.responseText +'" }'); 
        var json = $.parseJSON(jqXHR.responseText);
        if(json.code == "W00"){
            moduloDatagrid.limpiar();
            var colspan = $(moduloDatagrid.selector + " thead > tr th").length;
            $(moduloDatagrid.selector + " tbody").append("<tr><td colspan='"+colspan+"' style='text-align:left'><i class='fa fa-info-circle'></i> "+json.data+"</td></tr>");

        }else{
            json.type = 'ERR';
            MessageManager.show(json);
            moduloDatagrid.limpiar();
        }
        
    }
});

$('#denominador,#numerador').on('keyup',function(){
    $(this).change();
});
$('#denominador,#numerador').on('change',function(){
    var porcentaje = 0;
    if($('#tipo-formula').val()){
        var numerador = parseFloat($('#numerador').val()) || 0;
        var denominador = parseFloat($('#denominador').val()) || 1;
        if($('#tipo-formula').val() == 'T'){
            porcentaje = parseFloat((numerador * 100000)/denominador);
        }else{
            porcentaje = parseFloat((numerador * 100)/denominador);
        }
        $('#porcentaje').text(porcentaje.format(2) + ' %');
    }else{
        $('#porcentaje').text('%');
    }
    $('#porcentaje').attr('data-valor',porcentaje);
});

$('#avance-denominador,#avance-numerador').on('keyup',function(){
    $(this).change();
});
$('#avance-denominador,#avance-numerador').on('change',function(){
    var porcentaje_avance = 0;
    if($('#tipo-formula').val()){
        var numerador = parseFloat($('#avance-numerador').val()) || 0;
        var denominador = parseFloat($('#avance-denominador').val()) || 1;
        if($('#tipo-formula').val() == 'T'){
            porcentaje_avance = parseFloat((numerador * 100000)/denominador);
        }else{
            porcentaje_avance = parseFloat((numerador * 100)/denominador);
        }
        $('#avance-porcentaje').text(porcentaje_avance.format(2) + ' %');
    }else{
        $('#avance-porcentaje').text('%');
    }
    $('#avance-porcentaje').attr('data-valor',porcentaje_avance);

    var porcentaje_total = 0;
    if(porcentaje_avance){
        var porcentaje_meta = parseFloat($('#porcentaje').attr('data-valor'));
        porcentaje_total = (porcentaje_avance / porcentaje_meta)*100;
        $('#porcentaje-total').text(porcentaje_total.format(2) + ' %')
    }else{
        $('#porcentaje-total').text('%');
    }
    actualizar_porcentaje(porcentaje_total);
});

$('#modalIndicador').on('hidden.bs.modal',function(){

	for(var i in comentariosArray)
	{
		var comen = comentariosArray[i];
		var objetoADesColorear= '#'+comen[1];
		//console.log(objetoADesColorear);
		$(objetoADesColorear).parent().parent().removeClass('has-error has-feedback');
	}
	comentariosArray.length = 0;
	
	$('#id-estatus-meta').val("");
	$('#id-estatus-avance').val("");
	tipoRevision = 'none';
	
    $('#form_indicador_fassa').get(0).reset();
    $('#form_indicador_fassa #id').val("");
    $('#form_indicador_fassa #id-avance').val("");
    $('#form_indicador_fassa .seguro-edicion').remove();
    $('#form_indicador_fassa .form-control').prop('disabled',false);
    $('#estatus-programacion').empty();
    $('#estatus-avance').empty();
    $('#porcentaje').text('%');
    $('#porcentaje').attr('data-valor','');
    $('#avance-porcentaje').text('%');
    $('#avance-porcentaje').attr('data-valor','');
    $('#avance-denominador').html('&nbsp;');
    $('#avance-numerador').html('&nbsp;');
    $('#justificacion').html('&nbsp;');
    $('#porcentaje-total').text('%');
    $('#porcentaje-total').attr('data-valor','');
    $('#justificacion').prop('disabled',true);
    $('#panel-avance-fassa').removeClass('hidden');
    $('#panel-programacion-fassa').removeClass('hidden');
    Validation.cleanFormErrors('#form_indicador_fassa');

});


function escribirComentario(idcampo,nombrecampo,objetoconinformacion, tipo)
{
	if(tipoRevision==tipo)
	{
		$('#modalComentario').find(".modal-title").html("<i class=\"fa fa-pencil-square-o\"></i> Escribir comentario");    
		$('#lbl-nombredelcampo').html(nombrecampo);
		$('#idcampo').val(idcampo);
		
		if(tipoRevision == 'avance')
			$('#idavance').val($('#id-avance').val());
		else
			$('#idavance').val('0');
			
		$('#lbl-informacioncampo').text($('#'+objetoconinformacion).text());
		var borrarAreaText = 1;
	
		for(var i = 0; i < comentariosArray.length; i++)
		{
			var arrayTemporal = comentariosArray[i];
			if(arrayTemporal[1]==idcampo)
			{
				$('#idproyectocomentarios').val(arrayTemporal[0]);
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
	else
	{
		MessageManager.show({data:'El estatus del proyecto impide comentar este elemento',timer:4,type:'ADV'});
	}
}

function editar(e){
    moduloResource.get(e,null,{
        _success: function(response){
            var nivel = '';
            switch(response.data.claveNivel){
                case 'F': nivel = 'Fin'; break;
                case 'P': nivel = 'Proposito'; break;
                case 'C': nivel = 'Componente'; break;
                case 'A': nivel = 'Actividad'; break;
            }
            $('#modalIndicador').find(".modal-title").html('Nivel: ' + nivel);

            $('#indicador').text(response.data.indicador);
            $('#tipo-formula').val(response.data.claveTipoFormula);
            $('#formula').text(response.data.formula);

            $('#numerador').text(parseFloat(response.data.numerador));
            $('#denominador').text(parseFloat(response.data.denominador));
            if(response.data.porcentaje){
                $('#porcentaje').text(parseFloat(response.data.porcentaje).format(2) + ' %');
                $('#porcentaje').attr('data-valor',response.data.porcentaje);
            }

            var label_class = '';
            switch(response.data.idEstatus){
                case 1: label_class = 'label-info'; break;
                case 2: label_class = 'label-warning'; break;
                case 3: label_class = 'label-danger'; break;
                case 4: label_class = 'label-primary'; break;
                case 5: label_class = 'label-success'; break;
            }

            var inicio = 1;
            var final = 4;
            var incremento = 1;
            if(response.data.claveFrecuencia == 'A'){
                inicio = 4;
                final = 4;
                incremento = 1;
            }else if(response.data.claveFrecuencia == 'S'){
                inicio = 2;
                final = 4;
                incremento = 2;
            }

            if(response.data.idEstatus == 2 || response.data.idEstatus == 4){
                tipoRevision = 'meta';
                //console.log(response.data.comentario);                
            }

            var rows = '';
            for (var i = inicio; i <= final; i = i + incremento) {
                rows += '<tr>' +
                            '<th width="20%">Trimestre '+i+'</th>' +
                            '<td>' +
                                    
                                '<div class="input-group">' +
                                    '<span class="input-group-btn" onclick="escribirComentario(\'numerador-'+i+'\',\'Numerador\',\'numerador-'+i+'\',\'meta\');">' +
                                        '<span class="btn btn-default"> <i class="fa fa-pencil-square-o"></i></span>' +
                                    '</span>' +
                                    '<p id="numerador-'+i+'" class="form-control informacion-meta" style="height:auto">&nbsp;</p>' +
                                '</div>' +

                            '</td>' +
                            '<td>' +

                                '<div class="input-group">' +
                                    '<span class="input-group-btn" onclick="escribirComentario(\'denominador-'+i+'\',\'Denominador\',\'denominador-'+i+'\',\'meta\');">' +
                                        '<span class="btn btn-default"> <i class="fa fa-pencil-square-o"></i></span>' +
                                    '</span>' +
                                    '<p id="denominador-'+i+'" class="form-control informacion-meta" style="height:auto">&nbsp;</p>' +
                                '</div>' +

                            '</td>' +
                            '<td width="20%">' +
                                '<span class="form-control" id="porcentaje-'+i+'">%</span>' +
                            '</td>' +
                        '</tr>';
            }
            
            $('#table-programacion-trimestres tbody').html(rows);
			
			$('#id-estatus-meta').val(response.data.idEstatus);

            var label_html = '<div class="text-center '+label_class+'"><span class="label"><big>'+response.data.estatus+'</big></span></div>';
            $('#estatus-programacion').html(label_html);
            $('#estatus-programacion-trimestre').html(label_html);
            //if(response.data.idEstatus == 2 || response.data.idEstatus == 4 || response.data.idEstatus == 5){
            bloquear_controles('.informacion-meta');
            //}

            for(var i in response.data.metas_trimestre){
                var meta = response.data.metas_trimestre[i];
                $('#numerador-'+meta.trimestre).text(parseFloat(meta.numerador).format(2));
                $('#denominador-'+meta.trimestre).text(parseFloat(meta.denominador).format(2));
                $('#porcentaje-'+meta.trimestre).text(parseFloat(meta.porcentaje).format(2)+' %');
            }

			for(var j in response.data.comentario)
			{
				var come = response.data.comentario[j];
				
				if(come.idAvance==0)
					comentariosArray.push([come.id, come.idCampo, come.observacion, come.idAvance]);
				else
					if(come.mes == response.data.mes_actual)
						comentariosArray.push([come.id, come.idCampo, come.observacion, come.idAvance]); 
			}
            
            var puede_editar_avance = false;
            if(response.data.idEstatus == 4 || response.data.idEstatus == 5){
                if(response.data.claveFrecuencia == 'A' && response.data.mes_actual == 12){
                    puede_editar_avance = true;
                }else if(response.data.claveFrecuencia == 'T' && (response.data.mes_actual%3) == 0){
                    puede_editar_avance = true;
                }else if(response.data.claveFrecuencia == 'S' && (response.data.mes_actual == 6 || response.data.mes_actual == 12)){
                    puede_editar_avance = true;
                }
            }

            label_html = '<div class="text-center text-muted"><big>Inactivo</big></div>';
            if(!puede_editar_avance){
                bloquear_controles('.informacion-avance');
                $('#panel-avance-fassa').addClass('hidden');
            }else{
                $('#panel-programacion-fassa').addClass('hidden');

                var trimestre_actual = Math.floor(response.data.mes_actual/3);
                var numerador = 0;
                var denominador = 0;
                var porcentaje = 0;

                for(var i in response.data.metas_trimestre){
                    var meta = response.data.metas_trimestre[i];
                    if(meta.trimestre == trimestre_actual){
                        denominador = meta.denominador;
                        numerador = meta.numerador;
                        porcentaje = meta.porcentaje;
                    }
                }

                $('#numerador-trimestre').text((parseFloat(numerador) || 0).format(2));
                $('#denominador-trimestre').text((parseFloat(denominador) || 0).format(2));
                if(porcentaje){
                    $('#porcentaje-trimestre').text(parseFloat(porcentaje).format(2) + ' %');
                    $('#porcentaje-trimestre').attr('data-valor',porcentaje);
                }

                if(response.data.registro_avance.length){
                    for(var i in response.data.registro_avance){
                        var avance = response.data.registro_avance[i];
                        if(avance.mes == response.data.mes_actual){
                            $('#avance-denominador').text(parseFloat(avance.denominador));
                            $('#avance-numerador').text(parseFloat(avance.numerador));
                            $('#avance-porcentaje').text(parseFloat(avance.porcentaje).format(2) + ' %');
                            $('#justificacion').text(avance.justificacionAcumulada);
                            var porcentaje_total = (avance.porcentaje/porcentaje)*100;
                            actualizar_porcentaje(porcentaje_total);

                            label_class = '';
                            switch(avance.idEstatus){
                                case 1: label_class = 'label-info'; break;
                                case 2: label_class = 'label-warning'; break;
                                case 3: label_class = 'label-danger'; break;
                                case 4: label_class = 'label-primary'; break;
                                case 5: label_class = 'label-success'; break;
                            }
							$('#id-estatus-avance').val(avance.idEstatus);							
                            label_html = '<div class="text-center '+label_class+'"><span class="label"><big>'+avance.estatus+'</big></span></div>';
                            $('#id-avance').val(avance.id);

                            //if(avance.idEstatus == 2 || avance.idEstatus == 4 || avance.idEstatus == 5){
                                bloquear_controles('.informacion-avance');
                            //}
							if(avance.idEstatus == 2 || avance.idEstatus == 4){
								tipoRevision = 'avance';
								//console.log(response.data.comentario);
							}

                        }
                    }
                }
            }
            $('#estatus-avance').html(label_html);
			
			for(var k in comentariosArray)
			{
				var come = comentariosArray[k];
				var objetoAColorear = '#'+come[1];
				$(objetoAColorear).parent().parent().addClass('has-error has-feedback');
			}

            $('#id').val(response.data.id);
            $('#modalIndicador').modal('show');
        }
    });
}

$('#btn-guardar-validar-indicador').on('click',function(e){
    e.preventDefault();
    guardar_indicador(true);
});

$('#btn-guardar-indicador').on('click',function(e){
    e.preventDefault();
    guardar_indicador(false);
});

function guardar_indicador(validar){
    Validation.cleanFormErrors('#form_indicador_fassa');

    var parametros = $("#form_indicador_fassa").serialize();

    if(validar){
        parametros += '&validar=1';
    }

    moduloResource.put($('#id').val(), parametros,{
        _success: function(response){
            moduloDatagrid.actualizar();
            if(validar){
                MessageManager.show({data:'Los datos fueron enviados para su validación',timer:4,type:'OK'});
            }else{
                MessageManager.show({data:'Elemento actualizado con éxito',timer:4});
            }
            $('#modalIndicador').modal('hide');
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
    },'Guardando');
}

function actualizar_porcentaje(porcentaje_total){
    $('#porcentaje-total').attr('data-valor',porcentaje_total);
    if(porcentaje_total < 100){
        $('#porcentaje-total').html('<span class="text-danger"><span class="fa fa-arrow-down"></span> ' + porcentaje_total.format(2) + ' %</span>');
        $('#justificacion').prop('disabled',false);
    }else if(porcentaje_total > 100){
        $('#porcentaje-total').html('<span class="text-danger"><span class="fa fa-arrow-up"></span> ' + porcentaje_total.format(2) + ' %</span>');
        $('#justificacion').prop('disabled',false);
    }else{
        $('#porcentaje-total').html('<span class="text-success">' + porcentaje_total.format(2) + ' %</span>');
        $('#justificacion').prop('disabled',true);
    }
}

function bloquear_controles(identificador){
    //'input,textarea,select'
    $(identificador).each(function(){
        $(this).prop('disabled',true);
        $('label[for="' + $(this).attr('id') + '"]').prepend('<span class="seguro-edicion fa fa-lock"></span> ');
        if($(this).hasClass('chosen-one')){
            $(this).trigger('chosen:updated');
        }
    });
}
/*             Extras               */
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


$('#btnGuardarComentario').on('click',function(){
	
	var parametros = $(formComentario).serialize();
	parametros = parametros + '&idproyecto=' + $('#id').val();
	
	var objetoQueSeColorea = '#'+$('#idcampo').val();

	if($('#comentario').val()=="")
	{
		MessageManager.show({data:'Debe escribir un comentario antes de guardar',type:'ADV',timer:3});		
	}
	else
	{
		if($('#idproyectocomentarios').val()=='')//Nuevo comentario
		{
			moduloResource.post(parametros,{
		        _success: function(response){
	    	        MessageManager.show({data:'Datos del comentario almacenados con éxito',type:'OK',timer:3});	
					$(objetoQueSeColorea).parent().parent().addClass('has-error has-feedback');
					comentariosArray.push([response.data.id, $('#idcampo').val(), $('#comentario').val(), $('#idavance').val()]);
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
			moduloResource.put($('#idproyectocomentarios').val(),parametros,{
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
			titulo:"Eliminar comentario",
			mensaje: "¿Estás seguro de eliminar el comentario seleccionado?",
				//si: 'Actualizar',
				//no: 'No, gracias',
				callback: function(){
					moduloResource.delete($('#idproyectocomentarios').val(),null,{
                        _success: function(response){ 
                        	MessageManager.show({data:'Comentario eliminado con éxito.',type:'ADV',timer:3});

							var arrayTemporal = [];
							
							for(var i = 0; i < comentariosArray.length; i++)
								if(comentariosArray[i][0]!=$('#idproyectocomentarios').val())
									arrayTemporal.push([comentariosArray[i][0],comentariosArray[i][1],comentariosArray[i][2],comentariosArray[i][3]]);

							comentariosArray.length=0;
							comentariosArray = arrayTemporal;							
							//console.log(comentariosArray);
							
							var objetoADesColorear = '#'+$('#idcampo').val();
							$(objetoADesColorear).parent().parent().removeClass('has-error has-feedback');							
							
							$('#comentario').val('');
							$('#idproyectocomentarios').val('');
							$('#idcampo').val('');
							$('#idavance').val('');
														
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

$('#btnAprobar').on('click',function(){
	
	var aprobar = 0;
	
	if(tipoRevision == 'none')
		MessageManager.show({data:'Debido al estatus que guarda la meta o avance no es posible aprobar actualmente.',type:'ADV',timer:3});
	else if(tipoRevision == 'meta')
	{
		if($('#id-estatus-meta').val()!=2)
			MessageManager.show({data:'Debido al estatus que guarda la meta no es posible aprobarla actualmente.',type:'ADV',timer:3});
		else
		{
			if(comentariosArray.length>0)
				MessageManager.show({data:'No es posible aprobar la meta si tiene comentarios pendientes. Elimine primero todos los comentarios.',type:'ADV',timer:3});
			else
				aprobar = 1;
		}
	}
	else if(tipoRevision == 'avance')
	{
		if($('#id-estatus-avance').val()!=2)
			MessageManager.show({data:'Debido al estatus que guarda el avance no es posible aprobarlo actualmente.',type:'ADV',timer:3});
		else
		{
			if(comentariosArray.length>0)
				MessageManager.show({data:'No es posible aprobar el avance si tiene comentarios pendientes. Elimine primero todos los comentarios.',type:'ADV',timer:3});
			else
				aprobar = 1;
		}
	}
	
	if(aprobar == 1)
	{
		Confirm.show({
			titulo:"Aprobar Meta/Avance",
			mensaje: "¿Estás seguro de aprobar la meta/avance seleccionada(o)?",
			callback: function(){
				var parametros = {'actualizarproyecto':'aprobar','tiporevision':tipoRevision,'idavance':$('#id-avance').val()};
				moduloResource.put($('#id').val(),parametros,{
			        	_success: function(response){
							MessageManager.show({data:'Se ha aprobado con éxito la Meta/avance seleccionado',type:'OK',timer:3});					
							moduloDatagrid.actualizar();
							$('#modalIndicador').modal('hide');
						}
				});
			}
		});			
	}

});

$('#btnRegresar').on('click',function(){
	
	var regresar = 0;
	if(tipoRevision == 'none')
		MessageManager.show({data:'El estatus que guarda la meta o avance indica que ya se ha regresado o aprobado.',type:'ADV',timer:3});
	else if(tipoRevision == 'meta')
	{
		if($('#id-estatus-meta').val()!=2)
			MessageManager.show({data:'Debido al estatus que guarda la meta no es posible regresarla actualmente.',type:'ADV',timer:3});
		else
		{
			if(comentariosArray.length<=0)
				MessageManager.show({data:'No es posible regresar la meta si aún no tiene comentarios. Escriba al menos un comentario, para poder regresar a corregir.',type:'ADV',timer:3});
			else
				regresar = 1;
		}
	}
	else if(tipoRevision == 'avance')
	{
		if($('#id-estatus-avance').val()!=2)
			MessageManager.show({data:'Debido al estatus que guarda el avance no es posible regresarlo actualmente.',type:'ADV',timer:3});
		else
		{
			if(comentariosArray.length<=0)
				MessageManager.show({data:'No es posible regresar el avance si aún no tiene comentarios. Escriba al menos un comentario, para poder regresar a corregir.',type:'ADV',timer:3});
			else
				regresar = 1;
		}
	}
	
	if(regresar == 1)
	{
		Confirm.show({
			titulo:"Regresar Meta/Avance",
			mensaje: "¿Estás seguro de regresar a corrección la meta/avance seleccionada(o)?",
			callback: function(){
				var parametros = {'actualizarproyecto':'regresar','tiporevision':tipoRevision,'idavance':$('#id-avance').val()};
				moduloResource.put($('#id').val(),parametros,{
			        	_success: function(response){
							MessageManager.show({data:'Se ha regresado a corrección con éxito la Meta/avance seleccionado',type:'OK',timer:3});					
							moduloDatagrid.actualizar();
							$('#modalIndicador').modal('hide');
						}
				});
			}
		});			
	}

});

$('#btnFirmar').on('click',function(){
	
	var firmar = 0;
	if(tipoRevision == 'none')
		MessageManager.show({data:'El estatus que guarda la meta o avance indica que ya se ha regresado o aprobado.',type:'ADV',timer:3});
	else if(tipoRevision == 'meta')
	{
		if($('#id-estatus-meta').val()!=4)
			MessageManager.show({data:'Debido al estatus que guarda la meta no es posible firmarla actualmente.',type:'ADV',timer:3});
		else
		{
			if(comentariosArray.length>0)
				MessageManager.show({data:'No es posible firmar la meta si tiene comentarios pendientes. Elimine primero todos los comentarios.',type:'ADV',timer:3});
			else
				firmar = 1;
		}
	}
	else if(tipoRevision == 'avance')
	{
		if($('#id-estatus-avance').val()!=4)
			MessageManager.show({data:'Debido al estatus que guarda el avance no es posible firmarlo actualmente.',type:'ADV',timer:3});
		else
		{
			if(comentariosArray.length>0)
				MessageManager.show({data:'No es posible firmar el comentario si tiene comentarios pendientes. Elimine primero todos los comentarios.',type:'ADV',timer:3});
			else
				firmar = 1;
		}
	}
	
	if(firmar == 1)
	{
		Confirm.show({
			titulo:"Regresar Meta/Avance",
			mensaje: "¿Estás seguro de regresar a corrección la meta/avance seleccionada(o)?",
			callback: function(){
				var parametros = {'actualizarproyecto':'firmar','tiporevision':tipoRevision,'idavance':$('#id-avance').val()};
				moduloResource.put($('#id').val(),parametros,{
			        	_success: function(response){
							MessageManager.show({data:'Se ha firmado con éxito la Meta/avance seleccionado',type:'OK',timer:3});					
							moduloDatagrid.actualizar();
							$('#modalIndicador').modal('hide');
						}
				});
			}
		});			
	}

});


//
//

