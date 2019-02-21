/*=====================================

    # Nombre:
        revision-programas.js

    # Módulo:
        revision/revision-estrategia-institucional

    # Descripción:
        Se utiliza para comentar los indicadores de las estrategias institucionales

=====================================*/

// Inicialización General para casi cualquier módulo
var moduloResource = new RESTfulRequests(SERVER_HOST+'/v1/revision-estrategia');
var moduloDatagrid = new Datagrid("#datagridRevisionEstrategia",moduloResource);
moduloDatagrid.init();
moduloDatagrid.actualizar({
    _success: function(response){
        console.log(response);
        moduloDatagrid.limpiar();
        var datos_grid = [];
        for(var i in response.data){
            var item = {};
            var clase_label = 'label-info';
            if(response.data[i].idEstatus == 2){
                clase_label = 'label-warning';
            }else if(response.data[i].idEstatus == 3){
                clase_label = 'label-danger';
            }else if(response.data[i].idEstatus == 4){
                clase_label = 'label-primary';
            }

            item.id = response.data[i].id;
            item.programa = response.data[i].clave + ' ' + response.data[i].estrategia;
            item.descripcion = response.data[i].descripcion;
            item.estatus = '<span class="label ' + clase_label + '">' + response.data[i].estatus + '</span>';
            item.usuario = response.data[i].username;
            item.fecha_modificado = response.data[i].modificadoAl.substring(0,11);

            datos_grid.push(item);
        }
        moduloDatagrid.cargarDatos(datos_grid);                         
        moduloDatagrid.cargarTotalResultados(response.resultados,'<b> Estrategias Institucionales(s)</b>');
        var total = parseInt(response.resultados/moduloDatagrid.rxpag); 
        var plus = parseInt(response.resultados)%moduloDatagrid.rxpag;
        if(plus>0) 
            total++;
        moduloDatagrid.paginacion(total);
    }
});


function cargar_datos_estrategia(e){
    window.location.href = SERVER_HOST+'/revision/revision-ver-estrategia/'+e;
}