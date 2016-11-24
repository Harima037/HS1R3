/*=====================================

    # Nombre:
        fibapAcciones.js

    # Módulo:
        expediente/caratula-inversion

    # Descripción:
        Comportamiendo y funciones del formulario de la FIBAP para los datos relacionados a las acciones y su desglose de un proyecto
        de inversión

=====================================*/

var fibapAcciones = {};

(function(context){

var comentarios = { componentes:{}, actividades:{}, desgloses:{} };
var id_fibap;
var fibap_resource;
var accionesDatagrid;
//var actividadDatagrid;
var distribucionDatagrid;
var tabla_presupuesto_partida = '#tabla_presupuesto_partida';
var tabla_componente_partidas = '#tabla_accion_partidas';
var modal_accion = '#modal-accion'
var form_accion = '#form_accion';

//var modal_actividad = '#modal-actividad'
//var form_actividad = '#form_actividad';

var modal_presupuesto = '#modal-presupuesto';
var form_presupuesto = '#form-presupuesto';

var modal_subir_archivo = '#modal-subir-archivo';
var form_subir_archivo = '#form-subir-archivo';

//var presupuesto_total = 0;
//var presupuesto_origenes = [];

var ejecucion_fecha_inicio;
var ejecucion_fecha_fin;

context.cargar_jurisdicciones = function(datos){
    llenar_select_jurisdicciones(datos);
};

/*context.cargar_municipios = function(datos){
    //llenar_select_municipios(datos);
}*/

context.actualizar_total_presupuesto = function(presupuesto){
    $('#total-presupuesto-requerido').text('$ ' + parseFloat(presupuesto).format(2));
    $('#total-presupuesto-requerido').attr('data-valor',presupuesto);

    var distribuido = $('#total-presupuesto-distribuido').attr('data-valor');
    var porcentaje = (distribuido * 100) / parseFloat(presupuesto);

    actualiza_porcentaje('#porcentaje_completo',porcentaje);
}

context.init = function(id,resource){
    id_fibap = id;
    fibap_resource = resource;

    //Inicializacion de los DataGrids
    //actividadDatagrid = new Datagrid('#datagridActividades',fibap_resource);
    //actividadDatagrid.init();

    accionesDatagrid = new Datagrid("#datagridAcciones",fibap_resource);
    accionesDatagrid.init();

    llenar_datagrid_acciones([]);
    //llenar_datagrid_actividades([]);
    llenar_tabla_distribucion([]);

    $('#btn-agregar-distribucion').on('click',function(){
        $(modal_presupuesto).find(".modal-title").html("Nuevo Desglose");
        $(modal_presupuesto).modal('show');
    });

    $('#btn-mostrar-subir-archivo').on('click',function (e){
        e.preventDefault();
        $(modal_subir_archivo).modal('show');
    });

    $('#btn-subir-archivo').on('click',function(){
        //Validation.cleanFormErrors(form_subir_archivo);
        Validation.cleanFieldErrors('tipo-archivo');
        Validation.cleanFieldErrors('archivo');
        var cuantosArchivos = document.getElementById("archivo").files.length;
        if($('#tipo-archivo').val() == ''){
            Validation.printFieldsErrors('tipo-archivo','Debe seleccionar un tipo de archivo a subir.');
            return false;
        }
        if(cuantosArchivos>0){
            //var parametros = $(form_subir_archivo).serialize();
            //parametros += '&guardar=cargar-archivo-desglose&id-accion='+$('#id-accion').val()+'&id-fibap='+id_fibap;
            var data  = new FormData();
            var archivo = document.getElementById("archivo").files;
                
            $("#loading").fadeIn();
            data.append('datoscsv', archivo[0]); 
            data.append('tipo-archivo',$('#tipo-archivo').val());
            data.append('id-accion', $('#id-accion').val());
            data.append('id-fibap', id_fibap);
            data.append('id-proyecto',$('#id').val());
            data.append('guardar','cargar-archivo-desglose');
            data.append('nivel',$('#nivel-desglose').val());
            
            $.ajax({
                url: SERVER_HOST+'/v1/inversion', //Url a donde la enviaremos
                type:'POST', //Metodo que usaremos
                dataType:'json',
                contentType:false, //Debe estar en false para que pase el objeto sin procesar
                data:data, //Le pasamos el objeto que creamos con los archivos
                processData:false, //Debe estar en false para que JQuery no procese los datos a enviar
                cache:false, //Para que el formulario no guarde cache,
                success: function(response){ 
                    distribucionDatagrid.actualizar();
                    MessageManager.show({timer:10,data:response.data,type:'OK'});
                    if(response.extras){
                        if(response.extras.distribucion_total){
                            llenar_tabla_distribucion(response.extras.distribucion_total);
                        }
                    }
                },
                error: function( response ){
                    $("#loading").fadeOut(function(){ 
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
                    });
                }   
            });
        }else{
            Validation.printFieldsErrors('archivo','Debe seleccionar un archivo a subir.');
            //MessageManager.show({data:'Debe seleccionar un archivo a subir.',timer:10,type:'ERR'});
        }
    });
    /*
    $('#btn-agregar-actividad').on('click',function(){
        var actividades = $('#conteo-actividades').text().split('/');

        if(parseInt(actividades[0]) >= parseInt(actividades[1])){
            MessageManager.show({code:'S03',data:"Las actividades para este componente ya estan completas.",timer:2});
        }else{
            $(modal_actividad).find(".modal-title").html("Nueva Actividad");
            $(modal_actividad).modal('show');
        }
    });
    */
    $('#btn-agregar-accion').on('click',function(){
        $('#tablink-componente-actividades').attr('data-toggle','');
        $('#tablink-componente-actividades').parent().addClass('disabled');
        $('#lista-tabs-componente a:first').tab('show');
        $(modal_accion).find(".modal-title").html("Nueva Acción");
        $(modal_accion).modal('show');
    });
    
    $('#nivel-accion').on('change',function(){
        if($(this).val() == 'actividad'){
            $('#seleccion-componente').removeClass('hidden');
            $('#entregable').prop('disabled',true);
            $('#tipo-entregable').prop('disabled',true);
            $('#accion-entregable').prop('disabled',true);
        }else{
            $('#seleccion-componente').addClass('hidden');
            $('#entregable').prop('disabled',false);
            $('#tipo-entregable').prop('disabled',false);
            $('#accion-entregable').prop('disabled',false);
        }
        $('#entregable').trigger('chosen:updated');
        $('#tipo-entregable').trigger('chosen:updated');
        $('#accion-entregable').trigger('chosen:updated');
    });

    $('#btn-agregar-partida').on('click',function(){
        var seleccionado = $('#objeto-gasto-presupuesto option:selected');
        if(seleccionado != ''){
            var arreglo_datos_partida = seleccionado.text().split(' - ');
            var datos_partida = {
                id: seleccionado.val(),
                clave: arreglo_datos_partida[0].trim(),
                descripcion: arreglo_datos_partida[1].trim()
            }
            if($(tabla_componente_partidas + ' > table > tbody > tr[data-partida-id="'+datos_partida.id+'"]').length == 0){
                var linea = linea_tabla_partidas(datos_partida);
                $(tabla_componente_partidas + ' > table > tbody').append(linea);
                $(tabla_componente_partidas).append('<input type="hidden" class="ocultos-partidas-componente" value="'+datos_partida.id+'" name="objeto-gasto-presupuesto[]" id="objeto-gasto-'+datos_partida.id+'">');
            }
        }
    });
    $('.meta-mes').on('keyup',function(){ $(this).change(); });
    $('.meta-mes').on('change',function(){
        var mes = $(this).data('meta-mes');
        var trimestre = Math.ceil(mes/3);

        var suma = 0;

        if(trimestre == 1){
            var i = 1;
            var j = 3;
        }else if(trimestre == 2){
            var i = 4;
            var j = 6;
        }else if(trimestre == 3){
            var i = 7;
            var j = 9;
        }else{
            var i = 10;
            var j = 12;
        }
        for(i; i <= j ; i++){
            suma += parseFloat($('.meta-mes[data-meta-mes="' + i + '"]').val()) || 0;
        }
        $('#trim'+trimestre).val(suma);
        $('#trim'+trimestre+'-lbl').text(suma.format(2));
        
        suma = 0;
        for(var i = 1 ; i <= 4 ; i++){
            suma += parseFloat($('#trim'+i).val()) || 0;
        }
        $('#cantidad-meta').val(suma);
        $('#cantidad-meta-lbl').text(suma.format(2));
    });
    actualizar_eventos_metas();

    $('.accion-origen-financiamiento').on('change',function(){
        var sumatoria = 0;
        $('.accion-origen-financiamiento').each(function(){
            sumatoria += parseFloat($(this).val()) || 0;
        });
        $('#accion-presupuesto-requerido').val(sumatoria);
        $('#accion-presupuesto-requerido-lbl').text('$ ' + sumatoria.format(2));
    });

    $('.accion-origen-financiamiento').on('keyup',function(){ $(this).change(); });

    $('#entregable').on('change',function(){
        var selects = [
            {id:'#tipo-entregable',por_defecto:'NA'},
            {id:'#accion-entregable',por_defecto:false}
        ];

        var habilitar_id = $(this).val();

        for(var indx in selects){
            var selector = selects[indx].id;
            var default_id = selects[indx].por_defecto;
            
            var suma = $(selector + ' option[data-habilita-id="' + habilitar_id + '"]').length;

            $(selector + ' option[data-habilita-id]').attr('disabled',true).addClass('hidden');
            $(selector + ' option[data-habilita-id="' + habilitar_id + '"]').attr('disabled',false).removeClass('hidden');

            if(suma == 0 && default_id){
                $(selector + ' option[data-habilita-id="' + default_id + '"]').attr('disabled',false).removeClass('hidden');
            }

            $(selector).val('');
            $(selector).change();

            if($(selector).hasClass('chosen-one')){
                $(selector).trigger("chosen:updated");
            }
        }
    });

    $('#formula-componente').on('change',function(){ ejecutar_formula('componente'); });
    $('#denominador-componente').on('keyup',function(){ $(this).change(); });
    $('#denominador-componente').on('change',function(){ ejecutar_formula('componente'); });

    $('#formula-actividad').on('change',function(){ ejecutar_formula('actividad'); });
    $('#denominador-actividad').on('keyup',function(){ $(this).change(); });
    $('#denominador-actividad').on('change',function(){ ejecutar_formula('actividad'); });

    $('#jurisdiccion-accion').off('change');
    $('#jurisdiccion-accion').on('change',function(){
        if($('#jurisdiccion-accion').val() == '' || $('#jurisdiccion-accion').val() == 'OC' ){
            $('#municipio-accion').html('<option value="">Selecciona un Municipio</option>');
            $('#localidad-accion').html('<option value="">Selecciona un Localidad</option>');
            $('#municipio-accion').trigger("chosen:updated");
            $('#localidad-accion').trigger("chosen:updated");
        }else{
            var parametros = {
                'listar':'municipios',
                'id-proyecto':$('#id').val(),
                'jurisdiccion': $('#jurisdiccion-accion').val()
            };

            fibap_resource.get(null,parametros,{
                _success: function(response){
                    var html_options = '<option value="">Selecciona un Municipio</option>';
                    for(var i in response.data){
                        var municipio = response.data[i];
                        html_options += '<option value="'+municipio.clave+'">'+municipio.nombre+'</option>';
                    }
                    $('#municipio-accion').html(html_options);
                    if($('#municipio-accion').hasClass('chosen-one')){
                        $('#municipio-accion').trigger("chosen:updated");
                    }
                }
            });
        }
    });
    
    $('#municipio-accion').off('change');
    $('#municipio-accion').on('change',function(){
        if($('#municipio-accion').val() == ''){
            $('#localidad-accion').html('<option value="">Selecciona una Localidad</option>');
            $('#localidad-accion').trigger("chosen:updated");
        }else{
            var parametros = {
                'listar':'localidades',
                'municipio': $('#municipio-accion').val()
            };

            fibap_resource.get(null,parametros,{
                _success: function(response){
                    var html_options = '<option value="">Selecciona una Localidad</option>';
                    for(var i in response.data){
                        var localidad = response.data[i];
                        html_options += '<option value="'+localidad.clave+'">'+localidad.clave+' '+localidad.nombre+'</option>';
                    }
                    $('#localidad-accion').html(html_options);
                    if($('#localidad-accion').hasClass('chosen-one')){
                        $('#localidad-accion').trigger("chosen:updated");
                    }
                }
            });
        }
    });
    /*
    $(modal_accion + ' #lista-tabs-componente').on('show.bs.tab',function(event){
        var id = event.target.id;
        if(id == 'tablink-componente-actividades'){
            $('.btn-grupo-guardar').hide();
        }else{
            $('.btn-grupo-guardar').show();
        }
    });
    */
    $(modal_accion).on('hide.bs.modal',function(e){ reset_modal_form(form_accion); });
    $(modal_subir_archivo).on('hide.bs.modal',function(e){ reset_modal_form(form_subir_archivo); });
    
    $(modal_presupuesto).on('hide.bs.modal',function(e){
        $(modal_presupuesto).find(".modal-title").html("Nuevo");
        reset_modal_form(form_presupuesto);
    });

    //$(modal_actividad).on('hide.bs.modal',function(e){ reset_modal_form(form_actividad); });
    /*
    $("#datagridActividades .btn-delete-rows").unbind('click');
    $("#datagridActividades .btn-delete-rows").on('click',function(e){
        e.preventDefault();
        var rows = [];
        var contador= 0;
        
        $("#datagridActividades").find("tbody").find("input[type=checkbox]:checked").each(function () {
            contador++;
            rows.push($(this).parent().parent().data("id"));
        });

        if(contador>0){
            Confirm.show({
                    titulo:"Eliminar actividad",
                    //botones:[], 
                    mensaje: "¿Estás seguro que deseas eliminar la(s) actividad(es) seleccionadas?",
                    //si: 'Actualizar',
                    //no: 'No, gracias',
                    callback: function(){
                        proyectoResource.delete(rows,{'rows': rows, 'eliminar': 'actividad', 'id-componente': $('#id-componente').val(), 'id-proyecto': $('#id').val()},{
                            _success: function(response){ 
                                llenar_datagrid_actividades(response.actividades);
                                MessageManager.show({data:'Actividad eliminada con éxito.',timer:3});
                            },
                            _error: function(jqXHR){ 
                                MessageManager.show(jqXHR.responseJSON);
                            }
                        });
                    }
            });
        }else{
            MessageManager.show({data:'No has seleccionado ningún registro.',type:'ADV',timer:3});
        }
    });
    */
    
    $("#datagridAcciones .btn-delete-rows").unbind('click');
    $("#datagridAcciones .btn-delete-rows").on('click',function(e){
        e.preventDefault();
        var rows = [];
        var contador= 0;
        $("#datagridAcciones").find("tbody").find("input[type=checkbox]:checked").each(function () {
            contador++;
            rows.push($(this).parent().parent().data("id"));
        });
        if(contador>0){
            ocultar_detalles(true);
            Confirm.show({
                    titulo:"Eliminar Acción",
                    mensaje: "¿Estás seguro que deseas eliminar la(s) acción(es) seleccionada(s)?",
                    callback: function(){
                        fibap_resource.delete(rows,{'rows': rows, 'eliminar': 'accion', 'id-fibap': $('#id-fibap').val(), 'id-proyecto': $('#id').val()},{
                            _success: function(response){
                                llenar_datagrid_acciones(response.acciones);
                                llenar_tabla_distribucion(response.distribucion_total);
                                MessageManager.show({data:'Acción(es) eliminada(s) con éxito.',timer:3});
                            },
                            _error: function(jqXHR){  MessageManager.show(jqXHR.responseJSON); }
                        });
                    }
            });
        }else{ MessageManager.show({data:'No has seleccionado ningún registro.',type:'ADV',timer:3}); }
    });
};

context.mostrar_datos_presupuesto = function(datos){
    $(modal_presupuesto).find(".modal-title").html("Editar Desglose");
    //$('#jurisdiccion-accion').val(datos.calendarizado[0].claveJurisdiccion);
    //$('#jurisdiccion-accion').trigger('chosen:updated');

    llenar_select_municipios(datos.municipios);

    var desglose = datos.desglose;
    $('#jurisdiccion-accion').val(desglose.claveJurisdiccion);
    $('#jurisdiccion-accion').trigger('chosen:updated');
    if(desglose.claveMunicipio && desglose.claveJurisdiccion != 'OC'){
        $('#municipio-accion').val(desglose.claveMunicipio);
        $('#municipio-accion').trigger('chosen:updated');

        $('#localidad-accion').val(desglose.claveLocalidad);
        $('#localidad-accion').trigger('chosen:updated');
    }
    
    for(var indx in desglose.metas_mes){
        var meta = desglose.metas_mes[indx];
        $('#meta-mes-'+meta.mes).val(meta.meta);
        $('#meta-mes-'+meta.mes).attr('data-meta-id',meta.id);
        $('#meta-mes-'+meta.mes).change();
    }

    for(var indx in desglose.beneficiarios){
        var beneficiario = desglose.beneficiarios[indx];
        $('#beneficiarios-'+beneficiario.idTipoBeneficiario+'-f').val(beneficiario.totalF);
        $('#beneficiarios-'+beneficiario.idTipoBeneficiario+'-m').val(beneficiario.totalM).change();
    }

    var calendarizacion = datos.calendarizado;
    for(var indx in calendarizacion){
        $('#mes-'+calendarizacion[indx].mes+'-'+calendarizacion[indx].idObjetoGasto).val(calendarizacion[indx].cantidad);
        $('#mes-'+calendarizacion[indx].mes+'-'+calendarizacion[indx].idObjetoGasto).attr('data-presupuesto-id',calendarizacion[indx].id);
    }

    $('#id-desglose').val(desglose.id);

    $('.presupuesto-mes').first().change();
    $('.meta-mes').first().change();

    if(datos.idComponente){
        if(comentarios.desgloses.componentes[desglose.id]){
            mostrar_comentario(comentarios.desgloses.componentes[desglose.id]);
        }
    }else if(datos.idActividad){
        if(comentarios.desgloses.actividades[desglose.id]){
            mostrar_comentario(comentarios.desgloses.actividades[desglose.id]);
        }
    }

    $(modal_presupuesto).modal('show');
};
/*
context.mostrar_datos_actividad = function(datos){
    $(modal_actividad).find(".modal-title").html('Editar Actividad');

    $('#descripcion-obj-actividad').val(datos.objetivo);
    $('#verificacion-actividad').val(datos.mediosVerificacion);
    $('#supuestos-actividad').val(datos.supuestos);
    $('#descripcion-ind-actividad').val(datos.indicador);
    $('#numerador-ind-actividad').val(datos.numerador);
    $('#denominador-ind-actividad').val(datos.denominador);
    $('#interpretacion-actividad').val(datos.interpretacion);
    $('#denominador-actividad').val(datos.valorDenominador).change();
    $('#linea-base-actividad').val(datos.lineaBase);
    $('#anio-base-actividad').val(datos.anioBase);
    $('#formula-actividad').val(datos.idFormula);
    $('#dimension-actividad').val(datos.idDimensionIndicador);
    $('#frecuencia-actividad').val(datos.idFrecuenciaIndicador);
    $('#tipo-ind-actividad').val(datos.idTipoIndicador);
    $('#unidad-medida-actividad').val(datos.idUnidadMedida);

    $(form_actividad + ' .chosen-one').trigger('chosen:updated');

    $('#id-actividad').val(datos.id);

    if(datos.metas_mes){
        var suma = 0;
        var suma_trimestre = {'1':0,'2':0,'3':0,'4':0};
        var trimestre = 0;
        for(var i in datos.metas_mes){
            var meta = datos.metas_mes[i];
            var meta_valor = parseFloat(meta.meta);
            trimestre = Math.ceil(meta.mes/3);

            suma_trimestre[trimestre] += meta_valor;
            suma += meta_valor;
            $('#mes-actividad-' + meta.claveJurisdiccion + '-' + meta.mes).val(meta_valor);
            $('#mes-actividad-' + meta.claveJurisdiccion + '-' + meta.mes).attr('data-meta-id',meta.id);
        }
        for(var i in suma_trimestre){
            $('#trim'+i+'-actividad').val(suma_trimestre[i]);
            $('#trim'+i+'-actividad-lbl').text(suma_trimestre[i]);
        }
        $('#numerador-actividad').val(suma);
        $('#numerador-actividad-lbl').text(suma.format(2));
        ejecutar_formula('actividad');
    }

    if(comentarios.actividades[datos.id]){
        mostrar_comentarios(comentarios.actividades[datos.id]);
    }

    $(modal_actividad).modal('show');
}
*/
context.mostrar_datos = function(datos){
    var elemento;
    
    ocultar_detalles(true);

    $(modal_accion).find(".modal-title").html('Editar Acción');
    
    if(datos.componente){
        elemento = datos.componente;
        $('#id-componente').val(datos.componente.id);
        $('#nivel-accion').val('componente').change();
    }else{
        elemento = datos.actividad;
        $('#id-actividad').val(datos.actividad.id);
        //$('#id-componente').val(datos.actividad.idComponente);
        $('#nivel-accion').val('actividad').change();
        $('#componente-seleccionado').val(elemento.idComponente);
    }
    $('#nivel-accion').prop('disabled',true);
    
    $('#descripcion-obj-accion').val(elemento.objetivo);
    $('#verificacion-accion').val(elemento.mediosVerificacion);
    $('#supuestos-accion').val(elemento.supuestos);
    $('#descripcion-ind-accion').val(elemento.indicador);
    $('#numerador-ind-accion').val(elemento.numerador);
    $('#denominador-ind-accion').val(elemento.denominador);
    $('#interpretacion-accion').val(elemento.interpretacion);
    $('#denominador-accion').val(elemento.valorDenominador).change();
    $('#linea-base-accion').val(elemento.lineaBase);
    $('#anio-base-accion').val(elemento.anioBase);
    $('#formula-accion').val(elemento.idFormula);
    $('#dimension-accion').val(elemento.idDimensionIndicador);
    $('#frecuencia-accion').val(elemento.idFrecuenciaIndicador);
    $('#tipo-ind-accion').val(elemento.idTipoIndicador);
    $('#unidad-medida-accion').val(elemento.idUnidadMedida);
    
    if(elemento.idEntregable){
        $('#entregable').val(elemento.idEntregable);
        $('#entregable').chosen().change();
        $('#tipo-entregable').val(elemento.idEntregableTipo || 'NA');
    }
    if(elemento.idEntregableAccion){
        $('#accion-entregable').val(elemento.idEntregableAccion);
    }

    $(form_accion + ' .chosen-one').trigger('chosen:updated');

    $('#id-accion').val(datos.id);
    
    //$('#tablink-componente-actividades').attr('data-toggle','tab');
    //$('#tablink-componente-actividades').parent().removeClass('disabled');

    $('#accion-presupuesto-requerido').val(datos.presupuestoRequerido);
    $('#accion-presupuesto-requerido-lbl').text('$ ' + parseFloat(datos.presupuestoRequerido).format(2));
    for(var indx in datos.propuestas_financiamiento){
        var origen = datos.propuestas_financiamiento[indx];
        $('#accion-origen-'+origen.idOrigenFinanciamiento).val(origen.cantidad);
        $('#accion-origen-'+origen.idOrigenFinanciamiento).attr('data-captura-id',origen.id);
    }
    
    llenar_tabla_componente_partidas(datos.partidas);
    //llenar_datagrid_actividades(datos.componente.actividades);

    if(elemento.metas_mes){
        var suma = 0;
        var suma_trimestre = {'1':0,'2':0,'3':0,'4':0};
        var trimestre = 0;
        for(var i in elemento.metas_mes){
            var meta = elemento.metas_mes[i];

            trimestre = Math.ceil(meta.mes/3);
            suma_trimestre[trimestre] += parseFloat(meta.meta);
            suma += parseFloat(meta.meta);
            $('#mes-accion-' + meta.claveJurisdiccion + '-' + meta.mes).val(parseFloat(meta.meta));
            $('#mes-accion-' + meta.claveJurisdiccion + '-' + meta.mes).attr('data-meta-id',meta.id);
        }
        for(var i in suma_trimestre){
            $('#trim'+i+'-accion').val(suma_trimestre[i]);
            $('#trim'+i+'-accion-lbl').text(suma_trimestre[i].format(2));
        }
        $('#numerador-accion').val(suma);
        $('#numerador-accion-lbl').text(suma);
        ejecutar_formula('accion');
    }

    //actualizar_metas_ids(datos.componente.metas_mes);
    if(datos.componente){
        if(comentarios.componentes[datos.componente.id]){
            mostrar_comentarios(comentarios.componentes[datos.componente.id]);
        }
    }else{
        if(comentarios.actividades[datos.actividad.id]){
            mostrar_comentarios(comentarios.actividades[datos.actividad.id]);
        }
    }
    
    //

    $(modal_accion).modal('show');

    //actualizar_metas('componente',response.data.metas_mes);
    //var tab_id = cargar_formulario_componente_actividad('componente',response.data);
    //$(tab_id).tab('show');

    //actualizar_grid_actividades(response.data.actividades);
}

context.quitar_partida_componente = function(id){
    $(tabla_componente_partidas + ' > table > tbody > tr[data-partida-id="'+id+'"]').remove();
    $('#objeto-gasto-' + id).remove();
};

context.actualizar_metas_mes = function(jurisdicciones){
    actualizar_tabla_metas_mes('accion',jurisdicciones);
    //actualizar_tabla_metas_mes('actividad',jurisdicciones);
    if(ejecucion_fecha_inicio){
        //habilitar_meses_metas('actividad', ejecucion_fecha_inicio, ejecucion_fecha_fin);
        $(form_accion + ' .metas-mes').prop('disabled',true);
    }
};

context.mostrarComentarios = function(datos){
    comentarios = datos;
};

context.llenar_datagrid = function(datos){
    llenar_datagrid_acciones(datos);
};
/*
context.llenar_datagrid_actividades = function(datos){
    llenar_datagrid_actividades(datos);
};
*/
context.actualizar_metas_ids = function(identificador,metas){
    var indx;
    for(indx in metas){
        $('#mes-'+identificador+'-'+metas[indx].claveJurisdiccion+'-'+metas[indx].mes).val(metas[indx].meta);
        $('#mes-'+identificador+'-'+metas[indx].claveJurisdiccion+'-'+metas[indx].mes).attr('data-meta-id',metas[indx].id);
    }
    $('.metas-mes[data-meta-jurisdiccion="OC"][data-meta-identificador="'+identificador+'"]').change();
}

context.mostrar_detalles = function(id){
    ocultar_detalles(false);

    if($('#datagrid-contenedor-' + id).length){
        $('#datagridAcciones > table > tbody > tr.contendor-desechable').remove();
        llenar_datagrid_distribucion(0,0,null);
    }else{
        var parametros = {'mostrar':'desglose-accion'};
        fibap_resource.get(id,parametros,{
            _success:function(response){
                var nivel = '';
                if(response.data.idComponente){
                    nivel = 'componente';
                    $('#nivel-desglose').val('componente');
                    $('#indicador_texto').text(response.data.datos_componente_detalle.indicador);
                    $('#unidad_medida_texto').text(response.data.datos_componente_detalle.unidadMedida);
                }else{
                    nivel = 'actividad';
                    $('#nivel-desglose').val('actividad');
                    $('#indicador_texto').text(response.data.datos_actividad_detalle.indicador);
                    $('#unidad_medida_texto').text(response.data.datos_actividad_detalle.unidadMedida);
                }
                $('#id-accion').val(id);
                
                $('#datagridAcciones > table > tbody > tr.contendor-desechable').remove();
                llenar_datagrid_distribucion(response.data.id,response.data.presupuestoRequerido,nivel);
                
                $('#lnk-descarga-archivo-metas').attr('href',SERVER_HOST+'/expediente/descargar-archivo-municipios/'+$('#id').val()+'?tipo-carga=meta');
                $('#lnk-descarga-archivo-presupuesto').attr('href',SERVER_HOST+'/expediente/descargar-archivo-municipios/'+$('#id').val()+'?tipo-carga=presupuesto&id-accion='+id);
                $('#lnk-descarga-archivo-beneficiarios').attr('href',SERVER_HOST+'/expediente/descargar-archivo-municipios/'+$('#id').val()+'?tipo-carga=beneficiarios');
                
                $('#datagridAcciones > table > tbody > tr[data-id="' +  id+ '"]').addClass('bg-info');
                $('#datagridAcciones > table > tbody > tr[data-id="' +  id+ '"]').addClass('text-primary');
                $('#datagridAcciones > table > tbody > tr[data-id="' +  id+ '"] > td > span.boton-detalle > span.fa-plus-square-o').addClass('fa-minus-square-o');
                $('#datagridAcciones > table > tbody > tr[data-id="' +  id+ '"] > td > span.boton-detalle > span.fa-plus-square-o').removeClass('fa-plus-square-o');
                
                $('#datagridAcciones > table > tbody > tr[data-id="' +  id+ '"]').after('<tr class="contendor-desechable disabled"><td class="disabled bg-info" colspan="6" id="datagrid-contenedor-' + id + '"></td></tr>');
                $('#datagridDistribucion').appendTo('#datagrid-contenedor-' + id);
                $('#datagridDistribucion').attr('data-selected-id',id);
                
                actualizar_claves_presupuesto(response.data.partidas);
            }
        });
    }
};

context.llenar_datagrid_distribucion = function(id_accion,total_presupuesto,nivel){
    llenar_datagrid_distribucion(id_accion,total_presupuesto,nivel);
};

context.llenar_tabla_distribucion_general = function(datos){
    llenar_tabla_distribucion(datos);
};

context.actualizar_lista_beneficiarios = function(datos){
    var beneficiarios = [];
    var beneficiario;
    for(var indx in datos){
        if(beneficiarios[datos[indx].idTipoBeneficiario]){
            beneficiario = beneficiarios[datos[indx].idTipoBeneficiario];
        }else{
            beneficiario = {};
            beneficiario.id = datos[indx].idTipoBeneficiario;
            beneficiario.tipoBeneficiario = datos[indx].tipo_beneficiario.descripcion;
            beneficiario.totalF = 0;
            beneficiario.totalM = 0;
        }

        if(datos[indx].sexo == 'f'){
            beneficiario.totalF = datos[indx].total;
        }else{
            beneficiario.totalM = datos[indx].total;
        }
        
        beneficiarios[datos[indx].idTipoBeneficiario] = beneficiario;
    }
    $('#tabla_beneficiarios > tbody').empty();
    var html = '';
    for(var i in beneficiarios){
        html += '<tr>';
        html += '<td>' + beneficiarios[i].tipoBeneficiario + '</td>';
        html += '<td><div class="form-group"><input type="number" min="0" class="form-control benef-totales-accion" name="beneficiarios[' + beneficiarios[i].id + '][f]" id="beneficiarios-' + beneficiarios[i].id + '-f" data-tipo-beneficiario="' + beneficiarios[i].id + '"></div></td>';
        html += '<td><div class="form-group"><input type="number" min="0" class="form-control benef-totales-accion" name="beneficiarios[' + beneficiarios[i].id + '][m]" id="beneficiarios-' + beneficiarios[i].id + '-m" data-tipo-beneficiario="' + beneficiarios[i].id + '"></div></td>';
        html += '<td><span id="beneficiarios-' + beneficiarios[i].id + '-total">0</span></td>';
        html += '</tr>';
    }
    $('#tabla_beneficiarios > tbody').html(html);

    $('.benef-totales-accion').on('keyup',function(){
        $(this).change();
    });

    $('.benef-totales-accion').on('change',function(){
        var id_beneficiario = $(this).attr('data-tipo-beneficiario');
        var suma = 0;
        var suma_general = 0;
        $('.benef-totales-accion').each(function(){
            if($(this).attr('data-tipo-beneficiario') == id_beneficiario){
                suma += parseInt($(this).val()) || 0;
            }
            suma_general += parseInt($(this).val()) || 0;
        });
        
        $('#beneficiarios-' + id_beneficiario + '-total').text(suma.format());
        $('#total-beneficiarios').val(suma_general);
        $('#total-beneficiarios-lbl').text(suma_general.format());
    });    
};

context.habilitar_meses = function(fechaInicio,fechaFinal){
    habilitar_meses_captura(fechaInicio, fechaFinal);
};

context.init_responsables = function(){
    $('#responsable').on('change',function(){
        if($(this).val()){
            var cargo = $('#responsable option:selected').attr('data-cargo');
            $('#ayuda-responsable').text(cargo);
        }else{
            $('#ayuda-responsable').text('');
        }
    });
}

context.mostrar_datos_informacion = function(datos){
    $('#fuente-informacion').val(datos.fuenteInformacion);
    $('#responsable').val(datos.idResponsable);
    $('#responsable').change();
    $('#responsable').trigger('chosen:updated');
};

context.llenar_select_responsables = function(datos){
    var html = '<option value="">Selecciona un responsable</option>';
    for(var i in datos){
        var responsable = datos[i];
        html += '<option value="'+responsable.id+'" data-cargo="'+responsable.cargo+'">';
        html += responsable.nombre;
        html += '</option>';
    }
    $('#responsable').html(html);
    $('#responsable').val('');
    $('#responsable').change();
    if($('#responsable').hasClass('chosen-one')){
        $('#responsable').trigger('chosen:updated');
    }
}
/***********************************************************************************************
                    Funciones Privadas
************************************************************************************************/
function llenar_datagrid_distribucion(id_accion,total_presupuesto,nivel){
    $('#datagridDistribucion > table > tbody').html('<tr><td colspan="5" style="text-align:left"><i class="fa fa-spin fa-spinner"></i> Cargando datos...</td></tr>');
    $('#total-grid-presupuesto').text(0);
    actualiza_porcentaje('#porcentaje_accion',0);
    if(id_accion == 0){
        $("#datagridDistribucion .txt-quick-search").val('');
        actualiza_porcentaje('#porcentaje_accion',0);
        $('#datagridDistribucion > table > tbody').html('<tr><td colspan="5" style="text-align:left"><i class="fa fa-info-circle"></i> No hay datos</td></tr>');
        distribucionDatagrid.paginacion(1);
        distribucionDatagrid.setPagina(1);
    }else{
        distribucionDatagrid = new Datagrid("#datagridDistribucion",fibap_resource,{ desglosegrid:true, pagina: 1, idAccion: id_accion, nivel: nivel}); 
        distribucionDatagrid.init();
        distribucionDatagrid.cargarTotalResultados(0);
        distribucionDatagrid.actualizar({ 
            _success: function(response){ 
                var distribucion = [];
                $('#datagridDistribucion > table > tbody').empty();
                var total_porcentaje = 0;
                var total_desglose = parseFloat(response.total_presupuesto) || 0;
                var datos = response.data;
                
                for(var indx in datos){
                    var presupuesto = {};

                    presupuesto.id = datos[indx].id;

                    if(datos[indx].claveJurisdiccion != 'OC'){
                        presupuesto.localidad = datos[indx].claveLocalidad + ' - ' + datos[indx].localidad;
                        presupuesto.municipio = datos[indx].municipio;
                        presupuesto.jurisdiccion = datos[indx].jurisdiccion;
                    }else{
                        presupuesto.localidad = 'OFICINA CENTRAL';
                        presupuesto.municipio = 'OFICINA CENTRAL';
                        presupuesto.jurisdiccion = 'OFICINA CENTRAL';
                    }
                    
                    presupuesto.monto = '$ <span class="pull-right">' + parseFloat(datos[indx].presupuesto || 0).format(2) + '</span>';

                    if(datos[indx].idComponente){
                        if(comentarios.desgloses.componentes[presupuesto.id]){
                            presupuesto.localidad = '<span class="text-warning fa fa-warning"></span> ' + presupuesto.localidad;
                        }
                    }else if(datos[indx].idActividad){
                        if(comentarios.desgloses.actividades[presupuesto.id]){
                            presupuesto.localidad = '<span class="text-warning fa fa-warning"></span> ' + presupuesto.localidad;
                        }
                    }
                    
                    distribucion.push(presupuesto);
                }

                total_porcentaje = (total_desglose * 100) / (parseFloat(total_presupuesto) || 1);

                if(distribucion.length == 0){
                    actualiza_porcentaje('#porcentaje_accion',0);
                }else{
                    actualiza_porcentaje('#porcentaje_accion',total_porcentaje);
                }
                distribucionDatagrid.cargarDatos(distribucion);
                distribucionDatagrid.cargarTotalResultados(response.resultados);
                $('#total-grid-presupuesto').text(total_desglose.format(2));
                var total = parseInt(response.resultados/distribucionDatagrid.rxpag); 
                var plus = parseInt(response.resultados)%distribucionDatagrid.rxpag;
                if(plus>0) 
                    total++;
                distribucionDatagrid.paginacion(total);
            }
        });
        
        $("#datagridDistribucion .btn-delete-rows").unbind('click');
        $("#datagridDistribucion .btn-delete-rows").on('click',function(e){
            e.preventDefault();
            var rows = [];
            var contador= 0;
            $("#datagridDistribucion").find("tbody").find("input[type=checkbox]:checked").each(function () {
                contador++;
                rows.push($(this).parent().parent().data("id"));
            });
            if(contador>0){
                var id_accion = $('#datagridDistribucion').attr('data-selected-id');
                Confirm.show({
                        titulo:"Eliminar presupuesto",
                        mensaje: "¿Estás seguro que deseas eliminar los presupuestos seleccionados?",
                        callback: function(){
                            fibap_resource.delete(rows,{'rows': rows, 'eliminar': 'desglose-presupuesto', 'id-accion': id_accion, 'id-proyecto': $('#id').val(), 'nivel':$('#nivel-desglose').val() },{
                                _success: function(response){
                                    distribucionDatagrid.actualizar();
                                    llenar_tabla_distribucion(response.distribucion_total);
                                    MessageManager.show({data:'Presupuesto(s) eliminado(s) con éxito.',timer:3});
                                },
                                _error: function(jqXHR){  MessageManager.show(jqXHR.responseJSON); }
                            });
                        }
                });
            }else{ MessageManager.show({data:'No has seleccionado ningún registro.',type:'ADV',timer:3}); }
        });
    }
}

function llenar_datagrid_acciones(datos){
    $('#datagridAcciones > table > tbody').empty();
    var acciones = [];
    var sumas_origenes = [];
    var listado_componentes = '';
    
    var componentes = {};
    
    for(var indx in datos){
        var accion = {};

        var presupuesto = parseFloat(datos[indx].presupuestoRequerido);

        accion.id = datos[indx].id;
        if(datos[indx].idComponente){
            accion.nivel = 'C';//+contador_componentes;
            accion.indicador = datos[indx].datos_componente_detalle.indicador;
            accion.unidadMedida = datos[indx].datos_componente_detalle.unidadMedida;
            listado_componentes += '<option value="'+datos[indx].idComponente+'">'+datos[indx].datos_componente_detalle.indicador+'</option>';
        }else{
            accion.nivel = 'A';
            accion.indicador = datos[indx].datos_actividad_detalle.indicador;
            accion.unidadMedida = datos[indx].datos_actividad_detalle.unidadMedida;
        }
        accion.presupuesto = '$ ' + (parseFloat(presupuesto) || 0).format(2);
        accion.boton = '<span class="btn-link text-info boton-detalle" onClick="fibapAcciones.mostrar_detalles(' + datos[indx].id + ')"><span class="fa fa-plus-square-o"></span></span>'
        
        if(comentarios.componentes[accion.idComponente]){
            accion.indicador = '<span class="text-warning fa fa-warning comentario-row"></span> ' + accion.indicador;
        }
        
        if(datos[indx].idComponente){
            if(!componentes[datos[indx].idComponente]){
                componentes[datos[indx].idComponente] = {componente:{},actividades:[]};
            }
            componentes[datos[indx].idComponente].componente = accion;
        }else{
            if(!componentes[datos[indx].datos_actividad_detalle.idComponente]){
                componentes[datos[indx].datos_actividad_detalle.idComponente] = {componente:{},actividades:[]};
            }
            componentes[datos[indx].datos_actividad_detalle.idComponente].actividades.push(accion);
        }
        
        for(var i in datos[indx].propuestas_financiamiento){
            var origen = datos[indx].propuestas_financiamiento[i];
            if(!sumas_origenes[origen.idOrigenFinanciamiento]){
                sumas_origenes[origen.idOrigenFinanciamiento] = 0;
            }
            sumas_origenes[origen.idOrigenFinanciamiento] += parseFloat(origen.cantidad);
        }
    }
    
    var contador_componentes = 0;
    var contador_actividades = 0;
    for(var i in componentes){
        if(componentes[i].componente.nivel){
            contador_componentes++;
            contador_actividades = 0;
            componentes[i].componente.nivel = 'C ' + contador_componentes;
            acciones.push(componentes[i].componente);
        }
        for(var j in componentes[i].actividades){
            contador_actividades++;
            if(componentes[i].componente.nivel){
                componentes[i].actividades[j].nivel = 'A ' + contador_componentes + '.' + contador_actividades;
            }else{
                componentes[i].actividades[j].nivel = 'A -.-';
            }
            
            acciones.push(componentes[i].actividades[j]);
        }
    }
    
    $('.totales-financiamiento').each(function(){
        var id_origen = $(this).data('total-origen-id');
        if(sumas_origenes[id_origen]){
            $(this).text('$ ' + sumas_origenes[id_origen].format(2));
        }else{
            $(this).text('$ 0.00');
        }
    });

    if(datos.length == 0){
        $('#datagridAcciones > table > tbody').html('<tr><td colspan="5" style="text-align:left"><i class="fa fa-info-circle"></i> No hay datos</td></tr>');
    }else{
        $('#tab-link-acciones-fibap > span.badge').text(acciones.length);
        accionesDatagrid.cargarDatos(acciones);
    }
    
    if(listado_componentes != ''){
        var id_componente = $('#componente-seleccionado').val();
        $('#componente-seleccionado').html('<option value="">Seleccione un Componente</option>'+listado_componentes);
        $('#componente-seleccionado').val(id_componente);
    }
}

/*
function llenar_datagrid_actividades(datos){
    $('#datagridActividades > table > tbody').empty();
    var actividades = [];
    for(var indx in datos){
        var actividad = {};

        actividad.id = datos[indx].id;
        actividad.indicador = datos[indx].indicador;
        actividad.interpretacion = datos[indx].interpretacion;
        actividad.unidad_medida = datos[indx].unidad_medida.descripcion;
        actividad.creadoPor = datos[indx].usuario.username;
        actividad.creadoAl = datos[indx].creadoAl.substring(0,11);

        if(comentarios.actividades[actividad.id]){
            actividad.indicador = '<span class="text-warning fa fa-warning"></span> ' + actividad.indicador;
        }

        actividades.push(actividad);
    }

    $('#conteo-actividades').text(' ' + actividades.length + ' ');

    if(actividades.length == 0){
        $('#datagridActividades > table > tbody').html('<tr><td colspan="6" style="text-align:left"><i class="fa fa-info-circle"></i> No hay datos</td></tr>');
    }else{
        actividadDatagrid.cargarDatos(actividades);
    }
}
*/

function llenar_tabla_distribucion(datos){
    var distribucion = '';
    $(tabla_presupuesto_partida + ' > table > tbody').empty();
    var total_porcentaje = 0;
    var suma_distribuido = 0;
    var total_presup = $('#total-presupuesto-requerido').attr('data-valor');

    for(var indx in datos){
        var porcentaje = (parseFloat(datos[indx].cantidad) * 100) / parseFloat(total_presup);
        
        distribucion += '<tr>';
        distribucion += '<td>' + datos[indx].objeto_gasto.clave + '</td>';
        distribucion += '<td>' + datos[indx].objeto_gasto.descripcion + '</td>';
        distribucion += '<td>$ ' + parseFloat(datos[indx].cantidad).format(2) + '</td>';
        distribucion += '<td>' + parseFloat(porcentaje.toFixed(2)) + ' %</td>';
        distribucion += '</tr>';

        suma_distribuido += parseFloat(datos[indx].cantidad);
    }

    total_porcentaje = ((suma_distribuido * 100) / parseFloat(total_presup)) || 0;

    $('#total-presupuesto-distribuido').attr('data-valor',suma_distribuido);
    $('#total-presupuesto-distribuido').text('$ ' + suma_distribuido.format(2));
    
    if(suma_distribuido == 0){
        actualiza_porcentaje('#porcentaje_completo',0);
        $(tabla_presupuesto_partida + ' > table > tbody').html('<tr><td colspan="4" style="text-align:left"><i class="fa fa-info-circle"></i> No hay datos</td></tr>');
    }else{
        actualiza_porcentaje('#porcentaje_completo',parseFloat(total_porcentaje.toFixed(2)));
        $(tabla_presupuesto_partida + ' > table > tbody').html(distribucion);
    }
}

function llenar_tabla_componente_partidas(datos){
    $(tabla_componente_partidas + ' > table > tbody').empty();
    var partidas = '';
    var ocultos = '';
    for(var indx in datos){
        partidas += linea_tabla_partidas(datos[indx]);
        ocultos += '<input type="hidden" class="ocultos-partidas-componente" value="'+datos[indx].id+'" name="objeto-gasto-presupuesto[]" id="objeto-gasto-'+datos[indx].id+'">';
    }
    $(tabla_componente_partidas + ' > table > tbody').html(partidas);
    $(tabla_componente_partidas).append(ocultos);
}

function linea_tabla_partidas(datos_partida){
    var linea = '<tr data-partida-id="' + datos_partida.id + '">';
    linea += '<td>' + datos_partida.clave + '</td>';
    linea += '<td>' + datos_partida.descripcion + '</td>';
    linea += '<td><button class="btn btn-danger" onClick="fibapAcciones.quitar_partida_componente(' + datos_partida.id + ')"><span class="fa fa-trash"></span></button></td>';
    linea += '</tr>';
    return linea;
}

function actualiza_porcentaje(selector,porcentaje){
    $(selector).text(porcentaje.format(2) + ' %');
    $(selector).attr('aria-valuenow',porcentaje);
    $(selector).attr('style','width:'+porcentaje + '%;');
    if(porcentaje > 100){
        $(selector).addClass('progress-bar-danger');
        MessageManager.show({
            data:'El porcentaje se exedio, por favor modifique la propuesta de financiamiento o elimine uno o varios elementos en la distribución del presupuesto para corregir esto.',
            type:'ERR',
            container: '#grid_distribucion_presupuesto'
        });
    }else{
        $(selector).removeClass('progress-bar-danger');
    }
}

function llenar_select_jurisdicciones(datos){
    var options = $('#jurisdiccion-accion');
    options.html('<option value="">Selecciona una Jurisdicción</option><option value="OC">OFICINA CENTRAL</option>')
    $.each(datos, function() {
        options.append($("<option />").attr('data-jurisdiccion-id',this.id).val(this.clave).text(this.nombre));
    });
    options.val('');
    $(options).trigger('chosen:updated');
}

function llenar_select_municipios(datos){
    var html_municipios = '<option value="">Selecciona un Municipio</option>';
    var html_localidades = '<option value="">Selecciona una Localidad</option>';
    
    for(var i in datos){
        html_municipios += '<option value="'+datos[i].clave+'">'+datos[i].nombre+'</option>';

        if(datos[i].localidades.length){
            for(var j in datos[i].localidades){
                var localidad = datos[i].localidades[j];
                html_localidades += '<option value="'+localidad.clave+'">'+localidad.clave+' '+localidad.nombre+'</option>';
            }
        }
    }

    $("#municipio-accion").html(html_municipios);
    $("#localidad-accion").html(html_localidades);
}

function ocultar_detalles(remover_contendor){
    $('#datagridAcciones > table > tbody > tr > td > span.boton-detalle > span.fa-minus-square-o').addClass('fa-plus-square-o');
    $('#datagridAcciones > table > tbody > tr > td > span.boton-detalle > span.fa-minus-square-o').removeClass('fa-minus-square-o');
    $('#datagridAcciones > table > tbody > tr.bg-info').removeClass('text-primary');
    $('#datagridAcciones > table > tbody > tr.bg-info').removeClass('bg-info');
    $('#datagridDistribucion').appendTo('#datagrid-contenedor');
    if(remover_contendor){
        $('#datagridAcciones > table > tbody > tr.contendor-desechable').remove();
    }
}

function habilitar_meses_presupuesto(fechaInicio, fechaFinal){
    if(typeof(fechaInicio) == 'string'){
        var inicio = fechaInicio;
        var fin = fechaFinal;
    }else{
        var inicio = fechaInicio.date;
        var fin = fechaFinal.date;
    }

    var primer_mes = parseInt(inicio.substring(5,7));
    var ultimo_mes = parseInt(fin.substring(5,7));
    $('.presupuesto-mes').each(function(){
        if($(this).attr('data-presupuesto-mes') < primer_mes){
            $(this).prop('disabled',true);
        }else if($(this).attr('data-presupuesto-mes') > ultimo_mes){
            $(this).prop('disabled',true);
        }else{
            $(this).prop('disabled',false);
        }
    });
}
/*
function habilitar_meses_metas(selector, fechaInicio, fechaFinal){
    if(typeof(fechaInicio) == 'string'){
        var inicio = fechaInicio;
        var fin = fechaFinal;
    }else{
        var inicio = fechaInicio.date;
        var fin = fechaFinal.date;
    }

    if(selector == 'componente'){
        var formulario = form_accion;
    }else{
        var formulario = form_actividad;
    }
    
    var primer_mes = parseInt(inicio.substring(5,7));
    var ultimo_mes = parseInt(fin.substring(5,7));
    $(formulario + ' .metas-mes').each(function(){
        if($(this).attr('data-meta-mes') < primer_mes){
            $(this).prop('disabled',true);
        }else if($(this).attr('data-meta-mes') > ultimo_mes){
            $(this).prop('disabled',true);
        }else{
            $(this).prop('disabled',false);
        }
    });
}
*/
function habilitar_meses_captura(fechaInicio, fechaFinal){
    if(typeof(fechaInicio) == 'string'){
        var inicio = fechaInicio;
        var fin = fechaFinal;
    }else{
        var inicio = fechaInicio.date;
        var fin = fechaFinal.date;
    }

    ejecucion_fecha_inicio = inicio;
    ejecucion_fecha_fin = fin;

    //habilitar_meses_metas('actividad', inicio, fin);
    $(form_accion + ' .metas-mes').prop('disabled',true);
    
    var primer_mes = parseInt(inicio.substring(5,7));
    var ultimo_mes = parseInt(fin.substring(5,7));
    $('.meta-mes').each(function(){
        if($(this).attr('data-meta-mes') < primer_mes){
            $(this).prop('disabled',true);
        }else if($(this).attr('data-meta-mes') > ultimo_mes){
            $(this).prop('disabled',true);
        }else{
            $(this).prop('disabled',false);
        }
    });
}

function actualizar_claves_presupuesto(datos){
    $('.grupo-partidas > .grupo-partida-presupuestal').remove();
    $('.grupo-partidas').each(function(){
        var mes = $(this).attr('data-grupo-mes');
        var html = '';
        for(var i in datos){
            html += '<div class="input-group grupo-partida-presupuestal">';
            html += '<span class="input-group-addon" title="' + datos[i].descripcion + '">' + datos[i].clave + '</span>';
            html += '<input id="mes-' + mes + '-' + datos[i].id + '" name="mes[' + mes + '][' + datos[i].id + ']" type="number" class="form-control input-sm presupuesto-mes" data-presupuesto-partida="' + datos[i].id + '" data-presupuesto-mes="' + mes + '" data-presupuesto-id="" min="0">';
            html += '</div>';
        }
        $(this).append(html);
    });
    $('.presupuesto-mes').on('keyup',function(){
        $(this).change();
    });
    $('.presupuesto-mes').on('change',function(){
        var sumatoria = 0;
        $('.presupuesto-mes').each(function(){
            sumatoria += parseFloat($(this).val()) || 0;
        });
        $('#cantidad-presupuesto').val(sumatoria);
        $('#cantidad-presupuesto-lbl').text(sumatoria.format(2));
    });
    habilitar_meses_presupuesto(ejecucion_fecha_inicio, ejecucion_fecha_fin);
}

function actualizar_tabla_metas_mes(identificador,jurisdicciones){
    var meses = [];
    meses[1] = ['ENE','FEB','MAR'];
    meses[2] = ['ABR','MAY','JUN'];
    meses[3] = ['JUL','AGO','SEP'];
    meses[4] = ['OCT','NOV','DIC'];

    for(var j = 1 ; j <= 4 ; j++ ){
        var tabla_id = '#tabla-'+identificador+'-metas-mes-'+j;

        var html = '';
        var idx, id_mes;

        for(var i in jurisdicciones){
            html += '<tr>';
            html += '<th>'+jurisdicciones[i].clave+'</th>';
            for(idx in meses[j]){
                id_mes = parseInt(((j-1)*3)+parseInt(idx)) + 1;
                html += '<td><input id="mes-'+identificador+'-'+jurisdicciones[i].clave+'-'+id_mes+'" name="mes-'+identificador+'['+jurisdicciones[i].clave+']['+id_mes+']" type="number" class="form-control input-sm metas-mes" data-meta-mes="'+id_mes+'" data-meta-jurisdiccion="'+jurisdicciones[i].clave+'" data-meta-identificador="'+identificador+'" data-meta-id=""></td>';
            }
            html += '</tr>';
        }

        html += '<tr><th>O.C.</th>';
        for(idx in meses[j]){
            id_mes = parseInt(((j-1)*3)+parseInt(idx)) + 1;
            html += '<td><input id="mes-'+identificador+'-OC-'+id_mes+'" name="mes-'+identificador+'[OC]['+id_mes+']" type="number" class="form-control input-sm metas-mes" data-meta-mes="'+id_mes+'" data-meta-jurisdiccion="OC" data-meta-identificador="'+identificador+'" data-meta-id=""></td>';
        }
        html += '</tr>';

        $(tabla_id + ' tbody').empty();
        $(tabla_id + ' tbody').html(html);
    }

    actualizar_eventos_metas(identificador);
}

function actualizar_eventos_metas(identificador){
    $('.metas-mes[data-meta-identificador="' + identificador + '"]').on('keyup',function(){ $(this).change(); });
    $('.metas-mes[data-meta-identificador="' + identificador + '"]').off('change');
    $('.metas-mes[data-meta-identificador="' + identificador + '"]').on('change',function(){
        var mes = $(this).data('meta-mes');
        var trimestre = Math.ceil(mes/3);
        //var identificador = $(this).data('meta-identificador');
        
        var suma = 0;
        var mes_inicio = 0;
        var mes_fin = 0;

        if(trimestre == 1){
            mes_inicio = 1;
            mes_fin = 3;
        }else if(trimestre == 2){
            mes_inicio = 4;
            mes_fin = 6;
        }else if(trimestre == 3){
            mes_inicio = 7;
            mes_fin = 9;
        }else if(trimestre == 4){
            mes_inicio = 10;
            mes_fin = 12;
        }

        for(var i = mes_inicio; i <= mes_fin; i++) {
            $('.metas-mes[data-meta-mes="' + i + '"][data-meta-identificador="' + identificador + '"]').each(function(){
                suma += parseFloat($(this).val()) || 0;
            });
        }
        
        $('#trim'+trimestre+'-'+identificador).val(suma);
        $('#trim'+trimestre+'-'+identificador+'-lbl').text(suma);

        var trim1 = parseFloat($('#trim1-'+identificador).val()) || 0;
        var trim2 = parseFloat($('#trim2-'+identificador).val()) || 0;
        var trim3 = parseFloat($('#trim3-'+identificador).val()) || 0;
        var trim4 = parseFloat($('#trim4-'+identificador).val()) || 0;

        suma = trim1 + trim2 + trim3 + trim4;

        $('#numerador-'+identificador).val(suma);
        $('#numerador-'+identificador+'-lbl').text(suma.format(2));
        ejecutar_formula(identificador);
    });
}

function ejecutar_formula(identificador){   
    var numerador = parseFloat($('#numerador-'+identificador).val()) || 0;
    var denominador = parseFloat($('#denominador-'+identificador).val()) || 1;
    var total;
    var id_formula = $('#formula-'+identificador).val();
    if(id_formula){
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
        $('#meta-'+identificador).val(total);
        $('#meta-'+identificador+'-lbl').text(total.format(2));
    }else{
        $('#meta-'+identificador).val('');
        $('#meta-'+identificador+'-lbl').text('');
    }
    
}

function reset_modal_form(form){
    $(form).get(0).reset();
    Validation.cleanFormErrors(form);
    $(form + ' .chosen-one').trigger('chosen:updated');
    $(form + ' .texto-comentario').remove();
    $(form + ' .has-warning').removeClass('has-warning');
    if(form == form_accion){
        $(modal_accion + ' .alert').remove();
        $('#entregable').chosen().change();
        $('#id-componente').val('');
        $('#id-actividad').val('');
        $('#id-accion').val('');
        $(tabla_componente_partidas + ' > table > tbody').empty();
        $('.ocultos-partidas-componente').remove();
        $('#accion-presupuesto-requerido-lbl').text('');
        $('#trim1-componente-lbl').text('');
        $('#trim2-componente-lbl').text('');
        $('#trim3-componente-lbl').text('');
        $('#trim4-componente-lbl').text('');
        $('#numerador-componente-lbl').text('');
        $('#meta-componente-lbl').text('');
        $(form_accion + ' input[type="hidden"]').val('');
        $(form_accion + ' .accion-origen-financiamiento').attr('data-captura-id','');
        $(form_accion + ' .metas-mes').attr('data-meta-id','');
        $('#nivel-accion').val('componente').change();
        $('#nivel-accion').prop('disabled',false);
        Validation.cleanFieldErrors('componente-seleccionado');
    }else if(form == form_presupuesto){
        $(modal_presupuesto + ' .alert').remove();
        $('#id-desglose').val('');
        $('#jurisdiccion-accion').chosen().change();
        $('.benef-totales-accion').change();
        $('#trim1-lbl').text(0);
        $('#trim2-lbl').text(0);
        $('#trim3-lbl').text(0);
        $('#trim4-lbl').text(0);
        $('#cantidad-meta-lbl').text(0);
        $('#cantidad-presupuesto-lbl').text(0);
        $(form_presupuesto + ' .meta-mes').attr('data-meta-id','');
        $(form_presupuesto + ' .presupuesto-mes').attr('data-presupuesto-id','');
        $(form_presupuesto + ' input[type="hidden"]').val('');
    }
    /*
    else if(form == form_actividad){
        $(modal_actividad + ' .alert').remove();
        $('#id-actividad').val('');
        $(modal_actividad + ' .metas-mes').attr('data-meta-id','');
        $('#trim1-actividad-lbl').text('');
        $('#trim2-actividad-lbl').text('');
        $('#trim3-actividad-lbl').text('');
        $('#trim4-actividad-lbl').text('');
        $('#numerador-actividad-lbl').text('');
        $('#meta-actividad-lbl').text('');
        $(form_actividad + ' input[type="hidden"]').val('');
    }*/
}

function mostrar_comentario(comentario){
    console.log(comentario);
    $(modal_presupuesto + ' .modal-body').prepend('<div class="texto-comentario alert alert-warning"><span class="fa fa-warning"></span> '+comentario.observacion+'</div>');
}

function mostrar_comentarios(datos){
    for(var i in datos){
        var id_campo = datos[i].idCampo;
        var observacion = datos[i].observacion;
        var tipo_comentario = datos[i].tipoComentario;
        
        if(tipo_comentario == 1){
            if(id_campo.substring(0,8) == 'partidas'){
                $(tabla_componente_partidas).addClass('has-warning');
                $(tabla_componente_partidas).prepend('<p class="help-block texto-comentario"><span class="fa fa-warning"></span> '+observacion+'</p>');
            }else{
                if($('#'+id_campo).length){
                    $('label[for="' + id_campo + '"]').prepend('<span class="fa fa-warning texto-comentario"></span> ');
                    $('#'+id_campo).parent('.form-group').addClass('has-warning');
                    $('#'+id_campo).parent('.form-group').append('<p class="texto-comentario has-warning help-block"> '+observacion+'</p>');
                }
            }
        }
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
    //return this.toFixed(Math.max(0, ~~n)).replace(new RegExp(re, 'g'), '$&,');
    var formateado = this.toFixed(Math.max(0, ~~n)).replace(new RegExp(re, 'g'), '$&,');
    var partes = formateado.split('.');
    if(parseInt(partes[1]) == 0){
        return partes[0];
    }else{
        return formateado;
    }
};

})(fibapAcciones);