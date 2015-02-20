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
var tabla_presupuesto_partida = '#tabla_presupuesto_partida';
var tabla_componente_partidas = '#tabla_componente_partidas';
//var modal_accion = '#modal-accion';
var modal_accion = '#modal-componente'
var form_accion = '#form-componente';
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
    accionesDatagrid = new Datagrid("#datagridAcciones",fibap_resource);
    accionesDatagrid.init();
    llenar_datagrid_acciones([]);
    llenar_tabla_distribucion([]);

    $('#btn-agregar-distribucion').on('click',function(){
        $(modal_presupuesto).find(".modal-title").html("Nuevo Presupuesto");
        $(modal_presupuesto).modal('show');
    });

    $('#btn-agregar-accion').on('click',function(){
        $(modal_accion).find(".modal-title").html("Nuevo Componente");
        $(modal_accion).modal('show');
    });

    $('#btn-agregar-partida').on('click',function(){
        var seleccionado = $('#objeto-gasto option:selected');
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
};

context.quitar_partida_componente = function(id){
    $(tabla_componente_partidas + ' > table > tbody > tr[data-partida-id="'+id+'"]').remove();
    $('#objeto-gasto-' + id).remove();
};

/***********************************************************************************************
                    Funciones Privadas
************************************************************************************************/
function llenar_datagrid_acciones(datos){
    $('#datagridAcciones > table > tbody').empty();
    var acciones = [];
    var sumas_origenes = [];

    for(var indx in datos){
        var accion = {};

        var presupuesto = parseFloat(datos[indx].presupuestoRequerido);

        accion.id = datos[indx].id;
        accion.entregable = datos[indx].datos_componente_listado.entregable;
        accion.tipo = datos[indx].datos_componente_listado.entregableTipo || 'N / A';
        accion.accion = datos[indx].datos_componente_listado.entregableAccion;
        accion.modalidad = 'pendiente';//datos[indx].cantidad;
        accion.presupuesto = '$ ' + parseFloat(presupuesto.toFixed(2)).format();
        accion.boton = '<span class="btn-link text-info boton-detalle" onClick="mostrar_detalles(' + datos[indx].id + ')"><span class="fa fa-plus-square-o"></span></span>'

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
        accionesDatagrid.cargarDatos(acciones);
    }
}

function llenar_tabla_distribucion(datos){
    var distribucion = '';
    $(tabla_presupuesto_partida + ' > table > tbody').empty();
    var total_porcentaje = 0;
    var suma_distribuido = 0;
    var total_presup = $('#total-presupuesto-requerido').attr('data-valor');
    for(var indx in datos){
        var porcentaje = (datos[indx].cantidad * 100) / parseFloat(total_presup);

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
        ocultos += '<input type="hidden" value="'+datos[indx].id+'" name="objeto-gasto-presupuesto[]" id="objeto-gasto-'+datos[indx].id+'">';
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

function reset_modal_form(form){
    $(form).get(0).reset();
    Validation.cleanFormErrors(form);
    if(form == form_accion){
        $(modal_accion + ' .alert').remove();
        $('#id-accion').val('');
    }else if(form == form_presupuesto){
        $(modal_presupuesto + ' .alert').remove();
        $('#id-presupuesto').val('');
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