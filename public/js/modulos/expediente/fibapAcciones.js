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

var id_fibap;
var fibap_resource;
var accionesDatagrid;
var actividadDatagrid;
var distribucionDatagrid;
var tabla_presupuesto_partida = '#tabla_presupuesto_partida';
var tabla_componente_partidas = '#tabla_componente_partidas';
//var modal_accion = '#modal-accion';
var modal_accion = '#modal-componente'
var form_accion = '#form_componente';

var modal_actividad = '#modal-actividad'
var form_actividad = '#form_actividad';

var modal_presupuesto = '#modal-presupuesto';
var form_presupuesto = '#form-presupuesto';

var presupuesto_total = 0;
var presupuesto_origenes = [];

context.cargar_jurisdicciones = function(datos){
    llenar_select_jurisdicciones(datos);
};

context.cargar_municipios = function(datos){
    llenar_select_municipios(datos);
}

context.actualizar_total_presupuesto = function(presupuesto){
    $('#total-presupuesto-requerido').text('$ ' + presupuesto.format());
    $('#total-presupuesto-requerido').attr('data-valor',presupuesto);

    var distribuido = $('#total-presupuesto-distribuido').attr('data-valor');
    var porcentaje = (distribuido * 100) / parseFloat(presupuesto);

    actualiza_porcentaje('#porcentaje_completo',porcentaje);
}

context.init = function(id,resource){
    id_fibap = id;
    fibap_resource = resource;

    //Inicializacion de los DataGrids
    actividadDatagrid = new Datagrid('#datagridActividades',fibap_resource);
    actividadDatagrid.init();

    accionesDatagrid = new Datagrid("#datagridAcciones",fibap_resource);
    accionesDatagrid.init();

    distribucionDatagrid = new Datagrid("#datagridDistribucion",fibap_resource); 
    distribucionDatagrid.init();

    llenar_datagrid_acciones([]);
    llenar_datagrid_actividades([]);
    llenar_tabla_distribucion([]);

    $('#btn-agregar-distribucion').on('click',function(){
        $(modal_presupuesto).find(".modal-title").html("Nuevo Presupuesto");
        $(modal_presupuesto).modal('show');
    });

    $('#btn-agregar-actividad').on('click',function(){
        var actividades = $('#conteo-actividades').text().split('/');

        if(parseInt(actividades[0]) >= parseInt(actividades[1])){
            MessageManager.show({code:'S03',data:"Las actividades para este componente ya estan completas.",timer:2});
        }else{
            $(modal_actividad).find(".modal-title").html("Nueva Actividad");
            $(modal_actividad).modal('show');
        }
    });

    $('#btn-agregar-accion').on('click',function(){
        var componentes = $('#tab-link-acciones-fibap > span.badge').text().split('/');

        if(parseInt(componentes[0]) >= parseInt(componentes[1])){
            MessageManager.show({code:'S03',data:"Los componentes para este proyecto ya estan completos.",timer:2});
        }else{
            $('#tablink-componente-actividades').attr('data-toggle','');
            $('#tablink-componente-actividades').parent().addClass('disabled');
            $('#lista-tabs-componente a:first').tab('show');
            $(modal_accion).find(".modal-title").html("Nuevo Componente");
            $(modal_accion).modal('show');
        }
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
                $(tabla_componente_partidas).append('<input type="hidden" value="'+datos_partida.id+'" name="objeto-gasto-presupuesto[]" id="objeto-gasto-'+datos_partida.id+'">');
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
            suma += parseInt($('.meta-mes[data-meta-mes="' + i + '"]').val()) || 0;
        }
        $('#trim'+trimestre).val(suma);
        $('#trim'+trimestre+'-lbl').text(suma.format());
        
        suma = 0;
        for(var i = 1 ; i <= 4 ; i++){
            suma += parseInt($('#trim'+i).val()) || 0;
        }
        $('#cantidad-meta').val(suma);
        $('#cantidad-meta-lbl').text(suma.format());
    });

    $('.accion-origen-financiamiento').on('change',function(){
        var sumatoria = 0;
        $('.accion-origen-financiamiento').each(function(){
            sumatoria += parseFloat($(this).val()) || 0;
        });
        $('#accion-presupuesto-requerido').val(sumatoria);
        $('#accion-presupuesto-requerido-lbl').text(sumatoria.format());
    });

    $('.accion-origen-financiamiento').on('keyup',function(){
        $(this).change();
    });

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

    $('#jurisdiccion-accion').on('change',function(){
        var selector = '#municipio-accion';
        var habilitar_id = $('#jurisdiccion-accion option:selected').attr('data-jurisdiccion-id');
        var default_id;

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
    });

    $('#municipio-accion').on('change',function(){
        var selector = '#localidad-accion';
        var habilitar_id = $('#municipio-accion option:selected').attr('data-municipio-id');
        var default_id;

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
    });

    $(modal_accion + ' #lista-tabs-componente').on('show.bs.tab',function(event){
        var id = event.target.id;
        if(id == 'tablink-componente-actividades'){
            $('.btn-grupo-guardar').hide();
        }else{
            $('.btn-grupo-guardar').show();
        }
    });

    $(modal_accion).on('hide.bs.modal',function(e){
        reset_modal_form(form_accion);
    });

    $(modal_presupuesto).on('hide.bs.modal',function(e){
        $(modal_presupuesto).find(".modal-title").html("Nuevo");
        reset_modal_form(form_presupuesto);
    });
};

