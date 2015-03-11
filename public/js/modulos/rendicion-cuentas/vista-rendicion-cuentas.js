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
var beneficiariosDatagrid = new Datagrid("#datagridBeneficiarios",moduloResource,{ formatogrid:true, pagina: 1, idProyecto: $('#id').val(), grid:'rendicion-beneficiarios'});
var accionesDatagrid = new Datagrid("#datagridAcciones",moduloResource,{ formatogrid:true, pagina: 1, idProyecto: $('#id').val(), grid:'rendicion-acciones'});

beneficiariosDatagrid.init();
beneficiariosDatagrid.actualizar({
    _success: function(response){
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
});

accionesDatagrid.init();
accionesDatagrid.actualizar({
    _success: function(response){
        llenar_grid_acciones(response);
    }
});

$('#btn-guardar-avance').on('click',function(){
    if($('td.avance-mes[data-estado-avance=1]').length){
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
                accionesDatagrid.actualizar({
                    _success: function(response){
                        llenar_grid_acciones(response);
                    }
                });
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
                accionesDatagrid.actualizar({
                    _success: function(response){
                        llenar_grid_acciones(response);
                    }
                });
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

function seguimiento_beneficiarios(e){
    $('#modalBeneficiario').find(".modal-title").html("Seguimiento de Beneficiarios");
    $('#modalBeneficiario').modal('show');
}

function seguimiento_metas(e){
    var datos_id = e.split('-');
    if(datos_id[0] == '1'){
        var nivel = 'componente';
    }else{
        var nivel = 'actividad';
    }
    var parametros = {'mostrar':'datos-'+nivel+'-avance'};
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
            
            $('#id-accion').val(response.data.id);

            var total_programado = 0;
            var total_acumulado = 0;
            var total_avance = 0;

            for(var i in response.data.metas_mes_jurisdiccion){
                var dato = response.data.metas_mes_jurisdiccion[i];
                var row = '#tabla-avances-metas > tbody > tr[data-clave-jurisdiccion="'+dato.claveJurisdiccion+'"]';

                total_programado += dato.meta;
                
                $(row + ' > td.meta-programada').attr('data-meta',dato.meta);
                $(row + ' > td.meta-programada').text(dato.meta);
                if(dato.avance){
                    total_acumulado += dato.avance;
                    $(row + ' > td.avance-acumulado > span.vieja-cantidad').text(dato.avance);
                    $(row + ' > td.avance-acumulado').attr('data-acumulado',dato.avance);
                }
                /*if(dato.meta > 0){
                    $('#avance_'+dato.claveJurisdiccion).attr('disabled',false);
                }*/
            }

            for(var i in response.data.metas_mes){
                var dato = response.data.metas_mes[i];
                var row = '#tabla-avances-metas > tbody > tr[data-clave-jurisdiccion="'+dato.claveJurisdiccion+'"]';
                
                $('#avance_'+dato.claveJurisdiccion).attr('data-meta-programada',dato.meta);
                if(dato.avance){
                    $('#avance_'+dato.claveJurisdiccion).val(dato.avance);
                    total_avance += dato.avance;
                    total_acumulado -= dato.avance;
                    avance_jurisdiccion = parseFloat($(row + ' > td.avance-acumulado > span.vieja-cantidad').text());
                    $(row + ' > td.avance-acumulado > span.vieja-cantidad').text(avance_jurisdiccion - dato.avance);
                    $(row + ' > td.avance-acumulado').attr('data-acumulado',(avance_jurisdiccion - dato.avance));
                    $('#avance_'+dato.claveJurisdiccion).change();
                }

                if(dato.meta){
                    $('#avance_'+dato.claveJurisdiccion).attr('disabled',false);
                }

                var avance_metas = $(row + ' > td.avance-acumulado').attr('data-acumulado');
                var total_metas = $(row +' > td.meta-programada').attr('data-meta');
                var viejo_porcentaje = ((avance_metas * 100) / total_metas).toFixed(2);
                $(row + ' > td.porcentaje-acumulado').attr('data-porcentaje',viejo_porcentaje);
                $(row + ' > td.porcentaje-acumulado > span.vieja-cantidad').text(parseFloat(viejo_porcentaje)+'% ');
            }

            if(response.data.registro_avance.length){
                $('#id-avance').val(response.data.registro_avance[0].id);
                $('#analisis-resultados').val(response.data.registro_avance[0].analisisResultados);
                $('#justificacion-acumulada').val(response.data.registro_avance[0].justificacionAcumulada);
            }

            var total_porcentaje_acumulado = ((total_acumulado * 100) / total_programado).toFixed(2);
            $('#total-meta-programada').text(total_programado.format());
            $('#total-avance-mes').text(total_avance.format());
            $('#total-avance-acumulado').text(total_acumulado.format());
            $('#total-porcentaje').text(parseFloat(total_porcentaje_acumulado)+'% ');
            $('#modalEditarAvance').modal('show');
        }
    });    
}
$('.avance-mes').on('keyup',function(){ $(this).change() });
$('.avance-mes').on('change',function(){
    if($(this).val() != ''){
        var jurisdiccion = $(this).attr('data-jurisdiccion');
        //Actualiza la columna de avance acumulado
        var avance = $(this).val() || 0;
        var row = '#tabla-avances-metas > tbody > tr[data-clave-jurisdiccion="'+jurisdiccion+'"]';

        if(avance > 0){
            $(row +' > td.avance-acumulado > span.nueva-cantidad').html(' <small>(+'+avance+')</small>');
        }else{
            $(row +' > td.avance-acumulado > span.nueva-cantidad').html('');
        }
        
        //Actualiza la columna de porcentaje acumulado
        var acumulado = parseFloat($(row +' > td.avance-acumulado').attr('data-acumulado')) || 0;
        acumulado += parseFloat($(this).val()) || 0;
        var total_programado = $(row +' > td.meta-programada').attr('data-meta');
        var nuevo_porcentaje = ((acumulado * 100) / total_programado).toFixed(2);
        
        if(nuevo_porcentaje > 0){
            $(row +' > td.porcentaje-acumulado > span.nueva-cantidad').html(' <small>('+parseFloat(nuevo_porcentaje)+'%)</small>');
        }else{
            $(row +' > td.porcentaje-acumulado > span.nueva-cantidad').html('');
        }
        
        //Actualiza el porcentaje de avance al mes
        var avance_mes = ((avance  / $(this).attr('data-meta-programada') ) * 100).toFixed(2);
        if(avance_mes > 110){
            $(row +' > td.avance-mes').html('<small class="text-primary"><span class="fa fa-arrow-up"></span> '+parseFloat(avance_mes)+'%</small>');
            $(row +' > td.avance-mes').attr('data-estado-avance','1');
        }else if(avance_mes < 90){
            $(row +' > td.avance-mes').html('<small class="text-danger"><span class="fa fa-arrow-down"></span> '+parseFloat(avance_mes)+'%</small>');
            $(row +' > td.avance-mes').attr('data-estado-avance','1');
        }else{
            $(row +' > td.avance-mes').html('<small class="text-success">'+parseFloat(avance_mes)+'%</small>');
            $(row +' > td.avance-mes').attr('data-estado-avance','');
        }

        var suma = 0;
        $('.avance-mes').each(function(){
            suma += parseFloat($(this).val()) || 0;
            suma = parseFloat(suma.toFixed(2));
        });
        $('#total-avance-mes').text(suma);

        if($('td.avance-mes[data-estado-avance=1]').length){
            $('#tab-link-justificacion').attr('data-toggle','tab');
            $('#tab-link-justificacion').parent().removeClass('disabled');
        }else{
            $('#tab-link-justificacion').attr('data-toggle','');
            $('#tab-link-justificacion').parent().addClass('disabled');
        }
    }
});

$('#modalEditarAvance').on('hide.bs.modal',function(e){
    $('#modalEditarAvance .alert').remove();
    $('#form_avance').get(0).reset();
    $('#form_avance input[type="hidden"]').val('');
    $('#form_avance input[type="number"]').attr('disabled',true);
    $('#form_avance input[type="number"]').attr('data-meta-programada','');
    $('td.avance-mes').text('');
    $('td.avance-mes').attr('data-estado-avance','');
    $('td.avance-acumulado').attr('data-acumulado','');
    $('td.meta-programada').attr('data-meta','0');
    $('span.nueva-cantidad').text('');
    $('span.vieja-cantidad').text('0');
    $('#tab-link-justificacion').attr('data-toggle','');
    $('#tab-link-justificacion').parent().addClass('disabled');
    $('#tabs-seguimiento-metas a:first').tab('show');
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
        var mes = fecha.getMonth() + 1;
        
        if(componente.registro_avance.length){
            for(var j in componente.registro_avance){
                var avance = componente.registro_avance[j];
                item.avances_acumulados += avance.avanceMes;
                if(avance.mes == mes){
                    item.avances_mes += avance.avanceMes;
                    if(avance.analisisResultados){
                        item.justificacion = '<span class="fa fa-comment"></span>';
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
                        item.avances_mes += avance.avanceMes;
                        if(avance.analisisResultados){
                            item.justificacion = '<span class="fa fa-comment"></span>';
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