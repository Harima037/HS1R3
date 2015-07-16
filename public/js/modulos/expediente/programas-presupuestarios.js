/*=====================================

    # Nombre:
        programas-presupuestarios.js

    # Módulo:
        expediente/programas-presupuestarios

    # Descripción:
        Se utiliza para crear, editar y eliminar los indicadores de los programas presupuestarios

=====================================*/

// Inicialización General para casi cualquier módulo
var moduloResource = new RESTfulRequests(SERVER_HOST+'/v1/programas-presupuestarios');
var moduloDatagrid = new Datagrid("#datagridProgramas",moduloResource);
moduloDatagrid.init();
moduloDatagrid.actualizar({
    _success: function(response){
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
            }else if(response.data[i].idEstatus == 5){
                clase_label = 'label-success';
            }

            item.id = response.data[i].id;
            item.programa = response.data[i].clave + ' ' + response.data[i].programa;
            item.fecha_inicion = response.data[i].fechaInicio;
            item.fecha_termino = response.data[i].fechaTermino;
            item.estatus = '<span class="label ' + clase_label + '">' + response.data[i].estatus + '</span>';
            item.usuario = response.data[i].username;
            item.fecha_modificado = response.data[i].modificadoAl.substring(0,11);

            datos_grid.push(item);
        }
        moduloDatagrid.cargarDatos(datos_grid);
        moduloDatagrid.cargarTotalResultados(response.resultados,'<b>Programa(s)</b>');
        var total = parseInt(response.resultados/moduloDatagrid.rxpag); 
        var plus = parseInt(response.resultados)%moduloDatagrid.rxpag;
        if(plus>0) 
            total++;
        moduloDatagrid.paginacion(total);
    }
});

var modal_name = '#modal_programa_datos';
var form_name = '#form_programa_datos';

$('.btn-datagrid-agregar').on('click', function () {
    window.location.href = SERVER_HOST+'/expediente/editar-programa';
});

function cargar_datos_proyecto(e){
    window.location.href = SERVER_HOST+'/expediente/editar-programa/'+e;
}