context.mostrar_datos_presupuesto = function(datos){
    $(modal_presupuesto).find(".modal-title").html("Editar Presupuesto");
    //$(modal_presupuesto).find(".modal-title").html("Editar Presupuesto");
    $('#jurisdiccion-accion').val(datos.calendarizado[0].claveJurisdiccion);
    $('#jurisdiccion-accion').trigger('chosen:updated');
    $('#jurisdiccion-accion').chosen().change();

    var desglose = datos.desglose;
    $('#municipio-accion').val(desglose.claveMunicipio);
    $('#municipio-accion').trigger('chosen:updated');
    $('#municipio-accion').chosen().change();

    $('#localidad-accion').val(desglose.claveMunicipio + '|' + desglose.claveLocalidad);
    $('#localidad-accion').trigger('chosen:updated');

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

    //$('#cantidad-presupuesto').val(desglose.presupuesto);
    //$('#cantidad-presupuesto').change();
    
    var calendarizacion = datos.calendarizado;
    for(var indx in calendarizacion){
        $('#mes-'+calendarizacion[indx].mes+'-'+calendarizacion[indx].idObjetoGasto).val(calendarizacion[indx].cantidad);
        $('#mes-'+calendarizacion[indx].mes+'-'+calendarizacion[indx].idObjetoGasto).attr('data-presupuesto-id',calendarizacion[indx].id);
    }

    $('#id-desglose').val(desglose.id);

    $('.presupuesto-mes').first().change();
    $('.meta-mes').first().change();

    $(modal_presupuesto).modal('show');
};

