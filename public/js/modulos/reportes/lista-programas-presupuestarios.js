/*=====================================

    # Nombre:
        lista-proyectos-rendicion.js

    # Módulos:
        seguimiento/seguimiento-inst
        seguimiento/seguimiento-inv

    # Descripción:
        Funciones para seguimiento de metas de proyectos institucionales y de inversión

=====================================*/
// Inicialización General para casi cualquier módulo
var moduloResource = new RESTfulRequests(SERVER_HOST+'/v1/reporte-seg-programas');
var moduloDatagrid = new Datagrid("#datagridProgramas",moduloResource,{ formatogrid:true, pagina: 1});

moduloDatagrid.init();
moduloDatagrid.actualizar({
    _success: function(response){
        moduloDatagrid.limpiar();
        var datos_grid = [];
        
        for(var i in response.data){
            var item = {};

            item.id = response.data[i].id;
            item.clave = response.data[i].clave;
            item.programa = response.data[i].programaPresupuestario;
            item.trim1 = '<span class="fa fa-times"></span>';
            item.trim2 = '<span class="fa fa-times"></span>';
            item.trim3 = '<span class="fa fa-times"></span>';
            item.trim4 = '<span class="fa fa-times"></span>';
            
            if(response.data[i].evaluacion_trimestre){
                for(var j in response.data[i].evaluacion_trimestre){
                    var eval = response.data[i].evaluacion_trimestre[j];
                    if(eval.idEstatus == 4){
                        item['trim'+eval.trimestre] = '<button onClick="cargarReporte('+item.id+','+eval.trimestre+')" class="btn btn-primary" type="button"><span class="fa fa-check"></span></button>';
                    }else{
                        item['trim'+eval.trimestre] = '<button onClick="cargarReporte('+item.id+','+eval.trimestre+')" class="btn btn-success" type="button"><span class="fa fa-pencil"></span></button>';
                    }
                }
            }
            datos_grid.push(item);
        }
        moduloDatagrid.cargarDatos(datos_grid);                         
        moduloDatagrid.cargarTotalResultados(response.resultados,'<b>Programas(s)</b>');
        var total = parseInt(response.resultados/moduloDatagrid.rxpag); 
        var plus = parseInt(response.resultados)%moduloDatagrid.rxpag;
        if(plus>0) 
            total++;
        moduloDatagrid.paginacion(total);
    }
});

function cargarReporte(id,trimestre){
    var parametros = id + '?trimestre='+trimestre;
    window.open(SERVER_HOST+'/v1/reporte-programa/'+parametros);
}

function cargar_datos_programa(){}

$("#datagridProgramas .txt-quick-search").off('keydown');
$("#datagridProgramas .txt-quick-search").on('keydown', function(event){
    if (event.which == 13) {
        realizar_busqueda();
    }
});

$('#datagridProgramas .btn-quick-search').off('click');
$('#datagridProgramas .btn-quick-search').on('click',function(){
    realizar_busqueda();
})

function realizar_busqueda(){
    moduloDatagrid.setPagina(1);
    moduloDatagrid.parametros.buscar = $('.txt-quick-search').val();
    moduloDatagrid.parametros.ejercicio = $('#ejercicio').val();
    moduloDatagrid.actualizar();
}

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