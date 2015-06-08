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
            item.otro = response.data[i].unidadResponsable;

            var nivel = '';
            switch(response.data[i].claveNivel){
                case 'F': nivel = 'Fin'; break;
                case 'P': nivel = 'Proposito'; break;
                case 'C': nivel = 'Componente'; break;
                case 'A': nivel = 'Actividad'; break;
            }
            item.nivel = nivel;
            var clase_estatus = '';
            switch(response.data[i].idEstatus){
                case 1: clase_estatus = 'label-info'; break;
                case 2: clase_estatus = 'label-warning'; break;
                case 3: clase_estatus = 'label-danger'; break;
                case 4: clase_estatus = 'label-primary'; break;
                case 5: clase_estatus = 'label-success'; break;
                default: clase_estatus = 'label-default'; break;
            }
            item.mas = '<span class="label '+clase_estatus+'">'+response.data[i].estatus+'</span>';

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
    if($('#tipo-formula').val()){
        var numerador = parseFloat($('#numerador').val()) || 0;
        var denominador = parseFloat($('#denominador').val()) || 1;
        var porcentaje = '';
        if($('#tipo-formula').val() == 'T'){
            porcentaje = parseFloat((numerador * 100000)/denominador);
        }else{
            porcentaje = parseFloat((numerador * 100)/denominador);
        }
        $('#porcentaje').text(porcentaje.format(2) + ' %');
    }else{
        $('#porcentaje').text('%');
    }
});

$('#modalIndicador').on('hidden.bs.modal',function(){
    $('#form_indicador_fassa').get(0).reset();
    $('#form_indicador_fassa #id').val("");
    $('#form_indicador_fassa #id-meta').val("");
    $('#form_indicador_fassa .seguro-edicion').remove();
    $('#form_indicador_fassa .form-control').prop('disabled',false);
    $('#estatus-programacion').empty();
    $('#estatus-avance').empty();
    $('#porcentaje').text('%');
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

            if(response.data.registroAvance){
                //
            }else{
                var label_html = '<div class="text-center label-default"><span class="label"><big>Inactivo</big></span></div>';
                $('#estatus-avance').html(label_html);
            }

            bloquear_controles('.informacion-avance');

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

    moduloResource.put($('#id').val(), parametros,{
        _success: function(response){
            moduloDatagrid.actualizar();
            if(validar){
                console.log('Enviado a Validar...');
            }else{
                MessageManager.show({data:'Elemento actualizado con éxito',timer:4});
                $('#modalIndicador').modal('hide');
            }
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