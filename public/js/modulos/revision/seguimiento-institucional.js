/*=====================================

    # Nombre:
        seguimiento-institucional.js

    # Módulo:
        revision/segui-proyectos-inst

    # Descripción:
        Para revisión del seguimiento de metas de proyectos institucionales

=====================================*/

// Inicialización General para casi cualquier módulo

var moduloResource = new RESTfulRequests(SERVER_HOST+'/v1/segui-proyectos-inst');
var moduloDatagrid = new Datagrid("#datagridProyectos",moduloResource,{ formatogrid:true, pagina: 1, clasificacionProyecto: 1});
moduloDatagrid.init();
moduloDatagrid.actualizar({
    _success: function(response){
        moduloDatagrid.limpiar();
        var datos_grid = [];
        var mes_activo = $('#datagridProyectos > table').attr('data-mes-activo');
        for(var i in response.data){
            var item = {};

            var mes_activo = $('#datagridProyectos').attr('data-mes-activo'); 
            var trimestre = $('#datagridProyectos').attr('data-trim-activo');

            var mes_inicia = ((trimestre - 1) * 3) + 1;
            var meses = [1,2,3,4,5,6,7,8,9,10,11,12];
            var estado_actual = 0;
            var meses_capturados = {'1':'','2':'','3':'','4':'','5':'','6':'','7':'','8':'','9':'','10':'','11':'','12':''};

            for(var j in response.data[i].componentes_metas_mes){
                var meta = response.data[i].componentes_metas_mes[j];
                if(parseFloat(meta.totalMeta) > 0){
                    meses_capturados[meta.mes] = 'style="background-color:#DDDDDD"';
                }
            }

            for(var j in response.data[i].actividades_metas_mes){
                var meta = response.data[i].actividades_metas_mes[j];
                if(parseFloat(meta.totalMeta) > 0){
                    meses_capturados[meta.mes] = 'style="background-color:#DDDDDD"';
                }
            }

            item.id = response.data[i].id;
            item.clave = response.data[i].clavePresup;
            item.nombre_tecnico = response.data[i].nombreTecnico;

            for(var j in meses){
                if(meses[j] == mes_activo){
                    item['mes_'+meses[j]] = '<div id="grid-mes-'+meses[j]+'" class="text-center" '+meses_capturados[meses[j]]+'><span class="fa fa-unlock"></span></div>';
                }else if(meses[j] < mes_activo){
                    item['mes_'+meses[j]] = '<div id="grid-mes-'+meses[j]+'" class="text-center text-muted" '+meses_capturados[meses[j]]+'><span class="fa fa-times"></span></div>';
                }else{
                    item['mes_'+meses[j]] = '<div id="grid-mes-'+meses[j]+'" class="text-center" '+meses_capturados[meses[j]]+'><span class="fa fa-lock"></span></div>';
                }
            }
			var estatus_anteriores = {};
            item.estado = '<span class="text-muted">Inactivo</span>';
			if(response.data[i].evaluacion_meses.length){
                for(var j in response.data[i].evaluacion_meses){
                    var evaluacion_mes = response.data[i].evaluacion_meses[j];
                    if(evaluacion_mes.mes == mes_activo){
                        if(evaluacion_mes.idEstatus == 1){
                            item.estado = '<span class="label label-info">En Trámite</span>';
                        }else if(evaluacion_mes.idEstatus == 2){
                            item.estado = '<span class="label label-warning">En Revisión</span>';
                            estado_actual = 1;
                        }else if(evaluacion_mes.idEstatus == 3){
                            item.estado = '<span class="label label-danger">En Correción</span>';
                        }else if(evaluacion_mes.idEstatus == 4){
                            item.estado = '<span class="label label-primary">Registrado</span>';
                            estado_actual = 1;
                        }else if(evaluacion_mes.idEstatus == 5){
                            item.estado = '<span class="label label-success">Firmado</span>';
                            estado_actual = 1;
                        }
                    }else{
                        estatus_anteriores[evaluacion_mes.mes] = {idEstatus:evaluacion_mes.idEstatus,planMejora:parseInt(evaluacion_mes.planMejora)};
                    }
                }
            }else{
                item.estado = '<span class="text-muted">Inactivo</span>';
            }

            if(estatus_anteriores){
                for(var j in estatus_anteriores){
                    if(estatus_anteriores[j].idEstatus == 6){
                        if(parseInt(estatus_anteriores[j].planMejora) > 0){
                            item['mes_'+j] = '<div id="grid-mes-'+j+'" class="text-center text-danger" '+meses_capturados[j]+'><span class="fa fa-circle-o"></span></div>';
                        }else{
                            item['mes_'+j] = '<div id="grid-mes-'+j+'" class="text-center text-success" '+meses_capturados[j]+'><span class="fa fa-circle-o"></span></div>';
                        }
                    }
                }
            }

            var fuente_informacion = '';
            if(response.data[i].fuenteInformacion){
                fuente_informacion = 'text-primary';
            }else{
                fuente_informacion = 'text-muted';
            }

            var responsable_informacion = '';
            if(response.data[i].idResponsable){
                responsable_informacion = 'text-primary';
            }else{
                responsable_informacion = 'text-muted';
            }

            item.informacion = '<span class="fa fa-info-circle '+fuente_informacion+'"></span> <span class="fa fa-user '+responsable_informacion+'"></span>';

            for(var j in response.data[i].registro_avance){
                var avance = response.data[i].registro_avance[j];
                var clase_icono = (avance.mes != mes_activo)?'fa-circle':(estado_actual != 0)?'fa-lock':'fa-unlock';
                if(parseInt(avance.planMejora) > 0){
                    item['mes_'+avance.mes] = '<div id="grid-mes-'+avance.mes+'" class="text-center text-danger" '+meses_capturados[avance.mes]+'><span class="fa '+clase_icono+'"></span></div>';
                }else{
                    item['mes_'+avance.mes] = '<div id="grid-mes-'+avance.mes+'" class="text-center text-success" '+meses_capturados[avance.mes]+'><span class="fa '+clase_icono+'"></span></div>';
                }
            }
            
            datos_grid.push(item);
        }
        moduloDatagrid.cargarDatos(datos_grid);
        moduloDatagrid.cargarTotalResultados(response.resultados,'<b>Proyecto(s)</b>');
        var total = parseInt(response.resultados/moduloDatagrid.rxpag); 
        var plus = parseInt(response.resultados)%moduloDatagrid.rxpag;
        if(plus>0) 
            total++;
        moduloDatagrid.paginacion(total);
    }
});

