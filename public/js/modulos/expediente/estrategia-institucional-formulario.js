/*=====================================

    # Nombre:
        estrategia-institucional-formulario.js

    # Módulo:
        expediente/estrategia-institucional-formulario

    # Descripción:
        Se utiliza para crear, editar y eliminar las estrategias institucionales

=====================================*/

// Inicialización General para casi cualquier módulo
var moduloResource = new RESTfulRequests(SERVER_HOST+'/v1/estrategia-institucional');
var moduloResourceResponsables = new RESTfulRequests(SERVER_HOST+'/v1/directorio');

var comentariosIndicadores = {};

var indicadoresDatagrid;
var problemasDatagrid;
var objetivosDatagrid;
var proyectosDatagrid;

var modal_indicador = '#modal_programa_indicador';

var form_programa = '#form_estrategia_datos';
var form_indicador = '#form_programa';

$('.chosen-one').chosen({width:'100%'});

$('#formula').on('change',function(){ ejecutar_formula(); });
$('.valor-trimestre').on('change',function(){ sumar_trimestres(); });
$('.valor-trimestre').on('keyup',function(){ $(this).change(); });
$('#valorDenominador').on('change',function(){ ejecutar_formula(); });
$('#valorDenominador').on('keyup',function(){ $(this).change(); });

/****************************************************************** Carga de datos (Editar) ********************************************************************/
if($('#id').val()){
    var parametros = {'mostrar':'editar-estrategia'};
    moduloResource.get($('#id').val(),parametros,{
        _success:function(response){
            $('#estrategia-pnd').val(response.data.idEstrategiaNacional);
            $('#unidad-responsable').val(response.data.claveUnidadResponsable);
            $('#programa-sectorial').val(response.data.claveProgramaSectorial);
            $('#ejercicio').val(response.data.ejercicio);
            $('#ods').val(response.data.idOds);
            $('#vinculacion-ped').val(response.data.idObjetivoPED);
            $('#descripcion-indicador').val(response.data.descripcionIndicador);
            $('#numerador').val(response.data.numerador);
            $('#denominador').val(response.data.denominador);
            $('#interpretacion').val(response.data.interpretacion);
            $('#tipo-ind').val(response.data.idTipoIndicador);
            $('#dimension').val(response.data.idDimensionIndicador);
            $('#unidad-medida').val(response.data.idUnidadMedida);
            $('#objetivo-estrategico').val(response.data.objetivoEstrategico);
            $('#linea-base').val(response.data.lineaBase);
            $('#anio-base').val(response.data.anioBase);
            $('#formula').val(response.data.idFormula);
            $('#frecuencia').val(response.data.idFrecuenciaIndicador);
            $('#trim1').val(response.data.trim1);
            $('#trim2').val(response.data.trim2);
            $('#trim3').val(response.data.trim3);
            $('#trim4').val(response.data.trim4);
            $('#valorNumerador').val(response.data.valorNumerador);
            $('#valorDenominador').val(response.data.valorDenominador);
            $('#meta').val(response.data.metaIndicador);
            $('#comportamiento-meta-estrategia').val(response.data.idComportamientoAccion);
            $('#tipo-valor-meta-estrategia').val(response.data.idTipoValorMeta);
        
            $('#responsable').on('change',function(){
                if($(this).val()){
                    var cargo = $('#responsable option:selected').attr('data-cargo');
                    $('#ayuda-responsable').text(cargo);
                }else{
                    $('#ayuda-responsable').text('');
                }
            });

            $('#fuente-informacion').val(response.data.fuenteInformacion);
            llenar_responsables(response.data.responsables);
            $('#responsable').val(response.data.idResponsable);
            $('#responsable').change();
            $('#responsable').trigger('chosen:updated');

            if(response.data.metas_anios && response.data.metas_anios.length > 0){
                for(var i in response.data.metas_anios){
                    var item = response.data.metas_anios[i];

                    var lista_anio = $('#lista-meta-anios').val();
                    lista_anio += item.anio+'|';
                    
                    var table_row = '<tr id="meta_row_'+item.anio+'">';
                    table_row += '<td><div class="input-group"><span class="input-group-addon">'+item.anio+'</span><input type="hidden" id="meta-anio-'+item.anio+'" name="meta-anio-'+item.anio+'" value="'+item.anio+'"><span class="input-group-addon"></span></div></td>';
                    table_row += '<td><input type="number" class="form-control" id="meta-numerador-'+item.anio+'" name="meta-numerador-'+item.anio+'" value="'+item.numerador+'"></td>';
                    table_row += '<td><input type="number" class="form-control" id="meta-denominador-'+item.anio+'" name="meta-denominador-'+item.anio+'" value="'+item.denominador+'"></td>';
                    table_row += '<td><input type="number" class="form-control" id="meta-indicador-'+item.anio+'" name="meta-indicador-'+item.anio+'" value="'+item.metaIndicador+'"></td>';
                    table_row += '<td><button type="button" class="btn btn-danger" onClick="eliminar_anio_meta('+item.anio+')"><span class="fa fa-trash"></span></button></td>';
                    table_row += '</tr>';

                    $('#lista-meta-anios').val(lista_anio);
                    $('#tabla-anios-metas-indicadores tbody').append(table_row);
                    $('#txt-anio-agregar').val('');
                }
            }

            //resetear_borrar();

            if(response.data.idEstatus != 1 && response.data.idEstatus != 3){
                bloquear_controles();
            }else if(response.data.idEstatus == 3){
                mostrar_comentarios(response.data.comentario);
            }

            $('.chosen-one').trigger('chosen:updated');
        }
    });
}

