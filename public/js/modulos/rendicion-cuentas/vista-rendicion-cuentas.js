/*=====================================

    # Nombre:
        vista-rendicion-cuentas.js

    # Módulo:
        seguimiento/editar-avance

    # Descripción:
        Para rendición de cuentas de proyectos

=====================================*/

// Inicialización General para casi cualquier módulo
var moduloResource = new RESTfulRequests(SERVER_HOST+'/v1/seguimiento');

$('#btn-proyecto-cancelar').on('click',function(){
    if($(this).attr('data-clase-proyecto') == 1){
        window.location.href = SERVER_HOST+'/rendicion-cuentas/rend-cuenta-inst';
    }else if($(this).attr('data-clase-proyecto') == 2){
        window.location.href = SERVER_HOST+'/rendicion-cuentas/rend-cuenta-inv';
    }
});


/********************************************************************************************************************************
        Inicio: Seguimiento de Metas
*********************************************************************************************************************************/
var accionesDatagrid = new Datagrid("#datagridAcciones",moduloResource,{ formatogrid:true, pagina: 1, idProyecto: $('#id').val(), grid:'rendicion-acciones'});
accionesDatagrid.init();
accionesDatagrid.actualizar({ _success: function(response){ llenar_grid_acciones(response); } });

$('#btn-guardar-avance').on('click',function(){
    if($('#total-porcentaje').attr('data-estado-avance')){
        if(($('#analisis-resultados').val().trim() == '') || ($('#justificacion-acumulada').val().trim() == '')){
            MessageManager.show({data:'Es necesario capturar una justificacion en base al porcentaje de avance del mes.',container:'#modalEditarAvance .modal-body',type:'ADV'});
            $('#tab-link-justificacion').tab('show');
            return;
        }
    }
    var parametros = $('#form_avance').serialize();
    parametros += '&guardar=avance-metas';

    Validation.cleanFormErrors('#form_avance');

    if($('#id-avance').val()){
        moduloResource.put($('#id-avance').val(),parametros,{
            _success: function(response){
                MessageManager.show({data:'Datos del proyecto almacenados con éxito',type:'OK',timer:4});
                accionesDatagrid.actualizar({ _success: function(response){ llenar_grid_acciones(response); } });
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
                accionesDatagrid.actualizar({ _success: function(response){ llenar_grid_acciones(response); } });
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
    var datos_id = e.split('-');
    if(datos_id[0] == '1'){
        var nivel = 'componente';
    }else{
        var nivel = 'actividad';
    }
    var parametros = {'mostrar':'datos-metas-avance','nivel':nivel};
    var id = datos_id[1];
    moduloResource.get(id,parametros,{
        _success: function(response){
            if(response.data.idComponente){
                $('#modalEditarAvance').find(".modal-title").html("Seguimiento de Metas de la Actividad");
                $('#nivel').val('actividad');
            }else{
                $('#modalEditarAvance').find(".modal-title").html("Seguimiento de Metas del Componente");
                $('#nivel').val('componente');
            }

            $('#indicador').text(response.data.indicador);
            $('#unidad-medida').text(response.data.unidad_medida.descripcion);
            $('#meta-total').text(response.data.valorNumerador.format());
            
            $('#id-accion').val(response.data.id);

            var total_programado = 0;
            var total_acumulado = 0;
            var total_avance = 0;

            for(var i in response.data.metas_mes_jurisdiccion){
                var dato = response.data.metas_mes_jurisdiccion[i];
                var row = '#tabla-avances-metas > tbody > tr[data-clave-jurisdiccion="'+dato.claveJurisdiccion+'"]';

                total_programado += dato.meta || 0;
                
                $(row + ' > td.meta-programada').attr('data-meta',dato.meta);
                $(row + ' > td.meta-programada').text(dato.meta);
                if(dato.avance){
                    total_acumulado += dato.avance;
                    $(row + ' > td.avance-acumulado').text(dato.avance);
                    $(row + ' > td.avance-acumulado').attr('data-acumulado',dato.avance);
                    $(row + ' > td.avance-total').attr('data-avance-total',dato.avance);
                    $(row + ' > td.avance-total').text(dato.avance.format());
                }
            }

            var total_programado_mes = 0;
            for(var i in response.data.metas_mes){
                var dato = response.data.metas_mes[i];
                var row = '#tabla-avances-metas > tbody > tr[data-clave-jurisdiccion="'+dato.claveJurisdiccion+'"]';
                
                $('#avance_'+dato.claveJurisdiccion).attr('data-meta-programada',dato.meta);
                $(row + ' > td.meta-del-mes').text((dato.meta || 0));
                $(row + ' > td.meta-del-mes').attr('data-meta-mes',dato.meta);
                total_programado_mes += dato.meta || 0;

                if(dato.avance != null){
                    $('#avance_'+dato.claveJurisdiccion).val(dato.avance);
                    total_avance += dato.avance;
                    total_acumulado -= dato.avance;
                    avance_jurisdiccion = parseFloat($(row + ' > td.avance-acumulado').attr('data-acumulado')) || 0;
                    $(row + ' > td.avance-acumulado').text((avance_jurisdiccion - dato.avance).format());
                    $(row + ' > td.avance-acumulado').attr('data-acumulado',(avance_jurisdiccion - dato.avance));
                }
            }

            if(response.data.registro_avance.length){
                $('#id-avance').val(response.data.registro_avance[0].id);
                $('#analisis-resultados').val(response.data.registro_avance[0].analisisResultados);
                $('#justificacion-acumulada').val(response.data.registro_avance[0].justificacionAcumulada);
            }

            if(response.data.plan_mejora.length){
                var plan_mejora = response.data.plan_mejora[0];
                $('#accion-mejora').val(plan_mejora.accionMejora);
                $('#grupo-trabajo').val(plan_mejora.grupoTrabajo);
                $('#documentacion-comprobatoria').val(plan_mejora.documentacionComprobatoria);
                $('#fecha-inicio').val(plan_mejora.fechaInicio);
                $('#fecha-termino').val(plan_mejora.fechaTermino);
                $('#fecha-notificacion').val(plan_mejora.fechaNotificacion);
            }

            //var total_porcentaje_acumulado = parseFloat((((total_acumulado + total_avance) * 100) / total_programado).toFixed(2)) || 0;
            $('#total-meta-programada').text(total_programado.format());
            $('#total-meta-programada').attr('data-total-programado',total_programado);
            $('#total-meta-mes').text(total_programado_mes.format());
            $('#total-avance-mes').text(total_avance.format());
            $('#total-avance-acumulado').text(total_acumulado.format());
            //$('#total-porcentaje').text(total_porcentaje_acumulado+'% ');
            $('.avance-mes').change();

            if(response.data.desglose_municipios){
                if(response.data.desglose_municipios.length){
                    asignar_municipios(response.data.desglose_municipios);
                    $('input.avance-mes').attr('disabled',true);
                }
            }
            $('#modalEditarAvance').modal('show');
        }
    });    
}

function asignar_municipios(datos){
    //datos
    var municipios = {};
    for(var i in datos){
        if(!municipios[datos[i].claveJurisdiccion]){
            municipios[datos[i].claveJurisdiccion] = {
                'claveJurisdiccion': datos[i].claveJurisdiccion,
                'municipios': []
            };
        }
        municipios[datos[i].claveJurisdiccion].municipios.push({
            'clave': datos[i].clave,
            'nombre': datos[i].nombre
        })
    }
    
    for(var i in municipios){
        var claveJurisdiccion = municipios[i].claveJurisdiccion;
        var row = '#tabla-avances-metas > tbody > tr[data-clave-jurisdiccion="'+claveJurisdiccion+'"]';
        
        $(row + ' td.accion-municipio').addClass('btn-link');
        $(row + ' td.accion-municipio span').addClass('caret');
        $(row + ' td.accion-municipio').on('click',function(){ $('#desglose-avance-'+$(this).parent().attr('data-clave-jurisdiccion')).removeClass('hidden'); });

        $(row).after('<tr id="desglose-avance-'+claveJurisdiccion+'" class="lista-localidades-jurisdiccion hidden"><td colspan="7"><div id="panel-localidades-'+claveJurisdiccion+'" class="panel panel-primary" style="margin-bottom:0;"></div></td></tr>');

        var panel_id = '#panel-localidades-'+claveJurisdiccion;
        $(panel_id).html($('#panel-estructura-localidades').html());
        $(panel_id + ' .select-lista-municipios').attr('data-jurisdiccion',claveJurisdiccion);
        $(panel_id + ' .btn-guardar-avance-localidades').attr('data-jurisdiccion',claveJurisdiccion);
        $(panel_id + ' .btn-ocultar-avance-localidades').attr('data-jurisdiccion',claveJurisdiccion);

        var opciones = [];
        opciones.push('<option value="">Selecciona un municipio</option>');
        for(var j in municipios[i].municipios){
            var municipio = municipios[i].municipios[j];
            opciones.push('<option value="'+ municipio.clave +'">'+ municipio.nombre +'</option>');
        }
        $(panel_id + ' .select-lista-municipios').html(opciones.join(''));
    }

    $('.btn-ocultar-avance-localidades').on('click',function(){
        $('#desglose-avance-'+$(this).attr('data-jurisdiccion')+' .select-lista-municipios').val('');
        $('#panel-localidades-'+$(this).attr('data-jurisdiccion')+' table.tabla-avance-localidades tbody').empty();
        $('#desglose-avance-'+$(this).attr('data-jurisdiccion')).addClass('hidden');
    });

    $('.btn-guardar-avance-localidades').on('click',function(){
        enviar_datos_municipio($(this).attr('data-jurisdiccion'));
    });

    $('.select-lista-municipios').on('change',function(){
        buscar_localidades($(this).attr('data-jurisdiccion'),$(this).val());
    });
}

function enviar_datos_municipio(jurisdiccion){
    var municipio = $('#panel-localidades-'+jurisdiccion+' select.select-lista-municipios[data-jurisdiccion="'+jurisdiccion+'"]').val();
    if(municipio == ''){
        return false;
    }

    var parametros = $('#form_avance').serialize();
    parametros += '&guardar=avance-localidad-metas&clave-municipio='+municipio;

    Validation.cleanFormErrors('#form_avance');

    moduloResource.post(parametros,{
        _success: function(response){
            MessageManager.show({data:'Datos del proyecto almacenados con éxito',type:'OK',timer:4});
            var jurisdiccion = response.data.claveJurisdiccion;
            var avance = response.data.avance;
            $('#avance_'+jurisdiccion).val(avance).change();
            accionesDatagrid.actualizar({ _success: function(response){ llenar_grid_acciones(response); } });
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

function buscar_localidades(jurisdiccion,municipio){
    //sconsole.log('Jurisdiccion = '+jurisdiccion+'| Municipio = '+municipio);
    if(municipio == ''){
        $('#panel-localidades-'+jurisdiccion+' table.tabla-avance-localidades tbody').empty();
        return false;
    }
    var parametros = {
        'mostrar':'datos-municipio-avance',
        'nivel':$('#nivel').val(),
        'clave-municipio':municipio
    };
    moduloResource.get($('#id-accion').val(),parametros,{
        _success: function(response){
            //
            var tabla = '#panel-localidades-'+jurisdiccion+' table.tabla-avance-localidades tbody';
            $(tabla).empty();
            var html_rows = '';
            for(var i in response.data){
                var dato = response.data[i];
                if(dato.metas_mes.length > 0){
                    var datos_meta = {
                        meta: dato.metas_mes[0].meta || 0,
                        avance: dato.metas_mes[0].avance,
                    };
                }else{
                    var datos_meta = {meta:0,avance:0};
                }
                if(dato.metas_mes_acumuladas){
                    var datos_acumulado = {
                        meta: dato.metas_mes_acumuladas.meta || 0,
                        avance: dato.metas_mes_acumuladas.avance || 0
                    }
                }else{
                    var datos_acumulado = {meta:0, avance:0}
                }
                var avance_anterior = datos_acumulado.avance - (datos_meta.avance || 0); 
                html_rows += '<tr data-clave-localidad="'+dato.claveLocalidad+'"><td>' + dato.localidad + '</td>' +
                                '<td class="localidad-metas-acumuladas bg-success" data-metas-acumuladas="'+datos_acumulado.meta+'">'+datos_acumulado.meta.format()+'</td>' + 
                                '<td class="localidad-meta-mes" data-meta-mes="'+datos_meta.meta+'">'+datos_meta.meta.format()+'</td>' +
                                '<td><div class="form-group"><input name="localidad-avance-mes['+dato.claveLocalidad+']" id="localidad_avance_mes_'+dato.claveLocalidad+'" type="number" class="form-control localidad-avance" value="'+datos_meta.avance+'" data-localidad="'+dato.claveLocalidad+'" data-jurisdiccion="'+jurisdiccion+'"></div></td>' + 
                                '<td class="localidad-avance-acumulado" data-avance-acumulado="'+avance_anterior+'">'+avance_anterior.format()+'</td>' +
                                '<td class="localidad-total-avance bg-info" data-avance-total="'+datos_acumulado.avance+'">'+datos_acumulado.avance.format()+'</td></tr>';
            }
            $(tabla).html(html_rows);
            $('.localidad-avance').on('keyup',function(){ $(this).change() });
            $('.localidad-avance').on('change',function(){
                var jurisdiccion = $(this).attr('data-jurisdiccion');
                var localidad = $(this).attr('data-localidad');

                var row = '#panel-localidades-'+jurisdiccion+' table.tabla-avance-localidades tbody tr[data-clave-localidad="'+localidad+'"]';

                var avance_acumulado = parseFloat($(row + ' td.localidad-avance-acumulado').attr('data-avance-acumulado')) || 0;
                avance_acumulado += parseFloat($(this).val()) || 0;

                $(row + ' td.localidad-total-avance').attr('data-avance-total',avance_acumulado);
                $(row + ' td.localidad-total-avance').text(avance_acumulado.format());
            });
        }
    });
}

$('.avance-mes').on('keyup',function(){ $(this).change() });
$('.avance-mes').on('change',function(){
    var jurisdiccion = $(this).attr('data-jurisdiccion');
    //Actualiza la columna de avance acumulado
    var row = '#tabla-avances-metas > tbody > tr[data-clave-jurisdiccion="'+jurisdiccion+'"]';

    var avance = parseFloat($(this).val()) || 0;
    //Actualiza la columna de porcentaje acumulado
    var acumulado = parseFloat($(row +' > td.avance-acumulado').attr('data-acumulado')) || 0;
    acumulado += avance;
    $(row +' > td.avance-total').text(acumulado);
    $(row +' > td.avance-total').attr('data-avance-total',acumulado);

    var total_programado = $(row +' > td.meta-programada').attr('data-meta');

    if(acumulado == 0 && total_programado == 0){
        $(row +' > td.avance-mes').html('<small class="text-success">0%</small>');
    }else{
        if(total_programado > 0){
            var avance_mes = parseFloat(((acumulado * 100) / total_programado).toFixed(2))||0;
        }else if(acumulado > 0){
            var avance_mes = 100;
        }else{
            var avance_mes = 0;
        }

        if(avance_mes > 110){
            $(row +' > td.avance-mes').html('<small class="text-danger"><span class="fa fa-arrow-up"></span> '+avance_mes+'%</small>');
        }else if(avance_mes < 90){
            $(row +' > td.avance-mes').html('<small class="text-danger"><span class="fa fa-arrow-down"></span> '+avance_mes+'%</small>');
        }else if(total_programado == 0 && avance_mes > 0){
            $(row +' > td.avance-mes').html('<small class="text-info"><span class="fa fa-arrow-up"></span> '+avance_mes+'%</small>');
        }else{
            $(row +' > td.avance-mes').html('<small class="text-success">'+avance_mes+'%</small>');
        }
    }

    var suma = 0;
    $('.avance-mes').each(function(){
        suma += parseFloat($(this).val()) || 0;
    });
    suma = parseFloat(suma.toFixed(2));
    $('#total-avance-mes').text(suma.format());

    var suma = 0;
    $('.avance-total').each(function(){
        suma += parseFloat($(this).attr('data-avance-total')) || 0;
    });
    suma = parseFloat(suma.toFixed(2));
    $('#total-avance-total').attr('data-total-avance',suma);
    $('#total-avance-total').text(suma.format());

    total_programado = parseFloat($('#total-meta-programada').attr('data-total-programado'));
    total_acumulado = parseFloat($('#total-avance-total').attr('data-total-avance'));
    

    if(total_programado == 0 && total_acumulado ==  0){
        total_porcentaje_acumulado = '<small class="text-success">0%</small>';
        $('#total-porcentaje').attr('data-estado-avance','');
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
            $('#total-porcentaje').attr('data-estado-avance','1');
        }else if(total_porcentaje_acumulado < 90){
            total_porcentaje_acumulado = '<small class="text-danger"><span class="fa fa-arrow-down"></span> '+total_porcentaje_acumulado+'%</small>';
            $('#total-porcentaje').attr('data-estado-avance','1');
        }else if(total_programado == 0 && total_porcentaje_acumulado > 0){
            total_porcentaje_acumulado = '<small class="text-info"><span class="fa fa-arrow-up"></span> '+total_porcentaje_acumulado+'%</small>';
            $('#total-porcentaje').attr('data-estado-avance','1');
        }else{
            total_porcentaje_acumulado = '<small class="text-success">'+total_porcentaje_acumulado+'%</small>';
            $('#total-porcentaje').attr('data-estado-avance','');
        }
    }

    $('#total-porcentaje').html(total_porcentaje_acumulado);

    if($('#total-porcentaje').attr('data-estado-avance')){
        $('#justificacion-acumulada').attr('disabled',false);
        if($('#justificacion-acumulada').val() == 'El avance se encuentra dentro de los parametros establecidos'){
            $('#justificacion-acumulada').val('');
        }
        $('#tab-link-plan-mejora').attr('data-toggle','tab');
        $('#tab-link-plan-mejora').parent().removeClass('disabled');
    }else{
        $('#justificacion-acumulada').attr('disabled',true);
        $('#tab-link-plan-mejora').attr('data-toggle','');
        $('#tab-link-plan-mejora').parent().addClass('disabled');
    }
});

$('#modalEditarAvance').on('hide.bs.modal',function(e){
    $('#modalEditarAvance .alert').remove();
    $('#form_avance').get(0).reset();
    $('#form_avance input[type="hidden"]').val('');
    //$('#form_avance input[type="number"]').attr('disabled',true);
    $('#form_avance input[type="number"]').attr('data-meta-programada','');
    $('td.avance-mes').text('');
    //$('td.avance-mes').attr('data-estado-avance','');
    $('td.avance-acumulado').attr('data-acumulado','');
    $('td.avance-acumulado').text('0');
    $('td.meta-programada').attr('data-meta','0');
    $('td.meta-programada').text('0');
    $('td.meta-del-mes').attr('data-meta-mes','');
    $('td.meta-del-mes').text('0');
    $('td.avance-total').attr('data-avance-total','');
    $('td.avance-total').text('0');
    $('#total-avance-total').attr('data-total-avance','');
    $('#total-avance-total').text('0');
    $('#total-porcentaje').attr('data-estado-avance','');
    $('#total-porcentaje').text('0%');
    $('.accion-municipio').removeClass('btn-link');
    $('.accion-municipio span').removeClass('caret');
    $('input.avance-mes').attr('disabled',false);
    //$('span.nueva-cantidad').text('');
    //$('span.vieja-cantidad').text('0');
    $('#justificacion-acumulada').attr('disabled',true);
    $('#tab-link-plan-mejora').attr('data-toggle','');
    $('#tab-link-plan-mejora').parent().addClass('disabled');
    $('#tabs-seguimiento-metas a:first').tab('show');
    $('.lista-localidades-jurisdiccion').remove();
    Validation.cleanFormErrors('#form_avance');
});

function llenar_grid_acciones(response){
    accionesDatagrid.limpiar();
    var datos_grid = [];
    var contador_componente = 0;
    for(var i in response.data.componentes){
        var contador_actividad = 0;
        contador_componente++;
        var componente = response.data.componentes[i];

        var item = {};

        item.id = '1-' + componente.id;
        item.nivel = 'Componente ' + contador_componente;
        item.indicador = componente.indicador;
        item.meta = componente.valorNumerador.format();
        item.avances_acumulados = 0;
        item.avances_mes = 0;
        item.justificacion = '';

        var fecha = new Date();
        var mes = $('#mes').val();
        
        if(componente.registro_avance.length){
            
            for(var j in componente.registro_avance){
                var avance = componente.registro_avance[j];
                item.avances_acumulados += avance.avanceMes;
                if(avance.mes == mes){
                    item.justificacion += '<span class="fa fa-floppy-o"></span> ';
                    item.avances_mes += avance.avanceMes;
                    if(avance.planMejora){
                        item.justificacion += '<span class="fa fa-align-left"></span>';
                    }
                }
            }
        }

        datos_grid.push(item);
        for(var j in componente.actividades){
            contador_actividad++;
            var actividad = componente.actividades[j];
            var item = {};
            item.id = '2-' + actividad.id;
            item.nivel = 'Actividad ' + contador_componente + '.' + contador_actividad;
            item.indicador = actividad.indicador;
            item.meta = actividad.valorNumerador.format();
            item.avances_acumulados = 0;
            item.avances_mes = 0;
            item.justificacion = '';
            
            if(actividad.registro_avance.length){
                
                for(var j in actividad.registro_avance){
                    var avance = actividad.registro_avance[j];
                    item.avances_acumulados += avance.avanceMes;
                    if(avance.mes == mes){
                        item.justificacion += '<span class="fa fa-floppy-o"></span> ';
                        item.avances_mes += avance.avanceMes;
                        if(avance.planMejora){
                            item.justificacion += '<span class="fa fa-align-left"></span>';
                        }
                    }
                }
            }

            datos_grid.push(item);
        }
    }
    accionesDatagrid.cargarDatos(datos_grid);                         
    var total = parseInt(response.resultados/accionesDatagrid.rxpag); 
    var plus = parseInt(response.resultados)%accionesDatagrid.rxpag;
    if(plus>0) 
        total++;
    accionesDatagrid.paginacion(total);
}
/********************************************************************************************************************************
        Fin: Seguimiento de Metas
*********************************************************************************************************************************/


/********************************************************************************************************************************
        Inicio: Seguimiento de Beneficiarios
*********************************************************************************************************************************/
var beneficiariosDatagrid = new Datagrid("#datagridBeneficiarios",moduloResource,{ formatogrid:true, pagina: 1, idProyecto: $('#id').val(), grid:'rendicion-beneficiarios'});
beneficiariosDatagrid.init();
beneficiariosDatagrid.actualizar({ _success: function(response){ llenar_grid_beneficiarios(response); } });
function seguimiento_beneficiarios(e){
    var parametros = {'mostrar':'datos-beneficiarios-avance','id-proyecto':$('#id').val()};
    moduloResource.get(e,parametros,{
        _success: function(response){
            $('#form_beneficiario input.masc').attr('disabled',true);
            $('#form_beneficiario input.fem').attr('disabled',true);

            $('#modalBeneficiario').find(".modal-title").html('Seguimiento de Beneficiarios');
            var beneficiario = response.data.beneficiario[0].tipo_beneficiario;
            $('#tipo-beneficiario').text(beneficiario.descripcion);
            $('#id-beneficiario').val(beneficiario.id);
            var suma = 0;
            for(var i in response.data.beneficiario){
                var beneficiario = response.data.beneficiario[i];
                $('#total-'+beneficiario.sexo).text(beneficiario.total.format());
                $('#total-'+beneficiario.sexo).attr('data-valor',beneficiario.total)
                suma += beneficiario.total;

                if(beneficiario.sexo == 'f'){
                    $('#form_beneficiario input.fem').attr('disabled',false);
                }else{
                    $('#form_beneficiario input.masc').attr('disabled',false);
                }

                if(beneficiario.registro_avance.length){
                    $('#hay-avance').val(1);
                }

                for(var j in beneficiario.registro_avance){
                    var avance = beneficiario.registro_avance[j];

                    $('#muyalta'+avance.sexo).val(avance.muyAlta);
                    $('#alta'+avance.sexo).val(avance.alta);
                    $('#media'+avance.sexo).val(avance.media);
                    $('#baja'+avance.sexo).val(avance.baja);
                    $('#muybaja'+avance.sexo).val(avance.muyBaja);

                    $('#indigena'+avance.sexo).val(avance.indigena);
                    $('#inmigrante'+avance.sexo).val(avance.inmigrante);
                    $('#mestiza'+avance.sexo).val(avance.mestiza);
                    $('#otros'+avance.sexo).val(avance.otros);

                    $('#rural'+avance.sexo).val(avance.rural);
                    $('#urbana'+avance.sexo).val(avance.urbana);
                }
            }

            $('#muybajaf').change();
            $('#otrosf').change();
            $('#urbanaf').change();
            $('#muybajam').change();
            $('#otrosm').change();
            $('#urbanam').change();

            var suma_acumulados = 0;
            for(var i in response.data.acumulado){
                $('#acumulado-'+response.data.acumulado[i].sexo).text(parseInt(response.data.acumulado[i].total).format());
                $('#acumulado-'+response.data.acumulado[i].sexo).attr('data-valor',response.data.acumulado[i].total);
                suma_acumulados += parseInt(response.data.acumulado[i].total);
            }

            $('#acumulado-beneficiario').text(suma_acumulados.format());
            $('#acumulado-beneficiario').attr('data-valor',suma_acumulados);

            $('#total-beneficiario').text(suma.format());
            $('#total-beneficiario').attr('data-valor',suma);

            $('#modalBeneficiario').modal('show');
        }
    });
}
$('#btn-beneficiario-guardar').on('click',function(){
    var parametros = $('#form_beneficiario').serialize();
    parametros += '&guardar=avance-beneficiarios&id-proyecto='+$('#id').val();

    Validation.cleanFormErrors('#form_beneficiario');

    var total_marginacion = [];
    var total_poblacion = [];
    var total_zona = [];

    total_marginacion['f'] = 0;
    $('.sub-total-marginacion.fem').each(function(){ total_marginacion['f'] += parseInt($(this).val()) || 0; });
    total_poblacion['f'] = 0;
    $('.sub-total-poblacion.fem').each(function(){ total_poblacion['f'] += parseInt($(this).val()) || 0; });
    total_zona['f'] = 0;
    $('.sub-total-zona.fem').each(function(){ total_zona['f'] += parseInt($(this).val()) || 0; });

    total_marginacion['m'] = 0;
    $('.sub-total-marginacion.masc').each(function(){ total_marginacion['m'] += parseInt($(this).val()) || 0; });
    total_poblacion['m'] = 0;
    $('.sub-total-poblacion.masc').each(function(){ total_poblacion['m'] += parseInt($(this).val()) || 0; });
    total_zona['m'] = 0;
    $('.sub-total-zona.masc').each(function(){ total_zona['m'] += parseInt($(this).val()) || 0; });

    var sexos = ['f','m'];
    for(var i in sexos){
        if(total_marginacion[sexos[i]] != total_poblacion[sexos[i]] || total_marginacion[sexos[i]] != total_zona[sexos[i]] || total_zona[sexos[i]] != total_poblacion[sexos[i]]){
            MessageManager.show({data:'Los totales capturados no coinciden entre si.',container:'#modalBeneficiario .modal-body',type:'ERR'});
            return false;
        }
    }

    var total_f = $('#total-f').attr('data-valor');
    var total_m = $('#total-m').attr('data-valor');
    
    if(total_zona['f'] > total_f || total_zona['m'] > total_m){
        Confirm.show({
                titulo:"¿Esta seguro de guardarl los datos?",
                mensaje: "Los totales capturados son mayores a los programados para el proyecto, ¿Desea continuar?",
                callback: function(){
                    guardar_datos_beneficiarios(parametros);
                }
        });
    }else{
        guardar_datos_beneficiarios(parametros);
    }
});

function guardar_datos_beneficiarios(parametros){
    var hay_avance = parseInt($('#hay-avance').val());
    if(hay_avance){
        moduloResource.put($('#id-beneficiario').val(),parametros,{
            _success: function(response){
                if(response.advertencia){
                    MessageManager.show({data:response.advertencia,container:'#modalBeneficiario .modal-body',type:'ADV'});
                }else{
                    MessageManager.show({data:'Datos del proyecto almacenados con éxito',type:'OK',timer:4});
                    $('#modalBeneficiario').modal('hide');
                }
                beneficiariosDatagrid.actualizar({ _success: function(response){ llenar_grid_beneficiarios(response); } });
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
                if(response.advertencia){
                    MessageManager.show({data:response.advertencia,container:'#modalBeneficiario .modal-body',type:'ADV'});
                    $('#hay-avance').val(1);
                }else{
                    MessageManager.show({data:'Datos del proyecto almacenados con éxito',type:'OK',timer:4});
                    $('#modalBeneficiario').modal('hide');
                }
                beneficiariosDatagrid.actualizar({ _success: function(response){ llenar_grid_beneficiarios(response); } });
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
$('.fem,.masc').on('keyup',function(){ $(this).change(); });
$('.fem').on('change',function(){
    if($(this).hasClass('sub-total-zona')){
        sumar_valores('.sub-total-zona.fem','#total-zona-f');
    }else if($(this).hasClass('sub-total-poblacion')){
        sumar_valores('.sub-total-poblacion.fem','#total-poblacion-f');
    }else if($(this).hasClass('sub-total-marginacion')){
        sumar_valores('.sub-total-marginacion.fem','#total-marginacion-f');
    }
});
$('.masc').on('change',function(){
    if($(this).hasClass('sub-total-zona')){
        sumar_valores('.sub-total-zona.masc','#total-zona-m');
    }else if($(this).hasClass('sub-total-poblacion')){
        sumar_valores('.sub-total-poblacion.masc','#total-poblacion-m');
    }else if($(this).hasClass('sub-total-marginacion')){
        sumar_valores('.sub-total-marginacion.masc','#total-marginacion-m');
    }
});

$('#modalBeneficiario').on('hide.bs.modal',function(e){
    $('#modalBeneficiario .alert').remove();
    $('#form_beneficiario').get(0).reset();
    $('#form_beneficiario input[type="hidden"]').val('');
    $('#form_beneficiario .cant-benficiarios').text('0');
    $('#form_beneficiario .cant-benficiarios').attr('data-valor','0');
    Validation.cleanFormErrors('#form_beneficiario');
});

function llenar_grid_beneficiarios(response){
    beneficiariosDatagrid.limpiar();
    var datos_grid = {};

    for(var i in response.data){
        var beneficiario = response.data[i];

        if(!datos_grid[beneficiario.idTipoBeneficiario]){
            datos_grid[beneficiario.idTipoBeneficiario] = {
                'id': beneficiario.idTipoBeneficiario,
                'tipoBeneficiario': beneficiario.tipo_beneficiario.descripcion,
                'f': 0,
                'f-avance':0,
                'm': 0,
                'm-avance':0,
                'total': 0,
                'total-avance':0
            };
        }

        if(beneficiario.registro_avance.length){
            var avance = beneficiario.registro_avance[0];
            datos_grid[beneficiario.idTipoBeneficiario][beneficiario.sexo+'-avance'] += parseInt(avance.total) || 0;
            datos_grid[beneficiario.idTipoBeneficiario]['total-avance'] += parseInt(avance.total) || 0;
        }

        datos_grid[beneficiario.idTipoBeneficiario][beneficiario.sexo] += beneficiario.total;
        datos_grid[beneficiario.idTipoBeneficiario]['total'] += beneficiario.total;
    }
    var datos = [];

    for(var i in datos_grid){
        datos_grid[i].f = datos_grid[i].f.format();
        datos_grid[i].m = datos_grid[i].m.format();
        datos_grid[i].total = datos_grid[i].total.format();
        datos_grid[i]['f-avance'] = datos_grid[i]['f-avance'].format();
        datos_grid[i]['m-avance'] = datos_grid[i]['m-avance'].format();
        datos_grid[i]['total-avance'] = datos_grid[i]['total-avance'].format();

        datos.push(datos_grid[i]);
    }
    
    beneficiariosDatagrid.cargarDatos(datos);                         
    var total = parseInt(datos.length/beneficiariosDatagrid.rxpag); 
    var plus = parseInt(datos.length)%beneficiariosDatagrid.rxpag;
    if(plus>0) 
        total++;
    beneficiariosDatagrid.paginacion(total);
}
/********************************************************************************************************************************
        Fin: Seguimiento de Beneficiarios
*********************************************************************************************************************************/

/********************************************************************************************************************************
        Inicio: Formulario de Analisis Funcional
*********************************************************************************************************************************/
if($('#id-analisis').val()){
    var parametros = 'mostrar=analisis-funcional';
    moduloResource.get($('#id-analisis').val(),parametros,{
        _success: function(response){
            $('#finalidad-proyecto').val(response.data.finalidadProyecto);
            $('#analisis-resultado').val(response.data.analisisResultado);
            $('#beneficiarios').val(response.data.beneficiarios);
            $('#justificacion-global').val(response.data.justificacionGlobal);
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
$('#btn-guadar-analisis-funcional').on('click',function(){
    var parametros = $('#form_analisis').serialize();
    parametros += '&guardar=analisis-funcional&id-proyecto='+$('#id').val();
    if($('#id-analisis').val()){
        moduloResource.put($('#id-analisis').val(),parametros,{
            _success: function(response){
                MessageManager.show({data:'Datos del proyecto almacenados con éxito',type:'OK',timer:4});
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
                $('#id-analisis').val(response.data.id);
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
/********************************************************************************************************************************
        Fin: Formulario de Analisis Funcional
*********************************************************************************************************************************/

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