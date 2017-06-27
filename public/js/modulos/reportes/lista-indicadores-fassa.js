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
var moduloResourceProyecto = new RESTfulRequests(SERVER_HOST+'/v1/revision-rendicion-fassa');
var firmar="";
var tmp="";

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
                        firmar='<li><a href="#" onClick="firmarProyecto('+item.id+','+"'avance'"+')" class="btn-edit-rows"><span class="glyphicon glyphicon-edit"></span> Firmar</a> </li>';

                        tmp='<div class="btn-group" style="position:absolute"><button type="button" class="btn btn-success dropdown-toggle" data-toggle="dropdown" style="width:24pt;height:20pt"><span class="caret"></span></button><ul class="dropdown-menu pull-right" role="menu"><li><a href="#" onClick="cargarReporte('+item.id+','+eval.mes+')" class="btn-edit-rows" type="button"><span class="fa fa-check "></span> Imprimir</a></li>'+firmar+'</ul></div>';

                        /*item['trim'+trimestre] = '<button onClick="cargarReporte('+item.id+','+eval.mes+')" class="btn btn-primary" type="button"><span class="fa fa-check"></span></button>';*/
                    }else{
                        /*item['trim'+trimestre] = '<button onClick="cargarReporte('+item.id+','+eval.mes+')" class="btn btn-success" type="button"><span class="fa fa-pencil"></span></button>';*/
                        tmp='<div class="btn-group" style="position:absolute"><button type="button" class="btn btn-success dropdown-toggle" data-toggle="dropdown" style="width:24pt;height:20pt"><span class="caret"></span></button><ul class="dropdown-menu pull-right" role="menu"><li><a href="#" onClick="cargarReporte('+item.id+','+eval.mes+')" class="btn-edit-rows" type="button"><span class="fa fa-check "></span> Imprimir</a></li></ul></div>';
                    }
                    item['trim'+trimestre] =tmp;
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

function cargarReporteMetas(id){
    var parametros = id + '?metas='+1;
    window.open(SERVER_HOST+'/v1/reporte-fassa/'+parametros);
}

function firmarProyecto(id,tipoRevision){
    alert("SUMAMI");
///////////////////////////
    var firmar = 0;
  
    /* if(tipoRevision == 'meta')
    {
        if($('#id-estatus-meta').val()!=4)
            MessageManager.show({data:'Debido al estatus que guarda la meta no es posible firmarla actualmente.',type:'ADV',timer:3});
        else
        {
            if(comentariosArray.length>0)
                MessageManager.show({data:'No es posible firmar la meta si tiene comentarios pendientes. Elimine primero todos los comentarios.',type:'ADV',timer:3});
            else
                firmar = 1;
        }
    }
    else
    if(tipoRevision == 'avance')
    {
        if($('#id-estatus-avance').val()!=4)
            MessageManager.show({data:'Debido al estatus que guarda el avance no es posible firmarlo actualmente.',type:'ADV',timer:3});
        else
        {
            if(comentariosArray.length>0)
                MessageManager.show({data:'No es posible firmar el comentario si tiene comentarios pendientes. Elimine primero todos los comentarios.',type:'ADV',timer:3});
            else
                firmar = 1;
        }
    }
    */
   
        Confirm.show({
            titulo:"Firmar Avance",
            mensaje: "¿Estás seguro de firmar el avance seleccionada(o)?",
            callback: function(){
                var parametros = {'actualizarproyecto':'firmar','tiporevision':tipoRevision,'idavance':id};
                moduloResourceProyecto.put(id,parametros,{
                        _success: function(response){
                            MessageManager.show({data:'Se ha firmado con éxito la Meta/avance seleccionado',type:'OK',timer:3});                    
                            moduloDatagrid.actualizar();
                            $('#modalIndicador').modal('hide');
                        }
                });
            }
        });         
    
//////////////////////

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