$("#unidad-responsable").on("change", function()
{
    unidad_responsable = $("#unidad-responsable").val();
    moduloResourceResponsables.get(unidad_responsable,{},{
        _success:function(response){
            llenar_responsables(response);
            $('#responsable').val('');
            $('#responsable').change();
            $('#responsable').trigger('chosen:updated');
        }
    });
    
});

/****************************************************************** Funciones de datagrids ********************************************************************/
/*

$(form_proyectos + ' .check-select-all-rows').on('change',function(){
    var checado = $(this).prop('checked');
    $(form_proyectos + ' input[type="checkbox"]').prop('checked',checado);
});

$('#datagridProyectos .btn-datagrid-agregar').on('click', function () {
    $(modal_proyecto).find(".modal-title").html("Agregar Proyecto");
    var parametros = {'formatogrid':true,'listar':'buscar-proyecto','id-programa':$('#id').val()};
    moduloResource.get(null,parametros,{
        _success: function(response){
            var rows = '';
            for(var i in response.data){
                rows += '<tr>';
                rows += '<td><input type="checkbox" name="proyectos[]" value="'+response.data[i].id+'"></td>';
                rows += '<td>'+response.data[i].ClavePresupuestaria+'</td>';
                rows += '<td>'+response.data[i].nombreTecnico+'</td>';
                rows += '<td>'+response.data[i].coberturaDescripcion+'</td>';
                rows += '<td>'+response.data[i].username+'</td>';
                rows += '</tr>';
            }
            if(rows.length){
                $('#tabla-proyectos-encontrados tbody').html(rows);
            }else{
                $('#tabla-proyectos-encontrados tbody').html('<tr><td colspan="5"><span class="fa fa-info-sign"></span> No hay datos</td></tr>');
            }
        },
        _error: function(response){
            try{
                var json = $.parseJSON(response.responseText);
                if(json.resultados === 0){
                    if(json.resultados == '0'){
                        $('#tabla-proyectos-encontrados tbody').html('<tr><td colspan="5"><span class="fa fa-info-circle"></span> No hay datos</td></tr>');
                    }
                }else{
                    if(!json.code)
                        MessageManager.show({code:'S03',data:"Hubo un problema al realizar la transacción, inténtelo de nuevo o contacte con soporte técnico."});
                    else{
                        MessageManager.show(json);
                    }
                }
            }catch(e){
                console.log(e);
            }                       
        }
    });
    $(modal_proyecto).modal('show');
});

function editar_problema(e){
    var parametros = {'mostrar':'editar-causa-efecto'};

    moduloResource.get(e,parametros,{
        _success:function(response){
            $('#causa').val(response.data.causa);
            $('#efecto').val(response.data.efecto);
            $('#id-causa-efecto').val(response.data.id);

            $(modal_problema).find(".modal-title").html("Editar Causa/Efecto");
            $(modal_problema).modal('show');
        }
    });
}

function editar_objetivo(e){
    var parametros = {'mostrar':'editar-medio-fin'};

    moduloResource.get(e,parametros,{
        _success:function(response){
            $('#medio').val(response.data.medio);
            $('#fin').val(response.data.fin);
            $('#id-medio-fin').val(response.data.id);
            
            $(modal_objetivo).find(".modal-title").html("Editar Medio/Fin");
            $(modal_objetivo).modal('show');
        }
    });
}

function ver_proyecto(e){}

function editar_indicador(e){
    var parametros = {'mostrar':'editar-indicador'};

    moduloResource.get(e,parametros,{
        _success:function(response){
            $('#tipo-indicador').val(response.data.claveTipoIndicador);
            $('#ambito-programa').val(response.data.claveAmbito);
            $('#descripcion-obj-programa').val(response.data.descripcionObjetivo);
            $('#verificacion-programa').val(response.data.mediosVerificacion);
            $('#supuestos-programa').val(response.data.supuestos);
            $('#descripcion-ind-programa').val(response.data.descripcionIndicador);
            $('#numerador-ind-programa').val(response.data.numerador);
            $('#denominador-ind-programa').val(response.data.denominador);
            $('#interpretacion-programa').val(response.data.interpretacion);
            $('#formula-programa').val(response.data.idFormula);
            $('#dimension-programa').val(response.data.idDimensionIndicador);
            $('#frecuencia-programa').val(response.data.idFrecuenciaIndicador);
            $('#tipo-ind-programa').val(response.data.idTipoIndicador);
            $('#unidad-medida-programa').val(response.data.idUnidadMedida);
            $('#lbl-meta-programa').text(parseFloat(response.data.metaIndicador).format(2));
            $('#meta-programa').val(response.data.metaIndicador);
            $('#trim1-programa').val(response.data.trim1);
            $('#trim2-programa').val(response.data.trim2);
            $('#trim3-programa').val(response.data.trim3);
            $('#trim4-programa').val(response.data.trim4);
            $('#numerador-programa').val(response.data.valorNumerador);
            $('#lbl-numerador-programa').text(parseFloat(response.data.valorNumerador).format(2));
            $('#denominador-programa').val(response.data.valorDenominador);
            $('#linea-base-programa').val(response.data.lineaBase);
            $('#anio-base-programa').val(response.data.anioBase);

            $('#id-indicador').val(response.data.id);

            $(form_indicador + ' .chosen-one').trigger('chosen:updated');
            $('#tipo-indicador').trigger('chosen:updated');

            if(comentariosIndicadores[response.data.claveTipoIndicador]){
                mostrar_comentarios(comentariosIndicadores[response.data.claveTipoIndicador]);
            }

            $(modal_indicador).find(".modal-title").html("Editar Indicador");
            $(modal_indicador).modal('show');
        }
    });
}

/****************************************************************** Funciones de Botones ********************************************************************/

