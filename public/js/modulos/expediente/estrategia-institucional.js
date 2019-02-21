/*=====================================

    # Nombre:
        estrategia-institucional.js

    # Módulo:
        expediente/estrategia institucional

    # Descripción:
        Se utiliza para crear, editar y eliminar los indicadores de los estrategia institucional

=====================================*/

// Inicialización General para casi cualquier módulo
var moduloResource = new RESTfulRequests(SERVER_HOST+'/v1/estrategia-institucional');
var moduloDatagrid = new Datagrid("#datagridEstrategia",moduloResource);
moduloDatagrid.init();
moduloDatagrid.actualizar({
    _success: function(response){
        
        moduloDatagrid.limpiar();
        var datos_grid = [];
        for(var i in response.data){
            var item = {};
            var registro = response.data[i];
            var clase_label = 'label-info';
            if(registro.idEstatus == 2){
                clase_label = 'label-warning';
            }else if(registro.idEstatus == 3){
                clase_label = 'label-danger';
            }else if(registro.idEstatus == 4){
                clase_label = 'label-primary';
            }else if(registro.idEstatus == 5){
                clase_label = 'label-success';
            }

            item.id = registro.id;
            item.programa = registro.claveProgramaPresupuestario + ' ' + registro.programa_presupuestario.descripcion;
            //item.clave = registro.claveProgramaPresupuestario+" - "+registro.claveProgramaSectorial+" - "+registro.claveProgramaPresupuestario;
            item.descripcion = registro.descripcionIndicador;
            //item.fecha_termino = registro.fechaTermino;
            item.estatus = '<span class="label ' + clase_label + '">' + registro.estatus.descripcion + '</span>';
            item.usuario = registro.usuario.username;
            item.fecha_modificado = registro.modificadoAl.substring(0,11);

            datos_grid.push(item);
        }
        
        moduloDatagrid.cargarDatos(datos_grid);
        moduloDatagrid.cargarTotalResultados(response.resultados,'<b>Estrategias Institucionales(s)</b>');
        var total = parseInt(response.resultados/moduloDatagrid.rxpag); 
        var plus = parseInt(response.resultados)%moduloDatagrid.rxpag;
        if(plus>0) 
            total++;
        moduloDatagrid.paginacion(total);
    }
});

//var modal_name = '#modal_programa_datos';
//var form_name = '#form_programa_datos';

$('.btn-datagrid-agregar').on('click', function () {
    window.location.href = SERVER_HOST+'/expediente/editar-estrategia';
});

function cargar_datos_estrategia(e){
    window.location.href = SERVER_HOST+'/expediente/editar-estrategia/'+e;
}