context.mostrar_datos = function(datos){
    ocultar_detalles(true);

    $(modal_accion).find(".modal-title").html('Editar Componente');

    $('#descripcion-obj-componente').val(datos.componente.objetivo);
    $('#verificacion-componente').val(datos.componente.mediosVerificacion);
    $('#supuestos-componente').val(datos.componente.supuestos);
    $('#descripcion-ind-componente').val(datos.componente.indicador);
    $('#numerador-ind-componente').val(datos.componente.numerador);
    $('#denominador-ind-componente').val(datos.componente.denominador);
    $('#interpretacion-componente').val(datos.componente.interpretacion);
    $('#denominador-componente').val(datos.componente.valorDenominador).change();
    $('#linea-base-componente').val(datos.componente.lineaBase);
    $('#anio-base-componente').val(datos.componente.anioBase);
    $('#formula-componente').val(datos.componente.idFormula);
    $('#dimension-componente').val(datos.componente.idDimensionIndicador);
    $('#frecuencia-componente').val(datos.componente.idFrecuenciaIndicador);
    $('#tipo-ind-componente').val(datos.componente.idTipoIndicador);
    $('#unidad-medida-componente').val(datos.componente.idUnidadMedida);

    $('#entregable').val(datos.componente.idEntregable);
    $('#entregable').chosen().change();
    $('#tipo-entregable').val(datos.componente.idEntregableTipo || 'NA');
    $('#accion-entregable').val(datos.componente.idEntregableAccion);

    $(form_accion + ' .chosen-one').trigger('chosen:updated');

    $('#id-accion').val(datos.id);
    $('#id-componente').val(datos.componente.id);
    $('#tablink-componente-actividades').attr('data-toggle','tab');
    $('#tablink-componente-actividades').parent().removeClass('disabled');

    $('#accion-presupuesto-requerido').val(datos.presupuestoRequerido);
    $('#accion-presupuesto-requerido-lbl').text(datos.presupuestoRequerido.format());
    for(var indx in datos.propuestas_financiamiento){
        var origen = datos.propuestas_financiamiento[indx];
        $('#accion-origen-'+origen.idOrigenFinanciamiento).val(origen.cantidad);
        $('#accion-origen-'+origen.idOrigenFinanciamiento).attr('data-captura-id',origen.id);
    }
    
    llenar_tabla_componente_partidas(datos.partidas);
    llenar_datagrid_actividades(datos.componente.actividades);

    if(datos.componente.metas_mes){
        var suma = 0;
        var suma_trimestre = {'1':0,'2':0,'3':0,'4':0};
        var trimestre = 0;
        for(var i in datos.componente.metas_mes){
            trimestre = Math.ceil(datos.componente.metas_mes[i].mes/3);
            suma_trimestre[trimestre] += datos.componente.metas_mes[i].meta;
            suma += datos.componente.metas_mes[i].meta;
            $('#mes-componente-' + datos.componente.metas_mes[i].claveJurisdiccion + '-' + datos.componente.metas_mes[i].mes).val(datos.componente.metas_mes[i].meta);
        }
        for(var i in suma_trimestre){
            $('#trim'+i+'-componente').val(suma_trimestre[i]);
            $('#trim'+i+'-componente-lbl').text(suma_trimestre[i]);
        }
        $('#numerador-componente').val(suma);
        $('#numerador-componente-lbl').text(suma);
    }

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
    actualizar_tabla_metas_mes('componente',jurisdicciones);
    actualizar_tabla_metas_mes('actividad',jurisdicciones);
};

context.llenar_datagrid = function(datos){
    llenar_datagrid_acciones(datos);
};

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
        llenar_datagrid_distribucion([],0);
    }else{
        var parametros = {'mostrar':'desglose-componente'};
        fibap_resource.get(id,parametros,{
            _success:function(response){
                //
                $('#datagridAcciones > table > tbody > tr.contendor-desechable').remove();
                llenar_datagrid_distribucion(response.data.distribucion_presupuesto_agrupado,response.data.presupuestoRequerido);

                $('#datagridAcciones > table > tbody > tr[data-id="' +  id+ '"]').addClass('bg-info');
                $('#datagridAcciones > table > tbody > tr[data-id="' +  id+ '"]').addClass('text-primary');
                $('#datagridAcciones > table > tbody > tr[data-id="' +  id+ '"] > td > span.boton-detalle > span.fa-plus-square-o').addClass('fa-minus-square-o');
                $('#datagridAcciones > table > tbody > tr[data-id="' +  id+ '"] > td > span.boton-detalle > span.fa-plus-square-o').removeClass('fa-plus-square-o');
                
                $('#datagridAcciones > table > tbody > tr[data-id="' +  id+ '"]').after('<tr class="contendor-desechable disabled"><td class="disabled bg-info" colspan="7" id="datagrid-contenedor-' + id + '"></td></tr>');
                $('#datagridDistribucion').appendTo('#datagrid-contenedor-' + id);
                $('#datagridDistribucion').attr('data-selected-id',id);

                $('#indicador_texto').text(response.data.datos_componente_detalle.indicador);
                $('#unidad_medida_texto').text(response.data.datos_componente_detalle.unidadMedida);

                actualizar_claves_presupuesto(response.data.partidas);
            }
        });
    }
};

