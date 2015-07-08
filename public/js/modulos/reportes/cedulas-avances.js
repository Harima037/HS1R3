/*=====================================

    # Nombre:
        cedulas-avances.js

    # Módulo:
        reportes/cedulas-avances

    # Descripción:
        Para imprimir reporte de cédulas de avances

=====================================*/

// Inicialización General para casi cualquier módulo

var moduleResource = new RESTfulRequests(SERVER_HOST+'/v1/cedulas-avances');
var moduleDatagrid = new Datagrid("#datagridProyectos",moduleResource,{formatogrid:true,pagina:1,ejercicio:$('#ejercicio').val(),mes:$('#mes').val()});
moduleDatagrid.init();
moduleDatagrid.actualizar({
    _success: function(response){
        moduleDatagrid.limpiar();
        var datos_grid = [];
        for(var i in response.data){
            var item = {};

            item.id = response.data[i].id;
            item.clave = response.data[i].ClavePresupuestaria;
            item.nombre_tecnico = response.data[i].nombreTecnico;
            item.presupMod = '$ ' + parseFloat(response.data[i].presupuestoModificado).format(2);
            item.presupEjer = '$ ' + parseFloat(response.data[i].presupuestoEjercidoModificado).format(2);
            
            datos_grid.push(item);
        }
        moduleDatagrid.cargarDatos(datos_grid);                         
        moduleDatagrid.cargarTotalResultados(response.resultados,'<b>Proyecto(s)</b>');
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
    //$('#modalSeguimiento').find(".modal-title").html("Detalles Seguimiento");
    //$('#modalSeguimiento').modal('show');
    /*moduleResource.get(e,null,{
        _success: function(response){
            $('#modalSeguimiento').find(".modal-title").html("Detalles Seguimiento");
            $('#modalSeguimiento').modal('show');
        }
    });*/
}

$("#datagridSeguimientos .txt-quick-search").off('keydown');
$("#datagridSeguimientos .txt-quick-search").on('keydown', function(event){
    if (event.which == 13) {
        realizar_busqueda();
    }
});

$('#datagridSeguimientos .btn-quick-search').off('click');
$('#datagridSeguimientos .btn-quick-search').on('click',function(){
    realizar_busqueda();
})

function realizar_busqueda(){
    moduleDatagrid.setPagina(1);
    moduleDatagrid.parametros.buscar = $('.txt-quick-search').val();
    moduleDatagrid.parametros.ejercicio = $('#ejercicio').val();
    moduleDatagrid.parametros.mes = $('#mes').val();
    moduleDatagrid.actualizar();
}

/*===================================*/
// Configuración General para cualquier módulo
$('#btn-ver-cedulas').on('click',function(){
    var parametros = '?mes='+$('#mes').val()+'&ejercicio='+$('#ejercicio').val();
    if($('.txt-quick-search').val()){
        parametros += '&buscar='+$('.txt-quick-search').val();
    }
    window.open(SERVER_HOST+'/v1/cedulas-avances'+parametros);
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
