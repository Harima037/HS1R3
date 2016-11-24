/*=====================================

    # Nombre:
        lista-fassa-rendicion.js

    # Módulos:
        rend-cuenta-fassa/rend-cuenta-fassa

    # Descripción:
        Funciones para captura de la rendicion de cuentas de los indicadores del FASSA

=====================================*/

var moduloResource = new RESTfulRequests(SERVER_HOST+'/v1/rend-cuenta-fassa');
var moduloDatagrid = new Datagrid("#datagridIndicadores",moduloResource,{ formatogrid:true, pagina: 1});

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
        moduloDatagrid.cargarTotalResultados(response.resultados,'<b>Indicador(es)</b>');
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
            moduloDatagrid.cargarTotalResultados(0,'<b>Indicador(es)</b>');
            var colspan = $(moduloDatagrid.selector + " thead > tr th").length;
            $(moduloDatagrid.selector + " tbody").append("<tr><td colspan='"+colspan+"' style='text-align:left'><i class='fa fa-info-circle'></i> "+json.data+"</td></tr>");

        }else{
            json.type = 'ERR';
            MessageManager.show(json);
            moduloDatagrid.limpiar();
        }
        
    }
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
            var tasa = $('#tasa').val();
            porcentaje_avance = Math.round((parseFloat((numerador * tasa)/denominador))*100)/100;
        }else{
            porcentaje_avance = Math.round((parseFloat((numerador * 100)/denominador))*100)/100;
        }
        $('#avance-porcentaje').text(porcentaje_avance.format(2) + ' %');
    }else{
        $('#avance-porcentaje').text('%');
    }
    $('#avance-porcentaje').attr('data-valor',Math.round(porcentaje_avance * 100) / 100);

    var porcentaje_total = 0;
    if(porcentaje_avance){
        var porcentaje_meta = parseFloat($('#porcentaje-trimestre').attr('data-valor'));
        porcentaje_total = (porcentaje_avance / porcentaje_meta)*100;
        $('#porcentaje-total').text(porcentaje_total.format(2) + ' %')
    }else{
        $('#porcentaje-total').text('%');
    }
    actualizar_porcentaje(porcentaje_total);
});

$('#modalIndicador').on('hidden.bs.modal',function(){
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
    $('#porcentaje-total').text('%');
    $('#porcentaje-total').attr('data-valor','');
    $('#justificacion').prop('disabled',true);
    $('#form_indicador_fassa .texto-comentario').remove();
    $('#form_indicador_fassa .has-warning').removeClass('has-warning');
    $('#btn-imprimir-reporte').addClass('hidden');
    $('#panel-avance-fassa').removeClass('hidden');
$('#panel-programacion-fassa').removeClass('hidden');
    Validation.cleanFormErrors('#form_indicador_fassa');
});

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
            $('#tasa').val(parseFloat(response.data.tasa));
            $('#formula').text(response.data.formula);

            $('#numerador').text((parseFloat(response.data.numerador) || 0).format(2));
            $('#denominador').text((parseFloat(response.data.denominador) || 0).format(2));
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

            var rows = '';
            for (var i = inicio; i <= final; i = i + incremento) {
                rows += '<tr>' +
                            '<th>Trimestre '+i+'</th>' +
                            '<td>' +
                                '<input type="number" min="0" class="form-control informacion-meta informacion-meta-numerador" id="numerador-'+i+'" name="trimestre['+i+'][numerador]" data-trimestre="'+i+'">' +
                            '</td>' +
                            '<td>' +
                                '<input type="number" min="0" class="form-control informacion-meta informacion-meta-denominador" id="denominador-'+i+'" name="trimestre['+i+'][denominador]" data-trimestre="'+i+'">' +
                            '</td>' +
                            '<td>' +
                                '<span class="form-control" id="porcentaje-'+i+'">%</span>' +
                            '</td>' +
                        '</tr>';
            }

            $('#table-programacion-trimestres tbody').html(rows);
            actualizar_acciones_metas();

            var label_html = '<div class="text-center '+label_class+'"><span class="label"><big>'+response.data.estatus+'</big></span></div>';
            $('#estatus-programacion').html(label_html);
            $('#estatus-programacion-trimestre').html(label_html);
            if(response.data.idEstatus == 2 || response.data.idEstatus == 4 || response.data.idEstatus == 5){
                bloquear_controles('.informacion-meta');
            }

            for(var i in response.data.metas_trimestre){
                var meta = response.data.metas_trimestre[i];
                $('#numerador-'+meta.trimestre).val(meta.numerador);
                $('#denominador-'+meta.trimestre).val(meta.denominador);
                $('#porcentaje-'+meta.trimestre).text(parseFloat(meta.porcentaje).format(2)+' %');
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
                
                $('#avance-denominador').val(parseFloat(denominador));
                if(response.data.registro_avance.length){
                    for(var i in response.data.registro_avance){
                        var avance = response.data.registro_avance[i];
                        if(avance.mes == response.data.mes_actual){
                            $('#avance-denominador').val(parseFloat(avance.denominador));
                            $('#avance-numerador').val(parseFloat(avance.numerador));
                            $('#avance-porcentaje').text(parseFloat(avance.porcentaje).format(2) + ' %');
                            $('#justificacion').val(avance.justificacionAcumulada);
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
                            label_html = '<div class="text-center '+label_class+'"><span class="label"><big>'+avance.estatus+'</big></span></div>';
                            $('#id-avance').val(avance.id);

                            if(avance.idEstatus == 2 || avance.idEstatus == 4 || avance.idEstatus == 5){
                                bloquear_controles('.informacion-avance');
                            }
                            
                            if(avance.idEstatus == 4 || avance.idEstatus == 5){
                                $('#btn-imprimir-reporte').removeClass('hidden');
                                $('#btn-imprimir-reporte').off('click');
                                $('#btn-imprimir-reporte').on('click',function(){
                                    window.open(SERVER_HOST+'/v1/reporte-fassa/' + response.data.id);
                                });
                            }
                        }
                    }
                }
            }
            $('#estatus-avance').html(label_html);
            
            if(response.data.comentario.length){
                for(var i in response.data.comentario){
                    var comentario = response.data.comentario[i];
                    var id_campo = comentario.idCampo;
                    var observacion = comentario.observacion;
                    $('#'+id_campo).parent('.form-group').addClass('has-warning');
                    $('#'+id_campo).after('<p class="texto-comentario help-block"><span class="fa fa-warning"></span> '+observacion+'</p>');
                }
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

function actualizar_acciones_metas(){
    $('.informacion-meta').on('keyup',function(){
        $(this).change();
    });
    $('.informacion-meta').on('change',function(){
        var trimestre = $(this).attr('data-trimestre');
        var porcentaje = 0;
        if($('#tipo-formula').val()){
            var numerador = parseFloat($('#numerador-'+trimestre).val()) || 0;
            var denominador = parseFloat($('#denominador-'+trimestre).val()) || 1;
            if($('#tipo-formula').val() == 'T'){
                var tasa = $('#tasa').val();
                porcentaje = parseFloat((numerador * tasa)/denominador);
            }else{
                porcentaje = parseFloat((numerador * 100)/denominador);
            }
            $('#porcentaje-'+trimestre).text(porcentaje.format(2) + ' %');
        }else{
            $('#porcentaje-'+trimestre).text('%');
        }
        $('#porcentaje-'+trimestre).attr('data-valor',porcentaje);
    });
}

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