context.llenar_datagrid_distribucion = function(datos,total_presupuesto){
    llenar_datagrid_distribucion(datos,total_presupuesto);
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
        html += '<td><div class="form-group"><input type="number" class="form-control benef-totales-accion" name="beneficiarios[' + beneficiarios[i].id + '][f]" id="beneficiarios-' + beneficiarios[i].id + '-f" data-tipo-beneficiario="' + beneficiarios[i].id + '"></div></td>';
        html += '<td><div class="form-group"><input type="number" class="form-control benef-totales-accion" name="beneficiarios[' + beneficiarios[i].id + '][m]" id="beneficiarios-' + beneficiarios[i].id + '-m" data-tipo-beneficiario="' + beneficiarios[i].id + '"></div></td>';
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
/***********************************************************************************************
                    Funciones Privadas
************************************************************************************************/
function llenar_datagrid_distribucion(datos,total_presupuesto){
    var distribucion = [];
    $('#datagridDistribucion > table > tbody').empty();
    var total_porcentaje = 0;
    for(var indx in datos){
        var presupuesto = {};

        var porcentaje = (datos[indx].cantidad * 100) / parseInt(total_presupuesto);

        presupuesto.id = datos[indx].id;

        if(datos[indx].claveJurisdiccion != 'OC'){
            presupuesto.localidad = datos[indx].localidad;
            presupuesto.municipio = datos[indx].municipio;
            presupuesto.jurisdiccion = datos[indx].jurisdiccion.nombre;
        }else{
            presupuesto.localidad = 'OFICINA CENTRAL';
            presupuesto.municipio = 'OFICINA CENTRAL';
            presupuesto.jurisdiccion = 'OFICINA CENTRAL';
        }
        
        presupuesto.monto = '$ ' + datos[indx].cantidad.format();

        total_porcentaje += parseFloat(porcentaje.toFixed(2));

        distribucion.push(presupuesto);
    }

    if(distribucion.length == 0){
        actualiza_porcentaje('#porcentaje_accion',0);
        $('#datagridDistribucion > table > tbody').html('<tr><td colspan="5" style="text-align:left"><i class="fa fa-info-circle"></i> No hay datos</td></tr>');
    }else{
        actualiza_porcentaje('#porcentaje_accion',total_porcentaje);
        distribucionDatagrid.cargarDatos(distribucion);
    }

}

function llenar_datagrid_acciones(datos){
    $('#datagridAcciones > table > tbody').empty();
    var acciones = [];
    var sumas_origenes = [];

    for(var indx in datos){
        var accion = {};

        var presupuesto = parseFloat(datos[indx].presupuestoRequerido);

        accion.id = datos[indx].id;
        accion.entregable = datos[indx].datos_componente_detalle.entregable;
        accion.tipo = datos[indx].datos_componente_detalle.entregableTipo || 'N / A';
        accion.accion = datos[indx].datos_componente_detalle.entregableAccion;
        accion.modalidad = 'pendiente';//datos[indx].cantidad;
        accion.presupuesto = '$ ' + parseFloat(presupuesto.toFixed(2)).format();
        accion.boton = '<span class="btn-link text-info boton-detalle" onClick="fibapAcciones.mostrar_detalles(' + datos[indx].id + ')"><span class="fa fa-plus-square-o"></span></span>'

        acciones.push(accion);

        for(var i in datos[indx].propuestas_financiamiento){
            var origen = datos[indx].propuestas_financiamiento[i];
            if(!sumas_origenes[origen.idOrigenFinanciamiento]){
                sumas_origenes[origen.idOrigenFinanciamiento] = 0;
            }
            sumas_origenes[origen.idOrigenFinanciamiento] += origen.cantidad;
        }
    }
    
    $('.totales-financiamiento').each(function(){
        var id_origen = $(this).data('total-origen-id');
        if(sumas_origenes[id_origen]){
            $(this).text('$ ' + sumas_origenes[id_origen].format());
        }else{
            $(this).text('$ 0.00');
        }
    });

    if(datos.length == 0){
        $('#datagridAcciones > table > tbody').html('<tr><td></td><td colspan="5" style="text-align:left"><i class="fa fa-info-circle"></i> No hay datos</td></tr>');
    }else{
        $('#tab-link-acciones-fibap > span.badge').text(acciones.length + ' / 2');
        accionesDatagrid.cargarDatos(acciones);
    }
}

function llenar_datagrid_actividades(datos){
    $('#datagridActividades > table > tbody').empty();
    var actividades = [];
    for(indx in datos){
        var actividad = {};

        actividad.id = datos[indx].id;
        actividad.indicador = datos[indx].indicador;
        actividad.interpretacion = datos[indx].interpretacion;
        actividad.unidad_medida = datos[indx].unidad_medida.descripcion;
        actividad.creadoPor = datos[indx].usuario.username;
        actividad.creadoAl = datos[indx].creadoAl.substring(0,11);

        actividades.push(actividad);
    }

    $('#conteo-actividades').text(' ' + actividades.length + ' / 5 ');

    if(actividades.length == 0){
        $('#datagridActividades > table > tbody').html('<tr><td colspan="6" style="text-align:left"><i class="fa fa-info-circle"></i> No hay datos</td></tr>');
    }else{
        actividadDatagrid.cargarDatos(actividades);
    }
}

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
        distribucion += '<td>$ ' + datos[indx].cantidad.format() + '</td>';
        distribucion += '<td>' + parseFloat(porcentaje.toFixed(2)) + ' %</td>';
        distribucion += '</tr>';

        suma_distribuido += datos[indx].cantidad;
    }

    total_porcentaje = (suma_distribuido * 100) / parseFloat(total_presup);

    $('#total-presupuesto-distribuido').attr('data-valor',suma_distribuido);
    $('#total-presupuesto-distribuido').text('$ ' + suma_distribuido.format());

    if(distribucion == ''){
        actualiza_porcentaje('#porcentaje_completo',0);
        $(tabla_presupuesto_partida + ' > table > tbody').html('<tr><td colspan="4" style="text-align:left"><i class="fa fa-info-circle"></i> No hay datos</td></tr>');
    }else{
        actualiza_porcentaje('#porcentaje_completo',parseFloat(porcentaje.toFixed(2)));
        $(tabla_presupuesto_partida + ' > table > tbody').html(distribucion);
    }
}

