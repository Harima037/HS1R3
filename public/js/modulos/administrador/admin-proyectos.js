
/*=====================================

    # Nombre:
        proyectos.js

    # Módulo:
        expediente/proyectos

    # Descripción:
        Se utiliza para crear, editar y eliminar caratulas de captura para proyectos de inversión e institucionales

=====================================*/

// Inicialización General para casi cualquier módulo
var moduloResource = new RESTfulRequests(SERVER_HOST+'/v1/admin-proyectos');
var moduloDatagrid = new Datagrid("#datagridCaratulas",moduloResource);
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
            }else if(response.data[i].idEstatusProyecto == 5){
                clase_label = 'label-success';
            }

            item.id = response.data[i].id;
            item.clave = response.data[i].clavePresup;
            item.nombre_tecnico = response.data[i].nombreTecnico;
            item.estatus = '<span class="label ' + clase_label + '">' + response.data[i].estatusProyecto + '</span>';
            item.usuario = response.data[i].username;
            item.fecha_modificado = response.data[i].modificadoAl.substring(0,11);

            datos_grid.push(item);
        }
        moduloDatagrid.cargarDatos(datos_grid);                         
        moduloDatagrid.cargarTotalResultados(response.resultados,'<b>Caratula(s)</b>');
        var total = parseInt(response.resultados/moduloDatagrid.rxpag); 
        var plus = parseInt(response.resultados)%moduloDatagrid.rxpag;
        if(plus>0) 
            total++;
        moduloDatagrid.paginacion(total);
    }
});

function editar (e){
    moduloResource.get(e,null,{
        _success: function(response){
            $('#modalEditarProyecto').find(".modal-title").text('Editar Proyecto');

            $('#clave-presupuestaria').text(response.data.ClavePresupuestaria);
            $('#nombre-tecnico').text(response.data.nombreTecnico);

            $('#estatus-proyecto').val(response.data.idEstatusProyecto);

            $('#id').val(response.data.id);

            $('#modalEditarProyecto').modal('show');
        }
    });
}
$('#modalEditarProyecto').on('hide.bs.modal',function(e){ 
    $('#clave-presupuestaria').text('');
    $('#nombre-tecnico').text('');
    $('#id').val('');
});

$('#btn-guardar').on('click',function(){
    var parametros = $('#form_proyecto').serialize();
    if($('#id').val()){
        moduloResource.put($('#id').val(),parametros,{
            _success: function(response){
                moduloDatagrid.actualizar();
                $('#modalEditarProyecto').modal('hide');
            }
        });
    }
});