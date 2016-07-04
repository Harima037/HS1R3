/*=====================================

    # Nombre:
        lista-indicadores-fassa.js

    # Módulos:
        reportes/reporte-indicadores-fassa

    # Descripción:
        Funciones para listar los indicadores de FASSA e imprimir los reportes.

=====================================*/
// Inicialización General para casi cualquier módulo
var moduloResource = new RESTfulRequests(SERVER_HOST+'/v1/reporte-indicadores-fassa');
var moduloDatagrid = new Datagrid("#datagridIndicadores",moduloResource,{ formatogrid:true, pagina: 1, clasificacionProyecto: 1});

moduloDatagrid.init();
moduloDatagrid.actualizar({
    _success: function(response){
        moduloDatagrid.limpiar();
        var datos_grid = [];
        
        for(var i in response.data){
            var item = {};

            item.id = response.data[i].id;
            item.indicador = response.data[i].indicador;
            item.metas = '<div style="text-align:center"><span class="fa fa-times"></span></div>';
            item.trim1 = '<div style="text-align:center"><span class="fa fa-times"></span></div>';
            item.trim2 = '<div style="text-align:center"><span class="fa fa-times"></span></div>';
            item.trim3 = '<div style="text-align:center"><span class="fa fa-times"></span></div>';
            item.trim4 = '<div style="text-align:center"><span class="fa fa-times"></span></div>';
            item.cierre = '<div style="text-align:center"><span class="fa fa-times"></span></div>';
            
            if(response.data[i].idEstatus > 3){
                if(response.data[i].idEstatus == 4){
                    item.metas = '<button onClick="cargarReporteMetas('+item.id+')" class="btn btn-primary" type="button"><span class="fa fa-check"></span></button>';
                }else if(response.data[i].idEstatus == 5){
                    item.metas = '<button onClick="cargarReporteMetas('+item.id+')" class="btn btn-success" type="button"><span class="fa fa-pencil"></span></button>';
                }
            }

            if(response.data[i].registro_avance){
                for(var j in response.data[i].registro_avance){
                    var eval = response.data[i].registro_avance[j];
                    var trimestre = parseInt(eval.mes/3);
                    if(eval.idEstatus == 4){
                        item['trim'+trimestre] = '<button onClick="cargarReporte('+item.id+','+eval.mes+')" class="btn btn-primary" type="button"><span class="fa fa-check"></span></button>';
                    }else{
                        item['trim'+trimestre] = '<button onClick="cargarReporte('+item.id+','+eval.mes+')" class="btn btn-success" type="button"><span class="fa fa-pencil"></span></button>';
                    }
                }
            }
            if(response.data[i].idEstatusCierre){
                if(response.data[i].idEstatusCierre == 4){
                    item.cierre = '<button onClick="cargarReporteCierre('+item.id+')" class="btn btn-primary" type="button"><span class="fa fa-check"></span></button>';
                }else if(response.data[i].idEstatusCierre == 5){
                    item.cierre = '<button onClick="cargarReporteCierre('+item.id+')" class="btn btn-success" type="button"><span class="fa fa-pencil"></span></button>';
                }
            }
            datos_grid.push(item);
        }
        moduloDatagrid.cargarDatos(datos_grid);                         
        moduloDatagrid.cargarTotalResultados(response.resultados,'<b>Indicador(es)</b>');
        var total = parseInt(response.resultados/moduloDatagrid.rxpag); 
        var plus = parseInt(response.resultados)%moduloDatagrid.rxpag;
        if(plus>0) 
            total++;
        moduloDatagrid.paginacion(total);
    },
    _error: function(jqXHR){
        //console.log('{ error => "'+jqXHR.status +' - '+ jqXHR.statusText +'", response => "'+ jqXHR.responseText +'" }'); 
        var json = $.parseJSON(jqXHR.responseText);
        if(json.code == "W00"){
            moduloDatagrid.limpiar();
            moduloDatagrid.cargarTotalResultados(0,'<b>Indicador(es)</b>');
            var colspan = $(moduloDatagrid.selector + " thead > tr th").length;
            $(moduloDatagrid.selector + " tbody").append("<tr><td colspan='"+colspan+"' style='text-align:left'><i class='fa fa-info-circle'></i> "+json.data+"</td></tr>");
        }else{
            json.type = 'ERR';
            MessageManager.show(json);
            moduloDatagrid.limpiar();
        }
        
    }
});

function cargar_datos_indicador(){}

$("#datagridIndicadores .txt-quick-search").off('keydown');
$("#datagridIndicadores .txt-quick-search").on('keydown', function(event){
    if (event.which == 13) {
        realizar_busqueda();
    }
});

$('#datagridIndicadores .btn-quick-search').off('click');
$('#datagridIndicadores .btn-quick-search').on('click',function(){
    realizar_busqueda();
})

function realizar_busqueda(){
    moduloDatagrid.setPagina(1);
    moduloDatagrid.parametros.buscar = $('.txt-quick-search').val();
    moduloDatagrid.parametros.ejercicio = $('#ejercicio').val();
    moduloDatagrid.actualizar();
}

function cargarReporte(id,mes){
    var parametros = id + '?mes='+mes;
    window.open(SERVER_HOST+'/v1/reporte-fassa/'+parametros);
}

function cargarReporteCierre(id){
    var parametros = id + '?cierre=1';
    window.open(SERVER_HOST+'/v1/reporte-fassa/'+parametros);
}

function cargarReporteMetas(id){
    var parametros = id + '?metas='+1;
    window.open(SERVER_HOST+'/v1/reporte-fassa/'+parametros);
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