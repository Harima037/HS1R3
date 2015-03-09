/*=====================================

    # Nombre:
        vista-rendicion-cuentas.js

    # Módulo:
        seguimiento/editar-avance

    # Descripción:
        Para rendición de cuentas de proyectos

=====================================*/

// Inicialización General para casi cualquier módulo
var moduloResource = new RESTfulRequests(SERVER_HOST+'/v1/seguimiento');
var moduloDatagrid = new Datagrid("#datagridAcciones",moduloResource,{ formatogrid:true, pagina: 1, idProyecto: $('#id').val(), grid:'rendicion-acciones'});
moduloDatagrid.init();
moduloDatagrid.actualizar({
    _success: function(response){
        moduloDatagrid.limpiar();
        var datos_grid = [];
        var contador_componente = 0;
        for(var i in response.data.componentes){
            var contador_actividad = 0;
            contador_componente++;
            var componente = response.data.componentes[i];
            var item = {};

            item.id = '1-' + componente.id;
            item.nivel = 'Componente ' + contador_componente;
            item.indicador = componente.indicador;
            item.meta = componente.valorNumerador.format();
            item.avances_mes = 0;
            item.avances_acumulados = 0;

            datos_grid.push(item);
            for(var j in componente.actividades){
                contador_actividad++;
                var actividad = componente.actividades[j];
                var item = {};
                item.id = '2-' + actividad.id;
                item.nivel = 'Actividad ' + contador_componente + '.' + contador_actividad;
                item.indicador = actividad.indicador;
                item.meta = actividad.valorNumerador.format();
                item.avances_mes = 0;
                item.avances_acumulados = 0;
                datos_grid.push(item);
            }
        }
        moduloDatagrid.cargarDatos(datos_grid);                         
        var total = parseInt(response.resultados/moduloDatagrid.rxpag); 
        var plus = parseInt(response.resultados)%moduloDatagrid.rxpag;
        if(plus>0) 
            total++;
        moduloDatagrid.paginacion(total);
    }
});

function seguimiento_metas(e){
    var datos_id = e.split('-');
    if(datos_id[0] == '1'){
        var nivel = 'componente';
    }else{
        var nivel = 'actividad';
    }
    var parametros = {'mostrar':'datos-'+nivel+'-avance'};
    var id = datos_id[1];
    moduloResource.get(id,parametros,{
        _success: function(response){
            $('#modalEditarAvance').find(".modal-title").html("Seguimiento de Metas");

            var total_programado = 0;
            var total_acumulado = 0;
            var total_avance = 0;

            for(var i in response.data.metas_mes_jurisdiccion){
                var dato = response.data.metas_mes_jurisdiccion[i];
                total_programado += dato.meta;
                total_avance += 0;
                total_acumulado += 0;
                $('#tabla-avances-metas > tbody > tr[data-clave-jurisdiccion="'+dato.claveJurisdiccion+'"] > td.meta-programada').attr('data-meta',dato.meta);
                $('#tabla-avances-metas > tbody > tr[data-clave-jurisdiccion="'+dato.claveJurisdiccion+'"] > td.meta-programada').text(dato.meta);
                if(dato.meta > 0){
                    $('#avance_'+dato.claveJurisdiccion).attr('disabled',false);
                }
            }

            $('#total-meta-programada').text(total_programado);
            $('#total-avance-mes').text(total_avance);
            $('#total-avance-acumulado').text(total_acumulado);
            $('#modalEditarAvance').modal('show');
        }
    });    
}
$('.avance-mes').on('keyup',function(){ $(this).change() });
$('.avance-mes').on('change',function(){
    var jurisdiccion = $(this).attr('data-jurisdiccion');
    $('#tabla-avances-metas > tbody > tr[data-clave-jurisdiccion="'+jurisdiccion+'"] > td.avance-acumulado > span.nueva-cantidad').text(' ( + '+$(this).val()+' )');

    var suma = 0;
    $('.avance-mes').each(function(){
        suma += parseInt($(this).val()) || 0;
    });
    $('#total-avance-mes').text(suma);
});

$('#modalEditarAvance').on('hide.bs.modal',function(e){
    $('#form_avance').get(0).reset();
    $('#form_avance input[type="hidden"]').val('');
    $('#form_avance input[type="number"]').attr('disabled',true);
    $('span.nueva-cantidad').text('');
    Validation.cleanFormErrors('#form_avance');
});

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