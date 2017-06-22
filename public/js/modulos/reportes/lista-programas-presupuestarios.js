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
var moduloResourceFirmar = new RESTfulRequests(SERVER_HOST+'/v1/seguimiento-programas');
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
            item.clave = response.data[i].clave;
            item.programa = response.data[i].programaPresupuestario;
            item.trim1 = '<span class="fa fa-times"></span>';
            item.trim2 = '<span class="fa fa-times"></span>';
            item.trim3 = '<span class="fa fa-times"></span>';
            item.trim4 = '<span class="fa fa-times"></span>';
            
            
            /*
            algo='<div class="btn-group"><button type="button" class="btn btn-success dropdown-toggle" data-toggle="dropdown"><span class="caret"></span></button><ul class="dropdown-menu pull-right" role="menu"><li><a href="#" onClick="cargarReporte('+item.id+','+eval.trimestre+')" class="btn-edit-rows" type="button"><span class="fa '+icono+'"></span> Imprimir</a></li>'+firmar+'</ul></div>';

            */
            if(response.data[i].evaluacion_trimestre){
                for(var j in response.data[i].evaluacion_trimestre){
                    var eval = response.data[i].evaluacion_trimestre[j];
                    if(eval.idEstatus == 4){
                        /*item['trim'+eval.trimestre] = '<button onClick="cargarReporte('+item.id+','+eval.trimestre+')" class="btn btn-primary" type="button"><span class="fa fa-check"></span></button>';
                        */
                        firmar='<li><a href="#" onClick="firmarProyecto('+eval.trimestre+','+item.id+')" class="btn-edit-rows"><span class="glyphicon glyphicon-edit"></span> Firmar</a> </li>';

                        tmp='<div class="btn-group" style="position:absolute"><button type="button" class="btn btn-success dropdown-toggle" data-toggle="dropdown" style="width:24pt;height:20pt"><span class="caret"></span></button><ul class="dropdown-menu pull-right" role="menu"><li><a href="#" onClick="cargarReporte('+item.id+','+eval.trimestre+')" class="btn-edit-rows" type="button"><span class="fa fa-check "></span> Imprimir</a></li>'+firmar+'</ul></div>';
                    }else{
                        tmp='<div class="btn-group" style="position:absolute"><button type="button" class="btn btn-success dropdown-toggle" data-toggle="dropdown" style="width:24pt;height:20pt"><span class="caret"></span></button><ul class="dropdown-menu pull-right" role="menu"><li><a href="#" onClick="cargarReporte('+item.id+','+eval.trimestre+')" class="btn-edit-rows" type="button"><span class="fa fa-pencil"></span> Imprimir</a></li></ul></div>';
                    }
                    item['trim'+eval.trimestre]=tmp;
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

function firmarProyecto(trim,proy){
   // alert(trim+" "+proy);
    var parametros = 'actualizarprograma=firmar&trim_firma='+trim;   
    Confirm.show({
                titulo:"¿Poner en estatus de firma el avance de este trimestre?",
                mensaje: "¿Estás seguro de poner el estatus de firma el avance del trimestre actual? Una vez realizado esto, no es posible comentar o corregir por el trimestre corriente.",
                callback: function(){
                    moduloResourceFirmar.put(proy,parametros,{
                        _success: function(response){
                             moduloDatagrid.actualizar();
                            MessageManager.show({data:'Se ha firmado correctamente',type:'OK',timer:3});
                            //window.location.href = SERVER_HOST+'/revision/seguimiento-programas';
                        },
                        _error: function(response){
                            try{
                                var json = $.parseJSON(response.responseText);
                                if(!json.code)
                                    MessageManager.show({code:'S03',data:"Hubo un problema al realizar la transacción, inténtelo de nuevo o contacte con soporte técnico."});
                                else{
                                    MessageManager.show(json);
                                }
                                Validation.formValidate(json.data);
                            }catch(e){
                                console.log(e);
                            }                       
                        }
                    });                 
                }
    });
}