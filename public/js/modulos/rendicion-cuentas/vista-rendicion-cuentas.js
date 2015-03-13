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
        llenar_grid_beneficiarios(response);
    }
});

accionesDatagrid.init();
accionesDatagrid.actualizar({
    _success: function(response){
        llenar_grid_acciones(response);
    }
});

$('#btn-beneficiario-guardar').on('click',function(){
    var parametros = $('#form_beneficiario').serialize();
    parametros += '&guardar=avance-beneficiarios&id-proyecto='+$('#id').val();

    Validation.cleanFormErrors('#form_beneficiario');
    var hay_avance = parseInt($('#hay-avance').val());
    if(hay_avance){
        moduloResource.put($('#id-beneficiario').val(),parametros,{
            _success: function(response){
                MessageManager.show({data:'Datos del proyecto almacenados con éxito',type:'OK',timer:4});
                beneficiariosDatagrid.actualizar({
                    _success: function(response){
                        llenar_grid_beneficiarios(response);
                    }
                });
                $('#modalBeneficiario').modal('hide');
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
                beneficiariosDatagrid.actualizar({
                    _success: function(response){
                        llenar_grid_beneficiarios(response);
                    }
                });
                //$('#id-beneficiario').val(response.data.id);
                $('#modalBeneficiario').modal('hide');
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

                total_programado += dato.meta || 0;
                
                $(row + ' > td.meta-programada').attr('data-meta',dato.meta);
                $(row + ' > td.meta-programada').text(dato.meta);
                if(dato.avance){
                    total_acumulado += dato.avance;
                    $(row + ' > td.avance-acumulado > span.vieja-cantidad').text(dato.avance);
                    $(row + ' > td.avance-acumulado').attr('data-acumulado',dato.avance);
                }
            }

            for(var i in response.data.metas_mes){
                var dato = response.data.metas_mes[i];
                var row = '#tabla-avances-metas > tbody > tr[data-clave-jurisdiccion="'+dato.claveJurisdiccion+'"]';
                
                $('#avance_'+dato.claveJurisdiccion).attr('data-meta-programada',dato.meta);

                if(dato.avance != null){
                    $('#avance_'+dato.claveJurisdiccion).val(dato.avance);
                    total_avance += dato.avance;
                    total_acumulado -= dato.avance;
                    avance_jurisdiccion = parseFloat($(row + ' > td.avance-acumulado').attr('data-acumulado'));
                    $(row + ' > td.avance-acumulado > span.vieja-cantidad').text(avance_jurisdiccion - dato.avance);
                    $(row + ' > td.avance-acumulado').attr('data-acumulado',(avance_jurisdiccion - dato.avance));
                    $('#avance_'+dato.claveJurisdiccion).change();
                }
            }

            if(response.data.registro_avance.length){
                $('#id-avance').val(response.data.registro_avance[0].id);
                $('#analisis-resultados').val(response.data.registro_avance[0].analisisResultados);
                $('#justificacion-acumulada').val(response.data.registro_avance[0].justificacionAcumulada);
            }

            var total_porcentaje_acumulado = parseFloat((((total_acumulado + total_avance) * 100) / total_programado).toFixed(2)) || 0;
            $('#total-meta-programada').text(total_programado.format());
            $('#total-avance-mes').text(total_avance.format());
            $('#total-avance-acumulado').text(total_acumulado.format());
            $('#total-porcentaje').text(total_porcentaje_acumulado+'% ');
            $('#modalEditarAvance').modal('show');
        }
    });    
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

$('.avance-mes').on('keyup',function(){ $(this).change() });
$('.avance-mes').on('change',function(){
    var jurisdiccion = $(this).attr('data-jurisdiccion');
    //Actualiza la columna de avance acumulado
    var row = '#tabla-avances-metas > tbody > tr[data-clave-jurisdiccion="'+jurisdiccion+'"]';

    if($(this).val() == ''){
        $(row +' > td.avance-acumulado > span.nueva-cantidad').text('');
        $(row +' > td.avance-mes').html('');
        $(row +' > td.avance-mes').attr('data-estado-avance','');
    }else{
        var avance = parseFloat($(this).val()) || 0;
        //Actualiza la columna de porcentaje acumulado
        var acumulado = parseFloat($(row +' > td.avance-acumulado').attr('data-acumulado')) || 0;
        acumulado += avance;
        var total_programado = $(row +' > td.meta-programada').attr('data-meta');

        if(acumulado > 0){
            $(row +' > td.avance-acumulado > span.nueva-cantidad').text('+'+avance);
        }else{
            $(row +' > td.avance-acumulado > span.nueva-cantidad').text('');
        }

        if(total_programado > 0){
            var avance_mes = parseFloat(((acumulado  / total_programado ) * 100).toFixed(2)) || 0;

            if(avance_mes > 110){
                $(row +' > td.avance-mes').html('<small class="text-danger"><span class="fa fa-arrow-up"></span> '+avance_mes+'%</small>');
                $(row +' > td.avance-mes').attr('data-estado-avance','1');
            }else if(avance_mes < 90){
                $(row +' > td.avance-mes').html('<small class="text-danger"><span class="fa fa-arrow-down"></span> '+avance_mes+'%</small>');
                $(row +' > td.avance-mes').attr('data-estado-avance','1');
            }else{
                $(row +' > td.avance-mes').html('<small class="text-success">'+avance_mes+'%</small>');
                $(row +' > td.avance-mes').attr('data-estado-avance','');
            }
        }else{
            var avance_mes = acumulado * 100;

            if(avance_mes == 0){
                $(row +' > td.avance-mes').html('<small class="text-success">'+avance_mes+'%</small>');
                $(row +' > td.avance-mes').attr('data-estado-avance','');
            }else{
                $(row +' > td.avance-mes').html('<small class="text-primary"><span class="fa fa-arrow-up"></span> '+avance_mes+'%</small>');
                $(row +' > td.avance-mes').attr('data-estado-avance','1');
            }
        }
    }

    var suma = 0;
    $('.avance-mes').each(function(){
        suma += parseFloat($(this).val()) || 0;
    });
    suma = parseFloat(suma.toFixed(2));
    $('#total-avance-mes').text(suma);

    total_programado = parseFloat($('#total-meta-programada').text());
    total_acumulado = parseFloat($('#total-avance-acumulado').text());
    var total_porcentaje_acumulado = parseFloat((((total_acumulado + suma) * 100) / total_programado).toFixed(2))||0;
    $('#total-porcentaje').text(total_porcentaje_acumulado+'% ');


    if($('td.avance-mes[data-estado-avance=1]').length){
        $('#justificacion-acumulada').attr('disabled',false);
        //$('#tab-link-justificacion').attr('data-toggle','tab');
        //$('#tab-link-justificacion').parent().removeClass('disabled');
    }else{
        $('#justificacion-acumulada').attr('disabled',true);
        //$('#tab-link-justificacion').attr('data-toggle','');
        //$('#tab-link-justificacion').parent().addClass('disabled');
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

$('#modalEditarAvance').on('hide.bs.modal',function(e){
    $('#modalEditarAvance .alert').remove();
    $('#form_avance').get(0).reset();
    $('#form_avance input[type="hidden"]').val('');
    //$('#form_avance input[type="number"]').attr('disabled',true);
    $('#form_avance input[type="number"]').attr('data-meta-programada','');
    $('td.avance-mes').text('');
    $('td.avance-mes').attr('data-estado-avance','');
    $('td.avance-acumulado').attr('data-acumulado','');
    $('td.meta-programada').attr('data-meta','0');
    $('td.meta-programada').text('0');
    $('span.nueva-cantidad').text('');
    $('span.vieja-cantidad').text('0');
    $('#justificacion-acumulada').attr('disabled',true);
    //$('#tab-link-justificacion').attr('data-toggle','');
    //$('#tab-link-justificacion').parent().addClass('disabled');
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