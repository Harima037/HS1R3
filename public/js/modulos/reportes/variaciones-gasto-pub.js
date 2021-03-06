/*=====================================

    # Nombre:
        variaciones-gasto-pub.js

    # Módulo:
        reportes/variaciones-gasto-pub

    # Descripción:
        Para imprimir reporte de variaciones de gasto público

=====================================*/

// Inicialización General para casi cualquier módulo

//var presupuestosIguales = [];
var moduleResource = new RESTfulRequests(SERVER_HOST+'/v1/variacion-gasto');
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
			item.presupApr = '$ ' + parseFloat(response.data[i].presupuestoAprobado || 0).format(2);		
            item.presupMod = '$ ' + parseFloat(response.data[i].presupuestoModificado || 0).format(2);
			item.presupDev = '$ ' + parseFloat(response.data[i].presupuestoDevengadoModificado || 0).format(2);
			
			
			/*if(item.presupMod == item.presupApr)
				presupuestosIguales.push(item);*/
			
			if(response.data[i].razonesAprobado)
				item.razones = '<span class="label label-info">Registradas</span>';
			else
				item.razones = '<span class="label label-default">Sin registro</span>';
            
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
	
	$('#mes-razones').val($('#mes').val());
	$('#id').val(e);
	$('#razones').val('');
	$('#razones2').val('');

	var parametros = {'mes':$('#mes-razones').val()};
	
	/*for(var i in presupuestosIguales)
		if(presupuestosIguales[i].id == e)
		{
			MessageManager.show({data:'No es posible escribir razones si los presupuestos aprobado y modificado son iguales.',type:'OK',timer:3});
			return;
		}*/
	
	moduleResource.get(e,parametros,{
        _success: function(response){
			if(response.data[0])
			{
				$('#razones').val(response.data[0].razonesAprobado);
				$('#razones2').val(response.data[0].razonesDevengado);
			}
        }
   	});
		
	$('#modalRazones').modal('show');
}

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
    window.open(SERVER_HOST+'/v1/reporte-variacion-gasto'+parametros);
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