function cargar_datos_proyecto(e){
    var parametros = {'mostrar':'datos-proyecto-avance'};
    moduloResource.get(e,parametros,{
        _success: function(response){
            $('#modalDatosSeguimiento').find(".modal-title").html('Clave: <small>' + response.data.ClavePresupuestaria + '</small>');

            $('#nombre-tecnico').text(response.data.nombreTecnico);
            $('#programa-presupuestario').text(response.data.datos_programa_presupuestario.clave + ' ' + response.data.datos_programa_presupuestario.descripcion);
            $('#funcion').text(response.data.datos_funcion.clave + ' ' + response.data.datos_funcion.descripcion);
            $('#subfuncion').text(response.data.datos_sub_funcion.clave + ' ' + response.data.datos_sub_funcion.descripcion);

            $('#fuente-informacion').val(response.data.fuenteInformacion);
            if(response.data.responsables){
                var html_rows = '<option value="">Seleccione un responsable</option>';
                for(var i in response.data.responsables){
                    var responsable = response.data.responsables[i];
                    html_rows += '<option value="'+responsable.id+'">'+responsable.nombre+'</option>';
                }
                $('#responsable-informacion').html(html_rows);
            }
            $('#responsable-informacion').val(response.data.idResponsable);
            
            var html_tbody = '';
            var contador_componente = 0;
            var contador_actividad = 0;
            for(var i in response.data.componentes){
                contador_componente++;
                contador_actividad = 0;
                var componente = response.data.componentes[i];
                html_tbody += '<tr data-nivel="1" data-id="'+componente.id+'">';
                html_tbody += '<td>C '+contador_componente+'</td>'
                html_tbody += '<td>'+componente.indicador+'</td>'
                html_tbody += '<td data-trim-mes="1">-</td>';
                html_tbody += '<td data-trim-mes="2">-</td>';
                html_tbody += '<td data-trim-mes="3">-</td>';
                html_tbody += '<td class="bg-success" data-total-id="'+componente.id+'">0</td>';
                html_tbody += '</tr>';
                for(var j in componente.actividades){
                    contador_actividad++;
                    var actividad = componente.actividades[j];
                    html_tbody += '<tr data-nivel="2" data-id="'+actividad.id+'">';
                    html_tbody += '<td>A '+contador_componente+'.'+contador_actividad+'</td>'
                    html_tbody += '<td>'+actividad.indicador+'</td>'
                    html_tbody += '<td data-trim-mes="1">-</td>';
                    html_tbody += '<td data-trim-mes="2">-</td>';
                    html_tbody += '<td data-trim-mes="3">-</td>';
                    html_tbody += '<td class="bg-success" data-total-id="'+actividad.id+'">0</td>';
                    html_tbody += '</tr>';
                }
            }
            $('.tabla-avance-trim > tbody').empty();
            $('.tabla-avance-trim > tbody').append(html_tbody);

            var total_trimestres = {1:{},2:{},3:{},4:{}};
            for(var i in response.data.componentes){
                var componente = response.data.componentes[i];
                var sumatoria_componente = {1:0,2:0,3:0,4:0};
                //var sumatorias_trimestres = ;
                for(var j in componente.registro_avance){
                    var avance = componente.registro_avance[j];
                    var trimestre = Math.ceil(parseFloat(avance.mes/3));
                    var ajuste = (trimestre - 1) * 3;
                    var mes_del_trimestre = avance.mes - ajuste;
                    if(avance.planMejora){
                        var colo_texto = 'text-danger';
                    }else{
                        var colo_texto = 'text-primary';
                    }
                    var celda = '<span class="'+colo_texto+'">'+parseFloat(avance.avanceMes).format(2)+'</span>';
                    $('#avance-trim-'+trimestre+' > tbody > tr[data-nivel="1"][data-id="'+componente.id+'"] > td[data-trim-mes="'+mes_del_trimestre+'"]').html(celda);
                    sumatoria_componente[trimestre] += parseFloat(avance.avanceMes);
                    total_trimestres[trimestre][avance.mes] = (parseFloat(total_trimestres[trimestre][avance.mes]) || 0) + parseFloat(avance.avanceMes);
                }
                for(var j in sumatoria_componente){
                    if(j > 1){
                        sumatoria_componente[j] = sumatoria_componente[j] + sumatoria_componente[j-1];
                    }
                    $('#avance-trim-'+j+' > tbody > tr[data-nivel="1"][data-id="'+componente.id+'"] > td[data-total-id="'+componente.id+'"]').html(sumatoria_componente[j].format(2));
                }
                for(var k in componente.actividades){
                    var actividad = componente.actividades[k];
                    var sumatoria_actividad = {1:0,2:0,3:0,4:0};
                    for(var j in actividad.registro_avance){
                        var avance = actividad.registro_avance[j];
                        var trimestre = Math.ceil(parseFloat(avance.mes/3));
                        var ajuste = (trimestre - 1) * 3;
                        var mes_del_trimestre = avance.mes - ajuste;
                        if(avance.planMejora){
                            var colo_texto = 'text-danger';
                        }else{
                            var colo_texto = 'text-primary';
                        }
                        var celda = '<span class="'+colo_texto+'">'+parseFloat(avance.avanceMes).format(2)+'</span>';
                        $('#avance-trim-'+trimestre+' > tbody > tr[data-nivel="2"][data-id="'+actividad.id+'"] > td[data-trim-mes="'+mes_del_trimestre+'"]').html(celda);
                        sumatoria_actividad[trimestre] += parseFloat(avance.avanceMes);
                        total_trimestres[trimestre][avance.mes] = (parseFloat(total_trimestres[trimestre][avance.mes]) || 0) + parseFloat(avance.avanceMes);
                    }
                    for(var j in sumatoria_actividad){
                        if(j > 1){
                            sumatoria_actividad[j] = sumatoria_actividad[j] + sumatoria_actividad[j-1];
                        }
                        $('#avance-trim-'+j+' > tbody > tr[data-nivel="2"][data-id="'+actividad.id+'"] > td[data-total-id="'+actividad.id+'"]').html(sumatoria_actividad[j].format(2));
                    }
                }
            }

            /*for(var i in total_trimestres){
                // i = trimestre
                var meses = total_trimestres[i];
                var suma = 0;
                for(var j in meses){
                    $('#total-mes-'+j).text(meses[j]);
                    suma += meses[j];
                }
                $('#total-trim-'+i).text(suma);
            }*/
			$('#btn-firmar').hide();
			
            if(response.data.evaluacion_meses.length){
                if(response.data.evaluacion_meses[0].idEstatus == "4")
                    $('#btn-firmar').show();
            }
			
			
            $('#btn-comentar-avance').attr('data-id-proyecto',e);
            $('#modalDatosSeguimiento').modal('show');
        }
    });
}

