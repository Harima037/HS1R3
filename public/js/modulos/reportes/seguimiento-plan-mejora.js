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
            item.clave = response.data[i].clave;
            item.nombreTecnico = response.data[i].nombreTecnico;
            item.indicador = response.data[i].indicador;
            //item.fechaNotificacion = response.data[i].fechaNotificacion;
            item.porcentaje = (parseFloat(response.data[i].porcentaje) || 0).format(2) + ' %';
            if(parseInt(response.data[i].identificacionDocumentoProbatorio)){
                item.identificacionDocumentoProbatorio = '<i class="fa fa-check-square-o"></i>';
            }else{
                item.identificacionDocumentoProbatorio = '<i class="fa fa-square-o"></i>';
            }
            
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
    if($('.txt-quick-search').val() != ''){
        moduleDatagrid.parametros.buscar = $('.txt-quick-search').val();
    }else{
        delete moduleDatagrid.parametros.buscar;
    }
    moduleDatagrid.parametros.ejercicio = $('#ejercicio').val();
    moduleDatagrid.parametros.trimestre = $('#trimestre').val();
    moduleDatagrid.parametros.mes = $('#mes').val();
    moduleDatagrid.actualizar();
}

$('#btnDocumentoProbatorio').on('click',function (e) {
    e.preventDefault();
    row_ids = [];
    $(moduleDatagrid.selector).find("tbody").find("input[type=checkbox]:checked").each(function () {
        row_ids.push($(this).parent().parent().data("id"));
    });

    if (row_ids.length > 0) {
        var parametros = {'ids':row_ids};
        if($('#identificacion').val() == 1){
            parametros.identificacion = 1;
        }else{
            parametros.identificacion = 0;
        }
        moduleResource.put(0, parametros,{
            _success: function(response){
                moduleDatagrid.actualizar();
                MessageManager.show({data:'Planes actualizados con éxito.',type:'OK',timer:4});
            }
        });
    }else {
        MessageManager.show({code:'W00',data:"Seleccione al menos un plan de mejora.",timer:5});
    }
});

/*===================================*/
// Configuración General para cualquier módulo
$('#btn-descargar-reporte').on('click',function(){
    var parametros = '?formatogrid=1&pagina=0&excel=1&mes='+$('#mes').val()+'&ejercicio='+$('#ejercicio').val()+'&trimestre='+$('#trimestre').val();
    if($('.txt-quick-search').val()){
        parametros += '&buscar='+$('.txt-quick-search').val();
    }
    window.open(SERVER_HOST+'/v1/seguimiento-plan-mejora'+parametros);
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
