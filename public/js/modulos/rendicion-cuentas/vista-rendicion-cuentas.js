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
var beneficiariosDatagrid = new Datagrid("#datagridBeneficiarios",moduloResource,{ formatogrid:true, pagina: 1, idProyecto: $('#id').val(), grid:'rendicion-beneficiarios'});
var accionesDatagrid = new Datagrid("#datagridAcciones",moduloResource,{ formatogrid:true, pagina: 1, idProyecto: $('#id').val(), grid:'rendicion-acciones'});

beneficiariosDatagrid.init();
beneficiariosDatagrid.actualizar({
    _success: function(response){
        beneficiariosDatagrid.limpiar();
        var datos_grid = {};

        for(var i in response.data){
            var beneficiario = response.data[i];

            if(!datos_grid[beneficiario.idTipoBeneficiario]){
                datos_grid[beneficiario.idTipoBeneficiario] = {
                    'id': beneficiario.idTipoBeneficiario,
                    'tipoBeneficiario': beneficiario.tipo_beneficiario.descripcion,
                    'f': 0,
                    'f-avance':0,
                    'm': 0,
                    'm-avance':0,
                    'total': 0,
                    'total-avance':0
                };
            }

            datos_grid[beneficiario.idTipoBeneficiario][beneficiario.sexo] += beneficiario.total;
            datos_grid[beneficiario.idTipoBeneficiario]['total'] += beneficiario.total;
        }
        var datos = [];

        for(var i in datos_grid){
            datos_grid[i].f = datos_grid[i].f.format();
            datos_grid[i].m = datos_grid[i].m.format();
            datos_grid[i].total = datos_grid[i].total.format();
            datos_grid[i]['f-avance'] = datos_grid[i]['f-avance'].format();
            datos_grid[i]['m-avance'] = datos_grid[i]['m-avance'].format();
            datos_grid[i]['total-avance'] = datos_grid[i]['total-avance'].format();

            datos.push(datos_grid[i]);
        }
        
        beneficiariosDatagrid.cargarDatos(datos);                         
        var total = parseInt(datos.length/beneficiariosDatagrid.rxpag); 
        var plus = parseInt(datos.length)%beneficiariosDatagrid.rxpag;
        if(plus>0) 
            total++;
        beneficiariosDatagrid.paginacion(total);
    }
});

accionesDatagrid.init();
accionesDatagrid.actualizar({
    _success: function(response){
        accionesDatagrid.limpiar();
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
        accionesDatagrid.cargarDatos(datos_grid);                         
        var total = parseInt(response.resultados/accionesDatagrid.rxpag); 
        var plus = parseInt(response.resultados)%accionesDatagrid.rxpag;
        if(plus>0) 
            total++;
        accionesDatagrid.paginacion(total);
    }
});

function seguimiento_beneficiarios(e){
    $('#modalBeneficiario').find(".modal-title").html("Seguimiento de Beneficiarios");
    $('#modalBeneficiario').modal('show');
}

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
    $('#tabla-avances-metas > tbody > tr[data-clave-jurisdiccion="'+jurisdiccion+'"] > td.avance-acumulado > span.nueva-cantidad').text(' ( + '+($(this).val() || 0)+' )');

    var acumulado = $('#tabla-avances-metas > tbody > tr[data-clave-jurisdiccion="'+jurisdiccion+'"] > td.avance-acumulado').attr('data-acumulado');
    acumulado += parseFloat($(this).val()) || 0;
    var total_programado = $('#tabla-avances-metas > tbody > tr[data-clave-jurisdiccion="'+jurisdiccion+'"] > td.meta-programada').attr('data-meta');
    var nuevo_porcentaje = ((acumulado * 100) / total_programado).toFixed(2);
    $('#tabla-avances-metas > tbody > tr[data-clave-jurisdiccion="'+jurisdiccion+'"] > td.porcentaje-acumulado > span.nueva-cantidad').text(' ( '+parseFloat(nuevo_porcentaje)+' % )');

    var suma = 0;
    $('.avance-mes').each(function(){
        suma += parseFloat($(this).val()) || 0;
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