//rend-cuenta-inst-editar
$('#btn-comentar-avance').on('click',function(){
    window.location.href = SERVER_HOST+'/revision/comentar-avance/' + $('#btn-comentar-avance').attr('data-id-proyecto');
});

$('#btn-reporte-seguimiento').on('click',function(){
    window.open(SERVER_HOST+'/v1/reporte-seguimiento?clasificacion-proyecto=1');
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
    parametros += '&guardar=datos-informacion';
    moduloResource.put($('#btn-comentar-avance').attr('data-id-proyecto'),parametros,{
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
	
	Confirm.show({
		titulo:"¿Poner el programa en el estatus de firma?",
		mensaje: "¿Estás seguro que desea poner el estatus de firma? Una vez hecho esto, el programa ya no es modificable, y se entiende que se aprobó y firmó.",
		callback: function(){
			var parametros = 'actualizarproyecto=firmar';					
			
			moduloResource.put($('#btn-comentar-avance').attr('data-id-proyecto'),parametros,{
						_success: function(response){
							window.location = "../revision/segui-proyectos-inst";
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
	
    
});
$('#modalDatosSeguimiento').on('hide.bs.modal',function(e){
    $('#form_fuente_informacion').get(0).reset();
    Validation.cleanFormErrors('#form_fuente_informacion');
});
/*
$('#modalDatosSeguimiento').on('shown.bs.modal', function () {
    $('#modalDatosSeguimiento').find('input').eq(0).focus();
});
*/
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