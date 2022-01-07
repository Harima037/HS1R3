/*=====================================

    # Nombre:
        lista-estrategia-rendicion.js

    # Módulos:
        seguimiento/seguimiento-prog

    # Descripción:
        Funciones para seguimiento de metas de estrategias institucionales

=====================================*/

var moduloResource = new RESTfulRequests(SERVER_HOST+'/v1/seguimiento-estrategia');
var moduloDatagrid = new Datagrid("#datagridEstrategia",moduloResource,{ formatogrid:true, pagina: 1, clasificacionProyecto: 1});

var trimactual = '';

moduloDatagrid.init();
moduloDatagrid.actualizar({
    _success: function(response){
        console.log(response.data);
        moduloDatagrid.limpiar();
        var datos_grid = [];
        var trimestre = $('#datagridEstrategia').attr('data-trim-activo');
		trimactual = trimestre;
        var label_lock = '<span class=""><span class="fa fa-lock"></span></span>';
        var label_miss = '<span class="text-muted"><span class="fa fa-times"></span></span>';
        var estilo_programado = 'style="background-color:#DDDDDD"';
        for(var i in response.data){
            var estilos = [];
            for(var j = 1; j <= 4; j++){
                if(response.data[i]['trim'+j]){
                    estilos[j] = estilo_programado;
                }else{
                    estilos[j] = '';
                }
            }
        

            var item = {
                'id':       response.data[i].id,
                //'programa': response.data[i].clave + ' ' + response.data[i].programa,
                'descripcion': response.data[i].descripcion,
                'trim_1':   '<div class="text-center" '+estilos[1]+'>'+((trimestre > 1)?label_miss:label_lock)+'</div>',
                'trim_2':   '<div class="text-center" '+estilos[2]+'>'+((trimestre > 2)?label_miss:label_lock)+'</div>',
                'trim_3':   '<div class="text-center" '+estilos[3]+'>'+((trimestre > 3)?label_miss:label_lock)+'</div>',
                'trim_4':   '<div class="text-center" '+estilos[4]+'>'+((trimestre > 4)?label_miss:label_lock)+'</div>',
                'estado':   '<span class="text-muted">Inactivo</span>'
            };

            //if(response.data[i].evaluacion_trimestre.length){
                if(response.data[i].idEstatus == 1){
                    item['estado'] = '<span class="label label-success">En Trámite</span>';
                }else if(response.data[i].idEstatus == 2){
                    item['estado'] = '<span class="label label-warning">En Revisión</span>';
                }else if(response.data[i].idEstatus == 3){
                    item['estado'] = '<span class="label label-danger">En Corrección</span>';
                }else if(response.data[i].idEstatus == 4){
                    item['estado'] = '<span class="label label-info">Registrado</span>';
                }else if(response.data[i].idEstatus == 5){
                    item['estado'] = '<span class="label label-primary">Firmado</span>';
                }
            //}

            if(trimestre > 0){
                item['trim_'+trimestre] = '<div class="text-center" '+estilos[trimestre]+'><span id="grid-trim-'+trimestre+'" class=""><span class="fa fa-unlock"></span></span></div>'; 
            }

            for(var j in response.data[i].registro_avance){
                var avance = response.data[i].registro_avance[j];
                var clase_icono = (avance.trimestre == trimestre)?'fa-unlock':'fa-circle';
                if(parseInt(avance.justificacion) > 0){
                    item['trim_'+avance.trimestre] = '<div class="text-center" '+estilos[avance.trimestre]+'><span id="grid-trim-'+avance.trimestre+'" class="text-danger"><span class="fa '+clase_icono+'"></span></span></div>';
                }else{
                    item['trim_'+avance.trimestre] = '<div class="text-center" '+estilos[avance.trimestre]+'><span id="grid-trim-'+avance.trimestre+'" class="text-success"><span class="fa '+clase_icono+'"></span></span></div>';
                }
            }

            datos_grid.push(item);
        }
        moduloDatagrid.cargarDatos(datos_grid);
        moduloDatagrid.cargarTotalResultados(response.resultados,'<b>Estrategia(s)</b>');
        var total = parseInt(response.resultados/moduloDatagrid.rxpag); 
        var plus = parseInt(response.resultados)%moduloDatagrid.rxpag;
        if(plus>0) 
            total++;
        moduloDatagrid.paginacion(total);
    }
});

