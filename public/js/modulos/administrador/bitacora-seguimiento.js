
/*=====================================

    # Nombre:
        proyectos.js

    # Módulo:
        expediente/proyectos

    # Descripción:
        Se utiliza para crear, editar y eliminar caratulas de captura para proyectos de inversión e institucionales

=====================================*/

// Inicialización General para casi cualquier módulo
var moduloResource = new RESTfulRequests(SERVER_HOST+'/v1/bitacora-seguimiento');
var moduloDatagrid = new Datagrid("#dataGridModulo",moduloResource);
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
            item.clave = response.data[i].clavePresup;
            item.nombre_tecnico = response.data[i].nombreTecnico;
            item.estatus = '<span class="label ' + clase_label + '">' + response.data[i].estatus + '</span>';
            item.usuario = response.data[i].usuario;
            item.fecha_modificado = response.data[i].fechaHora;

            datos_grid.push(item);
        }
        moduloDatagrid.cargarDatos(datos_grid);                         
        moduloDatagrid.cargarTotalResultados(response.resultados,'<b>Eventos(s)</b>');
        var total = parseInt(response.resultados/moduloDatagrid.rxpag); 
        var plus = parseInt(response.resultados)%moduloDatagrid.rxpag;
        if(plus>0) 
            total++;
        moduloDatagrid.paginacion(total);
    }
});

function editar(e){};

$("#dataGridModulo .txt-quick-search").off('keydown');
$("#dataGridModulo .txt-quick-search").on('keydown', function(event){
    if (event.which == 13) {
        realizar_busqueda();
    }
});

$('#dataGridModulo .btn-quick-search').off('click');
$('#dataGridModulo .btn-quick-search').on('click',function(){
    realizar_busqueda();
})

function realizar_busqueda(){
    moduloDatagrid.setPagina(1);
    moduloDatagrid.parametros.buscar = $('.txt-quick-search').val();
    moduloDatagrid.parametros.ejercicio = $('#ejercicio').val();
    moduloDatagrid.parametros.mes = $('#mes').val();
    moduloDatagrid.actualizar();
}