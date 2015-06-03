/*=====================================

    # Nombre:
        indicadores-fassa.js

    # Módulos:
        expediente/indicadores-fassa

    # Descripción:
        Funciones para captura de indicadores de FASSA

=====================================*/

var moduloResource = new RESTfulRequests(SERVER_HOST+'/v1/indicadores-fassa');
var moduloDatagrid = new Datagrid("#datagridIndicadores",moduloResource,{ formatogrid:true, pagina: 1});

moduloDatagrid.init();
//moduloDatagrid.actualizar();
moduloDatagrid.actualizar({
    _success: function(response){
        moduloDatagrid.limpiar();
        for(var i in response.data){
            var nivel = '';
            switch(response.data[i].claveNivel){
                case 'F':
                    nivel = 'Fin';
                    break;
                case 'P':
                    nivel = 'Proposito';
                    break;
                case 'C':
                    nivel = 'Componente';
                    break;
                case 'A':
                    nivel = 'Actividad';
                    break;
            }
            response.data[i].claveNivel = nivel;

            response.data[i].modificadoAl = response.data[i].modificadoAl.substring(0,11);
        }
        moduloDatagrid.cargarDatos(response.data);                         
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

function editar(e){
    moduloResource.get(e,null,{
        _success: function(response){
            $('#modalIndicador').find(".modal-title").html('Editar Indicador');

            $('#nivel-indicador').val(response.data.claveNivel);
            $('#indicador').val(response.data.indicador);
            $('#tipo-formula').val(response.data.claveTipoFormula);
            $('#formula').val(response.data.formula);
            $('#fuente-informacion').val(response.data.fuenteInformacion);

            if(response.data.idEstatus == 2 || response.data.idEstatus == 4){
                bloquear_controles('.informacion-indicador');
            }

            if(response.data.metas_detalle.length){
                var html_tabs = '';
                var html_panels = '';
                for(var i in response.data.metas_detalle){
                    var meta = response.data.metas_detalle[i];
                    if(meta.idEstatus < 4){
                        $('#ejercicio').val(meta.ejercicio);
                        $('#numerador').val(meta.numerador);
                        $('#denominador').val(meta.denominador);
                        $('#porcentaje').text(parseFloat(meta.porcentaje).format(2) + ' %');

                        $('#unidad-responsable').val(meta.claveUnidadResponsable);
                        $('#responsable-informacion').empty();
                        var html_opciones = '<option value="">Selecciona un responsable</option>';
                        for(var i in meta.responsables){
                            html_opciones += '<option value="'+meta.responsables[i].id+'">'+meta.responsables[i].nombre+'</option>';
                        }
                        $('#responsable-informacion').html(html_opciones);
                        $('#responsable-informacion').val(meta.idResponsableInformacion);

                        $('#id-meta').val(meta.id);

                        if(meta.idEstatus == 2 || meta.idEstatus == 4 || meta.idEstatus == 5){
                            bloquear_controles('.informacion-meta');
                        }
                    }else{
                        html_tabs += '<li class="borrable" role="presentation"><a href="#meta-'+meta.ejercicio+'" aria-controls="meta-'+meta.ejercicio+'" role="tab" data-toggle="tab">'+meta.ejercicio+'</a></li>';

                        html_panels += '<div role="tabpanel" class="tab-pane borrable" id="meta-'+meta.ejercicio+'"><br>';
                        html_panels += '<div class="row">'+
                            '<div class="col-sm-4">'+
                                '<div class="form-group">'+
                                    '<label class="control-label">Numerador</label><p class="form-control-static">'+parseFloat(meta.numerador).format(2)+'</p>'+
                                '</div>'+
                            '</div>'+
                            '<div class="col-sm-4">'+
                                '<div class="form-group">'+
                                    '<label class="control-label">Denominador</label><p class="form-control-static">'+parseFloat(meta.denominador).format(2)+'</p>'+
                                '</div>'+
                            '</div>'+
                            '<div class="col-sm-4">'+
                                '<div class="form-group">'+
                                    '<label class="control-label">Porcentaje</label><p class="form-control-static">'+parseFloat(meta.porcentaje).format(2)+' %</p>'+
                                '</div>'+
                            '</div>'+
                            '<div class="col-sm-6">'+
                                '<div class="form-group">'+
                                    '<label class="control-label">Lider del Programa</label><p class="form-control-static">'+meta.nombreLiderPrograma+'</p>'+
                                    '<p class="help-block">'+meta.cargoLiderPrograma+'</p>'+
                                '</div>'+
                            '</div>'+
                            '<div class="col-sm-6">'+
                                '<div class="form-group">'+
                                    '<label class="control-label">Responsable de la Información</label><p class="form-control-static">'+meta.nombreResponsableInformacion+'</p>'+
                                    '<p class="help-block">'+meta.cargoResponsableInformacion+'</p>'+
                                '</div>'+
                            '</div>'+
                        '</div>';
                        html_panels += '</div>';
                    }
                }
                if(html_panels != ''){
                    $('#tab-lista-ejercicios').prepend(html_tabs);
                    $('#tab-lista-metas').prepend(html_panels);
                }
            }else{

            }
            
            $('#id').val(response.data.id);
            $('#modalIndicador').modal('show');
        }
    });
}

$('#tipo-formula').on('change',function(){
    $('#numerador').change();
});

$('#denominador,#numerador').on('keyup',function(){
    $(this).change();
});

$('#denominador,#numerador').on('change',function(){
    if($('#tipo-formula').val()){
        var numerador = parseFloat($('#numerador').val()) || 0;
        var denominador = parseFloat($('#denominador').val()) || 1;
        var porcentaje = '';
        if($('#tipo-formula').val() == 'T'){
            porcentaje = parseFloat((numerador * 100000)/denominador);
            //porcentaje = parseFloat((numerador/denominador) * 100000);
        }else{
            porcentaje = parseFloat((numerador * 100)/denominador);
            //porcentaje = parseFloat((numerador/denominador) * 100);
        }
        $('#porcentaje').text(porcentaje.format(2) + ' %');
    }else{
        $('#porcentaje').text('%');
    }
});

$('#modalIndicador').on('shown.bs.modal', function () {
    $('#modalIndicador').find('input').eq(0).focus();
     $('#tab-lista-ejercicios a:last').tab('show');
});

$('#modalIndicador').on('hidden.bs.modal',function(){
    $('#form_indicador_fassa').get(0).reset();
    $('#form_indicador_fassa #id').val("");
    $('#form_indicador_fassa #id-meta').val("");
    $('#form_indicador_fassa .borrable').remove();
    $('#unidad-responsable').change();
    $('#form_indicador_fassa .seguro-edicion').remove();
    $('#form_indicador_fassa .form-control').prop('disabled',false);
    $('#porcentaje').text('%');
    Validation.cleanFormErrors('#form_indicador_fassa');
});

$('#btn-agregar-indicador').on('click', function () {
    $('#modalIndicador').find(".modal-title").html("Nuevo Indicador");
    $('#modalIndicador').modal('show');
});

$('#unidad-responsable').on('change',function(){
    if($(this).val()){
        var parametros = {'cargar-responsables':1,'unidad-responsable':$(this).val()};
        moduloResource.get(null,parametros,{
            _success: function(response){
                $('#responsable-informacion').empty();
                var html_opciones = '<option value="">Selecciona un responsable</option>';
                for(var i in response.data){
                    html_opciones += '<option value="'+response.data[i].id+'">'+response.data[i].nombre+'</option>';
                }
                $('#responsable-informacion').html(html_opciones);
            }
        });
    }else{
        $('#responsable-informacion').empty();
        var html_opciones = '<option value="">Selecciona una unidad</option>';
        $('#responsable-informacion').html(html_opciones);
    }
});

$('#btn-guardar-indicador').on('click',function(e){
    e.preventDefault();
    Validation.cleanFormErrors('#form_indicador_fassa');

    var parametros = $("#form_indicador_fassa").serialize();
    if($('#id').val()){
        moduloResource.put($('#id').val(), parametros,{
            _success: function(response){
                moduloDatagrid.actualizar();
                if($('#id-meta').val() == ""){
                    $('#id-meta').val(response.data.meta.id);
                }
                MessageManager.show({data:'Elemento actualizado con éxito',timer:4});
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
    }else{
        moduloResource.post(parametros,{
            _success: function(response){
                moduloDatagrid.actualizar();
                $('#id').val(response.data.id);
                $('#id-meta').val(response.data.meta.id);
                MessageManager.show({data:'Elemento creado con éxito',timer:4});
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
});

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