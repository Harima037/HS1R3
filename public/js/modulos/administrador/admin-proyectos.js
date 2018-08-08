
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
var proyectoResource = new RESTfulRequests(SERVER_HOST+'/v1/admin-proyectos-avances');
var moduloDatagrid = new Datagrid("#datagridCaratulas",moduloResource);

var avancesNuevosStatus = {};

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
            item.boton = '<button onClick="cargar_datos_proyecto('+response.data[i].id+')" type="button" class="btn btn-info"><span class="fa fa-edit"></span></button>';
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

$('#btn-cambiar-estatus-avance').on('click',function(){
    if($('#proyecto-id').val()){
        proyectoResource.put($('#proyecto-id').val(),{estatus: avancesNuevosStatus},{
            _success: function(response){
                moduloDatagrid.actualizar();
                $('#modalDatosSeguimiento').modal('hide');
            }
        });
    }
});

function poner_estatus(mes){
    var estatus_id = $('#estatus-avance-'+mes).val();
    var avance_id = $('#estatus-avance-'+mes).attr('avance-id');
    avancesNuevosStatus[avance_id] = estatus_id;
}

function cargar_datos_proyecto(e){
    proyectoResource.get(e,null,{
        _success: function(response){
            $('#modalDatosSeguimiento').find(".modal-title").html('Clave: <small>' + response.data.ClavePresupuestaria + '</small>');

            $('#nombre-proyecto').text(response.data.nombreTecnico);
            $('#programa-presupuestario').text(response.data.datos_programa_presupuestario.clave + ' ' + response.data.datos_programa_presupuestario.descripcion);
            $('#funcion').text(response.data.datos_funcion.clave + ' ' + response.data.datos_funcion.descripcion);
            $('#subfuncion').text(response.data.datos_sub_funcion.clave + ' ' + response.data.datos_sub_funcion.descripcion);
            $('#proyecto-id').val(response.data.id);

            var meses_capturados = {1:false,2:false,3:false,4:false,5:false,6:false,7:false,8:false,9:false,10:false,11:false,12:false};
            
            for(var i in response.data.evaluacion_meses){
                evaluacion = response.data.evaluacion_meses[i];
                var icono = 'fa-file-pdf-o';
                var clase = 'btn-default';
                var firmar='';

                if(evaluacion.idEstatus == 4){
                    icono = 'fa-check';
                    clase = 'btn-primary';
                    firmar='<li><a href="#" onClick="firmarProyecto('+evaluacion.mes+')" class="btn-edit-rows"><span class="glyphicon glyphicon-edit"></span> Firmar</a> </li>';
                }else{
                    icono = 'fa-pencil';
                    clase = 'btn-success';
                    firmar='';
                }
                $('#estatus-avance-'+evaluacion.mes).val(evaluacion.idEstatus);
                $('#estatus-avance-'+evaluacion.mes).prop('disabled',false);
                $('#estatus-avance-'+evaluacion.mes).attr('avance-id',evaluacion.id);

                meses_capturados[evaluacion.mes] = true;
            }

            for(var i = 1; i <= 12; i++) {
                if(!meses_capturados[i]){
                    $('#estatus-avance-'+i).val(0);
                    $('#estatus-avance-'+i).prop('disabled',true);
                }
            }

            $('#modalDatosSeguimiento').modal('show');
        }
    });
}
$('#modalDatosSeguimiento').on('hide.bs.modal',function(e){
    $('#tabla-reportes tbody .status-avance').val(0);
    $('#tabla-reportes tbody .status-avance').prop('disabled',false);
    $('#tabla-reportes tbody .status-avance').removeAttr('avance-id');
    avancesNuevosStatus = {};
});