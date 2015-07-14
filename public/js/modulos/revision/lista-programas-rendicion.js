/*=====================================

    # Nombre:
        lista-programas-rendicion.js

    # Módulos:
        seguimiento/seguimiento-prog

    # Descripción:
        Funciones para seguimiento de metas de Programas Presupuestales

=====================================*/

var moduloResource = new RESTfulRequests(SERVER_HOST+'/v1/seguimiento-programas');
var moduloDatagrid = new Datagrid("#datagridProgramas",moduloResource,{ formatogrid:true, pagina: 1, clasificacionProyecto: 1});

var trimactual = '';

moduloDatagrid.init();
moduloDatagrid.actualizar({
    _success: function(response){
        moduloDatagrid.limpiar();
        var datos_grid = [];
        var trimestre = $('#datagridProgramas').attr('data-trim-activo');
		trimactual = trimestre;
        var label_lock = '<span class=""><span class="fa fa-lock"></span></span>';
        var label_miss = '<span class="text-muted"><span class="fa fa-times"></span></span>';
        for(var i in response.data){
            var item = {
                'id':       response.data[i].id,
                'programa': response.data[i].clave + ' ' + response.data[i].programa,
                'trim_1':   (trimestre > 1)?label_miss:label_lock,
                'trim_2':   (trimestre > 2)?label_miss:label_lock,
                'trim_3':   (trimestre > 3)?label_miss:label_lock,
                'trim_4':   (trimestre > 4)?label_miss:label_lock,
                'estado':   '<span class="text-muted">Inactivo</span>'
            };

            //if(response.data[i].evaluacion_trimestre.length){
                if(response.data[i].idEstatus == 1){
                    item['estado'] = '<span class="label label-success">En Trámite</span>';
                }else if(response.data[i].idEstatus == 2){
                    item['estado'] = '<span class="label label-warning">En Revisión</span>';
                }else if(response.data[i].idEstatus == 3){
                    item['estado'] = '<span class="label label-danger">En Correción</span>';
                }else if(response.data[i].idEstatus == 4){
                    item['estado'] = '<span class="label label-info">Registrado</span>';
                }else if(response.data[i].idEstatus == 5){
                    item['estado'] = '<span class="label label-primary">Firmado</span>';
                }
            //}

            if(trimestre > 0){
                item['trim_'+trimestre] = '<span id="grid-trim-'+trimestre+'" class=""><span class="fa fa-unlock"></span></span>'; 
            }

            for(var j in response.data[i].registro_avance){
                var avance = response.data[i].registro_avance[j];
                var clase_icono = (avance.trimestre == trimestre)?'fa-unlock':'fa-circle';
                if(parseInt(avance.justificacion) > 0){
                    item['trim_'+avance.trimestre] = '<span id="grid-trim-'+avance.trimestre+'" class="text-danger"><span class="fa '+clase_icono+'"></span></span>';
                }else{
                    item['trim_'+avance.trimestre] = '<span id="grid-trim-'+avance.trimestre+'" class="text-success"><span class="fa '+clase_icono+'"></span></span>';
                }
            }
			/*response.data[i].evaluacion_trimestre[0].idEstatus;
			if(*/
            datos_grid.push(item);
        }
        moduloDatagrid.cargarDatos(datos_grid);                         
        var total = parseInt(response.resultados/moduloDatagrid.rxpag); 
        var plus = parseInt(response.resultados)%moduloDatagrid.rxpag;
        if(plus>0) 
            total++;
        moduloDatagrid.paginacion(total);
    }
});

function cargar_datos_programa(e){
    var parametros = {'mostrar':'datos-programa-avance'};
    moduloResource.get(e,parametros,{
        _success: function(response){
            $('#modalDatosSeguimiento').find(".modal-title").html('Detalles del avance');
            $('#programa-presupuestario').text(response.data.claveProgramaPresupuestario + ' ' +response.data.programaPresupuestario);
            $('#unidad-responsable').text(response.data.claveUnidadResponsable + ' ' + response.data.unidadResponsable);
            var datos_programa = {};
			
			//console.log(trimactual);
			$('#btn-firmar').hide();			
			for(var j in response.data.evaluacion_trimestre)
			{
				var evaltrim = response.data.evaluacion_trimestre[j];				
				if(evaltrim.trimestre == trimactual && evaltrim.idEstatus == 4)
					$('#btn-firmar').show();
			}
            for(var i in response.data.indicadores){
                var indicador = response.data.indicadores[i];
                datos_programa[indicador.id] = {
                    'id': indicador.id,
                    'claveTipo': indicador.claveTipoIndicador,
                    'nombre': indicador.descripcionIndicador,
                    'metas': {
                        1: (parseFloat(indicador.trim1) || 0) + 0,
                        2: (parseFloat(indicador.trim1) || 0) + (parseFloat(indicador.trim2) || 0) + 0,
                        3: (parseFloat(indicador.trim1) || 0) + (parseFloat(indicador.trim2) || 0) + (parseFloat(indicador.trim3) || 0) + 0,
                        4: (parseFloat(indicador.trim1) || 0) + (parseFloat(indicador.trim2) || 0) + (parseFloat(indicador.trim3) || 0) + (parseFloat(indicador.trim4) || 0) + 0
                    },
                    'avances': {
                        1:0,
                        2:0,
                        3:0,
                        4:0
                    }
                }

                if(indicador.registro_avance.length){
                    for(var j in indicador.registro_avance){
                        var avance = indicador.registro_avance[j];
                        datos_programa[indicador.id]['avances'][avance.trimestre] = parseFloat(avance.avance);
                    }
                }
            }

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

            $('#btn-editar-avance').attr('data-id-programa',e);
            
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


$('#btn-firmar').on('click',function(){
	var parametros = 'actualizarprograma=firmar';	
	Confirm.show({
				titulo:"¿Poner en estatus de firma el avance de este trimestre?",
				mensaje: "¿Estás seguro de poner el estatus de firma el avance del trimestre actual? Una vez realizado esto, no es posible comentar o corregir por el trimestre corriente.",
				callback: function(){
					moduloResource.put($('#btn-editar-avance').attr('data-id-programa'),parametros,{
			        	_success: function(response){
			    	        MessageManager.show({data:'Se ha env',type:'OK',timer:3});
							window.location.href = SERVER_HOST+'/revision/seguimiento-programas';
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
    window.location.href = SERVER_HOST+'/revision/avance-programa/' + $('#btn-editar-avance').attr('data-id-programa');
});

/*$('#btn-reporte').on('click',function(){
    window.open(SERVER_HOST+'/v1/reporte-programa/' + $('#btn-editar-avance').attr('data-id-programa'));
});*/

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