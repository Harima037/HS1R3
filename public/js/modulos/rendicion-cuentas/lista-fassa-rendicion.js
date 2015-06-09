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

            if(response.data[i].claveFrecuencia == 'A'){
                item.trim1 = '<div class="text-center text-muted"><span class="fa fa-minus"></span></div>';
                item.trim2 = '<div class="text-center text-muted"><span class="fa fa-minus"></span></div>';
                item.trim3 = '<div class="text-center text-muted"><span class="fa fa-minus"></span></div>';
                if(response.mes_actual == 12 && (response.data[i].idEstatus == 4 || response.data[i].idEstatus == 5)){
                    item.trim4 = '<div class="text-center"><span class="fa fa-unlock"></span></div>';
                }else{
                    item.trim4 = '<div class="text-center"><span class="fa fa-lock"></span></div>';
                }
            }else{
                var trim_actual = response.mes_actual/3;
                var trim_cerrado = '<div class="text-center"><span class="fa fa-lock"></span></div>';
                var trim_abierto = '<div class="text-center"><span class="fa fa-unlock"></span></div>';
                var trim_perdido = '<div class="text-center text-muted"><span class="fa fa-times"></span></div>';

                item.trim1 = (trim_actual == 1)?trim_abierto:(trim_actual > 1)?trim_perdido:trim_cerrado;
                item.trim2 = (trim_actual == 2)?trim_abierto:(trim_actual > 2)?trim_perdido:trim_cerrado;
                item.trim3 = (trim_actual == 3)?trim_abierto:(trim_actual > 3)?trim_perdido:trim_cerrado;
                item.trim4 = (trim_actual == 4)?trim_abierto:(trim_actual > 4)?trim_perdido:trim_cerrado;
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
            $('#formula').text(response.data.formula);
            //$('#fuente-informacion').text(response.data.fuenteInformacion);

            $('#numerador').val(parseFloat(response.data.numerador));
            $('#denominador').val(parseFloat(response.data.denominador));
            if(response.data.porcentaje){
                $('#porcentaje').text(parseFloat(response.data.porcentaje).format(2) + ' %');
                $('#porcentaje').attr('data-valor',response.data.porcentaje);
            }
            /*
            var responsable_informacion = '<big>' + response.data.nombreResponsableInformacion + '</big><br><small>'+response.data.cargoResponsableInformacion+'</small>';
            $('#responsable-informacion').html(responsable_informacion);
            var lider_programa = '<big>' + response.data.nombreLiderPrograma + '</big><br><small>'+response.data.cargoLiderPrograma+'</small>';
            $('#lider-programa').html(lider_programa);
            */

            var label_class = '';
            switch(response.data.idEstatus){
                case 1: label_class = 'label-info'; break;
                case 2: label_class = 'label-warning'; break;
                case 3: label_class = 'label-danger'; break;
                case 4: label_class = 'label-primary'; break;
                case 5: label_class = 'label-success'; break;
            }

            var label_html = '<div class="text-center '+label_class+'"><span class="label"><big>'+response.data.estatus+'</big></span></div>';
            $('#estatus-programacion').html(label_html);
            if(response.data.idEstatus == 2 || response.data.idEstatus == 4 || response.data.idEstatus == 5){
                bloquear_controles('.informacion-meta');
            }

            var puede_editar_avance = false;
            if(response.data.claveFrecuencia == 'A' && response.data.mes_actual == 12 && (response.data.idEstatus == 4 || response.data.idEstatus == 5)){
                puede_editar_avance = true;
            }else if(response.data.claveFrecuencia == 'T' && (response.data.mes_actual%3) == 0){
                puede_editar_avance = true;
            }

            label_html = '<div class="text-center text-muted"><big>Inactivo</big></div>';
            if(!puede_editar_avance){
                bloquear_controles('.informacion-avance');
            }else{
                if(response.data.registro_avance.length){
                    for(var i in response.data.registro_avance){
                        var avance = response.data.registro_avance[i];
                        if(avance.mes == response.data.mes_actual){
                            $('#avance-denominador').val(parseFloat(avance.denominador));
                            $('#avance-numerador').val(parseFloat(avance.numerador));
                            $('#avance-porcentaje').text(parseFloat(avance.porcentaje).format(2) + ' %');
                            $('#justificacion').val(avance.justificacionAcumulada);
                            var porcentaje_total = (avance.porcentaje/response.data.porcentaje)*100;
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
                        }
                    }
                }
            }
            $('#estatus-avance').html(label_html);

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
    if(porcentaje_total < 90){
        $('#porcentaje-total').html('<span class="text-danger"><span class="fa fa-arrow-down"></span> ' + porcentaje_total.format(2) + ' %</span>');
        $('#justificacion').prop('disabled',false);
    }else if(porcentaje_total > 110){
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