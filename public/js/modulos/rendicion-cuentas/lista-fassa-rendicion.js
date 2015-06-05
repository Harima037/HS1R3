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

            /*if(response.data.idEstatus == 2 || response.data.idEstatus == 4){
                bloquear_controles('.informacion-indicador');
            }*/

            if(response.data.metas_detalle.length){
                var html_tabs = '';
                var html_panels = '';
                for(var i in response.data.metas_detalle){
                    var meta = response.data.metas_detalle[i];
                    if(meta.ejercicio == response.data.ejercicio_actual){
                        //$('#ejercicio').val(meta.ejercicio);

                        $('#numerador').text(parseFloat(meta.numerador).format(2));
                        $('#denominador').text(parseFloat(meta.denominador).format(2));
                        $('#porcentaje').text(parseFloat(meta.porcentaje).format(2) + ' %');

                        $('#unidad-responsable').val(meta.claveUnidadResponsable);
                        $('#responsable-informacion').empty();
                        var html_opciones = '<option value="">Selecciona un responsable</option>';
                        for(var i in meta.responsables){
                            html_opciones += '<option value="'+meta.responsables[i].id+'">'+meta.responsables[i].nombre+'</option>';
                        }
                        $('#responsable-informacion').html(html_opciones);
                        $('#responsable-informacion').val(meta.idResponsableInformacion);

                        var label_class = '';
                        switch(meta.idEstatus){
                            case 1:
                                label_class = 'label-info';
                                break;
                            case 2:
                                label_class = 'label-warning';
                                break;
                            case 3:
                                label_class = 'label-danger';
                                break;
                            case 4:
                                label_class = 'label-primary';
                                break;
                            case 5:
                                label_class = 'label-success';
                                break;
                        }

                        var label_html = '<div class="col-sm-2 col-sm-offset-10 '+label_class+'"><span class="label"><big>'+meta.estatus+'</big></span></div>';
                        $('#lbl-estatus').html(label_html);

                        $('#id')
                        $('#id-meta').val(meta.id);

                        /*if(meta.idEstatus == 2 || meta.idEstatus == 4 || meta.idEstatus == 5){
                            bloquear_controles('.informacion-meta');
                        }*/
                    }else{
                        html_tabs += '<li class="borrable" role="presentation"><a href="#meta-'+meta.ejercicio+'" aria-controls="meta-'+meta.ejercicio+'" role="tab" data-toggle="tab">'+meta.ejercicio+'</a></li>';

                        html_panels += '<div role="tabpanel" class="tab-pane borrable" id="meta-'+meta.ejercicio+'"><br>';
                        html_panels += '<div class="row">'+
                            '<div class="col-sm-8">'+
                                '<div class="form-group">'+
                                    '<label class="control-label">Lider del Programa</label><p class="form-control-static">'+meta.nombreLiderPrograma+'</p>'+
                                    '<p class="help-block">'+meta.cargoLiderPrograma+'</p>'+
                                '</div>'+
                            
                                '<div class="form-group">'+
                                    '<label class="control-label">Responsable de la Información</label><p class="form-control-static">'+meta.nombreResponsableInformacion+'</p>'+
                                    '<p class="help-block">'+meta.cargoResponsableInformacion+'</p>'+
                                '</div>'+
                            '</div>'+
                            '<div class="col-sm-4">'+
                                '<div class="form-group">'+
                                    '<label class="control-label">Denominador</label><p class="form-control-static">'+parseFloat(meta.denominador).format(2)+'</p>'+
                                '</div>'+
                                '<div class="form-group">'+
                                    '<label class="control-label">Numerador</label><p class="form-control-static">'+parseFloat(meta.numerador).format(2)+'</p>'+
                                '</div>'+
                                '<div class="form-group">'+
                                    '<label class="control-label">Porcentaje</label><p class="form-control-static">'+parseFloat(meta.porcentaje).format(2)+' %</p>'+
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