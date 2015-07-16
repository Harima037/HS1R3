/*=====================================

    # Nombre:
        vista-rendicion-programas.js

    # Módulo:
        seguimiento/avance-programa

    # Descripción:
        Para rendición de cuentas de Programas Presupuestales

=====================================*/

// Inicialización General para casi cualquier módulo
var moduloResource = new RESTfulRequests(SERVER_HOST+'/v1/rend-cuenta-prog');

$('#btn-programa-cancelar').on('click',function(){
    window.location.href = SERVER_HOST+'/rendicion-cuentas/rend-cuenta-prog';
});

$('#btn-enviar-programa').on('click',function(){
    var parametros = 'guardar=validar-avance';

    Confirm.show({
        titulo:"Enviar avance a Validación",
        mensaje: "¿Estás seguro que deseas enviar este avance para su validación? <br><b>IMPORTANTE:</b> Mientras el programa este en validación no se podra editar.",
        si: '<span class="fa fa-send"></span> Enviar',
        no: 'Cancelar',
        callback: function(){
            moduloResource.put($('#id').val(),parametros,{
                _success: function(response){
                    MessageManager.show({data:response.data,type:'OK',timer:6});
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

if($('#id').val()){
    var parametros = {mostrar:'datos-programa-presupuestario'};
    moduloResource.get($('#id').val(),parametros,{
        _success: function (response){
            $('#lbl-programa-presup').text(response.data.claveProgramaPresupuestario + ' ' + response.data.programaPresupuestario);
            $('#lbl-unidad-responsable').text(response.data.claveUnidadResponsable + ' ' + response.data.unidadResponsable);
        }
    });
}

/********************************************************************************************************************************
        Inicio: Seguimiento de Metas
*********************************************************************************************************************************/
var indicadoresDatagrid = new Datagrid("#datagridIndicadores",moduloResource,{ formatogrid:true, pagina: 1, idPrograma: $('#id').val(), grid:'rendicion-indicadores'});
indicadoresDatagrid.init();
indicadoresDatagrid.actualizar({ _success: function(response){ llenar_grid_indicadores(response); } });

$('#btn-guardar-avance').on('click',function(){
    if($('#trimestre-porcentaje').attr('data-estado-avance')){
        if(($('#analisis-resultados').val().trim() == '') || ($('#justificacion-acumulada').val().trim() == '')){
            MessageManager.show({data:'Es necesario capturar una justificacion en base al porcentaje de avance del mes.',container:'#modalEditarAvance .modal-body',type:'ADV'});
            return;
        }
    }

    var parametros = $('#form_avance').serialize();
    parametros += '&guardar=avance-metas&id-programa='+$('#id').val();

    Validation.cleanFormErrors('#form_avance');

    if($('#id-avance').val()){
        moduloResource.put($('#id-avance').val(),parametros,{
            _success: function(response){
                MessageManager.show({data:'Datos del proyecto almacenados con éxito',type:'OK',timer:4});
                //accionesDatagrid.actualizar({ _success: function(response){ llenar_grid_acciones(response); } });
                indicadoresDatagrid.actualizar();
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
                //accionesDatagrid.actualizar({ _success: function(response){ llenar_grid_acciones(response); } });
                indicadoresDatagrid.actualizar();
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
    var parametros = {'mostrar':'datos-metas-avance'};
    moduloResource.get(e,parametros,{
        _success: function(response){
            if(response.data.claveTipoIndicador == 'F'){
                $('#modalEditarAvance').find(".modal-title").html("Seguimiento de Metas del Fin");
            }else{
                $('#modalEditarAvance').find(".modal-title").html("Seguimiento de Metas del Proposito");
            }

            $('#indicador').text(response.data.descripcionIndicador);
            $('#unidad-medida').text(response.data.unidadMedida);
            $('#meta-total').text((parseFloat(response.data.valorNumerador) || 0).format(2));
            $('#id-indicador').val(response.data.id);

            var trimestre = $('#trimestre').val();
            var acumulado = 0;
            for(var i = 1; i <= trimestre ; i++){
                acumulado += (parseFloat(response.data['trim'+i]) || 0);
            }

            var valor_trimestre = (parseFloat(response.data['trim'+trimestre]) || 0);

            $('#trimestre-meta').text((valor_trimestre+0).format(2));
                
            $('#trimestre-acumulada').attr('data-valor',acumulado);
            $('#trimestre-acumulada').text(acumulado.format(2));

            var avance_trimestre = null;
            var avance_acumulado = 0;
            if(response.data.registro_avance.length){
                for(var i in response.data.registro_avance){
                    var avance = response.data.registro_avance[i];
                    if(avance.trimestre == trimestre){
                        avance_trimestre = parseFloat(avance.avance);
                        $('#id-avance').val(avance.id);
                        $('#analisis-resultados').val(avance.analisisResultados);
                        $('#justificacion-acumulada').val(avance.justificacionAcumulada);

                        if(avance.comentarios.length){
                            for(var i in avance.comentarios){
                                var comentario = avance.comentarios[i];
                                var id_campo = comentario.idCampo;
                                var observacion = comentario.comentario;
                                $('#'+id_campo).parent('.form-group').addClass('has-warning');
                                $('#'+id_campo).after('<p class="texto-comentario help-block"><span class="fa fa-warning"></span> '+observacion+'</p>');
                            }
                        }

                    }else{
                        avance_acumulado += parseFloat(avance.avance);
                    }
                }
            }

            $('#avance-trimestre').val(avance_trimestre);

            $('#trimestre-avance').attr('data-valor',avance_acumulado);
            $('#trimestre-avance').text(avance_acumulado.format(2));

            $('#trimestre-total').attr('data-valor',avance_acumulado + avance_trimestre);
            $('#trimestre-total').text((avance_acumulado + avance_trimestre).format(2));

            if(parseFloat($('#trimestre-total').attr('data-valor')) > 0){
                $('#avance-trimestre').change();
            }
            
            $('#modalEditarAvance').modal('show');
        }
    });
}
$('#avance-trimestre').on('keyup',function(){ $(this).change() });
$('#avance-trimestre').on('change',function(){
    var trimestre = $('#trimestre').val();

    var avance = parseFloat($(this).val()) || 0;
    var acumulado = parseFloat($('#trimestre-avance').attr('data-valor'));

    var total_acumulado = avance + acumulado;

    $('#trimestre-total').attr('data-valor',total_acumulado);
    $('#trimestre-total').text(total_acumulado.format(2));

    var total_programado = parseFloat($('#trimestre-acumulada').attr('data-valor'));
    var necesita_justificar = false;
    if(total_programado == 0 && total_acumulado ==  0){
        total_porcentaje_acumulado = '<small class="text-success">0%</small>';
        $('#trimestre-porcentaje').attr('data-estado-avance','');
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
            $('#trimestre-porcentaje').attr('data-estado-avance','1');
            necesita_justificar = true;
        }else if(total_porcentaje_acumulado < 90){
            total_porcentaje_acumulado = '<small class="text-danger"><span class="fa fa-arrow-down"></span> '+total_porcentaje_acumulado+'%</small>';
            $('#trimestre-porcentaje').attr('data-estado-avance','1');
            necesita_justificar = true;
        }else if(total_programado == 0 && total_porcentaje_acumulado > 0){
            total_porcentaje_acumulado = '<small class="text-info"><span class="fa fa-arrow-up"></span> '+total_porcentaje_acumulado+'%</small>';
            $('#trimestre-porcentaje').attr('data-estado-avance','1');
            necesita_justificar = true;
        }else{
            total_porcentaje_acumulado = '<small class="text-success">'+total_porcentaje_acumulado+'%</small>';
            $('#trimestre-porcentaje').attr('data-estado-avance','');
        }
    }

    $('#trimestre-porcentaje').html(total_porcentaje_acumulado);

    if(necesita_justificar){
        $('#justificacion-acumulada').attr('disabled',false);
        if($('#justificacion-acumulada').val() == 'El avance se encuentra dentro de los parametros establecidos'){
            $('#justificacion-acumulada').val('');
        }
    }else{
        $('#justificacion-acumulada').attr('disabled',true);
    }
});

$('#modalEditarAvance').on('hide.bs.modal',function(e){
    $('#modalEditarAvance .alert').remove();
    $('#form_avance').get(0).reset();
    $('#form_avance input[type="hidden"]').val('');
    $('#justificacion-acumulada').attr('disabled',true);
    $('#trimestre-porcentaje').attr('data-estado-avance','');
    $('#trimestre-porcentaje').text('0%');
    $('#id-avance').val('');
    $('#form_avance .texto-comentario').remove();
    $('#form_avance .has-warning').removeClass('has-warning');
    Validation.cleanFormErrors('#form_avance');
});

function llenar_grid_indicadores(response){
    indicadoresDatagrid.limpiar();
    var trimestre = parseInt($('#trimestre').val());
    var datos_grid = [];
    for(var i in response.data){
        var indicador = response.data[i];

        var item = {};

        item.id = indicador.id;

        if(indicador.claveTipoIndicador == 'F'){
            item.nivel = 'Fin';
        }else{
            item.nivel = 'Proposito';
        }
        item.indicador = indicador.descripcionIndicador;
        item.meta = indicador.valorNumerador;
        item.avances_acumulados = 0;
        item.avances_mes = 0;
        item.justificacion = '';

        if(indicador.registro_avance.length){
            for(var j in indicador.registro_avance){
                var avance = indicador.registro_avance[j];
                item.avances_acumulados += parseFloat(avance.avance);
                if(avance.trimestre == trimestre){
                    item.justificacion += '<span class="fa fa-floppy-o"></span> ';
                    item.avances_mes += parseFloat(avance.avance);
                    if(avance.justificacion){
                        item.justificacion += '<span class="fa fa-align-left"></span>';
                    }
                    if(avance.comentarios.length){
                        item.nivel = '<span class="text-warning fa fa-warning"></span> ' + item.nivel;
                    }
                }
            }
        }

        datos_grid.push(item);
    }
    indicadoresDatagrid.cargarDatos(datos_grid);                         
    var total = parseInt(response.resultados/indicadoresDatagrid.rxpag); 
    var plus = parseInt(response.resultados)%indicadoresDatagrid.rxpag;
    if(plus>0) 
        total++;
    indicadoresDatagrid.paginacion(total);
}
/********************************************************************************************************************************
        Fin: Seguimiento de Metas
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