$("#txt-anio-agregar").on("keydown", function (e) {
    if (e.keyCode === 13) {  //checks whether the pressed key is "Enter"
        agregar_anio_meta();
    }
});

$('#btn-agregar-anio').on('click',function(){
    agregar_anio_meta();
});

function agregar_anio_meta(){
    var anio = $('#txt-anio-agregar').val();

    if(anio && anio.length == 4){
        if(!$('#meta_row_'+anio).length){
            var lista_anio = $('#lista-meta-anios').val();
            lista_anio += anio+'|';
            $('#lista-meta-anios').val(lista_anio);
            
            var table_row = '<tr id="meta_row_'+anio+'">';
            table_row += '<td><div class="input-group"><span class="input-group-addon">'+anio+'</span><input type="hidden" id="meta-anio-'+anio+'" name="meta-anio-'+anio+'" value="'+anio+'"><span class="input-group-addon"></span></div></td>';
            table_row += '<td><input type="number" class="form-control" id="meta-numerador-'+anio+'" name="meta-numerador-'+anio+'"></td>';
            table_row += '<td><input type="number" class="form-control" id="meta-denominador-'+anio+'" name="meta-denominador-'+anio+'"></td>';
            table_row += '<td><input type="number" class="form-control" id="meta-indicador-'+anio+'" name="meta-indicador-'+anio+'"></td>';
            table_row += '<td><button type="button" class="btn btn-danger" onClick="eliminar_anio_meta('+anio+')"><span class="fa fa-trash"></span></button></td>';
            table_row += '</tr>';

            $('#tabla-anios-metas-indicadores tbody').append(table_row);
            $('#txt-anio-agregar').val('');
        }
    }
}

