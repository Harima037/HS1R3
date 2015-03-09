/*=====================================

    # Nombre:
        institucional.js

    # Módulo:
        seguimiento/seguimiento-inst

    # Descripción:
        Para seguimiento de metas de proyectos institucionales

=====================================*/

// Inicialización General para casi cualquier módulo
var moduloResource = new RESTfulRequests(SERVER_HOST+'/v1/seguimiento');
var moduloDatagrid = new Datagrid("#datagridProyectos",moduloResource,{ formatogrid:true, pagina: 1, clasificacionProyecto: 1});
moduloDatagrid.init();
moduloDatagrid.actualizar({
    _success: function(response){
        moduloDatagrid.limpiar();
        var datos_grid = [];
        for(var i in response.data){
            var item = {};
            var clase_label = 'label-info';
            if(response.data[i].idEstatusProyecto == 2){
                clase_label = 'label-warning';
            }else if(response.data[i].idEstatusProyecto == 3){
                clase_label = 'label-danger';
            }else if(response.data[i].idEstatusProyecto == 4){
                clase_label = 'label-primary';
            }

            item.id = response.data[i].id;
            item.clave = response.data[i].clavePresup;
            item.nombre_tecnico = response.data[i].nombreTecnico;

            item.primerMes = '<span class="label label-info"><span class="fa fa-square-o"></span> ENERO</span>';
            item.segundoMes = '<span class="label label-info"><span class="fa fa-square-o"></span> FEBRERO</span>';
            item.tercerMes = '<span class="label label-info"><span class="fa fa-square-o"></span> MARZO</span>';
            
            item.estatus = '<span class="label label-info"><span class="fa fa-square-o"></span> Trimestre</span>';

            datos_grid.push(item);
        }
        moduloDatagrid.cargarDatos(datos_grid);                         
        var total = parseInt(response.resultados/moduloDatagrid.rxpag); 
        var plus = parseInt(response.resultados)%moduloDatagrid.rxpag;
        if(plus>0) 
            total++;
        moduloDatagrid.paginacion(total);
    }
});

function cargar_datos_proyecto(e){
    $('#modalDatosSeguimiento').find(".modal-title").html("Datos del Proyecto Institucional");
    $('#modalDatosSeguimiento').modal('show');
    /*var parametros = {ver:'proyecto'};
    moduloResource.get(e,parametros,{
        _success: function(response){
            //
        }
    });*/
}

/*
$('#modalDatosSeguimiento').on('shown.bs.modal', function () {
    $('#modalDatosSeguimiento').find('input').eq(0).focus();
});
*/
/*
$('#modalDatosSeguimiento').on('hide.bs.modal',function(e){ 
    resetModalModuloForm();
});
*/