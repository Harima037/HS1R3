/*=====================================

    # Nombre:
        evaluacion-proyectos.js

    # Módulo:
        reportes/evaluacion-proyectos

    # Descripción:
        Para imprimir el libro de evaluacion de proyectos

=====================================*/

// Inicialización General para casi cualquier módulo
var moduleResource = new RESTfulRequests(SERVER_HOST+'/v1/evaluacion-proyectos');
/*===================================*/
// Implementación personalizada del módulo

function editar (e){}

$("#datagridProyectos .txt-quick-search").off('keydown');
$("#datagridProyectos .txt-quick-search").on('keydown', function(event){
    if (event.which == 13) {
        realizar_busqueda();
    }
});

$('#datagridProyectos .btn-quick-search').off('click');
$('#datagridProyectos .btn-quick-search').on('click',function(){
    realizar_busqueda();
})

function realizar_busqueda(){
    /*
    moduleDatagrid.setPagina(1);
    moduleDatagrid.parametros.buscar = $('.txt-quick-search').val();
    moduleDatagrid.parametros.ejercicio = $('#ejercicio').val();
    moduleDatagrid.parametros.mes = $('#mes').val();
    moduleDatagrid.actualizar();
    */
}

/*===================================*/
// Configuración General para cualquier módulo
$('#btn-ver-reporte').on('click',function(){
    var parametros = '?mes='+$('#mes').val()+'&ejercicio='+$('#ejercicio').val();
    if($('.txt-quick-search').val()){
        parametros += '&buscar='+$('.txt-quick-search').val();
    }
    window.open(SERVER_HOST+'/v1/evaluacion-proyectos'+parametros);
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
