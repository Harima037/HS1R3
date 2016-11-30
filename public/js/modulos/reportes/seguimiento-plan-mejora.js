/*=====================================

    # Nombre:
        seguimiento-plan-mejora.js

    # Módulo:
        reportes/seguimiento-plan-mejora

    # Descripción:
        Imprimir reporte de Seguimiento de Planes de Mejora

=====================================*/

// Inicialización General para casi cualquier módulo
var moduleResource = new RESTfulRequests(SERVER_HOST+'/v1/seguimiento-plan-mejora');
var moduleDatagrid = new Datagrid("#datagridPlanesMejora",moduleResource,{formatogrid:true,pagina:1,ejercicio:$('#ejercicio').val(),trimestre:$('#trimestre').val(),mes:$('#mes').val()});
moduleDatagrid.init();
moduleDatagrid.actualizar({
    _success: function(response){
        moduleDatagrid.limpiar();
        var datos_grid = [];
        for(var i in response.data){
            var item = {};

            item.id = response.data[i].id;
            item.areaResponsable = response.data[i].areaResponsable;
            item.indicador = response.data[i].indicador;
            item.fechaNotificacion = response.data[i].fechaNotificacion;
            item.avance = response.data[i].avance;
            
            datos_grid.push(item);
        }
        moduleDatagrid.cargarDatos(datos_grid);                         
        moduleDatagrid.cargarTotalResultados(response.resultados,'<b>Plan(es) de Mejora</b>');
        var total = parseInt(response.resultados/moduleDatagrid.rxpag); 
        var plus = parseInt(response.resultados)%moduleDatagrid.rxpag;
        if(plus>0) 
            total++;
        moduleDatagrid.paginacion(total);
    }
});
/*===================================*/
// Implementación personalizada del módulo

function editar (e){
}

$("#datagridPlanesMejora .txt-quick-search").off('keydown');
$("#datagridPlanesMejora .txt-quick-search").on('keydown', function(event){
    if (event.which == 13) {
        realizar_busqueda();
    }
});

$('#datagridPlanesMejora .btn-quick-search').off('click');
$('#datagridPlanesMejora .btn-quick-search').on('click',function(){
    realizar_busqueda();
})

function realizar_busqueda(){
    moduleDatagrid.setPagina(1);
    moduleDatagrid.parametros.buscar = $('.txt-quick-search').val();
    moduleDatagrid.parametros.ejercicio = $('#ejercicio').val();
    moduleDatagrid.parametros.trimestre = $('#trimestre').val();
    moduleDatagrid.parametros.mes = $('#mes').val();
    moduleDatagrid.actualizar();
}

/*===================================*/
// Configuración General para cualquier módulo
$('#btn-ver-reporte').on('click',function(){
    /*
    var parametros = '?mes='+$('#mes').val()+'&ejercicio='+$('#ejercicio').val();
    if($('.txt-quick-search').val()){
        parametros += '&buscar='+$('.txt-quick-search').val();
    }
    window.open(SERVER_HOST+'/v1/evaluacion-proyectos-reporte'+parametros);
    */
});
/*===================================*/
// Funciones adicionales por módulo

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