function cargar_datos_estrategia(e){
    var parametros = {'mostrar':'datos-estrategia-avance'};
    moduloResource.get(e,parametros,{
        _success: function(response){
            $('#modalDatosSeguimiento').find(".modal-title").html('Detalles del avance');
            $('#lbl-descripcion').text(response.data.descripcionIndicador);
            $('#unidad-responsable').text(response.data.claveUnidadResponsable + ' ' + response.data.unidadResponsable);
            var datos_programa = {};
			
            $('#fuente-informacion').val(response.data.fuenteInformacion);

            if(response.data.responsables.length){
                var opciones_responsables = '<option value="">Seleccione un responsable</option>';
                for(var i in response.data.responsables){
                    var responsable = response.data.responsables[i];
                    opciones_responsables += '<option value="'+responsable.id+'" data-cargo="'+responsable.cargo+'">'+responsable.nombre+'</option>';
                }
                $('#responsable-informacion').html(opciones_responsables);
                $('#descripcion-cargo-responsable').text('');
            }
            $('#responsable-informacion').val(response.data.idResponsable).change();

			$('#btn-firmar').hide();			
            
            for(var j in response.data.evaluacion_trimestre)
			{
				var evaltrim = response.data.evaluacion_trimestre[j];				
				if(evaltrim.trimestre == trimactual && evaltrim.idEstatus == 4)
					$('#btn-firmar').show();
			}
            //for(var i in response.data.indicadores){
                //var indicador = response.data.indicadores[i];
                var estrategia = response.data;
                datos_programa[estrategia.id] = {
                    'id': estrategia.id,
                    'claveTipo': estrategia.TipoIndicadorMeta,
                    'nombre': estrategia.descripcionIndicador,
                    'metas': {
                        1: (parseFloat(estrategia.trim1) || 0) + 0,
                        2: (parseFloat(estrategia.trim1) || 0) + (parseFloat(estrategia.trim2) || 0) + 0,
                        3: (parseFloat(estrategia.trim1) || 0) + (parseFloat(estrategia.trim2) || 0) + (parseFloat(estrategia.trim3) || 0) + 0,
                        4: (parseFloat(estrategia.trim1) || 0) + (parseFloat(estrategia.trim2) || 0) + (parseFloat(estrategia.trim3) || 0) + (parseFloat(estrategia.trim4) || 0) + 0
                    },
                    'avances': {
                        1:0,
                        2:0,
                        3:0,
                        4:0
                    }
                }

                if(estrategia.registro_avance.length){
                    for(var j in estrategia.registro_avance){
                        var avance = estrategia.registro_avance[j];
                        datos_programa[estrategia.id]['avances'][avance.trimestre] = parseFloat(avance.avance);
                    }
                }
            //}

            for(var i = 1; i <= 4; i++){
                var html_tbody = '';
                var total_acumulado = 0;
                var total_avance = 0;
                for(var j in datos_programa){
                    var indicador = datos_programa[j];

                    html_tbody += '<tr data-clave="'+indicador['claveTipo']+'" data-id="'+indicador['id']+'">';
                    html_tbody += '<td>'+indicador['claveTipo']+'</td>'
                    html_tbody += '<td>'+indicador['nombre']+'</td>'
                    html_tbody += '<td class="meta-acumulada" data-meta="'+indicador['metas'][i]+'">'+indicador['metas'][i]+'</td>';
                    html_tbody += '<td class="avance-acumulado" data-avance="'+indicador['avances'][i]+'">'+indicador['avances'][i]+'</td>';
                    html_tbody += '</tr>';

                    total_acumulado += indicador['metas'][i];
                    total_avance += indicador['avances'][i];
                }
                $('#avance-trim-'+i+' > tbody').empty();
                $('#avance-trim-'+i+' > tbody').append(html_tbody);
                $('#total-programado-trim-'+i).text(total_acumulado.format(2));
                $('#total-avance-trim-'+i).text(total_avance.format(2));
            };

            $('#btn-editar-avance').attr('data-id-estrategia',e);
            
            /*if(response.data.evaluacion_trimestre.length){
                if(response.data.evaluacion_trimestre[0].idEstatus == 4 || response.data.evaluacion_trimestre[0].idEstatus == 5){
                    $('#btn-reporte').removeClass('hidden');
                }else{
                    $('#btn-reporte').addClass('hidden');
                }
            }else{
                $('#btn-reporte').addClass('hidden');
            }*/

            $('#modalDatosSeguimiento').modal('show');
        }
    });
}

$('#responsable-informacion').on('change',function(){
    if($('#responsable-informacion').val() != ''){
        var cargo_seleccionado = $('#responsable-informacion option:selected').attr('data-cargo');
    }else{
        var cargo_seleccionado = '';
    }
    $('#descripcion-cargo-responsable').text(cargo_seleccionado);
});

$('#btn-guardar-informacion').on('click',function(){
    Validation.cleanFormErrors('#form_fuente_informacion');
    if(($('#fuente-informacion').val().trim() == '') || (!$('#responsable-informacion').val())){
        if($('#fuente-informacion').val().trim() == ''){
            Validation.printFieldsErrors('fuente-informacion','Este campo es requerido');
        }
        if(!$('#responsable-informacion').val()){
            Validation.printFieldsErrors('responsable-informacion','Este campo es requerido');
        }
        return false;
    }
    //
    var parametros = $('#form_fuente_informacion').serialize();
    parametros += '&actualizarestrategia=datos-informacion';
    moduloResource.put($('#btn-editar-avance').attr('data-id-estrategia'),parametros,{
        _success: function(response){
            MessageManager.show({data:'La información fue almacenada con éxito.',type:'OK',timer:3});
            moduloDatagrid.actualizar();
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
});

$('#btn-firmar').on('click',function(){
	var parametros = 'actualizarestrategia=firmar';	
	Confirm.show({
				titulo:"¿Poner en estatus de firma el avance de este trimestre?",
				mensaje: "¿Estás seguro de poner el estatus de firma el avance del trimestre actual? Una vez realizado esto, no es posible comentar o corregir por el trimestre corriente.",
				callback: function(){
					moduloResource.put($('#btn-editar-avance').attr('data-id-estrategia'),parametros,{
			        	_success: function(response){
			    	        MessageManager.show({data:'Se ha env',type:'OK',timer:3});
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

//rend-cuenta-inst-editar
$('#btn-editar-avance').on('click',function(){
    window.location.href = SERVER_HOST+'/revision/avance-estrategia/' + $('#btn-editar-avance').attr('data-id-estrategia');
});


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