/*=====================================

    # Nombre:
        lista-proyectos-rendicion.js

    # Módulos:
        seguimiento/seguimiento-inst
        seguimiento/seguimiento-inv

    # Descripción:
        Funciones para seguimiento de metas de proyectos institucionales y de inversión

=====================================*/
moduloDatagrid.init();
moduloDatagrid.actualizar({
    _success: function(response){
        moduloDatagrid.limpiar();
        var datos_grid = [];
        var mes_activo = parseInt($('#datagridProyectos').attr('data-mes-activo'));
        var mes_actual = parseInt($('#datagridProyectos').attr('data-mes-actual'));

        var trimestre = $('#datagridProyectos').attr('data-trim-activo');

        for(var i in response.data){
            var item = {};

            item.id = response.data[i].id;
            item.clave = response.data[i].clavePresup;
            item.nombre_tecnico = response.data[i].nombreTecnico;
            item.revisor = response.data[i].nombreRevisor || '<span class="text-muted">Sin revisor asignado</span>';
            datos_grid.push(item);
        }
        moduloDatagrid.cargarDatos(datos_grid);                         
        moduloDatagrid.cargarTotalResultados(response.resultados,'<b>Proyecto(s)</b>');
        var total = parseInt(response.resultados/moduloDatagrid.rxpag); 
        var plus = parseInt(response.resultados)%moduloDatagrid.rxpag;
        if(plus>0) 
            total++;
        moduloDatagrid.paginacion(total);
    }
});

function cargar_datos_proyecto(e){
    $('#modalDatosSeguimiento').modal('show');
    return false;
    var parametros = {'mostrar':'datos-proyecto-avance'};
    moduloResource.get(e,parametros,{
        _success: function(response){
            //
        }
    });
}

//rend-cuenta-inst-editar
$('#btn-reporte-general').off('click');
$('#btn-reporte-general').on('click',function(){
    var parametros = $('#btn-editar-avance').attr('data-id-proyecto') + '|seg-metas';
    window.open(SERVER_HOST+'/v1/reporte-evaluacion/'+parametros);
});
$('#btn-reporte-beneficiarios').off('click');
$('#btn-reporte-beneficiarios').on('click',function(){
    var parametros = $('#btn-editar-avance').attr('data-id-proyecto') + '|seg-beneficiarios';
    window.open(SERVER_HOST+'/v1/reporte-evaluacion/'+parametros);
});
$('#btn-reporte-plan-mejora').off('click');
$('#btn-reporte-plan-mejora').on('click',function(){
    var parametros = $('#btn-editar-avance').attr('data-id-proyecto') + '|plan-mejora';
    window.open(SERVER_HOST+'/v1/reporte-evaluacion/'+parametros);
});
$('#btn-reporte-analisis').off('click');
$('#btn-reporte-analisis').on('click',function(){
    var parametros = $('#btn-editar-avance').attr('data-id-proyecto') + '|analisis';
    window.open(SERVER_HOST+'/v1/reporte-evaluacion/'+parametros);
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