function eliminar_anio_meta(anio){
    var lista_anio = $('#lista-meta-anios').val();
    lista_anio = lista_anio.replace(anio+'|','');
    $('#lista-meta-anios').val(lista_anio);
    $('#meta_row_'+anio+'').remove();
}

$('#btn-estrategia-guardar').on('click',function(){
    Validation.cleanFormErrors(form_programa);
    var parametros = $(form_programa).serialize();
    parametros += '&guardar=estrategia';

    if($('#id').val()){
        moduloResource.put($('#id').val(),parametros,{
            _success: function(response){
                MessageManager.show({data:'Datos del programa almacenados con éxito',type:'OK',timer:4});
                if(response.data.responsables){
                    llenar_responsables(response.data.responsables);
                    $('#responsable').val('');
                    $('#responsable').change();
                    $('#responsable').trigger('chosen:updated');
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
        });
    }else{
        moduloResource.post(parametros,{
            _success: function(response){
                MessageManager.show({data:'Datos del programa almacenados con éxito',type:'OK',timer:4});
                
                window.location.href = SERVER_HOST+'/expediente/estrategia-institucional';
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

/*$('#btn-guardar-problema').on('click',function(){
    Validation.cleanFormErrors(form_problema);
    var parametros = $(form_problema).serialize();
    parametros += '&guardar=programa-problema-objetivo';

    if($('#id').val()){
        moduloResource.put($('#id').val(),parametros,{
            _success: function(response){
                MessageManager.show({data:'Datos del árbol del problema almacenados con éxito',type:'OK',timer:4});
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

$('#btn-guardar-causa-efecto').on('click',function(){
    Validation.cleanFormErrors(form_causa_efecto);
    var parametros = $(form_causa_efecto).serialize();
    parametros = parametros + '&guardar=causa-efecto&id-programa='+$('#id').val();

    if($('#id-causa-efecto').val()){
        moduloResource.put($('#id-causa-efecto').val(),parametros,{
            _success: function(response){
                MessageManager.show({data:'Causa y efecto almacenados con éxito',type:'OK',timer:4});
                problemasDatagrid.actualizar();
                $(modal_problema).modal('hide');
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
                MessageManager.show({data:'Causa y efecto almacenados con éxito',type:'OK',timer:4});
                problemasDatagrid.actualizar();
                $(modal_problema).modal('hide');
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

$('#btn-guardar-objetivo').on('click',function(){
    Validation.cleanFormErrors(form_objetivo);
    var parametros = $(form_objetivo).serialize();
    parametros += '&guardar=programa-problema-objetivo';

    if($('#id').val()){
        moduloResource.put($('#id').val(),parametros,{
            _success: function(response){
                MessageManager.show({data:'Datos del árbol del objetivo almacenados con éxito',type:'OK',timer:4});
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

$('#btn-agregar-proyecto').on('click',function(){
    var parametros = $(form_proyectos).serialize();
    parametros += '&guardar=asignar-proyectos';

    if($('#id').val()){
        moduloResource.put($('#id').val(),parametros,{
            _success: function(response){
                MessageManager.show({data:'Proyectos asginados correctamente',type:'OK',timer:4});
                proyectosDatagrid.actualizar();
                $(modal_proyecto).modal('hide');
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

$('#btn-guardar-medio-fin').on('click',function(){
    Validation.cleanFormErrors(form_medio_fin);
    var parametros = $(form_medio_fin).serialize();
    parametros += '&guardar=medio-fin&id-programa='+$('#id').val();

    if($('#id-medio-fin').val()){
        moduloResource.put($('#id-medio-fin').val(),parametros,{
            _success: function(response){
                MessageManager.show({data:'Medio y fin almacenados con éxito',type:'OK',timer:4});
                objetivosDatagrid.actualizar();
                $(modal_objetivo).modal('hide');
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
                MessageManager.show({data:'Medio y fin almacenados con éxito',type:'OK',timer:4});
                objetivosDatagrid.actualizar();
                $(modal_objetivo).modal('hide');
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

$('#btn-guardar-indicador').on('click',function(){
    $('#id-programa').val($('#id').val());

    Validation.cleanFormErrors(form_indicador);
    var parametros = $(form_indicador).serialize();
    parametros += '&guardar=indicador&tipo-indicador='+$('#tipo-indicador').val();

    if($('#id-indicador').val()){
        moduloResource.put($('#id-indicador').val(),parametros,{
            _success:function(response){
                MessageManager.show({data:'Indicador almacenado con éxito',type:'OK',timer:4});
                indicadoresDatagrid.actualizar();
                $(modal_indicador).modal('hide');
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
                MessageManager.show({data:'Indicador almacenado con éxito',type:'OK',timer:4});
                indicadoresDatagrid.actualizar();
                $(modal_indicador).modal('hide');
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
*/
$('#btn-enviar-estrategia').on('click',function(){
    if($('#id').val()){
        var parametros = 'guardar=validar-estrategia';

        Confirm.show({
            titulo:"Enviar estrategia a Validación",
            mensaje: "¿Estás seguro que deseas enviar esta estrategia institucional para su validación? <br><b>IMPORTANTE:</b> Mientras la estrategia institucional este en validación no se podra editar ningún elemento del mismo.",
            si: '<span class="fa fa-send"></span> Enviar',
            no: 'Cancelar',
            callback: function(){
                moduloResource.put($('#id').val(),parametros,{
                    _success: function(response){
                        MessageManager.show({data:response.data,type:'OK',timer:6});
                        bloquear_controles();
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
    }
});

$('#btn-estrategia-cancelar').on('click',function(){
    window.location.href = SERVER_HOST+'/expediente/estrategia-institucional';
});

/****************************************************************** Funciones de cambio de accion para borrar ********************************************************************/
/*function resetear_borrar(){
    $("#datagridProblemas .btn-delete-rows").unbind('click');
    $("#datagridProblemas .btn-delete-rows").on('click',function(e){
        e.preventDefault();
        var rows = [];
        var contador= 0;
        
        $('#datagridProblemas').find("tbody").find("input[type=checkbox]:checked").each(function () { rows.push($(this).parent().parent().data("id")); });

        contador = rows.length;

        if(contador>0){
            Confirm.show({
                    titulo:"Eliminar Causa/Efecto",
                    mensaje: "¿Estás seguro que deseas eliminar el(los) elemento(s) seleccionados(?)",
                    callback: function(){
                        moduloResource.delete(rows,{'rows':rows, 'eliminar':'causa-efecto', 'id-programa':$('#id').val()},{
                            _success: function(response){ 
                                problemasDatagrid.actualizar();
                                MessageManager.show({data:'Elemento(s) eliminado(s) con éxito.',timer:3});
                            },
                            _error: function(jqXHR){  MessageManager.show(jqXHR.responseJSON); }
                        });
                    }
            });
        }else{ MessageManager.show({data:'No has seleccionado ningún registro.',type:'ADV',timer:3}); }
    });

    $("#datagridObjetivos .btn-delete-rows").unbind('click');
    $("#datagridObjetivos .btn-delete-rows").on('click',function(e){
        e.preventDefault();
        var rows = [];
        var contador= 0;
        
        $('#datagridObjetivos').find("tbody").find("input[type=checkbox]:checked").each(function () { rows.push($(this).parent().parent().data("id")); });

        contador = rows.length;

        if(contador>0){
            Confirm.show({
                    titulo:"Eliminar Medio/Fin",
                    mensaje: "¿Estás seguro que deseas eliminar el(los) elemento(s) seleccionado(s)?",
                    callback: function(){
                        moduloResource.delete(rows,{'rows':rows, 'eliminar':'medio-fin', 'id-programa':$('#id').val()},{
                            _success: function(response){ 
                                objetivosDatagrid.actualizar();
                                MessageManager.show({data:'Elemento(s) eliminado(s) con éxito.',timer:3});
                            },
                            _error: function(jqXHR){  MessageManager.show(jqXHR.responseJSON); }
                        });
                    }
            });
        }else{ MessageManager.show({data:'No has seleccionado ningún registro.',type:'ADV',timer:3}); }
    });

    $("#datagridIndicadores .btn-delete-rows").unbind('click');
    $("#datagridIndicadores .btn-delete-rows").on('click',function(e){
        e.preventDefault();
        var rows = [];
        
        $('#datagridIndicadores').find("tbody").find("input[type=checkbox]:checked").each(function () { rows.push($(this).parent().parent().data("id")); });

        var contador = rows.length;

        if(contador>0){
            Confirm.show({
                    titulo:"Eliminar Indicador",
                    mensaje: "¿Estás seguro que deseas eliminar el(los) elemento(s) seleccionado(s)?",
                    callback: function(){
                        moduloResource.delete(rows,{'rows':rows, 'eliminar':'indicador', 'id-programa':$('#id').val()},{
                            _success: function(response){ 
                                indicadoresDatagrid.actualizar();
                                MessageManager.show({data:'Elemento(s) eliminado(s) con éxito.',timer:3});
                            },
                            _error: function(jqXHR){  MessageManager.show(jqXHR.responseJSON); }
                        });
                    }
            });
        }else{ MessageManager.show({data:'No has seleccionado ningún registro.',type:'ADV',timer:3}); }
    });

    $("#datagridProyectos .btn-delete-rows").unbind('click');
    $("#datagridProyectos .btn-delete-rows").on('click',function(e){
        e.preventDefault();
        var rows = [];
        $('#datagridProyectos').find("tbody").find("input[type=checkbox]:checked").each(function () { rows.push($(this).parent().parent().data("id")); });
        var contador = rows.length;

        if(contador>0){
            Confirm.show({
                    titulo:"Quitar Proyecto",
                    mensaje: "¿Estás seguro que deseas quitar el(los) proyecto(s) seleccionado(s)?",
                    callback: function(){
                        moduloResource.delete(rows,{'rows':rows, 'eliminar':'proyecto', 'id-programa':$('#id').val()},{
                            _success: function(response){ 
                                proyectosDatagrid.actualizar();
                                MessageManager.show({data:'Proyecto(s) removido(s) con éxito.',timer:3});
                            },
                            _error: function(jqXHR){  MessageManager.show(jqXHR.responseJSON); }
                        });
                    }
            });
        }else{ MessageManager.show({data:'No has seleccionado ningún registro.',type:'ADV',timer:3}); }
    });
}*/

function llenar_responsables(datos){
    var html = '<option value="">Selecciona un responsable</option>';
    for(var i in datos){
        var responsable = datos[i];
        html += '<option value="'+responsable.id+'" data-cargo="'+responsable.cargo+'">';
        html += responsable.nombre;
        html += '</option>';
    }
    $('#responsable').html(html);
}

/*function actualizar_lista_proyectos(response){
    proyectosDatagrid.limpiar();
    var datos_grid = [];
    for(var i in response.data){
        var item = {};

        item.id = response.data[i].id;
        item.clave = response.data[i].ClavePresupuestaria;
        item.nombre_tecnico = response.data[i].nombreTecnico;
        item.cobertura = response.data[i].coberturaDescripcion;
        item.usuario = response.data[i].username;

        datos_grid.push(item);
    }
    proyectosDatagrid.cargarDatos(datos_grid);
}*/

function sumar_trimestres(){
    var suma = 0;
    $('.valor-trimestre').each(function(){
        suma += parseFloat($(this).val()) || 0;
    });
    $('#valorNumerador').val(suma);
    //$('#lbl-numerador-programa').text(suma.format(2));
    ejecutar_formula();
}

function ejecutar_formula(){  
    
    var numerador = parseFloat($('#valorNumerador').val()) || 0;
    var denominador = parseFloat($('#valorDenominador').val()) || 1;
    var total;
    var id_formula = $('#formula').val();
    switch(id_formula){
        case '1':
            //(Numerador / Denominador) * 100
            total = (numerador/denominador)*100;
            break;
        case '2':
            //((Numerador / Denominador) - 1) * 100
            total = ((numerador/denominador)-1)*100;
            break;
        case '3':
            //(Numerador / Denominador)
            total = (numerador/denominador);
            break;
        case '4':
            //(Numerador - 1,000) / Denominador
            total = (numerador*1000)/denominador;
            break;
        case '5':
            //(Numerador / 10,000) / Denominador
            total = (numerador*10000)/denominador;
            break;
        case '6':
            //(Numerador / 100,000) / Denominador
            total = (numerador*100000)/denominador;
            break;
        case '7':
            //Indicador simple
            total = numerador;
            break;
        default:
            total = '';
            break;
    }
    $('#meta').val(total);
    /*if(total != ''){ //Esto no se para que es
        $('#lbl-meta-programa').text(total.format(2));
    }else{
        $('#lbl-meta-programa').text('');
    }*/
}
/*

function reset_modal_form(formulario){
    $(formulario).get(0).reset();
    $(formulario + ' input[type="hidden"]').val('');
    Validation.cleanFormErrors(formulario);
    $(formulario + ' .chosen-one').trigger('chosen:updated');

    if(formulario == form_indicador){
        $('#id-indicador').val('');
        Validation.cleanFieldErrors('tipo-indicador');
        $('#tipo-indicador').val('');
        $('#tipo-indicador').trigger('chosen:updated');
        $('#lbl-numerador-programa').text('');
        $('#lbl-meta-programa').text('');
        $(formulario+' .texto-comentario').remove();
        $(formulario+' .has-warning').removeClass('has-warning');
    }else if(formulario == form_causa_efecto){
        $('#id-causa-efecto').val('');
    }else if(formulario == form_medio_fin){
        $('#id-medio-fin').val('');
    }
}*/

function bloquear_controles(){
    $('input,textarea,select').each(function(){
        $(this).prop('disabled',true);
        $('label[for="' + $(this).attr('id') + '"]').prepend('<span class="fa fa-lock"></span> ');
        if($(this).hasClass('chosen-one')){
            $(this).trigger('chosen:updated');
        }
    });
}

function mostrar_comentarios(datos){
    for(var i in datos){
        var id_campo = datos[i].idCampo;
        var observacion = datos[i].observacion;
        if(datos[i].tipoComentario == 1){
            if(id_campo.indexOf("-F") > -1 || id_campo.indexOf("-P") > -1){
                if(id_campo.indexOf("-F") > -1){
                    var tipo_indicador = 'F';
                }else{
                    var tipo_indicador = 'P';
                }
                var index_tipo = id_campo.indexOf(tipo_indicador);
                var nuevo_id = id_campo.substring(0,index_tipo) + 'estrategia';

                if(!comentariosIndicadores[tipo_indicador]){
                    comentariosIndicadores[tipo_indicador] = [];
                }

                comentariosIndicadores[tipo_indicador].push({
                    'idCampo': nuevo_id,
                    'observacion': observacion,
                    'tipoComentario': 1
                });

            }else if(id_campo == 'arbolproblema' || id_campo == 'arbolobjetivo'){
                if(id_campo == 'arbolproblema'){
                    $('#datagridProblemas table').parent('.panel').addClass('has-warning');
                    $('#datagridProblemas table').before(' <p class="texto-comentario help-block"> <span class="fa fa-warning"></span> '+observacion+'</p>');
                }else{
                    $('#datagridObjetivos table').parent('.panel').addClass('has-warning');
                    $('#datagridObjetivos table').before(' <p class="texto-comentario help-block"> <span class="fa fa-warning"></span> '+observacion+'</p>');
                }
            }else{
                $('label[for="' + id_campo + '"]').prepend('<span class="fa fa-warning texto-comentario"></span> ');
                $('#'+id_campo).parent('.form-group').addClass('has-warning');
                $('#'+id_campo).parent('.form-group').append('<p class="texto-comentario has-warning help-block"> '+observacion+'</p>');
                //var texto_lbl = $('label[for="' + id_campo + '"]').text();
                //$('label[for="' + id_campo + '"]').html('<span class="programa-comentario" data-placement="auto top" data-toggle="popover" data-trigger="click" data-content="'+observacion+'">'+texto_lbl+'</span>');
                //
            }
            
        }
    }
    $('.estrategia-comentario').popover();
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