function llenar_tabla_componente_partidas(datos){
    $('#tabla_componente_partidas' + ' > table > tbody').empty();
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
    $(selector).text(porcentaje + ' %');
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
    var municipios = $("#municipio-accion");
    var localidades = $("#localidad-accion");

    localidades.html('<option value="">Selecciona una Localidad</option>')
    municipios.html('<option value="">Selecciona un Municipio</option>')

    $.each(datos, function() {
        for(var i in this.localidades){
            localidades.append($("<option />").attr('data-habilita-id',this.localidades[i].idMunicipio)
                                            .attr('data-clave-municipio',this.clave)
                                            .attr('disabled',true)
                                            .addClass('hidden')
                                            .val(this.clave + '|' + this.localidades[i].clave).text(this.localidades[i].nombre));
        }
        municipios.append($("<option />").attr('data-habilita-id',this.idJurisdiccion)
                                        .attr('data-municipio-id',this.id)
                                        .attr('disabled',true)
                                        .addClass('hidden')
                                        .val(this.clave).text(this.nombre));
    });
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

function actualizar_claves_presupuesto(datos){
    $('.grupo-partidas > .grupo-partida-presupuestal').remove();
    $('.grupo-partidas').each(function(){
        var mes = $(this).attr('data-grupo-mes');
        var html = '';
        for(var i in datos){
            html += '<div class="input-group grupo-partida-presupuestal">';
            html += '<span class="input-group-addon" title="' + datos[i].descripcion + '">' + datos[i].clave + '</span>';
            html += '<input id="mes-' + mes + '-' + datos[i].id + '" name="mes[' + mes + '][' + datos[i].id + ']" type="number" class="form-control input-sm presupuesto-mes" data-presupuesto-partida="' + datos[i].id + '" data-presupuesto-mes="' + mes + '" data-presupuesto-id="">';
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
        $('#cantidad-presupuesto-lbl').text(sumatoria.format());
    });
}

function actualizar_tabla_metas_mes(identificador,jurisdicciones){
    var tabla_id = '#tabla-'+identificador+'-metas-mes';
    var meses = ['ENE','FEB','MAR','ABR','MAY','JUN','JUL','AGO','SEP','OCT','NOV','DIC'];

    var html = '';
    var indx,idx;

    for(var i in jurisdicciones){
        html += '<tr>';
        html += '<th>'+jurisdicciones[i].clave+'</th>';
        for(idx in meses){
            id_mes = parseInt(idx) + 1;
            html += '<td><input id="mes-'+identificador+'-'+jurisdicciones[i].clave+'-'+id_mes+'" name="mes-'+identificador+'['+jurisdicciones[i].clave+']['+id_mes+']" type="number" class="form-control input-sm metas-mes" data-meta-mes="'+id_mes+'" data-meta-jurisdiccion="'+jurisdicciones[i].clave+'" data-meta-identificador="'+identificador+'" data-meta-id=""></td>';
        }
        html += '</tr>';
    }

    html += '<tr><th>O.C.</th>';
    for(idx in meses){
        id_mes = parseInt(idx) + 1;
        html += '<td><input id="mes-'+identificador+'-OC-'+id_mes+'" name="mes-'+identificador+'[OC]['+id_mes+']" type="number" class="form-control input-sm metas-mes" data-meta-mes="'+id_mes+'" data-meta-jurisdiccion="OC" data-meta-identificador="'+identificador+'" data-meta-id=""></td>';
    }
    html += '</tr>';

    $(tabla_id + ' tbody').empty();
    $(tabla_id + ' tbody').html(html);
    actualizar_eventos_metas();
}

function actualizar_eventos_metas(){
    $('.metas-mes').off('change');
    $('.metas-mes').on('change',function(){
        var mes = $(this).data('meta-mes');
        var trimestre = Math.ceil(mes/3);
        var identificador = $(this).data('meta-identificador');
        
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
                suma += parseInt($(this).val()) || 0;
            });
        }
        
        $('#trim'+trimestre+'-'+identificador).val(suma).change();

        var trim1 = parseInt($('#trim1-'+identificador).val()) || 0;
        var trim2 = parseInt($('#trim2-'+identificador).val()) || 0;
        var trim3 = parseInt($('#trim3-'+identificador).val()) || 0;
        var trim4 = parseInt($('#trim4-'+identificador).val()) || 0;

        suma = trim1 + trim2 + trim3 + trim4;

        $('#numerador-'+identificador).val(suma).change();
        ejecutar_formula(identificador);
    });
}

function reset_modal_form(form){
    $(form).get(0).reset();
    Validation.cleanFormErrors(form);
    $(form + ' .chosen-one').trigger('chosen:updated');
    if(form == form_accion){
        $(modal_accion + ' .alert').remove();
        $('#entregable').chosen().change();
        $('#id-componente').val('');
        $('#id-accion').val('');
        $('#tabla_componente_partidas' + ' > table > tbody').empty();
        $('.ocultos-partidas-componente').remove();
        $('#accion-presupuesto-requerido-lbl').text('');
        $(modal_accion + ' .accion-origen-financiamiento').attr('data-captura-id','');
        $(modal_accion + ' .metas-mes').attr('data-meta-id','');
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

})(fibapAcciones);